{{-- resources/views/livewire/staff/dashboard.blade.php --}}
<div>
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-teal-800 to-teal-700 rounded-2xl p-5 mb-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold font-[Poppins]">Staff Dashboard</h2>
                <p class="text-teal-200 text-sm mt-0.5">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2 bg-teal-900/50 rounded-xl px-4 py-2">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                <span class="text-sm font-medium text-teal-100">On Shift</span>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-6">
        @php
            $kpis = [
                ['label' => 'New Orders',   'value' => $stats['new_orders'],       'color' => 'amber',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',   'link' => route('staff.orders')],
                ['label' => 'Processing',   'value' => $stats['processing'],       'color' => 'blue',    'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',                     'link' => route('staff.orders')],
                ['label' => 'Dispatched',   'value' => $stats['dispatched'],       'color' => 'orange',  'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',                                                             'link' => route('staff.orders')],
                ['label' => 'Done Today',   'value' => $stats['completed_today'],  'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                               'link' => route('staff.orders')],
                ['label' => 'Open Returns', 'value' => $stats['open_returns'],     'color' => 'red',     'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',                                                                                    'link' => route('staff.returns')],
                ['label' => 'Balance Due',  'value' => $stats['pending_payments'], 'color' => 'rose',    'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                                    'link' => route('staff.payments')],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <a href="{{ $kpi['link'] }}"
           class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide leading-tight">{{ $kpi['label'] }}</span>
                <div class="w-7 h-7 rounded-lg bg-{{ $kpi['color'] }}-50 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-{{ $kpi['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $kpi['value'] }}</p>
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Recent Orders --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">Recent Orders</h3>
                <a href="{{ route('staff.orders') }}" class="text-xs text-teal-600 hover:text-teal-800 font-semibold">View all →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentOrders as $order)
                @php
                    $addr = $order->shipping_address ?? [];
                    $sc = [
                        'new'              => 'bg-slate-100 text-slate-600',
                        'payment_received' => 'bg-blue-100 text-blue-700',
                        'confirmed'        => 'bg-indigo-100 text-indigo-700',
                        'sourcing'         => 'bg-yellow-100 text-yellow-700',
                        'dispatched'       => 'bg-orange-100 text-orange-700',
                        'delivered'        => 'bg-teal-100 text-teal-700',
                        'completed'        => 'bg-emerald-100 text-emerald-700',
                        'cancelled'        => 'bg-red-100 text-red-600',
                    ][$order->status] ?? 'bg-slate-100 text-slate-600';
                @endphp
                <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $addr['full_name'] ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 ml-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sc }}">
                            {{ str_replace('_', ' ', ucfirst($order->status)) }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium">Rs. {{ number_format($order->total) }}</span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">No orders yet</div>
                @endforelse
            </div>
        </div>

        {{-- Outstanding Balances --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">Outstanding Balances</h3>
                <a href="{{ route('staff.notifications') }}" class="text-xs text-teal-600 hover:text-teal-800 font-semibold">Send reminders →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($pendingPaymentOrders as $order)
                @php $addr = $order->shipping_address ?? []; @endphp
                <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $addr['full_name'] ?? '—' }} · {{ $addr['phone'] ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-3">
                        <span class="text-xs font-bold text-red-600">Rs. {{ number_format($order->balance_due ?? 0) }}</span>
                        <a href="{{ route('staff.notifications') }}"
                           class="text-[10px] font-bold text-white bg-[#25D366] px-2 py-1 rounded-lg hover:bg-[#1da851]">
                            Remind
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm">
                    <svg class="w-8 h-8 mx-auto mb-2 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-emerald-600 font-medium">All payments clear!</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $quickActions = [
                ['label' => 'Manage Orders',    'href' => route('staff.orders'),         'color' => 'teal',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label' => 'WhatsApp Orders',  'href' => route('staff.whatsapp-orders'), 'color' => 'green',  'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ['label' => 'Notify Customers', 'href' => route('staff.notifications'),   'color' => 'purple', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                ['label' => 'Manage Returns',   'href' => route('staff.returns'),         'color' => 'red',    'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
            ];
        @endphp
        @foreach($quickActions as $action)
        <a href="{{ $action['href'] }}"
           class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col items-center gap-2 text-center">
            <div class="w-10 h-10 rounded-xl bg-{{ $action['color'] }}-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $action['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-600">{{ $action['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>
