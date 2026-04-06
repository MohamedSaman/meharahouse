{{-- resources/views/livewire/admin/payment.blade.php --}}
<div class="space-y-5">

    {{-- Header --}}
    <div>
        <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Payment Transactions</h2>
        <p class="text-sm text-[#64748B]">Customer payment records — advance, balance, and completed</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        {{-- Total Collected --}}
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Total Collected</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">Rs. {{ number_format($summary['total_collected'], 2) }}</p>
                <p class="text-xs text-[#94A3B8]">Confirmed payments</p>
            </div>
        </div>

        {{-- Partial / Advance Paid --}}
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Partial Payments</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">{{ $summary['partial_count'] }} orders</p>
                <p class="text-xs text-[#94A3B8]">Advance paid, balance due</p>
            </div>
        </div>

        {{-- Total Due --}}
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Total Due</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">Rs. {{ number_format($summary['total_due'], 2) }}</p>
                <p class="text-xs text-[#94A3B8]">Outstanding balance</p>
            </div>
        </div>

        {{-- Pending Receipts --}}
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">Awaiting Confirmation</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">Rs. {{ number_format($summary['total_pending_receipts'], 2) }}</p>
                <p class="text-xs text-[#94A3B8]">Receipts not yet reviewed</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Search order # or customer name / phone…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#0F172A] bg-white text-[#0F172A] placeholder-[#94A3B8]">
            </div>

            {{-- Status Filter --}}
            <select wire:model.live="filterStatus"
                    class="text-sm border border-[#E2E8F0] rounded-lg px-3 py-2 bg-white text-[#475569] focus:outline-none focus:border-[#0F172A] min-w-[170px]">
                <option value="">All Statuses</option>
                <option value="paid">Fully Paid</option>
                <option value="partial">Partial (Advance Paid)</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>

            {{-- Method Filter --}}
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

        {{-- Quick Tabs --}}
        <div class="flex gap-2 flex-wrap mt-3">
            @foreach([
                ''         => ['label' => 'All', 'count' => $summary['total_orders']],
                'paid'     => ['label' => 'Paid',     'count' => $summary['count_paid']],
                'partial'  => ['label' => 'Partial',  'count' => $summary['partial_count']],
                'pending'  => ['label' => 'Pending',  'count' => $summary['count_pending']],
                'failed'   => ['label' => 'Failed',   'count' => $summary['count_failed']],
                'refunded' => ['label' => 'Refunded', 'count' => $summary['count_refunded']],
            ] as $val => $tab)
            <button wire:click="$set('filterStatus', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                    {{ $filterStatus === $val
                        ? 'bg-[#0F172A] text-white border-[#0F172A]'
                        : 'bg-white text-[#64748B] border-[#E2E8F0] hover:border-[#0F172A]' }}">
                {{ $tab['label'] }}
                <span class="ml-1 opacity-70">({{ $tab['count'] }})</span>
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
                        <th>Advance Paid</th>
                        <th>Balance Due</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $order)
                    @php
                        $name  = $order->shipping_address['full_name'] ?? ($order->user?->name ?? 'Guest');
                        $phone = $order->shipping_address['phone'] ?? '';

                        // Sum all confirmed payments (advance + balance) for this order
                        $confirmedTotal = $order->payments->sum('amount');

                        // Advance paid
                        $advancePaid = $order->payments->where('type', 'advance')->sum('amount');

                        // Balance confirmed paid
                        $balancePaid = $order->payments->where('type', 'balance')->sum('amount');

                        // Total paid so far
                        $totalPaid = $confirmedTotal;

                        // Due = total - everything confirmed paid
                        $due = max(0, (float)$order->total - $totalPaid);

                        // Determine payment label
                        if ($order->payment_status === 'paid' || $due <= 0) {
                            $payLabel = 'fully_paid';
                        } elseif ($totalPaid > 0) {
                            $payLabel = 'partial';
                        } elseif ($order->payment_status === 'failed') {
                            $payLabel = 'failed';
                        } elseif ($order->payment_status === 'refunded') {
                            $payLabel = 'refunded';
                        } else {
                            $payLabel = 'pending';
                        }
                    @endphp
                    <tr>
                        {{-- Order # --}}
                        <td>
                            <span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span>
                        </td>

                        {{-- Customer --}}
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-[#0F172A]">{{ $name }}</span>
                                @if($phone)
                                <span class="text-xs text-[#94A3B8]">{{ $phone }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Method --}}
                        <td>
                            <span class="badge badge-navy text-xs">
                                {{ ucwords(str_replace('_', ' ', $order->payment_method ?? '—')) }}
                            </span>
                        </td>

                        {{-- Order Total --}}
                        <td>
                            <span class="font-semibold text-sm text-[#0F172A]">
                                Rs. {{ number_format($order->total, 2) }}
                            </span>
                        </td>

                        {{-- Advance / Amount Paid --}}
                        <td>
                            @if($totalPaid > 0)
                                <span class="text-sm font-semibold {{ $payLabel === 'fully_paid' ? 'text-green-600' : 'text-orange-500' }}">
                                    Rs. {{ number_format($totalPaid, 2) }}
                                </span>
                                @if($advancePaid > 0 && $balancePaid > 0)
                                <div class="text-xs text-[#94A3B8]">Adv + Balance</div>
                                @elseif($advancePaid > 0)
                                <div class="text-xs text-[#94A3B8]">Advance</div>
                                @endif
                            @else
                                <span class="text-xs text-[#94A3B8]">—</span>
                            @endif
                        </td>

                        {{-- Balance Due --}}
                        <td>
                            @if($due > 0)
                                <span class="text-sm font-semibold text-red-500">
                                    Rs. {{ number_format($due, 2) }}
                                </span>
                            @else
                                <span class="text-xs font-semibold text-green-500">Cleared</span>
                            @endif
                        </td>

                        {{-- Payment Status Badge --}}
                        <td>
                            @if($payLabel === 'fully_paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($payLabel === 'partial')
                                <span class="badge" style="background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA;">
                                    Partial
                                </span>
                            @elseif($payLabel === 'failed')
                                <span class="badge badge-danger">Failed</span>
                            @elseif($payLabel === 'refunded')
                                <span class="badge" style="background:#EDE9FE;color:#7C3AED;">Refunded</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td>
                            <span class="text-xs text-[#94A3B8]">{{ $order->created_at->format('M d, Y') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="flex flex-col items-center py-14 text-[#94A3B8]">
                                <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <p class="text-sm">No payment records found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->hasPages())
        <div class="px-4 py-3 border-t border-[#F1F5F9]">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

</div>
