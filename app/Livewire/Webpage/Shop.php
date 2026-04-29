<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;

class Shop extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categorySlug = '';
    public string $sortBy = 'latest';
    public string $priceMin = '';
    public string $priceMax = '';
    public string $view = 'grid'; // grid | list
    public bool $onSaleOnly = false;
    public bool $inStockOnly = false;

    protected $queryString = [
        'search'       => ['except' => ''],
        'categorySlug' => ['except' => '', 'as' => 'category'],
        'sortBy'       => ['except' => 'latest'],
        'priceMin'     => ['except' => ''],
        'priceMax'     => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategorySlug(): void
    {
        $this->resetPage();
    }

    public function setCategory(string $slug): void
    {
        $this->categorySlug = $slug;
        $this->resetPage();
    }

    public function toggleWishlist(int $productId): void
    {
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
            return;
        }

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            session()->flash('info', 'Removed from wishlist.');
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $productId,
            ]);
            session()->flash('success', 'Added to wishlist!');
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'categorySlug', 'priceMin', 'priceMax', 'onSaleOnly', 'inStockOnly']);
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::active()->with('category');

        if ($this->search) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"));
        }

        if ($this->categorySlug) {
            $query->whereHas('category', fn($q) => $q->where('slug', $this->categorySlug));
        }

        if ($this->priceMin !== '') {
            $query->where(fn($q) => $q->where('sale_price', '>=', $this->priceMin)
                ->orWhere(fn($q2) => $q2->whereNull('sale_price')->where('price', '>=', $this->priceMin)));
        }

        if ($this->priceMax !== '') {
            $query->where(fn($q) => $q->where('sale_price', '<=', $this->priceMax)
                ->orWhere(fn($q2) => $q2->whereNull('sale_price')->where('price', '<=', $this->priceMax)));
        }

        if ($this->onSaleOnly) {
            $query->whereNotNull('sale_price');
        }

        if ($this->inStockOnly) {
            $query->inStock();
        }

        match ($this->sortBy) {
            'price_asc'  => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'featured'   => $query->orderByDesc('is_featured'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12);
        $categories = Category::active()->root()->withCount('products')->orderBy('sort_order')->get();

        return view('livewire.webpage.shop', compact('products', 'categories'))
            ->layout('layouts.webpage')
            ->title('Shop — Meharahouse');
    }
}
