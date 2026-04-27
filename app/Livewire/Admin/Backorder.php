<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderBackorder;
use App\Models\Product;
use App\Services\WhatsappService;

#[Title('Backorders')]
#[Layout('layouts.admin')]
class Backorder extends Component
{
    use WithPagination;

    public $search       = '';
    public $filterStatus = '';

    // ── Order detail slide-over ───────────────────────────────────────
    public $showDetail   = false;
    public $selectedOrder = null;

    // ── Dispatch modal ────────────────────────────────────────────────
    public $showDispatchModal = false;
    public $dispatchBoId      = 0;
    public $dispatchNotes     = '';

    // ── Replace modal ─────────────────────────────────────────────────
    public $showReplaceModal         = false;
    public $replacingBoId            = 0;
    public $replacementProductSearch = '';
    public $selectedReplacementId    = null;
    public $replacementSize           = '';
    public $replaceNotes             = '';
    // Price diff tracking (set when modal opens / product selected)
    public $originalItemPrice        = 0.0;  // price per unit from order item
    public $replacingQty             = 0;    // short_qty of the backorder
    public $selectedReplacementPrice = 0.0;  // price of chosen replacement product
    public $replaceQty               = 1;

    // ── Payment gate ──────────────────────────────────────────────────
    public $showPaymentGate    = false;
    public $paymentGateOrderId = 0;
    public $paymentGateOrderNo = '';
    public $paymentGateDue    = 0.0;

    // ── Refund modal ──────────────────────────────────────────────────
    public $showRefundModal       = false;
    public $refundingBoId          = 0;
    public $refundAmount           = '0';
    public $refundMethod           = 'bank_transfer';
    public $customerBankAccount    = '';
    public $refundNotes            = '';
    public $refundQty              = 1;

    // ─────────────────────────────────────────────────────────────────

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function updatedRefundQty(): void
    {
        $bo = OrderBackorder::with('orderItem', 'product')->find($this->refundingBoId);
        if (!$bo) return;
        $unitPrice = $bo->orderItem ? (float) $bo->orderItem->price : ($bo->product ? (float) $bo->product->price : 0);
        $qty = max(1, min((int) $this->refundQty, (int) $bo->short_qty));
        $this->refundQty    = $qty;
        $this->refundAmount = (string) round($unitPrice * $qty, 2);
    }

    // ── Detail Panel (order-level) ────────────────────────────────────

    public function viewOrder(int $orderId): void
    {
        $this->selectedOrder = Order::with([
            'user',
            'backorders' => fn($q) => $q->with(['product', 'replacementProduct', 'creator', 'dispatcher'])
                                        ->orderBy('id'),
        ])->findOrFail($orderId);
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail    = false;
        $this->selectedOrder = null;
    }

    private function refreshDetail(): void
    {
        if ($this->showDetail && $this->selectedOrder) {
            $this->selectedOrder = Order::with([
                'user',
                'backorders' => fn($q) => $q->with(['product', 'replacementProduct', 'creator', 'dispatcher'])
                                            ->orderBy('id'),
            ])->find($this->selectedOrder->id);
        }
    }

    // ── Status Transitions (per individual backorder) ─────────────────

