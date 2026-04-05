<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;

#[Title('Customer Lookup')]
#[Layout('layouts.staff')]
class Customer extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDetail = false;
    public ?User $selectedUser = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewCustomer(int $id): void
    {
        $this->selectedUser = User::with(['orders.items' => fn($q) => $q->take(3)])->findOrFail($id);
        $this->showDetail   = true;
    }

    public function render()
    {
        $customers = User::withCount('orders')
            ->where('role', 'customer')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(20);

        return view('livewire.staff.customer', compact('customers'));
    }
}
