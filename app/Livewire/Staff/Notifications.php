<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\User;
use App\Services\WhatsappService;

#[Title('Customer Notifications')]
#[Layout('layouts.staff')]
class Notifications extends Component
{
    use WithPagination;

    public string $tab       = 'completed';
    public string $search    = '';
    public string $customMsg = '';

    // Selection
    public array $selected   = [];
    public bool  $selectAll  = false;

    // Send results
    public array  $sendResults  = [];
    public bool   $showResults  = false;
    public bool   $sending      = false;

    // Preview modal (single)
    public bool   $showPreview  = false;
    public int    $previewOrder = 0;
    public string $previewMsg   = '';
    public string $previewPhone = '';
    public string $previewName  = '';

    public function updatingTab(): void
    {
        $this->selected   = [];
        $this->selectAll  = false;
        $this->sendResults = [];
        $this->showResults = false;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selected  = [];
        $this->selectAll = false;
    }

    // ── Select All on current page ────────────────────────────────────────

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->getPageOrderIds();
        } else {
            $this->selected = [];
        }
    }

    private function getPageOrderIds(): array
    {
        return $this->buildQuery()->paginate(20)->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    // ── Templates ─────────────────────────────────────────────────────────

    private function buildMessage(Order $order, string $type): string
    {
        $name    = $order->shipping_address['full_name'] ?? 'Customer';
        $num     = $order->order_number;
        $total   = number_format($order->total);
        $balance = number_format($order->balance_amount ?? 0);
        $site    = \App\Models\Setting::get('site_name', 'Meharahouse');

        return match($type) {
            'completed' =>
                "Hi {$name} 👋\n\n"
              . "✅ Your order *{$num}* has been completed successfully!\n\n"
              . "Thank you for shopping with {$site}. Your items are on the way! 🛍️\n\n"
              . "Total: Rs. {$total}\n\n"
              . "If you have any questions, feel free to reach out. We appreciate your trust! ❤️\n\n"
              . "— {$site} Team",

            'due' =>
                "Hi {$name} 👋\n\n"
              . "💳 Friendly reminder for order *{$num}*\n\n"
              . "Your outstanding balance is *Rs. {$balance}*.\n\n"
              . "Please complete your payment so we can process your order smoothly. 🙏\n\n"
              . "Pay via bank transfer or contact us to arrange payment.\n\n"
              . "— {$site} Team",

            'thankyou' =>
                "Hi {$name} ✨\n\n"
              . "Thank you so much for your order *{$num}*! 🙏\n\n"
              . "We truly appreciate your support. Your order has been received and is being processed with care.\n\n"
              . "We hope you love your new purchase! If you need anything, we're always here to help. 💚\n\n"
              . "— {$site} Team",

            'review' =>
                "Hi {$name} 😊\n\n"
              . "We hope you're enjoying your recent purchase from order *{$num}*!\n\n"
              . "⭐ We'd love to hear your feedback. Could you take a moment to leave us a review?\n\n"
              . "👉 " . url('/reviews') . "\n\n"
              . "Your opinion helps us improve and serve you better. Thank you! 💕\n\n"
              . "— {$site} Team",

            'custom' => $this->customMsg,

            default => '',
        };
    }

    // ── Preview single ─────────────────────────────────────────────────────

    public function previewMessage(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $this->previewOrder = $orderId;
        $this->previewPhone = $order->shipping_address['phone'] ?? '';
        $this->previewName  = $order->shipping_address['full_name'] ?? 'Customer';
        $this->previewMsg   = $this->buildMessage($order, $this->tab);
        $this->showPreview  = true;
    }

    // ── Send single via Twilio ─────────────────────────────────────────────

    public function sendSingle(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $phone = $order->shipping_address['phone'] ?? '';

        if (!$phone) {
            session()->flash('error', 'No phone number for order ' . $order->order_number);
            return;
        }

        $msg    = $this->buildMessage($order, $this->tab);
        $result = WhatsappService::send($phone, $msg);

        $this->sendResults = [[
            'order'   => $order->order_number,
            'name'    => $order->shipping_address['full_name'] ?? '—',
            'phone'   => $phone,
            'success' => $result['success'],
            'message' => $result['message'],
        ]];
        $this->showResults  = true;
        $this->showPreview  = false;
    }

    // ── Send bulk via Twilio ───────────────────────────────────────────────

    public function sendBulk(): void
    {
        if (empty($this->selected)) {
            session()->flash('error', 'No customers selected.');
            return;
        }

        $orders  = Order::whereIn('id', $this->selected)->get();
        $results = [];

        foreach ($orders as $order) {
            $phone = $order->shipping_address['phone'] ?? '';

            if (!$phone) {
                $results[] = [
                    'order'   => $order->order_number,
                    'name'    => $order->shipping_address['full_name'] ?? '—',
                    'phone'   => '—',
                    'success' => false,
                    'message' => 'No phone number.',
                ];
                continue;
            }

            $msg    = $this->buildMessage($order, $this->tab);
            $result = WhatsappService::send($phone, $msg);

            $results[] = [
                'order'   => $order->order_number,
                'name'    => $order->shipping_address['full_name'] ?? '—',
                'phone'   => $phone,
                'success' => $result['success'],
                'message' => $result['message'],
            ];
        }

        $this->sendResults = $results;
        $this->showResults = true;
        $this->selected    = [];
        $this->selectAll   = false;
    }

    // ── Render ─────────────────────────────────────────────────────────────

    private function buildQuery()
    {
        return Order::with('user')
            ->when($this->search, fn($q) =>
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.full_name')) LIKE ?", ["%{$this->search}%"])
            )
            ->when($this->tab === 'completed', fn($q) => $q->whereIn('status', ['completed', 'delivered']))
            ->when($this->tab === 'due',       fn($q) => $q->where(fn($q2) =>
                $q2->where('payment_status', 'partial')
                   ->orWhere(fn($q3) => $q3->where('payment_status', 'pending')->whereNotIn('status', ['cancelled']))
            ))
            ->when($this->tab === 'thankyou',  fn($q) => $q->whereIn('status', ['confirmed', 'processing', 'sourcing']))
            ->when($this->tab === 'review',    fn($q) => $q->whereIn('status', ['delivered', 'completed']))
            ->latest();
    }

    public function render()
    {
        $orders = $this->buildQuery()->paginate(20);

        return view('livewire.staff.notifications', compact('orders'));
    }
}
