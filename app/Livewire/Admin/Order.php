<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Order as OrderModel;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Setting;
use App\Services\WhatsappService;

#[Title('Orders')]
class Order extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterSource  = '';
    public string $filterPayment = '';
    public string $dateFrom      = '';
    public string $dateTo        = '';
    public string $sortBy        = 'created_at';
    public string $sortDir       = 'desc';

    // ── Detail Slide-Over Panel ───────────────────────────────────────
    public bool         $showDetail    = false;
    public ?OrderModel  $selectedOrder = null;

    // ── Stock Alert Modal ─────────────────────────────────────────────
    public bool  $showStockAlert  = false;
    public array $stockIssues     = []; // [{name, needed, available}]
    public int   $stockAlertOrder = 0;

    // ── Refund Modal ──────────────────────────────────────────────────
    public bool   $showRefundModal  = false;
    public int    $refundOrderId    = 0;
    public string $refundAmount     = '';
    public string $refundMethod     = 'bank_transfer';
    public string $refundReference  = '';
    public string $refundNotes      = '';

    // ─────────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }

    public function clearDates(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    // ── View Detail Panel ─────────────────────────────────────────────

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = OrderModel::with([
            'user',
            'items.product',
            'payments.confirmedBy',
            'statusLogs.createdBy',
            'refund',
            'whatsappToken',
        ])->findOrFail($id);

        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail    = false;
        $this->selectedOrder = null;
    }

    // ── Status Transitions ────────────────────────────────────────────

    /**
     * Move an order to 'confirmed' — checks stock first, then deducts it.
     */
    public function confirmOrder(int $orderId): void
    {
        $order = OrderModel::with('items.product')->findOrFail($orderId);

        // ── Stock check ───────────────────────────────────────────────
        $issues = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) continue;
            if ($product->stock < $item->quantity) {
                $issues[] = [
                    'name'      => $item->product_name,
                    'needed'    => $item->quantity,
                    'available' => $product->stock,
                ];
            }
        }

        if (!empty($issues)) {
            $this->stockIssues     = $issues;
            $this->stockAlertOrder = $orderId;
            $this->showStockAlert  = true;
            return;
        }

        // ── Deduct stock & confirm ────────────────────────────────────
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        $order->logStatus('confirmed', 'Order confirmed by admin. Stock deducted.', auth()->id());
        $order->update(['status' => 'confirmed']);
        try { WhatsappService::orderConfirmed($order->fresh('items')); } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order confirmed and stock updated.');
    }

    /**
     * Force-confirm an order even if stock is insufficient (admin override).
     */
    public function forceConfirmOrder(): void
    {
        $order = OrderModel::with('items.product')->findOrFail($this->stockAlertOrder);

        foreach ($order->items as $item) {
            if ($item->product) {
                // Allow negative stock (admin override)
                $item->product->decrement('stock', $item->quantity);
            }
        }

        $order->logStatus('confirmed', 'Order force-confirmed by admin (stock override).', auth()->id());
        $order->update(['status' => 'confirmed']);
        try { WhatsappService::orderConfirmed($order->fresh('items')); } catch (\Throwable) {}

        $this->showStockAlert  = false;
        $this->stockIssues     = [];
        $this->stockAlertOrder = 0;
        $this->refreshSelectedOrder($order->id);
        session()->flash('success', 'Order force-confirmed. Stock may be negative — please restock.');
    }

    public function closeStockAlert(): void
    {
        $this->showStockAlert  = false;
        $this->stockIssues     = [];
        $this->stockAlertOrder = 0;
    }

    /**
     * Mark an order as being sourced from supplier.
     */
    public function markSourcing(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $order->logStatus('sourcing', 'Product being sourced from supplier.', auth()->id());
        $order->update(['status' => 'sourcing', 'supplier_status' => 'ordered']);
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order marked as sourcing.');
    }

    /**
     * Mark that the supplier has delivered the product to the store.
     */
    public function markSupplierReceived(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $order->logStatus('confirmed', 'Product received from supplier, ready to dispatch.', auth()->id());
        $order->update(['status' => 'confirmed', 'supplier_status' => 'received']);
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Supplier delivery recorded. Order ready to dispatch.');
    }

    /**
     * Mark that the supplier cannot fulfill this product.
     */
    public function markSupplierUnavailable(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $order->logStatus('sourcing', 'Product marked unavailable by supplier.', auth()->id());
        $order->update(['supplier_status' => 'unavailable']);
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Supplier status updated to unavailable.');
    }

    /**
     * Record that the order was dispatched for delivery.
     * If a payment balance is still outstanding, fires a payment-due warning event
     * so the admin can decide whether to hold or force-dispatch.
     */
    public function markDispatched(int $orderId): void
    {
        $order = OrderModel::with(['payments' => fn($q) => $q->where('status', 'confirmed')])->findOrFail($orderId);

        // Payment gate — warn if balance is still due
        $confirmedTotal = $order->payments->sum('amount');
        $due = max(0, (float) $order->total - $confirmedTotal);

        if ($due > 0) {
            $this->dispatch('payment-due-on-dispatch', [
                'orderId'  => $orderId,
                'due'      => $due,
                'total'    => (float) $order->total,
                'paid'     => $confirmedTotal,
                'orderNum' => $order->order_number,
            ]);
            return;
        }

        $order->logStatus('dispatched', 'Order dispatched for delivery.', auth()->id());
        $order->update(['status' => 'dispatched']);
        try { WhatsappService::orderDispatched($order->fresh()); } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order marked as dispatched.');
    }

    /**
     * Force dispatch even if payment is incomplete (admin override).
     */
    public function forceDispatch(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $order->logStatus('dispatched', 'Order dispatched — balance payment still outstanding (admin override).', auth()->id());
        $order->update(['status' => 'dispatched']);
        try { WhatsappService::orderDispatched($order->fresh()); } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order dispatched (payment still pending — please follow up).');
    }

    /**
     * Record that the order was delivered to the customer.
     */
    public function markDelivered(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $order->logStatus('delivered', 'Order delivered to customer.', auth()->id());
        $order->update(['status' => 'delivered']);
        try { WhatsappService::orderDelivered($order->fresh()); } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order marked as delivered.');
    }

    /**
     * Mark the order as fully completed (balance paid and received).
     * Stock is already deducted at confirmOrder — no double-deduction here.
     */
    public function markCompleted(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $order->logStatus('completed', 'Order fully completed and balance paid.', auth()->id());
        $order->update(['status' => 'completed', 'payment_status' => 'paid']);
        try { WhatsappService::orderCompleted($order->fresh()); } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order marked as completed.');
    }

    /**
     * Set refund_option = 'refund' to indicate admin will process a refund.
     */
    public function offerRefund(int $orderId): void
    {
        OrderModel::findOrFail($orderId)->update(['refund_option' => 'refund']);
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order flagged for refund processing.');
    }

    /**
     * Set refund_option = 'reorder' so the customer can reorder.
     */
    public function offerReorder(int $orderId): void
    {
        OrderModel::findOrFail($orderId)->update(['refund_option' => 'reorder']);
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order flagged for reorder.');
    }

    // ── Payment Actions ───────────────────────────────────────────────

    /**
     * Confirm an advance payment receipt — moves order to 'payment_received'.
     */
    public function confirmPayment(int $paymentId): void
    {
        $payment = OrderPayment::with('order.items')->findOrFail($paymentId);
        $order   = $payment->order;

        $payment->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Only advance order status if order is still 'new'
        if ($order->status === 'new' && $payment->type === 'advance') {
            $order->logStatus('payment_received', 'Advance payment receipt confirmed by admin.', auth()->id());
            $order->update(['status' => 'payment_received', 'payment_status' => 'pending']);
        }

        try { WhatsappService::paymentReceived($order->fresh(), (float) $payment->amount); } catch (\Throwable) {}

        // Build WhatsApp confirmation message for admin to send to customer
        $address  = $order->shipping_address ?? [];
        $phone    = preg_replace('/[^0-9+]/', '', $address['phone'] ?? '');
        $siteName = \App\Models\Setting::get('site_name', 'Meharahouse');

        if ($phone) {
            $items = $order->items->map(fn($i) => "• {$i->product_name} x{$i->quantity}")->implode("\n");
            $msg = "✅ *Payment Confirmed — {$siteName}*\n\n"
                 . "Dear {$address['full_name']},\n\n"
                 . "Your payment has been received and confirmed! 🎉\n\n"
                 . "📦 *Order No:* {$order->order_number}\n"
                 . "💰 *Amount Paid:* Rs. " . number_format($payment->amount, 0) . "\n\n"
                 . "*Items:*\n{$items}\n\n"
                 . "We are now processing your order and will update you once it is dispatched.\n\n"
                 . "Thank you for shopping with {$siteName}! 🙏";

            $this->dispatch('open-whatsapp-prompt', phone: $phone, message: $msg);
        }

        $this->refreshSelectedOrder($order->id);
        session()->flash('success', 'Payment confirmed.');
    }

    /**
     * Reject a payment receipt — marks it rejected, order stays in current status.
     */
    public function rejectPayment(int $paymentId): void
    {
        $payment = OrderPayment::findOrFail($paymentId);
        $payment->update(['status' => 'rejected']);
        $this->refreshSelectedOrder($payment->order_id);
        session()->flash('success', 'Payment receipt rejected. Customer should re-upload.');
    }

    // ── Refund Processing ─────────────────────────────────────────────

    public function openRefundModal(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        $this->refundOrderId   = $orderId;
        $this->refundAmount    = (string) $order->advance_amount;
        $this->refundMethod    = 'bank_transfer';
        $this->refundReference = '';
        $this->refundNotes     = '';
        $this->showRefundModal = true;
    }

    public function processRefund(): void
    {
        $this->validate([
            'refundAmount'    => ['required', 'numeric', 'min:0.01'],
            'refundMethod'    => ['required', 'in:bank_transfer,online'],
            'refundReference' => ['nullable', 'string', 'max:255'],
            'refundNotes'     => ['nullable', 'string', 'max:1000'],
        ]);

        $order = OrderModel::findOrFail($this->refundOrderId);

        Refund::create([
            'order_id'     => $order->id,
            'amount'       => $this->refundAmount,
            'method'       => $this->refundMethod,
            'reference'    => $this->refundReference ?: null,
            'notes'        => $this->refundNotes ?: null,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $order->logStatus('refunded', 'Refund of Rs. ' . number_format($this->refundAmount, 0) . ' processed.', auth()->id());
        $order->update(['status' => 'refunded', 'payment_status' => 'refunded']);

        $this->showRefundModal = false;
        $this->refreshSelectedOrder($order->id);
        session()->flash('success', 'Refund processed and order marked as refunded.');
    }

    // ── WhatsApp Balance Reminder ─────────────────────────────────────

    /**
     * Build a ready-to-copy WhatsApp reminder message for the admin to send.
     * The message contains the outstanding balance amount and bank details.
     */
    public function sendBalanceReminder(int $orderId): void
    {
        $order       = OrderModel::findOrFail($orderId);
        $bankDetails = Setting::get('bank_transfer_details', '(Bank details not configured)');
        $balanceDue  = $order->balanceDue();

        $message = "Dear customer, your order *{$order->order_number}* has been delivered.\n\n"
            . "Outstanding balance: *Rs. " . number_format($balanceDue, 0) . "*\n\n"
            . "Please transfer to:\n{$bankDetails}\n\n"
            . "Thank you for shopping with Meharahouse!";

        // Flash the message for the admin to copy and paste into WhatsApp
        session()->flash('whatsapp_reminder', $message);
        session()->flash('whatsapp_reminder_order', $order->order_number);
    }

    // ── CSV Export ───────────────────────────────────────────────────

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $orders = OrderModel::with(['user', 'items', 'payments'])
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSource, fn($q) => $q->where('source', $this->filterSource))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Order #', 'Date', 'Customer Name', 'Phone', 'Alt Phone',
                'WhatsApp Tag (Last 4)', 'City', 'District', 'Address',
                'Abaya Size', 'Abaya Model',
                'Items', 'Subtotal', 'Advance', 'Balance Due',
                'Total', 'Payment Status', 'Order Status', 'Source',
                'Waybill #', 'Delivery Agent', 'Notes'
            ]);

            foreach ($orders as $order) {
                $addr    = $order->shipping_address ?? [];
                $phone   = $addr['phone'] ?? '';
                $last4   = strlen(preg_replace('/[^0-9]/', '', $phone)) >= 4
                            ? substr(preg_replace('/[^0-9]/', '', $phone), -4)
                            : '';
                $items   = $order->items->map(fn($i) => $i->product_name . ' x' . $i->quantity)->implode(' | ');
                $balance = $order->balanceDue();

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $addr['full_name'] ?? ($order->user?->name ?? ''),
                    $phone,
                    $addr['alt_phone'] ?? '',
                    $last4,
                    $addr['city'] ?? '',
                    $addr['district'] ?? '',
                    $addr['address'] ?? '',
                    $addr['abaya_size'] ?? '',
                    $addr['abaya_model'] ?? '',
                    $items,
                    $order->subtotal,
                    $order->advance_amount,
                    $balance,
                    $order->total,
                    $order->payment_status,
                    $order->status,
                    $order->source,
                    $order->waybill_number ?? '',
                    $order->delivery_agent ?? '',
                    $order->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ── Legacy status update (kept for compatibility) ─────────────────

    public function updateStatus(int $id, string $status): void
    {
        $allowed = ['new', 'payment_received', 'confirmed', 'sourcing', 'dispatched', 'delivered', 'completed', 'refunded', 'cancelled'];
        if (!in_array($status, $allowed)) return;

        $order = OrderModel::findOrFail($id);
        $order->logStatus($status, 'Status updated manually by admin.', auth()->id());
        $order->update(['status' => $status]);
        $this->refreshSelectedOrder($id);
        session()->flash('success', 'Order status updated to ' . ucfirst($status) . '.');
    }

    public function updatePaymentStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'partial', 'paid', 'failed', 'refunded'];
        if (!in_array($status, $allowed)) return;

        OrderModel::findOrFail($id)->update(['payment_status' => $status]);
        $this->refreshSelectedOrder($id);
        session()->flash('success', 'Payment status updated.');
    }

    // ── Helper: Refresh Selected Order ───────────────────────────────

    private function refreshSelectedOrder(int $orderId): void
    {
        if ($this->selectedOrder && $this->selectedOrder->id === $orderId) {
            $this->selectedOrder = OrderModel::with([
                'user',
                'items.product',
                'payments.confirmedBy',
                'statusLogs.createdBy',
                'refund',
                'whatsappToken',
            ])->find($orderId);
        }
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $orders = OrderModel::with('user')
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%"))
                  ->orWhere(function ($q) {
                      // Also search guest orders by shipping address name/phone
                      $q->whereJsonContains('shipping_address->full_name', $this->search)
                        ->orWhereJsonContains('shipping_address->phone', $this->search);
                  });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSource, fn($q) => $q->where('source', $this->filterSource))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(20);

        // Count by the new status values
        $statusCounts = OrderModel::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Count pending receipt confirmations (advance payments awaiting review)
        $pendingReceiptCount = OrderPayment::where('status', 'pending')
            ->whereNotNull('receipt_path')
            ->count();

        $layout = auth()->user()?->isAdmin() ? 'layouts.admin' : 'layouts.staff';
        return view('livewire.admin.order', compact('orders', 'statusCounts', 'pendingReceiptCount'))->layout($layout);
    }
}
