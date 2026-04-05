<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier as SupplierModel;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

#[Title('Purchase Orders')]
#[Layout('layouts.admin')]
class Purchasing extends Component
{
    use WithPagination;

    // ─── List filters ────────────────────────────────────────────
    public string $search          = '';
    public string $filterStatus    = '';
    public ?int   $filterSupplierId = null;

    // ─── Create / Edit PO modal ──────────────────────────────────
    public bool  $showPoModal  = false;
    public bool  $editMode     = false;
    public ?int  $editingPoId  = null;

    // PO form fields
    public int    $supplierId    = 0;
    public string $notes         = '';
    public string $expectedDate  = '';
    public string $shippingCost  = '0';

    /**
     * Each item: [product_id, product_name, sku, qty_ordered, unit_cost]
     */
    public array $poItems = [];

    // Product search within the PO modal
    public string $itemProductSearch = '';

    // ─── Receive Goods modal ─────────────────────────────────────
    public bool  $showReceiveModal = false;
    public ?int  $receivingPoId    = null;
    public array $receiveQtys      = []; // [item_id => qty_to_receive]

    // ─── PO Detail modal ─────────────────────────────────────────
    public bool  $showDetailModal = false;
    public ?int  $detailPoId      = null;

    // ─────────────────────────────────────────────────────────────

    public function updatedSearch(): void          { $this->resetPage(); }
    public function updatedFilterStatus(): void    { $this->resetPage(); }
    public function updatedFilterSupplierId(): void { $this->resetPage(); }

    // Recalculate totals whenever items or shipping change
    public function updatedPoItems(): void        { $this->recalculateTotals(); }
    public function updatedShippingCost(): void   { $this->recalculateTotals(); }

    // ─── Computed: product search results ────────────────────────

    #[Computed]
    public function productSearchResults(): Collection
    {
        if (strlen(trim($this->itemProductSearch)) < 2) {
            return collect();
        }

        return Product::where('is_active', true)
            ->where(fn($q) =>
                $q->where('name', 'like', "%{$this->itemProductSearch}%")
                  ->orWhere('sku', 'like', "%{$this->itemProductSearch}%")
            )
            ->select('id', 'name', 'sku', 'stock', 'price')
            ->limit(8)
            ->get();
    }

    // ─── Open Create PO ──────────────────────────────────────────

    public function openCreatePo(): void
    {
        $this->resetPoForm();
        $this->editMode        = false;
        $this->editingPoId     = null;
        $this->showPoModal     = true;
    }

    // ─── Open Edit PO ────────────────────────────────────────────

