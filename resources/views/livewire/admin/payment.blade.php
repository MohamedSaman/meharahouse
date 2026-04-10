{{-- resources/views/livewire/admin/payment.blade.php --}}
<div
    x-data="{
        waPrompt: false,
        waLink: '',
        waMsg: '',
        openWhatsapp() { window.open(this.waLink, '_blank'); this.waPrompt = false; }
    }"
    @open-payment-whatsapp.window="
        waMsg  = $event.detail.message;
        waLink = 'https://wa.me/' + $event.detail.phone.replace(/\D/g,'') + '?text=' + encodeURIComponent($event.detail.message);
        waPrompt = true;
    "
    class="space-y-5"
>

    {{-- Header --}}
    <div>
        <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Payment Transactions</h2>
        <p class="text-sm text-[#64748B]">Customer payment records — advance, balance, and completed</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Total Collected</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">Rs. {{ number_format($summary['total_collected'], 2) }}</p>
                <p class="text-xs text-[#94A3B8]">Confirmed payments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Partial Payments</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">{{ $summary['partial_count'] }} orders</p>
                <p class="text-xs text-[#94A3B8]">Advance paid, balance due</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Total Due</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">Rs. {{ number_format($summary['total_due'], 2) }}</p>
                <p class="text-xs text-[#94A3B8]">Outstanding balance</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Awaiting Review</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">Rs. {{ number_format($summary['total_pending_receipts'], 2) }}</p>
                <p class="text-xs text-[#94A3B8]">Receipts not yet confirmed</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Search order # or customer name / phone…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#0F172A] bg-white text-[#0F172A] placeholder-[#94A3B8]">
            </div>
            <select wire:model.live="filterStatus"
                    class="text-sm border border-[#E2E8F0] rounded-lg px-3 py-2 bg-white text-[#475569] focus:outline-none focus:border-[#0F172A] min-w-[180px]">
                <option value="">All Statuses</option>
                <option value="paid">Fully Paid</option>
                <option value="partial">Partial (Advance Paid)</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
            <select wire:model.live="filterMethod"
                    class="text-sm border border-[#E2E8F0] rounded-lg px-3 py-2 bg-white text-[#475569] focus:outline-none focus:border-[#0F172A] min-w-[160px]">
                <option value="">All Methods</option>
                <option value="cash_on_delivery">Cash on Delivery</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="telebirr">Telebirr</option>
                <option value="cbebirr">CBE Birr</option>
                <option value="payhere">PayHere</option>
                <option value="paypal">PayPal</option>
                <option value="stripe">Stripe</option>
            </select>
        </div>
        {{-- Date Range + Presets + Status Tabs — single row --}}
        <div class="flex items-center gap-2 flex-wrap mt-3"
             x-data="{
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
            {{-- Date inputs --}}
            <div class="flex items-center gap-1.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg px-2 py-1.5 shrink-0">
                <svg class="w-4 h-4 text-[#94A3B8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <input wire:model.live="dateFrom" type="date"
                       class="text-xs text-[#475569] bg-transparent border-none outline-none w-28">
                <span class="text-xs text-[#94A3B8]">—</span>
                <input wire:model.live="dateTo" type="date"
                       class="text-xs text-[#475569] bg-transparent border-none outline-none w-28">
            </div>
            {{-- Quick Presets --}}
            <div class="flex gap-1 shrink-0">
                <button @click="today()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Today</button>
                <button @click="last7()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">7d</button>
                <button @click="thisMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Month</button>
                <button @click="lastMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Last Mo</button>
                @if($dateFrom || $dateTo)
                <button wire:click="clearDates"
                        class="px-2 py-1 text-[10px] font-semibold rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition-colors">Clear</button>
                @endif
            </div>
            {{-- Divider --}}
            <span class="h-5 w-px bg-[#E2E8F0] shrink-0"></span>
            {{-- Status Tabs --}}
            @foreach([
                ''         => ['label' => 'All',      'count' => $summary['total_orders']],
                'paid'     => ['label' => 'Paid',      'count' => $summary['count_paid']],
                'partial'  => ['label' => 'Partial',   'count' => $summary['partial_count']],
                'pending'  => ['label' => 'Pending',   'count' => $summary['count_pending']],
                'failed'   => ['label' => 'Failed',    'count' => $summary['count_failed']],
                'refunded' => ['label' => 'Refunded',  'count' => $summary['count_refunded']],
            ] as $val => $tab)
            <button wire:click="$set('filterStatus', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors shrink-0
                    {{ $filterStatus === $val
                        ? 'bg-[#0F172A] text-white border-[#0F172A]'
                        : 'bg-white text-[#64748B] border-[#E2E8F0] hover:border-[#0F172A]' }}">
                {{ $tab['label'] }} <span class="opacity-70">({{ $tab['count'] }})</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Method</th>
                        <th>Order Total</th>
                        <th>Paid</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $order)
                    @php
                        $name  = $order->shipping_address['full_name'] ?? ($order->user?->name ?? 'Guest');
                        $phone = $order->shipping_address['phone'] ?? '';

                        // Use confirmed payments loaded via eager load (only status=confirmed rows)
                        $confirmedTotal = (float) $order->payments->sum('amount');
                        $advancePaid    = (float) $order->payments->where('type', 'advance')->sum('amount');
                        $balancePaid    = (float) $order->payments->where('type', 'balance')->sum('amount');

                        // Use balance_amount from the order record — this is the authoritative
                        // remaining balance kept in sync by payment confirmation logic.
                        $due = max(0, (float) $order->balance_amount);

                        // Derive status label from the stored payment_status field, not
                        // recomputed from payments, to avoid edge-case mismatches.
                        $payLabel = match($order->payment_status) {
                            'paid'     => 'fully_paid',
                            'partial'  => 'partial',
                            'failed'   => 'failed',
                            'refunded' => 'refunded',
                            default    => $confirmedTotal > 0 ? 'partial' : 'pending',
                        };

                        // Treat as fully paid when balance_amount is zero or payment_status is paid
                        if ($due <= 0 || $order->payment_status === 'paid') {
                            $payLabel    = 'fully_paid';
                        }

                        $isFullyPaid = $payLabel === 'fully_paid';
                    @endphp
                    <tr>
                        <td><span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span></td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-[#0F172A]">{{ $name }}</span>
                                @if($phone)<span class="text-xs text-[#94A3B8]">{{ $phone }}</span>@endif
                            </div>
                        </td>
                        <td><span class="badge badge-navy text-xs">{{ ucwords(str_replace('_', ' ', $order->payment_method ?? '—')) }}</span></td>
                        <td><span class="font-semibold text-sm text-[#0F172A]">Rs. {{ number_format($order->total, 2) }}</span></td>
                        <td>
                            @if($confirmedTotal > 0)
                                <span class="text-sm font-semibold {{ $isFullyPaid ? 'text-green-600' : 'text-orange-500' }}">
                                    Rs. {{ number_format($confirmedTotal, 2) }}
                                </span>
                                @if($advancePaid > 0 && $balancePaid > 0)
                                    <div class="text-xs text-[#94A3B8]">Adv + Bal</div>
                                @elseif($balancePaid > 0)
                                    <div class="text-xs text-[#94A3B8]">Balance</div>
                                @elseif($advancePaid > 0)
                                    <div class="text-xs text-[#94A3B8]">Advance only</div>
                                @endif
                            @else
                                <span class="text-xs text-[#94A3B8]">—</span>
                            @endif
                        </td>
                        <td>
                            @if($order->payment_status === 'refunded')
                                <span class="text-xs font-semibold text-purple-500">Refunded</span>
                            @elseif($due > 0)
                                <span class="text-sm font-semibold text-red-500">Rs. {{ number_format($due, 2) }}</span>
                            @else
                                <span class="text-xs font-semibold text-green-500">Cleared</span>
                            @endif
                        </td>
                        <td>
                            @if($order->payment_status === 'refunded')
                                <span class="badge" style="background:#EDE9FE;color:#7C3AED;">Refunded</span>
                            @elseif($isFullyPaid)
                                <span class="badge badge-success">Paid</span>
                            @elseif($payLabel === 'partial')
                                <span class="badge" style="background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA;">Partial</span>
                            @elseif($payLabel === 'failed')
                                <span class="badge badge-danger">Failed</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $order->created_at->format('M d, Y') }}</span></td>

                        {{-- Action Buttons --}}
                        <td>
                            <div class="flex items-center gap-1.5">
                                {{-- Receive Payment — only if balance still due --}}
                                @if(!$isFullyPaid && $payLabel !== 'refunded')
                                <button wire:click="openReceiveModal({{ $order->id }})"
                                        title="Receive Payment"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0F172A] text-white hover:bg-[#1E293B] transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Receive
                                </button>
                                @endif

                                {{-- WhatsApp Reminder — only if has phone and balance due --}}
                                @if($phone && $due > 0)
                                <button wire:click="sendReminder({{ $order->id }})"
                                        title="Send WhatsApp Reminder"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-green-500 text-white hover:bg-green-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.862L.057 23.571a.5.5 0 00.614.614l5.709-1.476A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.943 0-3.76-.524-5.32-1.436l-.38-.225-3.938 1.018 1.018-3.938-.225-.38A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                                    </svg>
                                    Remind
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="flex flex-col items-center py-14 text-[#94A3B8]">
                                <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <p class="text-sm">No payment records found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-4 py-3 border-t border-[#F1F5F9]">{{ $payments->links() }}</div>
        @endif
    </div>

    {{-- ── Receive Payment Modal ─────────────────────────────────────── --}}
    @if($showReceiveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.5);"
         wire:click.self="$set('showReceiveModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" wire:click.stop>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F1F5F9]">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Receive Payment</h3>
                    <p class="text-xs text-[#64748B] mt-0.5">{{ $receiveOrderNumber }} — {{ $receiveCustomerName }}</p>
                </div>
                <button wire:click="$set('showReceiveModal', false)"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-[#94A3B8] hover:bg-[#F1F5F9]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Balance Summary --}}
            <div class="px-6 pt-5">
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="bg-[#F8FAFC] rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Order Total</p>
                        <p class="font-bold text-sm text-[#0F172A]">Rs. {{ number_format($receiveOrderTotal, 2) }}</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Paid So Far</p>
                        <p class="font-bold text-sm text-green-600">Rs. {{ number_format($receiveOrderTotal - $receiveBalanceDue, 2) }}</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-[#94A3B8] mb-1">Balance Due</p>
                        <p class="font-bold text-sm text-red-500">Rs. {{ number_format($receiveBalanceDue, 2) }}</p>
                    </div>
                </div>

                {{-- Form --}}
                <div class="space-y-4">
                    {{-- Amount Received --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#475569] mb-1.5 uppercase tracking-wide">Amount Received (Rs.)</label>
                        <input wire:model.live="receiveAmount"
                               type="number"
                               step="0.01"
                               min="0.01"
                               max="{{ $receiveBalanceDue }}"
                               placeholder="Enter amount…"
                               class="w-full px-4 py-2.5 text-sm border border-[#E2E8F0] rounded-xl focus:outline-none focus:border-[#0F172A] font-semibold text-[#0F172A]">
                        @error('receiveAmount')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Remaining after entry --}}
                        @if($receiveAmount && is_numeric($receiveAmount) && $receiveAmount > 0)
                        @php $stillDue = max(0, $receiveBalanceDue - floatval($receiveAmount)); @endphp
                        <div class="mt-2 flex items-center gap-2">
                            @if($stillDue > 0)
                            <span class="text-xs text-orange-600 font-semibold">Still due after this: Rs. {{ number_format($stillDue, 2) }}</span>
                            @else
                            <span class="text-xs text-green-600 font-semibold">✓ Fully paid after this payment</span>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#475569] mb-1.5 uppercase tracking-wide">Payment Method</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['bank_transfer' => 'Bank Transfer', 'online' => 'Online', 'cash' => 'Cash'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer border rounded-xl px-3 py-2.5 transition-colors
                                {{ $receiveMethod === $val ? 'border-[#0F172A] bg-[#0F172A]' : 'border-[#E2E8F0] hover:border-[#CBD5E1]' }}">
                                <input type="radio" wire:model.live="receiveMethod" value="{{ $val }}" class="sr-only">
                                <span class="text-xs font-semibold {{ $receiveMethod === $val ? 'text-white' : 'text-[#475569]' }}">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Reference --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#475569] mb-1.5 uppercase tracking-wide">Reference / Transaction ID <span class="normal-case text-[#94A3B8] font-normal">(optional)</span></label>
                        <input wire:model="receiveReference"
                               type="text"
                               placeholder="Bank ref, receipt #…"
                               class="w-full px-4 py-2.5 text-sm border border-[#E2E8F0] rounded-xl focus:outline-none focus:border-[#0F172A] text-[#0F172A]">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#475569] mb-1.5 uppercase tracking-wide">Notes <span class="normal-case text-[#94A3B8] font-normal">(optional)</span></label>
                        <textarea wire:model="receiveNotes" rows="2"
                                  placeholder="Any note about this payment…"
                                  class="w-full px-4 py-2.5 text-sm border border-[#E2E8F0] rounded-xl focus:outline-none focus:border-[#0F172A] text-[#0F172A] resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center gap-3 px-6 py-4 mt-2 border-t border-[#F1F5F9]">
                <button wire:click="recordPayment"
                        wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-[#0F172A] text-white text-sm font-bold hover:bg-[#1E293B] transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="recordPayment">Confirm Payment</span>
                    <span wire:loading wire:target="recordPayment">Saving…</span>
                </button>
                <button wire:click="$set('showReceiveModal', false)"
                        class="px-5 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── WhatsApp Reminder Popup ──────────────────────────────────── --}}
    <div x-show="waPrompt" x-cloak style="display:none;"
         class="fixed bottom-6 right-6 z-50 w-80 bg-white rounded-2xl shadow-2xl border border-[#E2E8F0] overflow-hidden">
        <div class="bg-[#25D366] px-4 py-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.862L.057 23.571a.5.5 0 00.614.614l5.709-1.476A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.943 0-3.76-.524-5.32-1.436l-.38-.225-3.938 1.018 1.018-3.938-.225-.38A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
            </svg>
            <span class="text-white font-bold text-sm">Send Balance Reminder</span>
        </div>
        <div class="p-4">
            <p class="text-xs text-[#475569] mb-3">WhatsApp message is ready to send to the customer.</p>
            <div class="bg-[#F0FDF4] rounded-lg p-3 mb-4 max-h-32 overflow-y-auto">
                <p class="text-xs text-[#166534] whitespace-pre-line" x-text="waMsg"></p>
            </div>
            <div class="flex gap-2">
                <button @click="openWhatsapp()"
                        class="flex-1 py-2 rounded-xl bg-[#0F172A] text-white text-sm font-bold hover:bg-[#1E293B] transition-colors">
                    Open WhatsApp
                </button>
                <button @click="waPrompt = false"
                        class="px-4 py-2 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Later
                </button>
            </div>
        </div>
    </div>

</div>
