{{-- resources/views/livewire/admin/product.blade.php --}}
<div class="space-y-5">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Products</h2>
            <p class="text-sm text-[#64748B]">{{ $products->total() }} products in your catalog</p>
        </div>
        <button wire:click="openCreate" class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Product
        </button>
    </div>

    {{-- Filters --}}
    <div class="card p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search products or SKU..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
        </div>
        <select wire:model.live="filterCategory" class="form-input text-sm py-2 w-auto">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="form-input text-sm py-2 w-auto">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="featured">Featured</option>
            <option value="low_stock">Low Stock</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-16">Image</th>
                        <th>
                            <button wire:click="sort('name')" class="flex items-center gap-1 hover:text-[#0F172A]">
                                Product Name
                                @if($sortBy === 'name')
                                <svg class="w-3 h-3 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>
                            <button wire:click="sort('price')" class="flex items-center gap-1 hover:text-[#0F172A]">
                                Price
                                @if($sortBy === 'price')
                                <svg class="w-3 h-3 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sort('stock')" class="flex items-center gap-1 hover:text-[#0F172A]">
                                Stock
                            </button>
                        </th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr wire:key="{{ $product->id }}">
                        <td>
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-[#F1F5F9] flex items-center justify-center">
                                <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'">
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="text-sm font-semibold text-[#0F172A] leading-tight">{{ Str::limit($product->name, 35) }}</p>
                                @if($product->is_featured)
                                <span class="badge badge-gold text-[10px]">Featured</span>
                                @endif
                            </div>
                        </td>
                        <td><span class="text-sm text-[#475569]">{{ $product->category->name }}</span></td>
                        <td><span class="font-mono text-xs text-[#64748B]">{{ $product->sku ?? '—' }}</span></td>
                        <td>
                            <div>
                                <span class="font-semibold text-sm text-[#0F172A]">ETB {{ number_format($product->effectivePrice(), 0) }}</span>
                                @if($product->isOnSale())
                                <br><span class="text-xs text-[#94A3B8] line-through">ETB {{ number_format($product->price, 0) }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-sm font-semibold {{ $product->stock === 0 ? 'text-red-500' : ($product->stock <= 5 ? 'text-orange-500' : 'text-green-600') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            <button wire:click="toggleActive({{ $product->id }})"
                                    class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }} cursor-pointer">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <button wire:click="openEdit({{ $product->id }})"
                                        class="p-1.5 rounded-lg text-[#475569] hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="toggleFeatured({{ $product->id }})"
                                        class="p-1.5 rounded-lg transition-colors {{ $product->is_featured ? 'text-[#F59E0B] bg-[#FFFBEB]' : 'text-[#94A3B8] hover:text-[#F59E0B] hover:bg-[#FFFBEB]' }}" title="Toggle Featured">
                                    <svg class="w-4 h-4" fill="{{ $product->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </button>
                                <button wire:click="delete({{ $product->id }})"
                                        wire:confirm="Are you sure you want to delete this product?"
                                        class="p-1.5 rounded-lg text-[#94A3B8] hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-[#94A3B8]">
                            No products found. <button wire:click="openCreate" class="text-[#F59E0B] font-semibold hover:underline">Add your first product.</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════ CREATE/EDIT MODAL ══════════════════════ --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-data @keydown.escape.window="$wire.set('showModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white z-10">
                <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">
                    {{ $editMode ? 'Edit Product' : 'Add New Product' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form wire:submit="save" class="p-6 space-y-4">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Product Name *</label>
                    <input wire:model="name" type="text" class="form-input @error('name') border-red-400 @enderror" placeholder="e.g. Samsung Galaxy A54">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Category + SKU row --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Category *</label>
                        <select wire:model="category_id" class="form-input @error('category_id') border-red-400 @enderror">
                            <option value="0">Select category...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">SKU</label>
                        <input wire:model="sku" type="text" class="form-input" placeholder="e.g. MH-ABC123">
                        @error('sku')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Price + Sale Price row --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Price (ETB) *</label>
                        <input wire:model="price" type="number" step="0.01" class="form-input @error('price') border-red-400 @enderror" placeholder="0.00">
                        @error('price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Sale Price (ETB)</label>
                        <input wire:model="sale_price" type="number" step="0.01" class="form-input" placeholder="Leave empty if no sale">
                        @error('sale_price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Stock --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Stock Quantity *</label>
                    <input wire:model="stock" type="number" class="form-input @error('stock') border-red-400 @enderror" placeholder="0">
                    @error('stock')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Description</label>
                    <textarea wire:model="description" rows="3" class="form-input resize-none" placeholder="Product description..."></textarea>
                </div>

                {{-- Images --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Product Images</label>
                    <input wire:model="newImages" type="file" multiple accept="image/*" class="block w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FFFBEB] file:text-[#D97706] hover:file:bg-[#FEF3C7]">
                    @error('newImages.*')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    @if(!empty($uploadedImages))
                    <div class="flex gap-2 mt-2 flex-wrap">
                        @foreach($uploadedImages as $img)
                        <img src="{{ asset('storage/' . $img) }}" class="w-14 h-14 rounded-lg object-cover border border-[#E2E8F0]">
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Toggles --}}
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="is_featured" type="checkbox" class="w-4 h-4 rounded border-[#CBD5E1] text-[#F59E0B] focus:ring-[#F59E0B]">
                        <span class="text-sm font-semibold text-[#374151]">Featured Product</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="is_active" type="checkbox" class="w-4 h-4 rounded border-[#CBD5E1] text-[#F59E0B] focus:ring-[#F59E0B]">
                        <span class="text-sm font-semibold text-[#374151]">Active (visible in store)</span>
                    </label>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary btn-sm">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="btn-primary btn-sm">
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove>{{ $editMode ? 'Update Product' : 'Create Product' }}</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
