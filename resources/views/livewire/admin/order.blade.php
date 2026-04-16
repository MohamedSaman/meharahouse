{{-- resources/views/livewire/admin/order.blade.php --}}
<div
    class="space-y-5"
    x-data="{
        detailOpen: @entangle('showDetail'),
        refundOpen: @entangle('showRefundModal'),
        backorderOpen: @entangle('showBackorderModal'),
        waPrompt: false,
        waLink: '',
        copyDone: false,
        dispatchAlert: false,
        dispatchData: {},
        deliverAlert: false,
        deliverData: {},
        completeAlert: false,
        completeData: {},
        confirmAlert: false,
        confirmData: {},
        copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.copyDone = true;
                setTimeout(() => this.copyDone = false, 3000);
            });
        }
    }"
    @open-whatsapp-prompt.window="
        waLink = 'https://wa.me/' + $event.detail.phone + '?text=' + encodeURIComponent($event.detail.message);
        waPrompt = true;
    "
    @no-payment-on-confirm.window="confirmAlert = true; confirmData = $event.detail[0]"
    @payment-due-on-dispatch.window="dispatchAlert = true; dispatchData = $event.detail[0]"
    @payment-due-on-deliver.window="deliverAlert = true; deliverData = $event.detail[0]"
    @payment-due-on-complete.window="completeAlert = true; completeData = $event.detail[0]"
