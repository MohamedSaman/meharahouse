<?php

// app/Livewire/Webpage/Reviews.php

namespace App\Livewire\Webpage;

use App\Models\Product;
use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Customer Reviews')]
#[Layout('layouts.webpage')]
class Reviews extends Component
{
    use WithPagination;

    // --- Form fields ---
    public string $name    = '';
    public string $email   = '';
    public int    $rating  = 5;
    public string $comment = '';
    public int    $selectedProductId = 0;

    // --- UI state ---
    public bool $showForm  = false;
    public bool $submitted = false;

    // --- Filter / sort ---
    public string $filterRating = '';
    public string $sortBy       = 'latest';

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    /**
     * Pre-fill name/email from the authenticated user so they don't have to
     * type them manually.
     */
    public function mount(): void
    {
        if (auth()->check()) {
            $this->name  = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    // -------------------------------------------------------------------------
    // Watchers — reset pagination whenever a filter or sort value changes
    // -------------------------------------------------------------------------

    public function updatedFilterRating(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /**
     * Validate and persist the guest/authenticated review.
     * Reviews are stored with is_approved = false and await admin moderation.
     * Because the Review model has no dedicated guest_name / guest_email columns
     * we prefix the comment with "[Name | email]" for guest submissions so
     * admins can identify the reviewer without a user account.
     */
    public function submitReview(): void
    {
        $this->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'required|email|max:255',
            'rating'            => 'required|integer|min:1|max:5',
            'comment'           => 'required|string|min:10|max:1000',
            'selectedProductId' => 'nullable|integer|exists:products,id',
        ]);

        // For guests: embed the submitter identity at the start of the comment
        // so it is visible in the admin review queue.
        $storedComment = $this->comment;
        if (! auth()->check()) {
            $storedComment = "[{$this->name} | {$this->email}] {$this->comment}";
        }

        Review::create([
            'user_id'     => auth()->id(), // null for guests — that is intentional
            'product_id'  => $this->selectedProductId ?: null,
            'rating'      => $this->rating,
            'comment'     => $storedComment,
            'is_approved' => false, // requires admin approval before going live
        ]);

        // Reset form fields
        $this->reset(['comment', 'selectedProductId']);
        $this->rating   = 5;
        $this->showForm = false;

        // If the user is a guest also clear name/email
        if (! auth()->check()) {
            $this->reset(['name', 'email']);
        }

        $this->submitted = true;
    }

    /**
     * Dismiss the success banner and allow the user to submit another review.
     */
    public function resetSubmitted(): void
    {
        $this->submitted = false;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        // Build the approved-reviews query with optional filtering and sorting
        $reviews = Review::where('is_approved', true)
            ->with(['user', 'product'])
            ->when($this->filterRating, fn ($q) => $q->where('rating', (int) $this->filterRating))
            ->when($this->sortBy === 'highest', fn ($q) => $q->orderByDesc('rating'))
            ->when($this->sortBy === 'lowest',  fn ($q) => $q->orderBy('rating'))
            ->when(
                $this->sortBy === 'latest' || ! $this->sortBy,
                fn ($q) => $q->latest()
            )
            ->paginate(12);

        // Aggregate statistics for the summary bar
        $approvedBase = Review::where('is_approved', true);

        $stats = [
            'total'   => (clone $approvedBase)->count(),
            'average' => (float) ((clone $approvedBase)->avg('rating') ?? 0),
            'counts'  => (clone $approvedBase)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        // Products list for the "which product are you reviewing?" dropdown
        $products = Product::active()->orderBy('name')->get(['id', 'name']);

        return view('livewire.webpage.reviews', [
            'reviews'  => $reviews,
            'stats'    => $stats,
            'products' => $products,
        ]);
    }
}
