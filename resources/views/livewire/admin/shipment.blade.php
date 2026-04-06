{{-- resources/views/livewire/admin/shipment.blade.php --}}
<div class="space-y-6">

    {{-- ══════════════════════ PAGE HEADER ══════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-14 -right-10 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-blue-400/15 blur-3xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300">Logistics</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-white">Shipment Batches</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm text-slate-300">Dubai</span>
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <span class="text-sm text-slate-300">Sri Lanka</span>
                    <span class="ml-2 text-xs text-slate-400">· Group orders into shipment batches and track their international journey</span>
                </div>
            </div>
            <button wire:click="openCreateBatch"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-slate-900 hover:bg-amber-300 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-400/40 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Batch
            </button>
        </div>
    </div>

    {{-- ══════════════════════ FLASH MESSAGES ══════════════════════ --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ══════════════════════ STAT CARDS ══════════════════════ --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Active Batches --}}
        <div class="card p-4 border-l-4 border-blue-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', '')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-2xl font-[Poppins] font-bold text-[#0F172A]">{{ $stats['active'] }}</span>
            </div>
            <p class="text-sm font-semibold text-[#0F172A]">Active Batches</p>
            <p class="text-xs text-[#64748B] mt-0.5">Currently in progress</p>
        </div>

        {{-- In Transit --}}
        <div class="card p-4 border-l-4 border-indigo-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', 'in_transit')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span class="text-2xl font-[Poppins] font-bold text-[#0F172A]">{{ $stats['in_transit'] }}</span>
            </div>
            <p class="text-sm font-semibold text-[#0F172A]">In Transit</p>
            <p class="text-xs text-[#64748B] mt-0.5">En route to Sri Lanka</p>
        </div>

        {{-- Arrived --}}
        <div class="card p-4 border-l-4 border-teal-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', 'arrived')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-2xl font-[Poppins] font-bold text-[#0F172A]">{{ $stats['arrived'] }}</span>
            </div>
            <p class="text-sm font-semibold text-[#0F172A]">Arrived / Distributing</p>
            <p class="text-xs text-[#64748B] mt-0.5">Ready for local delivery</p>
        </div>

        {{-- Completed --}}
        <div class="card p-4 border-l-4 border-green-400 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             wire:click="$set('filterStatus', 'completed')">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-2xl font-[Poppins] font-bold text-[#0F172A]">{{ $stats['completed'] }}</span>
            </div>
            <p class="text-sm font-semibold text-[#0F172A]">Completed</p>
            <p class="text-xs text-[#64748B] mt-0.5">All orders delivered</p>
        </div>
    </div>

    {{-- ══════════════════════ FILTER BAR ══════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
                <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.350ms="search"
                       type="text"
                       placeholder="Search batch name or number..."
                       class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
            </div>

            {{-- Status Filter --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                @php
                $statusFilters = [
                    ''             => 'All',
                    'collecting'   => 'Collecting',
                    'purchased'    => 'Purchased',
                    'packed'       => 'Packed',
                    'shipped'      => 'Shipped',
                    'in_transit'   => 'In Transit',
                    'arrived'      => 'Arrived',
                    'distributing' => 'Distributing',
                    'completed'    => 'Completed',
                ];
                @endphp
                @foreach($statusFilters as $key => $label)
                <button wire:click="$set('filterStatus', '{{ $key }}')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                            {{ $filterStatus === $key
                                ? 'bg-slate-800 text-white shadow-md'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════ BATCHES TABLE ══════════════════════ --}}
    <div class="card overflow-hidden">
        @if($batches->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="font-[Poppins] font-semibold text-[#0F172A] text-lg">No shipment batches found</p>
            <p class="text-[#64748B] text-sm mt-1">Create your first batch to start grouping orders for Dubai → Sri Lanka shipping.</p>
            <button wire:click="openCreateBatch"
                    class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0F172A] text-white text-sm font-semibold hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create First Batch
            </button>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Batch</th>
                        <th>Status</th>
                        <th class="text-center">Orders</th>
                        <th>Courier / Tracking</th>
                        <th>Expected Arrival</th>
                        <th>Progress</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                    {{-- Main row --}}
                    <tr class="group">
                        {{-- Batch Name & Number --}}
                        <td>
                            <div class="font-semibold text-[#0F172A] text-sm">{{ $batch->name }}</div>
                            <div class="text-xs text-[#94A3B8] font-mono mt-0.5">{{ $batch->batch_number }}</div>
                            @if($batch->notes)
                            <div class="text-xs text-[#64748B] mt-1 truncate max-w-[200px]" title="{{ $batch->notes }}">
                                {{ $batch->notes }}
                            </div>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $batch->statusColor() }}">
                                {{ $batch->statusLabel() }}
                            </span>
                        </td>

                        {{-- Order Count --}}
                        <td class="text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                {{ $batch->orders_count > 0 ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-slate-100 text-slate-400' }} text-sm">
                                {{ $batch->orders_count }}
                            </span>
                        </td>

                        {{-- Courier --}}
                        <td>
                            @if($batch->courier_name)
                            <div class="text-sm text-[#0F172A] font-medium">{{ $batch->courier_name }}</div>
                            @endif
                            @if($batch->tracking_number)
                            <div class="text-xs text-[#64748B] font-mono mt-0.5">{{ $batch->tracking_number }}</div>
                            @endif
                            @if(!$batch->courier_name && !$batch->tracking_number)
                            <span class="text-xs text-[#CBD5E1]">—</span>
                            @endif
                        </td>

                        {{-- Expected Arrival --}}
                        <td>
                            @if($batch->expected_arrival)
                            <div class="text-sm text-[#0F172A]">{{ $batch->expected_arrival->format('d M Y') }}</div>
                            @if($batch->arrived_at)
                            <div class="text-xs text-teal-600 mt-0.5">
                                Arrived {{ $batch->arrived_at->format('d M') }}
                            </div>
                            @elseif($batch->expected_arrival->isPast() && !in_array($batch->status, ['arrived','distributing','completed']))
                            <div class="text-xs text-red-500 mt-0.5">Overdue</div>
                            @endif
                            @else
                            <span class="text-xs text-[#CBD5E1]">—</span>
                            @endif
                        </td>

                        {{-- Progress Bar --}}
                        <td class="min-w-[140px]">
                            @php
                            $stages   = ['collecting','purchased','packed','shipped','in_transit','arrived','distributing','completed'];
                            $stageIdx = array_search($batch->status, $stages);
                            $progress = $stageIdx !== false ? round((($stageIdx + 1) / count($stages)) * 100) : 0;
                            @endphp
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                    {{ $batch->status === 'completed' ? 'bg-green-500' : 'bg-amber-400' }}"
                                     style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-[10px] text-[#94A3B8]">Step {{ ($stageIdx ?? 0) + 1 }} of {{ count($stages) }}</span>
                                <span class="text-[10px] font-semibold text-[#64748B]">{{ $progress }}%</span>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                {{-- Assign Orders --}}
                                @if(!in_array($batch->status, ['completed']))
                                <button wire:click="openAssignModal({{ $batch->id }})"
                                        title="Assign Orders"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Orders
                                </button>
                                @endif

                                {{-- Waybills (arrived/distributing) --}}
                                @if(in_array($batch->status, ['arrived', 'distributing']))
                                <button wire:click="toggleExpand({{ $batch->id }})"
                                        title="Manage Waybills"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 text-xs font-semibold hover:bg-purple-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Waybills
                                </button>
                                @endif

                                {{-- Advance Status --}}
                                @if($batch->status !== 'completed')
                                <button wire:click="advanceStatus({{ $batch->id }})"
                                        wire:confirm="Advance '{{ $batch->name }}' to the next stage?"
                                        title="Advance to next stage"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold hover:bg-amber-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                    </svg>
                                    Advance
                                </button>
                                @endif

                                {{-- Edit --}}
                                <button wire:click="openEditBatch({{ $batch->id }})"
                                        title="Edit batch"
                                        class="p-1.5 rounded-lg text-[#64748B] hover:bg-slate-100 hover:text-[#0F172A] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Expanded Waybill Sub-Table --}}
                    @if(in_array($batch->id, $expandedBatches) && isset($expandedBatchOrders[$batch->id]))
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="bg-slate-50 border-t border-b border-slate-200 px-6 py-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-[Poppins] font-semibold text-sm text-[#0F172A]">
                                        Waybill Management — {{ $batch->name }}
                                    </h4>
                                    <span class="text-xs text-[#64748B]">{{ $expandedBatchOrders[$batch->id]->count() }} orders</span>
                                </div>

                                @if($expandedBatchOrders[$batch->id]->isEmpty())
                                <p class="text-sm text-[#94A3B8] text-center py-4">No orders assigned to this batch.</p>
                                @else
                                <div class="overflow-x-auto rounded-xl border border-slate-200">
                                    <table class="w-full text-sm">
                                        <thead class="bg-white border-b border-slate-200">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#64748B]">Order #</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#64748B]">Customer</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#64748B]">Payment</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#64748B]">Waybill #</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#64748B]">Delivery Agent</th>
                                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-[#64748B]">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($expandedBatchOrders[$batch->id] as $order)
                                            @php
                                            $addr     = $order->shipping_address ?? [];
                                            $custName = $addr['full_name'] ?? ($order->user?->name ?? 'Guest');
                                            $custPhone = $addr['phone'] ?? '';
                                            @endphp
                                            <tr class="bg-white hover:bg-slate-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <span class="font-mono text-xs font-semibold text-[#0F172A]">{{ $order->order_number }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-[#0F172A] text-xs">{{ $custName }}</div>
                                                    @if($custPhone)
                                                    <div class="text-[10px] text-[#94A3B8]">{{ $custPhone }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                    $payClass = match($order->payment_status) {
                                                        'paid'    => 'bg-green-100 text-green-700',
                                                        'partial' => 'bg-orange-100 text-orange-700',
                                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                                        default   => 'bg-slate-100 text-slate-600',
                                                    };
                                                    $payLabel = match($order->payment_status) {
                                                        'paid'    => 'Paid',
                                                        'partial' => 'Partial',
                                                        'pending' => 'Pending',
                                                        default   => ucfirst($order->payment_status),
                                                    };
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $payClass }}">
                                                        {{ $payLabel }}
                                                    </span>
                                                    @if($order->payment_status !== 'paid')
                                                    <div class="text-[10px] text-red-500 mt-0.5">Rs. {{ number_format($order->balanceDue(), 0) }} due</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($order->waybill_number)
                                                    <span class="font-mono text-xs text-[#0F172A] font-semibold">{{ $order->waybill_number }}</span>
                                                    @else
                                                    <span class="text-xs text-[#CBD5E1]">Not set</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-xs text-[#475569]">{{ $order->delivery_agent ?: '—' }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <button wire:click="openWaybillModal({{ $order->id }})"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-purple-600 text-white text-[11px] font-semibold hover:bg-purple-700 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        {{ $order->waybill_number ? 'Edit' : 'Add' }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($batches->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $batches->links() }}
        </div>
        @endif
        @endif
    </div>

    {{-- ══════════════════════ CREATE / EDIT BATCH MODAL ══════════════════════ --}}
    @if($showBatchModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(15,23,42,0.6);" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-white/10">
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-white">
                        {{ $editingBatchId ? 'Edit Batch' : 'New Shipment Batch' }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Dubai → Sri Lanka shipping batch</p>
                </div>
                <button wire:click="$set('showBatchModal', false)"
                        class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Validation Errors --}}
                @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                    @foreach($errors->all() as $error)
                    <p class="text-xs text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                {{-- Batch Name --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Batch Name <span class="text-red-500">*</span></label>
                    <input wire:model="batchName"
                           type="text"
                           placeholder="e.g. April Batch 1"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition-colors @error('batchName') border-red-400 @enderror">
                </div>

                {{-- Courier Name --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Courier Name</label>
                    <input wire:model="courierName"
                           type="text"
                           placeholder="e.g. DHL, FedEx, Emirates Post..."
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition-colors">
                </div>

                {{-- Tracking Number --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">International Tracking Number</label>
                    <input wire:model="trackingNumber"
                           type="text"
                           placeholder="Courier tracking number"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Courier Cost --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Courier Cost (Rs.)</label>
                        <input wire:model="courierCost"
                               type="number"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition-colors">
                    </div>

                    {{-- Expected Arrival --}}
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Expected Arrival</label>
                        <input wire:model="expectedArrival"
                               type="date"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition-colors">
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Notes</label>
                    <textarea wire:model="batchNotes"
                              rows="3"
                              placeholder="Internal notes about this batch..."
                              class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400 transition-colors resize-none"></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center gap-3 px-6 pb-5">
                <button wire:click="$set('showBatchModal', false)"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Cancel
                </button>
                <button wire:click="saveBatch"
                        wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-amber-400 text-slate-900 text-sm font-bold hover:bg-amber-300 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveBatch">
                        {{ $editingBatchId ? 'Update Batch' : 'Create Batch' }}
                    </span>
                    <span wire:loading wire:target="saveBatch">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════ ASSIGN ORDERS MODAL ══════════════════════ --}}
    @if($showAssignModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(15,23,42,0.65);" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[85vh]">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-900 to-blue-800 border-b border-white/10 shrink-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-white">Assign Orders to Batch</h3>
                    <p class="text-xs text-blue-300 mt-0.5">{{ $assigningBatchName }}</p>
                </div>
                <button wire:click="$set('showAssignModal', false)"
                        class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Search --}}
            <div class="px-5 pt-4 pb-3 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-xl px-3 py-2.5">
                    <svg class="w-4 h-4 text-[#94A3B8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.350ms="orderSearch"
                           type="text"
                           placeholder="Search order number or customer name..."
                           class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
                </div>
                <p class="text-xs text-[#94A3B8] mt-2">
                    Showing confirmed/sourcing/dispatched orders not yet in another batch.
                    <span class="font-semibold text-blue-600">{{ count($selectedOrderIds) }} selected.</span>
                </p>
            </div>

            {{-- Order List --}}
            <div class="overflow-y-auto flex-1 px-5 py-3 space-y-2">
                @if($assignableOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-[#64748B] font-medium">No eligible orders found</p>
                    <p class="text-xs text-[#94A3B8] mt-1">Orders must be in confirmed, sourcing, or dispatched status.</p>
                </div>
                @else
                @foreach($assignableOrders as $order)
                @php
                $addr      = $order->shipping_address ?? [];
                $custName  = $addr['full_name'] ?? ($order->user?->name ?? 'Guest');
                $custPhone = $addr['phone'] ?? '';
                $isSelected = in_array($order->id, $selectedOrderIds);

                $payBadge = match($order->payment_status) {
                    'paid'    => ['class' => 'bg-green-100 text-green-700', 'label' => 'Paid'],
                    'partial' => ['class' => 'bg-orange-100 text-orange-700', 'label' => 'Partial'],
                    default   => ['class' => 'bg-yellow-100 text-yellow-700', 'label' => 'Pending'],
                };
                @endphp
                <div wire:click="toggleOrderSelection({{ $order->id }})"
                     class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150
                         {{ $isSelected
                             ? 'border-blue-400 bg-blue-50'
                             : 'border-transparent bg-[#F8FAFC] hover:border-slate-200 hover:bg-slate-50' }}">

                    {{-- Checkbox --}}
                    <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center shrink-0 transition-colors
                        {{ $isSelected ? 'bg-blue-600 border-blue-600' : 'border-slate-300 bg-white' }}">
                        @if($isSelected)
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        @endif
                    </div>

                    {{-- Order Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $payBadge['class'] }}">
                                {{ $payBadge['label'] }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-600">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-[#475569] font-medium">{{ $custName }}</span>
                            @if($custPhone)
                            <span class="text-xs text-[#94A3B8]">{{ $custPhone }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Order Total --}}
                    <div class="text-right shrink-0">
                        <div class="text-sm font-bold text-[#0F172A]">Rs. {{ number_format($order->total, 0) }}</div>
                        @if($order->payment_status !== 'paid')
                        <div class="text-[11px] text-red-500">Due: Rs. {{ number_format($order->balanceDue(), 0) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center gap-3 px-6 py-4 border-t border-slate-100 shrink-0">
                <button wire:click="$set('showAssignModal', false)"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Cancel
                </button>
                <button wire:click="saveOrderAssignment"
                        wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveOrderAssignment">
                        Assign {{ count($selectedOrderIds) }} Order{{ count($selectedOrderIds) !== 1 ? 's' : '' }}
                    </span>
                    <span wire:loading wire:target="saveOrderAssignment">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════ WAYBILL MODAL ══════════════════════ --}}
    @if($showWaybillModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(15,23,42,0.6);" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-purple-900 to-purple-800 border-b border-white/10">
                <div>
                    <h3 class="font-[Poppins] font-bold text-base text-white">Waybill Details</h3>
                    <p class="text-xs text-purple-300 mt-0.5">Local delivery tracking information</p>
                </div>
                <button wire:click="$set('showWaybillModal', false)"
                        class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-4">

                @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                    @foreach($errors->all() as $error)
                    <p class="text-xs text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                {{-- Waybill Number --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Waybill Number</label>
                    <input wire:model="waybillNumber"
                           type="text"
                           placeholder="Local courier waybill / tracking number"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400 transition-colors">
                </div>

                {{-- Delivery Agent --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Delivery Agent</label>
                    <input wire:model="deliveryAgent"
                           type="text"
                           placeholder="e.g. Priyantha, Lanka Express..."
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400 transition-colors">
                </div>

                {{-- Delivery Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Delivery Notes</label>
                    <textarea wire:model="deliveryNotes"
                              rows="3"
                              placeholder="Any special delivery instructions or notes..."
                              class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400 transition-colors resize-none"></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center gap-3 px-6 pb-5">
                <button wire:click="$set('showWaybillModal', false)"
                        class="flex-1 py-2.5 rounded-xl border border-[#E2E8F0] text-sm font-semibold text-[#475569] hover:bg-[#F8FAFC] transition-colors">
                    Cancel
                </button>
                <button wire:click="saveWaybill"
                        wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-bold hover:bg-purple-700 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveWaybill">Save Waybill</span>
                    <span wire:loading wire:target="saveWaybill">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
