<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Order;

class Orders extends Component
{
    use WithPagination, WithFileUploads;

    public ?Order $selectedOrder = null;
    public bool   $showDetail    = false;

    // Balance payment upload state
    public $balanceProofFile          = null;
    public bool $balanceProofUploaded = false;
    public ?int $uploadingBalanceOrderId = null;

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
        }
    }

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = Order::with(['items.product', 'payments'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        $this->balanceProofUploaded    = false;
        $this->balanceProofFile        = null;
        $this->uploadingBalanceOrderId = $id; // always track selected order id
        $this->showDetail = true;
    }

    public function openBalanceUpload(int $orderId): void
    {
        $this->uploadingBalanceOrderId = $orderId;
        $this->balanceProofFile        = null;
        $this->balanceProofUploaded    = false;
    }

    public function uploadBalanceProof(): void
    {
        $this->validate([
            'balanceProofFile' => 'required|file|image|max:5120',
        ], [
            'balanceProofFile.required' => 'Please select an image.',
            'balanceProofFile.image'    => 'Only image files are allowed.',
            'balanceProofFile.max'      => 'Image must be under 5MB.',
        ]);

        $orderId = $this->uploadingBalanceOrderId ?? $this->selectedOrder?->id;
        if (!$orderId) return;

        $order = Order::with('payments')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        $path = $this->balanceProofFile->store('payment-proofs', 'public');

        \App\Models\OrderPayment::create([
            'order_id'     => $order->id,
            'type'         => 'balance',
            'amount'       => $order->balanceDue(),
            'method'       => 'bank_transfer',
            'receipt_path' => $path,
            'status'       => 'pending',
        ]);

        $this->balanceProofFile     = null;
        $this->balanceProofUploaded = true;

        // Refresh selected order so payment list updates immediately
        $this->selectedOrder = Order::with(['items.product', 'payments'])
            ->where('user_id', auth()->id())
            ->find($order->id);
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
