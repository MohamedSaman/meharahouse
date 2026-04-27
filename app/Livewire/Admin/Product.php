<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product as ProductModel;
use App\Models\Category;
use App\Models\OrderReturn;
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

    // ── Size & Color Attributes ───────────────────────────────────────
    public array  $editSizes   = [];
    public string $sizeInput   = '';
    public array  $editColors  = [];
    public string $colorInput  = '';
    public string $colorHex    = '#000000';

    // ── Product History ───────────────────────────────────────────────
    public bool   $showHistoryModal    = false;
    public ?int   $historyProductId    = null;
    public string $historyProductName  = '';
    public string $historyTab          = 'purchases';
    public array  $historyPurchases    = [];
    public array  $historySales        = [];
    public array  $historyReturns      = [];

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
        $this->editSizes       = $product->sizes ?? [];
        $this->editColors      = $product->colors ?? [];
        $this->editMode        = true;
        $this->showModal       = true;
    }

    // ── Product History ───────────────────────────────────────────────

    public function openHistory(int $id): void
    {
        $product = ProductModel::with([
            'purchaseOrderItems.purchaseOrder.supplier',
            'orderItems.order',
        ])->findOrFail($id);

        $this->historyProductId   = $id;
        $this->historyProductName = $product->name;
        $this->historyTab         = 'purchases';

        // Purchase history — all PO items for this product
        $this->historyPurchases = $product->purchaseOrderItems
            ->sortByDesc('created_at')
            ->map(fn($poi) => [
                'po_number'     => $poi->purchaseOrder->po_number,
                'supplier'      => $poi->purchaseOrder->supplier?->name ?? '—',
                'date'          => $poi->purchaseOrder->created_at->format('d M Y'),
                'qty_ordered'   => $poi->quantity_ordered,
                'qty_received'  => $poi->quantity_received,
                'unit_cost'     => $poi->unit_cost,
                'subtotal'      => $poi->subtotal,
                'status'        => $poi->purchaseOrder->status,
                'po_id'         => $poi->purchase_order_id,
            ])
            ->values()
            ->toArray();

        // Sales history — all order items for this product
        $this->historySales = $product->orderItems
            ->sortByDesc('created_at')
            ->map(fn($oi) => [
                'order_number' => $oi->order->order_number,
                'customer'     => $oi->order->shipping_address['full_name'] ?? '—',
                'date'         => $oi->order->created_at->format('d M Y'),
                'qty'          => $oi->quantity,
                'unit_price'   => $oi->price,
                'subtotal'     => $oi->quantity * $oi->price,
                'order_status' => $oi->order->status,
                'order_id'     => $oi->order_id,
            ])
            ->values()
            ->toArray();

        // Returns — order returns where the order contains this product
        $this->historyReturns = OrderReturn::whereHas('order.items', fn($q) => $q->where('product_id', $id))
            ->with('order')
            ->latest()
            ->get()
            ->map(fn($ret) => [
                'order_number' => $ret->order->order_number,
                'date'         => $ret->created_at->format('d M Y'),
                'reason'       => $ret->reason,
                'status'       => $ret->status,
                'condition'    => $ret->condition,
                'resolved_at'  => $ret->resolved_at?->format('d M Y'),
            ])
            ->toArray();

        $this->showHistoryModal = true;
    }

    // ── Save / Delete ─────────────────────────────────────────────────

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
            'sizes'       => !empty($this->editSizes) ? array_values($this->editSizes) : null,
            'colors'      => !empty($this->editColors) ? array_values($this->editColors) : null,
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

    public function addSize(): void
    {
        $val = strtoupper(trim($this->sizeInput));
        if ($val && !in_array($val, $this->editSizes)) {
            $this->editSizes[] = $val;
        }
        $this->sizeInput = '';
    }

    public function removeSize(int $index): void
    {
        array_splice($this->editSizes, $index, 1);
        $this->editSizes = array_values($this->editSizes);
    }

    public function addColor(): void
    {
        $name = trim($this->colorInput);
        if (!$name) return;
        foreach ($this->editColors as $c) {
            if (strtolower($c['name']) === strtolower($name)) return;
        }
        $this->editColors[] = ['name' => $name, 'hex' => $this->colorHex];
        $this->colorInput = '';
        $this->colorHex   = '#000000';
    }

    public function removeColor(int $index): void
    {
        array_splice($this->editColors, $index, 1);
        $this->editColors = array_values($this->editColors);
    }

    private function resetForm(): void
    {
        $this->reset([
            'name', 'description', 'sku', 'price',
            'sale_price', 'uploadedImages', 'newImages', 'editingId', 'editMode',
            'sizeInput', 'colorInput',
        ]);
        $this->category_id = 0;
        $this->stock       = 0;
        $this->is_featured = false;
        $this->is_active   = true;
        $this->editSizes   = [];
        $this->editColors  = [];
        $this->colorHex    = '#000000';
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
