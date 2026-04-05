{{-- resources/views/livewire/admin/payment.blade.php --}}
<div class="space-y-5">
    <div>
        <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Payment Transactions</h2>
        <p class="text-sm text-[#64748B]">All payment records and statuses</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach([['label' => 'Total Collected', 'value' => 'Rs. 284,500', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-[#FFFBEB] text-[#F59E0B]'], ['label' => 'Pending Payments', 'value' => 'Rs. 12,400', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-yellow-50 text-yellow-600'], ['label' => 'Failed Transactions', 'value' => '14', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-red-50 text-red-600']] as $card)
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl {{ $card['bg'] }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider">{{ $card['label'] }}</p>
                <p class="font-[Poppins] font-bold text-lg text-[#0F172A]">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $payments = [
                        ['txn' => 'TXN-8821', 'order' => '#ORD-1024', 'customer' => 'Selam Tadesse', 'method' => 'CBE Birr', 'amount' => '4,200', 'status' => 'Completed', 'sc' => 'badge-success', 'date' => 'Apr 5, 2026'],
                        ['txn' => 'TXN-8820', 'order' => '#ORD-1023', 'customer' => 'Yonas Bekele', 'method' => 'Telebirr', 'amount' => '1,850', 'sc' => 'badge-warning', 'status' => 'Pending', 'date' => 'Apr 5, 2026'],
                        ['txn' => 'TXN-8819', 'order' => '#ORD-1022', 'customer' => 'Hana Girma', 'method' => 'Cash', 'amount' => '2,750', 'status' => 'Completed', 'sc' => 'badge-success', 'date' => 'Apr 5, 2026'],
                        ['txn' => 'TXN-8818', 'order' => '#ORD-1021', 'customer' => 'Dawit Alemu', 'method' => 'CBE Birr', 'amount' => '8,400', 'status' => 'Completed', 'sc' => 'badge-success', 'date' => 'Apr 4, 2026'],
                        ['txn' => 'TXN-8817', 'order' => '#ORD-1020', 'customer' => 'Mekdes Fikre', 'method' => 'Telebirr', 'amount' => '3,100', 'status' => 'Failed', 'sc' => 'badge-danger', 'date' => 'Apr 4, 2026'],
                    ];
                    @endphp
                    @foreach($payments as $p)
                    <tr>
                        <td><span class="font-mono text-xs font-bold text-[#0F172A]">{{ $p['txn'] }}</span></td>
                        <td><span class="font-mono text-xs text-[#64748B]">{{ $p['order'] }}</span></td>
                        <td><span class="text-sm text-[#475569]">{{ $p['customer'] }}</span></td>
                        <td><span class="badge badge-navy">{{ $p['method'] }}</span></td>
                        <td><span class="font-semibold text-sm text-[#0F172A]">Rs. {{ $p['amount'] }}</span></td>
                        <td><span class="badge {{ $p['sc'] }}">{{ $p['status'] }}</span></td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $p['date'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
