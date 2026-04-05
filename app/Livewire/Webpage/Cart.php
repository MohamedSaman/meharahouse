<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;  
use Livewire\Attributes\Layout;

#[Title('Cart Cart')]
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

        // Guest cart from session
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) return collect();

        $productIds = array_keys($sessionCart);
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($sessionCart)->map(function ($item, $productId) use ($products) {
            $product = $products->get($productId);
            if (!$product) return null;
            return (object)[
                'id'       => $productId,
                'product'  => $product,
                'quantity' => $item['quantity'],
            ];
        })->filter();
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($itemId);
            return;
        }

        if (auth()->check()) {
            $cart = CartModel::where('user_id', auth()->id())->find($itemId);
            if ($cart) {
                $maxQty = $cart->product->stock;
                $cart->update(['quantity' => min($quantity, $maxQty)]);
            }
        } else {
            $sessionCart = session()->get('cart', []);
            if (isset($sessionCart[$itemId])) {
                $product = Product::find($itemId);
                $sessionCart[$itemId]['quantity'] = min($quantity, $product->stock ?? $quantity);
                session()->put('cart', $sessionCart);
            }
        }

        $this->recalculateDiscount();
        $this->dispatch('cart-updated');
    }

    public function remove(int $itemId): void
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
            $this->couponError = 'Your order does not meet the minimum requirement for this coupon (ETB ' . number_format($coupon->min_order, 2) . ').';
            return;
        }

        $this->appliedCoupon  = $coupon;
        $this->discountAmount  = $discount;
        $this->couponSuccess   = 'Coupon applied! You save ETB ' . number_format($discount, 2) . '.';
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
        $shipping = $subtotal >= 500 ? 0 : 50;
        $tax      = $subtotal * 0.15;
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
        $shipping = $subtotal >= 500 ? 0 : 50;
        $tax      = round($subtotal * 0.15, 2);
        $total    = round($subtotal + $shipping + $tax - $this->discountAmount, 2);

        return view('livewire.webpage.cart', compact('subtotal', 'shipping', 'tax', 'total'));
    }
}
