<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Review;

#[Title('Review Management')]
#[Layout('layouts.admin')]
class ReviewManagement extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending'; // pending | approved | rejected | all
    public string $search = '';
    public string $sortBy = 'latest'; // latest | highest | lowest

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        Review::findOrFail($id)->update(['status' => 'approved', 'approved_at' => now()]);
        session()->flash('success', 'Review approved successfully.');
    }

    public function reject(int $id): void
    {
        Review::findOrFail($id)->update(['status' => 'rejected', 'approved_at' => null]);
        session()->flash('success', 'Review rejected.');
    }

    public function delete(int $id): void
    {
        Review::findOrFail($id)->delete();
        session()->flash('success', 'Review deleted.');
    }

    public function render()
    {
        $reviews = Review::with('product')
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $this->search . '%');
            }))
            ->when($this->sortBy === 'highest', fn ($q) => $q->orderByDesc('rating')->orderByDesc('created_at'))
            ->when($this->sortBy === 'lowest', fn ($q) => $q->orderBy('rating')->orderByDesc('created_at'))
            ->when($this->sortBy === 'latest' || ! $this->sortBy, fn ($q) => $q->latest())
            ->paginate(15);

        $counts = [
            'pending'  => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
            'all'      => Review::count(),
        ];

        return view('livewire.admin.review-management', compact('reviews', 'counts'));
    }
}
