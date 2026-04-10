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

    public string $search       = '';
    public string $filterStatus = '';

    // ── Order detail slide-over ───────────────────────────────────────
    public bool   $showDetail   = false;
    public ?Order $selectedOrder = null;

    // ── Dispatch modal ────────────────────────────────────────────────
    public bool   $showDispatchModal = false;
    public int    $dispatchBoId      = 0;
    public string $dispatchNotes     = '';

    // ── Replace modal ─────────────────────────────────────────────────
    public bool   $showReplaceModal         = false;
    public int    $replacingBoId            = 0;
    public string $replacementProductSearch = '';
    public ?int   $selectedReplacementId    = null;
    public string $replaceNotes             = '';

    // ─────────────────────────────────────────────────────────────────

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

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
        $bo->update(['status' => 'ready']);
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

        if ($bo->isReplacement() && $bo->replacementProduct) {
            if ($bo->replacementProduct->stock >= $bo->short_qty) {
                $bo->replacementProduct->decrement('stock', $bo->short_qty);
            }
        } else {
            if ($bo->product && $bo->product->stock >= $bo->short_qty) {
                $bo->product->decrement('stock', $bo->short_qty);
            }
        }

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
            // Also update order to 'delivered' if it was still in sourcing/confirmed
            if (in_array($bo->order->status, ['sourcing', 'confirmed'])) {
                $bo->order->update(['status' => 'delivered']);
            }
        }

        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} completed.");
    }

    public function cancelBackorder(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        $bo->update(['status' => 'cancelled']);
        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} cancelled.");
    }

    // ── Replace Product ───────────────────────────────────────────────

    public function openReplaceModal(int $id): void
    {
        $this->replacingBoId            = $id;
        $this->replacementProductSearch = '';
        $this->selectedReplacementId    = null;
        $this->replaceNotes             = '';
        $this->showReplaceModal         = true;
    }

    public function selectReplacement(int $productId): void
    {
        $this->selectedReplacementId = $productId;
    }

    public function confirmReplacement(): void
    {
        $this->validate([
            'selectedReplacementId' => ['required', 'integer', 'exists:products,id'],
        ], [
            'selectedReplacementId.required' => 'Please select a replacement product.',
        ]);

        $bo          = OrderBackorder::with(['product'])->findOrFail($this->replacingBoId);
        $replacement = Product::findOrFail($this->selectedReplacementId);

        if ($replacement->stock < $bo->short_qty) {
            $this->addError('selectedReplacementId',
                "Not enough stock. Need {$bo->short_qty}, only {$replacement->stock} available.");
            return;
        }

        $bo->update([
            'decision'               => 'replace',
            'replacement_product_id' => $replacement->id,
            'replacement_price'      => $replacement->price,
            'replacement_notes'      => $this->replaceNotes ?: null,
            'status'                 => 'ready',
        ]);

        // Bug 7 fix: Update the order item to the replacement product so the
        // order detail reflects the new product and the price difference is captured.
        $orderItem = \App\Models\OrderItem::find($bo->order_item_id);
        if ($orderItem) {
            $newQty      = $bo->short_qty;
            $newPrice    = (float) $replacement->price;
            $newSubtotal = round($newPrice * $newQty, 2);
            $orderItem->update([
                'product_id'   => $replacement->id,
                'product_name' => $replacement->name,
                'price'        => $newPrice,
                'quantity'     => $newQty,
                'subtotal'     => $newSubtotal,
            ]);

            // Recalculate order totals and balance_amount
            $order = $orderItem->order()->with(['items', 'payments'])->first();
            if ($order) {
                $newSubtotalOrder = $order->items()->sum('subtotal');
                $newTotal         = round($newSubtotalOrder + $order->shipping_cost + $order->tax - $order->discount, 2);

                $advPct           = (float) ($order->advance_percentage ?? 0);
                $newAdvance       = $advPct > 0 ? round($newTotal * $advPct / 100, 2) : (float) $order->advance_amount;
                $newBalance       = max(0, round($newTotal - $newAdvance, 2));
                $confirmedBalance = $order->payments()->where('type', 'balance')->where('status', 'confirmed')->sum('amount');
                $newBalanceDue    = max(0, $newBalance - (float) $confirmedBalance);

                $order->update([
                    'subtotal'       => $newSubtotalOrder,
                    'total'          => $newTotal,
                    'advance_amount' => $newAdvance,
                    'balance_amount' => $newBalanceDue,
                ]);
            }
        }

        $this->showReplaceModal      = false;
        $this->replacingBoId         = 0;
        $this->selectedReplacementId = null;
        $this->refreshDetail();
        session()->flash('success', "{$bo->backorder_number} set to replace with \"{$replacement->name}\". Ready to dispatch.");
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        // Query orders that have active backorders — grouped at order level
        $ordersQuery = Order::with([
            'user',
            'backorders' => fn($q) => $q->with(['product', 'replacementProduct'])->orderBy('id'),
        ])
        ->whereHas('backorders', fn($q) => $q->whereNotIn('status', ['completed', 'cancelled']))
        ->when($this->filterStatus, fn($q) => $q->whereHas('backorders',
            fn($b) => $b->where('status', $this->filterStatus)
        ))
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