>

    {{-- ══════════════════════ FLASH MESSAGES ══════════════════════ --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- WhatsApp balance reminder to copy --}}
    @if(session('whatsapp_reminder'))
    <div x-data="{ show: true, copied: false }" x-show="show"
         class="rounded-2xl border-2 border-emerald-200 bg-emerald-50 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-emerald-800 mb-2">Balance Reminder — {{ session('whatsapp_reminder_order') }}</p>
                <pre class="text-xs text-emerald-700 bg-white/70 rounded-lg p-3 border border-emerald-200 whitespace-pre-wrap font-mono leading-relaxed">{{ session('whatsapp_reminder') }}</pre>
            </div>
            <div class="flex flex-col gap-2 shrink-0">
                <button @click="navigator.clipboard.writeText(`{{ addslashes(session('whatsapp_reminder')) }}`).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors">
                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <svg x-show="copied" class="w-3.5 h-3.5" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
                <button @click="show = false" class="px-3 py-1.5 rounded-lg border border-emerald-300 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition-colors">Dismiss</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════ HEADER ══════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Orders</h2>
            <p class="text-sm text-[#64748B]">{{ $orders->total() }} total orders</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($pendingReceiptCount > 0)
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $pendingReceiptCount }} receipt{{ $pendingReceiptCount > 1 ? 's' : '' }} awaiting confirmation
            </div>
            @endif
            <button wire:click="exportCsv"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all duration-200 hover:-translate-y-0.5 shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </button>
        </div>
    </div>

    {{-- ══════════════════════ STATUS FILTER TABS ══════════════════════ --}}
    <div class="flex flex-wrap gap-2 p-2 rounded-2xl bg-white/80 border border-slate-200 shadow-sm">
        @php
        $statuses = [
            ''               => 'All',
            'new'            => 'New',
            'payment_received' => 'Payment Received',
            'confirmed'      => 'Confirmed',
            'sourcing'       => 'Sourcing',
            'dispatched'     => 'Dispatched',
            'delivered'      => 'Delivered',
            'completed'      => 'Completed',
            'refunded'       => 'Refunded',
            'cancelled'      => 'Cancelled',
        ];
        @endphp
        @foreach($statuses as $key => $label)
        <button wire:click="$set('filterStatus', '{{ $key }}')"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 border
                    {{ $filterStatus === $key
                        ? 'bg-blue-600 text-white border-blue-700 shadow-lg shadow-blue-600/25 -translate-y-px'
                        : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900 hover:border-slate-300 hover:-translate-y-px' }}">
            {{ $label }}
            @if($key !== '' && isset($statusCounts[$key]) && $statusCounts[$key] > 0)
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $filterStatus === $key ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-500' }}">{{ $statusCounts[$key] }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════ SEARCH & FILTERS ══════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
                <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search by order# or customer..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
            </div>
            {{-- Source Filter --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">Source:</span>
                <button wire:click="$set('filterSource', '')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $filterSource === '' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </button>
                <button wire:click="$set('filterSource', 'website')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $filterSource === 'website' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Web
                </button>
                <button wire:click="$set('filterSource', 'whatsapp')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $filterSource === 'whatsapp' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    WhatsApp
                </button>
            </div>
            {{-- Date Range --}}
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg px-2 py-1.5">
                    <svg class="w-4 h-4 text-[#94A3B8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input wire:model.live="dateFrom" type="date"
                           class="text-xs text-[#475569] bg-transparent border-none outline-none w-32">
                    <span class="text-xs text-[#94A3B8]">—</span>
                    <input wire:model.live="dateTo" type="date"
                           class="text-xs text-[#475569] bg-transparent border-none outline-none w-32">
                </div>
                {{-- Quick Presets --}}
                <div class="flex gap-1" x-data="{
                    setRange(from, to) {
                        $wire.set('dateFrom', from);
                        $wire.set('dateTo', to);
                    },
                    today() {
                        let d = new Date().toISOString().split('T')[0];
                        this.setRange(d, d);
                    },
                    last7() {
                        let to   = new Date();
                        let from = new Date(); from.setDate(from.getDate() - 6);
                        this.setRange(from.toISOString().split('T')[0], to.toISOString().split('T')[0]);
                    },
                    thisMonth() {
                        let now  = new Date();
                        let from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                        let to   = now.toISOString().split('T')[0];
                        this.setRange(from, to);
                    },
                    lastMonth() {
                        let now  = new Date();
                        let from = new Date(now.getFullYear(), now.getMonth()-1, 1).toISOString().split('T')[0];
                        let to   = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().split('T')[0];
                        this.setRange(from, to);
                    }
                }">
                    <button @click="today()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Today</button>
                    <button @click="last7()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">7d</button>
                    <button @click="thisMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Month</button>
                    <button @click="lastMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Last Mo</button>
                    @if($dateFrom || $dateTo)
                    <button wire:click="clearDates"
                            class="px-2 py-1 text-[10px] font-semibold rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                        Clear
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ ORDERS TABLE ══════════════════════ --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total / Advance</th>
                        <th>Receipt</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php
                    $statusColors = [
                        'new'              => 'bg-slate-100 text-slate-600 border-slate-200',
                        'payment_received' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'confirmed'        => 'bg-blue-100 text-blue-700 border-blue-200',
                        'sourcing'         => 'bg-orange-100 text-orange-700 border-orange-200',
                        'dispatched'       => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                        'delivered'        => 'bg-teal-100 text-teal-700 border-teal-200',
                        'completed'        => 'bg-green-100 text-green-700 border-green-200',
                        'refunded'         => 'bg-red-100 text-red-700 border-red-200',
                        'cancelled'        => 'bg-red-100 text-red-700 border-red-200',
                    ];
                    $statusColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';

                    // Use already-loaded payments relation (no extra queries)
                    $pendingReceipt      = $order->payments->where('status', 'pending')->whereNotNull('receipt_path')->first();
                    $hasConfirmedAdvance = $order->payments->whereIn('type', ['advance', 'full', 'balance'])->where('status', 'confirmed')->isNotEmpty();
                    @endphp
                    <tr wire:key="order-{{ $order->id }}" class="{{ $pendingReceipt ? 'bg-amber-50/30' : '' }}">
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span>
                                {{-- Source badge --}}
                                @if($order->source === 'whatsapp')
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/></svg>
                                    WA
                                </span>
                                @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase tracking-wide">Web</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $addr    = $order->shipping_address ?? [];
                                $phone   = $addr['phone'] ?? ($order->user?->phone ?? '');
                                $digits  = preg_replace('/[^0-9]/', '', $phone);
                                $last4   = strlen($digits) >= 4 ? substr($digits, -4) : '';
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#0F172A] flex items-center justify-center shrink-0">
                                    @php $nameChar = strtoupper(substr($order->user->name ?? ($order->shipping_address['full_name'] ?? 'G'), 0, 1)); @endphp
                                    <span class="text-[#F59E0B] text-[10px] font-bold">{{ $nameChar }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-[#0F172A] truncate">
                                        {{ $order->user->name ?? ($order->shipping_address['full_name'] ?? 'Guest') }}
                                    </p>
                                    <p class="text-xs text-[#94A3B8] truncate">{{ $order->user->email ?? ($order->shipping_address['phone'] ?? '') }}</p>
                                    @if($last4)
                                    <span class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold border border-emerald-100">
                                        WA-{{ $last4 }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold text-sm text-[#0F172A]">Rs. {{ number_format($order->total, 0) }}</p>
                                @if($order->advance_amount > 0)
                                <p class="text-[10px] text-amber-600 font-medium">Advance: Rs. {{ number_format($order->advance_amount, 0) }}</p>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($pendingReceipt)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-100 border border-amber-200 text-amber-700 text-[10px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Receipt Uploaded
                            </span>
                            @elseif($hasConfirmedAdvance)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-100 border border-green-200 text-green-700 text-[10px] font-bold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Confirmed
                            </span>
                            @else
                            <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide {{ $statusColor }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $order->created_at->format('d M Y') }}</span></td>
                        <td>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                {{-- View Detail --}}
                                <button wire:click="viewOrder({{ $order->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 hover:bg-blue-100 transition-all duration-200 hover:-translate-y-0.5"
                                        style="color:#475569;" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>

                                        {{-- ── Status-based quick actions ── --}}

                                {{-- Confirm Receipt: any pending receipt on any non-terminal status --}}
                                @if($pendingReceipt && !in_array($order->status, ['completed','cancelled','refunded']))
                                <button wire:click="confirmPayment({{ $pendingReceipt->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-amber-500 hover:bg-amber-600 border border-amber-500 transition-all hover:-translate-y-0.5 shadow-sm"
                                        style="color:#ffffff;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Confirm Receipt
                                </button>
                                @endif

                                {{-- Confirm Order:
                                     - Bank transfer: after payment_received (receipt confirmed)
                                     - COD/others: directly from new --}}
                                @if($order->status === 'payment_received' ||
                                    ($order->status === 'new' && $order->payment_method !== 'bank_transfer'))
                                <button wire:click="confirmOrder({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-blue-600 hover:bg-blue-700 border border-blue-600 transition-all hover:-translate-y-0.5 shadow-sm"
                                        style="color:#ffffff;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Confirm Order
                                </button>
                                @endif

                                {{-- Start Sourcing (from confirmed, not yet ordered) --}}
                                @if($order->status === 'confirmed' && $order->supplier_status === 'none')
                                <button wire:click="markSourcing({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-orange-500 hover:bg-orange-600 border border-orange-500 transition-all hover:-translate-y-0.5 shadow-sm"
                                        style="color:#ffffff;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Sourcing
                                </button>
                                @endif

                                {{-- Dispatch: from confirmed (skip sourcing) or sourcing+received --}}
                                @php
                                    $hasDispatchable = $order->items->whereIn('status', ['active', 'replaced'])->isNotEmpty();
                                @endphp
                                @if($hasDispatchable && (
                                    (in_array($order->status, ['confirmed']) && $order->supplier_status !== 'ordered') ||
                                    ($order->status === 'sourcing' && $order->supplier_status === 'received')
                                ))
                                <button wire:click="markDispatched({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-600 hover:bg-indigo-700 border border-indigo-600 transition-all hover:-translate-y-0.5 shadow-sm"
                                        style="color:#ffffff;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    Dispatch
                                </button>
                                @endif

                                {{-- Sourcing sub-actions --}}
                                @if($order->status === 'sourcing' && $order->supplier_status === 'ordered')
                                <button wire:click="markSupplierReceived({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-teal-600 hover:bg-teal-700 border border-teal-600 transition-all hover:-translate-y-0.5 shadow-sm"
                                        style="color:#ffffff;">
                                    Stock In
                                </button>
                                <button wire:click="markSupplierUnavailable({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-red-50 hover:bg-red-100 border border-red-200 transition-all hover:-translate-y-0.5"
                                        style="color:#b91c1c;">
                                    Unavailable
                                </button>
                                @endif

                                {{-- Mark Delivered --}}
                                @if($order->status === 'dispatched')
                                <button wire:click="markDelivered({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-teal-600 hover:bg-teal-700 border border-teal-600 transition-all hover:-translate-y-0.5 shadow-sm"
                                        style="color:#ffffff;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Delivered
                                </button>
                                @endif

                                {{-- Complete --}}
                                @if($order->status === 'delivered')
                                    @if($order->isWhatsapp() && $order->balanceDue() > 0)
                                    <button wire:click="sendBalanceReminder({{ $order->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all hover:-translate-y-0.5"
                                            style="color:#047857;">
                                        Reminder
                                    </button>
                                    @endif
                                    <button wire:click="markCompleted({{ $order->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-green-600 hover:bg-green-700 border border-green-600 transition-all hover:-translate-y-0.5 shadow-sm"
                                            style="color:#ffffff;">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Complete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-12 text-[#94A3B8]">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">{{ $orders->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         ORDER DETAIL SLIDE-OVER PANEL
    ══════════════════════════════════════════════════════════════ --}}
    @if($selectedOrder)
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
                    <h3 class="font-[Poppins] font-bold text-white text-base">{{ $selectedOrder->order_number }}</h3>
                    <p class="text-xs text-slate-400">{{ $selectedOrder->created_at->format('d M Y, h:i A') }}</p>
                </div>
                {{-- Source badge --}}
                @if($selectedOrder->isWhatsapp())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide" style="background:#25D366;color:#fff">
                    WA
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-500 text-white uppercase tracking-wide">Web</span>
                @endif
            </div>
            <button wire:click="closeDetail" class="p-2 rounded-xl text-slate-400 hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Panel Scrollable Body --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-6">

            {{-- ── Customer & Address ── --}}
            <div class="bg-[#F8FAFC] rounded-2xl p-4 space-y-3">
                <h4 class="font-semibold text-sm text-[#0F172A] flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Customer & Delivery
                </h4>
                @php
                    $detailAddr   = $selectedOrder->shipping_address ?? [];
                    $detailPhone  = $detailAddr['phone'] ?? ($selectedOrder->user?->phone ?? '');
                    $detailDigits = preg_replace('/[^0-9]/', '', $detailPhone);
                    $detailLast4  = strlen($detailDigits) >= 4 ? substr($detailDigits, -4) : '';
                @endphp
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-[#64748B] text-xs font-semibold uppercase tracking-wide">Customer</p>
                        <p class="font-semibold text-[#0F172A] mt-1">{{ $selectedOrder->user->name ?? ($selectedOrder->shipping_address['full_name'] ?? 'Guest') }}</p>
                        <p class="text-[#64748B] text-xs mt-0.5">{{ $selectedOrder->user->email ?? ($selectedOrder->shipping_address['email'] ?? '') }}</p>
                        <div class="flex items-center gap-2 flex-wrap mt-0.5">
                            <p class="text-[#64748B] text-xs">{{ $detailPhone }}</p>
                            @if($detailLast4)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold border border-emerald-100">
                                WA-{{ $detailLast4 }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-[#64748B] text-xs font-semibold uppercase tracking-wide">Shipping Address</p>
                        <p class="font-semibold text-[#0F172A] mt-1">{{ $selectedOrder->shipping_address['full_name'] ?? '' }}</p>
                        <p class="text-[#64748B] text-xs mt-0.5">{{ $selectedOrder->shipping_address['address'] ?? '' }}</p>
                        <p class="text-[#64748B] text-xs">{{ $selectedOrder->shipping_address['city'] ?? '' }}{{ isset($selectedOrder->shipping_address['region']) ? ', ' . $selectedOrder->shipping_address['region'] : '' }}</p>
                        <div class="flex items-center gap-2 flex-wrap mt-0.5">
                            <p class="text-[#64748B] text-xs">{{ $selectedOrder->shipping_address['phone'] ?? '' }}</p>
                            @if($detailLast4)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold border border-emerald-100">
                                WA-{{ $detailLast4 }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Status Timeline ── --}}
            <div>
                <h4 class="font-semibold text-sm text-[#0F172A] mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Status History
                </h4>
                @if($selectedOrder->statusLogs->isNotEmpty())
                @php
                $statusColors = [
                    'new'              => ['dot' => 'bg-amber-400',  'badge' => 'bg-amber-50 text-amber-700 border-amber-200'],
                    'confirmed'        => ['dot' => 'bg-blue-500',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                    'processing'       => ['dot' => 'bg-indigo-500', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                    'sourcing'         => ['dot' => 'bg-purple-500', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200'],
                    'dispatched'       => ['dot' => 'bg-sky-500',    'badge' => 'bg-sky-50 text-sky-700 border-sky-200'],
                    'delivered'        => ['dot' => 'bg-green-500',  'badge' => 'bg-green-50 text-green-700 border-green-200'],
                    'completed'        => ['dot' => 'bg-emerald-500','badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'cancelled'        => ['dot' => 'bg-red-400',    'badge' => 'bg-red-50 text-red-600 border-red-200'],
                    'payment_received' => ['dot' => 'bg-teal-500',   'badge' => 'bg-teal-50 text-teal-700 border-teal-200'],
                ];
                @endphp
                <div class="space-y-0">
                    @foreach($selectedOrder->statusLogs as $i => $log)
                    @php
                        $isLast  = $loop->last;
                        $colors  = $statusColors[$log->to_status] ?? ['dot' => 'bg-slate-400', 'badge' => 'bg-slate-50 text-slate-600 border-slate-200'];
                    @endphp
                    <div class="flex gap-4">
                        {{-- Timeline spine --}}
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full {{ $colors['dot'] }} ring-2 ring-white shadow-sm mt-1 shrink-0"></div>
                            @if(!$isLast)
                            <div class="w-px flex-1 bg-slate-200 my-1"></div>
                            @endif
                        </div>
                        {{-- Content --}}
                        <div class="pb-5 flex-1 min-w-0">
                            {{-- Status transition badges --}}
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                @if($log->from_status)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                    {{ ucwords(str_replace('_', ' ', $log->from_status)) }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $colors['badge'] }}">
                                    {{ ucwords(str_replace('_', ' ', $log->to_status)) }}
                                </span>
                            </div>
                            {{-- Notes --}}
                            @if($log->notes)
                            <p class="text-xs text-slate-500 leading-relaxed mb-1">{{ $log->notes }}</p>
                            @endif
                            {{-- Meta --}}
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                @if($log->createdBy)
                                <span class="text-slate-300">·</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>{{ $log->createdBy->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex items-center gap-2 text-xs text-slate-400 italic py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    No status history recorded.
                </div>
                @endif
            </div>

            {{-- ── Payments Section ── --}}
            <div>
                <h4 class="font-semibold text-sm text-[#0F172A] mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Payments
                </h4>

                {{-- Payment summary bar --}}
                @php
                    $summaryTotalPaid    = $selectedOrder->totalPaid();
                    $summaryTotalRefund  = $selectedOrder->totalRefunded();
                    $summaryBalanceDue   = $selectedOrder->balanceDue();
                @endphp
                <div class="bg-slate-900 rounded-xl p-4 mb-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="border-r border-slate-700 last:border-0 sm:border-r sm:last:border-0 pr-3 last:pr-0">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 mb-0.5">Order Total</p>
                        <p class="font-bold text-white text-sm">Rs. {{ number_format($selectedOrder->total, 0) }}</p>
                    </div>
                    <div class="sm:border-r border-slate-700 sm:pr-3">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 mb-0.5">Advance Paid</p>
                        <p class="font-bold text-amber-400 text-sm">
                            Rs. {{ number_format($summaryTotalPaid, 0) }}
                        </p>
                        @if($summaryTotalPaid > 0 && $summaryTotalPaid < $selectedOrder->total)
                        <p class="text-[9px] text-slate-500 mt-0.5">of Rs. {{ number_format($selectedOrder->advance_amount, 0) }} req.</p>
                        @endif
                    </div>
                    <div class="border-r border-slate-700 last:border-0 sm:border-r pr-3 last:pr-0">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 mb-0.5">Refunded</p>
                        @if($summaryTotalRefund > 0)
                        <p class="font-bold text-purple-400 text-sm">Rs. {{ number_format($summaryTotalRefund, 0) }}</p>
                        @else
                        <p class="font-bold text-slate-600 text-sm">Rs. 0</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 mb-0.5">Balance Due</p>
                        @if(in_array($selectedOrder->status, ['refunded', 'cancelled']) && $summaryTotalRefund > 0)
                        <p class="font-bold text-slate-400 text-sm">Refunded</p>
                        @elseif($summaryBalanceDue > 0)
                        <p class="font-bold text-red-400 text-sm">Rs. {{ number_format($summaryBalanceDue, 0) }}</p>
                        @else
                        <p class="font-bold text-emerald-400 text-sm">Cleared</p>
                        @endif
                    </div>
                </div>

                {{-- Payment records --}}
                @if($selectedOrder->payments->isNotEmpty())
                <div class="space-y-3">
                    @foreach($selectedOrder->payments as $payment)
                    @php
                    $payBadge = match($payment->status) {
                        'confirmed' => 'bg-green-100 text-green-700 border-green-200',
                        'rejected'  => 'bg-red-100 text-red-700 border-red-200',
                        default     => 'bg-amber-100 text-amber-700 border-amber-200',
                    };
                    $typeBadge = match($payment->type) {
                        'advance' => 'bg-blue-100 text-blue-700',
                        'balance' => 'bg-purple-100 text-purple-700',
                        'refund'  => 'bg-red-100 text-red-700',
                        'full'    => 'bg-green-100 text-green-700',
                        default   => 'bg-slate-100 text-slate-600',
                    };
                    @endphp
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $typeBadge }}">
                                        {{ ucfirst($payment->type) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $payBadge }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                                <p class="font-bold text-[#0F172A] text-base mt-1.5">Rs. {{ number_format($payment->amount, 0) }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</p>
                                @if($payment->reference)
                                <p class="text-xs text-slate-400 mt-0.5 font-mono">Ref: {{ $payment->reference }}</p>
                                @endif
                                @if($payment->confirmedBy && $payment->confirmed_at)
                                <p class="text-[10px] text-slate-400 mt-1">
                                    Confirmed by {{ $payment->confirmedBy->name }} on {{ $payment->confirmed_at->format('d M Y') }}
                                </p>
                                @endif
                            </div>

                            {{-- Receipt thumbnail + actions --}}
                            @if($payment->receipt_path)
                            <div class="shrink-0 flex flex-col items-end gap-2">
                                <a href="{{ $payment->receiptUrl() }}" target="_blank"
                                   class="block w-16 h-16 rounded-lg overflow-hidden border-2 border-slate-200 hover:border-amber-400 transition-colors shadow-sm">
                                    <img src="{{ $payment->receiptUrl() }}" alt="Payment Receipt"
                                         class="w-full h-full object-cover">
                                </a>
                                @if($payment->status === 'pending' && !in_array($selectedOrder->status, ['refunded','cancelled','completed']))
                                <div class="flex gap-1">
                                    <button wire:click="confirmPayment({{ $payment->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-[10px] font-bold transition-colors">
                                        Confirm
                                    </button>
                                    <button wire:click="rejectPayment({{ $payment->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-[10px] font-bold border border-red-200 transition-colors">
                                        Reject
                                    </button>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-slate-400 italic">No payment records yet.</p>
                @endif

                {{-- Bank transfer: show pending receipt notice only when no payments recorded yet --}}
                @if($selectedOrder->payment_method === 'bank_transfer' && $selectedOrder->payments->where('status', 'pending')->whereNotNull('receipt_path')->isEmpty() && $selectedOrder->payments->isEmpty())
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-700 font-medium">Awaiting customer payment receipt upload</p>
                </div>
                @endif

                {{-- Balance reminder / complete buttons --}}
                @if(in_array($selectedOrder->status, ['delivered', 'completed']))
                <div class="mt-3 flex flex-wrap gap-2">
                    @if($selectedOrder->isWhatsapp() && $selectedOrder->balanceDue() > 0)
                    <button wire:click="sendBalanceReminder({{ $selectedOrder->id }})"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/></svg>
                        Send Balance Reminder
                    </button>
                    @endif
                    @if($selectedOrder->status !== 'completed')
                    <button wire:click="markCompleted({{ $selectedOrder->id }})"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mark Completed
                    </button>
                    @endif
                </div>
                @endif
            </div>

            {{-- ── Supplier Status ── --}}
            @if($selectedOrder->status === 'sourcing' || $selectedOrder->supplier_status !== 'none')
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                <h4 class="font-semibold text-sm text-orange-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Supplier Status
                </h4>
                @php
                $supplierBadge = match($selectedOrder->supplier_status) {
                    'ordered'     => 'bg-amber-100 text-amber-700 border-amber-300',
                    'received'    => 'bg-green-100 text-green-700 border-green-300',
                    'unavailable' => 'bg-red-100 text-red-700 border-red-300',
                    default       => 'bg-slate-100 text-slate-600 border-slate-200',
                };
                @endphp
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3 py-1.5 rounded-lg border text-xs font-bold uppercase tracking-wide {{ $supplierBadge }}">
                        {{ ucfirst($selectedOrder->supplier_status) }}
                    </span>
                    @if($selectedOrder->supplier_status === 'ordered')
                    <button wire:click="markSupplierReceived({{ $selectedOrder->id }})"
                            class="px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition-colors">
                        Mark Received from Supplier
                    </button>
                    <button wire:click="markSupplierUnavailable({{ $selectedOrder->id }})"
                            class="px-3 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 text-xs font-bold transition-colors">
                        Mark Unavailable
                    </button>
                    @endif
                    @if($selectedOrder->supplier_status === 'unavailable' && !$selectedOrder->refund_option)
                    <button wire:click="offerRefund({{ $selectedOrder->id }})"
                            class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">
                        Offer Refund
                    </button>
                    <button wire:click="offerReorder({{ $selectedOrder->id }})"
                            class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                        Offer Reorder
                    </button>
                    @endif
                    @if($selectedOrder->refund_option)
                    <div class="text-xs font-semibold text-slate-700 bg-white border border-slate-200 px-3 py-1.5 rounded-lg">
                        Resolution: <span class="uppercase text-amber-600">{{ $selectedOrder->refund_option }}</span>
                    </div>
                    @endif
                </div>

                @if(($selectedOrder->refund_option === 'refund' || $selectedOrder->status === 'refunded') && $selectedOrder->refunds->isEmpty())
                <div class="mt-3 pt-3 border-t border-orange-200">
                    <button wire:click="openRefundModal({{ $selectedOrder->id }})"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Process Refund Payment
                    </button>
                </div>
                @endif

                @if($selectedOrder->refund)
                <div class="mt-3 rounded-lg bg-red-50 border border-red-200 p-3 text-xs text-red-800">
                    <p class="font-bold">Refund Processed</p>
                    <p class="mt-0.5">Amount: Rs. {{ number_format($selectedOrder->refund->amount, 0) }} via {{ ucfirst(str_replace('_', ' ', $selectedOrder->refund->method)) }}</p>
                    @if($selectedOrder->refund->customer_bank_account)
                    <p class="mt-0.5">Transfer to: <span class="font-mono font-bold">{{ $selectedOrder->refund->customer_bank_account }}</span></p>
                    @endif
                    @if($selectedOrder->refund->reference_number)
                    <p class="font-mono mt-0.5">Ref: {{ $selectedOrder->refund->reference_number }}</p>
                    @endif
                    <p class="mt-0.5 text-red-600">{{ $selectedOrder->refund->processed_at?->format('d M Y') }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- ── Partial Fulfillment / Backorder ── --}}
            @php
                $shortageItems = [];
                if (in_array($selectedOrder->status, ['sourcing', 'confirmed', 'payment_received', 'new'])) {
                    foreach ($selectedOrder->items as $bi) {
                        $inStock = (int)($bi->product?->stock ?? 0);
                        if ($inStock < (int)$bi->quantity) {
                            $shortageItems[] = [
                                'name'    => $bi->product_name,
                                'ordered' => (int)$bi->quantity,
                                'stock'   => $inStock,
                                'short'   => (int)$bi->quantity - $inStock,
                            ];
                        }
                    }
                }
                $pendingBackorders = $selectedOrder->backorders->whereIn('status', ['pending', 'repurchasing'])->values();
                $activeBackorders  = $selectedOrder->backorders->whereNotIn('status', ['completed', 'cancelled'])->values();
                $fulfilledBackorders = $selectedOrder->backorders->where('status', 'completed')->values();
            @endphp

            {{-- Show shortage banner + action button when there is a shortage and no active backorder yet --}}
            @if(count($shortageItems) > 0 && $pendingBackorders->isEmpty())
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-3 space-y-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs font-semibold text-orange-700">Stock shortage — items cannot be fully fulfilled.</p>
                </div>
                @foreach($shortageItems as $s)
                <p class="text-xs text-orange-600 pl-6">
                    &bull; {{ $s['name'] }}: ordered <strong>{{ $s['ordered'] }}</strong>, in stock <strong>{{ $s['stock'] }}</strong>, <span class="font-bold text-red-600">short {{ $s['short'] }}</span>
                </p>
                @endforeach
                <!-- <button wire:click="openBackorderModal({{ $selectedOrder->id }})"
                        class="w-full mt-1 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold transition-colors">
                    Handle Stock Shortage
                </button> -->
            </div>
            @endif

            {{-- Show active backorders when they exist --}}
            @if($activeBackorders->isNotEmpty())
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 space-y-2">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <p class="text-xs font-semibold text-blue-700">Active Backorders ({{ $activeBackorders->count() }})</p>
                    </div>
                    <a href="{{ route('admin.backorders') }}"
                       class="text-[10px] text-blue-600 hover:text-blue-800 font-semibold underline underline-offset-2">
                        Manage &rarr;
                    </a>
                </div>
                @foreach($activeBackorders as $bo)
                <div class="flex items-center justify-between gap-2 rounded-lg bg-white border border-blue-100 px-3 py-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $bo->product_name }}</p>
                        <p class="text-[10px] text-slate-500">
                            Short: <strong class="text-red-600">{{ $bo->short_qty }}</strong>
                            &middot;
                            <span class="capitalize font-semibold text-blue-600">{{ $bo->decisionLabel() }}</span>
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $bo->statusBadgeClass() }}">
                        {{ $bo->statusLabel() }}
                    </span>
                </div>
                @endforeach
                {{-- Quick action for pending/repurchasing only --}}
                @if($pendingBackorders->isNotEmpty())
                @foreach($pendingBackorders as $bo)
                <button wire:click="fulfillBackorder({{ $bo->id }})"
                        wire:loading.attr="disabled"
                        class="w-full py-1.5 rounded-lg border border-blue-200 bg-blue-100 hover:bg-blue-200 text-blue-700 text-[10px] font-bold transition-colors whitespace-nowrap">
                    Mark "{{ $bo->product_name }}" as Ready
                </button>
                @endforeach
                @endif
            </div>
            @endif

            {{-- Completed backorders summary --}}
            @if($fulfilledBackorders->isNotEmpty() && $activeBackorders->isEmpty())
            <div class="rounded-xl border border-green-200 bg-green-50 p-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs font-semibold text-green-700">All backorders completed and delivered</p>
                </div>
            </div>
            @endif

            {{-- ── Order Items ── --}}
            <div>
                <h4 class="font-semibold text-sm text-[#0F172A] mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Order Items
                </h4>
                <div class="space-y-2">
                    @foreach($selectedOrder->items as $item)
                    @php
                        $statusConfig = match($item->status ?? 'active') {
                            'refunded'    => ['label' => 'Refunded',  'bg' => 'bg-red-50',    'border' => 'border-red-200',    'badge' => 'bg-red-100 text-red-700',      'icon_color' => 'text-red-500'],
                            'replaced'    => ['label' => 'Replaced',  'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-700','icon_color' => 'text-orange-500'],
                            'backordered' => ['label' => 'Backorder', 'bg' => 'bg-blue-50',   'border' => 'border-blue-200',   'badge' => 'bg-blue-100 text-blue-700',   'icon_color' => 'text-blue-500'],
                            default       => ['label' => 'Confirmed', 'bg' => 'bg-white',     'border' => 'border-slate-100',  'badge' => 'bg-green-100 text-green-700', 'icon_color' => 'text-green-500'],
                        };
                        $priceDiffAmt = $item->is_replaced
                            ? round(((float)$item->price - (float)$item->original_price) * $item->quantity, 2)
                            : 0;
                        // Find the backorder record linked to this item (if backordered)
                        $itemBackorder = ($item->status === 'backordered')
                            ? $selectedOrder->backorders->where('order_item_id', $item->id)->first()
                            : null;
                    @endphp
                    <div class="rounded-xl border {{ $statusConfig['border'] }} {{ $statusConfig['bg'] }} p-3">
                        {{-- Item header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $item->product_name }}</p>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusConfig['badge'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                                {{-- Show original qty if it differs from fulfilled qty --}}
                                @if($item->original_qty && $item->original_qty != $item->quantity)
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Ordered: {{ $item->original_qty }} unit(s)
                                    @if($item->quantity > 0) &middot; Fulfilled: {{ $item->quantity }} unit(s) @endif
                                    @if(!empty($item->size)) &middot; Size: <span class="font-semibold">{{ $item->size }}</span> @endif
                                </p>
                                @else
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Rs. {{ number_format($item->price, 0) }} &times; {{ $item->quantity }}
                                    @if(!empty($item->size)) &middot; Size: <span class="font-semibold">{{ $item->size }}</span> @endif
                                </p>
                                @endif
                            </div>
                            {{-- Amount column --}}
                            <div class="text-right shrink-0">
                                @if($item->status === 'refunded')
                                    <p class="text-sm font-bold text-red-600">Rs. {{ number_format($item->original_ordered_subtotal ?? $item->refund_amount, 0) }}</p>
                                    <p class="text-[10px] text-red-500">refunded</p>
                                @elseif($item->status === 'backordered')
                                    <p class="text-sm font-semibold text-blue-700">Rs. {{ number_format($item->subtotal, 0) }}</p>
                                    <p class="text-[10px] text-blue-500">pending dispatch</p>
                                @else
                                    <p class="text-sm font-semibold text-[#0F172A]">Rs. {{ number_format($item->subtotal, 0) }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Refund detail line --}}
                        @if($item->status === 'refunded' || ($item->refund_amount !== null && $item->refund_amount > 0))
                        <div class="mt-2 flex items-center gap-2 text-[11px] text-red-700">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            @if($item->status === 'refunded')
                                <span>Product out of stock &mdash; <strong>Rs. {{ number_format($item->refund_amount, 0) }} item value cancelled</strong></span>
                            @else
                                <span>Short by {{ $item->original_qty - $item->quantity }} unit(s) &mdash; <strong>Rs. {{ number_format($item->refund_amount, 0) }} item value cancelled</strong> for the shortage</span>
                            @endif
                        </div>
                        @endif

                        {{-- Replacement detail line --}}
                        @if($item->is_replaced)
                        <div class="mt-2 flex items-start gap-2 text-[11px] text-orange-700">
                            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <div>
                                <span class="font-semibold">{{ $item->original_product_name }}</span>
                                <span class="mx-1">&rarr;</span>
                                <span class="font-semibold">{{ $item->product_name }}</span>
                                @if($item->replacement_notes)
                                    <span class="text-orange-500"> &middot; {{ $item->replacement_notes }}</span>
                                @endif
                                <span class="text-[10px] text-orange-400 ml-1">&middot; replaced {{ $item->replaced_at?->diffForHumans() }}</span>
                                @if(abs($priceDiffAmt) > 0.01)
                                <br>
                                <span class="font-bold {{ $priceDiffAmt > 0 ? 'text-red-600' : 'text-blue-600' }}">
                                    Price diff: {{ $priceDiffAmt > 0 ? '+' : '' }}Rs. {{ number_format(abs($priceDiffAmt), 0) }}
                                    ({{ $priceDiffAmt > 0 ? 'extra charged to customer' : 'refund owed to customer' }})
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Backorder detail line --}}
                        @if($item->status === 'backordered' && $itemBackorder)
                        <div class="mt-2 flex items-center gap-2 text-[11px] text-blue-700">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>
                                Backorder <strong>{{ $itemBackorder->backorder_number }}</strong> &mdash;
                                status: <strong>{{ ucfirst($itemBackorder->status) }}</strong>
                                &middot; will be dispatched separately
                            </span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Totals: full accounting breakdown --}}
                @php
                    // Original items subtotal = what customer was charged when they ordered
                    // For changed items (replaced/refunded), use the stored original_ordered_subtotal
                    $origItemsSubtotal = $selectedOrder->items->sum(function($item) {
                        if (!empty($item->original_ordered_subtotal)) {
                            return (float) $item->original_ordered_subtotal;
                        }
                        return (float) $item->subtotal;
                    });
                    $origTotal = round(
                        $origItemsSubtotal
                        + (float) $selectedOrder->shipping_cost
                        + (float) $selectedOrder->tax
                        - (float) $selectedOrder->discount,
                    2);

                    // Replacement price difference: positive = customer owes more, negative = cheaper replacement
                    $replaceDiff = $selectedOrder->items->where('status', 'replaced')->sum(function($item) {
                        $orig = (float) ($item->original_ordered_subtotal ?? $item->subtotal);
                        return (float) $item->subtotal - $orig;
                    });

                    $refundedAmt = $selectedOrder->totalRefunded();
                    $paidAmt     = $selectedOrder->totalPaid();
                    $balanceDue  = $selectedOrder->balanceDue();
                @endphp

                <div class="bg-[#F8FAFC] rounded-xl p-4 mt-3 text-sm space-y-1.5">

                    {{-- Original order lines --}}
                    <div class="flex justify-between text-[#64748B]">
                        <span>Subtotal (as ordered)</span>
                        <span>Rs. {{ number_format($origItemsSubtotal, 0) }}</span>
                    </div>
                    @if($selectedOrder->shipping_cost > 0)
                    <div class="flex justify-between text-[#64748B]">
                        <span>Shipping</span><span>Rs. {{ number_format($selectedOrder->shipping_cost, 0) }}</span>
                    </div>
                    @endif
                    @if($selectedOrder->tax > 0)
                    <div class="flex justify-between text-[#64748B]">
                        <span>Tax</span><span>Rs. {{ number_format($selectedOrder->tax, 0) }}</span>
                    </div>
                    @endif
                    @if($selectedOrder->discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span><span>-Rs. {{ number_format($selectedOrder->discount, 0) }}</span>
                    </div>
                    @endif

                    {{-- Original total --}}
                    <div class="flex justify-between font-semibold border-t border-[#E2E8F0] pt-2 text-[#0F172A]">
                        <span>Original Order Total</span>
                        <span>Rs. {{ number_format($origTotal, 0) }}</span>
                    </div>

                    {{-- Replacement charge / saving --}}
                    @if($replaceDiff > 0)
                    <div class="flex justify-between text-orange-600">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Replace charge (more expensive item)
                        </span>
                        <span>+Rs. {{ number_format($replaceDiff, 0) }}</span>
                    </div>
                    @elseif($replaceDiff < 0)
                    <div class="flex justify-between text-blue-600">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            Replace saving (cheaper item)
                        </span>
                        <span>-Rs. {{ number_format(abs($replaceDiff), 0) }}</span>
                    </div>
                    @endif

                    {{-- Refunded --}}
                    @if($refundedAmt > 0)
                    <div class="flex justify-between text-red-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Refunded to customer
                        </span>
                        <span>-Rs. {{ number_format($refundedAmt, 0) }}</span>
                    </div>
                    @endif

                    {{-- Current total (after all adjustments) --}}
                    @if($replaceDiff != 0 || $refundedAmt > 0)
                    <div class="flex justify-between font-semibold border-t border-[#E2E8F0] pt-2 text-[#0F172A]">
                        <span>Current Order Total</span>
                        <span>Rs. {{ number_format($selectedOrder->total, 0) }}</span>
                    </div>
                    @endif

                    {{-- Advance paid --}}
                    <div class="flex justify-between text-emerald-600 {{ ($replaceDiff == 0 && $refundedAmt == 0) ? 'border-t border-[#E2E8F0] pt-2' : '' }}">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Advance Paid
                        </span>
                        <span>-Rs. {{ number_format($paidAmt, 0) }}</span>
                    </div>

                    {{-- Balance Due --}}
                    <div class="flex justify-between font-bold text-base border-t border-[#E2E8F0] pt-2">
                        <span class="text-[#0F172A]">Balance Due</span>
                        @if($balanceDue > 0)
                            <span class="text-red-600">Rs. {{ number_format($balanceDue, 0) }}</span>
                        @else
                            <span class="text-emerald-600">Cleared</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── Admin Notes ── --}}
            @if($selectedOrder->notes)
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Order Notes</p>
                <p class="text-sm text-[#0F172A]">{{ $selectedOrder->notes }}</p>
            </div>
            @endif

        </div>{{-- end scrollable body --}}

        {{-- Panel Footer — sticky action bar --}}
        <div class="shrink-0 border-t border-slate-100 bg-white px-6 py-4 flex items-center gap-3">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.order.waybill', $selectedOrder) : route('staff.order.waybill', $selectedOrder) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Waybill
            </a>
            <button wire:click="closeDetail"
                    class="ml-auto inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold hover:bg-slate-200 transition-all">
                Close
            </button>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         REFUND MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div
        x-show="refundOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        style="display:none;"
        wire:ignore.self
    >
        <div
            x-show="refundOpen"
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
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Record Refund</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Payment details will be entered on the Refunds page</p>
                </div>
                <button @click="refundOpen = false; $wire.set('showRefundModal', false)"
                        class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Refund Amount (Rs.)</label>
                    <input wire:model="refundAmount" type="number" step="0.01" min="0" class="form-input w-full"
                           placeholder="0.00">
                    @error('refundAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea wire:model="refundNotes" rows="3" class="form-input w-full resize-none" placeholder="Reason for refund..."></textarea>
                    @error('refundNotes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <button @click="refundOpen = false; $wire.set('showRefundModal', false)"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                <button wire:click="processRefund"
                        wire:loading.attr="disabled"
                        wire:target="processRefund"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold text-sm transition-colors">
                    <svg wire:loading.remove wire:target="processRefund" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <svg wire:loading wire:target="processRefund" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    <span wire:loading.remove wire:target="processRefund">Record Refund</span>
                    <span wire:loading wire:target="processRefund">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         BACKORDER / PARTIAL FULFILLMENT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div
        x-show="backorderOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        style="display:none;"
        wire:ignore.self
        @click.self="backorderOpen = false; $wire.set('showBackorderModal', false)"
    >
        <div
            x-show="backorderOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-lg max-h-[90vh] flex flex-col"
            @click.stop
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-[#0F172A]">Handle Stock Shortage</h3>
                        <p class="text-xs text-slate-500">Choose how to handle each unavailable item</p>
                    </div>
                </div>
                <button @click="backorderOpen = false; $wire.set('showBackorderModal', false)"
                        class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

                <div class="bg-orange-50 rounded-xl p-3 text-xs text-orange-700 border border-orange-200">
                    <strong>Note:</strong> Available stock will be deducted and the order confirmed for dispatch. The short quantity will be tracked as a backorder for resolution.
                </div>

                {{-- Items list --}}
                <div class="space-y-3">
                    @foreach($backorderItems as $idx => $item)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3">

                        {{-- Product info row --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-sm text-[#0F172A]">{{ $item['product_name'] }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-slate-500 flex-wrap">
                                    <span>Ordered: <strong class="text-slate-700">{{ $item['ordered'] }}</strong></span>
                                    <span>In Stock: <strong class="text-green-600">{{ $item['available'] }}</strong></span>
                                    <span>Short: <strong class="text-red-600">{{ $item['short'] }}</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 shrink-0 whitespace-nowrap">
                                -{{ $item['short'] }} units
                            </span>
                        </div>

                        {{-- Decision selector --}}
                        <div>
                            <p class="text-xs font-semibold text-slate-600 mb-2">Decision for short qty:</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    wire:click="setBackorderDecision({{ $idx }}, 'repurchase')"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold border-2 transition-all text-left {{ $item['decision'] === 'repurchase' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-400' }}"
                                >
                                    <span class="block text-base leading-none mb-1">🛒</span>
                                    Repurchase
                                    <span class="block font-normal text-[10px] mt-0.5 leading-snug {{ $item['decision'] === 'repurchase' ? 'text-blue-100' : 'text-slate-400' }}">Add to next purchasing plan</span>
                                </button>
                                <button
                                    wire:click="setBackorderDecision({{ $idx }}, 'waitlist')"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold border-2 transition-all text-left {{ $item['decision'] === 'waitlist' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-600 border-slate-200 hover:border-amber-400' }}"
                                >
                                    <span class="block text-base leading-none mb-1">⏳</span>
                                    Waitlist
                                    <span class="block font-normal text-[10px] mt-0.5 leading-snug {{ $item['decision'] === 'waitlist' ? 'text-amber-100' : 'text-slate-400' }}">Hold for next shipment batch</span>
                                </button>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 pb-5 pt-3 flex items-center gap-3 border-t border-slate-100 shrink-0">
                <button @click="backorderOpen = false; $wire.set('showBackorderModal', false)"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="processBackorder"
                        wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="processBackorder">Confirm &amp; Record Backorder</span>
                    <span wire:loading wire:target="processBackorder">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ WHATSAPP SEND PROMPT ══════════════════════ --}}
    <div x-show="waPrompt"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display:none;"
         class="fixed bottom-6 right-6 z-50 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

        {{-- Green header --}}
        <div class="flex items-center gap-3 px-4 py-3" style="background:#25D366">
            <svg class="w-5 h-5 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
            </svg>
            <span class="text-white font-bold text-sm flex-1">Send Confirmation to Customer</span>
            <button @click="waPrompt = false" class="text-white/70 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-4 py-4 space-y-3">
            <p class="text-sm text-slate-600 leading-relaxed">
                Payment confirmed! Click below to open WhatsApp and send the confirmation message to the customer.
            </p>
            <div class="flex gap-2">
                <a :href="waLink"
                   target="_blank"
                   @click="waPrompt = false"
                   class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-white text-sm font-bold transition-opacity hover:opacity-90"
                   style="background:#25D366">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                    </svg>
                    Open WhatsApp
                </a>
                <button @click="waPrompt = false"
                        class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition-colors">
                    Later
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ STOCK ALERT MODAL ══════════════════════ --}}
    @if($showStockAlert)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.6);">

        {{-- ── Main Stock Alert Card ─────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-red-50 border-b border-red-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-[#0F172A]">Insufficient Stock</h3>
                        <p class="text-xs text-slate-500">Pick an action for each short item, then confirm.</p>
                    </div>
                </div>
                <button wire:click="closeStockAlert" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Product cards --}}
            <div class="overflow-y-auto flex-1 px-5 py-4 space-y-3">
                @foreach($stockIssues as $idx => $issue)
                @php
                    $dec = $stockDecisions[$idx] ?? 'next_batch';
                    $replaceProductId = $stockReplaceChoices[$idx] ?? null;
                    $replaceProduct   = $replaceProductId ? \App\Models\Product::find($replaceProductId) : null;
                @endphp
                <div class="rounded-2xl border p-4 transition-colors
                    {{ $dec === 'refund'   ? 'border-red-200 bg-red-50/40'
                     : ($dec === 'replace' ? 'border-orange-200 bg-orange-50/30'
                     :                      'border-amber-200 bg-amber-50/30') }}">

                    {{-- Product name + price --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-semibold text-sm text-[#0F172A]">{{ $issue['name'] }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Rs. {{ number_format($issue['unit_price'], 0) }} per unit</p>
                            @if($dec === 'replace' && $replaceProduct)
                            <div class="flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <span class="text-[11px] font-semibold text-orange-700">{{ $replaceProduct->name }}</span>
                            </div>
                            @endif
                        </div>
                        {{-- Active decision badge --}}
                        @if($dec === 'next_batch')
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wide shrink-0">Backorder</span>
                        @elseif($dec === 'replace')
                        <span class="px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase tracking-wide shrink-0">Replace</span>
                        @else
                        <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wide shrink-0">Refund</span>
                        @endif
                    </div>

                    {{-- Qty pills --}}
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex-1 text-center rounded-xl bg-slate-100 py-2">
                            <p class="text-[9px] text-slate-400 uppercase tracking-wide font-semibold">Ordered</p>
                            <p class="text-base font-bold text-slate-700 leading-tight">{{ $issue['needed'] }}</p>
                        </div>
                        <div class="flex-1 text-center rounded-xl bg-green-50 border border-green-200 py-2">
                            <p class="text-[9px] text-green-500 uppercase tracking-wide font-semibold">In Stock</p>
                            <p class="text-base font-bold text-green-700 leading-tight">{{ $issue['available'] }}</p>
                        </div>
                        <div class="flex-1 text-center rounded-xl bg-red-50 border border-red-200 py-2">
                            <p class="text-[9px] text-red-400 uppercase tracking-wide font-semibold">Short</p>
                            <p class="text-base font-bold text-red-600 leading-tight">-{{ $issue['short'] }}</p>
                        </div>
                    </div>

                    {{-- Three action buttons --}}
                    <div class="grid grid-cols-3 gap-1.5">

                        {{-- Next Batch Order --}}
                        <button wire:click="setStockNextBatch({{ $idx }})"
                                class="flex flex-col items-center gap-1.5 px-2 py-2.5 rounded-xl border-2 transition-all
                                       {{ $dec === 'next_batch'
                                            ? 'border-amber-400 bg-amber-400 text-white shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-500 hover:border-amber-300 hover:bg-amber-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div class="text-center leading-tight">
                                <p class="text-[11px] font-bold">Next Batch</p>
                                <p class="text-[9px] {{ $dec === 'next_batch' ? 'text-amber-100' : 'text-slate-400' }}">Backorder</p>
                            </div>
                        </button>

                        {{-- Replace Product --}}
                        <button wire:click="openStockReplaceModal({{ $idx }})"
                                class="flex flex-col items-center gap-1.5 px-2 py-2.5 rounded-xl border-2 transition-all
                                       {{ $dec === 'replace'
                                            ? 'border-orange-400 bg-orange-500 text-white shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-500 hover:border-orange-300 hover:bg-orange-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <div class="text-center leading-tight">
                                <p class="text-[11px] font-bold">Replace</p>
                                <p class="text-[9px] {{ $dec === 'replace' ? 'text-orange-100' : 'text-slate-400' }}">Substitute</p>
                            </div>
                        </button>

                        {{-- Refund Short --}}
                        <button wire:click="openStockRefundConfirm({{ $idx }})"
                                class="flex flex-col items-center gap-1.5 px-2 py-2.5 rounded-xl border-2 transition-all
                                       {{ $dec === 'refund'
                                            ? 'border-red-400 bg-red-500 text-white shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-500 hover:border-red-300 hover:bg-red-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <div class="text-center leading-tight">
                                <p class="text-[11px] font-bold">Refund</p>
                                <p class="text-[9px] {{ $dec === 'refund' ? 'text-red-100' : 'text-slate-400' }}">Rs. {{ number_format($issue['short_amount'], 0) }}</p>
                            </div>
                        </button>

                    </div>
                </div>
                @endforeach
            </div>

            {{-- Footer summary + apply --}}
            <div class="px-5 pb-5 pt-3 border-t border-slate-100 bg-slate-50/60 shrink-0 space-y-3">
                @php
                    $sumNextBatch   = collect($stockDecisions)->filter(fn($d) => $d === 'next_batch')->count();
                    $sumRefund      = collect($stockDecisions)->filter(fn($d) => $d === 'refund')->count();
                    $sumReplace     = collect($stockDecisions)->filter(fn($d) => $d === 'replace')->count();
                    $totalRefundAmt = 0;
                    foreach ($stockIssues as $i => $iss) {
                        if (($stockDecisions[$i] ?? '') === 'refund') $totalRefundAmt += $iss['short_amount'];
                    }
                @endphp
                <div class="flex items-center gap-2 flex-wrap">
                    @if($sumNextBatch)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $sumNextBatch }} Backorder{{ $sumNextBatch > 1 ? 's' : '' }}
                    </span>
                    @endif
                    @if($sumReplace)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-semibold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ $sumReplace }} Replace{{ $sumReplace > 1 ? 's' : '' }}
                    </span>
                    @endif
                    @if($sumRefund)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Refund Rs. {{ number_format($totalRefundAmt, 0) }}
                    </span>
                    @endif
                </div>

                <div class="flex gap-2">
                    <button wire:click="closeStockAlert"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-white transition-colors">
                        Cancel
                    </button>
                    <button wire:click="applyStockDecisions"
                            wire:loading.attr="disabled"
                            class="flex-1 py-2.5 rounded-xl bg-[#0F172A] text-white text-sm font-bold hover:bg-slate-800 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="applyStockDecisions">Confirm Order</span>
                        <span wire:loading wire:target="applyStockDecisions">Processing…</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Replace Sub-Modal (slides over main card) ────────────── --}}
        @if($showStockReplaceModal && $stockReplaceIdx >= 0)
        @php $ri = $stockIssues[$stockReplaceIdx]; @endphp
        <div class="absolute inset-0 flex items-center justify-center p-4 z-10" style="background:rgba(0,0,0,0.45);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">

                {{-- Sub-header --}}
                <div class="flex items-center gap-3 px-5 py-4 bg-orange-50 border-b border-orange-100">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-[Poppins] font-bold text-sm text-orange-700">Select Replacement Product</h4>
                        <p class="text-xs text-slate-500 truncate max-w-[200px]">For: {{ $ri['name'] }} (×{{ $ri['short'] }})</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="px-5 py-4 space-y-3">
                    {{-- Search --}}
                    <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2 focus-within:ring-2 focus-within:ring-orange-400 focus-within:border-transparent transition-all">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input wire:model.live.debounce.400ms="stockReplaceSearch"
                               type="text"
                               placeholder="Search product name or SKU..."
                               class="bg-transparent text-sm outline-none flex-1 placeholder-slate-400"
                               autofocus>
                    </div>

                    {{-- Product list --}}
                    <div class="max-h-48 overflow-y-auto space-y-1.5">
                        @if(strlen($stockReplaceSearch) >= 2)
                            @forelse($stockReplaceProducts as $rp)
                            <button wire:click="confirmStockReplaceItem({{ $rp->id }})"
                                    type="button"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border border-slate-200 hover:border-orange-300 hover:bg-orange-50 text-left transition-all">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-[#0F172A] truncate">{{ $rp->name }}</p>
                                    @if($rp->sku)
                                    <p class="text-[11px] text-slate-400 font-mono">{{ $rp->sku }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0 ml-3">
                                    <p class="font-bold text-sm text-[#0F172A]">Rs. {{ number_format($rp->price, 0) }}</p>
                                    <p class="text-[11px] {{ $rp->stock >= $ri['short'] ? 'text-green-600' : 'text-red-500' }} font-semibold">
                                        {{ $rp->stock }} in stock
                                    </p>
                                </div>
                            </button>
                            @empty
                            <p class="text-center text-xs text-slate-400 py-4">No products found</p>
                            @endforelse
                        @else
                        <p class="text-center text-xs text-slate-400 py-4">Type at least 2 characters to search</p>
                        @endif
                    </div>
                </div>

                {{-- Sub-footer --}}
                <div class="px-5 pb-4 flex gap-2">
                    <button wire:click="closeStockReplaceModal"
                            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
        @endif

        {{-- ── Refund Sub-Confirm Popup (slides over main card) ────── --}}
        @if($showStockRefundConfirm && $stockRefundConfirmIdx >= 0)
        @php $ri = $stockIssues[$stockRefundConfirmIdx]; @endphp
        <div class="absolute inset-0 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">

                {{-- Sub-header --}}
                <div class="flex items-center gap-3 px-5 py-4 bg-red-50 border-b border-red-100">
                    <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-[Poppins] font-bold text-sm text-red-700">Confirm Partial Refund</h4>
                        <p class="text-xs text-slate-500">{{ $ri['name'] }}</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="px-5 py-5 space-y-4">

                    {{-- Amount breakdown --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200 divide-y divide-slate-200">
                        <div class="flex justify-between items-center px-4 py-2.5 text-sm">
                            <span class="text-slate-500">Short qty</span>
                            <span class="font-bold text-slate-700">{{ $ri['short'] }} unit{{ $ri['short'] > 1 ? 's' : '' }}</span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-2.5 text-sm">
                            <span class="text-slate-500">Unit price</span>
                            <span class="font-bold text-slate-700">Rs. {{ number_format($ri['unit_price'], 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-3 bg-red-50">
                            <span class="font-semibold text-red-700 text-sm">Refund Amount</span>
                            <span class="font-bold text-red-700 text-lg">Rs. {{ number_format($ri['short_amount'], 0) }}</span>
                        </div>
                    </div>

                    {{-- What happens note --}}
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-800 leading-relaxed">
                        The order total will be <strong>reduced by Rs. {{ number_format($ri['short_amount'], 0) }}</strong>.
                        @if($ri['available'] > 0)
                        The remaining <strong>{{ $ri['available'] }} unit{{ $ri['available'] > 1 ? 's' : '' }}</strong> will be delivered.
                        @else
                        This item will be <strong>removed</strong> from the order.
                        @endif
                        Customer's payment for this amount will need to be refunded.
                    </div>
                </div>

                {{-- Sub-footer --}}
                <div class="px-5 pb-5 flex gap-2">
                    <button wire:click="closeStockRefundConfirm"
                            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="confirmStockRefundItem"
                            class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-bold hover:bg-red-600 transition-colors">
                        Confirm Refund
                    </button>
                </div>

            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ══════════════════════ NO PAYMENT ON CONFIRM WARNING ══════════════════════ --}}
    <div x-show="confirmAlert" x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none;background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border-b border-red-100">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-red-800">No Payment Received</h3>
                    <p class="text-xs text-red-500" x-text="'Order ' + (confirmData.orderNum || '')"></p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-3">
                <p class="text-sm text-slate-600">This order has <strong class="text-red-600">no confirmed payment</strong> on record. You should collect payment before confirming.</p>
                <p class="text-xs text-slate-400">Go to the Payments page to confirm the customer's receipt, then come back to confirm the order.</p>
            </div>
            <div class="flex flex-col gap-2 px-6 pb-5">
                <a href="{{ route('admin.payments') }}"
                   class="w-full text-center py-2.5 rounded-xl bg-[#0F172A] hover:bg-slate-700 text-white text-sm font-bold transition-colors">
                    Go to Payments Page
                </a>
                <div class="flex gap-2">
                    <button @click="confirmAlert = false"
                            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="$wire.confirmOrderAnyway(confirmData.orderId); confirmAlert = false"
                            class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition-colors">
                        Confirm Anyway
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ PAYMENT DUE ON DISPATCH WARNING ══════════════════════ --}}
    <div x-show="dispatchAlert" x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none;background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-amber-50 border-b border-amber-100">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-amber-800">Payment Outstanding</h3>
                    <p class="text-xs text-amber-600" x-text="'Order ' + (dispatchData.orderNum || '')"></p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-3">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-[#F8FAFC] rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Order Total</p>
                        <p class="font-bold text-sm text-[#0F172A]">Rs. <span x-text="dispatchData.total ? Number(dispatchData.total).toLocaleString() : '0'"></span></p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Paid</p>
                        <p class="font-bold text-sm text-green-600">Rs. <span x-text="dispatchData.paid ? Number(dispatchData.paid).toLocaleString() : '0'"></span></p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Still Due</p>
                        <p class="font-bold text-sm text-red-600">Rs. <span x-text="dispatchData.due ? Number(dispatchData.due).toLocaleString() : '0'"></span></p>
                    </div>
                </div>
                <p class="text-sm text-[#64748B]">This customer has an outstanding balance. Are you sure you want to dispatch without full payment?</p>
            </div>
            <div class="flex items-center gap-3 px-6 pb-5">
                <button @click="dispatchAlert = false"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Hold — Collect Payment First
                </button>
                <button @click="$wire.forceDispatch(dispatchData.orderId); dispatchAlert = false"
                        class="flex-1 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-bold hover:bg-amber-600 transition-colors">
                    Dispatch Anyway
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ PAYMENT DUE ON DELIVER WARNING ══════════════════════ --}}
    <div x-show="deliverAlert" x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none;background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-amber-50 border-b border-amber-100">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-amber-800">Balance Not Cleared</h3>
                    <p class="text-xs text-amber-600" x-text="'Order ' + (deliverData.orderNum || '')"></p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-3">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-[#F8FAFC] rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Order Total</p>
                        <p class="font-bold text-sm text-[#0F172A]">Rs. <span x-text="deliverData.total ? Number(deliverData.total).toLocaleString() : '0'"></span></p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Paid</p>
                        <p class="font-bold text-sm text-green-600">Rs. <span x-text="deliverData.paid ? Number(deliverData.paid).toLocaleString() : '0'"></span></p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Still Due</p>
                        <p class="font-bold text-sm text-red-600">Rs. <span x-text="deliverData.due ? Number(deliverData.due).toLocaleString() : '0'"></span></p>
                    </div>
                </div>
                <p class="text-sm text-[#64748B]">This customer still has an outstanding balance. Are you sure you want to mark as delivered?</p>
            </div>
            <div class="flex items-center gap-3 px-6 pb-5">
                <button @click="deliverAlert = false"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Hold — Collect Payment First
                </button>
                <button @click="$wire.forceDeliver(deliverData.orderId); deliverAlert = false"
                        class="flex-1 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-bold hover:bg-amber-600 transition-colors">
                    Deliver Anyway
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ PAYMENT DUE ON COMPLETE WARNING ══════════════════════ --}}
    <div x-show="completeAlert" x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none;background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border-b border-red-100">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-red-800">Payment Incomplete</h3>
                    <p class="text-xs text-red-600" x-text="'Order ' + (completeData.orderNum || '')"></p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-3">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-[#F8FAFC] rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Order Total</p>
                        <p class="font-bold text-sm text-[#0F172A]">Rs. <span x-text="completeData.total ? Number(completeData.total).toLocaleString() : '0'"></span></p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Paid</p>
                        <p class="font-bold text-sm text-green-600">Rs. <span x-text="completeData.paid ? Number(completeData.paid).toLocaleString() : '0'"></span></p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Still Due</p>
                        <p class="font-bold text-sm text-red-600">Rs. <span x-text="completeData.due ? Number(completeData.due).toLocaleString() : '0'"></span></p>
                    </div>
                </div>
                <p class="text-sm text-[#64748B]">This order has an unpaid balance. Completing it will mark the payment status as partial. Are you sure?</p>
            </div>
            <div class="flex items-center gap-3 px-6 pb-5">
                <button @click="completeAlert = false"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Hold — Collect Payment First
                </button>
                <button @click="$wire.forceComplete(completeData.orderId); completeAlert = false"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-bold hover:bg-red-600 transition-colors">
                    Complete Anyway
                </button>
            </div>
        </div>
    </div>

</div>
