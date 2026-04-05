{{-- resources/views/livewire/admin/customer.blade.php --}}
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Customers</h2>
            <p class="text-sm text-[#64748B]">4,831 registered customers</p>
        </div>
        <button class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $customers = [
                        ['name' => 'Selam Tadesse', 'email' => 'selam@email.com', 'phone' => '+251 911 001 001', 'orders' => 12, 'spent' => '18,420', 'joined' => 'Jan 2024', 'active' => true],
                        ['name' => 'Yonas Bekele', 'email' => 'yonas@email.com', 'phone' => '+251 922 002 002', 'orders' => 7, 'spent' => '9,840', 'joined' => 'Mar 2024', 'active' => true],
                        ['name' => 'Hana Girma', 'email' => 'hana@email.com', 'phone' => '+251 933 003 003', 'orders' => 3, 'spent' => '4,200', 'joined' => 'Jun 2024', 'active' => false],
                        ['name' => 'Dawit Alemu', 'email' => 'dawit@email.com', 'phone' => '+251 944 004 004', 'orders' => 22, 'spent' => '41,500', 'joined' => 'Aug 2023', 'active' => true],
                        ['name' => 'Mekdes Fikre', 'email' => 'mekdes@email.com', 'phone' => '+251 955 005 005', 'orders' => 5, 'spent' => '7,200', 'joined' => 'Nov 2023', 'active' => true],
                    ];
                    @endphp
                    @foreach($customers as $c)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#0F172A] to-[#334155] flex items-center justify-center shrink-0">
                                    <span class="text-[#F59E0B] text-xs font-bold">{{ substr($c['name'], 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-semibold text-[#0F172A]">{{ $c['name'] }}</span>
                            </div>
                        </td>
                        <td><span class="text-sm text-[#475569]">{{ $c['email'] }}</span></td>
                        <td><span class="text-sm text-[#475569]">{{ $c['phone'] }}</span></td>
                        <td><span class="text-sm font-semibold text-[#0F172A]">{{ $c['orders'] }}</span></td>
                        <td><span class="text-sm font-semibold text-[#0F172A]">ETB {{ $c['spent'] }}</span></td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $c['joined'] }}</span></td>
                        <td><span class="badge {{ $c['active'] ? 'badge-success' : 'badge-navy' }}">{{ $c['active'] ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
