<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

#[Title('Payments')]
#[Layout('layouts.admin')]
class Payment extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public string $filterMethod = '';
    public string $dateFrom     = '';
    public string $dateTo       = '';

    // Receive Payment Modal
    public bool   $showReceiveModal    = false;
    public ?int   $receiveOrderId      = null;
    public string $receiveAmount       = '';
    public string $receiveMethod       = 'bank_transfer';
    public string $receiveReference    = '';
    public string $receiveNotes        = '';
    public float  $receiveOrderTotal   = 0;
    public float  $receiveBalanceDue   = 0;
    public string $receiveOrderNumber  = '';
    public string $receiveCustomerName = '';

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedFilterMethod(): void { $this->resetPage(); }
    public function updatedDateFrom(): void     { $this->resetPage(); }
    public function updatedDateTo(): void       { $this->resetPage(); }

    public function clearDates(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    // ── Open Receive Payment Modal ────────────────────────────────────

    public function openReceiveModal(int $orderId): void
    {
        $order = Order::with(['payments' => fn($q) => $q->where('status', 'confirmed')])->findOrFail($orderId);

        $confirmedTotal = $order->payments->sum('amount');
        $due            = max(0, (float) $order->total - $confirmedTotal);

        $this->receiveOrderId      = $orderId;
        $this->receiveOrderNumber  = $order->order_number;
        $this->receiveCustomerName = $order->shipping_address['full_name'] ?? ($order->user?->name ?? 'Guest');
        $this->receiveOrderTotal   = (float) $order->total;
        $this->receiveBalanceDue   = $due;
        $this->receiveAmount       = (string) $due; // pre-fill with full due amount
        $this->receiveMethod       = 'bank_transfer';
        $this->receiveReference    = '';
        $this->receiveNotes        = '';
        $this->showReceiveModal    = true;
    }

    // ── Record Received Payment ───────────────────────────────────────

    public function recordPayment(): void
    {
        $this->validate([
            'receiveAmount'    => ['required', 'numeric', 'min:0.01', 'max:' . ($this->receiveBalanceDue + 0.01)],
            'receiveMethod'    => ['required', 'in:bank_transfer,online,cash'],
            'receiveReference' => ['nullable', 'string', 'max:255'],
            'receiveNotes'     => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::with(['payments' => fn($q) => $q->where('status', 'confirmed')])->findOrFail($this->receiveOrderId);

        // Determine type — if advance already exists, this is a balance payment
        $hasAdvance = $order->payments->where('type', 'advance')->count() > 0;
        $type       = $hasAdvance ? 'balance' : 'advance';

        // Create the payment record
        OrderPayment::create([
            'order_id'     => $order->id,
            'type'         => $type,
            'amount'       => $this->receiveAmount,
            'method'       => $this->receiveMethod,
            'reference'    => $this->receiveReference ?: null,
            'notes'        => $this->receiveNotes ?: null,
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Recalculate total confirmed
        $order->refresh();
        $allConfirmed = $order->payments()->where('status', 'confirmed')->sum('amount');
        $remaining    = max(0, (float) $order->total - $allConfirmed);

        // Mark fully paid if no remaining balance
        if ($remaining <= 0) {
            $order->update([
                'payment_status' => 'paid',
                'balance_amount' => 0,
            ]);
        } else {
            $order->update([
                'balance_amount' => $remaining,
            ]);
        }

        $this->showReceiveModal = false;
        session()->flash('success', 'Payment of Rs. ' . number_format($this->receiveAmount, 2) . ' recorded for ' . $order->order_number . '.');
    }

    // ── WhatsApp Balance Reminder ─────────────────────────────────────

    public function sendReminder(int $orderId): void
    {
        $order       = Order::with(['payments' => fn($q) => $q->where('status', 'confirmed')])->findOrFail($orderId);
        $phone       = $order->shipping_address['phone'] ?? null;
        $name        = $order->shipping_address['full_name'] ?? 'Customer';
        $siteName    = Setting::get('site_name', 'Meharahouse');
        $bankDetails = Setting::get('bank_transfer_details', '(Bank details not configured)');

        $confirmedTotal = $order->payments->sum('amount');
        $due            = max(0, (float) $order->total - $confirmedTotal);

        $message = "💳 *Payment Reminder — {$siteName}*\n\n"
            . "Dear {$name},\n\n"
            . "This is a friendly reminder regarding your order *{$order->order_number}*.\n\n"
            . "📦 *Order Total:* Rs. " . number_format($order->total, 0) . "\n"
            . "✅ *Amount Paid:* Rs. " . number_format($confirmedTotal, 0) . "\n"
            . "⚠️ *Balance Due:* Rs. " . number_format($due, 0) . "\n\n"
            . "Please transfer the balance to:\n{$bankDetails}\n\n"
            . "Thank you for shopping with {$siteName}! 🙏";

        if ($phone) {
            $this->dispatch('open-payment-whatsapp', phone: $phone, message: $message);
        } else {
            session()->flash('error', 'No phone number found for this customer.');
        }
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $payments = Order::with(['user', 'payments' => fn($q) => $q->where('status', 'confirmed')])
            ->when($this->search, fn($q) => $q
                ->where('order_number', 'like', "%{$this->search}%")
                ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.full_name'))"), 'like', "%{$this->search}%")
                ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.phone'))"), 'like', "%{$this->search}%"))
            ->when($this->filterStatus, function ($q) {
                if ($this->filterStatus === 'partial') {
                    $q->where('payment_status', 'pending')
                      ->where('advance_amount', '>', 0)
                      ->where('balance_amount', '>', 0);
                } else {
                    $q->where('payment_status', $this->filterStatus);
                }
            })
            ->when($this->filterMethod, fn($q) => $q->where('payment_method', $this->filterMethod))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        $totalConfirmedPayments   = OrderPayment::where('status', 'confirmed')->whereIn('type', ['advance', 'balance'])->sum('amount');
        $totalPendingReceipts     = OrderPayment::where('status', 'pending')->sum('amount');
        $partialCount             = Order::where('payment_status', 'pending')->where('advance_amount', '>', 0)->where('balance_amount', '>', 0)->count();
        $confirmedBalancePayments = OrderPayment::where('status', 'confirmed')->where('type', 'balance')->sum('amount');
        $totalDue = max(0, Order::where('payment_status', '!=', 'paid')->where('balance_amount', '>', 0)->sum('balance_amount') - $confirmedBalancePayments);

        $summary = [
            'total_collected'        => $totalConfirmedPayments,
            'total_pending_receipts' => $totalPendingReceipts,
            'partial_count'          => $partialCount,
            'total_due'              => $totalDue,
            'total_orders'           => Order::count(),
            'count_paid'             => Order::where('payment_status', 'paid')->count(),
            'count_pending'          => Order::where('payment_status', 'pending')->count(),
            'count_failed'           => Order::where('payment_status', 'failed')->count(),
            'count_refunded'         => Order::where('payment_status', 'refunded')->count(),
        ];

        return view('livewire.admin.payment', compact('payments', 'summary'));
    }
}
