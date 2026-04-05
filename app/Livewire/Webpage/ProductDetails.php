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
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        if ($this->product->stock <= 0) {
            session()->flash('error', 'This product is out of stock.');
            return;
        }

        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->first();

            if ($cart) {
                $newQty = $cart->quantity + $this->quantity;
                $cart->update(['quantity' => min($newQty, $this->product->stock)]);
            } else {
                Cart::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $this->product->id,
                    'quantity'   => min($this->quantity, $this->product->stock),
                ]);
            }
        } else {
            // Guest cart via session
            $sessionCart = session()->get('cart', []);
            $productId   = $this->product->id;

            if (isset($sessionCart[$productId])) {
                $sessionCart[$productId]['quantity'] = min(
                    $sessionCart[$productId]['quantity'] + $this->quantity,
                    $this->product->stock
                );
            } else {
                $sessionCart[$productId] = ['quantity' => $this->quantity];
            }
            session()->put('cart', $sessionCart);
        }

        $this->dispatch('cart-updated');
        session()->flash('success', 'Added to cart!');
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
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
            return;
        }

        $this->validate([
            'reviewRating'  => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:1000',
        ]);

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $this->product->id],
            ['rating' => $this->reviewRating, 'comment' => $this->reviewComment, 'is_approved' => false]
        );

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
