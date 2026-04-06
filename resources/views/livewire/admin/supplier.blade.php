{{-- resources/views/livewire/admin/supplier.blade.php --}}
<div class="space-y-6">

    {{-- ══════════════════════ PAGE HEADER ══════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-14 -right-10 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-blue-400/15 blur-3xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300">Purchasing</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-dark">Suppliers</h2>
                <p class="text-sm text-slate-300 mt-1">
                    Manage production companies &amp; wholesalers Meharahouse buys stock from
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full bg-white/10 text-white text-xs font-medium">
                        {{ $stats['total'] }} total
                    </span>
                </p>
            </div>
            <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-slate-900 hover:bg-amber-300 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-400/40 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Supplier
            </button>
        </div>
    </div>

    {{-- ══════════════════════ FLASH MESSAGES ══════════════════════ --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ══════════════════════ STAT CARDS ══════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Suppliers --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900 font-[Poppins]">{{ $stats['total'] }}</p>
                <p class="text-sm text-slate-500">Total Suppliers</p>
            </div>
        </div>
        {{-- Active --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900 font-[Poppins]">{{ $stats['active'] }}</p>
                <p class="text-sm text-slate-500">Active Suppliers</p>
            </div>
        </div>
        {{-- Total Spent --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900 font-[Poppins]">Rs. {{ number_format($stats['total_spent']) }}</p>
                <p class="text-sm text-slate-500">Total Spent (Received POs)</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ SEARCH & FILTER ══════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search by name, contact, phone, or city..."
                       class="form-input pl-9 w-full">
            </div>
            <select wire:model.live="filterActive" class="form-input w-full sm:w-44">
                <option value="">All Status</option>
                <option value="1">Active Only</option>
                <option value="0">Inactive Only</option>
            </select>
        </div>
    </div>

    {{-- ══════════════════════ SUPPLIERS TABLE ══════════════════════ --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Supplier</th>
                        <th class="text-left hidden md:table-cell">Contact</th>
                        <th class="text-left hidden lg:table-cell">Location</th>
                        <th class="text-center hidden xl:table-cell">POs</th>
                        <th class="text-right hidden xl:table-cell">Total Spent</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr wire:key="supplier-{{ $supplier->id }}" class="hover:bg-slate-50 transition-colors">
                        {{-- Name + Contact Person --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center shrink-0 shadow">
                                    <span class="text-amber-400 font-bold text-sm">{{ strtoupper(substr($supplier->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 text-sm">{{ $supplier->name }}</p>
                                    @if($supplier->contact_person)
                                    <p class="text-xs text-slate-500">{{ $supplier->contact_person }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Phone + WhatsApp --}}
                        <td class="py-3.5 px-4 hidden md:table-cell">
                            <a href="tel:{{ $supplier->phone }}" class="flex items-center gap-1.5 text-sm text-slate-700 hover:text-amber-600 transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $supplier->phone }}
                            </a>
                            @if($supplier->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supplier->whatsapp) }}"
                               target="_blank"
                               class="flex items-center gap-1.5 text-xs text-emerald-600 hover:text-emerald-700 mt-1 transition-colors">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.113.549 4.097 1.508 5.826L0 24l6.334-1.482A11.933 11.933 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.796 9.796 0 01-5.064-1.41l-.364-.216-3.76.879.936-3.66-.236-.376A9.787 9.787 0 012.182 12C2.182 6.569 6.569 2.182 12 2.182S21.818 6.569 21.818 12 17.431 21.818 12 21.818z"/>
                                </svg>
                                WhatsApp
                            </a>
                            @endif
                        </td>

                        {{-- Location --}}
                        <td class="py-3.5 px-4 hidden lg:table-cell">
                            <p class="text-sm text-slate-700">{{ $supplier->city ?? '—' }}</p>
                            <p class="text-xs text-slate-500">{{ $supplier->country }}</p>
                        </td>

                        {{-- PO Count --}}
                        <td class="py-3.5 px-4 text-center hidden xl:table-cell">
                            <a href="{{ route('admin.purchasing') }}?filterSupplierId={{ $supplier->id }}"
                               class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors">
                                {{ $supplier->purchase_orders_count }}
                            </a>
                        </td>

                        {{-- Total Spent --}}
                        <td class="py-3.5 px-4 text-right hidden xl:table-cell">
                            <span class="text-sm font-semibold text-slate-900">Rs. {{ number_format($supplier->totalSpent()) }}</span>
                        </td>

                        {{-- Status Toggle --}}
                        <td class="py-3.5 px-4 text-center">
                            <button wire:click="toggleActive({{ $supplier->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all
                                        {{ $supplier->is_active
                                            ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                            : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $supplier->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>

                        {{-- Actions --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Edit --}}
                                <button wire:click="openEdit({{ $supplier->id }})"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors"
                                        title="Edit Supplier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Delete --}}
                                <button wire:click="delete({{ $supplier->id }})"
                                        wire:confirm="Delete supplier '{{ addslashes($supplier->name) }}'? This cannot be undone."
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors"
                                        title="Delete Supplier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <p class="text-slate-600 font-medium">No suppliers found</p>
                                <p class="text-sm text-slate-400">Add your first supplier to get started</p>
                                <button wire:click="openCreate" class="btn-primary btn-sm mt-1">Add Supplier</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($suppliers->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>


    {{-- ══════════════════════ ADD / EDIT SUPPLIER MODAL ══════════════════════ --}}
    <div x-show="$wire.showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;">

        <div x-show="$wire.showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="$wire.showModal = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-slate-900">
                        {{ $editMode ? 'Edit Supplier' : 'Add New Supplier' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $editMode ? 'Update supplier information' : 'Add a production company or wholesaler' }}
                    </p>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                {{-- Row 1: Name + Contact Person --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="e.g. Sunrise Fabrics Ltd." class="form-input w-full">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Contact Person</label>
                        <input wire:model="contactPerson" type="text" placeholder="e.g. Ahmed Yusuf" class="form-input w-full">
                        @error('contactPerson') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 2: Email + Phone --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
                        <input wire:model="email" type="email" placeholder="supplier@example.com" class="form-input w-full">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="phone" type="text" placeholder="+251 911 000 000" class="form-input w-full">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 3: WhatsApp + Website --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">WhatsApp Number</label>
                        <input wire:model="whatsapp" type="text" placeholder="+251 911 000 000" class="form-input w-full">
                        @error('whatsapp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Website</label>
                        <input wire:model="website" type="url" placeholder="https://supplier.com" class="form-input w-full">
                        @error('website') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 4: City + Country --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">City</label>
                        <input wire:model="city" type="text" placeholder="e.g. Addis Ababa" class="form-input w-full">
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Country</label>
                        <input wire:model="country" type="text" placeholder="Ethiopia" class="form-input w-full">
                        @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Address</label>
                    <textarea wire:model="address" rows="2" placeholder="Street address..." class="form-input w-full resize-none"></textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Internal Notes</label>
                    <textarea wire:model="notes" rows="3" placeholder="Payment terms, lead times, quality notes..." class="form-input w-full resize-none"></textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Active Toggle --}}
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Active Supplier</p>
                        <p class="text-xs text-slate-500">Active suppliers appear in Purchase Order dropdowns</p>
                    </div>
                    <button type="button" wire:click="$toggle('isActive')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                {{ $isActive ? 'bg-emerald-500' : 'bg-slate-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                            {{ $isActive ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 shrink-0 bg-slate-50 rounded-b-2xl">
                <button wire:click="$set('showModal', false)"
                        class="btn-secondary">
                    Cancel
                </button>
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-not-allowed"
                        class="btn-primary inline-flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update Supplier' : 'Add Supplier' }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>

</div>
