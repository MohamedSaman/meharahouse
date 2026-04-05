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

    // Step 1 — Customer Details & Shipping Address
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
    public string $couponError = '';
    public string $couponSuccess = '';

    // Success
    public ?string $orderNumber = null;

    public function mount(): void
    {
        // Pre-fill details if user is logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->fullName = $user->name;
            $this->email    = $user->email;
            $this->phone    = $user->phone ?? '';
        }
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
        if (auth()->check()) {
            return CartModel::where('user_id', auth()->id())
                ->with('product')
                ->get();
        }

        // Guest cart from session
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) return collect();

        $productIds = array_keys($sessionCart);
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($sessionCart)->map(function ($item, $productId) use ($products) {
            $product = $products->get($productId);
            if (!$product) return null;
            return (object)[
                'id'         => $productId,
                'product_id' => $productId,
                'product'    => $product,
                'quantity'   => $item['quantity'],
            ];
        })->filter()->values();
    }

    public function getSubtotal(): float
    {
        return $this->cartItems->sum(fn($i) => $i->product->effectivePrice() * $i->quantity);
    }

    public function applyCoupon(): void
    {
        $this->couponError   = '';
        $this->couponSuccess = '';

        $coupon = Coupon::where('code', strtoupper(trim($this->couponCode)))->first();

        if (!$coupon || !$coupon->isValid()) {
            $this->couponError    = 'Invalid or expired coupon code.';
            $this->appliedCoupon  = null;
            $this->discountAmount = 0;
            return;
        }

        $subtotal = $this->getSubtotal();
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount <= 0) {
            $this->couponError = 'Your order does not meet the minimum requirement (ETB ' . number_format($coupon->min_order) . ').';
            return;
        }

        $this->appliedCoupon  = $coupon;
        $this->discountAmount  = $discount;
        $this->couponSuccess   = 'Coupon applied! You save ETB ' . number_format($discount) . '.';
    }

    public function removeCoupon(): void
    {
        $this->appliedCoupon  = null;
        $this->discountAmount  = 0;
        $this->couponCode     = '';
        $this->couponError    = '';
        $this->couponSuccess  = '';
    }

    public function placeOrder(): void
    {
        $this->validate($this->stepOneRules());

        if ($this->cartItems->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        $subtotal    = $this->getSubtotal();
        $shipping    = $subtotal >= 500 ? 0 : 50;
        $tax         = round($subtotal * 0.15, 2);
        $total       = round($subtotal + $shipping + $tax - $this->discountAmount, 2);
        $orderNumber = Order::generateOrderNumber();

        DB::transaction(function () use ($subtotal, $shipping, $tax, $total, $orderNumber) {
            $order = Order::create([
                'user_id'          => auth()->id(), // null for guests
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

                $item->product->decrement('stock', $item->quantity);
            }

            if ($this->appliedCoupon) {
                $this->appliedCoupon->increment('used_count');
            }

            // Clear cart — DB for logged-in users, session for guests
            if (auth()->check()) {
                CartModel::where('user_id', auth()->id())->delete();
            } else {
                session()->forget('cart');
            }
        });

        $this->orderNumber = $orderNumber;
        $this->step        = 4;
    }

    public function render()
    {
        $subtotal = $this->getSubtotal();
        $shipping = $subtotal >= 500 ? 0 : 50;
        $tax      = round($subtotal * 0.15, 2);
        $total    = round($subtotal + $shipping + $tax - $this->discountAmount, 2);

        return view('livewire.webpage.checkout', compact('subtotal', 'shipping', 'tax', 'total'))
            ->layout('layouts.webpage')
            ->title('Checkout — Meharahouse');
    }
}
