<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class Orders extends Component
{
    use WithPagination;

    public ?Order $selectedOrder = null;
    public bool $showDetail = false;

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
        }
    }

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        $this->showDetail = true;
    }

    public function cancelOrder(int $id): void
    {
        $order = Order::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $order->update(['status' => 'cancelled']);

        // Restore stock
        foreach ($order->items as $item) {
            $item->product?->increment('stock', $item->quantity);
        }

        if ($this->showDetail) {
            $this->selectedOrder = $order->fresh(['items.product']);
        }

        session()->flash('success', 'Order cancelled successfully.');
    }

    public function render()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.webpage.orders', compact('orders'))
            ->layout('layouts.webpage')
            ->title('My Orders — Meharahouse');
    }
}
