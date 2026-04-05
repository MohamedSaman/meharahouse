{{-- resources/views/livewire/admin/product.blade.php --}}
<div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 mb-5 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 mb-5">
        <div class="absolute -top-16 -right-12 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] tracking-[0.16em] uppercase font-semibold text-amber-300">Catalog Manager</p>
            <h2 class="font-[Poppins] font-bold text-2xl text-white">Products</h2>
            <p class="text-sm text-slate-300">{{ $products->total() }} products in your catalog</p>
        </div>
        <button wire:click.prevent="openCreate" wire:loading.attr="disabled" wire:target="openCreate" class="btn-primary btn-sm shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Product
        </button>
    </div>
    </div>

    {{-- Filters --}}
    <div class="card p-4 flex flex-col sm:flex-row gap-3 mb-5 shadow-sm hover:shadow-md transition-all duration-300">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
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
            <option value="low_stock">Low Stock (≤5)</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-slate-200">
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
                                @if($sortBy === 'stock')
                                <svg class="w-3 h-3 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr wire:key="product-{{ $product->id }}">
                        <td>
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-[#F1F5F9] flex items-center justify-center">
                                @if(!empty($product->images))
                                <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.parentElement.innerHTML='<svg class=\'w-6 h-6 text-[#CBD5E1]\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg>'">
                                @else
                                <svg class="w-6 h-6 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
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
                        <td><span class="text-sm text-[#475569]">{{ $product->category->name ?? '—' }}</span></td>
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
                                    class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }} cursor-pointer hover:opacity-80 transition-opacity">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                {{-- Edit --}}
                                <button wire:click="openEdit({{ $product->id }})"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                {{-- Star / Featured --}}
                                <button wire:click="toggleFeatured({{ $product->id }})"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm {{ $product->is_featured ? 'text-[#F59E0B] bg-[#FFFBEB]' : 'bg-slate-100 text-slate-500 hover:text-[#F59E0B] hover:bg-[#FFFBEB]' }}" title="Toggle Featured">
                                    <svg class="w-4 h-4" fill="{{ $product->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </button>
                                {{-- Delete --}}
                                <button wire:click="delete({{ $product->id }})"
                                        wire:confirm="Delete '{{ addslashes($product->name) }}'? This cannot be undone."
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-500 hover:text-red-600 hover:bg-red-100 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-16 text-[#94A3B8]">
                            <svg class="w-12 h-12 mx-auto mb-3 text-[#E2E8F0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="font-medium">No products found.</p>
                            <button wire:click="openCreate" class="mt-2 text-[#F59E0B] font-semibold hover:underline text-sm">Add your first product</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════ CREATE / EDIT MODAL ══════════════ --}}
    @if($showModal)
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-data @keydown.escape.window="$wire.set('showModal', false)" @click.self="$wire.set('showModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white z-10">
                <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">
                    {{ $editMode ? 'Edit Product' : 'Add New Product' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form wire:submit="save" class="p-6 space-y-5">

                {{-- Product Name --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Product Name <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text"
                           class="form-input @error('name') border-red-400 bg-red-50 @enderror"
                           placeholder="e.g. Classic Black Abaya">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Category + SKU --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Category <span class="text-red-500">*</span></label>
                        <select wire:model="category_id"
                                class="form-input @error('category_id') border-red-400 bg-red-50 @enderror">
                            <option value="0">— Select category —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">SKU <span class="text-xs font-normal text-[#94A3B8]">(optional)</span></label>
                        <input wire:model="sku" type="text"
                               class="form-input @error('sku') border-red-400 bg-red-50 @enderror"
                               placeholder="e.g. MH-ABY-001">
                        @error('sku')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Price + Sale Price --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Price (ETB) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[#94A3B8] font-semibold">ETB</span>
                            <input wire:model="price" type="number" step="0.01" min="0"
                                   class="form-input pl-12 @error('price') border-red-400 bg-red-50 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Sale Price (ETB) <span class="text-xs font-normal text-[#94A3B8]">(optional)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[#94A3B8] font-semibold">ETB</span>
                            <input wire:model="sale_price" type="number" step="0.01" min="0"
                                   class="form-input pl-12 @error('sale_price') border-red-400 bg-red-50 @enderror"
                                   placeholder="Leave empty for no discount">
                        </div>
                        @error('sale_price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Stock --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Stock Quantity <span class="text-red-500">*</span></label>
                    <input wire:model="stock" type="number" min="0"
                           class="form-input @error('stock') border-red-400 bg-red-50 @enderror"
                           placeholder="0">
                    @error('stock')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Description</label>
                    <textarea wire:model="description" rows="3"
                              class="form-input resize-none"
                              placeholder="Describe the product — fabric, style, size options..."></textarea>
                </div>

                {{-- Images --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Product Images</label>
                    <div class="border-2 border-dashed border-[#CBD5E1] rounded-xl p-4 text-center hover:border-[#F59E0B] transition-colors">
                        <input wire:model="newImages" type="file" multiple accept="image/*"
                               id="product-images"
                               class="block w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FFFBEB] file:text-[#D97706] hover:file:bg-[#FEF3C7] cursor-pointer">
                        <p class="text-xs text-[#94A3B8] mt-2">PNG, JPG, WEBP up to 2MB each. Multiple allowed.</p>
                    </div>

                    {{-- Upload progress --}}
                    <div wire:loading wire:target="newImages" class="mt-2 text-xs text-[#64748B] flex items-center gap-2">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Uploading...
                    </div>

                    @error('newImages.*')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

                    {{-- Existing uploaded images with remove option --}}
                    @if(!empty($uploadedImages))
                    <div class="flex gap-2 mt-3 flex-wrap">
                        @foreach($uploadedImages as $index => $img)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $img) }}"
                                 class="w-16 h-16 rounded-lg object-cover border border-[#E2E8F0]"
                                 onerror="this.src='{{ asset('images/meharahouse-logo.png') }}'">
                            <button type="button"
                                    wire:click="$set('uploadedImages', {{ json_encode(array_values(array_filter($uploadedImages, fn($k) => $k !== $index, ARRAY_FILTER_USE_KEY))) }})"
                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Toggles --}}
                <div class="flex flex-wrap items-center gap-6 pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <div class="relative">
                            <input wire:model="is_featured" type="checkbox" class="sr-only peer">
                            <div class="w-10 h-6 bg-[#E2E8F0] peer-checked:bg-[#F59E0B] rounded-full transition-colors"></div>
                            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-sm font-semibold text-[#374151]">Featured Product</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <div class="relative">
                            <input wire:model="is_active" type="checkbox" class="sr-only peer">
                            <div class="w-10 h-6 bg-[#E2E8F0] peer-checked:bg-green-500 rounded-full transition-colors"></div>
                            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-sm font-semibold text-[#374151]">Active <span class="font-normal text-[#94A3B8]">(visible in store)</span></span>
                    </label>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="btn-secondary btn-sm">
                        Cancel
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="btn-primary btn-sm min-w-[130px] justify-center">
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">
                            {{ $editMode ? 'Update Product' : 'Create Product' }}
                        </span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
