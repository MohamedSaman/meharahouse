{{-- resources/views/livewire/admin/order.blade.php --}}
<div
    class="space-y-5"
    x-data="{
        detailOpen: @entangle('showDetail'),
        refundOpen: @entangle('showRefundModal'),
        waPrompt: false,
        waLink: '',
        copyDone: false,
        dispatchAlert: false,
        dispatchData: {},
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
    @payment-due-on-dispatch.window="dispatchAlert = true; dispatchData = $event.detail[0]"
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

                    // Check for pending receipt
                    $pendingReceipt = $order->payments()->where('status', 'pending')->whereNotNull('receipt_path')->first();
                    $hasConfirmedAdvance = $order->payments()->where('type', 'advance')->where('status', 'confirmed')->exists();
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
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 hover:-translate-y-0.5" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>

                                {{-- Status-based quick actions --}}
                                @if($order->status === 'new' && $pendingReceipt)
                                <button wire:click="confirmPayment({{ $pendingReceipt->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-all hover:-translate-y-0.5">
                                    Confirm Receipt
                                </button>
                                @endif

                                @if($order->status === 'payment_received')
                                <button wire:click="confirmOrder({{ $order->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-all hover:-translate-y-0.5">
                                    Confirm Order
                                </button>
                                @endif

                                @if($order->status === 'confirmed' && $order->supplier_status === 'none')
                                <button wire:click="markSourcing({{ $order->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 transition-all hover:-translate-y-0.5">
                                    Start Sourcing
                                </button>
                                @endif

                                @if($order->status === 'confirmed' || ($order->status === 'sourcing' && $order->supplier_status === 'received'))
                                <button wire:click="markDispatched({{ $order->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-all hover:-translate-y-0.5">
                                    Dispatch
                                </button>
                                @endif

                                @if($order->status === 'sourcing' && $order->supplier_status === 'ordered')
                                <button wire:click="markSupplierReceived({{ $order->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 transition-all hover:-translate-y-0.5">
                                    Stock Received
                                </button>
                                <button wire:click="markSupplierUnavailable({{ $order->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition-all hover:-translate-y-0.5">
                                    Unavailable
                                </button>
                                @endif

                                @if($order->status === 'dispatched')
                                <button wire:click="markDelivered({{ $order->id }})"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 transition-all hover:-translate-y-0.5">
                                    Mark Delivered
                                </button>
                                @endif

                                @if($order->status === 'delivered')
                                    @if($order->isWhatsapp())
                                    <button wire:click="sendBalanceReminder({{ $order->id }})"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-all hover:-translate-y-0.5">
                                        Balance Reminder
                                    </button>
                                    @endif
                                    <button wire:click="markCompleted({{ $order->id }})"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-all hover:-translate-y-0.5">
                                        Mark Completed
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
                <h4 class="font-semibold text-sm text-[#0F172A] mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Status History
                </h4>
                @if($selectedOrder->statusLogs->isNotEmpty())
                <div class="relative pl-5 space-y-4">
                    <div class="absolute top-0 bottom-0 left-2 w-0.5 bg-slate-100"></div>
                    @foreach($selectedOrder->statusLogs as $log)
                    <div class="relative">
                        <div class="absolute -left-3 top-1 w-4 h-4 rounded-full bg-white border-2 border-amber-400 flex items-center justify-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        </div>
                        <div class="pl-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($log->from_status)
                                <span class="text-xs text-slate-400">{{ ucfirst(str_replace('_', ' ', $log->from_status)) }}</span>
                                <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                @endif
                                <span class="text-xs font-bold text-[#0F172A]">{{ ucfirst(str_replace('_', ' ', $log->to_status)) }}</span>
                            </div>
                            @if($log->notes)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $log->notes }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400">
                                <span>{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                @if($log->createdBy)
                                <span>&middot; {{ $log->createdBy->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-slate-400 italic">No status history recorded.</p>
                @endif
            </div>

            {{-- ── Payments Section ── --}}
            <div>
                <h4 class="font-semibold text-sm text-[#0F172A] mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Payments
                </h4>

                {{-- Payment summary bar --}}
                <div class="bg-slate-900 rounded-xl p-4 mb-4 grid grid-cols-3 gap-3 text-center">
                    <div>
                        <p class="text-xs text-slate-400">Total</p>
                        <p class="font-bold text-white text-sm mt-0.5">Rs. {{ number_format($selectedOrder->total, 0) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Advance</p>
                        <p class="font-bold text-amber-400 text-sm mt-0.5">Rs. {{ number_format($selectedOrder->advance_amount, 0) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Balance Due</p>
                        <p class="font-bold text-{{ $selectedOrder->balanceDue() > 0 ? 'red' : 'emerald' }}-400 text-sm mt-0.5">
                            Rs. {{ number_format($selectedOrder->balanceDue(), 0) }}
                        </p>
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
                                @if($payment->status === 'pending')
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

                @if($selectedOrder->refund_option === 'refund' && !$selectedOrder->refund)
                <div class="mt-3 pt-3 border-t border-orange-200">
                    <button wire:click="openRefundModal({{ $selectedOrder->id }})"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Process Refund
                    </button>
                </div>
                @endif

                @if($selectedOrder->refund)
                <div class="mt-3 rounded-lg bg-red-50 border border-red-200 p-3 text-xs text-red-800">
                    <p class="font-bold">Refund Processed</p>
                    <p class="mt-0.5">Amount: Rs. {{ number_format($selectedOrder->refund->amount, 0) }} via {{ ucfirst(str_replace('_', ' ', $selectedOrder->refund->method)) }}</p>
                    @if($selectedOrder->refund->reference)
                    <p class="font-mono mt-0.5">Ref: {{ $selectedOrder->refund->reference }}</p>
                    @endif
                    <p class="mt-0.5 text-red-600">{{ $selectedOrder->refund->processed_at?->format('d M Y') }}</p>
                </div>
                @endif
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
                    <div class="flex items-center justify-between py-2.5 border-b border-[#F1F5F9] last:border-0">
                        <div>
                            <p class="text-sm font-medium text-[#0F172A]">{{ $item->product_name }}</p>
                            <p class="text-xs text-[#64748B]">Rs. {{ number_format($item->price, 0) }} &times; {{ $item->quantity }}</p>
                        </div>
                        <span class="font-semibold text-sm text-[#0F172A]">Rs. {{ number_format($item->subtotal, 0) }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm mt-3">
                    <div class="flex justify-between"><span class="text-[#64748B]">Subtotal</span><span>Rs. {{ number_format($selectedOrder->subtotal, 0) }}</span></div>
                    @if($selectedOrder->shipping_cost > 0)
                    <div class="flex justify-between"><span class="text-[#64748B]">Shipping</span><span>Rs. {{ number_format($selectedOrder->shipping_cost, 0) }}</span></div>
                    @endif
                    @if($selectedOrder->tax > 0)
                    <div class="flex justify-between"><span class="text-[#64748B]">Tax</span><span>Rs. {{ number_format($selectedOrder->tax, 0) }}</span></div>
                    @endif
                    @if($selectedOrder->discount > 0)
                    <div class="flex justify-between text-green-600"><span>Discount</span><span>-Rs. {{ number_format($selectedOrder->discount, 0) }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-base border-t border-[#E2E8F0] pt-2">
                        <span class="text-[#0F172A]">Total</span>
                        <span class="text-[#0F172A]">Rs. {{ number_format($selectedOrder->total, 0) }}</span>
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
            <a href="{{ route('admin.order.waybill', $selectedOrder) }}"
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
        @click.self="refundOpen = false; $wire.set('showRefundModal', false)"
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
                <h3 class="font-[Poppins] font-bold text-[#0F172A]">Process Refund</h3>
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
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Refund Method</label>
                    <select wire:model="refundMethod" class="form-input w-full">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Reference / Transaction ID <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input wire:model="refundReference" type="text" class="form-input w-full" placeholder="Bank reference or transaction ID">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea wire:model="refundNotes" rows="3" class="form-input w-full resize-none" placeholder="Reason for refund..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <button @click="refundOpen = false; $wire.set('showRefundModal', false)"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                <button wire:click="processRefund"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Process Refund
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

    {{-- ── Stock Alert Modal ─────────────────────────────────────────── --}}
    @if($showStockAlert)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border-b border-red-100">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-red-700">Insufficient Stock</h3>
                    <p class="text-xs text-red-500">Cannot confirm order — some products are out of stock</p>
                </div>
            </div>

            {{-- Stock Issues List --}}
            <div class="px-6 py-5 space-y-3">
                @foreach($stockIssues as $issue)
                <div class="flex items-center justify-between bg-red-50 rounded-xl px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-[#0F172A]">{{ $issue['name'] }}</p>
                        <p class="text-xs text-[#64748B] mt-0.5">
                            Needed: <span class="font-bold text-red-600">{{ $issue['needed'] }}</span>
                            &nbsp;·&nbsp;
                            In stock: <span class="font-bold text-[#0F172A]">{{ $issue['available'] }}</span>
                        </p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                @endforeach

                <p class="text-xs text-[#64748B] pt-1">
                    You can <strong>restock the products</strong> first, or <strong>force-confirm</strong> to proceed anyway (stock will go negative).
                </p>
            </div>

            {{-- Footer --}}
            <div class="flex items-center gap-3 px-6 pb-5">
                <button wire:click="closeStockAlert"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Cancel
                </button>
                <button wire:click="forceConfirmOrder"
                        class="flex-1 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition-colors">
                    Force Confirm Anyway
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════ PAYMENT DUE ON DISPATCH WARNING ══════════════════════ --}}
    <div x-show="dispatchAlert" x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.55);">
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

</div>
