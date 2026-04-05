{{-- resources/views/livewire/staff/customer.blade.php --}}
<div class="space-y-5">
    <div>
        <h2 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Customer Lookup</h2>
        <p class="text-sm text-[#64748B]">Search and view customer order history</p>
    </div>

    <div class="card p-4">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2.5 max-w-md">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search by name, email, or phone..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Last Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $customers = [
                        ['name' => 'Selam Tadesse', 'phone' => '+251 911 001 001', 'orders' => 12, 'last' => 'Apr 5, 2026'],
                        ['name' => 'Yonas Bekele', 'phone' => '+251 922 002 002', 'orders' => 7, 'last' => 'Apr 5, 2026'],
                        ['name' => 'Hana Girma', 'phone' => '+251 933 003 003', 'orders' => 3, 'last' => 'Apr 3, 2026'],
                        ['name' => 'Dawit Alemu', 'phone' => '+251 944 004 004', 'orders' => 22, 'last' => 'Apr 4, 2026'],
                        ['name' => 'Mekdes Fikre', 'phone' => '+251 955 005 005', 'orders' => 5, 'last' => 'Apr 4, 2026'],
                    ];
                    @endphp
                    @foreach($customers as $c)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#134e4a] flex items-center justify-center shrink-0">
                                    <span class="text-teal-300 text-xs font-bold">{{ substr($c['name'], 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-semibold text-[#0F172A]">{{ $c['name'] }}</span>
                            </div>
                        </td>
                        <td><span class="text-sm text-[#475569]">{{ $c['phone'] }}</span></td>
                        <td><span class="font-semibold text-sm text-[#0F172A]">{{ $c['orders'] }}</span></td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $c['last'] }}</span></td>
                        <td>
                            <button class="px-3 py-1.5 text-xs font-semibold bg-[#F1F5F9] text-[#475569] rounded-lg hover:bg-[#E2E8F0] transition-colors">
                                View Orders
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
