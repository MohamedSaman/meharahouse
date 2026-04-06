{{-- resources/views/livewire/admin/dashboard.blade.php --}}
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200  from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-16 -right-12 h-44 w-44 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-12 h-44 w-44 rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-dark-300">Meharahouse Admin</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-slate-800">Dashboard Overview</h2>
                <p class="text-sm text-slate-600 mt-1">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Report
            </button>
            <button class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-3 py-2 text-xs font-bold text-slate-900 hover:bg-amber-300 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-400/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Product
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════════ --}}
    {{-- KPI Cards — real data --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Revenue --}}
        <div class="stat-card hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider mb-1">Total Revenue</p>
                <p class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-1">Rs. {{ number_format($stats['total_revenue'], 0) }}</p>
                <p class="text-xs text-[#94A3B8]">All non-cancelled orders</p>
            </div>
        </div>
        {{-- Orders --}}
        <div class="stat-card hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider mb-1">Total Orders</p>
                <p class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-1">{{ number_format($stats['total_orders']) }}</p>
                <p class="text-xs text-yellow-600 font-semibold">{{ $stats['pending_orders'] }} pending</p>
            </div>
        </div>
        {{-- Customers --}}
        <div class="stat-card hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider mb-1">Customers</p>
                <p class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-1">{{ number_format($stats['total_users']) }}</p>
                <p class="text-xs text-[#94A3B8]">Registered accounts</p>
            </div>
        </div>
        {{-- Products --}}
        <div class="stat-card hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#64748B] font-medium uppercase tracking-wider mb-1">Products</p>
                <p class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-1">{{ number_format($stats['total_products']) }}</p>
                @if($stats['out_of_stock'] > 0)
                <p class="text-xs text-red-500 font-semibold">{{ $stats['out_of_stock'] }} out of stock</p>
                @else
                <p class="text-xs text-green-600 font-semibold">All in stock</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         CHARTS ROW
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Revenue Chart (Placeholder) --}}
        <div class="card xl:col-span-2 p-6 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Revenue Overview</h3>
                    <p class="text-xs text-[#64748B] mt-0.5">Monthly revenue for 2024</p>
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1.5 text-xs font-semibold bg-[#0F172A] text-white rounded-lg">Monthly</button>
                    <button class="px-3 py-1.5 text-xs font-semibold text-[#64748B] hover:bg-[#F1F5F9] rounded-lg transition-colors">Weekly</button>
                    <button class="px-3 py-1.5 text-xs font-semibold text-[#64748B] hover:bg-[#F1F5F9] rounded-lg transition-colors">Daily</button>
                </div>
            </div>

            {{-- Chart Visualization (CSS bars) --}}
            <div class="flex items-end gap-2 h-40 mb-3">
                @php
                $bars = [
                    ['h' => 45, 'label' => 'Jan', 'val' => '18.2K'],
                    ['h' => 62, 'label' => 'Feb', 'val' => '24.8K'],
                    ['h' => 55, 'label' => 'Mar', 'val' => '21.5K'],
                    ['h' => 78, 'label' => 'Apr', 'val' => '31.2K'],
                    ['h' => 68, 'label' => 'May', 'val' => '27.1K'],
                    ['h' => 88, 'label' => 'Jun', 'val' => '35.6K'],
                    ['h' => 72, 'label' => 'Jul', 'val' => '28.8K'],
                    ['h' => 95, 'label' => 'Aug', 'val' => '38.2K'],
                    ['h' => 83, 'label' => 'Sep', 'val' => '33.4K'],
                    ['h' => 76, 'label' => 'Oct', 'val' => '30.5K'],
                    ['h' => 90, 'label' => 'Nov', 'val' => '36.1K'],
                    ['h' => 100, 'label' => 'Dec', 'val' => '40.2K'],
                ];
                @endphp
                @foreach($bars as $i => $bar)
                <div class="flex-1 flex flex-col items-center gap-1 group relative" x-data>
                    <div class="w-full relative">
                        {{-- Tooltip --}}
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#0F172A] text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                            Rs. {{ $bar['val'] }}
                        </div>
                        {{-- Bar --}}
                            <div class="w-full rounded-t-md {{ $i === 11 ? 'bg-[#F59E0B]' : 'bg-[#CBD5E1] group-hover:bg-[#0F172A]' }} transition-all duration-300 hover:shadow-sm"
                             style="height: {{ $bar['h'] * 1.4 }}px;">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                @foreach($bars as $bar)
                <span class="flex-1 text-center text-[10px] text-[#94A3B8]">{{ $bar['label'] }}</span>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-5 mt-5 pt-5 border-t border-[#F1F5F9]">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-sm bg-[#F59E0B]"></span>
                    <span class="text-xs text-[#64748B]">Current Month</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-sm bg-[#CBD5E1]"></span>
                    <span class="text-xs text-[#64748B]">Previous Months</span>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-xs text-[#64748B]">Year Total</p>
                    <p class="font-[Poppins] font-bold text-sm text-[#0F172A]">Rs. 365,600</p>
                </div>
            </div>
        </div>

        {{-- Order Status Donut --}}
        <div class="card p-6 shadow-sm hover:shadow-md transition-all duration-300">
            <h3 class="font-[Poppins] font-bold text-[#0F172A] mb-1">Order Status</h3>
            <p class="text-xs text-[#64748B] mb-6">This month's breakdown</p>

            {{-- Donut Chart (SVG) --}}
            <div class="flex justify-center mb-6">
                <div class="relative w-36 h-36">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#F1F5F9" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#F59E0B" stroke-width="3"
                                stroke-dasharray="42 58" stroke-dashoffset="0"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#3B82F6" stroke-width="3"
                                stroke-dasharray="28 72" stroke-dashoffset="-42"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#22C55E" stroke-width="3"
                                stroke-dasharray="20 80" stroke-dashoffset="-70"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#EF4444" stroke-width="3"
                                stroke-dasharray="10 90" stroke-dashoffset="-90"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-[Poppins] font-bold text-xl text-[#0F172A]">1,248</span>
                        <span class="text-xs text-[#64748B]">Total</span>
                    </div>
                </div>
            </div>

            {{-- Legend — real data --}}
            @php
            $statusColors = ['pending' => 'bg-[#F59E0B]', 'processing' => 'bg-blue-500', 'shipped' => 'bg-purple-500', 'delivered' => 'bg-green-500', 'cancelled' => 'bg-red-500'];
            $totalOrders  = $orderStatuses->sum() ?: 1;
            @endphp
            <div class="space-y-3">
                @foreach(['pending','processing','shipped','delivered','cancelled'] as $status)
                @php $count = $orderStatuses[$status] ?? 0; $pct = round(($count / $totalOrders) * 100); @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $statusColors[$status] }}"></span>
                        <span class="text-xs text-[#475569] font-medium">{{ ucfirst($status) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#0F172A]">{{ $count }}</span>
                        <span class="text-xs text-[#94A3B8]">{{ $pct }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         QUICK ACTIONS
    ══════════════════════════════════════════════════════ --}}
    <div class="card p-5 shadow-sm hover:shadow-md transition-all duration-300">
        <h3 class="font-[Poppins] font-bold text-[#0F172A] mb-4 text-sm">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $actions = [
                ['label' => 'Add Product',   'icon' => 'M12 4v16m8-8H4',         'color' => 'bg-[#FFFBEB] text-[#D97706] hover:bg-[#FEF3C7]'],
                ['label' => 'New Order',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'bg-[#EFF6FF] text-blue-600 hover:bg-blue-100'],
                ['label' => 'Add Customer',  'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'color' => 'bg-[#F0FDF4] text-green-600 hover:bg-green-100'],
                ['label' => 'View Reports',  'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'bg-[#F5F3FF] text-purple-600 hover:bg-purple-100'],
            ];
            @endphp
            @foreach($actions as $action)
            <button class="flex items-center gap-3 p-3.5 rounded-xl {{ $action['color'] }} transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm text-left font-semibold text-sm">
                <div class="w-8 h-8 rounded-lg bg-white/60 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
                    </svg>
                </div>
                {{ $action['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         BOTTOM ROW: Recent Orders + Low Stock
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Recent Orders Table --}}
        <div class="card xl:col-span-2 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F1F5F9]">
                <h3 class="font-[Poppins] font-bold text-[#0F172A]">Recent Orders</h3>
                <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-[#F59E0B] hover:text-[#D97706] flex items-center gap-1">
                    View All
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Products</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Real orders from DB --}}
                        @php
                        $statusBadge = ['pending'=>'badge-warning','processing'=>'badge-info','shipped'=>'badge-info','delivered'=>'badge-success','cancelled'=>'badge-danger'];
                        @endphp
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#0F172A] flex items-center justify-center">
                                        <span class="text-[#F59E0B] text-[10px] font-bold">{{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-[#0F172A]">{{ $order->user->name ?? 'Guest' }}</span>
                                </div>
                            </td>
                            <td><span class="text-sm text-[#475569]">{{ $order->items_count ?? '—' }} items</span></td>
                            <td><span class="font-semibold text-sm text-[#0F172A]">Rs. {{ number_format($order->total, 0) }}</span></td>
                            <td><span class="badge {{ $statusBadge[$order->status] ?? 'badge-info' }}">{{ ucfirst($order->status) }}</span></td>
                            <td><span class="text-xs text-[#94A3B8]">{{ $order->created_at->diffForHumans() }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-8 text-[#94A3B8]">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="card shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F1F5F9]">
                <h3 class="font-[Poppins] font-bold text-[#0F172A] text-sm">Low Stock Alerts</h3>
                <span class="badge badge-danger">{{ $lowStockProducts->count() }} Items</span>
            </div>
            <div class="divide-y divide-[#F1F5F9]">
                @forelse($lowStockProducts as $item)
                <div class="px-5 py-3.5">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#0F172A] truncate">{{ $item->name }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $item->sku ?? 'No Code' }}</p>
                        </div>
                        <span class="badge {{ $item->stock === 0 ? 'badge-danger' : 'badge-warning' }} shrink-0">
                            {{ $item->stock === 0 ? 'Out' : $item->stock . ' left' }}
                        </span>
                    </div>
                    <div class="w-full h-1.5 bg-[#F1F5F9] rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $item->stock <= 2 ? 'bg-red-500' : 'bg-orange-400' }}"
                             style="width: {{ $item->stock === 0 ? 5 : min(($item->stock / 50) * 100, 100) }}%"></div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm text-green-600 font-semibold">All products are well stocked!</div>
                @endforelse
            </div>
            <div class="p-4 border-t border-[#F1F5F9]">
                <button class="btn-primary w-full justify-center btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restock Now
                </button>
            </div>
        </div>
    </div>

</div>
