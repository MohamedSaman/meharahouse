<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order as OrderModel;

#[Title('Orders')]
#[Layout('layouts.admin')]
class Order extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterPayment = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    // Detail modal
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

    public function updateStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) return;

        OrderModel::findOrFail($id)->update(['status' => $status]);

        // Refresh if detail modal is open
        if ($this->selectedOrder && $this->selectedOrder->id === $id) {
            $this->selectedOrder = OrderModel::with(['user', 'items.product'])->find($id);
        }

        session()->flash('success', 'Order status updated to ' . ucfirst($status) . '.');
    }

    public function updatePaymentStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($status, $allowed)) return;

        OrderModel::findOrFail($id)->update(['payment_status' => $status]);
        session()->flash('success', 'Payment status updated.');
    }

    public function render()
    {
        $orders = OrderModel::with('user')
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%"));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(20);

        $statusCounts = OrderModel::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('livewire.admin.order', compact('orders', 'statusCounts'));
    }
}