    public function openEditPo(int $id): void
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);

        if (! in_array($po->status, ['draft', 'ordered'])) {
            session()->flash('error', 'Only Draft or Ordered POs can be edited.');
            return;
        }

        $this->editMode       = true;
        $this->editingPoId    = $id;
        $this->supplierId     = $po->supplier_id;
        $this->notes          = $po->notes ?? '';
        $this->expectedDate   = $po->expected_date ? $po->expected_date->format('Y-m-d') : '';
        $this->shippingCost   = (string) $po->shipping_cost;

        $this->poItems = $po->items->map(fn($item) => [
            'product_id'   => $item->product_id,
            'product_name' => $item->product_name,
            'sku'          => $item->sku ?? '',
            'qty_ordered'  => $item->quantity_ordered,
            'unit_cost'    => (string) $item->unit_cost,
        ])->values()->toArray();

        $this->itemProductSearch = '';
        $this->showPoModal       = true;
    }

    // ─── Add product to PO items ─────────────────────────────────

    public function addPoItem(int $productId): void
    {
        $product = Product::findOrFail($productId);

        // Prevent duplicates
        foreach ($this->poItems as $item) {
            if ((int) ($item['product_id'] ?? 0) === $productId) {
                $this->itemProductSearch = '';
                return;
            }
        }

        $this->poItems[] = [
            'product_id'   => $productId,
            'product_name' => $product->name,
            'sku'          => $product->sku ?? '',
            'qty_ordered'  => 1,
            'unit_cost'    => '0',
        ];

        $this->itemProductSearch = '';
        $this->recalculateTotals();
    }

    // ─── Remove item from PO ─────────────────────────────────────

    public function removePoItem(int $idx): void
    {
        unset($this->poItems[$idx]);
        $this->poItems = array_values($this->poItems);
        $this->recalculateTotals();
    }

    // ─── Recalculate totals ───────────────────────────────────────

    public function recalculateTotals(): void
    {
        // Re-index so Livewire wire:model works cleanly
        $this->poItems = array_values($this->poItems);
    }

    // Helper: compute subtotal from current poItems
    private function computeSubtotal(): float
    {
        $sub = 0.0;
        foreach ($this->poItems as $item) {
            $qty  = max(0, (int)   ($item['qty_ordered'] ?? 0));
            $cost = max(0, (float) ($item['unit_cost']   ?? 0));
            $sub += $qty * $cost;
        }
        return $sub;
    }

    // ─── Save PO (create or update) ──────────────────────────────

    public function savePo(bool $markOrdered = false): void
    {
        $this->validate([
            'supplierId'   => ['required', 'integer', 'exists:suppliers,id'],
            'poItems'      => ['required', 'array', 'min:1'],
            'shippingCost' => ['nullable', 'numeric', 'min:0'],
            'expectedDate' => ['nullable', 'date'],
        ]);

        // Validate each item
        foreach ($this->poItems as $idx => $item) {
            if (empty(trim($item['product_name'] ?? ''))) {
                $this->addError("poItems.{$idx}.product_name", 'Product name is required.');
                return;
            }
            if ((int) ($item['qty_ordered'] ?? 0) < 1) {
                $this->addError("poItems.{$idx}.qty_ordered", 'Quantity must be at least 1.');
                return;
            }
            if ((float) ($item['unit_cost'] ?? 0) < 0) {
                $this->addError("poItems.{$idx}.unit_cost", 'Unit cost cannot be negative.');
                return;
            }
        }

        $subtotal     = $this->computeSubtotal();
        $shippingCost = max(0, (float) $this->shippingCost);
        $total        = $subtotal + $shippingCost;
        $status       = $markOrdered ? 'ordered' : 'draft';

        DB::transaction(function () use ($subtotal, $shippingCost, $total, $status, $markOrdered) {

            $poData = [
                'supplier_id'   => $this->supplierId,
                'notes'         => $this->notes ?: null,
                'expected_date' => $this->expectedDate ?: null,
                'shipping_cost' => $shippingCost,
                'subtotal'      => $subtotal,
                'total'         => $total,
                'status'        => $status,
                'ordered_at'    => $markOrdered ? now() : null,
            ];

            if ($this->editMode && $this->editingPoId) {
                $po = PurchaseOrder::findOrFail($this->editingPoId);
                $po->update($poData);
                // Remove old items and re-create
                $po->items()->delete();
            } else {
                $poData['po_number'] = PurchaseOrder::generatePoNumber();
                $poData['currency']  = 'Rs.';
                $po = PurchaseOrder::create($poData);
            }

            foreach ($this->poItems as $item) {
                $qty  = (int)   $item['qty_ordered'];
                $cost = (float) $item['unit_cost'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'] ?: null,
                    'product_name'      => $item['product_name'],
                    'sku'               => $item['sku'] ?: null,
                    'quantity_ordered'  => $qty,
                    'quantity_received' => 0,
                    'unit_cost'         => $cost,
                    'subtotal'          => $qty * $cost,
                ]);
            }
        });

        $label = $markOrdered ? 'created and marked as Ordered' : ($this->editMode ? 'updated' : 'created as Draft');
        session()->flash('success', "Purchase Order {$label} successfully.");
        $this->showPoModal = false;
        $this->resetPoForm();
    }

    public function savePoOrdered(): void
    {
        $this->savePo(markOrdered: true);
    }

    // ─── Mark PO as Ordered ───────────────────────────────────────

    public function markOrdered(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'draft') {
            session()->flash('error', 'Only Draft POs can be marked as Ordered.');
            return;
        }

        $po->update(['status' => 'ordered', 'ordered_at' => now()]);
        session()->flash('success', "PO #{$po->po_number} marked as Ordered.");
    }

    // ─── Cancel PO ───────────────────────────────────────────────

    public function cancelPo(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);

        if (! in_array($po->status, ['draft', 'ordered'])) {
            session()->flash('error', 'Only Draft or Ordered POs can be cancelled.');
            return;
        }

        $po->update(['status' => 'cancelled']);
        session()->flash('success', "PO #{$po->po_number} has been cancelled.");
    }

    // ─── Delete PO ───────────────────────────────────────────────

    public function deletePo(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);

        if (! in_array($po->status, ['draft', 'cancelled'])) {
            session()->flash('error', 'Only Draft or Cancelled POs can be deleted.');
            return;
        }

        $poNumber = $po->po_number;
        $po->delete(); // cascades to items
        session()->flash('success', "PO #{$poNumber} deleted.");
    }

    // ─── Open Receive Goods modal ─────────────────────────────────

    public function openReceiveModal(int $id): void
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);

        if (! in_array($po->status, ['ordered', 'partial'])) {
            session()->flash('error', 'Only Ordered or Partially Received POs can receive goods.');
            return;
        }

        $this->receivingPoId = $id;
        $this->receiveQtys   = [];

        foreach ($po->items as $item) {
            $remaining = $item->quantity_ordered - $item->quantity_received;
            $this->receiveQtys[$item->id] = max(0, $remaining);
        }

        $this->showReceiveModal = true;
    }

    // ─── Confirm Receipt of Goods ─────────────────────────────────

    public function receiveGoods(): void
    {
        if (! $this->receivingPoId) return;

        $po = PurchaseOrder::with('items.product')->findOrFail($this->receivingPoId);

        DB::transaction(function () use ($po) {
            $allFullyReceived = true;

            foreach ($po->items as $item) {
                $qtyToReceive = (int) ($this->receiveQtys[$item->id] ?? 0);

                if ($qtyToReceive <= 0) {
                    // Check if this item is still not fully received
                    if ($item->quantity_received < $item->quantity_ordered) {
                        $allFullyReceived = false;
                    }
                    continue;
                }

                // Cap at remaining to prevent over-receiving
                $remaining    = $item->quantity_ordered - $item->quantity_received;
                $qtyToReceive = min($qtyToReceive, $remaining);

                // Update item received count
                $newReceived = $item->quantity_received + $qtyToReceive;
                $item->update(['quantity_received' => $newReceived]);

                // Add stock to the linked product
                if ($item->product) {
                    $item->product->increment('stock', $qtyToReceive);
                }

                // Check if this item is now fully received
                if ($newReceived < $item->quantity_ordered) {
                    $allFullyReceived = false;
                }
            }

            // Update PO status
            $po->update([
                'status'      => $allFullyReceived ? 'received' : 'partial',
                'received_at' => $allFullyReceived ? now() : $po->received_at,
            ]);
        });

        $po->refresh();
        $label = $po->status === 'received' ? 'fully received' : 'partially received';
        session()->flash('success', "Goods for PO #{$po->po_number} {$label}. Stock updated.");

        $this->showReceiveModal = false;
        $this->receivingPoId   = null;
        $this->receiveQtys     = [];
    }

    // ─── Open PO Detail modal ─────────────────────────────────────

    public function openDetailModal(int $id): void
    {
        $this->detailPoId    = $id;
        $this->showDetailModal = true;
    }

    // ─── Reset form helper ────────────────────────────────────────

    private function resetPoForm(): void
    {
        $this->supplierId        = 0;
        $this->notes             = '';
        $this->expectedDate      = '';
        $this->shippingCost      = '0';
        $this->poItems           = [];
        $this->itemProductSearch = '';
        $this->editingPoId       = null;
        $this->editMode          = false;
        $this->resetValidation();
    }

    // ─── Render ───────────────────────────────────────────────────

    public function render()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items'])
            ->when($this->search, fn($q) =>
                $q->where('po_number', 'like', "%{$this->search}%")
                  ->orWhereHas('supplier', fn($s) =>
                      $s->where('name', 'like', "%{$this->search}%")
                  )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSupplierId, fn($q) => $q->where('supplier_id', $this->filterSupplierId))
            ->latest()
            ->paginate(15);

        $stats = [
            'draft'        => PurchaseOrder::where('status', 'draft')->count(),
            'ordered'      => PurchaseOrder::where('status', 'ordered')->count(),
            'received'     => PurchaseOrder::where('status', 'received')->count(),
            'total_value'  => (float) PurchaseOrder::whereIn('status', ['received', 'partial'])->sum('total'),
        ];

        $suppliers    = SupplierModel::active()->orderBy('name')->get(['id', 'name', 'city']);
        $detailPo     = $this->showDetailModal && $this->detailPoId
                        ? PurchaseOrder::with(['supplier', 'items.product'])->find($this->detailPoId)
                        : null;
        $receivingPo  = $this->showReceiveModal && $this->receivingPoId
                        ? PurchaseOrder::with('items.product')->find($this->receivingPoId)
                        : null;

        return view('livewire.admin.purchasing', compact(
            'purchaseOrders', 'stats', 'suppliers', 'detailPo', 'receivingPo'
        ));
    }
}
