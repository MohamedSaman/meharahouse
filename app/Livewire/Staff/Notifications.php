<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\User;

#[Title('Customer Notifications')]
#[Layout('layouts.staff')]
class Notifications extends Component
{
    use WithPagination;

    public string $tab          = 'completed';   // completed | due | thankyou | review
    public string $search       = '';
    public string $customMsg    = '';

    // WhatsApp modal
    public bool   $showWaModal  = false;
    public string $waPhone      = '';
    public string $waMessage    = '';
    public string $waOrderNum   = '';

    // ── Templates ─────────────────────────────────────────────────────

    private function completedTemplate(Order $order): string
    {
        $name    = $order->shipping_address['full_name'] ?? 'Customer';
        $num     = $order->order_number;
        $total   = number_format($order->total);
        return "Hi {$name} 👋\n\n✅ Your order *{$num}* has been completed successfully!\n\nThank you for shopping with Meharahouse. Your items are on the way! 🛍️\n\nTotal: Rs. {$total}\n\nIf you have any questions, feel free to reach out. We appreciate your trust! ❤️\n\n— Meharahouse Team";
    }

    private function dueReminderTemplate(Order $order): string
    {
        $name    = $order->shipping_address['full_name'] ?? 'Customer';
        $num     = $order->order_number;
        $balance = number_format($order->balance_due ?? 0);
        return "Hi {$name} 👋\n\n💳 Friendly reminder for order *{$num}*\n\nYour outstanding balance is *Rs. {$balance}*.\n\nPlease complete your payment so we can process your order smoothly. 🙏\n\nPay via bank transfer or contact us to arrange payment.\n\n— Meharahouse Team";
    }

    private function thankYouTemplate(Order $order): string
    {
        $name = $order->shipping_address['full_name'] ?? 'Customer';
        $num  = $order->order_number;
        return "Hi {$name} ✨\n\nThank you so much for your order *{$num}*! 🙏\n\nWe truly appreciate your support. Your order has been received and is being processed with care.\n\nWe hope you love your new abaya! If you need anything, we're always here to help. 💚\n\n— Meharahouse Team";
    }

    private function reviewTemplate(Order $order): string
    {
        $name = $order->shipping_address['full_name'] ?? 'Customer';
        $num  = $order->order_number;
        $link = url('/reviews');
        return "Hi {$name} 😊\n\nWe hope you're enjoying your recent purchase from order *{$num}*!\n\n⭐ We'd love to hear your feedback. Could you take a moment to leave us a review?\n\n👉 {$link}\n\nYour opinion helps us improve and serve you better. Thank you so much! 💕\n\n— Meharahouse Team";
    }

    // ── Open WhatsApp Modal ────────────────────────────────────────────

    public function openWhatsApp(int $orderId, string $type): void
    {
        $order = Order::findOrFail($orderId);
        $phone = $order->shipping_address['phone'] ?? '';

        $this->waPhone    = $phone;
        $this->waOrderNum = $order->order_number;
        $this->waMessage  = match($type) {
            'completed' => $this->completedTemplate($order),
            'due'       => $this->dueReminderTemplate($order),
            'thankyou'  => $this->thankYouTemplate($order),
            'review'    => $this->reviewTemplate($order),
            default     => '',
        };
        $this->showWaModal = true;
    }

    public function openCustomWhatsApp(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $phone = $order->shipping_address['phone'] ?? '';

        $this->waPhone    = $phone;
        $this->waOrderNum = $order->order_number;
        $this->waMessage  = $this->customMsg;
        $this->showWaModal = true;
    }

    // ── Render ─────────────────────────────────────────────────────────

    public function render()
    {
        $query = Order::with('user')
            ->when($this->search, fn($q) =>
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.full_name')) LIKE ?", ["%{$this->search}%"])
            );

        // Filter by tab
        $orders = match($this->tab) {
            'completed' => (clone $query)->whereIn('status', ['completed', 'delivered'])->latest()->paginate(20),
            'due'       => (clone $query)->where('payment_status', 'partial')
                               ->orWhere(fn($q) => $q->where('payment_status', 'pending')->whereNotIn('status', ['cancelled']))
                               ->latest()->paginate(20),
            'thankyou'  => (clone $query)->whereIn('status', ['confirmed', 'processing', 'sourcing'])->latest()->paginate(20),
            'review'    => (clone $query)->whereIn('status', ['delivered', 'completed'])->latest()->paginate(20),
            default     => $query->latest()->paginate(20),
        };

        return view('livewire.staff.notifications', compact('orders'));
    }
}
