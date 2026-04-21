<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Wishlist as WishlistModel;

#[Title('My Wishlist')]
#[Layout('layouts.webpage')]
class Wishlist extends Component
{
    public function removeFromWishlist(int $wishlistId): void
    {
        WishlistModel::where('id', $wishlistId)
            ->where('user_id', auth()->id())
            ->delete();

        session()->flash('success', 'Removed from wishlist.');
    }

    public function render()
    {
        $wishlistItems = WishlistModel::where('user_id', auth()->id())
            ->with('product.category')
            ->latest()
            ->get();

        return view('livewire.webpage.wishlist', compact('wishlistItems'));
    }
}
