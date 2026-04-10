<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
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

    // ── Detail slide-over ─────────────────────────────────────────────
    public bool            $showDetail        = false;
    public ?OrderBackorder $selectedBackorder = null;

    // ── Dispatch modal ────────────────────────────────────────────────
    public bool   $showDispatchModal = false;
    public int    $dispatchBoId      = 0;
    public string $dispatchNotes     = '';

    // ── Replace modal ─────────────────────────────────────────────────
    public bool   $showReplaceModal          = false;
    public int    $replacingBoId             = 0;
    public string $replacementProductSearch  = '';
    public ?int   $selectedReplacementId     = null;
    public string $replaceNotes              = '';

    // ─────────────────────────────────────────────────────────────────

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    // ── Detail Panel ──────────────────────────────────────────────────

    public function viewBackorder(int $id): void
    {
        $this->selectedBackorder = OrderBackorder::with([
            'order.user',
            'order.items.product',
            'product',
            'replacementProduct',
            'creator',
            'dispatcher',
        ])->findOrFail($id);
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail        = false;
        $this->selectedBackorder = null;
    }

    // ── Status Transitions ────────────────────────────────────────────

    /** Mark a 'repurchasing' backorder as ready (stock confirmed arrived). */
    public function markReady(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        $bo->update(['status' => 'ready']);
        $this->refreshSelected($id);
        session()->flash('success', "Backorder {$bo->backorder_number} marked as ready to dispatch.");
    }

    /** Open dispatch modal. */
    public function openDispatch(int $id): void
    {
        $this->dispatchBoId      = $id;
        $this->dispatchNotes     = '';
        $this->showDispatchModal = true;
    }

    /** Confirm dispatch — deduct stock and mark as dispatched. */
    public function confirmDispatch(): void
    {
        $bo = OrderBackorder::with(['order', 'product', 'replacementProduct'])->findOrFail($this->dispatchBoId);

        // If this is a replacement, deduct from replacement product stock
        if ($bo->isReplacement() && $bo->replacementProduct) {
            if ($bo->replacementProduct->stock >= $bo->short_qty) {
                $bo->replacementProduct->decrement('stock', $bo->short_qty);
            }
        } else {
            // Normal backorder — deduct from original product
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

        // WhatsApp notification (best effort)
        try {
            $order = $bo->order->load('user', 'whatsappToken');
            WhatsappService::backorderDispatched($order, $bo);
        } catch (\Throwable) {}

        $this->showDispatchModal = false;
        $this->dispatchBoId      = 0;
        $this->refreshSelected($bo->id);
        session()->flash('success', "Backorder {$bo->backorder_number} dispatched.");
    }

    public function markDelivered(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        $bo->update(['status' => 'delivered', 'delivered_at' => now()]);
        $this->refreshSelected($id);
        session()->flash('success', "Backorder {$bo->backorder_number} marked as delivered.");
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
        }

        $this->refreshSelected($bo->id);
        session()->flash('success', "Backorder {$bo->backorder_number} completed.");
    }

    public function cancelBackorder(int $id): void
    {
        $bo = OrderBackorder::findOrFail($id);
        $bo->update(['status' => 'cancelled']);
        $this->refreshSelected($id);
        session()->flash('success', "Backorder {$bo->backorder_number} cancelled.");
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
            'replaceNotes'          => ['nullable', 'string', 'max:500'],
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

        $this->showReplaceModal      = false;
        $this->replacingBoId         = 0;
        $this->selectedReplacementId = null;
        $this->refreshSelected($bo->id);

        session()->flash('success',
            "Backorder {$bo->backorder_number} set to replace with \"{$replacement->name}\". Ready to dispatch.");
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function refreshSelected(int $id): void
    {
        if ($this->showDetail && $this->selectedBackorder?->id === $id) {
            $this->selectedBackorder = OrderBackorder::with([
                'order.user',
                'order.items.product',
                'product',
                'replacementProduct',
                'creator',
                'dispatcher',
            ])->find($id);
        }
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $backorders = OrderBackorder::with(['order.user', 'product', 'replacementProduct'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('backorder_number', 'like', "%{$this->search}%")
                          ->orWhere('product_name', 'like', "%{$this->search}%")
                          ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);

        $stats = [
            'pending'      => OrderBackorder::where('status', 'pending')->count(),
            'repurchasing' => OrderBackorder::where('status', 'repurchasing')->count(),
            'ready'        => OrderBackorder::where('status', 'ready')->count(),
            'dispatched'   => OrderBackorder::where('status', 'dispatched')->count(),
            'active'       => OrderBackorder::whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        // Products for replacement search
        $replacementProducts = ($this->showReplaceModal && strlen($this->replacementProductSearch) >= 2)
            ? Product::where('name', 'like', "%{$this->replacementProductSearch}%")
                     ->orWhere('sku', 'like', "%{$this->replacementProductSearch}%")
                     ->orderBy('name')
                     ->limit(20)
                     ->get()
            : collect();

        return view('livewire.admin.backorder', compact('backorders', 'stats', 'replacementProducts'));
    }
}
