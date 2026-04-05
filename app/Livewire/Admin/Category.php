<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Category as CategoryModel;
use Illuminate\Support\Str;

#[Title('Categories')]
#[Layout('layouts.admin')]
class Category extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $description = '';
    public ?int $parent_id = null;
    public bool $is_active = true;
    public int $sort_order = 0;
    public $image = null;
    public ?string $existingImage = null;

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $category          = CategoryModel::findOrFail($id);
        $this->editingId   = $category->id;
        $this->name        = $category->name;
        $this->description = $category->description ?? '';
        $this->parent_id   = $category->parent_id;
        $this->is_active   = $category->is_active;
        $this->sort_order  = $category->sort_order;
        $this->existingImage = $category->image;
        $this->editMode    = true;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate();

        $slug = Str::slug($this->name);

        $imagePath = $this->existingImage;
        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public');
        }

        $data = [
            'name'        => $this->name,
            'description' => $this->description,
            'parent_id'   => $this->parent_id ?: null,
            'is_active'   => $this->is_active,
            'sort_order'  => $this->sort_order,
            'image'       => $imagePath,
        ];

        if ($this->editMode && $this->editingId) {
            CategoryModel::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Category updated.');
        } else {
            // Unique slug
            $baseSlug = $slug;
            $counter  = 1;
            while (CategoryModel::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
            CategoryModel::create($data);
            session()->flash('success', 'Category created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $category = CategoryModel::withCount('products')->findOrFail($id);
        if ($category->products_count > 0) {
            session()->flash('error', 'Cannot delete category with existing products.');
            return;
        }
        $category->delete();
        session()->flash('success', 'Category deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'description', 'parent_id', 'image', 'existingImage', 'editingId', 'editMode']);
        $this->is_active  = true;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        $categories = CategoryModel::withCount('products')
            ->with('parent')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $parentOptions = CategoryModel::whereNull('parent_id')
            ->active()
            ->orderBy('name')
            ->get();

        return view('livewire.admin.category', compact('categories', 'parentOptions'));
    }
}
