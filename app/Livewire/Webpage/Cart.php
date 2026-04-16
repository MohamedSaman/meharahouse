<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;  
use Livewire\Attributes\Layout;

#[Title('Shopping Cart')]
#[Layout('layouts.webpage')]
class Cart extends Component
{
    public string $couponCode = '';
    public ?Coupon $appliedCoupon = null;
    public float $discountAmount = 0;
    public string $couponError = '';
    public string $couponSuccess = '';

    public function getCartItemsProperty()
    {
        if (auth()->check()) {
            return CartModel::where('user_id', auth()->id())
                ->with('product.category')
                ->get();
        }

        // Guest cart from session — supports composite keys (productId_size)
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) return collect();

        // Collect all product IDs from session cart entries
        $productIds = collect($sessionCart)->map(function ($item, $key) {
            return $item['product_id'] ?? (int) explode('_', (string) $key)[0];
        })->unique()->values()->all();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($sessionCart)->map(function ($item, $key) use ($products) {
            $productId = $item['product_id'] ?? (int) explode('_', (string) $key)[0];
            $product   = $products->get($productId);
            if (!$product) return null;
            return (object)[
                'id'       => $key, // use the composite key as the cart item ID
                'product'  => $product,
                'quantity' => $item['quantity'],
                'size'     => $item['size'] ?? null,
            ];
        })->filter();
    }

    public function updateQuantity(int|string $itemId, int $quantity): void
    {
        \Illuminate\Support\Facades\Log::info('updateQuantity called', ['itemId' => $itemId, 'quantity' => $quantity]);
        if ($quantity < 1) {
            $this->remove($itemId);
            return;
        }

        if (auth()->check()) {
            $cart = CartModel::where('user_id', auth()->id())->find($itemId);
            if ($cart) {
                // Pre-order model: no stock cap — customers can order even without stock
                $cart->update(['quantity' => $quantity]);
            }
        } else {
            $sessionCart = session()->get('cart', []);
            if (isset($sessionCart[$itemId])) {
                $sessionCart[$itemId]['quantity'] = $quantity;
                session()->put('cart', $sessionCart);
            }
        }

        $this->recalculateDiscount();
        $this->dispatch('cart-updated');
    }

    public function remove(int|string $itemId): void
    {
        if (auth()->check()) {
            CartModel::where('user_id', auth()->id())->find($itemId)?->delete();
        } else {
            $sessionCart = session()->get('cart', []);
            unset($sessionCart[$itemId]);
            session()->put('cart', $sessionCart);
        }

        $this->recalculateDiscount();
        $this->dispatch('cart-updated');
    }

    public function applyCoupon(): void
    {
        $this->couponError   = '';
        $this->couponSuccess = '';

        $coupon = Coupon::where('code', strtoupper(trim($this->couponCode)))->first();

        if (!$coupon || !$coupon->isValid()) {
            $this->couponError   = 'Invalid or expired coupon code.';
            $this->appliedCoupon = null;
            $this->discountAmount = 0;
            return;
        }

        $subtotal = $this->getSubtotal();
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount <= 0) {
            $this->couponError = 'Your order does not meet the minimum requirement for this coupon (Rs. ' . number_format($coupon->min_order, 2) . ').';
            return;
        }

        $this->appliedCoupon  = $coupon;
        $this->discountAmount  = $discount;
        $this->couponSuccess   = 'Coupon applied! You save Rs. ' . number_format($discount, 2) . '.';
    }

    public function removeCoupon(): void
    {
        $this->appliedCoupon  = null;
        $this->discountAmount  = 0;
        $this->couponCode     = '';
        $this->couponError    = '';
        $this->couponSuccess  = '';
    }

    public function getSubtotal(): float
    {
        return $this->cartItems->sum(fn($item) => $item->product->effectivePrice() * $item->quantity);
    }

    public function getTotal(): float
    {
        $subtotal = $this->getSubtotal();
        $deliveryEnabled = \App\Models\Setting::get('delivery_fee_enabled', '0') === '1';
        $shipping = $deliveryEnabled ? (float) \App\Models\Setting::get('delivery_fee_amount', '0') : 0;
        $tax      = $subtotal * ((float) \App\Models\Setting::get('tax_rate', '15') / 100);
        return $subtotal + $shipping + $tax - $this->discountAmount;
    }

    private function recalculateDiscount(): void
    {
        if ($this->appliedCoupon) {
            $this->discountAmount = $this->appliedCoupon->calculateDiscount($this->getSubtotal());
        }
    }

    public function render()
    {
        $subtotal = $this->getSubtotal();
        $deliveryEnabled = \App\Models\Setting::get('delivery_fee_enabled', '0') === '1';
        $shipping = $deliveryEnabled ? (float) \App\Models\Setting::get('delivery_fee_amount', '0') : 0;
        $taxRate  = (float) \App\Models\Setting::get('tax_rate', '15') / 100;
        $tax      = round($subtotal * $taxRate, 2);
        $total    = round($subtotal + $shipping + $tax - $this->discountAmount, 2);

        return view('livewire.webpage.cart', compact('subtotal', 'shipping', 'tax', 'total'));
    }
}
