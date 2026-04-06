{{-- resources/views/livewire/admin/purchasing.blade.php --}}
<div class="space-y-6">

    {{-- ══════════════════════ PAGE HEADER ══════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-14 -right-10 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-blue-400/15 blur-3xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300">Purchasing</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-dark">Purchase Orders</h2>
                <p class="text-sm text-slate-300 mt-1">Create and manage supplier purchase orders and stock intake</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button wire:click="generatePurchasingPlan"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/20 px-5 py-3 text-sm font-bold text-white hover:bg-white/20 transition-all duration-200 hover:-translate-y-0.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Generate Purchasing Plan
                </button>
                <button wire:click="openCreatePo"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-slate-900 hover:bg-amber-300 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-400/40 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Purchase Order
                </button>
            </div>
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
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Draft --}}
        <div class="card p-4 border-l-4 border-slate-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', 'draft')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-slate-700 font-[Poppins]">{{ $stats['draft'] }}</span>
            </div>
            <p class="text-sm font-medium text-slate-600">Draft</p>
            <p class="text-xs text-slate-400">Not yet ordered</p>
        </div>

        {{-- Ordered --}}
        <div class="card p-4 border-l-4 border-blue-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', 'ordered')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-blue-700 font-[Poppins]">{{ $stats['ordered'] }}</span>
            </div>
            <p class="text-sm font-medium text-slate-600">Ordered</p>
            <p class="text-xs text-slate-400">Awaiting delivery</p>
        </div>

        {{-- Received --}}
        <div class="card p-4 border-l-4 border-emerald-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', 'received')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-emerald-700 font-[Poppins]">{{ $stats['received'] }}</span>
            </div>
            <p class="text-sm font-medium text-slate-600">Received</p>
            <p class="text-xs text-slate-400">Stock updated</p>
        </div>

        {{-- Total Value --}}
        <div class="card p-4 border-l-4 border-amber-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', '')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-amber-700 font-[Poppins]">Rs. {{ number_format($stats['total_value']) }}</span>
            </div>
            <p class="text-sm font-medium text-slate-600">Total Purchased</p>
            <p class="text-xs text-slate-400">Received + partial POs</p>
        </div>
    </div>

    {{-- ══════════════════════ FILTER BAR ══════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search by PO number or supplier name..."
                       class="form-input pl-9 w-full">
            </div>
            <select wire:model.live="filterStatus" class="form-input w-full md:w-44">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="ordered">Ordered</option>
                <option value="partial">Partial</option>
                <option value="received">Received</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select wire:model.live="filterSupplierId" class="form-input w-full md:w-52">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ══════════════════════ PURCHASE ORDERS TABLE ══════════════════════ --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">PO Number</th>
                        <th class="text-left">Supplier</th>
                        <th class="text-center hidden md:table-cell">Items</th>
                        <th class="text-right">Total</th>
                        <th class="text-center hidden lg:table-cell">Expected</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                    <tr wire:key="po-{{ $po->id }}" class="hover:bg-slate-50 transition-colors">
                        {{-- PO Number --}}
                        <td class="py-3.5 px-4">
                            <span class="font-mono font-bold text-slate-900 text-sm tracking-wide">{{ $po->po_number }}</span>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $po->created_at->format('M d, Y') }}</p>
                        </td>

                        {{-- Supplier --}}
                        <td class="py-3.5 px-4">
                            <p class="font-semibold text-slate-900 text-sm">{{ $po->supplier?->name ?? '—' }}</p>
                            @if($po->supplier?->city)
                            <p class="text-xs text-slate-500">{{ $po->supplier->city }}</p>
                            @endif
                        </td>

                        {{-- Items Count --}}
                        <td class="py-3.5 px-4 text-center hidden md:table-cell">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $po->items->count() }}
                            </span>
                        </td>

                        {{-- Total --}}
                        <td class="py-3.5 px-4 text-right">
                            <span class="font-semibold text-slate-900 text-sm">Rs. {{ number_format($po->total) }}</span>
                        </td>

                        {{-- Expected Date --}}
                        <td class="py-3.5 px-4 text-center hidden lg:table-cell">
                            @if($po->expected_date)
                            <span class="text-sm {{ $po->expected_date->isPast() && ! in_array($po->status, ['received','cancelled']) ? 'text-red-500 font-semibold' : 'text-slate-600' }}">
                                {{ $po->expected_date->format('M d, Y') }}
                            </span>
                            @else
                            <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $color = match($po->status) {
                                    'draft'     => 'bg-slate-100 text-slate-600',
                                    'ordered'   => 'bg-blue-100 text-blue-700',
                                    'partial'   => 'bg-amber-100 text-amber-700',
                                    'received'  => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ $po->statusLabel() }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center justify-center gap-1">
                                {{-- View Detail --}}
                                <button wire:click="openDetailModal({{ $po->id }})"
                                        class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors"
                                        title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- Receive Goods --}}
                                @if(in_array($po->status, ['ordered', 'partial']))
                                <button wire:click="openReceiveModal({{ $po->id }})"
                                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors"
                                        title="Receive Goods">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- Mark Ordered (draft only) --}}
                                @if($po->status === 'draft')
                                <button wire:click="markOrdered({{ $po->id }})"
                                        wire:confirm="Mark PO #{{ $po->po_number }} as Ordered?"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors"
                                        title="Mark as Ordered">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- Edit --}}
                                @if(in_array($po->status, ['draft', 'ordered']))
                                <button wire:click="openEditPo({{ $po->id }})"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors"
                                        title="Edit PO">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- Cancel --}}
                                @if(in_array($po->status, ['draft', 'ordered']))
                                <button wire:click="cancelPo({{ $po->id }})"
                                        wire:confirm="Cancel PO #{{ $po->po_number }}? This action cannot be undone."
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors"
                                        title="Cancel PO">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- Delete (draft / cancelled only) --}}
                                @if(in_array($po->status, ['draft', 'cancelled']))
                                <button wire:click="deletePo({{ $po->id }})"
                                        wire:confirm="Permanently delete PO #{{ $po->po_number }}?"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors"
                                        title="Delete PO">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-slate-600 font-medium">No purchase orders found</p>
                                <p class="text-sm text-slate-400">Create your first PO to start purchasing stock</p>
                                <button wire:click="openCreatePo" class="btn-primary btn-sm mt-1">New Purchase Order</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($purchaseOrders->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $purchaseOrders->links() }}
        </div>
        @endif
    </div>


    {{-- ══════════════════════ CREATE / EDIT PO MODAL ══════════════════════ --}}
    <div x-show="$wire.showPoModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;">

        <div x-show="$wire.showPoModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="$wire.showPoModal = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-slate-900">
                        {{ $editMode ? 'Edit Purchase Order' : 'New Purchase Order' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $editMode ? 'Update PO details and items' : 'Order stock from a supplier' }}
                    </p>
                </div>
                <button wire:click="$set('showPoModal', false)"
                        class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-6">

                {{-- PO Info Row --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Supplier <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="supplierId" class="form-input w-full">
                            <option value="0">— Select Supplier —</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}{{ $sup->city ? ' ('.$sup->city.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('supplierId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Expected Delivery Date</label>
                        <input wire:model="expectedDate" type="date" class="form-input w-full">
                        @error('expectedDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Shipping Cost (Rs.)</label>
                        <input wire:model.live="shippingCost" type="number" min="0" step="0.01" placeholder="0" class="form-input w-full">
                        @error('shippingCost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Notes / Instructions</label>
                    <textarea wire:model="notes" rows="2" placeholder="Delivery instructions, quality requirements..." class="form-input w-full resize-none"></textarea>
                </div>

                {{-- Product Search & Add --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">Order Items</p>
                        <p class="text-xs text-slate-500">Search for products to add, then set quantities and costs</p>
                    </div>
                    <div class="p-4 space-y-4">

                        {{-- Product Search Input --}}
                        <div x-data="{ open: false }" class="relative">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input wire:model.live.debounce.300ms="itemProductSearch"
                                       type="text"
                                       placeholder="Search products by name or Code to add..."
                                       class="form-input pl-9 w-full"
                                       @focus="open = true"
                                       @click.outside="open = false"
                                       autocomplete="off">
                            </div>

                            {{-- Search Dropdown --}}
                            @if(strlen(trim($itemProductSearch)) >= 2)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-20 max-h-64 overflow-y-auto">
                                @forelse($this->productSearchResults as $product)
                                <button wire:click="addPoItem({{ $product->id }})"
                                        wire:key="search-result-{{ $product->id }}"
                                        type="button"
                                        class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition-colors text-left border-b border-slate-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500">Code: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right shrink-0 ml-4">
                                        <p class="text-xs text-slate-500">In stock: <span class="font-semibold text-slate-700">{{ $product->stock }}</span></p>
                                        <p class="text-xs text-amber-600 font-medium">Rs. {{ number_format($product->price) }}</p>
                                    </div>
                                </button>
                                @empty
                                <div class="px-4 py-6 text-center text-slate-500 text-sm">No products found for "{{ $itemProductSearch }}"</div>
                                @endforelse
                            </div>
                            @endif
                        </div>

                        {{-- Items Table --}}
                        @error('poItems') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror

                        @if(count($poItems) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="text-left pb-2 text-xs font-semibold text-slate-600 w-2/5">Product</th>
                                        <th class="text-left pb-2 text-xs font-semibold text-slate-600">Code</th>
                                        <th class="text-center pb-2 text-xs font-semibold text-slate-600 w-20">Qty</th>
                                        <th class="text-right pb-2 text-xs font-semibold text-slate-600 w-28">Unit Cost</th>
                                        <th class="text-right pb-2 text-xs font-semibold text-slate-600 w-28">Line Total</th>
                                        <th class="w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($poItems as $idx => $item)
                                    <tr wire:key="po-item-{{ $idx }}">
                                        <td class="py-2.5 pr-3">
                                            <input wire:model.live="poItems.{{ $idx }}.product_name"
                                                   type="text"
                                                   class="form-input w-full text-sm py-1.5"
                                                   placeholder="Product name">
                                            @error("poItems.{$idx}.product_name") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="py-2.5 pr-3">
                                            <input wire:model.live="poItems.{{ $idx }}.sku"
                                                   type="text"
                                                   class="form-input w-full text-sm py-1.5"
                                                   placeholder="Code">
                                        </td>
                                        <td class="py-2.5 pr-3">
                                            <input wire:model.live="poItems.{{ $idx }}.qty_ordered"
                                                   type="number" min="1"
                                                   class="form-input w-full text-sm py-1.5 text-center">
                                            @error("poItems.{$idx}.qty_ordered") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="py-2.5 pr-3">
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">Rs.</span>
                                                <input wire:model.live="poItems.{{ $idx }}.unit_cost"
                                                       type="number" min="0" step="0.01"
                                                       class="form-input w-full text-sm py-1.5 pl-8 text-right">
                                            </div>
                                            @error("poItems.{$idx}.unit_cost") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="py-2.5 pr-3 text-right">
                                            <span class="text-sm font-semibold text-slate-800">
                                                Rs. {{ number_format(max(0, (int)($item['qty_ordered'] ?? 0)) * max(0, (float)($item['unit_cost'] ?? 0))) }}
                                            </span>
                                        </td>
                                        <td class="py-2.5">
                                            <button wire:click="removePoItem({{ $idx }})"
                                                    type="button"
                                                    class="w-7 h-7 rounded-lg bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Order Totals Summary --}}
                        @php
                            $subTotal = collect($poItems)->sum(fn($i) => max(0, (int)($i['qty_ordered'] ?? 0)) * max(0, (float)($i['unit_cost'] ?? 0)));
                            $ship     = max(0, (float)$shippingCost);
                            $grandTotal = $subTotal + $ship;
                        @endphp
                        <div class="border-t border-slate-200 pt-4 space-y-1.5 flex flex-col items-end">
                            <div class="flex justify-between w-48">
                                <span class="text-sm text-slate-500">Subtotal</span>
                                <span class="text-sm font-semibold text-slate-800">Rs. {{ number_format($subTotal) }}</span>
                            </div>
                            <div class="flex justify-between w-48">
                                <span class="text-sm text-slate-500">Shipping</span>
                                <span class="text-sm font-semibold text-slate-800">Rs. {{ number_format($ship) }}</span>
                            </div>
                            <div class="flex justify-between w-48 border-t border-slate-200 pt-1.5 mt-0.5">
                                <span class="text-sm font-bold text-slate-900">Total</span>
                                <span class="text-base font-bold text-amber-600">Rs. {{ number_format($grandTotal) }}</span>
                            </div>
                        </div>
                        @else
                        <div class="py-10 text-center border-2 border-dashed border-slate-200 rounded-xl">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="text-sm text-slate-500">No items added yet.</p>
                            <p class="text-xs text-slate-400 mt-1">Search for products above to add them to this order.</p>
                        </div>
                        @endif

                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 shrink-0 bg-slate-50 rounded-b-2xl">
                <button wire:click="$set('showPoModal', false)" class="btn-secondary">Cancel</button>
                <div class="flex items-center gap-3">
                    {{-- Save as Draft --}}
                    <button wire:click="savePo"
                            wire:loading.attr="disabled"
                            wire:target="savePo,savePoOrdered"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 text-slate-800 text-sm font-semibold hover:bg-slate-300 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span wire:loading.remove wire:target="savePo">Save as Draft</span>
                        <span wire:loading wire:target="savePo">Saving...</span>
                    </button>
                    {{-- Save & Mark Ordered --}}
                    <button wire:click="savePoOrdered"
                            wire:loading.attr="disabled"
                            wire:target="savePo,savePoOrdered"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                            class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="savePoOrdered">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading wire:target="savePoOrdered">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="savePoOrdered">Save &amp; Mark Ordered</span>
                        <span wire:loading wire:target="savePoOrdered">Ordering...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════════════ RECEIVE GOODS MODAL ══════════════════════ --}}
    <div x-show="$wire.showReceiveModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;">

        <div x-show="$wire.showReceiveModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="$wire.showReceiveModal = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-slate-900">Receive Goods</h3>
                    @if($receivingPo)
                    <p class="text-xs text-slate-500 mt-0.5">PO #{{ $receivingPo->po_number }} &mdash; {{ $receivingPo->supplier?->name }}</p>
                    @endif
                </div>
                <button wire:click="$set('showReceiveModal', false)"
                        class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

                {{-- Stock Warning --}}
                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Stock will be updated upon confirmation</p>
                        <p class="text-xs text-amber-700 mt-0.5">The quantities you enter below will be added to each product's stock level immediately.</p>
                    </div>
                </div>

                {{-- Items Table --}}
                @if($receivingPo)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left pb-2.5 text-xs font-semibold text-slate-600">Product</th>
                                <th class="text-center pb-2.5 text-xs font-semibold text-slate-600 w-24">Ordered</th>
                                <th class="text-center pb-2.5 text-xs font-semibold text-slate-600 w-28">Prev. Received</th>
                                <th class="text-center pb-2.5 text-xs font-semibold text-slate-600 w-32">Receive Now</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($receivingPo->items as $item)
                            <tr wire:key="recv-item-{{ $item->id }}">
                                <td class="py-3 pr-3">
                                    <p class="font-semibold text-slate-900">{{ $item->product_name }}</p>
                                    @if($item->sku)
                                    <p class="text-xs text-slate-500">{{ $item->sku }}</p>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <span class="font-semibold text-slate-700">{{ $item->quantity_ordered }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="text-slate-600">{{ $item->quantity_received }}</span>
                                    @if($item->quantity_received > 0)
                                    <span class="block text-xs text-slate-400">received</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @php $remaining = $item->quantity_ordered - $item->quantity_received; @endphp
                                    <input wire:model="receiveQtys.{{ $item->id }}"
                                           type="number"
                                           min="0"
                                           max="{{ $remaining }}"
                                           class="form-input w-full text-center py-1.5 text-sm {{ $remaining === 0 ? 'bg-slate-50 text-slate-400' : '' }}"
                                           {{ $remaining === 0 ? 'disabled' : '' }}>
                                    @if($remaining === 0)
                                    <p class="text-xs text-emerald-600 text-center mt-0.5 font-medium">Fully received</p>
                                    @else
                                    <p class="text-xs text-slate-400 text-center mt-0.5">Max: {{ $remaining }}</p>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 shrink-0 bg-slate-50 rounded-b-2xl">
                <button wire:click="$set('showReceiveModal', false)" class="btn-secondary">Cancel</button>
                <button wire:click="receiveGoods"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-not-allowed"
                        class="btn-primary inline-flex items-center gap-2">
                    <span wire:loading.remove wire:target="receiveGoods">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="receiveGoods">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="receiveGoods">Confirm Receipt &amp; Update Stock</span>
                    <span wire:loading wire:target="receiveGoods">Processing...</span>
                </button>
            </div>
        </div>
    </div>


    {{-- ══════════════════════ PO DETAIL MODAL ══════════════════════ --}}
    <div x-show="$wire.showDetailModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;">

        <div x-show="$wire.showDetailModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="$wire.showDetailModal = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

            @if($detailPo)
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-slate-900">Purchase Order Details</h3>
                    <p class="text-xs font-mono text-slate-500 mt-0.5">{{ $detailPo->po_number }}</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- WhatsApp Supplier --}}
                    @if($detailPo->supplier?->whatsapp || $detailPo->supplier?->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $detailPo->supplier->whatsapp ?? $detailPo->supplier->phone) }}?text={{ urlencode('Hello, regarding Purchase Order #'.$detailPo->po_number.' — Meharahouse') }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.113.549 4.097 1.508 5.826L0 24l6.334-1.482A11.933 11.933 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.796 9.796 0 01-5.064-1.41l-.364-.216-3.76.879.936-3.66-.236-.376A9.787 9.787 0 012.182 12C2.182 6.569 6.569 2.182 12 2.182S21.818 6.569 21.818 12 17.431 21.818 12 21.818z"/>
                        </svg>
                        WhatsApp Supplier
                    </a>
                    @endif
                    <button wire:click="$set('showDetailModal', false)"
                            class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                {{-- PO Info Grid --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Supplier</p>
                            <p class="font-semibold text-slate-900 mt-0.5">{{ $detailPo->supplier?->name ?? '—' }}</p>
                            @if($detailPo->supplier?->contact_person)
                            <p class="text-xs text-slate-500">{{ $detailPo->supplier->contact_person }}</p>
                            @endif
                            @if($detailPo->supplier?->city)
                            <p class="text-xs text-slate-500">{{ $detailPo->supplier->city }}, {{ $detailPo->supplier->country }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Status</p>
                            @php
                                $dColor = match($detailPo->status) {
                                    'draft'     => 'bg-slate-100 text-slate-600',
                                    'ordered'   => 'bg-blue-100 text-blue-700',
                                    'partial'   => 'bg-amber-100 text-amber-700',
                                    'received'  => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold mt-0.5 {{ $dColor }}">
                                {{ $detailPo->statusLabel() }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Created</p>
                            <p class="font-medium text-slate-800 mt-0.5">{{ $detailPo->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($detailPo->ordered_at)
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Ordered At</p>
                            <p class="font-medium text-slate-800 mt-0.5">{{ $detailPo->ordered_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        @if($detailPo->expected_date)
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Expected Delivery</p>
                            <p class="font-medium mt-0.5 {{ $detailPo->expected_date->isPast() && !in_array($detailPo->status, ['received','cancelled']) ? 'text-red-500' : 'text-slate-800' }}">
                                {{ $detailPo->expected_date->format('M d, Y') }}
                            </p>
                        </div>
                        @endif
                        @if($detailPo->received_at)
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Received At</p>
                            <p class="font-medium text-slate-800 mt-0.5">{{ $detailPo->received_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($detailPo->notes)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-xs text-slate-500 font-medium mb-1">Notes</p>
                    <p class="text-sm text-slate-700">{{ $detailPo->notes }}</p>
                </div>
                @endif

                {{-- Items --}}
                <div>
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-3">Order Items</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left pb-2 text-xs font-semibold text-slate-600">Product</th>
                                    <th class="text-center pb-2 text-xs font-semibold text-slate-600 w-20">Ordered</th>
                                    <th class="text-center pb-2 text-xs font-semibold text-slate-600 w-24">Received</th>
                                    <th class="text-right pb-2 text-xs font-semibold text-slate-600 w-24">Unit Cost</th>
                                    <th class="text-right pb-2 text-xs font-semibold text-slate-600 w-24">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($detailPo->items as $item)
                                <tr wire:key="detail-item-{{ $item->id }}">
                                    <td class="py-2.5 pr-3">
                                        <p class="font-semibold text-slate-900">{{ $item->product_name }}</p>
                                        @if($item->sku)<p class="text-xs text-slate-500">{{ $item->sku }}</p>@endif
                                    </td>
                                    <td class="py-2.5 text-center text-slate-700">{{ $item->quantity_ordered }}</td>
                                    <td class="py-2.5 text-center">
                                        <span class="font-semibold {{ $item->quantity_received >= $item->quantity_ordered ? 'text-emerald-600' : ($item->quantity_received > 0 ? 'text-amber-600' : 'text-slate-400') }}">
                                            {{ $item->quantity_received }}
                                        </span>
                                        @if($item->quantity_received >= $item->quantity_ordered)
                                        <span class="block text-xs text-emerald-600">complete</span>
                                        @elseif($item->quantity_received > 0)
                                        <span class="block text-xs text-amber-600">partial</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-right text-slate-700">Rs. {{ number_format($item->unit_cost) }}</td>
                                    <td class="py-2.5 text-right font-semibold text-slate-900">Rs. {{ number_format($item->subtotal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="border-t border-slate-200 pt-3 mt-1 space-y-1 flex flex-col items-end">
                        <div class="flex justify-between w-48">
                            <span class="text-sm text-slate-500">Subtotal</span>
                            <span class="text-sm font-semibold text-slate-800">Rs. {{ number_format($detailPo->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between w-48">
                            <span class="text-sm text-slate-500">Shipping</span>
                            <span class="text-sm font-semibold text-slate-800">Rs. {{ number_format($detailPo->shipping_cost) }}</span>
                        </div>
                        <div class="flex justify-between w-48 border-t border-slate-200 pt-1.5">
                            <span class="text-sm font-bold text-slate-900">Total</span>
                            <span class="text-base font-bold text-amber-600">Rs. {{ number_format($detailPo->total) }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 shrink-0 bg-slate-50 rounded-b-2xl">
                <button wire:click="$set('showDetailModal', false)" class="btn-secondary">Close</button>
                @if(in_array($detailPo->status, ['ordered', 'partial']))
                <button wire:click="openReceiveModal({{ $detailPo->id }}); $wire.showDetailModal = false"
                        class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Receive Goods
                </button>
                @endif
            </div>
            @else
            <div class="flex-1 flex items-center justify-center py-16 text-slate-400">
                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ PURCHASING PLAN MODAL ══ --}}
    @if($showPlanModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-900 to-slate-800">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-white">Purchasing Plan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Based on {{ count($planOrderIds) }} pending order(s)</p>
                </div>
                <button wire:click="$set('showPlanModal', false)" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-y-auto flex-1 px-6 py-4">
                @if(empty($planItems))
                    <p class="text-center text-slate-400 py-8">All pending orders have sufficient stock.</p>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Product</th>
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Code</th>
                            <th class="text-center py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Orders</th>
                            <th class="text-center py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Needed</th>
                            <th class="text-center py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">In Stock</th>
                            <th class="text-center py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">To Buy</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($planItems as $item)
                        <tr class="{{ $item['to_buy'] > 0 ? 'bg-red-50/40' : '' }}">
                            <td class="py-3 font-medium text-slate-800">{{ $item['product_name'] }}</td>
                            <td class="py-3 text-slate-500 font-mono text-xs">{{ $item['sku'] ?: '—' }}</td>
                            <td class="py-3 text-center text-slate-500">{{ $item['order_count'] }}</td>
                            <td class="py-3 text-center font-semibold text-slate-700">{{ $item['qty_needed'] }}</td>
                            <td class="py-3 text-center">
                                <span class="{{ $item['current_stock'] < $item['qty_needed'] ? 'text-red-600 font-bold' : 'text-green-600 font-semibold' }}">
                                    {{ $item['current_stock'] }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                @if($item['to_buy'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        Buy {{ $item['to_buy'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center gap-3">
                @php $hasShortage = collect($planItems)->where('to_buy', '>', 0)->count() > 0; @endphp
                @if($hasShortage)
                <button wire:click="loadPlanIntoPoModal"
                        class="flex-1 py-2.5 rounded-xl bg-amber-400 text-slate-900 text-sm font-bold hover:bg-amber-300 transition-colors">
                    Create Purchase Order from Plan
                </button>
                @else
                <p class="flex-1 text-sm text-green-600 font-semibold">All products have sufficient stock. No purchasing needed.</p>
                @endif
                <button wire:click="$set('showPlanModal', false)"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ READY ORDERS MODAL (after receiving goods) ══ --}}
    @if($showReadyOrdersModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-green-600 to-emerald-600 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-white text-lg">Stock Updated!</h3>
                    <p class="text-green-100 text-xs">Goods have been received and stock incremented</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-center">
                    <p class="text-3xl font-bold text-green-700 font-[Poppins]">{{ $readyOrdersCount }}</p>
                    <p class="text-sm text-green-600 font-medium mt-1">{{ Str::plural('order', $readyOrdersCount) }} now ready to confirm</p>
                    <p class="text-xs text-slate-400 mt-1">All items have sufficient stock</p>
                </div>
                <p class="text-sm text-slate-600 mb-4">Would you like to auto-confirm all ready orders, or handle them one by one from the Orders page?</p>
                <div class="flex flex-col gap-2">
                    <button wire:click="autoConfirmReadyOrders"
                            wire:loading.attr="disabled"
                            class="w-full py-3 rounded-xl bg-green-600 text-white text-sm font-bold hover:bg-green-700 transition-colors">
                        <span wire:loading.remove wire:target="autoConfirmReadyOrders">Auto-Confirm All {{ $readyOrdersCount }} {{ Str::plural('Order', $readyOrdersCount) }}</span>
                        <span wire:loading wire:target="autoConfirmReadyOrders">Confirming...</span>
                    </button>
                    <button wire:click="dismissReadyOrders"
                            class="w-full py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        I'll Confirm Manually (Orders Page)
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
