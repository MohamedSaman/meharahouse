{{-- resources/views/livewire/admin/finance.blade.php --}}
<div
    x-data="{ showCustomDates: @entangle('quickFilter').live === 'custom' }"
    x-init="$watch('$wire.quickFilter', val => showCustomDates = (val === 'custom'))"
    class="space-y-6"
>

    {{-- ══════════════════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-16 -right-12 h-44 w-44 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-10 h-40 w-40 rounded-full bg-teal-400/10 blur-3xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300 mb-1">Admin &rarr; Finance</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-white">Finance Overview</h2>
                <p class="text-slate-400 text-sm mt-1">
                    @if($dateFrom && $dateTo)
                        {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }}
                        &mdash;
                        {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
                    @else
                        All-time financial summary
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                {{-- Loading indicator --}}
                <div wire:loading class="flex items-center gap-2 text-slate-300 text-sm">
                    <span class="inline-block w-4 h-4 border-2 border-slate-500 border-t-amber-400 rounded-full animate-spin"></span>
                    <span>Refreshing…</span>
                </div>
                <a href="{{ route('admin.reports') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Full Reports
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         QUICK FILTER BAR
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

            {{-- Quick preset buttons --}}
            <div class="flex flex-wrap gap-2">
                @foreach([
                    ['label' => 'Today',      'value' => 'today'],
                    ['label' => 'This Week',  'value' => 'this_week'],
                    ['label' => 'This Month', 'value' => 'this_month'],
                    ['label' => 'This Year',  'value' => 'this_year'],
                    ['label' => 'Custom',     'value' => 'custom'],
                ] as $preset)
                <button
                    wire:click="applyQuickFilter('{{ $preset['value'] }}')"
                    @if($preset['value'] === 'custom') x-on:click="showCustomDates = true" @endif
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150
                        {{ $quickFilter === $preset['value']
                            ? 'bg-amber-400 text-slate-900 shadow-sm'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    @if($preset['value'] === 'custom')
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Custom
                        </span>
                    @else
                        {{ $preset['label'] }}
                    @endif
                </button>
                @endforeach
            </div>

            {{-- Divider --}}
            <div class="hidden lg:block h-6 w-px bg-slate-200 shrink-0"></div>

            {{-- Custom date pickers — always visible when custom is selected --}}
            <div x-show="showCustomDates || '{{ $quickFilter }}' === 'custom'" x-cloak
                 class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-500 shrink-0 uppercase tracking-wide">From</label>
                    <input type="date"
                           wire:model.live="dateFrom"
                           class="form-input text-sm py-1.5 w-auto"
                           max="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-500 shrink-0 uppercase tracking-wide">To</label>
                    <input type="date"
                           wire:model.live="dateTo"
                           class="form-input text-sm py-1.5 w-auto"
                           max="{{ now()->format('Y-m-d') }}">
                </div>
            </div>

            {{-- Date range label for non-custom --}}
            @if($dateFrom && $dateTo)
            <p class="text-xs text-slate-400 lg:ml-auto shrink-0">
                Showing:
                <span class="font-semibold text-slate-600">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('M j') }}
                    &ndash;
                    {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
                </span>
            </p>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         6 SUMMARY KPI CARDS  (2 rows × 3)
    ══════════════════════════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-60 pointer-events-none" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 transition-opacity duration-200">

        {{-- 1. Total Sales --}}
        <div class="card p-5 border-l-4 border-green-400 hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l-6-6m6 6H3"/>
                    </svg>
                    {{ number_format($salesOrders->count ?? 0) }} orders
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Total Sales</p>
                <p class="font-[Poppins] font-bold text-2xl text-slate-800">
                    LKR {{ number_format($salesOrders->total ?? 0, 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1">All non-cancelled order totals</p>
            </div>
        </div>

        {{-- 2. Cash Collected --}}
        <div class="card p-5 border-l-4 border-blue-400 hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                    {{ number_format($collected->count ?? 0) }} txns
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Cash Collected</p>
                <p class="font-[Poppins] font-bold text-2xl text-slate-800">
                    LKR {{ number_format($collected->total ?? 0, 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Confirmed payment receipts</p>
            </div>
        </div>

        {{-- 3. Outstanding Due --}}
        @php
            $outstandingTotal = (float) ($outstanding->total ?? 0);
            $outstandingCount = (int) ($outstanding->count ?? 0);
        @endphp
        <div class="card p-5 border-l-4 {{ $outstandingTotal > 0 ? 'border-amber-400' : 'border-slate-300' }} hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl {{ $outstandingTotal > 0 ? 'bg-amber-50' : 'bg-slate-50' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $outstandingTotal > 0 ? 'text-amber-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if($outstandingCount > 0)
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                    {{ $outstandingCount }} unpaid
                </span>
                @else
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full">
                    All clear
                </span>
                @endif
            </div>
            <div class="mt-3">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Outstanding Due</p>
                <p class="font-[Poppins] font-bold text-2xl {{ $outstandingTotal > 0 ? 'text-amber-600' : 'text-slate-800' }}">
                    LKR {{ number_format($outstandingTotal, 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Unpaid balance on pending/partial orders</p>
            </div>
        </div>

        {{-- 4. Total Refunds --}}
        @php $refundTotal = (float) ($refundsData->total ?? 0); @endphp
        <div class="card p-5 border-l-4 {{ $refundTotal > 0 ? 'border-red-400' : 'border-slate-300' }} hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl {{ $refundTotal > 0 ? 'bg-red-50' : 'bg-slate-50' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $refundTotal > 0 ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
                <span class="inline-flex items-center text-xs font-semibold {{ $refundTotal > 0 ? 'text-red-600 bg-red-50' : 'text-slate-400 bg-slate-50' }} px-2 py-0.5 rounded-full">
                    {{ number_format($refundsData->count ?? 0) }} refunds
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Total Refunds</p>
                <p class="font-[Poppins] font-bold text-2xl {{ $refundTotal > 0 ? 'text-red-600' : 'text-slate-800' }}">
                    LKR {{ number_format($refundTotal, 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Processed refunds in period</p>
            </div>
        </div>

        {{-- 5. Supplier + Shipping Costs --}}
        @php
            $supplierTotal  = (float) ($supplierCosts->total ?? 0);
            $shipmentTotal  = (float) ($shipmentCosts->total ?? 0);
            $combinedCosts  = $supplierTotal + $shipmentTotal;
        @endphp
        <div class="card p-5 border-l-4 border-orange-400 hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="inline-flex items-center text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">
                    {{ number_format($supplierCosts->count ?? 0) }} POs
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Supplier + Shipping Costs</p>
                <p class="font-[Poppins] font-bold text-2xl text-orange-600">
                    LKR {{ number_format($combinedCosts, 2) }}
                </p>
                <div class="flex items-center gap-3 mt-1">
                    <p class="text-xs text-slate-400">POs: <span class="font-medium text-slate-600">LKR {{ number_format($supplierTotal, 2) }}</span></p>
                    <span class="text-slate-300">·</span>
                    <p class="text-xs text-slate-400">Courier: <span class="font-medium text-slate-600">LKR {{ number_format($shipmentTotal, 2) }}</span></p>
                </div>
            </div>
        </div>

        {{-- 6. Estimated Profit --}}
        @php $profitPositive = $estimatedProfit >= 0; @endphp
        <div class="card p-5 border-l-4 {{ $profitPositive ? 'border-teal-400' : 'border-red-400' }} hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl {{ $profitPositive ? 'bg-teal-50' : 'bg-red-50' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $profitPositive ? 'text-teal-600' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($profitPositive)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5m0 0v-8m0 8l8-8 4 4 6-6"/>
                        @endif
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $profitPositive ? 'text-teal-600 bg-teal-50' : 'text-red-600 bg-red-50' }} px-2 py-0.5 rounded-full">
                    {{ $profitPositive ? 'Profitable' : 'At a Loss' }}
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">
                    Estimated Profit
                    <span class="ml-1 text-[10px] font-normal text-slate-400 normal-case tracking-normal">(collected - costs)</span>
                </p>
                <p class="font-[Poppins] font-bold text-2xl {{ $profitPositive ? 'text-teal-600' : 'text-red-600' }}">
                    {{ $profitPositive ? '' : '-' }}LKR {{ number_format(abs($estimatedProfit), 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    Based on confirmed payments minus PO + courier + refund costs
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         COLLECTION BREAKDOWN ROW  (3 small cards: advance / balance / full)
    ══════════════════════════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-60 pointer-events-none" class="grid grid-cols-1 sm:grid-cols-3 gap-4 transition-opacity duration-200">
        @php
            $advanceCollected = (float) ($collectedByType->get('advance')->total ?? 0);
            $balanceCollected = (float) ($collectedByType->get('balance')->total ?? 0);
            $fullCollected    = (float) ($collectedByType->get('full')->total ?? 0);
            $advanceCount     = (int)   ($collectedByType->get('advance')->count ?? 0);
            $balanceCount     = (int)   ($collectedByType->get('balance')->count ?? 0);
            $fullCount        = (int)   ($collectedByType->get('full')->count ?? 0);
        @endphp

        {{-- Advance --}}
        <div class="card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Advance Collected</p>
                <p class="font-[Poppins] font-bold text-lg text-slate-800 truncate">LKR {{ number_format($advanceCollected, 2) }}</p>
                <p class="text-xs text-slate-400">{{ $advanceCount }} payments</p>
            </div>
        </div>

        {{-- Balance --}}
        <div class="card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l6 6m-6-6v12"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Balance Collected</p>
                <p class="font-[Poppins] font-bold text-lg text-slate-800 truncate">LKR {{ number_format($balanceCollected, 2) }}</p>
                <p class="text-xs text-slate-400">{{ $balanceCount }} payments</p>
            </div>
        </div>

        {{-- Full --}}
        <div class="card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Full Payment Collected</p>
                <p class="font-[Poppins] font-bold text-lg text-slate-800 truncate">LKR {{ number_format($fullCollected, 2) }}</p>
                <p class="text-xs text-slate-400">{{ $fullCount }} payments</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         PROFIT SUMMARY BAR
    ══════════════════════════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-60 pointer-events-none" class="card p-5 transition-opacity duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="font-[Poppins] font-semibold text-slate-800 text-base">Cash Flow Summary</h3>
                <p class="text-xs text-slate-400 mt-0.5">Inflows vs outflows for the selected period</p>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
                    <span class="text-slate-600 font-medium">Collected</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                    <span class="text-slate-600 font-medium">Costs</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full {{ $profitPositive ? 'bg-teal-400' : 'bg-red-400' }} inline-block"></span>
                    <span class="text-slate-600 font-medium">Profit</span>
                </div>
            </div>
        </div>

        {{-- Bar visualization --}}
        @php
            $barMax    = max($totalCollected, $totalCosts, 1);
            $barCollect = min(100, ($totalCollected / $barMax) * 100);
            $barCosts   = min(100, ($totalCosts   / $barMax) * 100);
            $barProfit  = min(100, (abs($estimatedProfit) / $barMax) * 100);
        @endphp
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-500 w-24 shrink-0 text-right">Collected</span>
                <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden">
                    <div class="h-full bg-blue-400 rounded-full transition-all duration-500"
                         style="width: {{ number_format($barCollect, 1) }}%"></div>
                </div>
                <span class="text-xs font-bold text-slate-700 w-32 shrink-0">LKR {{ number_format($totalCollected, 2) }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-500 w-24 shrink-0 text-right">Total Costs</span>
                <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden">
                    <div class="h-full bg-orange-400 rounded-full transition-all duration-500"
                         style="width: {{ number_format($barCosts, 1) }}%"></div>
                </div>
                <span class="text-xs font-bold text-slate-700 w-32 shrink-0">LKR {{ number_format($totalCosts, 2) }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-500 w-24 shrink-0 text-right">Est. Profit</span>
                <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden">
                    <div class="h-full {{ $profitPositive ? 'bg-teal-400' : 'bg-red-400' }} rounded-full transition-all duration-500"
                         style="width: {{ number_format($barProfit, 1) }}%"></div>
                </div>
                <span class="text-xs font-bold {{ $profitPositive ? 'text-teal-600' : 'text-red-600' }} w-32 shrink-0">
                    {{ $profitPositive ? '' : '-' }}LKR {{ number_format(abs($estimatedProfit), 2) }}
                </span>
            </div>
        </div>

        <p class="text-[11px] text-slate-400 mt-4 italic">
            Disclaimer: "Estimated Profit" uses confirmed payment receipts minus purchase order totals, courier costs, and refunds. It does not account for operational overhead, staff wages, or partial POs not yet invoiced.
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         ORDERS BY STATUS TABLE
    ══════════════════════════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-60 pointer-events-none" class="card overflow-hidden transition-opacity duration-200">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-[Poppins] font-semibold text-slate-800">Orders by Status</h3>
                <p class="text-xs text-slate-400 mt-0.5">Breakdown of all orders placed in the selected period</p>
            </div>
            @if($ordersByStatus->isNotEmpty())
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                {{ $ordersByStatus->sum('count') }} total
            </span>
            @endif
        </div>

        @if($ordersByStatus->isEmpty())
        <div class="px-5 py-12 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm text-slate-400 font-medium">No orders in this period</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="text-center">Count</th>
                        <th class="text-right">Total Value</th>
                        <th class="text-right">Share</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = (float) $ordersByStatus->sum('total'); @endphp
                    @foreach($ordersByStatus as $row)
                    @php
                        $pct = $grandTotal > 0 ? ($row->total / $grandTotal * 100) : 0;
                        [$badgeBg, $badgeText, $dotColor] = match($row->status) {
                            'new'              => ['bg-slate-100',   'text-slate-700',   'bg-slate-400'],
                            'payment_received' => ['bg-amber-100',   'text-amber-700',   'bg-amber-400'],
                            'confirmed'        => ['bg-blue-100',    'text-blue-700',    'bg-blue-500'],
                            'sourcing'         => ['bg-orange-100',  'text-orange-700',  'bg-orange-400'],
                            'dispatched'       => ['bg-indigo-100',  'text-indigo-700',  'bg-indigo-400'],
                            'delivered'        => ['bg-teal-100',    'text-teal-700',    'bg-teal-500'],
                            'completed'        => ['bg-green-100',   'text-green-700',   'bg-green-500'],
                            'refunded'         => ['bg-red-100',     'text-red-700',     'bg-red-400'],
                            'cancelled'        => ['bg-slate-100',   'text-slate-500',   'bg-slate-300'],
                            default            => ['bg-slate-100',   'text-slate-700',   'bg-slate-400'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border
                                {{ $badgeBg }} {{ $badgeText }} border-transparent">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} inline-block"></span>
                                {{ ucwords(str_replace('_', ' ', $row->status)) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="font-semibold text-slate-700">{{ number_format($row->count) }}</span>
                        </td>
                        <td class="text-right font-semibold text-slate-700">
                            LKR {{ number_format($row->total ?? 0, 2) }}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full {{ $dotColor }} rounded-full" style="width: {{ number_format($pct, 1) }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium w-9 text-right">{{ number_format($pct, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200">
                        <td class="font-semibold text-slate-700">Total</td>
                        <td class="text-center font-bold text-slate-700">{{ number_format($ordersByStatus->sum('count')) }}</td>
                        <td class="text-right font-bold text-slate-800">LKR {{ number_format($grandTotal, 2) }}</td>
                        <td class="text-right text-xs font-semibold text-slate-400">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         3-COLUMN BOTTOM SECTION
         Left: Recent Payments | Middle: Recent Refunds | Right: Recent POs
    ══════════════════════════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-60 pointer-events-none"
         class="grid grid-cols-1 lg:grid-cols-3 gap-5 transition-opacity duration-200">

        {{-- ── RECENT PAYMENTS ──────────────────────────────────────── --}}
        <div class="card overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-[Poppins] font-semibold text-slate-800 text-sm">Recent Payments</h3>
                    <p class="text-xs text-slate-400">Confirmed receipts</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            @if($recentPayments->isEmpty())
            <div class="flex-1 flex items-center justify-center py-10 text-center px-5">
                <div>
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-slate-400">No confirmed payments in this period</p>
                </div>
            </div>
            @else
            <div class="divide-y divide-slate-50 flex-1">
                @foreach($recentPayments as $payment)
                @php
                    $typeColor = match($payment->type) {
                        'advance' => 'text-violet-600 bg-violet-50',
                        'balance' => 'text-sky-600 bg-sky-50',
                        'full'    => 'text-emerald-600 bg-emerald-50',
                        'refund'  => 'text-red-600 bg-red-50',
                        default   => 'text-slate-600 bg-slate-100',
                    };
                @endphp
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-bold text-slate-700 truncate">
                                #{{ $payment->order?->id ?? '—' }}
                            </span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $typeColor }}">
                                {{ $payment->type }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            {{ $payment->confirmed_at ? $payment->confirmed_at->timezone('Asia/Colombo')->format('M j, g:i a') : '—' }}
                        </p>
                    </div>
                    <span class="font-[Poppins] font-semibold text-sm text-slate-800 shrink-0">
                        LKR {{ number_format($payment->amount, 2) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
                <a href="{{ route('admin.payments') }}"
                   class="text-xs font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition-colors">
                    View all payments
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── RECENT REFUNDS ───────────────────────────────────────── --}}
        <div class="card overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-[Poppins] font-semibold text-slate-800 text-sm">Recent Refunds</h3>
                    <p class="text-xs text-slate-400">Processed & issued</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
            </div>

            @if($recentRefunds->isEmpty())
            <div class="flex-1 flex items-center justify-center py-10 text-center px-5">
                <div>
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-slate-400">No refunds issued in this period</p>
                </div>
            </div>
            @else
            <div class="divide-y divide-slate-50 flex-1">
                @foreach($recentRefunds as $refund)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-bold text-slate-700 truncate">
                                #{{ $refund->order?->id ?? '—' }}
                            </span>
                            @if($refund->method)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                {{ $refund->method }}
                            </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400">
                            {{ $refund->processed_at
                                ? $refund->processed_at->timezone('Asia/Colombo')->format('M j, g:i a')
                                : $refund->created_at->timezone('Asia/Colombo')->format('M j, g:i a') }}
                        </p>
                    </div>
                    <span class="font-[Poppins] font-semibold text-sm text-red-600 shrink-0">
                        &minus; LKR {{ number_format($refund->amount, 2) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
                <a href="{{ route('admin.orders') }}"
                   class="text-xs font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition-colors">
                    View refunded orders
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── RECENT PURCHASE ORDERS ───────────────────────────────── --}}
        <div class="card overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-[Poppins] font-semibold text-slate-800 text-sm">Recent Purchase Orders</h3>
                    <p class="text-xs text-slate-400">Supplier procurement</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>

            @if($recentPurchaseOrders->isEmpty())
            <div class="flex-1 flex items-center justify-center py-10 text-center px-5">
                <div>
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-xs text-slate-400">No purchase orders in this period</p>
                </div>
            </div>
            @else
            <div class="divide-y divide-slate-50 flex-1">
                @foreach($recentPurchaseOrders as $po)
                @php
                    [$poStatusBg, $poStatusText] = match($po->status) {
                        'ordered'  => ['bg-blue-100',   'text-blue-700'],
                        'partial'  => ['bg-amber-100',  'text-amber-700'],
                        'received' => ['bg-green-100',  'text-green-700'],
                        'draft'    => ['bg-slate-100',  'text-slate-600'],
                        default    => ['bg-slate-100',  'text-slate-600'],
                    };
                @endphp
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-bold text-slate-700 truncate">
                                {{ $po->po_number ?? 'PO #' . $po->id }}
                            </span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $poStatusBg }} {{ $poStatusText }}">
                                {{ $po->status }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate">
                            {{ $po->supplier?->name ?? 'No supplier' }}
                            &middot; {{ $po->created_at->format('M j, Y') }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="font-[Poppins] font-semibold text-sm text-orange-600 block">
                            LKR {{ number_format($po->total, 2) }}
                        </span>
                        @if($po->currency && $po->currency !== 'LKR')
                        <span class="text-[10px] text-slate-400">{{ $po->currency }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
                <a href="{{ route('admin.purchasing') }}"
                   class="text-xs font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition-colors">
                    View all purchase orders
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

</div>
