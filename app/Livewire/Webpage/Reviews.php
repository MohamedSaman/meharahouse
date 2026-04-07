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

    // ── Submit form ───────────────────────────────────────────────
    public string $customerName      = '';
    public string $customerEmail     = '';
    public string $title             = '';
    public int    $rating            = 5;
    public string $description       = '';
    public int    $selectedProductId = 0;

    // ── UI state ──────────────────────────────────────────────────
    public bool $showForm  = false;
    public bool $submitted = false;

    // ── Filters ───────────────────────────────────────────────────
    public string $filterRating = '';
    public string $sortBy       = 'latest';

    public function updatedFilterRating(): void { $this->resetPage(); }
    public function updatedSortBy(): void       { $this->resetPage(); }

    public function submitReview(): void
    {
        $this->validate([
            'customerName'      => 'required|string|max:100',
            'customerEmail'     => 'nullable|email|max:255',
            'rating'            => 'required|integer|min:1|max:5',
            'title'             => 'nullable|string|max:150',
            'description'       => 'required|string|min:10|max:1000',
            'selectedProductId' => 'nullable|integer|min:0',
        ]);

        Review::create([
            'customer_name'  => trim($this->customerName),
            'customer_email' => $this->customerEmail ?: null,
            'product_id'     => $this->selectedProductId ?: null,
            'rating'         => $this->rating,
            'title'          => $this->title ?: null,
            'description'    => trim($this->description),
            'status'         => 'pending',
        ]);

        $this->reset(['customerName', 'customerEmail', 'title', 'description', 'selectedProductId']);
        $this->rating    = 5;
        $this->submitted = true;

        // Dispatch browser event — Alpine listens to close modal and show toast
        $this->dispatch('review-submitted');
    }

    public function openForm(): void
    {
        $this->submitted = false;
        $this->showForm  = true;
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
