<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Review;
use App\Models\Product;

#[Title('Customer Reviews')]
#[Layout('layouts.webpage')]
class Reviews extends Component
{
    use WithPagination;

    // ── UI state ──────────────────────────────────────────────────
    public bool $showForm  = false;
    public bool $submitted = false;

    // ── Filters ───────────────────────────────────────────────────
    public string $filterRating = '';
    public string $sortBy       = 'latest';

    public function updatedFilterRating(): void { $this->resetPage(); }
    public function updatedSortBy(): void       { $this->resetPage(); }

    public function submitReview(
        string $customerName,
        string $customerEmail,
        int    $rating,
        string $title,
        string $description,
        int    $productId
    ): void {
        $validated = validator([
            'customerName'  => $customerName,
            'customerEmail' => $customerEmail,
            'rating'        => $rating,
            'title'         => $title,
            'description'   => $description,
            'productId'     => $productId,
        ], [
            'customerName'  => 'required|string|max:100',
            'customerEmail' => 'nullable|email|max:255',
            'rating'        => 'required|integer|min:1|max:5',
            'title'         => 'nullable|string|max:150',
            'description'   => 'required|string|min:10|max:1000',
            'productId'     => 'nullable|integer|min:0',
        ])->validate();

        Review::create([
            'customer_name'  => trim($customerName),
            'customer_email' => $customerEmail ?: null,
            'product_id'     => $productId ?: null,
            'rating'         => $rating,
            'title'          => $title ?: null,
            'description'    => trim($description),
            'status'         => 'pending',
        ]);

        $this->dispatch('review-submitted');
    }

    public function render()
    {
        $reviews = Review::approved()
            ->with('product')
            ->when($this->filterRating, fn($q) => $q->where('rating', (int) $this->filterRating))
            ->when($this->sortBy === 'highest', fn($q) => $q->orderByDesc('rating')->orderByDesc('created_at'))
            ->when($this->sortBy === 'lowest',  fn($q) => $q->orderBy('rating')->orderByDesc('created_at'))
            ->when($this->sortBy === 'latest' || !$this->sortBy, fn($q) => $q->latest())
            ->paginate(12);

        $total   = Review::approved()->count();
        $average = $total > 0 ? round((float) Review::approved()->avg('rating'), 1) : 0;
        $counts  = Review::approved()
                        ->selectRaw('rating, count(*) as cnt')
                        ->groupBy('rating')
                        ->pluck('cnt', 'rating')
                        ->toArray();

        $stats    = compact('total', 'average', 'counts');
        $products = Product::active()->orderBy('name')->get(['id', 'name']);

        return view('livewire.webpage.reviews', compact('reviews', 'stats', 'products'));
    }
}
