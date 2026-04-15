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
    public $balanceProofFile             = null;
    public bool $balanceProofUploaded    = false;
    public ?int $uploadingBalanceOrderId = null;

    // Re-upload state for rejected payments
    public $reuploadProofFile            = null;
    public bool $reuploadProofUploaded   = false;
    public ?int $reuploadPaymentId       = null;  // the rejected OrderPayment ID being replaced
    public string $reuploadPaymentType   = '';

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

    /**
     * Open the re-upload form for a specific rejected payment record.
     */
    public function openReupload(int $paymentId): void
    {
        $this->reuploadPaymentId     = $paymentId;
        $this->reuploadProofFile     = null;
        $this->reuploadProofUploaded = false;

        $payment = \App\Models\OrderPayment::where('id', $paymentId)
            ->whereHas('order', fn($q) => $q->where('user_id', auth()->id()))
            ->where('status', 'rejected')
            ->firstOrFail();

        $this->reuploadPaymentType = $payment->type;
    }

    /**
     * Submit the new receipt for a previously rejected payment.
     * Creates a fresh pending payment record of the same type.
     */
    public function submitReupload(): void
    {
        $this->validate([
            'reuploadProofFile' => 'required|file|image|max:5120',
        ], [
            'reuploadProofFile.required' => 'Please select an image.',
            'reuploadProofFile.image'    => 'Only image files are allowed.',
            'reuploadProofFile.max'      => 'Image must be under 5MB.',
        ]);

        if (!$this->reuploadPaymentId) return;

        $rejectedPayment = \App\Models\OrderPayment::where('id', $this->reuploadPaymentId)
            ->whereHas('order', fn($q) => $q->where('user_id', auth()->id()))
            ->where('status', 'rejected')
            ->firstOrFail();

        $order = Order::with('payments')
            ->where('user_id', auth()->id())
            ->findOrFail($rejectedPayment->order_id);

        $path = $this->reuploadProofFile->store('payment-proofs', 'public');

        \App\Models\OrderPayment::create([
            'order_id'     => $order->id,
            'type'         => $rejectedPayment->type,
            'amount'       => $rejectedPayment->amount,
            'method'       => $rejectedPayment->method,
            'receipt_path' => $path,
            'status'       => 'pending',
        ]);

        $this->reuploadProofFile     = null;
        $this->reuploadProofUploaded = true;
        $this->reuploadPaymentId     = null;

        // Refresh selected order
        $this->selectedOrder = Order::with(['items.product', 'payments'])
            ->where('user_id', auth()->id())
            ->find($order->id);

        session()->flash('success', 'Receipt re-uploaded. Our team will review it shortly.');
    }

    public function cancelOrder(int $id): void
    {
        // BUG-04 fix: orders are created with status 'new', not 'pending'
        $order = Order::where('user_id', auth()->id())
            ->where('status', 'new')
            ->findOrFail($id);

        $order->update(['status' => 'cancelled']);

        // BUG-05 fix: In the pre-order model, stock is NOT deducted at checkout.
        // Stock is only deducted when admin confirms the order.
        // So we should NOT restore stock here — nothing was deducted.
        // Only restore stock if the order was already confirmed (stock was deducted).
        // Since we only allow cancel on 'new' orders, stock was never deducted.

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
