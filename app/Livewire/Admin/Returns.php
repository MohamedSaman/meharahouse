<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\OrderReturn;
use App\Models\Order;

#[Title('Returns Management')]
class Returns extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $search       = '';

    // ── Create Return Modal ───────────────────────────────────────────
    public bool   $showCreateModal  = false;
    public string $searchOrder      = '';
    public array  $orderResults     = [];
    public int    $selectedOrderId  = 0;
    public string $selectedOrderNum = '';
    public string $reason           = '';
    public string $pickupAddress    = '';
    public string $pickupDate       = '';
    public string $createNotes      = '';

    // ── Detail / Update Modal ─────────────────────────────────────────
    public bool         $showDetailModal = false;
    public ?OrderReturn $selectedReturn  = null;
    public string       $updateStatus    = '';
    public string       $updateCondition = '';
    public string       $updateNotes     = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ── Order Search ──────────────────────────────────────────────────

    public function updatedSearchOrder(): void
    {
        if (strlen($this->searchOrder) < 2) {
            $this->orderResults = [];
            return;
        }

        $this->orderResults = Order::where('order_number', 'like', "%{$this->searchOrder}%")
            ->orWhere(function ($q) {
                $q->whereRaw("JSON_EXTRACT(shipping_address, '$.full_name') LIKE ?", ["%{$this->searchOrder}%"]);
            })
            ->whereDoesntHave('orderReturn', fn ($q) => $q->whereNotIn('status', ['closed', 'resold', 'sent_back_dubai']))
            ->limit(6)
            ->get(['id', 'order_number', 'shipping_address', 'total', 'status'])
            ->map(fn ($o) => [
                'id'     => $o->id,
                'number' => $o->order_number,
                'name'   => ($o->shipping_address['full_name'] ?? ''),
                'total'  => $o->total,
                'status' => $o->status,
            ])
            ->toArray();
    }

    public function selectOrder(int $id, string $number): void
    {
        $this->selectedOrderId  = $id;
        $this->selectedOrderNum = $number;
        $this->searchOrder      = $number;
        $this->orderResults     = [];
    }

    // ── Create Return ─────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->reset([
            'selectedOrderId', 'selectedOrderNum', 'searchOrder',
            'orderResults', 'reason', 'pickupAddress', 'pickupDate', 'createNotes',
        ]);
        $this->showCreateModal = true;
    }

    public function createReturn(): void
    {
        $this->validate([
            'selectedOrderId' => ['required', 'integer', 'min:1'],
            'reason'          => ['required', 'string', 'max:1000'],
        ], [
            'selectedOrderId.min' => 'Please select an order.',
        ]);

        OrderReturn::create([
            'order_id'       => $this->selectedOrderId,
            'status'         => 'requested',
            'reason'         => $this->reason,
            'pickup_address' => $this->pickupAddress ?: null,
            'pickup_date'    => $this->pickupDate    ?: null,
            'notes'          => $this->createNotes   ?: null,
            'created_by'     => auth()->id(),
        ]);

        // Flag the order as having a pending return
        Order::findOrFail($this->selectedOrderId)->update(['refund_option' => 'refund']);

        $this->showCreateModal = false;
        session()->flash('success', 'Return request created successfully.');
    }

    // ── View / Update Return ──────────────────────────────────────────

    public function openDetail(int $id): void
    {
        $this->selectedReturn  = OrderReturn::with(['order.items.product', 'createdBy'])->findOrFail($id);
        $this->updateStatus    = $this->selectedReturn->status;
        $this->updateCondition = $this->selectedReturn->condition ?? '';
        $this->updateNotes     = '';
        $this->showDetailModal = true;
    }

    public function updateReturn(): void
    {
        $this->validate([
            'updateStatus' => ['required', 'in:requested,pickup_arranged,received,resold,sent_back_dubai,closed'],
        ]);

        $data = ['status' => $this->updateStatus];

        if ($this->updateCondition) {
            $data['condition'] = $this->updateCondition;
        }

        if ($this->updateNotes) {
            // Append notes rather than replace so history is preserved
            $existing        = $this->selectedReturn->notes ? $this->selectedReturn->notes . "\n\n" : '';
            $data['notes']   = $existing . '[' . now()->format('d M Y H:i') . '] ' . $this->updateNotes;
        }

        // Stamp timestamps on first transition to each milestone
        if ($this->updateStatus === 'received' && ! $this->selectedReturn->received_at) {
            $data['received_at'] = now();
        }

        if (
            in_array($this->updateStatus, ['resold', 'sent_back_dubai', 'closed'])
            && ! $this->selectedReturn->resolved_at
        ) {
            $data['resolved_at'] = now();
        }

        // If resold — restore product stock for every item on the order
        if ($this->updateStatus === 'resold' && $this->selectedReturn->status !== 'resold') {
            foreach ($this->selectedReturn->order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        $this->selectedReturn->update($data);
        $this->showDetailModal = false;
        session()->flash('success', 'Return updated successfully.');
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $returns = OrderReturn::with(['order', 'createdBy'])
            ->when($this->search, fn ($q) =>
                $q->whereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$this->search}%"))
            )
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);

        $stats = [
            'requested'       => OrderReturn::where('status', 'requested')->count(),
            'pickup_arranged' => OrderReturn::where('status', 'pickup_arranged')->count(),
            'received'        => OrderReturn::where('status', 'received')->count(),
            'resolved'        => OrderReturn::whereIn('status', ['resold', 'sent_back_dubai', 'closed'])->count(),
        ];

        $layout = auth()->user()?->isAdmin() ? 'layouts.admin' : 'layouts.staff';
        return view('livewire.admin.returns', compact('returns', 'stats'))->layout($layout);
    }
}
