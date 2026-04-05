<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order as OrderModel;

#[Title('Order Queue')]
#[Layout('layouts.staff')]
class Order extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'pending';
    public bool $showDetail = false;
    public ?OrderModel $selectedOrder = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = OrderModel::with(['user', 'items.product'])->findOrFail($id);
        $this->showDetail    = true;
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $order = OrderModel::findOrFail($id);
        $order->update(['status' => $newStatus]);

        if ($this->selectedOrder && $this->selectedOrder->id === $id) {
            $this->selectedOrder->refresh();
        }

        session()->flash('success', 'Order #' . $order->order_number . ' marked as ' . ucfirst($newStatus) . '.');
    }

    public function render()
    {
        $orders = OrderModel::with('user')
            ->when($this->search, fn($q) => $q->where('order_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'    => OrderModel::where('status', 'pending')->count(),
            'processing' => OrderModel::where('status', 'processing')->count(),
            'shipped'    => OrderModel::where('status', 'shipped')->count(),
            'delivered'  => OrderModel::where('status', 'delivered')->count(),
            'cancelled'  => OrderModel::where('status', 'cancelled')->count(),
        ];

        return view('livewire.staff.order', compact('orders', 'counts'));
    }
}
