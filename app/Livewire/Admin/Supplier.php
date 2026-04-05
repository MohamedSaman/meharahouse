<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Supplier as SupplierModel;
use Illuminate\Validation\Rule;

#[Title('Suppliers')]
#[Layout('layouts.admin')]
class Supplier extends Component
{
    use WithPagination;

    // List filters
    public string $search       = '';
    public string $filterActive = '';

    // Modal state
    public bool $showModal = false;
    public bool $editMode  = false;
    public ?int $editingId = null;

    // Form fields
    public string $name          = '';
    public string $contactPerson = '';
    public string $email         = '';
    public string $phone         = '';
    public string $whatsapp      = '';
    public string $address       = '';
    public string $city          = '';
    public string $country       = 'Ethiopia';
    public string $website       = '';
    public string $notes         = '';
    public bool   $isActive      = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterActive(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editMode  = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $supplier = SupplierModel::findOrFail($id);

        $this->editMode       = true;
        $this->editingId      = $id;
        $this->name           = $supplier->name;
        $this->contactPerson  = $supplier->contact_person ?? '';
        $this->email          = $supplier->email ?? '';
        $this->phone          = $supplier->phone;
        $this->whatsapp       = $supplier->whatsapp ?? '';
        $this->address        = $supplier->address ?? '';
        $this->city           = $supplier->city ?? '';
        $this->country        = $supplier->country;
        $this->website        = $supplier->website ?? '';
        $this->notes          = $supplier->notes ?? '';
        $this->isActive       = $supplier->is_active;
        $this->showModal      = true;
    }

    public function save(): void
    {
        $rules = [
            'name'          => ['required', 'string', 'max:255'],
            'contactPerson' => ['nullable', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255'],
            'phone'         => ['required', 'string', 'max:50'],
            'whatsapp'      => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'website'       => ['nullable', 'url', 'max:255'],
            'notes'         => ['nullable', 'string'],
            'isActive'      => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'name'           => $this->name,
            'contact_person' => $this->contactPerson ?: null,
            'email'          => $this->email ?: null,
            'phone'          => $this->phone,
            'whatsapp'       => $this->whatsapp ?: null,
            'address'        => $this->address ?: null,
            'city'           => $this->city ?: null,
            'country'        => $this->country,
            'website'        => $this->website ?: null,
            'notes'          => $this->notes ?: null,
            'is_active'      => $this->isActive,
        ];

        if ($this->editMode && $this->editingId) {
            SupplierModel::findOrFail($this->editingId)->update($data);
            session()->flash('success', "Supplier '{$this->name}' updated successfully.");
        } else {
            SupplierModel::create($data);
            session()->flash('success', "Supplier '{$this->name}' added successfully.");
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $supplier = SupplierModel::findOrFail($id);

        // Prevent deletion if there are purchase orders
        if ($supplier->purchaseOrders()->exists()) {
            session()->flash('error', "Cannot delete '{$supplier->name}' — it has existing purchase orders. Deactivate it instead.");
            return;
        }

        $name = $supplier->name;
        $supplier->delete();
        session()->flash('success', "Supplier '{$name}' deleted.");
    }

    public function toggleActive(int $id): void
    {
        $supplier = SupplierModel::findOrFail($id);
        $supplier->update(['is_active' => ! $supplier->is_active]);
    }

    private function resetForm(): void
    {
        $this->name          = '';
        $this->contactPerson = '';
        $this->email         = '';
        $this->phone         = '';
        $this->whatsapp      = '';
        $this->address       = '';
        $this->city          = '';
        $this->country       = 'Ethiopia';
        $this->website       = '';
        $this->notes         = '';
        $this->isActive      = true;
        $this->resetValidation();
    }

    public function render()
    {
        $suppliers = SupplierModel::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('contact_person', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
            )
            ->when($this->filterActive !== '', fn($q) =>
                $q->where('is_active', (bool) $this->filterActive)
            )
            ->withCount('purchaseOrders')
            ->latest()
            ->paginate(10);

        $stats = [
            'total'       => SupplierModel::count(),
            'active'      => SupplierModel::where('is_active', true)->count(),
            'total_spent' => (float) \App\Models\PurchaseOrder::whereIn('status', ['received', 'partial'])->sum('total'),
        ];

        return view('livewire.admin.supplier', compact('suppliers', 'stats'));
    }
}
