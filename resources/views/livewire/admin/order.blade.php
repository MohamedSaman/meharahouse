{{-- resources/views/livewire/admin/order.blade.php --}}
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">All Orders</h2>
            <p class="text-sm text-[#64748B]">Manage and track all customer orders</p>
        </div>
        <div class="flex gap-2">
            <button class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search orders..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
        </div>
        <select class="form-input text-sm py-2 w-auto">
            <option>All Statuses</option>
            <option>Pending</option>
            <option>Processing</option>
            <option>Delivered</option>
            <option>Cancelled</option>
        </select>
        <input type="date" class="form-input text-sm py-2 w-auto">
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="rounded w-4 h-4"></th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $orders = [
                        ['id' => '#ORD-1024', 'customer' => 'Selam Tadesse', 'items' => 3, 'amount' => '4,200', 'payment' => 'CBE Birr', 'status' => 'Delivered', 'sc' => 'badge-success', 'date' => 'Apr 5, 2026'],
                        ['id' => '#ORD-1023', 'customer' => 'Yonas Bekele', 'items' => 1, 'amount' => '1,850', 'payment' => 'Telebirr', 'status' => 'Processing', 'sc' => 'badge-info', 'date' => 'Apr 5, 2026'],
                        ['id' => '#ORD-1022', 'customer' => 'Hana Girma', 'items' => 2, 'amount' => '2,750', 'payment' => 'Cash', 'status' => 'Pending', 'sc' => 'badge-warning', 'date' => 'Apr 5, 2026'],
                        ['id' => '#ORD-1021', 'customer' => 'Dawit Alemu', 'items' => 5, 'amount' => '8,400', 'payment' => 'CBE Birr', 'status' => 'Delivered', 'sc' => 'badge-success', 'date' => 'Apr 4, 2026'],
                        ['id' => '#ORD-1020', 'customer' => 'Mekdes Fikre', 'items' => 2, 'amount' => '3,100', 'payment' => 'Telebirr', 'status' => 'Cancelled', 'sc' => 'badge-danger', 'date' => 'Apr 4, 2026'],
                    ];
                    @endphp
                    @foreach($orders as $order)
                    <tr>
                        <td><input type="checkbox" class="rounded w-4 h-4"></td>
                        <td><span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order['id'] }}</span></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#0F172A] flex items-center justify-center shrink-0">
                                    <span class="text-[#F59E0B] text-[10px] font-bold">{{ substr($order['customer'], 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-medium text-[#0F172A]">{{ $order['customer'] }}</span>
                            </div>
                        </td>
                        <td><span class="text-sm text-[#475569]">{{ $order['items'] }}</span></td>
                        <td><span class="font-semibold text-sm text-[#0F172A]">ETB {{ $order['amount'] }}</span></td>
                        <td><span class="text-xs text-[#64748B]">{{ $order['payment'] }}</span></td>
                        <td><span class="badge {{ $order['sc'] }}">{{ $order['status'] }}</span></td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $order['date'] }}</span></td>
                        <td>
                            <div class="flex gap-1">
                                <button class="p-1.5 rounded-lg text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0F172A] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button class="p-1.5 rounded-lg text-[#64748B] hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3.5 border-t border-[#F1F5F9] flex items-center justify-between">
            <p class="text-xs text-[#94A3B8]">Showing 5 of 1,248 orders</p>
            <div class="flex gap-1">
                <button class="px-3 py-1.5 rounded-lg border border-[#E2E8F0] text-[#64748B] text-xs hover:bg-[#F1F5F9]">Prev</button>
                <button class="px-3 py-1.5 rounded-lg bg-[#0F172A] text-white text-xs font-bold">1</button>
                <button class="px-3 py-1.5 rounded-lg border border-[#E2E8F0] text-[#64748B] text-xs hover:bg-[#F1F5F9]">2</button>
                <button class="px-3 py-1.5 rounded-lg border border-[#E2E8F0] text-[#64748B] text-xs hover:bg-[#F1F5F9]">Next</button>
            </div>
        </div>
    </div>
</div>
