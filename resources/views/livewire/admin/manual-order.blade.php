{{-- resources/views/livewire/admin/manual-order.blade.php --}}
<div class="space-y-6" x-data="{ copyDone: false }">

    {{-- ══════════════════════ PAGE HEADER ══════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-14 -right-10 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-emerald-400/15 blur-3xl"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300">Admin Tool</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-dark">Create Manual Order</h2>
                <p class="text-sm text-slate-300 mt-1">Place an order on behalf of a customer by phone or walk-in</p>
            </div>
            <a href="{{ route('admin.orders') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-600/80 bg-slate-800/60 px-4 py-2 text-xs font-semibold text-slate-100 hover:bg-slate-700/70 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                All Orders
            </a>
        </div>
    </div>

    {{-- ══════════════════════ SUCCESS CARD ══════════════════════ --}}
    @if($createdOrderNumber)
    <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-6 shadow-sm">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-emerald-400/10 -translate-y-8 translate-x-8"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-[Poppins] font-bold text-lg text-emerald-800">Order Placed Successfully!</h3>
                <p class="text-sm text-emerald-700 mt-1">
                    Order <span class="font-mono font-bold bg-emerald-200/60 px-2 py-0.5 rounded">{{ $createdOrderNumber }}</span>
                    has been created and is now pending confirmation.
                </p>
                <p class="text-xs text-emerald-600 mt-2">Stock will be deducted when confirmed in the Purchasing page.</p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="{{ route('admin.orders') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Orders
                </a>
                <a href="{{ route('admin.purchasing') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-slate-900 text-sm font-bold hover:bg-amber-400 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Go to Purchasing
                </a>
                <button wire:click="resetAll"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Order
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════ FLASH MESSAGES ══════════════════════ --}}
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- ══════════════════════ CUSTOMER SEARCH ══════════════════════ --}}
    <div class="card p-5 shadow-sm">
        <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            Step 1 — Find Customer
        </h3>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input wire:model="phoneSearch"
                       wire:keydown.enter="searchCustomer"
                       type="text"
                       placeholder="Search by phone number or customer name..."
                       class="form-input pl-9 w-full">
            </div>
            <button wire:click="searchCustomer" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0F172A] text-white text-sm font-semibold hover:bg-slate-800 transition-colors shrink-0">
                <span wire:loading.remove wire:target="searchCustomer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <span wire:loading wire:target="searchCustomer">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
                Search
            </button>
        </div>

        {{-- Found customer card --}}
        @if($foundCustomer && $customerConfirmed)
        <div class="mt-4 flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <div class="w-11 h-11 rounded-full bg-emerald-600 flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-base">{{ strtoupper(substr($foundCustomer->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-semibold text-emerald-900">{{ $foundCustomer->name }}</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-bold uppercase tracking-wide">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Customer Confirmed
                    </span>
                </div>
                <p class="text-sm text-emerald-700">{{ $foundCustomer->email }}</p>
                @if($foundCustomer->phone)
                <p class="text-xs text-emerald-600">{{ $foundCustomer->phone }}</p>
                @endif
            </div>
            <button wire:click="$set('customerConfirmed', false); $set('foundCustomer', null)"
                    class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-200 transition-colors" title="Change customer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        {{-- New customer form --}}
        @if($showNewCustomerForm)
        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-3">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <p class="text-sm font-bold text-amber-800">Customer not found — Create New Customer</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-amber-800 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input wire:model="newName" type="text" placeholder="e.g. Tigist Bekele" class="form-input w-full text-sm">
                    @error('newName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-amber-800 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input wire:model="newPhone" type="text" placeholder="+251 9XX XXX XXX" class="form-input w-full text-sm">
                    @error('newPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-amber-800 mb-1">Email (optional)</label>
                    <input wire:model="newEmail" type="email" placeholder="customer@email.com" class="form-input w-full text-sm">
                    @error('newEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-3">
                <button wire:click="createNewCustomer" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-slate-900 text-sm font-bold hover:bg-amber-400 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create Customer
                </button>
                <button wire:click="$set('showNewCustomerForm', false)"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════ MAIN GRID ══════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- ══════════ LEFT COLUMN (3/5) — Products & Notes ══════════ --}}
        <div class="xl:col-span-3 space-y-5">

            {{-- Product Search --}}
            <div class="card p-5 shadow-sm">
                <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    Step 2 — Add Products
                </h3>

                {{-- Search input with live dropdown --}}
                <div class="relative" x-data="{ open: @entangle('productSearch').live }">
                    <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20 transition-all">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input wire:model.live.debounce.300ms="productSearch"
                               type="text"
                               placeholder="Search product name or Code..."
                               class="flex-1 text-sm outline-none bg-transparent placeholder-slate-400 min-w-0">
                        @if($productSearch)
                        <button wire:click="$set('productSearch', '')" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        @endif
                    </div>

                    {{-- Dropdown results --}}
                    @if(strlen($productSearch) >= 2)
                    <div class="absolute top-full left-0 right-0 z-30 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">
                        @if($this->productResults->isEmpty())
                        <div class="px-4 py-3 text-sm text-slate-500 text-center">No products found for "{{ $productSearch }}"</div>
                        @else
                        <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                            @foreach($this->productResults as $product)
                            <button wire:click="addProduct({{ $product->id }})"
                                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-amber-50 transition-colors text-left group">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                    @if(!empty($product->images))
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate group-hover:text-amber-700">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-500">Code: {{ $product->sku ?? 'N/A' }} &bull; Stock: {{ $product->stock }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-[#0F172A]">Rs. {{ number_format($product->effectivePrice(), 0) }}</p>
                                    @if($product->isOnSale())
                                    <p class="text-xs text-slate-400 line-through">Rs. {{ number_format($product->price, 0) }}</p>
                                    @endif
                                </div>
                                <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Order Items Table --}}
                <div class="mt-4">
                    @if(empty($orderItems))
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400 border-2 border-dashed border-slate-200 rounded-xl">
                        <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-sm font-medium">No products added yet</p>
                        <p class="text-xs mt-1">Search and click a product above to add it</p>
                    </div>
                    @else
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Product</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Qty</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Unit Price</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Line Total</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($orderItems as $productId => $item)
                                <tr class="hover:bg-slate-50 transition-colors" wire:key="item-{{ $productId }}">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-[#0F172A]">{{ $item['name'] }}</p>
                                        @if($item['sku'])
                                        <p class="text-xs text-slate-400">Code: {{ $item['sku'] }}</p>
                                        @endif
                                        @if($item['qty'] > $item['stock'])
                                        <p class="text-xs text-red-500 font-medium mt-0.5">Warning: only {{ $item['stock'] }} in stock</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="updateQty({{ $productId }}, {{ $item['qty'] - 1 }})"
                                                    class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:border-slate-300 transition-all font-bold text-base leading-none">
                                                &minus;
                                            </button>
                                            <input type="number"
                                                   value="{{ $item['qty'] }}"
                                                   wire:change="updateQty({{ $productId }}, $event.target.value)"
                                                   min="1"
                                                   class="w-12 text-center text-sm font-bold border border-slate-200 rounded-lg py-1 outline-none focus:border-amber-400">
                                            <button wire:click="updateQty({{ $productId }}, {{ $item['qty'] + 1 }})"
                                                    class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:border-slate-300 transition-all font-bold text-base leading-none">
                                                &#43;
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium text-slate-700">Rs. {{ number_format($item['price'], 0) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-bold text-[#0F172A]">Rs. {{ number_format($item['price'] * $item['qty'], 0) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="removeItem({{ $productId }})"
                                                class="w-7 h-7 rounded-lg bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center mx-auto transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="card p-5 shadow-sm">
                <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    Payment Method
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                    $paymentOptions = [
                        'cash_on_delivery' => ['label' => 'Cash on Delivery', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                        'card'             => ['label' => 'Card / POS', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        'telebirr'         => ['label' => 'TeleBirr', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                        'bank_transfer'    => ['label' => 'Bank Transfer', 'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'],
                    ];
                    @endphp
                    @foreach($paymentOptions as $value => $option)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="paymentMethod" value="{{ $value }}" class="sr-only">
                        <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all duration-200
                            {{ $paymentMethod === $value
                                ? 'border-amber-400 bg-amber-50 text-amber-700'
                                : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $option['icon'] }}"/>
                            </svg>
                            <span class="text-xs font-semibold text-center leading-tight">{{ $option['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Notes --}}
            <div class="card p-5 shadow-sm">
                <label class="block text-sm font-semibold text-[#0F172A] mb-2">Order Notes (optional)</label>
                <textarea wire:model="notes"
                          rows="3"
                          placeholder="Special instructions, delivery preferences, etc..."
                          class="form-input w-full resize-none text-sm"></textarea>
            </div>
        </div>

        {{-- ══════════ RIGHT COLUMN (2/5) — Delivery & Summary ══════════ --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Delivery Address --}}
            <div class="card p-5 shadow-sm">
                <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    Delivery Address
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input wire:model="deliveryName" type="text" placeholder="Recipient full name" class="form-input w-full text-sm">
                        @error('deliveryName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Phone <span class="text-red-500">*</span></label>
                        <input wire:model="deliveryPhone" type="text" placeholder="+251 9XX XXX XXX" class="form-input w-full text-sm">
                        @error('deliveryPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Address Line <span class="text-red-500">*</span></label>
                        <input wire:model="deliveryAddress" type="text" placeholder="Street, building, etc." class="form-input w-full text-sm">
                        @error('deliveryAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">City <span class="text-red-500">*</span></label>
                            <input wire:model="deliveryCity" type="text" placeholder="Addis Ababa" class="form-input w-full text-sm">
                            @error('deliveryCity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Region</label>
                            <input wire:model="deliveryRegion" type="text" placeholder="Oromia, etc." class="form-input w-full text-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card p-5 shadow-sm sticky top-24">
                <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    Order Summary
                </h3>

                <div class="space-y-3 mb-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Items</span>
                        <span class="font-semibold text-[#0F172A]">{{ collect($orderItems)->sum('qty') }} pcs</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Shipping</span>
                        <span class="text-emerald-600 font-semibold">Free</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Tax</span>
                        <span class="font-semibold text-[#0F172A]">Rs. 0</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex items-center justify-between">
                        <span class="font-bold text-[#0F172A]">Total</span>
                        <span class="font-[Poppins] font-extrabold text-xl text-[#0F172A]">Rs. {{ number_format($this->getSubtotal(), 0) }}</span>
                    </div>
                </div>

                {{-- Customer indicator --}}
                @if($customerConfirmed && $foundCustomer)
                <div class="flex items-center gap-2 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg mb-4 text-xs">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-emerald-700 font-medium truncate">{{ $foundCustomer->name }}</span>
                </div>
                @else
                <div class="flex items-center gap-2 p-2.5 bg-amber-50 border border-amber-200 rounded-lg mb-4 text-xs">
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-amber-700 font-medium">No customer selected</span>
                </div>
                @endif

                {{-- Place Order Button --}}
                <button wire:click="placeOrder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-not-allowed"
                        @if(empty($orderItems) || !$customerConfirmed) disabled @endif
                        class="w-full flex items-center justify-center gap-3 px-5 py-4 rounded-xl font-[Poppins] font-bold text-base transition-all duration-200
                            {{ (!empty($orderItems) && $customerConfirmed)
                                ? 'bg-emerald-600 text-white hover:bg-emerald-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-600/30 cursor-pointer'
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="placeOrder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="placeOrder">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                    <span wire:loading wire:target="placeOrder">Placing Order...</span>
                </button>

                @if(empty($orderItems))
                <p class="text-center text-xs text-slate-400 mt-2">Add at least one product to place the order</p>
                @elseif(!$customerConfirmed)
                <p class="text-center text-xs text-slate-400 mt-2">Search and confirm a customer first</p>
                @endif
            </div>
        </div>
    </div>

</div>
