<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Review;

class ProductDetails extends Component
{
    public Product $product;
    public int $quantity = 1;
    public string $size = '';
    public string $color = '';
    public int $activeImage = 0;
    public string $reviewComment = '';
    public int $reviewRating = 5;
    public bool $showReviewForm = false;

    public function mount(string $slug): void
    {
        $this->product = Product::active()
            ->with(['category', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function incrementQty(): void
    {
        $this->quantity++;
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    private function validateSize(): void
    {
        $rules    = [];
        $messages = [];

        $hasSizes  = !empty($this->product->sizes);
        $hasColors = !empty($this->product->colors);

        if ($hasSizes) {
            $rules['size']    = ['required', 'string', 'in:' . implode(',', $this->product->sizes)];
            $messages['size.required'] = 'Please select a size before continuing.';
            $messages['size.in']       = 'Please select a valid size.';
        } else {
            $rules['size']    = ['nullable', 'string', 'max:20'];
        }

        if ($hasColors) {
            $colorNames = collect($this->product->colors)->pluck('name')->implode(',');
            $rules['color']    = ['required', 'string', 'in:' . $colorNames];
            $messages['color.required'] = 'Please select a color before continuing.';
            $messages['color.in']       = 'Please select a valid color.';
        }

        $this->validate($rules, $messages);
    }

    /** Called by Alpine when customer clicks a size chip. */
    public function selectSize(string $size): void
    {
        $this->size = $size;
    }

    /** Called by Alpine when customer clicks a color swatch. */
    public function selectColor(string $color): void
    {
        $this->color = $color;
    }

    private function putInCart(): void
    {
        $productId = $this->product->id;
        $qty       = max(1, $this->quantity);
        $size      = trim($this->size);
        $color     = trim($this->color);

        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->where('size', $size)
                ->where('color', $color ?: null)
                ->first();

            if ($cart) {
                $cart->update(['quantity' => $cart->quantity + $qty]);
            } else {
                Cart::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    'size'       => $size ?: null,
                    'color'      => $color ?: null,
                ]);
            }
        } else {
            $sessionCart = session()->get('cart', []);
            $cartKey     = $productId . '_' . $size . '_' . $color;

            if (isset($sessionCart[$cartKey])) {
                $sessionCart[$cartKey]['quantity'] += $qty;
            } else {
                $sessionCart[$cartKey] = [
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    'size'       => $size ?: null,
                    'color'      => $color ?: null,
                ];
            }
            session()->put('cart', $sessionCart);
        }

        $this->dispatch('cart-updated');
    }

    public function addToCart(): void
    {
        $this->validateSize();
        $this->putInCart();
        session()->flash('success', 'Added to cart!');
    }

    public function preOrderNow(): void
    {
        $this->validateSize();

        // Store buy-now item in session so checkout uses ONLY this product,
        // not the existing cart items. This prevents cart items from being
        // unknowingly added to the order (BUG WS-1.5).
        session()->put('buy_now', [
            'product_id' => $this->product->id,
            'quantity'   => max(1, $this->quantity),
            'size'       => trim($this->size) ?: null,
            'color'      => trim($this->color) ?: null,
        ]);

        $this->redirect(route('webpage.checkout'));
    }

    public function toggleWishlist(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
            return;
        }

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            session()->flash('info', 'Removed from wishlist.');
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $this->product->id,
            ]);
            session()->flash('success', 'Added to wishlist!');
        }
    }

    public function submitReview(): void
    {
        $this->validate([
            'reviewRating'  => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:1000',
        ]);

        $name = auth()->check() ? auth()->user()->name : 'Anonymous';

        Review::create([
            'customer_name' => $name,
            'customer_email'=> auth()->user()?->email,
            'product_id'    => $this->product->id,
            'rating'        => $this->reviewRating,
            'description'   => $this->reviewComment ?: '',
            'status'        => 'pending',
        ]);

        $this->reset('reviewComment', 'reviewRating', 'showReviewForm');
        session()->flash('success', 'Review submitted for approval.');
        $this->product->refresh();
    }

    public function isInWishlist(): bool
    {
        if (!auth()->check()) return false;
        return Wishlist::where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->exists();
    }

    public function render()
    {
        $relatedProducts = Product::active()
            ->inStock()
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->take(4)
            ->get();

        return view('livewire.webpage.product-details', compact('relatedProducts'))
            ->layout('layouts.webpage')
            ->title($this->product->name . ' — Meharahouse');
    }
}
