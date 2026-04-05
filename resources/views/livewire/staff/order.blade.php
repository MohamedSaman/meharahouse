{{-- resources/views/livewire/staff/order.blade.php --}}
<div class="space-y-5">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Order Management Queue</h2>
            <p class="text-sm text-[#64748B]">Process and fulfill customer orders</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- Status Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        @php
        $statusCards = [
            ['key' => '',           'label' => 'All Orders',  'color_bg' => 'bg-[#0F172A]',   'color_text' => 'text-white',      'border' => 'border-[#0F172A]'],
            ['key' => 'pending',    'label' => 'Pending',     'color_bg' => 'bg-yellow-50',    'color_text' => 'text-yellow-700', 'border' => 'border-yellow-300'],
            ['key' => 'processing', 'label' => 'Processing',  'color_bg' => 'bg-blue-50',      'color_text' => 'text-blue-700',   'border' => 'border-blue-300'],
            ['key' => 'shipped',    'label' => 'Shipped',     'color_bg' => 'bg-purple-50',    'color_text' => 'text-purple-700', 'border' => 'border-purple-300'],
            ['key' => 'delivered',  'label' => 'Delivered',   'color_bg' => 'bg-green-50',     'color_text' => 'text-green-700',  'border' => 'border-green-300'],
        ];
        @endphp
        @foreach($statusCards as $card)
        <button wire:click="$set('filterStatus', '{{ $card['key'] }}')"
                class="card p-4 text-left transition-all duration-200 cursor-pointer border-2 {{ $filterStatus === $card['key'] ? $card['border'] . ' shadow-md' : 'border-transparent hover:shadow-md' }}">
            <div class="w-8 h-8 rounded-lg {{ $card['color_bg'] }} flex items-center justify-center mb-2">
                <span class="{{ $card['color_text'] }} font-bold text-sm">
                    {{ $card['key'] === '' ? ($counts['pending'] + $counts['processing'] + $counts['shipped'] + $counts['delivered'] + $counts['cancelled']) : ($counts[$card['key']] ?? 0) }}
                </span>
            </div>
            <p class="text-xs font-semibold text-[#475569]">{{ $card['label'] }}</p>
        </button>
        @endforeach
    </div>

    {{-- Filter Bar + Search --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
                <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search order # or customer..." class="bg-transparent text-sm text-[#475569] outline-none flex-1 placeholder-[#94A3B8]">
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php
                    $statusColors = [
                        'pending'    => 'bg-yellow-100 text-yellow-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        'shipped'    => 'bg-purple-100 text-purple-700',
                        'delivered'  => 'bg-green-100 text-green-700',
                        'cancelled'  => 'bg-red-100 text-red-700',
                    ];
                    @endphp
                    <tr wire:key="{{ $order->id }}" class="cursor-pointer hover:bg-[#F8FAFC]" wire:click="viewOrder({{ $order->id }})">
                        <td>
                            <span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#134e4a] flex items-center justify-center shrink-0">
                                    <span class="text-teal-300 text-xs font-bold">{{ strtoupper(substr($order->user?->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">{{ $order->user?->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-[#64748B] truncate">{{ $order->user?->phone ?? $order->user?->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm text-[#475569]">{{ $order->items_count ?? $order->items->count() }} item(s)</span>
                        </td>
                        <td>
                            <span class="font-semibold text-sm text-[#0F172A]">ETB {{ number_format($order->total, 0) }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-[#475569]">{{ str_replace('_', ' ', ucwords($order->payment_method)) }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-xs text-[#94A3B8] whitespace-nowrap">{{ $order->created_at->format('d M Y') }}</span>
                        </td>
                        <td wire:click.stop>
                            <div class="flex items-center gap-1.5">
                                @if($order->status === 'pending')
                                <button wire:click.stop="updateStatus({{ $order->id }}, 'processing')"
                                        class="px-2.5 py-1.5 text-xs font-bold bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors whitespace-nowrap">
                                    Process
                                </button>
                                @elseif($order->status === 'processing')
                                <button wire:click.stop="updateStatus({{ $order->id }}, 'shipped')"
                                        class="px-2.5 py-1.5 text-xs font-bold bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors whitespace-nowrap">
                                    Ship
                                </button>
                                @elseif($order->status === 'shipped')
                                <button wire:click.stop="updateStatus({{ $order->id }}, 'delivered')"
                                        class="px-2.5 py-1.5 text-xs font-bold bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors whitespace-nowrap">
                                    Deliver
                                </button>
                                @endif
                                <button wire:click.stop="viewOrder({{ $order->id }})"
                                        class="p-1.5 rounded-lg text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0F172A] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-14 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-[#F1F5F9] flex items-center justify-center">
                                    <svg class="w-7 h-7 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-[#475569]">No orders found</p>
                                <p class="text-xs text-[#94A3B8]">No orders match the selected filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

    {{-- Order Detail Modal --}}
    @if($showDetail && $selectedOrder)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            @php
            $statusColors = [
                'pending'    => 'bg-yellow-100 text-yellow-700',
                'processing' => 'bg-blue-100 text-blue-700',
                'shipped'    => 'bg-purple-100 text-purple-700',
                'delivered'  => 'bg-green-100 text-green-700',
                'cancelled'  => 'bg-red-100 text-red-700',
            ];
            @endphp

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F1F5F9] bg-[#F8FAFC] sticky top-0">
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">{{ $selectedOrder->order_number }}</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold mt-1 {{ $statusColors[$selectedOrder->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($selectedOrder->status) }}
                    </span>
                </div>
                <button wire:click="$set('showDetail', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#E2E8F0] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                {{-- Customer Info --}}
                <div class="flex items-center gap-3 p-4 bg-[#F8FAFC] rounded-xl">
                    <div class="w-12 h-12 rounded-full bg-[#134e4a] flex items-center justify-center shrink-0">
                        <span class="text-teal-300 text-lg font-bold">{{ strtoupper(substr($selectedOrder->user?->name ?? 'G', 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-[Poppins] font-bold text-[#0F172A]">{{ $selectedOrder->user?->name ?? 'Guest' }}</p>
                        <p class="text-sm text-[#64748B]">{{ $selectedOrder->shipping_address['phone'] ?? $selectedOrder->user?->phone ?? '' }}</p>
                        <p class="text-sm text-[#94A3B8]">{{ $selectedOrder->shipping_address['address'] ?? '' }}, {{ $selectedOrder->shipping_address['city'] ?? '' }}</p>
                    </div>
                </div>

                {{-- Order Info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-[#F8FAFC] rounded-lg">
                        <p class="text-xs text-[#94A3B8] font-medium mb-1">Order Date</p>
                        <p class="text-sm font-semibold text-[#0F172A]">{{ $selectedOrder->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="p-3 bg-[#F8FAFC] rounded-lg">
                        <p class="text-xs text-[#94A3B8] font-medium mb-1">Total Amount</p>
                        <p class="text-sm font-semibold text-[#0F172A]">ETB {{ number_format($selectedOrder->total, 0) }}</p>
                    </div>
                </div>

                {{-- Items --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#64748B] mb-3">Products Ordered</p>
                    <div class="space-y-2">
                        @foreach($selectedOrder->items as $item)
                        <div class="flex items-center justify-between p-3 border border-[#E2E8F0] rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#FFFBEB] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-[#475569]">{{ $item->product_name }}</p>
                                    <p class="text-xs text-[#94A3B8]">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-[#0F172A]">ETB {{ number_format($item->subtotal, 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Status Update Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-[#F1F5F9]">
                    @if($selectedOrder->status === 'pending')
                    <button wire:click="updateStatus({{ $selectedOrder->id }}, 'processing')"
                            class="btn-primary flex-1 justify-center text-sm">
                        Start Processing
                    </button>
                    @elseif($selectedOrder->status === 'processing')
                    <button wire:click="updateStatus({{ $selectedOrder->id }}, 'shipped')"
                            class="flex-1 justify-center px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-2">
                        Mark as Shipped
                    </button>
                    @elseif($selectedOrder->status === 'shipped')
                    <button wire:click="updateStatus({{ $selectedOrder->id }}, 'delivered')"
                            class="flex-1 justify-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-2">
                        Mark as Delivered
                    </button>
                    @elseif($selectedOrder->status === 'delivered')
                    <div class="flex-1 text-center py-2 text-green-600 font-bold text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Order Completed
                    </div>
                    @endif
                    <button wire:click="$set('showDetail', false)" class="btn-secondary flex-1 justify-center text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