    public function markReady(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        
        if ($bo->status !== 'ready') {
            if ($bo->isReplacement() && $bo->replacementProduct) {
                if ($bo->replacementProduct->stock >= $bo->short_qty) {
                    $bo->replacementProduct->decrement('stock', $bo->short_qty);
                } else {
                    session()->flash('error', "Insufficient stock for replacement product ({$bo->replacementProduct->name}).");
                    return;
                }
            } else {
                if ($bo->product && $bo->product->stock >= $bo->short_qty) {
                    $bo->product->decrement('stock', $bo->short_qty);
                } else {
                    session()->flash('error', "Insufficient stock for product ({$bo->product_name}).");
                    return;
                }
            }
        }

        $bo->update(['status' => 'ready']);

        // Update order status to 'sourcing' when at least one backorder is ready
        $order = $bo->order()->first();
        if ($order && $order->status === 'confirmed') {
            $order->logStatus('sourcing', 'Backorder item ready — awaiting shipment dispatch.', auth()->id());
            $order->update(['status' => 'sourcing']);
        }

        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} marked ready to dispatch.");
    }

    public function openDispatch(int $id): void
    {
        $this->dispatchBoId      = $id;
        $this->dispatchNotes     = '';
        $this->showDispatchModal = true;
    }

    public function confirmDispatch(): void
    {
        $bo = OrderBackorder::with(['order', 'product', 'replacementProduct'])->findOrFail($this->dispatchBoId);

        $bo->update([
            'status'        => 'dispatched',
            'dispatched_at' => now(),
            'dispatched_by' => auth()->id(),
            'notes'         => $this->dispatchNotes ?: $bo->notes,
        ]);

        try {
            $order = $bo->order->load('user', 'whatsappToken');
            WhatsappService::backorderDispatched($order, $bo);
        } catch (\Throwable) {}

        $this->showDispatchModal = false;
        $this->dispatchBoId      = 0;
        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} dispatched.");
    }

    public function markDelivered(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        $bo->update(['status' => 'delivered', 'delivered_at' => now()]);
        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} marked as delivered.");
    }

    public function markCompleted(int $id): void
    {
        $bo = OrderBackorder::with('order')->findOrFail($id);
        $bo->update(['status' => 'completed', 'fulfilled_at' => now()]);

        $remaining = OrderBackorder::where('order_id', $bo->order_id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        if ($remaining === 0 && $bo->order) {
            $bo->order->logStatus(
                $bo->order->status,
                'All backorders completed — order fully fulfilled.',
                auth()->id()
            );
            // Update order to 'delivered' if it was still pending/in-transit
            if (in_array($bo->order->status, ['sourcing', 'confirmed', 'dispatched'])) {
                $bo->order->update(['status' => 'delivered']);
            }
        }

        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} completed.");
    }

    public function cancelBackorder(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        
        if (in_array($bo->status, ['ready', 'dispatched', 'delivered', 'completed'])) {
            if ($bo->isReplacement() && $bo->replacementProduct) {
                $bo->replacementProduct->increment('stock', $bo->short_qty);
            } else if ($bo->product) {
                $bo->product->increment('stock', $bo->short_qty);
            }
        }

        $bo->update(['status' => 'cancelled']);
        
        // Also update order item to cancelled if it exists
        if ($bo->order_item_id) {
            \App\Models\OrderItem::where('id', $bo->order_item_id)->update(['status' => 'cancelled']);
        }

        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} cancelled.");
    }

    // ── Refund Action ───────────────────────────────────────────────

    public function openRefundModal(int $id): void
    {
        $bo = OrderBackorder::with(['order', 'orderItem'])->findOrFail($id);

        $this->refundingBoId = $id;
        $this->refundQty = (int) $bo->short_qty;
        $unitPrice    = $bo->orderItem ? (float) $bo->orderItem->price : ($bo->product ? (float) $bo->product->price : 0);
        $itemSubtotal = round($unitPrice * $bo->short_qty, 2);

        $this->refundAmount = (string) $itemSubtotal;
        $this->refundMethod         = 'bank_transfer';
        $this->customerBankAccount  = '';
        $this->refundNotes          = "Refund for out-of-stock item: {$bo->product_name}";
        $this->showRefundModal      = true;
    }

    public function confirmRefund(): void
    {
        $bo    = OrderBackorder::with(['order', 'orderItem', 'product'])->findOrFail($this->refundingBoId);
        $unitPrice = $bo->orderItem ? (float) $bo->orderItem->price : ($bo->product ? (float) $bo->product->price : 0);
        $maxRefund = round($unitPrice * $bo->short_qty, 2);

        $this->validate([
            'refundAmount' => ['required', 'numeric', 'min:0.01', 'max:' . $maxRefund],
            'refundMethod' => ['required', 'string'],
            'refundQty'    => ['required', 'integer', 'min:1', 'max:' . $bo->short_qty],
        ], [
            'refundAmount.max' => 'Refund amount cannot exceed LKR ' . number_format($maxRefund, 2) . ' (item price × quantity).',
        ]);

        $order = $bo->order;

        \DB::transaction(function() use ($bo, $order) {
            // 1. Create Refund record
            \App\Models\Refund::create([
                'order_id'              => $order->id,
                'customer_id'           => $order->user_id,
                'amount'                => (float) $this->refundAmount,
                'method'                => $this->refundMethod,
                'customer_bank_account' => $this->customerBankAccount ?: null,
                'notes'                 => $this->refundNotes ?: null,
                'status'                => 'processed',
                'processed_by'          => auth()->id(),
                'processed_at'          => now(),
            ]);

            // 2. Update Backorder (partial or full cancel)
            if ((int) $this->refundQty >= (int) $bo->short_qty) {
                // Full refund of all units — cancel the backorder
                $bo->update(['status' => 'cancelled', 'notes' => ($bo->notes ? $bo->notes . "\n" : "") . "Refunded: LKR " . $this->refundAmount]);
            } else {
                // Partial refund — reduce short_qty and keep backorder active
                $newShortQty = (int) $bo->short_qty - (int) $this->refundQty;
                $bo->update([
                    'short_qty' => $newShortQty,
                    'notes'     => ($bo->notes ? $bo->notes . "\n" : "") . "Partial refund: {$this->refundQty} unit(s) refunded LKR " . $this->refundAmount,
                ]);
            }

            // 3. Update Order Item
            if ($bo->orderItem) {
                $bo->orderItem->update([
                    'status'        => 'refunded',
                    'refund_amount' => (float) $this->refundAmount,
                ]);
            }

            // 4. Recalculate Order Totals
            $subtotalRemoved = $bo->orderItem ? (float) $bo->orderItem->subtotal : 0;
            $newSubtotal = max(0, (float) $order->subtotal - $subtotalRemoved);
            $taxRate = (float) \App\Models\Setting::get('tax_rate', '15') / 100;
            $newTax  = round($newSubtotal * $taxRate, 2);
            $newDiscount = (float) $order->discount;
            if ($order->coupon_code) {
                $coupon = \App\Models\Coupon::where('code', $order->coupon_code)->first();
                if ($coupon && $coupon->type === 'percentage') {
                    $newDiscount = round($newSubtotal * ($coupon->value / 100), 2);
                    if ($coupon->max_discount && $newDiscount > $coupon->max_discount) {
                        $newDiscount = (float) $coupon->max_discount;
                    }
                }
            }
            $newTotal = round($newSubtotal + $order->shipping_cost + $newTax - $newDiscount, 2);
            $totalPaid = (float) $order->payments()->whereIn('type', ['advance', 'balance', 'full'])->where('status', 'confirmed')->sum('amount');
            $newBalanceDue = max(0, round($newTotal - $totalPaid, 2));

            $order->update([
                'subtotal'       => $newSubtotal,
                'tax'            => $newTax,
                'discount'       => $newDiscount,
                'total'          => $newTotal,
                'balance_amount' => $newBalanceDue,
            ]);

            $order->logStatus($order->status, "Item '{$bo->product_name}' refunded (LKR {$this->refundAmount}). Order total adjusted.", auth()->id());
        });

        $this->showRefundModal = false;
        $this->refreshDetail();
        session()->flash('success', "Refund of LKR " . number_format((float)$this->refundAmount, 2) . " processed for {$bo->backorder_number}.");
    }

    // ── Payment Gate (shipments dispatch check) ───────────────────────

    public function checkDispatchPayment(int $orderId): void
    {
        $order = \App\Models\Order::findOrFail($orderId);
        $due   = $order->balanceDue();

        if ($due > 0) {
            $this->paymentGateOrderId = $orderId;
            $this->paymentGateOrderNo = $order->order_number;
            $this->paymentGateDue     = $due;
            $this->showPaymentGate    = true;
            return;
        }

        $this->redirectRoute('admin.shipments');
    }

    public function closePaymentGate(): void
    {
        $this->showPaymentGate    = false;
        $this->paymentGateOrderId = 0;
        $this->paymentGateOrderNo = '';
        $this->paymentGateDue     = 0.0;
    }

    // ── Replace Product ───────────────────────────────────────────────

    public function openReplaceModal(int $id): void
    {
        $bo = OrderBackorder::with('orderItem')->find($id);
        $this->replacingBoId            = $id;
        $this->replacementProductSearch = '';
        $this->selectedReplacementId    = null;
        $this->replacementSize          = $bo?->size ?? '';
        $this->replaceNotes             = '';
        $this->selectedReplacementPrice = 0.0;
        $this->originalItemPrice = $bo?->orderItem ? (float) $bo->orderItem->price : 0.0;
        $this->replacingQty      = (int) ($bo?->short_qty ?? 0);
        $this->replaceQty        = (int) ($bo?->short_qty ?? 1);
        $this->showReplaceModal  = true;
    }

    public function selectReplacement(int $productId): void
    {
        $this->selectedReplacementId    = $productId;
        $product = Product::find($productId);
        $this->selectedReplacementPrice = $product ? (float) $product->price : 0.0;
    }

    public function confirmReplacement(): void
    {
        $this->validate([
            'selectedReplacementId' => ['required', 'integer', 'exists:products,id'],
        ], [
            'selectedReplacementId.required' => 'Please select a replacement product.',
        ]);

        $bo          = OrderBackorder::with(['product', 'orderItem'])->findOrFail($this->replacingBoId);
        $replacement = Product::findOrFail($this->selectedReplacementId);
        $qty      = max(1, min((int) $this->replaceQty, (int) $bo->short_qty));
        $hasStock = $replacement->stock >= $qty;
        $origUnitPrice  = $bo->orderItem ? (float) $bo->orderItem->price : 0.0;
        $newUnitPrice   = (float) $replacement->price;
        $priceDiff      = round(($newUnitPrice - $origUnitPrice) * $qty, 2);

        if ($hasStock) {
            $replacement->decrement('stock', $qty);
        }

        $bo->update([
            'decision'               => 'replace',
            'replacement_product_id' => $replacement->id,
            'replacement_price'      => $replacement->price,
            'replacement_notes'      => $this->replaceNotes ?: null,
            'status'                 => $hasStock ? 'ready' : 'repurchasing',
        ]);

        $remainingQty = (int) $bo->short_qty - $qty;
        if ($remainingQty > 0) {
            OrderBackorder::create([
                'order_id'      => $bo->order_id,
                'order_item_id' => $bo->order_item_id,
                'product_id'    => $bo->product_id,
                'product_name'  => $bo->product_name,
                'size'          => $bo->size,
                'ordered_qty'   => $remainingQty,
                'available_qty' => 0,
                'short_qty'     => $remainingQty,
                'decision'      => 'repurchase',
                'status'        => 'repurchasing',
                'created_by'    => auth()->id(),
            ]);
        }
        $bo->update(['short_qty' => $qty]);

        $orderItem = \App\Models\OrderItem::find($bo->order_item_id);
        $refundNeeded = 0.0;

        if ($orderItem) {
            $newSubtotal = round($newUnitPrice * $qty, 2);
            $originalProductId   = $orderItem->is_replaced ? $orderItem->original_product_id   : $orderItem->product_id;
            $originalProductName = $orderItem->is_replaced ? $orderItem->original_product_name : $orderItem->product_name;
            $originalPrice       = $orderItem->is_replaced ? $orderItem->original_price        : $orderItem->price;
            $originalSubtotal    = $orderItem->is_replaced ? $orderItem->original_subtotal     : $orderItem->subtotal;

            $orderItem->update([
                'product_id'            => $replacement->id,
                'product_name'          => $replacement->name,
                'price'                 => $newUnitPrice,
                'quantity'              => $qty,
                'size'                  => $this->replacementSize ?: null,
                'subtotal'              => $newSubtotal,
                'is_replaced'           => true,
                'original_product_id'   => $originalProductId,
                'original_product_name' => $originalProductName,
                'original_price'        => $originalPrice,
                'original_subtotal'     => $originalSubtotal,
                'replacement_notes'     => $this->replaceNotes ?: null,
                'replaced_at'           => now(),
                'replaced_by'           => auth()->id(),
            ]);

            $order = $orderItem->order()->with(['items', 'payments'])->first();
            if ($order) {
                $newSubtotalOrder = $order->items()->sum('subtotal');
                $taxRate = (float) \App\Models\Setting::get('tax_rate', '15') / 100;
                $newTax  = round($newSubtotalOrder * $taxRate, 2);
                $newDiscount = (float) $order->discount;
                if ($order->coupon_code) {
                    $coupon = \App\Models\Coupon::where('code', $order->coupon_code)->first();
                    if ($coupon && $coupon->type === 'percentage') {
                        $newDiscount = round($newSubtotalOrder * ($coupon->value / 100), 2);
                        if ($coupon->max_discount && $newDiscount > $coupon->max_discount) {
                            $newDiscount = (float) $coupon->max_discount;
                        }
                    }
                }
                $newTotal = round($newSubtotalOrder + $order->shipping_cost + $newTax - $newDiscount, 2);
                $advPct       = (float) ($order->advance_percentage ?? 0);
                $newAdvance   = $advPct > 0 ? round($newTotal * $advPct / 100, 2) : (float) $order->advance_amount;
                $totalPaid = (float) $order->payments()->whereIn('type', ['advance', 'balance', 'full'])->where('status', 'confirmed')->sum('amount');
                $newBalanceDue = max(0, $newTotal - $totalPaid);
                $refundNeeded = max(0.0, round($totalPaid - $newTotal, 2));

                $order->update([
                    'subtotal'       => $newSubtotalOrder,
                    'tax'            => $newTax,
                    'discount'       => $newDiscount,
                    'total'          => $newTotal,
                    'advance_amount' => $newAdvance,
                    'balance_amount' => $newBalanceDue,
                ]);

                $diffNote = $priceDiff > 0
                    ? "Replacement product \"{$replacement->name}\" costs LKR " . number_format($priceDiff, 2) . " MORE than original. Balance due increased."
                    : ($priceDiff < 0
                        ? "Replacement product \"{$replacement->name}\" costs LKR " . number_format(abs($priceDiff), 2) . " LESS than original. Order total reduced."
                        : "Replacement product \"{$replacement->name}\" — same price, no adjustment.");
                $order->logStatus($order->status, $diffNote, auth()->id());
            }
        }

        $this->showReplaceModal         = false;
        $this->replacingBoId            = 0;
        $this->selectedReplacementId    = null;
        $this->replacementSize          = '';
        $this->selectedReplacementPrice = 0.0;

        if (!$hasStock) {
            session()->flash('success', "Replacement set to \"{$replacement->name}\" (out of stock). Status remains Repurchasing.");
            $this->refreshDetail();
            return;
        }

        session()->flash('success', "Replaced with \"{$replacement->name}\". Ready to dispatch.");
        $this->refreshDetail();
    }

    public function render()
    {
        $ordersQuery = Order::with([
            'user',
            'backorders' => fn($q) => $q->with(['product', 'replacementProduct'])->orderBy('id'),
        ])
        ->whereHas('backorders', fn($q) => $q->whereNotIn('status', ['completed', 'cancelled']))
        ->when($this->filterStatus, fn($q) => $q->whereHas('backorders', fn($b) => $b->where('status', $this->filterStatus)))
        ->when($this->search, fn($q) => $q
            ->where('order_number', 'like', "%{$this->search}%")
            ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"))
            ->orWhereHas('backorders', fn($b) => $b->where('product_name', 'like', "%{$this->search}%"))
        )
        ->latest()
        ->paginate(15);

        $stats = [
            'pending'      => OrderBackorder::where('status', 'pending')->count(),
            'repurchasing' => OrderBackorder::where('status', 'repurchasing')->count(),
            'ready'        => OrderBackorder::where('status', 'ready')->count(),
            'dispatched'   => OrderBackorder::where('status', 'dispatched')->count(),
            'active'       => OrderBackorder::whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        $replacementProducts = ($this->showReplaceModal && strlen($this->replacementProductSearch) >= 2)
            ? Product::where('name', 'like', "%{$this->replacementProductSearch}%")
                     ->orWhere('sku',  'like', "%{$this->replacementProductSearch}%")
                     ->orderBy('name')
                     ->limit(20)
                     ->get()
            : collect();

        return view('livewire.admin.backorder', compact('ordersQuery', 'stats', 'replacementProducts'));
    }
}
