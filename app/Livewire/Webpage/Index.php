<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class Index extends Component
{
    public function render()
    {
        $banners = Banner::active()->take(5)->get();

        $categories = Category::active()
            ->root()
            ->withCount(['products' => fn($q) => $q->active()])
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->inStock()
            ->with('category')
            ->take(8)
            ->latest()
            ->get();

        $newArrivals = Product::active()
            ->inStock()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.webpage.index', compact('banners', 'categories', 'featuredProducts', 'newArrivals'))
            ->layout('layouts.webpage')
            ->title('Meharahouse — Quality You Can Trust');
    }
}
