<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\OrderBackorder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Order;

#[Title('Purchasing')]
#[Layout('layouts.admin')]
class Purchasing extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────
    public string $search           = '';
    public string $filterStatus     = '';
    public int    $filterSupplierId = 0;

    // ── Create / Edit PO Modal ────────────────────────────────────────
    public bool   $showPoModal = false;
    public bool   $editMode    = false;
    public int    $editPoId    = 0;
    public int    $supplierId  = 0;
    public string $expectedDate  = '';
    public string $shippingCost  = '0';
    public string $notes         = '';
    public array  $poItems       = [];
    public string $itemProductSearch = '';

    // ── Receive Goods Modal ───────────────────────────────────────────
    public bool          $showReceiveModal = false;
    public ?PurchaseOrder $receivingPo     = null;
    public array          $receiveQtys     = [];

    // ── Detail Modal ──────────────────────────────────────────────────
    public bool           $showDetailModal = false;
    public ?PurchaseOrder $detailPo        = null;

    // ── Purchasing Plan Modal ─────────────────────────────────────────
    public bool  $showPlanModal = false;
    public array $planItems     = [];
    public array $planOrderIds  = [];

    // ── Ready Orders Modal ────────────────────────────────────────────
    public bool  $showReadyOrdersModal = false;
    public int   $readyOrdersCount     = 0;
    public array $readyOrderIds        = [];

    // ── Backorder Fulfillment Modal (after receiving goods) ───────────
    public bool  $showBackorderFulfillModal = false;
    public array $fulfillableBackorders     = [];

    // ── Lifecycle ─────────────────────────────────────────────────────

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterSupplierId(): void { $this->resetPage(); }

    // ── Computed: product search results ─────────────────────────────

    #[Computed]
    public function productSearchResults()
    {
        if (strlen(trim($this->itemProductSearch)) < 2) {
            return collect();
        }

        return Product::where('name', 'like', "%{$this->itemProductSearch}%")
            ->orWhere('sku', 'like', "%{$this->itemProductSearch}%")
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    // ── Create / Edit PO ─────────────────────────────────────────────

    public function openCreatePo(): void
    {
        $this->reset(['editMode', 'editPoId', 'supplierId', 'expectedDate', 'shippingCost', 'notes', 'poItems', 'itemProductSearch']);
        $this->shippingCost = '0';
        $this->showPoModal  = true;
    }

    public function openEditPo(int $id): void
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);

        $this->editMode     = true;
        $this->editPoId     = $po->id;
        $this->supplierId   = $po->supplier_id ?? 0;
        $this->expectedDate = $po->expected_date?->format('Y-m-d') ?? '';
        $this->shippingCost = (string) $po->shipping_cost;
        $this->notes        = $po->notes ?? '';
        $this->poItems      = $po->items->map(fn($item) => [
            'product_id'   => $item->product_id,
            'product_name' => $item->product_name,
            'sku'          => $item->sku ?? '',
            'size'         => $item->size ?? '',
            'qty_ordered'  => $item->quantity_ordered,
            'unit_cost'    => $item->unit_cost,
        ])->toArray();

        $this->itemProductSearch = '';
        $this->showPoModal = true;
    }

    public function addPoItem(int $productId): void
    {
        $product = Product::findOrFail($productId);

        // Avoid duplicates
        foreach ($this->poItems as $item) {
            if ($item['product_id'] === $productId) {
                $this->itemProductSearch = '';
                return;
            }
        }

        // Use last received cost price, fallback to product cost_price, then sale_price, then price
        $lastCost = PurchaseOrderItem::where('product_id', $productId)
            ->whereHas('purchaseOrder', fn($q) => $q->whereIn('status', ['received', 'partial']))
            ->latest()
            ->value('unit_cost');

        $unitCost = $lastCost ?? $product->cost_price ?? $product->sale_price ?? $product->price ?? 0;

        $this->poItems[] = [
            'product_id'   => $productId,
            'product_name' => $product->name,
            'sku'          => $product->sku ?? '',
            'size'         => '',
            'qty_ordered'  => 1,
            'unit_cost'    => (float) $unitCost,
        ];

        $this->itemProductSearch = '';
    }

    public function removePoItem(int $idx): void
    {
        array_splice($this->poItems, $idx, 1);
        $this->poItems = array_values($this->poItems);
    }

    public function savePo(): void
    {
        $this->persistPo('draft');
    }

    public function savePoOrdered(): void
    {
        $this->persistPo('ordered');
    }

    private function persistPo(string $status): void
    {
        $this->validate([
            'supplierId'            => ['required', 'integer', 'min:1'],
            'poItems'               => ['required', 'array', 'min:1'],
            'poItems.*.product_name'=> ['required', 'string', 'max:255'],
            'poItems.*.qty_ordered' => ['required', 'integer', 'min:1'],
            'poItems.*.unit_cost'   => ['required', 'numeric', 'min:0'],
            'poItems.*.size'        => ['required', 'string', 'min:1', 'max:50'],
        ], [
            'supplierId.min'    => 'Please select a supplier.',
            'poItems.min'       => 'Add at least one item to the purchase order.',
            'poItems.*.size.required' => 'The size field is mandatory for each item.',
            'poItems.*.size.min'      => 'The size field cannot be empty.',
        ]);

        $subtotal = collect($this->poItems)->sum(
            fn($i) => (int) $i['qty_ordered'] * (float) $i['unit_cost']
        );
        $shipping = max(0, (float) $this->shippingCost);
        $total    = $subtotal + $shipping;

        $data = [
            'supplier_id'   => $this->supplierId,
            'status'        => $status,
            'subtotal'      => $subtotal,
            'shipping_cost' => $shipping,
            'total'         => $total,
            'notes'         => $this->notes ?: null,
            'expected_date' => $this->expectedDate ?: null,
        ];

        if ($status === 'ordered') {
            $data['ordered_at'] = now();
        }

        if ($this->editMode) {
            $po = PurchaseOrder::findOrFail($this->editPoId);
            $po->update($data);
            $po->items()->delete();
        } else {
            $data['po_number'] = PurchaseOrder::generatePoNumber();
            $po = PurchaseOrder::create($data);
        }

        foreach ($this->poItems as $item) {
            $po->items()->create([
                'product_id'        => $item['product_id'] ?? null,
                'product_name'      => $item['product_name'],
                'sku'               => $item['sku'] ?? null,
                'size'              => $item['size'] ?? null,
                'quantity_ordered'  => (int) $item['qty_ordered'],
                'quantity_received' => 0,
                'unit_cost'         => (float) $item['unit_cost'],
                'subtotal'          => (int) $item['qty_ordered'] * (float) $item['unit_cost'],
            ]);
        }

        $this->showPoModal = false;
        session()->flash('success', $status === 'ordered'
            ? "Purchase Order #{$po->po_number} created and marked as Ordered."
            : "Purchase Order #{$po->po_number} saved as Draft."
        );
    }

    // ── Status Actions ────────────────────────────────────────────────

    public function markOrdered(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'ordered', 'ordered_at' => now()]);
        session()->flash('success', "PO #{$po->po_number} marked as Ordered.");
    }

    public function cancelPo(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'cancelled']);
        session()->flash('success', "PO #{$po->po_number} cancelled.");
    }

    public function deletePo(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->items()->delete();
        $po->delete();
        session()->flash('success', 'Purchase Order deleted.');
    }

    // ── Receive Goods Modal ───────────────────────────────────────────

    public function openReceiveModal(int $id): void
    {
        $this->receivingPo   = PurchaseOrder::with('items.product')->findOrFail($id);
        $this->receiveQtys   = [];
        $this->showReceiveModal = true;
    }

    public function receiveGoods(): void
    {
        if (!$this->receivingPo) return;

        $anyReceived = false;

        foreach ($this->receivingPo->items as $item) {
            $qty = (int) ($this->receiveQtys[$item->id] ?? 0);
            if ($qty <= 0) continue;

            $remaining = $item->quantity_ordered - $item->quantity_received;
            $qty = min($qty, $remaining);

            $item->increment('quantity_received', $qty);
            $item->refresh();

            // Update stock and cost price
            if ($item->product) {
                $item->product->increment('stock', $qty);
                // Track latest cost price on the product for future PO pre-fill
                $item->product->update(['cost_price' => $item->unit_cost]);
            }

            $anyReceived = true;
        }

        if (!$anyReceived) {
            session()->flash('error', 'No quantities entered. Nothing was received.');
            return;
        }

        // Determine new PO status
        $po = $this->receivingPo->fresh('items');
        $allReceived = $po->items->every(fn($i) => $i->quantity_received >= $i->quantity_ordered);
        $anyPartial  = $po->items->some(fn($i) => $i->quantity_received > 0);

        $newStatus = $allReceived ? 'received' : ($anyPartial ? 'partial' : $po->status);
        $updateData = ['status' => $newStatus];
        if ($allReceived) $updateData['received_at'] = now();
        $po->update($updateData);

        $this->showReceiveModal = false;
        $this->receivingPo      = null;

        // Check for orders that can now be confirmed.
        // If any ready orders exist, the backorder modal is deferred until
        // after the ready-orders modal is dismissed, so both modals never
        // overlap at the same time.
        $this->checkReadyOrders();

        if (!$this->showReadyOrdersModal) {
            // No ready-orders modal — show backorder modal immediately.
            $this->checkFulfillableBackorders();
        }
        // If showReadyOrdersModal IS true, checkFulfillableBackorders() will
        // be called from autoConfirmReadyOrders() / dismissReadyOrders().

        session()->flash('success', 'Goods received and stock updated successfully.');
    }

    private function checkReadyOrders(): void
    {
        $pendingOrders = Order::with('items.product')
            ->whereIn('status', ['payment_received', 'new'])
            ->get();

        $readyIds = [];
        foreach ($pendingOrders as $order) {
            $canConfirm = true;
            foreach ($order->items as $item) {
                $product = $item->product;
                $sizeMismatch = false;
                if ($product && !empty($item->size) && is_array($product->sizes) && !empty($product->sizes)) {
                    $sizeMismatch = !collect($product->sizes)->map(fn($s) => strtolower(trim($s)))->contains(strtolower(trim($item->size)));
                }

                if (!$product || $product->stock < $item->quantity || $sizeMismatch) {
                    $canConfirm = false;
                    break;
                }
            }
            if ($canConfirm) $readyIds[] = $order->id;
        }

        if (!empty($readyIds)) {
            $this->readyOrderIds       = $readyIds;
            $this->readyOrdersCount    = count($readyIds);
            $this->showReadyOrdersModal = true;
        }
    }

    public function autoConfirmReadyOrders(): void
    {
        foreach ($this->readyOrderIds as $orderId) {
            $order = Order::with('items.product')->find($orderId);
            if (!$order) continue;

            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            $order->logStatus('confirmed', 'Auto-confirmed after stock received from supplier.', auth()->id());
            $order->update(['status' => 'confirmed']);
        }

        $confirmedCount = $this->readyOrdersCount;

        $this->showReadyOrdersModal = false;
        $this->readyOrderIds        = [];
        $this->readyOrdersCount     = 0;

        session()->flash('success', "{$confirmedCount} order(s) auto-confirmed.");

        // Now it is safe to show the backorder fulfillment modal if applicable.
        $this->checkFulfillableBackorders();
    }

    public function dismissReadyOrders(): void
    {
        $this->showReadyOrdersModal = false;
        $this->readyOrderIds        = [];
        $this->readyOrdersCount     = 0;

        // Now it is safe to show the backorder fulfillment modal if applicable.
        $this->checkFulfillableBackorders();
    }

    // ── Backorder Fulfillment After Receiving ─────────────────────────

    private function checkFulfillableBackorders(): void
    {
        $backorders = OrderBackorder::with(['product', 'order'])
            ->whereIn('status', ['pending', 'repurchasing'])
            ->get();

        $fulfillable = [];
        $virtualStock = [];

        foreach ($backorders as $bo) {
            $pid = $bo->isReplacement() ? $bo->replacement_product_id : $bo->product_id;
            if (!$pid) continue;

            if (!isset($virtualStock[$pid])) {
                $product = $bo->isReplacement() ? $bo->replacementProduct : $bo->product;
                $virtualStock[$pid] = (int) ($product?->stock ?? 0);
            }

            if ($virtualStock[$pid] >= $bo->short_qty) {
                $fulfillable[] = [
                    'id'           => $bo->id,
                    'order_number' => $bo->order?->order_number ?? 'N/A',
                    'order_id'     => $bo->order_id,
                    'product_name' => $bo->product_name,
                    'size'         => $bo->size ?? '',
                    'short_qty'    => $bo->short_qty,
                    'stock'        => $virtualStock[$pid],
                ];
                $virtualStock[$pid] -= $bo->short_qty;
            }
        }

        if (!empty($fulfillable)) {
            $this->fulfillableBackorders     = $fulfillable;
            $this->showBackorderFulfillModal = true;
        }
    }

    public function fulfillAllBackorders(): void
    {
        $markedCount = 0;

        foreach ($this->fulfillableBackorders as $item) {
            $bo = OrderBackorder::find($item['id']);
            if (!$bo || $bo->status === 'ready') continue;

            // Re-query product stock fresh to account for previous iterations
            if ($bo->isReplacement() && $bo->replacementProduct) {
                $freshProduct = \App\Models\Product::find($bo->replacement_product_id);
                if (!$freshProduct || $freshProduct->stock < $bo->short_qty) continue;
                $freshProduct->decrement('stock', $bo->short_qty);
            } else {
                $freshProduct = \App\Models\Product::find($bo->product_id);
                if (!$freshProduct || $freshProduct->stock < $bo->short_qty) continue;
                $freshProduct->decrement('stock', $bo->short_qty);
            }

            $bo->update(['status' => 'ready']);
            $markedCount++;
        }

        $this->showBackorderFulfillModal = false;
        $this->fulfillableBackorders     = [];
        session()->flash('success', "{$markedCount} backorder(s) marked as ready. Go to Back Orders to dispatch them.");
    }

    public function dismissBackorderFulfill(): void
    {
        $this->showBackorderFulfillModal = false;
        $this->fulfillableBackorders     = [];
    }

    // ── Backorder Actions ─────────────────────────────────────────────

    /**
     * Mark a backorder record as ready from the purchasing page.
     * Stock deduction and full lifecycle completion are managed from the Backorders page.
     */
    public function fulfillBackorder(int $backorderId): void
    {
        $backorder = OrderBackorder::findOrFail($backorderId);

        if ($backorder->status !== 'ready') {
            if ($backorder->isReplacement() && $backorder->replacementProduct) {
                if ($backorder->replacementProduct->stock >= $backorder->short_qty) {
                    $backorder->replacementProduct->decrement('stock', $backorder->short_qty);
                }
            } else {
                if ($backorder->product && $backorder->product->stock >= $backorder->short_qty) {
                    $backorder->product->decrement('stock', $backorder->short_qty);
                }
            }
        }

        $backorder->update(['status' => 'ready']);
        session()->flash('success', 'Backorder marked as ready. Stock is now reserved. Go to Back Orders to dispatch it.');
    }

    // ── Detail Modal ──────────────────────────────────────────────────

    public function openDetailModal(int $id): void
    {
        $this->detailPo        = PurchaseOrder::with(['supplier', 'items'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    // ── Purchasing Plan ───────────────────────────────────────────────

    public function generatePurchasingPlan(): void
    {
        // BUG WS-1.3 fix: Only include confirmed/sourcing orders in purchasing plan.
        // Unconfirmed orders (new, payment_received) should not appear because admin
        // hasn't verified the payment or confirmed the order yet.
        $orders = Order::with('items.product')
            ->whereIn('status', ['confirmed', 'sourcing'])
            ->get();

        $this->planOrderIds = $orders->pluck('id')->toArray();

        $needed = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $pid = $item->product_id;
                $size = $item->size ?: '';
                $key = $pid . '_' . $size;
                if (!$pid) continue;
                if (!isset($needed[$key])) {
                    $needed[$key] = [
                        'product_id'      => $pid,
                        'product_name'    => $item->product_name,
                        'sku'             => $item->product?->sku ?? '',
                        'size'            => $size,
                        'qty_needed'      => 0,
                        'order_count'     => 0,
                        'backorder_qty'   => 0,
                        'backorder_count' => 0,
                        'current_stock'   => $item->product?->stock ?? 0, // Caution: shared stock
                    ];
                }
                $needed[$key]['qty_needed']  += $item->quantity;
                $needed[$key]['order_count'] += 1;
            }
        }

        // Merge pending backorders (repurchase) into the plan
        $backorders = OrderBackorder::with('product')
            ->whereIn('status', ['pending', 'repurchasing'])
            ->get();

        foreach ($backorders as $bo) {
            $pid = $bo->product_id;
            $size = $bo->size ?: '';
            $key = $pid . '_' . $size;
            if (!$pid) continue;
            if (!isset($needed[$key])) {
                $needed[$key] = [
                    'product_id'      => $pid,
                    'product_name'    => $bo->product_name,
                    'sku'             => $bo->product?->sku ?? '',
                    'size'            => $size,
                    'qty_needed'      => 0,
                    'order_count'     => 0,
                    'backorder_qty'   => 0,
                    'backorder_count' => 0,
                    'current_stock'   => $bo->product?->stock ?? 0,
                ];
            }
            $needed[$key]['backorder_qty']   += $bo->short_qty;
            $needed[$key]['backorder_count'] += 1;
        }

        $this->planItems = collect($needed)->map(function ($row) {
            $totalNeeded       = $row['qty_needed'] + $row['backorder_qty'];
            $row['total_needed'] = $totalNeeded;
            $row['to_buy']     = max(0, $totalNeeded - $row['current_stock']);
            return $row;
        })->sortByDesc('to_buy')->values()->toArray();

        $this->showPlanModal = true;
    }

    /**
     * Export the full purchasing plan as a CSV with customer details.
     * Section A: Summary (product + size + qty to buy)
     * Section B: Customer details (who ordered what)
     */
    public function exportPlan(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Re-run the plan logic to get fresh data
        $orders = Order::with('items.product')
            ->whereIn('status', ['new', 'payment_received', 'confirmed', 'sourcing'])
            ->get();

        $backorders = OrderBackorder::with(['product', 'order'])
            ->whereIn('status', ['pending', 'repurchasing'])
            ->get();

        // Build product-keyed map with full order details
        $needed  = [];
        $details = []; // [key => [order rows]]

        foreach ($orders as $order) {
            $addr = $order->shipping_address ?? [];
            foreach ($order->items as $item) {
                $pid  = $item->product_id;
                $size = $item->size ?: '';
                $key  = $pid . '_' . $size;
                if (!$pid) continue;

                if (!isset($needed[$key])) {
                    $needed[$key] = [
                        'product_name'   => $item->product_name,
                        'sku'            => $item->product?->sku ?? '',
                        'size'           => $size,
                        'current_stock'  => (int) ($item->product?->stock ?? 0),
                        'order_qty'      => 0,
                        'backorder_qty'  => 0,
                        'order_count'    => 0,
                        'backorder_count'=> 0,
                    ];
                    $details[$key] = [];
                }

                $needed[$key]['order_qty']   += (int) $item->quantity;
                $needed[$key]['order_count'] += 1;

                $details[$key][] = [
                    'type'          => 'Order',
                    'ref'           => $order->order_number,
                    'customer'      => $addr['full_name'] ?? ($order->user?->name ?? ''),
                    'phone'         => $addr['phone'] ?? '',
                    'address'       => trim(($addr['address'] ?? '') . ', ' . ($addr['city'] ?? '') . ', ' . ($addr['district'] ?? ''), ', '),
                    'product'       => $item->product_name,
                    'size'          => $size,
                    'color'         => $item->color ?? '',
                    'qty'           => (int) $item->quantity,
                    'order_status'  => $order->status,
                    'payment_status'=> $order->payment_status,
                ];
            }
        }

        foreach ($backorders as $bo) {
            $pid  = $bo->product_id;
            $size = $bo->size ?: '';
            $key  = $pid . '_' . $size;
            if (!$pid) continue;

            if (!isset($needed[$key])) {
                $needed[$key] = [
                    'product_name'   => $bo->product_name,
                    'sku'            => $bo->product?->sku ?? '',
                    'size'           => $size,
                    'current_stock'  => (int) ($bo->product?->stock ?? 0),
                    'order_qty'      => 0,
                    'backorder_qty'  => 0,
                    'order_count'    => 0,
                    'backorder_count'=> 0,
                ];
                $details[$key] = [];
            }

            $needed[$key]['backorder_qty']   += (int) $bo->short_qty;
            $needed[$key]['backorder_count'] += 1;

            $addr = $bo->order?->shipping_address ?? [];
            $details[$key][] = [
                'type'          => 'Backorder',
                'ref'           => $bo->backorder_number . ' (' . ($bo->order?->order_number ?? '') . ')',
                'customer'      => $addr['full_name'] ?? ($bo->order?->user?->name ?? ''),
                'phone'         => $addr['phone'] ?? '',
                'address'       => trim(($addr['address'] ?? '') . ', ' . ($addr['city'] ?? '') . ', ' . ($addr['district'] ?? ''), ', '),
                'product'       => $bo->product_name,
                'size'          => $size,
                'color'         => '',
                'qty'           => (int) $bo->short_qty,
                'order_status'  => 'backorder',
                'payment_status'=> '',
            ];
        }

        $filename = 'purchasing-plan-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($needed, $details) {
            $handle = fopen('php://output', 'w');

            // ── SECTION A: SUMMARY ────────────────────────────────────────
            fputcsv($handle, ['=== PURCHASING PLAN SUMMARY — ' . now()->format('d M Y') . ' ===']);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Product Name', 'SKU', 'Size',
                'Current Stock',
                'Qty (Orders)', 'Orders Count',
                'Qty (Backorders)', 'Backorder Count',
                'Total Needed', 'TO BUY',
            ]);

            foreach ($needed as $row) {
                $total  = $row['order_qty'] + $row['backorder_qty'];
                $to_buy = max(0, $total - $row['current_stock']);
                fputcsv($handle, [
                    $row['product_name'],
                    $row['sku'],
                    $row['size'] ?: '—',
                    $row['current_stock'],
                    $row['order_qty'],
                    $row['order_count'],
                    $row['backorder_qty'],
                    $row['backorder_count'],
                    $total,
                    $to_buy,
                ]);
            }

            // ── SECTION B: CUSTOMER DETAILS ───────────────────────────────
            fputcsv($handle, []);
            fputcsv($handle, []);
            fputcsv($handle, ['=== CUSTOMER DETAILS ===']);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Type', 'Reference', 'Customer Name', 'Phone', 'Address',
                'Product', 'Size', 'Color', 'Qty',
                'Order Status', 'Payment Status',
            ]);

            foreach ($needed as $key => $row) {
                if (empty($details[$key])) continue;

                // Sub-header for this product group
                fputcsv($handle, []);
                $total  = $row['order_qty'] + $row['backorder_qty'];
                $to_buy = max(0, $total - $row['current_stock']);
                fputcsv($handle, [
                    '>> ' . $row['product_name'] . ($row['size'] ? ' [' . $row['size'] . ']' : ''),
                    'TO BUY: ' . $to_buy,
                    'Stock: ' . $row['current_stock'],
                ]);

                foreach ($details[$key] as $d) {
                    fputcsv($handle, [
                        $d['type'],
                        $d['ref'],
                        $d['customer'],
                        $d['phone'],
                        $d['address'],
                        $d['product'],
                        $d['size'] ?: '—',
                        $d['color'] ?: '—',
                        $d['qty'],
                        ucfirst(str_replace('_', ' ', $d['order_status'])),
                        ucfirst(str_replace('_', ' ', $d['payment_status'])),
                    ]);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function loadPlanIntoPoModal(): void
    {
        $this->showPlanModal = false;

        $items = [];
        foreach ($this->planItems as $item) {
            if ($item['to_buy'] <= 0) continue;

            $pid = $item['product_id'] ?? null;

            // Auto-fill last cost price from previous received POs
            $lastCost = 0;
            if ($pid) {
                $lastPoi = PurchaseOrderItem::where('product_id', $pid)
                    ->whereHas('purchaseOrder', fn($q) => $q->whereIn('status', ['received', 'partial']))
                    ->latest()
                    ->first();
                $lastCost = $lastPoi?->unit_cost ?? Product::find($pid)?->cost_price ?? 0;
            }

            $items[] = [
                'product_id'   => $pid,
                'product_name' => $item['product_name'],
                'sku'          => $item['sku'] ?? '',
                'size'         => $item['size'] ?? '',
                'qty_ordered'  => $item['to_buy'],
                'unit_cost'    => (float) $lastCost,
            ];
        }

        $this->editMode     = false;
        $this->editPoId     = 0;
        $this->supplierId   = 0;
        $this->expectedDate = '';
        $this->shippingCost = '0';
        $this->notes        = 'Auto-generated from purchasing plan.';
        $this->poItems      = $items;
        $this->showPoModal  = true;
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items'])
            ->when($this->search, fn($q) =>
                $q->where('po_number', 'like', "%{$this->search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSupplierId, fn($q) => $q->where('supplier_id', $this->filterSupplierId))
            ->latest()
            ->paginate(20);

        $stats = [
            'draft'       => PurchaseOrder::where('status', 'draft')->count(),
            'ordered'     => PurchaseOrder::where('status', 'ordered')->count(),
            'received'    => PurchaseOrder::where('status', 'received')->count(),
            'total_value' => PurchaseOrder::whereIn('status', ['ordered', 'partial', 'received'])->sum('total'),
        ];

        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.admin.purchasing', compact('purchaseOrders', 'stats', 'suppliers'));
    }
}
