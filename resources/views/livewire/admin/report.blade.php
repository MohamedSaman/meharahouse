{{-- resources/views/livewire/admin/report.blade.php --}}
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER — dark gradient banner
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6">
        <div class="absolute -top-16 -right-12 h-44 w-44 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-8 h-36 w-36 rounded-full bg-amber-500/10 blur-2xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.16em] uppercase font-semibold text-amber-300 mb-1">Admin &rarr; Analytics</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-white">Reports &amp; Analytics</h2>
                <p class="text-slate-400 text-sm mt-1">Business performance insights across sales, finance, stock, expenses and profit.</p>
            </div>
            <div class="shrink-0">
                <button wire:click="exportCsv"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-semibold text-sm transition-colors">
                    <span wire:loading wire:target="exportCsv" class="inline-block w-4 h-4 border-2 border-slate-900/30 border-t-slate-900 rounded-full animate-spin"></span>
                    <svg wire:loading.remove wire:target="exportCsv" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         CONTROLS BAR — quick presets + date range
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

            {{-- Quick preset buttons --}}
            <div class="flex flex-wrap gap-2">
                @foreach([
                    ['label' => 'Today',     'value' => 'today'],
                    ['label' => '7 Days',    'value' => '7d'],
                    ['label' => '30 Days',   'value' => '30d'],
                    ['label' => '90 Days',   'value' => '90d'],
                    ['label' => 'This Year', 'value' => 'year'],
                ] as $preset)
                <button
                    wire:click="$set('period', '{{ $preset['value'] }}')"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $period === $preset['value']
                            ? 'bg-amber-400 text-slate-900'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $preset['label'] }}
                </button>
                @endforeach
            </div>

            <div class="h-px lg:h-6 lg:w-px bg-slate-200 shrink-0"></div>

            {{-- Date range inputs --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-slate-500 shrink-0">From</label>
                    <input type="date"
                           wire:model.live="dateFrom"
                           class="form-input text-sm py-1.5 w-auto"
                           max="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-slate-500 shrink-0">To</label>
                    <input type="date"
                           wire:model.live="dateTo"
                           class="form-input text-sm py-1.5 w-auto"
                           max="{{ now()->format('Y-m-d') }}">
                </div>
                <span class="text-xs text-slate-400">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} &mdash; {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         TAB NAVIGATION
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="card p-0 overflow-hidden">
        <div class="flex border-b border-slate-200 overflow-x-auto">
            @foreach([
                ['tab' => 'sales',    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Sales'],
                ['tab' => 'finance',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Finance'],
                ['tab' => 'stock',    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => 'Stock'],
                ['tab' => 'expenses', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Expenses'],
                ['tab' => 'profit',   'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'label' => 'Profit'],
            ] as $t)
            <button
                wire:click="$set('activeTab', '{{ $t['tab'] }}')"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors
                    {{ $activeTab === $t['tab']
                        ? 'border-amber-400 text-amber-600 bg-amber-50/50'
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
                </svg>
                {{ $t['label'] }}
            </button>
            @endforeach
        </div>

        <div class="p-5" wire:loading.class="opacity-60 pointer-events-none">

            {{-- ═══════════════════════════════════════════════════════
                 SALES TAB
            ═══════════════════════════════════════════════════════ --}}
            @if($activeTab === 'sales')
            <div class="space-y-5">

                {{-- KPI cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach([
                        ['label' => 'Total Revenue',  'value' => 'Rs. ' . number_format($salesSummary['total_revenue'], 2), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                        ['label' => 'Total Orders',   'value' => number_format($salesSummary['total_orders']),              'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'blue'],
                        ['label' => 'Avg Order Value','value' => 'Rs. ' . number_format($salesSummary['avg_order'], 2),     'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'color' => 'green'],
                        ['label' => 'Items Sold',     'value' => number_format($salesSummary['total_items']),               'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'purple'],
                    ] as $kpi)
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                {{ $kpi['color'] === 'amber' ? 'bg-amber-100' : ($kpi['color'] === 'blue' ? 'bg-blue-100' : ($kpi['color'] === 'green' ? 'bg-green-100' : 'bg-purple-100')) }}">
                                <svg class="w-4 h-4 {{ $kpi['color'] === 'amber' ? 'text-amber-600' : ($kpi['color'] === 'blue' ? 'text-blue-600' : ($kpi['color'] === 'green' ? 'text-green-600' : 'text-purple-600')) }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                                </svg>
                            </div>
                        </div>
                        <p class="font-[Poppins] font-bold text-xl text-slate-800">{{ $kpi['value'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Orders by Status --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="font-[Poppins] font-semibold text-slate-800 mb-3">Orders by Status</h3>
                    @if($ordersByStatus->isEmpty())
                        <p class="text-sm text-slate-400 text-center py-4">No orders in this period.</p>
                    @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($ordersByStatus as $row)
                        @php
                            $statusColor = match($row->status) {
                                'new'              => 'bg-slate-100 text-slate-700',
                                'payment_received' => 'bg-amber-100 text-amber-700',
                                'confirmed'        => 'bg-blue-100 text-blue-700',
                                'sourcing'         => 'bg-orange-100 text-orange-700',
                                'dispatched'       => 'bg-indigo-100 text-indigo-700',
                                'delivered'        => 'bg-teal-100 text-teal-700',
                                'completed'        => 'bg-green-100 text-green-700',
                                'refunded'         => 'bg-red-100 text-red-700',
                                'cancelled'        => 'bg-red-100 text-red-700',
                                default            => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $row->status)) }}
                            <span class="font-bold">{{ $row->count }}</span>
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Daily Revenue Table --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-[Poppins] font-semibold text-slate-800">Daily Revenue</h3>
                    </div>
                    @if($salesByDay->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-slate-400">No sales data for the selected period.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesByDay as $day)
                                <tr>
                                    <td class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($day->date)->format('D, M j Y') }}</td>
                                    <td class="text-right text-slate-600">{{ $day->orders }}</td>
                                    <td class="text-right font-semibold text-amber-600">Rs. {{ number_format($day->revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td class="font-semibold text-slate-700">Total</td>
                                    <td class="text-right font-semibold text-slate-700">{{ $salesByDay->sum('orders') }}</td>
                                    <td class="text-right font-bold text-amber-600">Rs. {{ number_format($salesByDay->sum('revenue'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Top 10 Products --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-[Poppins] font-semibold text-slate-800">Top 10 Products by Revenue</h3>
                    </div>
                    @if($topProducts->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-slate-400">No product sales data for the selected period.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th class="text-right">Qty Sold</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $i => $product)
                                <tr>
                                    <td>
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                            {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $i + 1 }}
                                        </span>
                                    </td>
                                    <td class="font-medium text-slate-800">{{ $product->product_name }}</td>
                                    <td class="text-right text-slate-600">{{ number_format($product->qty_sold) }}</td>
                                    <td class="text-right font-semibold text-amber-600">Rs. {{ number_format($product->revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

            </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 FINANCE TAB
            ═══════════════════════════════════════════════════════ --}}
            @if($activeTab === 'finance')
            <div class="space-y-5">

                {{-- KPI cards — 3 cols on md, 6 on xl --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
                    @foreach([
                        ['label' => 'Gross Revenue', 'value' => 'Rs. ' . number_format($financeSummary['gross_revenue'], 2), 'color' => 'amber'],
                        ['label' => 'Paid Amount',   'value' => 'Rs. ' . number_format($financeSummary['paid_amount'],   2), 'color' => 'green'],
                        ['label' => 'Pending',       'value' => 'Rs. ' . number_format($financeSummary['pending_amount'],2), 'color' => 'red'],
                        ['label' => 'Total Discount','value' => 'Rs. ' . number_format($financeSummary['total_discount'],2), 'color' => 'orange'],
                        ['label' => 'Total Tax',     'value' => 'Rs. ' . number_format($financeSummary['total_tax'],     2), 'color' => 'blue'],
                        ['label' => 'Shipping Fees', 'value' => 'Rs. ' . number_format($financeSummary['total_shipping'],2), 'color' => 'slate'],
                    ] as $kpi)
                    @php
                        $bg  = match($kpi['color']) { 'amber'=>'bg-amber-100','green'=>'bg-green-100','red'=>'bg-red-100','orange'=>'bg-orange-100','blue'=>'bg-blue-100', default=>'bg-slate-100' };
                        $txt = match($kpi['color']) { 'amber'=>'text-amber-700','green'=>'text-green-700','red'=>'text-red-700','orange'=>'text-orange-700','blue'=>'text-blue-700', default=>'text-slate-700' };
                        $val = match($kpi['color']) { 'amber'=>'text-amber-600','green'=>'text-green-600','red'=>'text-red-600','orange'=>'text-orange-600','blue'=>'text-blue-600', default=>'text-slate-600' };
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                        <div class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider {{ $bg }} {{ $txt }} mb-2">
                            {{ $kpi['label'] }}
                        </div>
                        <p class="font-[Poppins] font-bold text-lg {{ $val }} break-all">{{ $kpi['value'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Payment Methods --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-[Poppins] font-semibold text-slate-800">Payment Methods Breakdown</h3>
                    </div>
                    @if($paymentMethods->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-slate-400">No payment data for the selected period.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Total Amount</th>
                                    <th class="text-right">% of Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethods as $method)
                                @php
                                    $pct = $financeSummary['gross_revenue'] > 0
                                        ? round(($method->total / $financeSummary['gross_revenue']) * 100, 1)
                                        : 0;
                                @endphp
                                <tr>
                                    <td class="font-medium text-slate-800 capitalize">{{ str_replace('_', ' ', $method->payment_method ?? 'N/A') }}</td>
                                    <td class="text-right text-slate-600">{{ $method->count }}</td>
                                    <td class="text-right font-semibold text-amber-600">Rs. {{ number_format($method->total, 2) }}</td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden hidden sm:block">
                                                <div class="h-full bg-amber-400 rounded-full" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-slate-600">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

            </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 STOCK TAB
            ═══════════════════════════════════════════════════════ --}}
            @if($activeTab === 'stock')
            <div class="space-y-5">

                {{-- Stock KPI cards --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                    @foreach([
                        ['label' => 'Total Products', 'value' => number_format($stockSummary['total_products']), 'color' => 'blue',   'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['label' => 'In Stock (>10)', 'value' => number_format($stockSummary['in_stock']),       'color' => 'green',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Low Stock (1-10)','value'=> number_format($stockSummary['low_stock']),      'color' => 'amber',  'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ['label' => 'Out of Stock',   'value' => number_format($stockSummary['out_of_stock']),  'color' => 'red',    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Stock Value',    'value' => 'Rs. ' . number_format($stockSummary['total_value'], 2), 'color' => 'purple', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ] as $kpi)
                    @php
                        $bg  = match($kpi['color']) { 'green'=>'bg-green-100','amber'=>'bg-amber-100','red'=>'bg-red-100','purple'=>'bg-purple-100', default=>'bg-blue-100' };
                        $ic  = match($kpi['color']) { 'green'=>'text-green-600','amber'=>'text-amber-600','red'=>'text-red-600','purple'=>'text-purple-600', default=>'text-blue-600' };
                        $val = match($kpi['color']) { 'green'=>'text-green-700','amber'=>'text-amber-700','red'=>'text-red-700','purple'=>'text-purple-700', default=>'text-blue-700' };
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $bg }}">
                                <svg class="w-4 h-4 {{ $ic }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                                </svg>
                            </div>
                        </div>
                        <p class="font-[Poppins] font-bold text-xl {{ $val }} break-all">{{ $kpi['value'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Low Stock / Out of Stock alert table --}}
                <div class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-red-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="font-[Poppins] font-semibold text-slate-800">Low Stock &amp; Out of Stock Products</h3>
                        <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $lowStockProducts->count() }}</span>
                    </div>
                    @if($lowStockProducts->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-slate-400">All products are sufficiently stocked.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th class="text-right">Stock</th>
                                    <th class="text-right">Cost Price</th>
                                    <th class="text-right">Stock Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                <tr>
                                    <td>
                                        <span class="font-medium text-slate-800">{{ $product->name }}</span>
                                        <span class="block text-xs text-slate-400">{{ $product->sku }}</span>
                                    </td>
                                    <td class="text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="text-right font-bold {{ $product->stock === 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $product->stock }}</td>
                                    <td class="text-right text-slate-600">Rs. {{ number_format($product->cost_price ?? 0, 2) }}</td>
                                    <td class="text-right text-slate-600">Rs. {{ number_format($product->stock * ($product->cost_price ?? 0), 2) }}</td>
                                    <td>
                                        @if($product->stock === 0)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Out of Stock</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- All Products Table --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-[Poppins] font-semibold text-slate-800">All Products — Full Stock Report</h3>
                    </div>
                    @if($allProducts->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-slate-400">No products found.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th class="text-right">Stock</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Cost Price</th>
                                    <th class="text-right">Stock Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allProducts->sortBy('stock') as $product)
                                <tr>
                                    <td>
                                        <span class="font-medium text-slate-800">{{ $product->name }}</span>
                                        <span class="block text-xs text-slate-400">{{ $product->sku }}</span>
                                    </td>
                                    <td class="text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="text-right font-semibold
                                        {{ $product->stock === 0 ? 'text-red-600' : ($product->stock <= 10 ? 'text-amber-600' : 'text-green-600') }}">
                                        {{ $product->stock }}
                                    </td>
                                    <td class="text-right text-slate-600">Rs. {{ number_format($product->price, 2) }}</td>
                                    <td class="text-right text-slate-600">Rs. {{ number_format($product->cost_price ?? 0, 2) }}</td>
                                    <td class="text-right font-medium text-slate-700">Rs. {{ number_format($product->stock * ($product->cost_price ?? 0), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="2" class="font-semibold text-slate-700">Total</td>
                                    <td class="text-right font-semibold text-slate-700">{{ $allProducts->sum('stock') }}</td>
                                    <td colspan="2"></td>
                                    <td class="text-right font-bold text-amber-600">Rs. {{ number_format($allProducts->sum(fn($p) => $p->stock * ($p->cost_price ?? 0)), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

            </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 EXPENSES TAB
            ═══════════════════════════════════════════════════════ --}}
            @if($activeTab === 'expenses')
            <div class="space-y-5">

                {{-- KPI cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach([
                        ['label' => 'Purchase Orders',        'value' => number_format($expensesSummary['purchase_orders']),           'color' => 'blue'],
                        ['label' => 'Total PO Value',         'value' => 'Rs. ' . number_format($expensesSummary['total_purchases'],2), 'color' => 'amber'],
                        ['label' => 'Paid to Suppliers',      'value' => 'Rs. ' . number_format($expensesSummary['total_paid'],      2), 'color' => 'green'],
                    ] as $kpi)
                    @php
                        $bg  = match($kpi['color']) { 'green'=>'bg-green-100','amber'=>'bg-amber-100', default=>'bg-blue-100' };
                        $val = match($kpi['color']) { 'green'=>'text-green-700','amber'=>'text-amber-600', default=>'text-blue-700' };
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ $kpi['label'] }}</p>
                        <p class="font-[Poppins] font-bold text-2xl {{ $val }}">{{ $kpi['value'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Purchase Orders Table --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-[Poppins] font-semibold text-slate-800">Purchase Orders</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Ordered, partially received, and fully received POs in the selected period.</p>
                    </div>
                    @if($purchaseOrders->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-slate-400">No purchase orders for the selected period.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrders as $po)
                                @php
                                    $statusBg = match($po->status) {
                                        'ordered'   => 'bg-blue-100 text-blue-700',
                                        'partial'   => 'bg-amber-100 text-amber-700',
                                        'received'  => 'bg-green-100 text-green-700',
                                        default     => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <tr>
                                    <td class="font-mono text-sm font-semibold text-slate-700">{{ $po->po_number }}</td>
                                    <td class="text-slate-600">{{ $po->created_at->format('M j, Y') }}</td>
                                    <td class="font-medium text-slate-800">{{ $po->supplier?->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusBg }}">
                                            {{ $po->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-right font-semibold text-amber-600">Rs. {{ number_format($po->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="4" class="font-semibold text-slate-700">Total</td>
                                    <td class="text-right font-bold text-amber-600">Rs. {{ number_format($purchaseOrders->sum('total'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

            </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 PROFIT TAB
            ═══════════════════════════════════════════════════════ --}}
            @if($activeTab === 'profit')
            <div class="space-y-5">

                {{-- Profit KPI Cards — Row 1 (4 cards) --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach([
                        ['label' => 'Revenue',         'value' => 'Rs. ' . number_format($profitSummary['revenue'],      2), 'sign' => 1,  'color' => 'blue'],
                        ['label' => 'COGS',            'value' => 'Rs. ' . number_format($profitSummary['cogs'],         2), 'sign' => -1, 'color' => 'orange'],
                        ['label' => 'Gross Profit',    'value' => 'Rs. ' . number_format($profitSummary['gross_profit'], 2), 'sign' => $profitSummary['gross_profit'] >= 0 ? 1 : -1, 'color' => $profitSummary['gross_profit'] >= 0 ? 'green' : 'red'],
                        ['label' => 'Gross Margin',    'value' => $profitSummary['gross_margin'] . '%',                      'sign' => $profitSummary['gross_margin'] >= 0 ? 1 : -1, 'color' => $profitSummary['gross_margin'] >= 0 ? 'green' : 'red'],
                    ] as $kpi)
                    @php
                        $bg  = match($kpi['color']) { 'green'=>'bg-green-100','red'=>'bg-red-100','orange'=>'bg-orange-100', default=>'bg-blue-100' };
                        $val = match($kpi['color']) { 'green'=>'text-green-700','red'=>'text-red-700','orange'=>'text-orange-700', default=>'text-blue-700' };
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ $kpi['label'] }}</p>
                        <p class="font-[Poppins] font-bold text-xl {{ $val }} break-all">{{ $kpi['value'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Profit KPI Cards — Row 2 (3 cards) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach([
                        ['label' => 'Total Expenses',  'value' => 'Rs. ' . number_format($profitSummary['expenses'],    2), 'color' => 'orange'],
                        ['label' => 'Net Profit',      'value' => 'Rs. ' . number_format($profitSummary['net_profit'],  2), 'color' => $profitSummary['net_profit']  >= 0 ? 'green' : 'red'],
                        ['label' => 'Net Margin',      'value' => $profitSummary['net_margin'] . '%',                       'color' => $profitSummary['net_margin']   >= 0 ? 'green' : 'red'],
                    ] as $kpi)
                    @php
                        $bg  = match($kpi['color']) { 'green'=>'bg-green-100','red'=>'bg-red-100','orange'=>'bg-orange-100', default=>'bg-blue-100' };
                        $val = match($kpi['color']) { 'green'=>'text-green-700','red'=>'text-red-700','orange'=>'text-orange-700', default=>'text-blue-700' };
                    @endphp
                    <div class="bg-white rounded-xl border border-{{ $kpi['color'] === 'green' ? 'green' : ($kpi['color'] === 'red' ? 'red' : 'orange') }}-200 shadow-sm p-5 {{ $bg }}/30">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ $kpi['label'] }}</p>
                        <p class="font-[Poppins] font-bold text-2xl {{ $val }}">{{ $kpi['value'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- P&L Waterfall --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-[Poppins] font-semibold text-slate-800 mb-5">Profit &amp; Loss Summary</h3>

                    <div class="space-y-0">

                        {{-- Revenue --}}
                        <div class="flex items-center gap-4 py-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-700">Revenue</p>
                                <p class="text-xs text-slate-400">Delivered &amp; completed orders</p>
                            </div>
                            <p class="font-[Poppins] font-bold text-lg text-blue-700">Rs. {{ number_format($profitSummary['revenue'], 2) }}</p>
                        </div>

                        {{-- minus COGS --}}
                        <div class="flex items-center gap-4 py-3 border-b border-slate-100 pl-4">
                            <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center shrink-0 text-orange-600 font-bold text-sm">&minus;</div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-700">Cost of Goods Sold (COGS)</p>
                                <p class="text-xs text-slate-400">Qty &times; cost price per order item</p>
                            </div>
                            <p class="font-[Poppins] font-bold text-lg text-orange-600">Rs. {{ number_format($profitSummary['cogs'], 2) }}</p>
                        </div>

                        {{-- Gross Profit --}}
                        <div class="flex items-center gap-4 py-3 border-b-2 border-slate-300 {{ $profitSummary['gross_profit'] >= 0 ? 'bg-green-50' : 'bg-red-50' }} -mx-1 px-5 rounded-lg my-1">
                            <div class="w-8 h-8 rounded-lg {{ $profitSummary['gross_profit'] >= 0 ? 'bg-green-200' : 'bg-red-200' }} flex items-center justify-center shrink-0">
                                <span class="font-bold text-base {{ $profitSummary['gross_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">=</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-700">Gross Profit <span class="font-normal text-slate-400">({{ $profitSummary['gross_margin'] }}% margin)</span></p>
                            </div>
                            <p class="font-[Poppins] font-bold text-xl {{ $profitSummary['gross_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                Rs. {{ number_format($profitSummary['gross_profit'], 2) }}
                            </p>
                        </div>

                        {{-- minus Expenses --}}
                        <div class="flex items-center gap-4 py-3 border-b border-slate-100 pl-4">
                            <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center shrink-0 text-orange-600 font-bold text-sm">&minus;</div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-700">Supplier Payments (Expenses)</p>
                                <p class="text-xs text-slate-400">Confirmed payments to suppliers in period</p>
                            </div>
                            <p class="font-[Poppins] font-bold text-lg text-orange-600">Rs. {{ number_format($profitSummary['expenses'], 2) }}</p>
                        </div>

                        {{-- Net Profit --}}
                        <div class="flex items-center gap-4 py-4 {{ $profitSummary['net_profit'] >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-xl mt-2 px-5">
                            <div class="w-10 h-10 rounded-xl {{ $profitSummary['net_profit'] >= 0 ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center shrink-0">
                                @if($profitSummary['net_profit'] >= 0)
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @else
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5m0 0v-8m0 8l8-8 4 4 6-6"/></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wider {{ $profitSummary['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">Net Profit</p>
                                <p class="text-xs text-slate-500">After COGS and all expenses — {{ $profitSummary['net_margin'] }}% net margin</p>
                            </div>
                            <p class="font-[Poppins] font-bold text-2xl {{ $profitSummary['net_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                Rs. {{ number_format($profitSummary['net_profit'], 2) }}
                            </p>
                        </div>

                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>

</div>
