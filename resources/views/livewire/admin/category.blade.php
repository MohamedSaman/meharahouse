{{-- resources/views/livewire/admin/category.blade.php --}}
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
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Categories</h2>
            <p class="text-sm text-[#64748B]">{{ $categories->total() }} categories in your catalog</p>
        </div>
        <button wire:click="openCreate" class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    {{-- Search --}}
    <div class="card p-4">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search categories..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
        </div>
    </div>

    {{-- Categories Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-16">Image</th>
                        <th>Category Name</th>
                        <th>Parent</th>
                        <th>Products</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr wire:key="{{ $category->id }}">
                        <td>
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-[#F1F5F9] flex items-center justify-center">
                                @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'">
                                @else
                                <svg class="w-6 h-6 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="text-sm font-semibold text-[#0F172A]">{{ $category->name }}</p>
                                <p class="text-xs text-[#94A3B8] font-mono">{{ $category->slug }}</p>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm text-[#475569]">{{ $category->parent?->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-[#0F172A]">{{ $category->products_count }}</span>
                        </td>
                        <td>
                            <span class="text-sm text-[#475569]">{{ $category->sort_order }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <button wire:click="openEdit({{ $category->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="delete({{ $category->id }})"
                                        wire:confirm="Are you sure you want to delete this category?"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-[#94A3B8]">
                            No categories found.
                            <button wire:click="openCreate" class="text-[#F59E0B] font-semibold hover:underline">Add your first category.</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-data @keydown.escape.window="$wire.set('showModal', false)" @click.self="$wire.set('showModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white z-10">
                <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">
                    {{ $editMode ? 'Edit Category' : 'Add New Category' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Category Name *</label>
                    <input wire:model="name" type="text" class="form-input @error('name') border-red-400 @enderror" placeholder="e.g. Casual Abaya">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Description</label>
                    <textarea wire:model="description" rows="2" class="form-input resize-none" placeholder="Category description..."></textarea>
                </div>

                {{-- Parent Category --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Parent Category</label>
                    <select wire:model="parent_id" class="form-input">
                        <option value="">None (Root Category)</option>
                        @foreach($parentOptions as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort Order --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Sort Order</label>
                    <input wire:model="sort_order" type="number" class="form-input" min="0" placeholder="0">
                </div>

                {{-- Image --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Category Image</label>
                    <input wire:model="image" type="file" accept="image/*" class="block w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FFFBEB] file:text-[#D97706] hover:file:bg-[#FEF3C7]">
                    @error('image')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    @if($existingImage)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $existingImage) }}" class="w-16 h-16 rounded-lg object-cover border border-[#E2E8F0]">
                        <p class="text-xs text-[#94A3B8] mt-1">Current image (upload new to replace)</p>
                    </div>
                    @endif
                </div>

                {{-- Active Toggle --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_active" type="checkbox" class="w-4 h-4 rounded border-[#CBD5E1] text-[#F59E0B] focus:ring-[#F59E0B]">
                    <span class="text-sm font-semibold text-[#374151]">Active (visible in store)</span>
                </label>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary btn-sm">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="btn-primary btn-sm">
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove>{{ $editMode ? 'Update Category' : 'Create Category' }}</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
