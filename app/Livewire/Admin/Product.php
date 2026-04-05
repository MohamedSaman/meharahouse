<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product as ProductModel;
use App\Models\Category;
use Illuminate\Support\Str;

#[Title('Products')]
#[Layout('layouts.admin')]
class Product extends Component
{
    use WithPagination, WithFileUploads;

    // List state
    public string $search = '';
    public string $filterCategory = '';
    public string $filterStatus = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    // Form state
    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $description = '';
    public string $sku = '';
    public int $category_id = 0;
    public string $price = '';
    public string $sale_price = '';
    public int $stock = 0;
    public bool $is_featured = false;
    public bool $is_active = true;
    public array $uploadedImages = [];
    public $newImages = [];

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku'         => 'nullable|string|max:100|unique:products,sku,' . ($this->editingId ?? 'NULL'),
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'sale_price'  => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'newImages.*' => 'nullable|image|max:2048',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editMode  = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $product = ProductModel::findOrFail($id);
        $this->editingId       = $product->id;
        $this->name            = $product->name;
        $this->description     = $product->description ?? '';
        $this->sku             = $product->sku ?? '';
        $this->category_id     = $product->category_id;
        $this->price           = (string) $product->price;
        $this->sale_price      = $product->sale_price ? (string) $product->sale_price : '';
        $this->stock           = $product->stock;
        $this->is_featured     = $product->is_featured;
        $this->is_active       = $product->is_active;
        $this->uploadedImages  = $product->images ?? [];
        $this->editMode        = true;
        $this->showModal       = true;
    }

    public function save(): void
    {
        $this->validate();

        // Handle image uploads
        $images = $this->uploadedImages;
        foreach ($this->newImages as $img) {
            $path     = $img->store('products', 'public');
            $images[] = $path;
        }

        $slug = Str::slug($this->name);
        if (!$this->editMode) {
            // Ensure unique slug
            $baseSlug = $slug;
            $counter  = 1;
            while (ProductModel::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
        }

        $data = [
            'name'        => $this->name,
            'description' => $this->description,
            'sku'         => $this->sku ?: null,
            'category_id' => $this->category_id,
            'price'       => $this->price,
            'sale_price'  => $this->sale_price ?: null,
            'stock'       => $this->stock,
            'is_featured' => $this->is_featured,
            'is_active'   => $this->is_active,
            'images'      => $images,
        ];

        if ($this->editMode && $this->editingId) {
            ProductModel::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            $data['slug'] = $slug;
            ProductModel::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        ProductModel::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function toggleFeatured(int $id): void
    {
        $product = ProductModel::findOrFail($id);
        $product->update(['is_featured' => !$product->is_featured]);
    }

    public function toggleActive(int $id): void
    {
        $product = ProductModel::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'name', 'description', 'sku', 'price',
            'sale_price', 'uploadedImages', 'newImages', 'editingId', 'editMode',
        ]);
        $this->category_id = 0;
        $this->stock       = 0;
        $this->is_featured = false;
        $this->is_active   = true;
        $this->resetValidation();
    }

    public function render()
    {
        $products = ProductModel::with('category')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%"))
            ->when($this->filterCategory, fn($q) => $q->where('category_id', $this->filterCategory))
            ->when($this->filterStatus === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->filterStatus === 'featured', fn($q) => $q->where('is_featured', true))
            ->when($this->filterStatus === 'low_stock', fn($q) => $q->where('stock', '<=', 5))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);

        $categories = Category::active()->orderBy('name')->get();

        return view('livewire.admin.product', compact('products', 'categories'));
    }
}
