<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\ShipmentBatch;
use App\Models\Order;
use App\Models\OrderBackorder;
use Illuminate\Support\Facades\DB;

#[Title('Shipment Batches')]
#[Layout('layouts.admin')]
class Shipment extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $search       = '';

    // ── Create / Edit Batch Modal ─────────────────────────────────────
    public bool   $showBatchModal  = false;
    public ?int   $editingBatchId  = null;
    public string $batchName       = '';
    public string $courierName     = '';
    public string $trackingNumber  = '';
    public string $courierCost     = '0';
    public string $expectedArrival = '';
    public string $batchNotes      = '';

    // ── Assign Orders Modal ───────────────────────────────────────────
    public bool   $showAssignModal      = false;
    public ?int   $assigningBatchId     = null;
    public string $assigningBatchName   = '';
    public array  $selectedOrderIds     = [];
    public array  $selectedBackorderIds = [];
    public string $orderSearch          = '';

    // ── Waybill Modal ─────────────────────────────────────────────────
    public bool   $showWaybillModal = false;
    public ?int   $waybillOrderId   = null;
    public string $waybillNumber    = '';
    public string $deliveryAgent    = '';
    public string $deliveryNotes    = '';

    // ── Batch Detail Expand (for distributing/arrived sub-tables) ─────
    public array $expandedBatches = [];

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    // ── Create Batch ──────────────────────────────────────────────────

    public function openCreateBatch(): void
    {
        $this->editingBatchId  = null;
        $this->batchName       = '';
        $this->courierName     = '';
        $this->trackingNumber  = '';
        $this->courierCost     = '0';
        $this->expectedArrival = '';
        $this->batchNotes      = '';
        $this->showBatchModal  = true;
    }

    public function openEditBatch(int $id): void
    {
        $batch = ShipmentBatch::findOrFail($id);
        $this->editingBatchId  = $id;
        $this->batchName       = $batch->name;
        $this->courierName     = $batch->courier_name ?? '';
        $this->trackingNumber  = $batch->tracking_number ?? '';
        $this->courierCost     = (string) $batch->courier_cost;
        $this->expectedArrival = $batch->expected_arrival ? $batch->expected_arrival->format('Y-m-d') : '';
        $this->batchNotes      = $batch->notes ?? '';
        $this->showBatchModal  = true;
    }

    public function saveBatch(): void
    {
        $this->validate([
            'batchName'       => ['required', 'string', 'max:200'],
            'courierName'     => ['nullable', 'string', 'max:200'],
            'trackingNumber'  => ['nullable', 'string', 'max:200'],
            'courierCost'     => ['nullable', 'numeric', 'min:0'],
            'expectedArrival' => ['nullable', 'date'],
        ]);

        $data = [
            'name'             => $this->batchName,
            'courier_name'     => $this->courierName ?: null,
            'tracking_number'  => $this->trackingNumber ?: null,
            'courier_cost'     => (float) $this->courierCost,
            'expected_arrival' => $this->expectedArrival ?: null,
            'notes'            => $this->batchNotes ?: null,
        ];

        if ($this->editingBatchId) {
            ShipmentBatch::findOrFail($this->editingBatchId)->update($data);
            session()->flash('success', 'Batch updated successfully.');
        } else {
            $data['batch_number'] = ShipmentBatch::generateBatchNumber();
            $data['status']       = 'collecting';
            ShipmentBatch::create($data);
            session()->flash('success', 'Shipment batch created.');
        }

        $this->showBatchModal = false;
    }

    // ── Advance Batch Status ──────────────────────────────────────────

    // Bug 11: Track unpaid order count before delivery confirmation
    public int    $unpaidOrderCount   = 0;
    public bool   $showUnpaidWarning  = false;
    public int    $pendingAdvanceBatchId = 0;

    /**
     * Called from the advance confirm modal in the blade.
     * When advancing to 'completed' (which delivers all orders), check for unpaid balances first.
     */
    public function advanceStatus(int $id): void
    {
        $batch    = ShipmentBatch::findOrFail($id);
        $statuses = ['collecting', 'packed', 'shipped', 'in_transit', 'arrived', 'distributing', 'completed'];
        $idx      = array_search($batch->status, $statuses);

        if ($idx === false || $idx >= count($statuses) - 1) {
            return;
        }

        // Prevent advancing a batch that has no orders assigned
        $hasOrders = $batch->orders()->exists() || $batch->backorders()->exists();
        if (!$hasOrders) {
            session()->flash('error', 'Cannot advance a batch with no orders assigned.');
            return;
        }

        $newStatus = $statuses[$idx + 1];

        if (in_array($newStatus, ['distributing', 'completed'])) {
            $unpaidOrders = $batch->orders()
                ->whereIn('status', ['confirmed', 'sourcing', 'dispatched'])
                ->where('balance_amount', '>', 0)
                ->where('payment_status', '!=', 'paid')
                ->count();

            $unpaidBackorderParents = $batch->backorders()
                ->whereHas('order', function ($query) {
                    $query->where('balance_amount', '>', 0)
                          ->where('payment_status', '!=', 'paid');
                })
                ->count();

            $unpaid = $unpaidOrders + $unpaidBackorderParents;

            if ($unpaid > 0) {
                // Hard block: do not allow delivery if orders have due balance
                $this->unpaidOrderCount    = $unpaid;
                $this->showUnpaidWarning   = true;
                return;
            }
        }

        // Reset warning state before proceeding
        $this->showUnpaidWarning     = false;
        $this->unpaidOrderCount      = 0;

        $data = ['status' => $newStatus];

        if ($newStatus === 'shipped')  $data['shipped_at'] = now();
        if ($newStatus === 'arrived')  $data['arrived_at'] = now();

        $batch->update($data);
        $batch->refresh();

        // ── Sync orders & backorders in this batch ────────────────────
        $this->syncBatchOrderStatuses($batch, $newStatus);

        session()->flash('success', "Batch advanced to: {$batch->statusLabel()}");
    }



    public function dismissUnpaidWarning(): void
    {
        $this->showUnpaidWarning     = false;
        $this->pendingAdvanceBatchId = 0;
        $this->unpaidOrderCount      = 0;
    }

    /**
     * When a batch status changes, cascade to the orders and backorders inside it.
     *
     * Batch status  →  Order status   (Backorder status)
     * purchased     →  sourcing
     * shipped       →  dispatched
     * completed     →  delivered      (dispatched backorders → delivered)
     */
    private function syncBatchOrderStatuses(ShipmentBatch $batch, string $newStatus): void
    {
        // Map batch status → order status
        $orderStatusMap = [
            'shipped'      => 'dispatched',
            'completed'    => 'delivered',
        ];

        if (isset($orderStatusMap[$newStatus])) {
            $targetOrderStatus = $orderStatusMap[$newStatus];

            // Only update orders that are still "behind" the new status
            $eligibleOrderStatuses = match ($targetOrderStatus) {
                'sourcing'   => ['confirmed'],
                'dispatched' => ['confirmed', 'sourcing'],
                'delivered'  => ['confirmed', 'sourcing', 'dispatched'],
                default      => [],
            };

            if (!empty($eligibleOrderStatuses)) {
                $batch->orders()
                    ->whereIn('status', $eligibleOrderStatuses)
                    ->each(function ($order) use ($targetOrderStatus) {
                        $order->logStatus(
                            $targetOrderStatus,
                            "Auto-updated: shipment batch advanced to {$targetOrderStatus}.",
                            auth()->id()
                        );
                        $order->update(['status' => $targetOrderStatus]);
                    });
            }
        }

        // Backorder sync
        if ($newStatus === 'distributing') {
            // Backorders that are ready → dispatched when we start distributing locally
            $batch->backorders()
                ->where('status', 'ready')
                ->each(function ($bo) {
                    $bo->update([
                        'status'        => 'dispatched',
                        'dispatched_at' => now(),
                        'dispatched_by' => auth()->id(),
                    ]);
                });
        }

        if ($newStatus === 'completed') {
            // Backorders that are dispatched → delivered when batch completes
            $affectedOrderIds = collect();
            $batch->backorders()
                ->where('status', 'dispatched')
                ->each(function ($bo) use (&$affectedOrderIds) {
                    $bo->update([
                        'status'       => 'delivered',
                        'delivered_at' => now(),
                    ]);
                    $affectedOrderIds->push($bo->order_id);
                });

            // Update parent order status for backorder-linked orders
            $affectedOrderIds->unique()->each(function ($orderId) {
                $order = \App\Models\Order::find($orderId);
                if (!$order) return;

                // Check if any backorders are still not done
                $remaining = \App\Models\OrderBackorder::where('order_id', $orderId)
                    ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                    ->count();

                if ($remaining === 0 && in_array($order->status, ['sourcing', 'confirmed', 'dispatched'])) {
                    $order->logStatus('delivered', 'All backorders delivered via shipment batch.', auth()->id());
                    $order->update(['status' => 'delivered']);
                }
            });
        }
    }

    // ── Toggle Expanded Batch (for waybill sub-table) ─────────────────

    public function toggleExpand(int $batchId): void
    {
        if (in_array($batchId, $this->expandedBatches)) {
            $this->expandedBatches = array_values(array_filter($this->expandedBatches, fn($id) => $id !== $batchId));
        } else {
            $this->expandedBatches[] = $batchId;
        }
    }

    // ── Assign Orders to Batch ────────────────────────────────────────

    public function openAssignModal(int $batchId): void
    {
        $batch = ShipmentBatch::findOrFail($batchId);
        $this->assigningBatchId     = $batchId;
        $this->assigningBatchName   = $batch->name;
        $this->selectedOrderIds     = $batch->orders()->pluck('id')->toArray();
        $this->selectedBackorderIds = $batch->backorders()->pluck('id')->toArray();
        $this->orderSearch          = '';
        $this->showAssignModal      = true;
    }

    public function toggleOrderSelection(int $orderId): void
    {
        if (in_array($orderId, $this->selectedOrderIds)) {
            $this->selectedOrderIds = array_values(
                array_filter($this->selectedOrderIds, fn($id) => $id !== $orderId)
            );
        } else {
            $this->selectedOrderIds[] = $orderId;
        }
    }

    public function toggleBackorderSelection(int $boId): void
    {
        $ids = array_map('intval', $this->selectedBackorderIds);
        if (in_array($boId, $ids)) {
            $this->selectedBackorderIds = array_values(
                array_filter($ids, fn($id) => $id !== $boId)
            );
        } else {
            $ids[] = $boId;
            $this->selectedBackorderIds = $ids;
        }
    }

    public function saveOrderAssignment(): void
    {
        if (!$this->assigningBatchId) return;

        // Regular orders
        Order::where('shipment_batch_id', $this->assigningBatchId)
             ->update(['shipment_batch_id' => null]);
        if (!empty($this->selectedOrderIds)) {
            Order::whereIn('id', $this->selectedOrderIds)
                 ->update(['shipment_batch_id' => $this->assigningBatchId]);
        }

        // Backorders
        OrderBackorder::where('shipment_batch_id', $this->assigningBatchId)
                      ->update(['shipment_batch_id' => null]);
        if (!empty($this->selectedBackorderIds)) {
            OrderBackorder::whereIn('id', $this->selectedBackorderIds)
                          ->update(['shipment_batch_id' => $this->assigningBatchId]);
        }

        $orders    = count($this->selectedOrderIds);
        $backorders = count($this->selectedBackorderIds);
        $this->showAssignModal = false;
        session()->flash('success', "{$orders} order(s) and {$backorders} backorder(s) assigned to batch.");
    }

    // ── Waybill / Local Delivery Tracking ────────────────────────────

    public function openWaybillModal(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $this->waybillOrderId   = $orderId;
        $this->waybillNumber    = $order->waybill_number ?? '';
        $this->deliveryAgent    = $order->delivery_agent ?? '';
        $this->deliveryNotes    = $order->delivery_notes ?? '';
        $this->showWaybillModal = true;
    }

    public function saveWaybill(): void
    {
        $this->validate([
            'waybillNumber' => ['nullable', 'string', 'max:100'],
            'deliveryAgent' => ['nullable', 'string', 'max:200'],
            'deliveryNotes' => ['nullable', 'string', 'max:500'],
        ]);

        Order::findOrFail($this->waybillOrderId)->update([
            'waybill_number' => $this->waybillNumber ?: null,
            'delivery_agent' => $this->deliveryAgent ?: null,
            'delivery_notes' => $this->deliveryNotes ?: null,
        ]);

        $this->showWaybillModal = false;
        session()->flash('success', 'Waybill details saved.');
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $batches = ShipmentBatch::withCount(['orders', 'backorders'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('batch_number', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        // Orders for the assign modal — confirmed/sourcing/dispatched not yet in any batch,
        // or already assigned to the current batch being edited.
        // Exclude orders whose items are fully handled by an active backorder (ready/dispatched)
        // — those appear separately in the Backorders section to avoid showing the same order twice.
        $assignableOrders = $this->showAssignModal
            ? Order::where(function($q) {
                    $q->whereNull('shipment_batch_id')
                      ->where('status', 'confirmed')
                      ->orWhere('shipment_batch_id', $this->assigningBatchId);
                })
                ->whereHas('items', fn($q) => $q->whereIn('status', ['active', 'replaced']))
                ->whereDoesntHave('backorders', fn($q) => $q->where('status', 'repurchasing'))
                ->when($this->orderSearch, fn($q) => $q
                    ->where('order_number', 'like', "%{$this->orderSearch}%")
                    ->orWhere(
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.full_name'))"),
                        'like',
                        "%{$this->orderSearch}%"
                    ))
                ->latest()
                ->limit(50)
                ->get()
            : collect();

        // Backorders available for batch assignment (ready status, no batch or in current batch)
        $assignableBackorders = $this->showAssignModal
            ? OrderBackorder::with(['order.user'])
                ->whereIn('status', ['ready', 'dispatched'])
                ->where(fn($q) => $q
                    ->whereNull('shipment_batch_id')
                    ->orWhere('shipment_batch_id', $this->assigningBatchId))
                ->when($this->orderSearch, fn($q) => $q
                    ->where('product_name', 'like', "%{$this->orderSearch}%")
                    ->orWhere('backorder_number', 'like', "%{$this->orderSearch}%")
                    ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', "%{$this->orderSearch}%")))
                ->latest()
                ->limit(30)
                ->get()
            : collect();

        // Expanded batch orders for waybill management
        $expandedBatchOrders    = [];
        $expandedBatchBackorders = [];
        if (!empty($this->expandedBatches)) {
            foreach ($this->expandedBatches as $batchId) {
                $expandedBatchOrders[$batchId] = Order::where('shipment_batch_id', $batchId)
                    ->with('user')
                    ->latest()
                    ->get();
                $expandedBatchBackorders[$batchId] = OrderBackorder::where('shipment_batch_id', $batchId)
                    ->with(['order.user'])
                    ->latest()
                    ->get();
            }
        }

        $stats = [
            'active'     => ShipmentBatch::whereNotIn('status', ['completed'])->count(),
            'in_transit' => ShipmentBatch::where('status', 'in_transit')->count(),
            'arrived'    => ShipmentBatch::whereIn('status', ['arrived', 'distributing'])->count(),
            'completed'  => ShipmentBatch::where('status', 'completed')->count(),
        ];

        return view('livewire.admin.shipment', compact('batches', 'assignableOrders', 'assignableBackorders', 'expandedBatchOrders', 'expandedBatchBackorders', 'stats'));
    }
}
