<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class Checkout extends Component
{
    public int $step = 1; // 1=address, 2=payment, 3=review, 4=success

    // Step 1 — Shipping Address
    public string $fullName = '';
    public string $email = '';
    public string $phone = '';
    public string $addressLine = '';
    public string $city = '';
    public string $region = '';
    public string $postalCode = '';
    public string $notes = '';

    // Step 2 — Payment
    public string $paymentMethod = 'cash_on_delivery';

    // Applied coupon
    public string $couponCode = '';
    public float $discountAmount = 0;
    public ?Coupon $appliedCoupon = null;

    // Success
    public ?string $orderNumber = null;

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
            return;
        }

        $user = auth()->user();
        $this->fullName = $user->name;
        $this->email    = $user->email;
        $this->phone    = $user->phone ?? '';
    }

    protected function stepOneRules(): array
    {
        return [
            'fullName'    => 'required|string|max:255',
            'email'       => 'required|email',
            'phone'       => 'required|string|max:20',
            'addressLine' => 'required|string|max:500',
            'city'        => 'required|string|max:100',
            'region'      => 'required|string|max:100',
        ];
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate($this->stepOneRules());
        }
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function getCartItemsProperty()
    {
        return CartModel::where('user_id', auth()->id())
            ->with('product')
            ->get();
    }

    public function getSubtotal(): float
    {
        return $this->cartItems->sum(fn($i) => $i->product->effectivePrice() * $i->quantity);
    }

    public function placeOrder(): void
    {
        $this->validate($this->stepOneRules());

        if ($this->cartItems->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        $subtotal     = $this->getSubtotal();
        $shipping     = $subtotal >= 500 ? 0 : 50;
        $tax          = round($subtotal * 0.15, 2);
        $total        = round($subtotal + $shipping + $tax - $this->discountAmount, 2);
        $orderNumber  = Order::generateOrderNumber();

        DB::transaction(function () use ($subtotal, $shipping, $tax, $total, $orderNumber) {
            $order = Order::create([
                'user_id'          => auth()->id(),
                'order_number'     => $orderNumber,
                'status'           => 'pending',
                'subtotal'         => $subtotal,
                'tax'              => $tax,
                'shipping_cost'    => $shipping,
                'discount'         => $this->discountAmount,
                'total'            => $total,
                'shipping_address' => [
                    'full_name'   => $this->fullName,
                    'email'       => $this->email,
                    'phone'       => $this->phone,
                    'address'     => $this->addressLine,
                    'city'        => $this->city,
                    'region'      => $this->region,
                    'postal_code' => $this->postalCode,
                ],
                'payment_method'   => $this->paymentMethod,
                'payment_status'   => 'pending',
                'coupon_code'      => $this->appliedCoupon?->code,
                'notes'            => $this->notes,
            ]);

            foreach ($this->cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->effectivePrice(),
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->product->effectivePrice() * $item->quantity,
                ]);

                // Decrement stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Increment coupon usage
            if ($this->appliedCoupon) {
                $this->appliedCoupon->increment('used_count');
            }

            // Clear cart
            CartModel::where('user_id', auth()->id())->delete();
        });

        $this->orderNumber = $orderNumber;
        $this->step        = 4;
    }

    public function render()
    {
        $subtotal = auth()->check() ? $this->getSubtotal() : 0;
        $shipping = $subtotal >= 500 ? 0 : 50;
        $tax      = round($subtotal * 0.15, 2);
        $total    = round($subtotal + $shipping + $tax - $this->discountAmount, 2);

        return view('livewire.webpage.checkout', compact('subtotal', 'shipping', 'tax', 'total'))
            ->layout('layouts.webpage')
            ->title('Checkout — Meharahouse');
    }
}
