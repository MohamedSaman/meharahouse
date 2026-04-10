{{-- resources/views/livewire/admin/backorder.blade.php --}}
<div
    class="space-y-5"
    x-data="{
        detailOpen: @entangle('showDetail'),
        dispatchOpen: @entangle('showDispatchModal'),
        replaceOpen: @entangle('showReplaceModal'),
    }"
>

    {{-- ══════════════════════ FLASH MESSAGES ══════════════════════ --}}
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
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ══════════════════════ HEADER ══════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Back Orders</h2>
            <p class="text-sm text-[#64748B]">
                {{ $stats['active'] }} active backorder{{ $stats['active'] !== 1 ? 's' : '' }} awaiting fulfillment
            </p>
        </div>
        {{-- Stat chips --}}
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="$set('filterStatus', 'pending')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'pending' ? 'bg-amber-500 text-white border-amber-600 shadow-md shadow-amber-500/25' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400 {{ $stats['pending'] > 0 ? 'animate-pulse' : '' }}"></span>
                Pending <span class="font-bold">{{ $stats['pending'] }}</span>
            </button>
            <button wire:click="$set('filterStatus', 'repurchasing')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'repurchasing' ? 'bg-blue-500 text-white border-blue-600 shadow-md shadow-blue-500/25' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' }}">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                Repurchasing <span class="font-bold">{{ $stats['repurchasing'] }}</span>
            </button>
            <button wire:click="$set('filterStatus', 'ready')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'ready' ? 'bg-violet-500 text-white border-violet-600 shadow-md shadow-violet-500/25' : 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100' }}">
                <span class="w-2 h-2 rounded-full bg-violet-400 {{ $stats['ready'] > 0 ? 'animate-pulse' : '' }}"></span>
                Ready <span class="font-bold">{{ $stats['ready'] }}</span>
            </button>
            <button wire:click="$set('filterStatus', 'dispatched')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all
                        {{ $filterStatus === 'dispatched' ? 'bg-indigo-500 text-white border-indigo-600 shadow-md shadow-indigo-500/25' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                Dispatched <span class="font-bold">{{ $stats['dispatched'] }}</span>
            </button>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600">
                Active Total <span class="font-bold text-slate-800">{{ $stats['active'] }}</span>
            </div>
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

    {{-- ══════════════════════ FILTERS BAR ══════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
                <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search by backorder #, product, order..."
                       class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
                @if($search)
                <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>

            {{-- Status filter --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium shrink-0">Status:</span>
                <select wire:model.live="filterStatus"
                        class="text-sm border border-slate-200 rounded-lg px-3 py-2 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="repurchasing">Repurchasing</option>
                    <option value="ready">Ready to Dispatch</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="delivered">Delivered</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            {{-- Loading indicator --}}
            <div wire:loading.flex class="items-center gap-2 text-xs text-slate-400">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Loading...
            </div>
        </div>
    </div>

    {{-- ══════════════════════ TABLE ══════════════════════ --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">BO #</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Original Order</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Customer</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Product</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Short Qty</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Created</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-[#64748B] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F5F9]">
                    @forelse($backorders as $bo)
                    <tr wire:key="bo-{{ $bo->id }}" class="hover:bg-[#F8FAFC] transition-colors duration-150">

                        {{-- BO # --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono font-bold text-[#0F172A] text-xs tracking-tight">
                                    {{ $bo->backorder_number ?? 'BO-???-' . str_pad($bo->id, 3, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex w-fit items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border {{ $bo->statusBadgeClass() }}">
                                    {{ $bo->statusLabel() }}
                                </span>
                            </div>
                        </td>

                        {{-- Original Order --}}
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.orders') }}"
                               class="font-mono font-semibold text-blue-600 hover:text-blue-800 hover:underline text-xs">
                                {{ $bo->order?->order_number ?? 'N/A' }}
                            </a>
                        </td>

                        {{-- Customer --}}
                        <td class="px-5 py-4">
                            @php
                                $addr = $bo->order?->shipping_address ?? [];
                                $custName  = $bo->order?->user?->name ?? ($addr['full_name'] ?? 'Guest');
                                $custEmail = $bo->order?->user?->email ?? ($addr['email'] ?? '');
                            @endphp
                            <div>
                                <p class="font-semibold text-[#0F172A] text-xs">{{ $custName }}</p>
                                @if($custEmail)
                                <p class="text-[#94A3B8] text-[11px] mt-0.5 truncate max-w-[140px]">{{ $custEmail }}</p>
                                @endif
                            </div>
                        </td>

                        {{-- Product --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#0F172A] font-medium text-xs {{ $bo->isReplacement() ? 'line-through text-slate-400' : '' }}">{{ $bo->product_name }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        ×{{ $bo->short_qty }}
                                    </span>
                                </div>
                                @if($bo->isReplacement())
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <span class="text-[11px] font-semibold text-orange-700">{{ $bo->replacementProduct?->name ?? 'Replacement' }}</span>
                                </div>
                                @endif
                            </div>
                        </td>

                        {{-- Short Qty --}}
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 border border-amber-200 text-amber-700 text-xs font-bold">
                                {{ $bo->short_qty }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $bo->statusBadgeClass() }}">
                                {{ $bo->statusLabel() }}
                            </span>
                        </td>

                        {{-- Created --}}
                        <td class="px-5 py-4">
                            <span class="text-[#64748B] text-xs" title="{{ $bo->created_at->format('d M Y, H:i') }}">
                                {{ $bo->created_at->diffForHumans() }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Context-sensitive primary action --}}
                                @if($bo->status === 'ready')
                                <button wire:click="openDispatch({{ $bo->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="openDispatch({{ $bo->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition-all hover:-translate-y-0.5 shadow-sm shadow-violet-600/25 disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Dispatch
                                </button>
                                @elseif($bo->status === 'dispatched')
                                <button wire:click="markDelivered({{ $bo->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="markDelivered({{ $bo->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition-all hover:-translate-y-0.5 shadow-sm shadow-teal-600/25 disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Delivered
                                </button>
                                @elseif($bo->status === 'delivered')
                                <button wire:click="markCompleted({{ $bo->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="markCompleted({{ $bo->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition-all hover:-translate-y-0.5 shadow-sm shadow-green-600/25 disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Complete
                                </button>
                                @elseif(in_array($bo->status, ['pending', 'repurchasing']))
                                    @php $currentStock = $bo->product?->stock ?? 0; @endphp
                                    @if($currentStock >= $bo->short_qty)
                                    <button wire:click="markReady({{ $bo->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="markReady({{ $bo->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition-all hover:-translate-y-0.5 shadow-sm shadow-violet-600/25 disabled:opacity-50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Stock Arrived — Mark Ready
                                    </button>
                                    @else
                                    <div class="flex flex-col items-end gap-1.5">
                                        <span class="text-xs text-slate-400 italic">
                                            Awaiting stock ({{ $currentStock }}/{{ $bo->short_qty }})
                                        </span>
                                        <button wire:click="openReplaceModal({{ $bo->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="openReplaceModal({{ $bo->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-300 text-[11px] font-semibold transition-all disabled:opacity-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                            Replace Product
                                        </button>
                                    </div>
                                    @endif
                                @endif

                                {{-- Details button --}}
                                <button wire:click="viewBackorder({{ $bo->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="viewBackorder({{ $bo->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-700 text-sm">No backorders found</p>
                                    <p class="text-slate-400 text-xs mt-1">
                                        @if($search || $filterStatus)
                                            Try adjusting your filters
                                        @else
                                            Backorders appear here when stock runs short on confirmed orders
                                        @endif
                                    </p>
                                </div>
                                @if($search || $filterStatus)
                                <button wire:click="$set('search', ''); $set('filterStatus', '')"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                                    Clear filters
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($backorders->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">
            {{ $backorders->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         BACKORDER DETAIL SLIDE-OVER PANEL
    ══════════════════════════════════════════════════════════════ --}}
    @if($selectedBackorder)
    {{-- Backdrop --}}
    <div
        x-show="detailOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-40"
        style="display:none;"
        wire:ignore.self
        @click="detailOpen = false; $wire.closeDetail()"
    ></div>

    {{-- Panel --}}
    <div
        x-show="detailOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-0 right-0 h-screen w-full max-w-2xl bg-white shadow-2xl z-50 flex flex-col"
        style="display:none;"
        @click.stop
    >
        {{-- Panel Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-[#0F172A] shrink-0">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="font-[Poppins] font-bold text-white text-base">
                        {{ $selectedBackorder->backorder_number ?? 'Backorder #' . $selectedBackorder->id }}
                    </h3>
                    <p class="text-xs text-slate-400">Created {{ $selectedBackorder->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $selectedBackorder->statusBadgeClass() }}">
                    {{ $selectedBackorder->statusLabel() }}
                </span>
            </div>
            <button wire:click="closeDetail" class="p-2 rounded-xl text-slate-400 hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Panel Scrollable Body --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-5">

            {{-- ── Section 1: Original Order Info ── --}}
            <div class="bg-[#F8FAFC] rounded-2xl p-4 space-y-3">
                <h4 class="font-semibold text-sm text-[#0F172A] flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Original Order
                </h4>

                @php
                    $order = $selectedBackorder->order;
                    $addr  = $order?->shipping_address ?? [];
                    $custName  = $order?->user?->name  ?? ($addr['full_name'] ?? 'Guest');
                    $custEmail = $order?->user?->email ?? ($addr['email'] ?? '');
                    $custPhone = $addr['phone'] ?? ($order?->user?->phone ?? '');
                @endphp

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[#64748B] text-xs font-semibold uppercase tracking-wide">Order #</p>
                        <a href="{{ route('admin.orders') }}"
                           class="font-mono font-bold text-blue-600 hover:underline mt-0.5 block">
                            {{ $order?->order_number ?? 'N/A' }}
                        </a>
                        <p class="text-[#64748B] text-xs mt-0.5">{{ $order?->created_at?->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[#64748B] text-xs font-semibold uppercase tracking-wide">Order Status</p>
                        @if($order)
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                        </span>
                        @else
                        <span class="text-slate-400 text-xs">—</span>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-3 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[#64748B] text-xs font-semibold uppercase tracking-wide">Customer</p>
                        <p class="font-semibold text-[#0F172A] mt-0.5">{{ $custName }}</p>
                        @if($custEmail)
                        <p class="text-[#64748B] text-xs mt-0.5">{{ $custEmail }}</p>
                        @endif
                        @if($custPhone)
                        <p class="text-[#64748B] text-xs mt-0.5">{{ $custPhone }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[#64748B] text-xs font-semibold uppercase tracking-wide">Shipping Address</p>
                        <p class="font-semibold text-[#0F172A] mt-0.5">{{ $addr['full_name'] ?? $custName }}</p>
                        <p class="text-[#64748B] text-xs mt-0.5">{{ $addr['address'] ?? '—' }}</p>
                        <p class="text-[#64748B] text-xs">
                            {{ $addr['city'] ?? '' }}{{ isset($addr['region']) ? ', ' . $addr['region'] : '' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Backorder Details ── --}}
            <div class="bg-[#F8FAFC] rounded-2xl p-4 space-y-3">
                <h4 class="font-semibold text-sm text-[#0F172A] flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Backorder Details
                </h4>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-200">
                        <span class="text-[#64748B] text-xs font-medium">Product</span>
                        <span class="font-semibold text-[#0F172A] text-xs">{{ $selectedBackorder->product_name }}</span>
                    </div>
                    @if($selectedBackorder->product?->sku)
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-200">
                        <span class="text-[#64748B] text-xs font-medium">SKU</span>
                        <span class="font-mono text-xs text-slate-600">{{ $selectedBackorder->product->sku }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-200">
                        <span class="text-[#64748B] text-xs font-medium">Decision</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold
                            {{ $selectedBackorder->decision === 'repurchase' ? 'bg-blue-100 text-blue-700 border border-blue-200'
                             : ($selectedBackorder->decision === 'replace' ? 'bg-orange-100 text-orange-700 border border-orange-200'
                             : 'bg-amber-100 text-amber-700 border border-amber-200') }}">
                            {{ $selectedBackorder->decisionLabel() }}
                        </span>
                    </div>
                </div>

                {{-- Qty pills --}}
                <div class="grid grid-cols-3 gap-3 mt-2">
                    <div class="text-center p-3 rounded-xl bg-white border border-slate-200">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wide">Ordered</p>
                        <p class="text-2xl font-bold text-[#0F172A] mt-1">{{ $selectedBackorder->ordered_qty }}</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-white border border-slate-200">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wide">Available</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $selectedBackorder->available_qty }}</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="text-[10px] text-amber-600 font-semibold uppercase tracking-wide">Short (BO)</p>
                        <p class="text-2xl font-bold text-amber-700 mt-1">{{ $selectedBackorder->short_qty }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs text-slate-600 pt-1">
                    <div>
                        <span class="text-slate-400 font-medium">Created by: </span>
                        {{ $selectedBackorder->creator?->name ?? 'System' }}
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Created: </span>
                        {{ $selectedBackorder->created_at->format('d M Y, H:i') }}
                    </div>
                    @if($selectedBackorder->dispatcher)
                    <div>
                        <span class="text-slate-400 font-medium">Dispatched by: </span>
                        {{ $selectedBackorder->dispatcher->name }}
                    </div>
                    @endif
                    @if($selectedBackorder->notes)
                    <div class="col-span-2">
                        <span class="text-slate-400 font-medium">Notes: </span>
                        {{ $selectedBackorder->notes }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── Section 2b: Replacement Info (only when decision=replace) ── --}}
            @if($selectedBackorder->isReplacement())
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4 space-y-3">
                <h4 class="font-semibold text-sm text-orange-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Product Replacement
                </h4>

                {{-- Original vs Replacement --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white border border-orange-200 rounded-xl p-3 text-center">
                        <p class="text-[10px] text-orange-500 font-semibold uppercase tracking-wide mb-1">Original</p>
                        <p class="text-xs font-semibold text-slate-500 line-through">{{ $selectedBackorder->product_name }}</p>
                        @if($selectedBackorder->orderItem)
                        <p class="text-xs text-slate-400 mt-0.5">LKR {{ number_format($selectedBackorder->orderItem->price, 2) }}</p>
                        @endif
                    </div>
                    <div class="bg-orange-100 border border-orange-300 rounded-xl p-3 text-center">
                        <p class="text-[10px] text-orange-600 font-semibold uppercase tracking-wide mb-1">Replacement</p>
                        <p class="text-xs font-bold text-orange-800">{{ $selectedBackorder->replacementProduct?->name ?? '—' }}</p>
                        @if($selectedBackorder->replacement_price)
                        <p class="text-xs text-orange-600 mt-0.5">LKR {{ number_format($selectedBackorder->replacement_price, 2) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Price difference notice --}}
                @if($selectedBackorder->replacementProduct && $selectedBackorder->orderItem)
                @php
                    $origPrice    = (float) $selectedBackorder->orderItem->price;
                    $replacePrice = (float) $selectedBackorder->replacement_price;
                    $diff         = ($origPrice - $replacePrice) * $selectedBackorder->short_qty;
                @endphp
                @if($diff > 0)
                <div class="flex items-start gap-2.5 p-3 bg-teal-50 border border-teal-200 rounded-xl">
                    <svg class="w-4 h-4 text-teal-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-teal-700">Refund Required: LKR {{ number_format($diff, 2) }}</p>
                        <p class="text-[11px] text-teal-600 mt-0.5">Replacement is cheaper — issue partial refund to customer.</p>
                    </div>
                </div>
                @elseif($diff < 0)
                <div class="flex items-start gap-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-amber-700">Price Difference: LKR {{ number_format(abs($diff), 2) }}</p>
                        <p class="text-[11px] text-amber-600 mt-0.5">Replacement is more expensive — absorb cost or request extra payment from customer.</p>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs font-semibold text-green-700">Same price — no refund or extra charge needed.</p>
                </div>
                @endif
                @endif

                @if($selectedBackorder->replacement_notes)
                <div class="text-xs text-orange-700 bg-white border border-orange-200 rounded-xl p-2.5">
                    <span class="font-semibold">Note: </span>{{ $selectedBackorder->replacement_notes }}
                </div>
                @endif
            </div>
            @endif

            {{-- ── Section 3: Timeline ── --}}
            <div class="bg-[#F8FAFC] rounded-2xl p-4 space-y-3">
                <h4 class="font-semibold text-sm text-[#0F172A] flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Timeline
                </h4>

                @php
                    $timelineSteps = [
                        ['key' => 'pending',      'label' => 'Created',     'date' => $selectedBackorder->created_at,    'icon' => 'M12 4v16m8-8H4'],
                        ['key' => 'repurchasing', 'label' => 'Repurchasing','date' => null,                              'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        ['key' => 'ready',        'label' => 'Ready',       'date' => null,                              'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['key' => 'dispatched',   'label' => 'Dispatched',  'date' => $selectedBackorder->dispatched_at, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['key' => 'delivered',    'label' => 'Delivered',   'date' => $selectedBackorder->delivered_at,  'icon' => 'M5 13l4 4L19 7'],
                        ['key' => 'completed',    'label' => 'Completed',   'date' => $selectedBackorder->fulfilled_at,  'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ];

                    $statusOrder = ['pending', 'repurchasing', 'ready', 'dispatched', 'delivered', 'completed', 'cancelled'];
                    $currentIdx  = array_search($selectedBackorder->status, $statusOrder);
                @endphp

                <div class="space-y-0">
                    @foreach($timelineSteps as $stepIdx => $step)
                    @php
                        $stepPos   = array_search($step['key'], $statusOrder);
                        $isDone    = $currentIdx !== false && $stepPos <= $currentIdx && $selectedBackorder->status !== 'cancelled';
                        $isCurrent = $selectedBackorder->status === $step['key'];
                        $isLast    = $stepIdx === count($timelineSteps) - 1;
                    @endphp
                    <div class="flex gap-3 {{ !$isLast ? 'pb-4' : '' }}">
                        {{-- Icon + line --}}
                        <div class="flex flex-col items-center shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all
                                {{ $isDone
                                    ? ($isCurrent ? 'bg-blue-600 border-blue-600 shadow-md shadow-blue-600/25' : 'bg-green-500 border-green-500')
                                    : 'bg-white border-slate-200' }}">
                                <svg class="w-4 h-4 {{ $isDone ? 'text-white' : 'text-slate-300' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="{{ $step['icon'] }}"/>
                                </svg>
                            </div>
                            @if(!$isLast)
                            <div class="w-0.5 flex-1 mt-1 {{ $isDone ? 'bg-green-300' : 'bg-slate-100' }}"></div>
                            @endif
                        </div>
                        {{-- Content --}}
                        <div class="flex-1 pb-0 pt-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold {{ $isDone ? 'text-[#0F172A]' : 'text-slate-400' }}">
                                    {{ $step['label'] }}
                                    @if($isCurrent)
                                    <span class="ml-2 text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Current</span>
                                    @endif
                                </p>
                                @if($step['date'] && $isDone)
                                <span class="text-[11px] text-slate-400">{{ $step['date']->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Cancelled state --}}
                    @if($selectedBackorder->status === 'cancelled')
                    <div class="flex gap-3 pt-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 bg-red-50 border-red-300 shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-red-600">Cancelled</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Panel Footer — Action Buttons --}}
        <div class="shrink-0 border-t border-slate-100 px-6 py-4 bg-[#F8FAFC] flex items-center justify-between gap-3 flex-wrap">
            <button wire:click="closeDetail"
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-all">
                Close
            </button>

            <div class="flex items-center gap-2">
                @if($selectedBackorder->status === 'pending' || $selectedBackorder->status === 'repurchasing')
                <button wire:click="openReplaceModal({{ $selectedBackorder->id }})"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-300 text-sm font-semibold transition-all disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Replace Product
                </button>
                <button wire:click="markReady({{ $selectedBackorder->id }})"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-all shadow-md shadow-violet-600/25 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mark Ready
                </button>
                @elseif($selectedBackorder->status === 'ready')
                <button wire:click="openDispatch({{ $selectedBackorder->id }})"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-all shadow-md shadow-violet-600/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Dispatch Now
                </button>
                @elseif($selectedBackorder->status === 'dispatched')
                <button wire:click="markDelivered({{ $selectedBackorder->id }})"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition-all shadow-md shadow-teal-600/25 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mark Delivered
                </button>
                @elseif($selectedBackorder->status === 'delivered')
                <button wire:click="markCompleted({{ $selectedBackorder->id }})"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-all shadow-md shadow-green-600/25 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mark Completed
                </button>
                @endif

                @if($selectedBackorder->isActive())
                <button wire:click="cancelBackorder({{ $selectedBackorder->id }})"
                        wire:confirm="Are you sure you want to cancel this backorder? This cannot be undone."
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition-all disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         DISPATCH MODAL
    ══════════════════════════════════════════════════════════════ --}}
    @if($showDispatchModal)
    {{-- Backdrop --}}
    <div
        x-show="dispatchOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50"
        style="display:none;"
        wire:ignore.self
    ></div>

    {{-- Modal --}}
    <div
        x-show="dispatchOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none;"
        @click.self="dispatchOpen = false; $wire.set('showDispatchModal', false)"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            {{-- Modal Header --}}
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
                <button wire:click="$set('showDispatchModal', false)"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-4">
                @php
                    $dispatchBo         = $dispatchBoId ? \App\Models\OrderBackorder::with(['order', 'product', 'replacementProduct'])->find($dispatchBoId) : null;
                    $isReplacementDispatch = $dispatchBo?->isReplacement();
                    $stockProduct       = $isReplacementDispatch ? $dispatchBo->replacementProduct : $dispatchBo?->product;
                    $dispatchProductName = $isReplacementDispatch ? ($dispatchBo->replacementProduct?->name ?? 'Replacement') : $dispatchBo?->product_name;
                @endphp
                @if($dispatchBo)
                {{-- Replacement banner --}}
                @if($isReplacementDispatch)
                <div class="flex items-center gap-2.5 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-orange-700">Replacement Dispatch</p>
                        <p class="text-[11px] text-orange-600">Sending <strong>{{ $dispatchBo->replacementProduct?->name }}</strong> instead of original product.</p>
                    </div>
                </div>
                @endif

                {{-- Dispatch summary --}}
                <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">Backorder #</span>
                        <span class="font-mono font-bold text-[#0F172A] text-xs">{{ $dispatchBo->backorder_number }}</span>
                    </div>
                    @if($isReplacementDispatch)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">Original Product</span>
                        <span class="text-slate-400 line-through text-xs">{{ $dispatchBo->product_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">Sending Instead</span>
                        <span class="font-semibold text-orange-700 text-xs">{{ $dispatchBo->replacementProduct?->name }}</span>
                    </div>
                    @else
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">Product</span>
                        <span class="font-semibold text-[#0F172A] text-xs">{{ $dispatchBo->product_name }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">Qty to Dispatch</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold">
                            {{ $dispatchBo->short_qty }} units
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">For Order</span>
                        <span class="font-mono text-blue-600 text-xs font-semibold">{{ $dispatchBo->order?->order_number ?? 'N/A' }}</span>
                    </div>
                    @if($stockProduct)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-medium">Current Stock ({{ $isReplacementDispatch ? 'Replacement' : 'Product' }})</span>
                        <span class="font-semibold text-xs {{ $stockProduct->stock >= $dispatchBo->short_qty ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stockProduct->stock }} units
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Stock deduction warning --}}
                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-700 font-medium">
                        <strong>Stock will be deducted</strong> from <strong>"{{ $dispatchProductName }}"</strong>
                        when you confirm dispatch ({{ $dispatchBo->short_qty }} unit(s)).
                    </p>
                </div>

                @if($stockProduct && $stockProduct->stock < $dispatchBo->short_qty)
                <div class="flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-red-700 font-medium">
                        <strong>Insufficient stock:</strong> Current stock ({{ $stockProduct->stock }}) is less than required ({{ $dispatchBo->short_qty }}). Stock deduction will be skipped.
                    </p>
                </div>
                @endif
                @endif

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Dispatch Notes <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <textarea wire:model="dispatchNotes"
                              rows="3"
                              placeholder="e.g. Sent via DHL, tracking #XYZ123..."
                              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 resize-none focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent placeholder-slate-400"></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-[#F8FAFC] rounded-b-2xl">
                <button wire:click="$set('showDispatchModal', false)"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-all">
                    Cancel
                </button>
                <button wire:click="confirmDispatch"
                        wire:loading.attr="disabled"
                        wire:target="confirmDispatch"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-all shadow-md shadow-violet-600/25 disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmDispatch"
                          class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Confirm Dispatch
                    </span>
                    <span wire:loading wire:target="confirmDispatch"
                          class="flex items-center gap-2" style="display:none;">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Dispatching...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         REPLACE PRODUCT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    @if($showReplaceModal)
    {{-- Backdrop --}}
    <div
        x-show="replaceOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50"
        style="display:none;"
        wire:ignore.self
    ></div>

    {{-- Modal --}}
    <div
        x-show="replaceOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none;"
        @click.self="replaceOpen = false; $wire.set('showReplaceModal', false)"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.stop>
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-[#0F172A] text-base">Replace Product</h3>
                        <p class="text-xs text-slate-400">Select a substitute product to send instead</p>
                    </div>
                </div>
                <button wire:click="$set('showReplaceModal', false)"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-4">
                @php
                    $replaceBo = $replacingBoId ? \App\Models\OrderBackorder::with('product')->find($replacingBoId) : null;
                @endphp

                {{-- Original product info --}}
                @if($replaceBo)
                <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wide">Replacing (Out of Stock)</p>
                        <p class="text-sm font-bold text-slate-700">{{ $replaceBo->product_name }}</p>
                        <p class="text-[11px] text-slate-400">Short qty: {{ $replaceBo->short_qty }} unit(s)</p>
                    </div>
                </div>
                @endif

                {{-- Search box --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Search Replacement Product</label>
                    <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2.5 focus-within:ring-2 focus-within:ring-orange-400 focus-within:border-transparent transition-all">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input wire:model.live.debounce.400ms="replacementProductSearch"
                               type="text"
                               placeholder="Type product name or SKU (min 2 chars)..."
                               class="bg-transparent text-sm outline-none flex-1 placeholder-slate-400">
                        @if($replacementProductSearch)
                        <button wire:click="$set('replacementProductSearch', '')" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Validation error --}}
                @error('selectedReplacementId')
                <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-red-700 font-medium">{{ $message }}</p>
                </div>
                @enderror

                {{-- Product list --}}
                <div class="max-h-52 overflow-y-auto space-y-1.5 pr-1">
                    @if(strlen($replacementProductSearch) >= 2)
                        @forelse($replacementProducts as $rp)
                        <button wire:click="selectReplacement({{ $rp->id }})"
                                type="button"
                                class="w-full flex items-center justify-between px-3.5 py-3 rounded-xl border text-left transition-all
                                    {{ $selectedReplacementId === $rp->id
                                        ? 'border-orange-400 bg-orange-50 shadow-sm shadow-orange-400/20'
                                        : 'border-slate-200 bg-white hover:border-orange-300 hover:bg-orange-50/50' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Selected indicator --}}
                                <div class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center transition-all
                                    {{ $selectedReplacementId === $rp->id ? 'border-orange-500 bg-orange-500' : 'border-slate-300' }}">
                                    @if($selectedReplacementId === $rp->id)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-[#0F172A] truncate">{{ $rp->name }}</p>
                                    @if($rp->sku)
                                    <p class="text-[11px] text-slate-400 font-mono">{{ $rp->sku }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-3">
                                <p class="font-bold text-sm text-[#0F172A]">LKR {{ number_format($rp->price, 2) }}</p>
                                <p class="text-[11px] {{ $rp->stock >= ($replaceBo?->short_qty ?? 1) ? 'text-green-600' : 'text-red-500' }} font-semibold">
                                    {{ $rp->stock }} in stock
                                </p>
                            </div>
                        </button>
                        @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            No products found matching "{{ $replacementProductSearch }}"
                        </div>
                        @endforelse
                    @elseif(strlen($replacementProductSearch) > 0)
                    <div class="text-center py-4 text-slate-400 text-xs">
                        Type at least 2 characters to search...
                    </div>
                    @else
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Start typing to find a replacement product
                    </div>
                    @endif
                </div>

                {{-- Price comparison (shown once product is selected) --}}
                @if($selectedReplacementId && $replaceBo)
                @php
                    $selProduct = $replacementProducts->firstWhere('id', $selectedReplacementId)
                                  ?? \App\Models\Product::find($selectedReplacementId);
                    $origItemPrice = $replaceBo->orderItem?->price ?? 0;
                    $selPrice      = $selProduct?->price ?? 0;
                    $priceDiff     = ((float)$origItemPrice - (float)$selPrice) * $replaceBo->short_qty;
                @endphp
                @if($selProduct)
                <div class="p-3.5 border border-orange-200 bg-orange-50 rounded-xl text-xs space-y-1.5">
                    <div class="flex items-center justify-between text-orange-700">
                        <span>Original price/unit:</span>
                        <span class="font-semibold line-through text-slate-400">LKR {{ number_format($origItemPrice, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-orange-700">
                        <span>Replacement price/unit:</span>
                        <span class="font-bold">LKR {{ number_format($selPrice, 2) }}</span>
                    </div>
                    <div class="border-t border-orange-200 pt-1.5 flex items-center justify-between font-bold">
                        <span class="text-orange-800">Price difference (×{{ $replaceBo->short_qty }}):</span>
                        @if($priceDiff > 0)
                        <span class="text-teal-600">Refund LKR {{ number_format($priceDiff, 2) }}</span>
                        @elseif($priceDiff < 0)
                        <span class="text-amber-700">+LKR {{ number_format(abs($priceDiff), 2) }} extra cost</span>
                        @else
                        <span class="text-green-600">No difference</span>
                        @endif
                    </div>
                </div>
                @endif
                @endif

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Notes <span class="text-slate-400 font-normal">(optional — e.g. "Customer agreed to Product 3")</span>
                    </label>
                    <textarea wire:model="replaceNotes"
                              rows="2"
                              placeholder="Note about replacement agreement..."
                              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 resize-none focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent placeholder-slate-400"></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-[#F8FAFC] rounded-b-2xl">
                <button wire:click="$set('showReplaceModal', false)"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-all">
                    Cancel
                </button>
                <button wire:click="confirmReplacement"
                        wire:loading.attr="disabled"
                        wire:target="confirmReplacement"
                        @if(!$selectedReplacementId) disabled @endif
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-md disabled:opacity-50
                            {{ $selectedReplacementId ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-orange-500/25' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="confirmReplacement"
                          class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Confirm Replacement
                    </span>
                    <span wire:loading wire:target="confirmReplacement"
                          class="flex items-center gap-2" style="display:none;">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
