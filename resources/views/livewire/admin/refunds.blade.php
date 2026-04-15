{{-- resources/views/livewire/admin/refunds.blade.php --}}
<div x-data="{ detailOpen: @entangle('showDetail'), paymentOpen: @entangle('showPaymentModal') }">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 mb-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold font-[Poppins]">Refunds</h2>
                <p class="text-slate-300 text-sm mt-0.5">Track all processed customer refunds</p>
            </div>
            <a href="{{ route('admin.orders') }}"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-sm px-4 py-2 rounded-xl transition-colors border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                View Orders
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</span>
                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['all'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending</span>
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Processed</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['processed'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Completed</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['completed'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search order # or customer name..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-slate-800 bg-white text-slate-800 placeholder-slate-400">
            </div>
            {{-- Date range --}}
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <input wire:model.live="dateFrom" type="date"
                       class="text-xs text-slate-600 bg-transparent border-none outline-none w-28">
                <span class="text-xs text-slate-400">—</span>
                <input wire:model.live="dateTo" type="date"
                       class="text-xs text-slate-600 bg-transparent border-none outline-none w-28">
                @if($dateFrom || $dateTo)
                <button wire:click="clearDates" class="text-red-400 hover:text-red-600 ml-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>
        </div>

        {{-- Status Tabs --}}
        <div class="flex gap-2 flex-wrap mt-3">
            @foreach([
                ''          => ['label' => 'All',       'count' => $counts['all']],
                'pending'   => ['label' => 'Pending',   'count' => $counts['pending']],
                'processed' => ['label' => 'Processed', 'count' => $counts['processed']],
                'completed' => ['label' => 'Completed', 'count' => $counts['completed']],
            ] as $val => $tab)
            <button wire:click="$set('filterTab', '{{ $val }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                        {{ $filterTab === $val
                            ? 'bg-[#0F172A] text-white border-[#0F172A]'
                            : 'bg-white text-slate-500 border-slate-200 hover:border-slate-800' }}">
                {{ $tab['label'] }}
                @if($tab['count'] > 0)
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-bold
                    {{ $filterTab === $val ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $tab['count'] }}
                </span>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#0F172A] text-white">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider">Order #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider">Customer</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider">Amount</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider hidden md:table-cell">Method</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider hidden lg:table-cell">Bank Account</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider hidden lg:table-cell">Ref #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($refunds as $refund)
                    @php
                        $customerName = $refund->customer?->name
                            ?? $refund->order?->shipping_address['full_name']
                            ?? 'Guest';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        {{-- Order # --}}
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders') }}"
                               class="font-mono font-bold text-[#0F172A] hover:text-amber-600 transition-colors text-xs">
                                {{ $refund->order?->order_number ?? '—' }}
                            </a>
                        </td>
                        {{-- Customer --}}
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ $customerName }}</span>
                        </td>
                        {{-- Amount --}}
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold text-red-600 font-[Poppins]">
                                Rs. {{ number_format($refund->amount, 0) }}
                            </span>
                        </td>
                        {{-- Method --}}
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-slate-600">{{ $refund->method_label }}</span>
                        </td>
                        {{-- Bank Account --}}
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($refund->customer_bank_account)
                            <span class="font-mono text-xs text-slate-700">{{ $refund->customer_bank_account }}</span>
                            @else
                            <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- Ref # --}}
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($refund->reference_number)
                            <span class="font-mono text-xs text-slate-700">{{ $refund->reference_number }}</span>
                            @else
                            <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- Status Badge --}}
                        <td class="px-4 py-3">
                            @if($refund->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Pending
                            </span>
                            @elseif($refund->status === 'processed')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Processed
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed
                            </span>
                            @endif
                        </td>
                        {{-- Date --}}
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-xs text-slate-500">{{ $refund->created_at->format('d M Y') }}</span>
                        </td>
                        {{-- Actions --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($refund->status !== 'completed')
                                <button wire:click="openPaymentModal({{ $refund->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Process Payment
                                </button>
                                @endif
                                <button wire:click="viewRefund({{ $refund->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-amber-500 text-white text-xs font-semibold transition-colors">
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
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                <p class="font-semibold text-slate-500">No refunds found</p>
                                <p class="text-sm">Refunds created from the Orders page will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($refunds->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $refunds->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         REFUND DETAIL MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div
        x-show="detailOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        style="display:none;"
        wire:ignore.self
        @click.self="detailOpen = false; $wire.closeDetail()"
    >
        <div
            x-show="detailOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-lg max-h-[90vh] flex flex-col"
            @click.stop
        >
            @if($selected)
            @php
                $customerName = $selected->customer?->name
                    ?? $selected->order?->shipping_address['full_name']
                    ?? 'Guest';
            @endphp

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Refund Details</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Order {{ $selected->order?->order_number ?? '—' }}
                    </p>
                </div>
                <button @click="detailOpen = false; $wire.closeDetail()"
                        class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-5">

                {{-- Status badge --}}
                <div class="flex items-center justify-between">
                    <div>
                        @if($selected->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>Pending
                        </span>
                        @elseif($selected->status === 'processed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>Processed
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>Completed
                        </span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400">{{ $selected->created_at->format('d M Y, h:i A') }}</span>
                </div>

                {{-- Key details grid --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Refund Amount</p>
                        <p class="font-[Poppins] font-bold text-lg text-red-600">Rs. {{ number_format($selected->amount, 2) }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Method</p>
                        <p class="font-semibold text-slate-800">{{ $selected->method_label }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Customer</p>
                        <p class="font-semibold text-slate-800 text-sm">{{ $customerName }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Order</p>
                        <p class="font-mono font-bold text-slate-800 text-sm">{{ $selected->order?->order_number ?? '—' }}</p>
                    </div>
                </div>

                {{-- Bank account & reference --}}
                @if($selected->customer_bank_account || $selected->reference_number)
                <div class="border border-slate-200 rounded-xl divide-y divide-slate-100">
                    @if($selected->customer_bank_account)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Bank Account</span>
                        <span class="font-mono font-bold text-slate-800 text-sm">{{ $selected->customer_bank_account }}</span>
                    </div>
                    @endif
                    @if($selected->reference_number)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Reference #</span>
                        <span class="font-mono font-bold text-slate-800 text-sm">{{ $selected->reference_number }}</span>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Notes --}}
                @if($selected->notes)
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-2">Notes</p>
                    <p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3 leading-relaxed">{{ $selected->notes }}</p>
                </div>
                @endif

                {{-- Proof of payment --}}
                @if($selected->proof_file)
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-2">Payment Proof</p>
                    @if($selected->isProofImage())
                    <a href="{{ Storage::url($selected->proof_file) }}" target="_blank"
                       class="block rounded-xl overflow-hidden border border-slate-200 hover:border-amber-400 transition-colors">
                        <img src="{{ Storage::url($selected->proof_file) }}"
                             alt="Refund proof"
                             class="w-full max-h-56 object-contain bg-slate-50">
                    </a>
                    <a href="{{ Storage::url($selected->proof_file) }}" target="_blank"
                       class="mt-1.5 inline-flex items-center gap-1 text-xs text-amber-600 hover:text-amber-700 font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Open full size
                    </a>
                    @else
                    {{-- PDF download link --}}
                    <a href="{{ Storage::url($selected->proof_file) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl text-red-700 text-sm font-semibold transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Download PDF Proof
                    </a>
                    @endif
                </div>
                @endif

                {{-- Processed by --}}
                @if($selected->processedBy)
                <div class="flex items-center gap-2 text-xs text-slate-500 pt-1 border-t border-slate-100">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Processed by <span class="font-semibold text-slate-700">{{ $selected->processedBy->name }}</span>
                    @if($selected->processed_at)
                    on {{ $selected->processed_at->format('d M Y, h:i A') }}
                    @endif
                </div>
                @endif

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl shrink-0">
                <button @click="detailOpen = false; $wire.closeDetail()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Close
                </button>
                @if($selected->status !== 'completed')
                <button wire:click="openPaymentModal({{ $selected->id }})"
                        @click="detailOpen = false; $wire.closeDetail()"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Process Payment
                </button>
                @else
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 font-semibold text-sm border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Refund Completed
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         PROCESS PAYMENT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div
        x-show="paymentOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        style="display:none;"
        wire:ignore.self
    >
        <div
            x-show="paymentOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-md"
            @click.stop
        >
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Process Refund Payment</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Enter the payment details sent to the customer</p>
                </div>
                <button @click="paymentOpen = false; $wire.closePaymentModal()"
                        class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Payment Method</label>
                    <select wire:model="paymentMethod" class="form-input w-full">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online</option>
                        <option value="cash">Cash</option>
                    </select>
                    @error('paymentMethod') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Customer Bank Account
                        <span class="text-slate-400 font-normal">(for transfer)</span>
                    </label>
                    <input wire:model="paymentBankAccount" type="text" class="form-input w-full"
                           placeholder="e.g. 1234567890">
                    @error('paymentBankAccount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Reference / Transaction ID
                        <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input wire:model="paymentReference" type="text" class="form-input w-full"
                           placeholder="Bank ref or transaction ID">
                    @error('paymentReference') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Payment Proof
                        <span class="text-slate-400 font-normal">(screenshot or PDF)</span>
                    </label>
                    <input wire:model="paymentProofFile" type="file" accept="image/*,.pdf"
                           class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-xl p-1.5 bg-white">
                    @error('paymentProofFile') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @if($paymentProofFile)
                    <p class="mt-1 text-xs text-green-600 font-medium">File selected: {{ $paymentProofFile->getClientOriginalName() }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Notes
                        <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <textarea wire:model="paymentNotes" rows="2" class="form-input w-full resize-none"
                              placeholder="Payment notes..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button @click="paymentOpen = false; $wire.closePaymentModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                <button wire:click="processPayment"
                        wire:loading.attr="disabled"
                        wire:target="processPayment"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold text-sm transition-colors">
                    <svg wire:loading.remove wire:target="processPayment" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg wire:loading wire:target="processPayment" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span wire:loading.remove wire:target="processPayment">Mark as Paid</span>
                    <span wire:loading wire:target="processPayment">Processing...</span>
                </button>
            </div>
        </div>
    </div>

</div>
