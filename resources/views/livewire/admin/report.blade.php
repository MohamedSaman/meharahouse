{{-- resources/views/livewire/admin/report.blade.php --}}
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Reports & Analytics</h2>
            <p class="text-sm text-[#64748B]">Business insights and performance data</p>
        </div>
        <div class="flex gap-2">
            <select class="form-input text-sm py-2 w-auto">
                <option>Last 30 Days</option>
                <option>Last 90 Days</option>
                <option>This Year</option>
                <option>Custom Range</option>
            </select>
            <button class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export PDF
            </button>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Revenue', 'value' => 'Rs. 284,500', 'change' => '+12.5%', 'up' => true],
            ['label' => 'Orders Placed', 'value' => '1,248', 'change' => '+8.2%', 'up' => true],
            ['label' => 'Avg. Order Value', 'value' => 'Rs. 228', 'change' => '+3.1%', 'up' => true],
            ['label' => 'Return Rate', 'value' => '2.4%', 'change' => '-0.8%', 'up' => true],
        ] as $kpi)
        <div class="card p-5">
            <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider mb-2">{{ $kpi['label'] }}</p>
            <p class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-1">{{ $kpi['value'] }}</p>
            <span class="text-xs font-bold text-green-600">{{ $kpi['change'] }} vs last period</span>
        </div>
        @endforeach
    </div>

    {{-- Top Products --}}
    <div class="card p-6">
        <h3 class="font-[Poppins] font-bold text-[#0F172A] mb-4">Top Selling Products</h3>
        <div class="space-y-4">
            @php
            $top = [
                ['name' => 'Smart Watch Pro', 'sales' => 203, 'revenue' => '852,600', 'pct' => 82],
                ['name' => 'Premium Headphones', 'sales' => 178, 'revenue' => '444,822', 'pct' => 72],
                ['name' => 'Leather Weekend Bag', 'sales' => 124, 'revenue' => '229,400', 'pct' => 50],
                ['name' => 'Natural Skincare Set', 'sales' => 91, 'revenue' => '80,990', 'pct' => 37],
                ['name' => 'Bluetooth Speaker', 'sales' => 87, 'revenue' => '117,450', 'pct' => 35],
            ];
            @endphp
            @foreach($top as $i => $item)
            <div class="flex items-center gap-4">
                <span class="w-6 h-6 rounded-full bg-[#F1F5F9] flex items-center justify-center text-xs font-bold text-[#64748B] shrink-0">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-semibold text-[#0F172A] truncate">{{ $item['name'] }}</span>
                        <span class="text-xs text-[#64748B] ml-4 shrink-0">{{ $item['sales'] }} sold</span>
                    </div>
                    <div class="w-full h-1.5 bg-[#F1F5F9] rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-[#F59E0B] to-[#FBBF24]" style="width: {{ $item['pct'] }}%"></div>
                    </div>
                </div>
                <span class="text-xs font-bold text-[#0F172A] shrink-0 hidden sm:block">Rs. {{ $item['revenue'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
