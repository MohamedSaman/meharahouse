{{-- resources/views/livewire/admin/backorder.blade.php --}}
<div
    class="space-y-5"
    x-data="{
        detailOpen: @entangle('showDetail'),
        dispatchOpen: @entangle('showDispatchModal'),
        replaceOpen: @entangle('showReplaceModal'),
    }"
>

    {{-- ══════════════════════ FLASH ══════════════════════ --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    {{-- ══════════════════════ HEADER ══════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Back Orders</h2>
            <p class="text-sm text-[#64748B]">
                {{ $stats['active'] }} active backorder{{ $stats['active'] !== 1 ? 's' : '' }} across orders
            </p>
        </div>
        {{-- Stat chips --}}
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="$set('filterStatus', 'pending')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'pending' ? 'bg-amber-500 border-amber-600 shadow-md shadow-amber-500/25' : 'bg-amber-50 border-amber-200 hover:bg-amber-100' }}"
                    style="{{ $filterStatus === 'pending' ? 'color:#ffffff;' : 'color:#b45309;' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400 {{ $stats['pending'] > 0 ? 'animate-pulse' : '' }}"></span>
                Pending <span class="font-bold">{{ $stats['pending'] }}</span>
            </button>
            <button wire:click="$set('filterStatus', 'repurchasing')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'repurchasing' ? 'bg-blue-500 border-blue-600' : 'bg-blue-50 border-blue-200 hover:bg-blue-100' }}"
                    style="{{ $filterStatus === 'repurchasing' ? 'color:#ffffff;' : 'color:#1d4ed8;' }}">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                Repurchasing <span class="font-bold">{{ $stats['repurchasing'] }}</span>
            </button>
            <button wire:click="$set('filterStatus', 'ready')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'ready' ? 'bg-violet-500 border-violet-600' : 'bg-violet-50 border-violet-200 hover:bg-violet-100' }}"
                    style="{{ $filterStatus === 'ready' ? 'color:#ffffff;' : 'color:#6d28d9;' }}">
                <span class="w-2 h-2 rounded-full bg-violet-400 {{ $stats['ready'] > 0 ? 'animate-pulse' : '' }}"></span>
                Ready <span class="font-bold">{{ $stats['ready'] }}</span>
            </button>
            <button wire:click="$set('filterStatus', 'dispatched')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'dispatched' ? 'bg-indigo-500 border-indigo-600' : 'bg-indigo-50 border-indigo-200 hover:bg-indigo-100' }}"
                    style="{{ $filterStatus === 'dispatched' ? 'color:#ffffff;' : 'color:#4338ca;' }}">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                Dispatched <span class="font-bold">{{ $stats['dispatched'] }}</span>
            </button>
            @if($filterStatus)
            <button wire:click="$set('filterStatus', '')"
                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors" title="Clear filter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════ FILTERS ══════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
                <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search by order #, customer, or product..."
                       class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
                @if($search)
                <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium shrink-0">Filter items:</span>
                <select wire:model.live="filterStatus"
                        class="text-sm border border-slate-200 rounded-lg px-3 py-2 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="repurchasing">Repurchasing</option>
                    <option value="ready">Ready</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            <div wire:loading.flex class="items-center gap-2 text-xs text-slate-400">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Loading...
            </div>
        </div>
    </div>

    {{-- ══════════════════════ ORDER CARDS ══════════════════════ --}}
    <div class="space-y-3">
        @forelse($ordersQuery as $order)
        @php
            $activeBackorders = $order->backorders->whereNotIn('status', ['completed', 'cancelled']);
            $addr = $order->shipping_address ?? [];
            $custName = $order->user?->name ?? ($addr['full_name'] ?? 'Guest');

            // Determine overall worst status for badge
            $statusPriority = ['pending' => 0, 'repurchasing' => 1, 'ready' => 2, 'dispatched' => 3, 'delivered' => 4];
            $worstStatus = $activeBackorders->sortBy(fn($b) => $statusPriority[$b->status] ?? 99)->first()?->status ?? 'pending';

            $statusColors = [
                'pending'      => 'bg-amber-100 text-amber-700 border-amber-200',
                'repurchasing' => 'bg-blue-100 text-blue-700 border-blue-200',
                'ready'        => 'bg-violet-100 text-violet-700 border-violet-200',
                'dispatched'   => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                'delivered'    => 'bg-teal-100 text-teal-700 border-teal-200',
            ];
            $statusLabels = [
                'pending'      => 'Pending',
                'repurchasing' => 'Repurchasing',
                'ready'        => 'Ready to Dispatch',
                'dispatched'   => 'Dispatched',
                'delivered'    => 'Delivered',
            ];
        @endphp
        <div wire:key="order-{{ $order->id }}" class="card overflow-hidden">

            {{-- Order header bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0]">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="font-mono font-bold text-[#0F172A] text-sm">{{ $order->order_number }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $statusColors[$worstStatus] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        {{ $statusLabels[$worstStatus] ?? ucfirst($worstStatus) }}
                    </span>
                    <span class="text-xs text-slate-500">{{ $order->created_at->timezone('Asia/Colombo')->format('d M Y') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="font-semibold text-[#0F172A] text-sm">{{ $custName }}</p>
                        @if($addr['phone'] ?? $order->user?->phone ?? null)
                        <p class="text-xs text-slate-400">{{ $addr['phone'] ?? $order->user?->phone }}</p>
                        @endif
                    </div>
                    <button wire:click="viewOrder({{ $order->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-all hover:-translate-y-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Details
                    </button>
                </div>
            </div>

            {{-- Backordered items list --}}
            <div class="divide-y divide-[#F1F5F9]">
                @foreach($activeBackorders as $bo)
                @php
                    $currentStock = $bo->product?->stock ?? 0;
                @endphp
                <div wire:key="bo-{{ $bo->id }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5">

                    {{-- Product info --}}
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        {{-- Status dot --}}
                        <div class="mt-0.5 w-2 h-2 rounded-full shrink-0
                            {{ match($bo->status) {
                                'pending'      => 'bg-amber-400',
                                'repurchasing' => 'bg-blue-400',
                                'ready'        => 'bg-violet-500',
                                'dispatched'   => 'bg-indigo-500',
                                'delivered'    => 'bg-teal-500',
                                default        => 'bg-slate-300',
                            } }}"></div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                {{-- Product name (strikethrough if replacement) --}}
                                <span class="font-medium text-sm text-[#0F172A] {{ $bo->isReplacement() ? 'line-through text-slate-400' : '' }}">
                                    {{ $bo->product_name }}
                                </span>
                                @if($bo->isReplacement())
                                <span class="flex items-center gap-1 text-xs font-semibold text-orange-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    {{ $bo->replacementProduct?->name }}
                                </span>
                                @endif
                                {{-- Short qty pill --}}
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    ×{{ $bo->short_qty }} short
                                </span>
                                {{-- BO number --}}
                                <span class="font-mono text-[10px] text-slate-400">{{ $bo->backorder_number }}</span>
                                @if($bo->size)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Size: {{ $bo->size }}
                                </span>
                                @endif
                            </div>
                            {{-- Status badge --}}
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $bo->statusBadgeClass() }}">
                                {{ $bo->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if($bo->isActive())
                        <div class="flex items-center gap-1.5">
                            <button wire:click="openReplaceModal({{ $bo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 text-xs font-semibold transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Replace
                            </button>
                            <button wire:click="openRefundModal({{ $bo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 text-xs font-semibold transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                Refund
                            </button>
                        </div>
                        @endif
                        <span class="text-[10px] text-slate-400 font-medium italic">Logistics: Shipments</span>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @empty
        <div class="card p-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">No backorders found</p>
                    <p class="text-slate-400 text-sm mt-1">
                        @if($search || $filterStatus) Try adjusting your filters
                        @else Backorders appear here when stock runs short on confirmed orders
                        @endif
                    </p>
                </div>
                @if($search || $filterStatus)
                <button wire:click="$set('search', ''); $set('filterStatus', '')"
                        class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Clear filters</button>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($ordersQuery->hasPages())
    <div class="card px-5 py-4">
        {{ $ordersQuery->links() }}
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         ORDER DETAIL SLIDE-OVER
    ══════════════════════════════════════════════════════════════ --}}
    @if($selectedOrder)
    <div x-show="detailOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-40" style="display:none;"
         @click="detailOpen = false; $wire.closeDetail()"></div>

    <div x-show="detailOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-screen w-full max-w-2xl bg-white shadow-2xl z-50 flex flex-col"
         style="display:none;" @click.stop>

        {{-- Panel Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-[#0F172A] shrink-0">
            <div>
                <h3 class="font-[Poppins] font-bold text-white text-base">{{ $selectedOrder->order_number }}</h3>
                <p class="text-xs text-slate-400">Backordered items from this order</p>
            </div>
            <button wire:click="closeDetail" class="p-2 rounded-xl text-slate-400 hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Panel Body --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-5">

            {{-- Customer info --}}
            @php
                $addr2    = $selectedOrder->shipping_address ?? [];
                $custName2 = $selectedOrder->user?->name ?? ($addr2['full_name'] ?? 'Guest');
                $custPhone2 = $addr2['phone'] ?? ($selectedOrder->user?->phone ?? '');
            @endphp
            <div class="bg-[#F8FAFC] rounded-2xl p-4">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Customer</h4>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#0F172A] flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr($custName2, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-[#0F172A]">{{ $custName2 }}</p>
                        @if($custPhone2)<p class="text-xs text-slate-400">{{ $custPhone2 }}</p>@endif
                        @if($addr2['address'] ?? null)<p class="text-xs text-slate-400 mt-0.5">{{ $addr2['address'] }}, {{ $addr2['city'] ?? '' }}</p>@endif
                    </div>
                </div>
            </div>

            {{-- Each backorder item --}}
            @foreach($selectedOrder->backorders as $bo)
            @php
                $currentStock2 = $bo->product?->stock ?? 0;
                $origItemPrice = $bo->orderItem?->price ?? 0;
                $replacePrice  = (float) ($bo->replacement_price ?? 0);
                $priceDiff     = $bo->isReplacement() ? ($origItemPrice - $replacePrice) * $bo->short_qty : 0;
            @endphp
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                {{-- Item header --}}
                <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
                    <div>
                        <p class="font-mono text-xs text-slate-400">{{ $bo->backorder_number }}</p>
                        <p class="font-semibold text-sm text-[#0F172A] {{ $bo->isReplacement() ? 'line-through text-slate-400' : '' }}">
                            {{ $bo->product_name }}
                        </p>
                        @if($bo->isReplacement())
                        <p class="text-xs font-semibold text-orange-700 flex items-center gap-1 mt-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            {{ $bo->replacementProduct?->name }}
                        </p>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $bo->statusBadgeClass() }}">
                        {{ $bo->statusLabel() }}
                    </span>
                </div>

                {{-- Qty row --}}
                <div class="grid grid-cols-3 divide-x divide-slate-100 px-4 py-3">
                    <div class="text-center pr-3">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Ordered</p>
                        <p class="font-bold text-lg text-slate-700">{{ $bo->ordered_qty }}</p>
                    </div>
                    <div class="text-center px-3">
                        <p class="text-[10px] text-green-500 uppercase tracking-wide">Shipped</p>
                        <p class="font-bold text-lg text-green-600">{{ $bo->available_qty }}</p>
                    </div>
                    <div class="text-center pl-3">
                        <p class="text-[10px] text-amber-500 uppercase tracking-wide">Short (BO)</p>
                        <p class="font-bold text-lg text-amber-600">{{ $bo->short_qty }}</p>
                    </div>
                </div>

                {{-- Price diff if replacement --}}
                @if($bo->isReplacement() && $priceDiff !== 0.0)
                <div class="px-4 py-2.5 border-t border-slate-100 {{ $priceDiff > 0 ? 'bg-teal-50' : 'bg-amber-50' }}">
                    <p class="text-xs font-semibold {{ $priceDiff > 0 ? 'text-teal-700' : 'text-amber-700' }}">
                        @if($priceDiff > 0)
                            Refund LKR {{ number_format($priceDiff, 2) }} (replacement is cheaper)
                        @else
                            Extra cost LKR {{ number_format(abs($priceDiff), 2) }} (replacement is more expensive)
                        @endif
                    </p>
                </div>
                @endif

                {{-- Action row --}}
                <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100 bg-white">
                    <div class="text-xs text-slate-400">
                        {{ $bo->created_at->timezone('Asia/Colombo')->format('d M Y') }}
                        @if($bo->dispatcher) · Dispatched by {{ $bo->dispatcher->name }}@endif
                    </div>
                     <div class="flex items-center gap-2">
                        @if($bo->isActive())
                        <button wire:click="openReplaceModal({{ $bo->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-orange-200 bg-orange-50 text-orange-700 text-xs font-semibold hover:bg-orange-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Replace
                        </button>
                        <button wire:click="openRefundModal({{ $bo->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Refund
                        </button>
                        <button wire:click="cancelBackorder({{ $bo->id }})"
                                wire:confirm="Cancel this backorder?"
                                class="inline-flex items-center px-2 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-all">
                            Cancel
                        </button>
                        @endif
                        <span class="text-[10px] text-slate-400 italic">Process via Shipments</span>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="shrink-0 border-t border-slate-100 px-6 py-4 bg-[#F8FAFC]">
            <button wire:click="closeDetail"
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-all">
                Close
            </button>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         DISPATCH MODAL
    ══════════════════════════════════════════════════════════════ --}}
    @if($showDispatchModal)
    <div x-show="dispatchOpen" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50" style="display:none;"></div>
    <div x-show="dispatchOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;"
         @click.self="dispatchOpen = false; $wire.set('showDispatchModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-[#0F172A] text-base">Dispatch Backorder</h3>
                        <p class="text-xs text-slate-400">Confirm shipment to customer</p>
                    </div>
                </div>
                <button wire:click="$set('showDispatchModal', false)" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @php
                    $dispatchBo = $dispatchBoId ? \App\Models\OrderBackorder::with(['order','product','replacementProduct'])->find($dispatchBoId) : null;
                    $isRepDisp  = $dispatchBo?->isReplacement();
                    $stockProd  = $isRepDisp ? $dispatchBo->replacementProduct : $dispatchBo?->product;
                    $dispName   = $isRepDisp ? ($dispatchBo->replacementProduct?->name ?? 'Replacement') : $dispatchBo?->product_name;
                @endphp
                @if($dispatchBo)
                @if($isRepDisp)
                <div class="flex items-center gap-2.5 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <p class="text-xs font-bold text-orange-700">Replacement: sending <strong>{{ $dispatchBo->replacementProduct?->name }}</strong></p>
                </div>
                @endif
                <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs">Backorder #</span>
                        <span class="font-mono font-bold text-xs">{{ $dispatchBo->backorder_number }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs">Sending</span>
                        <span class="font-semibold text-xs {{ $isRepDisp ? 'text-orange-700' : 'text-[#0F172A]' }}">{{ $dispName }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs">Qty</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold">
                            {{ $dispatchBo->short_qty }} units
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs">For Order</span>
                        <span class="font-mono text-blue-600 text-xs font-semibold">{{ $dispatchBo->order?->order_number }}</span>
                    </div>
                    @if($stockProd)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs">Stock</span>
                        <span class="font-semibold text-xs {{ $stockProd->stock >= $dispatchBo->short_qty ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stockProd->stock }} units
                        </span>
                    </div>
                    @endif
                </div>
                <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-700 font-medium">
                        {{ $dispatchBo->short_qty }} unit(s) of "{{ $dispName }}" will be deducted from stock.
                    </p>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dispatch Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea wire:model="dispatchNotes" rows="2"
                              placeholder="e.g. Sent via DHL, tracking #XYZ..."
                              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-violet-500 placeholder-slate-400"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-[#F8FAFC] rounded-b-2xl">
                <button wire:click="$set('showDispatchModal', false)"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-all">
                    Cancel
                </button>
                <button wire:click="confirmDispatch" wire:loading.attr="disabled" wire:target="confirmDispatch"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-all shadow-md shadow-violet-600/25 disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmDispatch">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Confirm Dispatch
                    </span>
                    <span wire:loading wire:target="confirmDispatch">Dispatching...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         REPLACE MODAL
    ══════════════════════════════════════════════════════════════ --}}
    @if($showReplaceModal)
    <div x-show="replaceOpen" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50" style="display:none;"></div>
    <div x-show="replaceOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;"
         @click.self="replaceOpen = false; $wire.set('showReplaceModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-[#0F172A] text-base">Replace Product</h3>
                        <p class="text-xs text-slate-400">Select a substitute to send instead</p>
                    </div>
                </div>
                <button wire:click="$set('showReplaceModal', false)" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @php $replaceBo = $replacingBoId ? \App\Models\OrderBackorder::with('product')->find($replacingBoId) : null; @endphp
                @if($replaceBo)
                <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="min-w-0">
                        <p class="text-[10px] text-slate-400 uppercase font-semibold">Replacing (out of stock)</p>
                        <p class="font-bold text-sm text-slate-700">{{ $replaceBo->product_name }}</p>
                        <p class="text-xs text-slate-400">Short: {{ $replaceBo->short_qty }} unit(s)</p>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2.5 focus-within:ring-2 focus-within:ring-orange-400 focus-within:border-transparent transition-all">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.400ms="replacementProductSearch" type="text"
                           placeholder="Search product name or SKU (min 2 chars)..."
                           class="bg-transparent text-sm outline-none flex-1 placeholder-slate-400">
                </div>
                @error('selectedReplacementId')
                <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs text-red-700 font-medium">{{ $message }}</p>
                </div>
                @enderror
                <div class="max-h-52 overflow-y-auto space-y-1.5">
                    @if(strlen($replacementProductSearch) >= 2)
                        @forelse($replacementProducts as $rp)
                        <button wire:click="selectReplacement({{ $rp->id }})" type="button"
                                class="w-full flex items-center justify-between px-3.5 py-3 rounded-xl border text-left transition-all
                                    {{ $selectedReplacementId === $rp->id ? 'border-orange-400 bg-orange-50 shadow-sm' : 'border-slate-200 bg-white hover:border-orange-300 hover:bg-orange-50/50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center transition-all
                                    {{ $selectedReplacementId === $rp->id ? 'border-orange-500 bg-orange-500' : 'border-slate-300' }}">
                                    @if($selectedReplacementId === $rp->id)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-[#0F172A]">{{ $rp->name }}</p>
                                    @if($rp->sku)<p class="text-[11px] text-slate-400 font-mono">{{ $rp->sku }}</p>@endif
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-3">
                                <p class="font-bold text-sm">LKR {{ number_format($rp->price, 2) }}</p>
                                <p class="text-[11px] {{ $rp->stock >= ($replaceBo?->short_qty ?? 1) ? 'text-green-600' : 'text-red-500' }} font-semibold">
                                    {{ $rp->stock }} in stock
                                </p>
                            </div>
                        </button>
                        @empty
                        <p class="text-center py-5 text-xs text-slate-400">No products matching "{{ $replacementProductSearch }}"</p>
                        @endforelse
                    @else
                    <p class="text-center py-5 text-xs text-slate-400">Type at least 2 characters to search</p>
                    @endif
                </div>

                @if($selectedReplacementId)
                <div class="pt-2 animate-in fade-in slide-in-from-top-2 duration-300">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Specified Size <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input wire:model="replacementSize" type="text"
                           placeholder="e.g. M, L, XL, 42..."
                           class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder-slate-400">
                </div>
                @endif
                {{-- ── Live price difference alert ───────────────────── --}}
                @if($selectedReplacementId && $selectedReplacementPrice > 0 && $originalItemPrice > 0)
                @php
                    $totalOrig    = round($originalItemPrice * $replacingQty, 2);
                    $totalNew     = round($selectedReplacementPrice * $replacingQty, 2);
                    $diffAmount   = round($totalNew - $totalOrig, 2);
                @endphp
                @if($diffAmount > 0)
                <div class="flex items-start gap-3 p-3.5 bg-orange-50 border border-orange-300 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-orange-800">Replacement is more expensive</p>
                        <p class="text-xs text-orange-700 mt-0.5">
                            Original: <strong>LKR {{ number_format($totalOrig, 2) }}</strong>
                            &rarr; Replacement: <strong>LKR {{ number_format($totalNew, 2) }}</strong>
                        </p>
                        <p class="text-xs font-bold text-orange-800 mt-1">
                            +LKR {{ number_format($diffAmount, 2) }} will be added to the customer's balance due.
                        </p>
                    </div>
                </div>
                @elseif($diffAmount < 0)
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-300 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-blue-800">Replacement is cheaper</p>
                        <p class="text-xs text-blue-700 mt-0.5">
                            Original: <strong>LKR {{ number_format($totalOrig, 2) }}</strong>
                            &rarr; Replacement: <strong>LKR {{ number_format($totalNew, 2) }}</strong>
                        </p>
                        <p class="text-xs font-bold text-blue-800 mt-1">
                            LKR {{ number_format(abs($diffAmount), 2) }} will be deducted from order total.
                            If customer already paid in full, a refund will be required.
                        </p>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-xs font-semibold text-green-700">Same price — no adjustment needed.</p>
                </div>
                @endif
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea wire:model="replaceNotes" rows="2"
                              placeholder="e.g. Customer agreed to Product 3..."
                              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder-slate-400"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-[#F8FAFC] rounded-b-2xl">
                <button wire:click="$set('showReplaceModal', false)"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-all">
                    Cancel
                </button>
                <button wire:click="confirmReplacement" wire:loading.attr="disabled" wire:target="confirmReplacement"
                        @if(!$selectedReplacementId) disabled @endif
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-md disabled:opacity-50
                            {{ $selectedReplacementId ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-orange-500/25' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="confirmReplacement">Confirm Replacement</span>
                    <span wire:loading wire:target="confirmReplacement">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         REFUND MODAL
    ══════════════════════════════════════════════════════════════ --}}
    @if($showRefundModal)
    <div x-data="{ open: @entangle('showRefundModal') }"
         x-init="$watch('open', val => { if(val) document.body.classList.add('overflow-hidden'); else document.body.classList.remove('overflow-hidden'); })"
         x-show="open"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-slate-950/50 backdrop-blur-sm" @click="open = false; $wire.set('showRefundModal', false)"></div>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block w-full max-w-lg my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl">
                
                <div class="bg-white px-8 pt-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 font-[Poppins]">Record Item Refund</h3>
                            <p class="text-sm text-slate-500">Refund payment for out-of-stock product</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 space-y-5 bg-white">
                    {{-- Amount --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 label-required mb-1.5">Refund Amount (LKR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium">Rs.</span>
                            <input type="number" wire:model="refundAmount" 
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 focus:ring-red-500 focus:border-red-500 bg-slate-50 font-bold text-lg" 
                                   placeholder="0.00">
                        </div>
                        @error('refundAmount') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Method --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Refund Method</label>
                            <select wire:model="refundMethod" class="w-full rounded-xl border-slate-200 focus:ring-red-500 focus:border-red-500 bg-slate-50">
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online Payment</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        {{-- Account (optional) --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Customer Bank Detail</label>
                            <input type="text" wire:model="customerBankAccount" class="w-full rounded-xl border-slate-200 focus:ring-red-500 focus:border-red-500 bg-slate-50" placeholder="Acc/Bank info">
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Internal Notes</label>
                        <textarea wire:model="refundNotes" rows="2" class="w-full rounded-xl border-slate-200 focus:ring-red-500 focus:border-red-500 bg-slate-50"></textarea>
                    </div>

                    <div class="rounded-xl bg-orange-50 p-3 border border-orange-100 flex gap-3">
                        <svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-orange-800 leading-relaxed font-medium">
                            Proceeding will mark this item as refunded, update the order totals, and close this backorder.
                        </p>
                    </div>
                </div>

                <div class="px-8 py-5 bg-slate-50 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button wire:click="$set('showRefundModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-all">
                        Cancel
                    </button>
                    <button wire:click="confirmRefund" class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition-all shadow-md shadow-red-600/25">
                        Confirm Refund
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>
