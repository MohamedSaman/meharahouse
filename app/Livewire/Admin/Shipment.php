<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\ShipmentBatch;
use App\Models\Order;
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
    public bool   $showAssignModal    = false;
    public ?int   $assigningBatchId   = null;
    public string $assigningBatchName = '';
    public array  $selectedOrderIds   = [];
    public string $orderSearch        = '';

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

    public function advanceStatus(int $id): void
    {
        $batch    = ShipmentBatch::findOrFail($id);
        $statuses = ['collecting', 'purchased', 'packed', 'shipped', 'in_transit', 'arrived', 'distributing', 'completed'];
        $idx      = array_search($batch->status, $statuses);

        if ($idx === false || $idx >= count($statuses) - 1) {
            return;
        }

        $newStatus = $statuses[$idx + 1];
        $data      = ['status' => $newStatus];

        if ($newStatus === 'shipped') $data['shipped_at'] = now();
        if ($newStatus === 'arrived') $data['arrived_at'] = now();

        $batch->update($data);
        $batch->refresh();
        session()->flash('success', "Batch advanced to: {$batch->statusLabel()}");
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
        $this->assigningBatchId   = $batchId;
        $this->assigningBatchName = $batch->name;
        $this->selectedOrderIds   = $batch->orders()->pluck('id')->toArray();
        $this->orderSearch        = '';
        $this->showAssignModal    = true;
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

    public function saveOrderAssignment(): void
    {
        if (!$this->assigningBatchId) {
            return;
        }

        // Unassign previously assigned orders from this batch
        Order::where('shipment_batch_id', $this->assigningBatchId)
             ->update(['shipment_batch_id' => null]);

        // Assign selected orders to this batch
        if (!empty($this->selectedOrderIds)) {
            Order::whereIn('id', $this->selectedOrderIds)
                 ->update(['shipment_batch_id' => $this->assigningBatchId]);
        }

        $count = count($this->selectedOrderIds);
        $this->showAssignModal = false;
        session()->flash('success', "{$count} " . ($count === 1 ? 'order' : 'orders') . " assigned to batch.");
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
        $batches = ShipmentBatch::withCount('orders')
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('batch_number', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        // Orders for the assign modal — confirmed/sourcing/dispatched not yet in any batch,
        // or already assigned to the current batch being edited
        $assignableOrders = $this->showAssignModal
            ? Order::whereIn('status', ['confirmed', 'sourcing', 'dispatched'])
                ->where(fn($q) => $q
                    ->whereNull('shipment_batch_id')
                    ->orWhere('shipment_batch_id', $this->assigningBatchId))
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

        // Expanded batch orders for waybill management
        $expandedBatchOrders = [];
        if (!empty($this->expandedBatches)) {
            foreach ($this->expandedBatches as $batchId) {
                $expandedBatchOrders[$batchId] = Order::where('shipment_batch_id', $batchId)
                    ->with('user')
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

        return view('livewire.admin.shipment', compact('batches', 'assignableOrders', 'expandedBatchOrders', 'stats'));
    }
}
