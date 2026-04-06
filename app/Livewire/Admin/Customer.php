<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Validation\Rule;

#[Title('Customers')]
#[Layout('layouts.admin')]
class Customer extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = 'customer';
    public string $dateFrom   = '';
    public string $dateTo     = '';

    // Detail modal
    public bool  $showDetail  = false;
    public ?User $selectedUser = null;

    // Edit modal
    public bool   $showEdit   = false;
    public int    $editId     = 0;
    public string $editName   = '';
    public string $editEmail  = '';
    public string $editPhone  = '';
    public string $editRole   = '';

    // Delete confirm
    public bool $showDelete   = false;
    public int  $deleteId     = 0;
    public string $deleteName = '';

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

    // ── Edit ──────────────────────────────────────────────────────────────

    public function editCustomer(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editId    = $id;
        $this->editName  = $user->name;
        $this->editEmail = $user->email;
        $this->editPhone = $user->phone ?? '';
        $this->editRole  = $user->role;
        $this->showEdit  = true;
        $this->resetErrorBag();
    }

    public function saveCustomer(): void
    {
        $this->validate([
            'editName'  => 'required|string|max:255',
            'editEmail' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editId)],
            'editPhone' => 'nullable|string|max:20',
            'editRole'  => 'required|in:admin,staff,customer',
        ]);

        User::findOrFail($this->editId)->update([
            'name'  => $this->editName,
            'email' => $this->editEmail,
            'phone' => $this->editPhone ?: null,
            'role'  => $this->editRole,
        ]);

        $this->showEdit = false;
        $this->resetPage();
        session()->flash('success', 'Customer updated successfully.');
    }

    public function closeEdit(): void
    {
        $this->showEdit = false;
        $this->resetErrorBag();
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $this->deleteId   = $id;
        $this->deleteName = $user->name;
        $this->showDelete = true;
    }

    public function deleteCustomer(): void
    {
        $user = User::findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->showDelete = false;
            return;
        }

        $user->delete();
        $this->showDelete = false;
        $this->resetPage();
        session()->flash('success', "Customer \"{$this->deleteName}\" deleted.");
    }

    public function closeDelete(): void
    {
        $this->showDelete = false;
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
