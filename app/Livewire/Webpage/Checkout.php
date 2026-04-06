<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Setting;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Checkout')]
#[Layout('layouts.webpage')]
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

    protected function stepOneMessages(): array
    {
        return [
            'phone.regex' => 'Phone must include country code and start with + (e.g. +94761265772)',
        ];
    }

    protected function stepOneRules(): array
    {
        return [
            'fullName'    => 'required|string|max:255',
            'email'       => 'required|email',
            'phone'       => ['required', 'string', 'max:20', 'regex:/^\+[1-9][0-9]{6,14}$/'],
            'addressLine' => 'required|string|max:500',
            'city'        => 'required|string|max:100',
            'region'      => 'required|string|max:100',
        ];
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate($this->stepOneRules(), $this->stepOneMessages());
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
            $this->couponError = 'Your order does not meet the minimum requirement (Rs. ' . number_format($coupon->min_order) . ').';
            return;
        }

        $this->appliedCoupon  = $coupon;
        $this->discountAmount  = $discount;
        $this->couponSuccess   = 'Coupon applied! You save Rs. ' . number_format($discount) . '.';
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
        $this->validate($this->stepOneRules(), $this->stepOneMessages());

        if ($this->cartItems->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        $subtotal    = $this->getSubtotal();
        $shipping    = Setting::get('delivery_fee_enabled', '0') === '1' ? (float) Setting::get('delivery_fee_amount', '0') : 0;
        $tax         = round($subtotal * 0.15, 2);
        $total       = round($subtotal + $shipping + $tax - $this->discountAmount, 2);
        $orderNumber = Order::generateOrderNumber();

        DB::transaction(function () use ($subtotal, $shipping, $tax, $total, $orderNumber) {
            $order = Order::create([
                'user_id'          => auth()->id(), // null for guests
                'order_number'     => $orderNumber,
                'status'           => 'new',
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

                // Stock is deducted when admin confirms the order — not at checkout (pre-order model)
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

        // Send order placed WhatsApp message
        try {
            $placedOrder = Order::with('items')->where('order_number', $orderNumber)->first();
            if ($placedOrder) WhatsappService::orderPlaced($placedOrder);
        } catch (\Throwable) {}

        // Redirect to payment gateway for online payments
        if ($this->paymentMethod === 'payhere') {
            $this->redirect(route('payment.payhere', $orderNumber));
            return;
        }
        if ($this->paymentMethod === 'paypal') {
            $this->redirect(route('payment.paypal', $orderNumber));
            return;
        }

        $this->orderNumber = $orderNumber;
        $this->step        = 4;
    }

    public function render()
    {
        $subtotal = $this->getSubtotal();
        $shipping = $subtotal >= 500 ? 0 : 50;
        $tax      = round($subtotal * 0.15, 2);
        $total    = round($subtotal + $shipping + $tax - $this->discountAmount, 2);

        // Build payment methods list from admin-enabled settings
        $allMethods = [
            'cash_on_delivery' => ['label' => 'Cash on Delivery',  'desc' => 'Pay when your order arrives at your door.',               'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z', 'setting' => 'payment_cod_enabled',       'default' => '1'],
            'bank_transfer'    => ['label' => 'Bank Transfer',     'desc' => 'Transfer to our bank account (CBE, Awash, Abyssinia).', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                          'setting' => 'payment_bank_enabled',      'default' => '0'],
            'telebirr'         => ['label' => 'Telebirr',          'desc' => 'Pay via Telebirr mobile wallet.',                       'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',                                       'setting' => 'payment_telebirr_enabled',  'default' => '0'],
            'cbebirr'          => ['label' => 'CBE Birr',          'desc' => 'Pay via CBE Birr mobile banking.',                     'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',                                       'setting' => 'payment_cbebirr_enabled',   'default' => '0'],
            'payhere'          => ['label' => 'PayHere',           'desc' => 'Secure online payment via PayHere gateway.',           'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                          'setting' => 'payment_payhere_enabled',   'default' => '0'],
            'paypal'           => ['label' => 'PayPal',            'desc' => 'Pay securely with your PayPal account.',              'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                          'setting' => 'payment_paypal_enabled',    'default' => '0'],
            'stripe'           => ['label' => 'Stripe (Card)',     'desc' => 'Pay with Visa, Mastercard, or any debit/credit card.','icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                          'setting' => 'payment_stripe_enabled',    'default' => '0'],
        ];

        $paymentMethods = array_filter($allMethods, fn($m) => Setting::get($m['setting'], $m['default']) === '1');

        // If current selection is no longer enabled, reset to first available
        if (!empty($paymentMethods) && !array_key_exists($this->paymentMethod, $paymentMethods)) {
            $this->paymentMethod = array_key_first($paymentMethods);
        }

        return view('livewire.webpage.checkout', compact('subtotal', 'shipping', 'tax', 'total', 'paymentMethods'));
    }
}
