<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;

#[Title('Customers')]
#[Layout('layouts.admin')]
class Customer extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = 'customer';
    public string $dateFrom   = '';
    public string $dateTo     = '';
    public bool $showDetail = false;
    public ?User $selectedUser = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }

    public function clearDates(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    public function viewCustomer(int $id): void
    {
        $this->selectedUser = User::with(['orders' => fn($q) => $q->latest()->take(5)])->findOrFail($id);
        $this->showDetail   = true;
    }

    public function updateRole(int $id, string $role): void
    {
        $allowed = ['admin', 'staff', 'customer'];
        if (!in_array($role, $allowed)) return;

        User::findOrFail($id)->update(['role' => $role]);
        session()->flash('success', 'User role updated.');
    }

    public function render()
    {
        $users = User::withCount('orders')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.customer', compact('users'));
    }
}
