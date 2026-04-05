{{-- resources/views/livewire/admin/order.blade.php --}}
<div class="space-y-5">

    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Orders</h2>
            <p class="text-sm text-[#64748B]">{{ $orders->total() }} total orders</p>
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="flex flex-wrap gap-2">
        @php $statuses = [''=>'All', 'pending'=>'Pending', 'processing'=>'Processing', 'shipped'=>'Shipped', 'delivered'=>'Delivered', 'cancelled'=>'Cancelled']; @endphp
        @foreach($statuses as $key => $label)
        <button wire:click="$set('filterStatus', '{{ $key }}')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200
                    {{ $filterStatus === $key ? 'bg-[#0F172A] text-white' : 'bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0]' }}">
            {{ $label }}
            @if($key !== '' && isset($statusCounts[$key]))
            <span class="ml-1 opacity-70">({{ $statusCounts[$key] }})</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- Search --}}
    <div class="card p-4">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search by order# or customer..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
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
                    $statusColors = ['pending'=>'badge-warning','processing'=>'badge-info','shipped'=>'badge-info','delivered'=>'badge-success','cancelled'=>'badge-danger'];
                    $payColors    = ['pending'=>'badge-warning','paid'=>'badge-success','failed'=>'badge-danger','refunded'=>'badge-info'];
                    @endphp
                    <tr wire:key="{{ $order->id }}">
                        <td><span class="font-mono text-xs font-bold text-[#0F172A]">{{ $order->order_number }}</span></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#0F172A] flex items-center justify-center shrink-0">
                                    <span class="text-[#F59E0B] text-[10px] font-bold">{{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-[#0F172A] truncate">{{ $order->user->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-[#94A3B8] truncate">{{ $order->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="font-semibold text-sm text-[#0F172A]">ETB {{ number_format($order->total, 0) }}</span></td>
                        <td><span class="badge {{ $payColors[$order->payment_status] ?? 'badge-info' }} text-[10px]">{{ ucfirst($order->payment_status) }}</span></td>
                        <td><span class="badge {{ $statusColors[$order->status] ?? 'badge-info' }}">{{ ucfirst($order->status) }}</span></td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $order->created_at->format('d M Y') }}</span></td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <button wire:click="viewOrder({{ $order->id }})"
                                        class="p-1.5 rounded-lg text-[#475569] hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @if($order->status === 'pending')
                                <button wire:click="updateStatus({{ $order->id }}, 'processing')"
                                        class="px-2 py-1 rounded text-[10px] font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                    Process
                                </button>
                                @elseif($order->status === 'processing')
                                <button wire:click="updateStatus({{ $order->id }}, 'shipped')"
                                        class="px-2 py-1 rounded text-[10px] font-semibold bg-purple-50 text-purple-600 hover:bg-purple-100 transition-colors">
                                    Ship
                                </button>
                                @elseif($order->status === 'shipped')
                                <button wire:click="updateStatus({{ $order->id }}, 'delivered')"
                                        class="px-2 py-1 rounded text-[10px] font-semibold bg-green-50 text-green-600 hover:bg-green-100 transition-colors">
                                    Deliver
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-12 text-[#94A3B8]">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">{{ $orders->links() }}</div>
        @endif
    </div>

    {{-- Order Detail Modal --}}
    @if($showDetail && $selectedOrder)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white z-10">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Order {{ $selectedOrder->order_number }}</h3>
                    <p class="text-xs text-[#64748B]">{{ $selectedOrder->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <button wire:click="$set('showDetail', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-5">
                {{-- Status Update --}}
                <div class="flex flex-wrap gap-2">
                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                    <button wire:click="updateStatus({{ $selectedOrder->id }}, '{{ $s }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200
                                {{ $selectedOrder->status === $s ? 'bg-[#0F172A] text-white' : 'bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0]' }}">
                        {{ ucfirst($s) }}
                    </button>
                    @endforeach
                </div>

                {{-- Customer Info --}}
                <div class="bg-[#F8FAFC] rounded-xl p-4">
                    <h4 class="font-semibold text-sm text-[#0F172A] mb-3">Customer & Shipping</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-[#64748B] text-xs">Customer</p>
                            <p class="font-semibold text-[#0F172A]">{{ $selectedOrder->user->name }}</p>
                            <p class="text-[#64748B]">{{ $selectedOrder->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-[#64748B] text-xs">Shipping Address</p>
                            <p class="font-semibold text-[#0F172A]">{{ $selectedOrder->shipping_address['full_name'] ?? '' }}</p>
                            <p class="text-[#64748B]">{{ $selectedOrder->shipping_address['address'] ?? '' }}, {{ $selectedOrder->shipping_address['city'] ?? '' }}</p>
                            <p class="text-[#64748B]">{{ $selectedOrder->shipping_address['phone'] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div>
                    <h4 class="font-semibold text-sm text-[#0F172A] mb-3">Order Items</h4>
                    <div class="space-y-2">
                        @foreach($selectedOrder->items as $item)
                        <div class="flex items-center justify-between py-2 border-b border-[#F1F5F9] last:border-0">
                            <div>
                                <p class="text-sm font-medium text-[#0F172A]">{{ $item->product_name }}</p>
                                <p class="text-xs text-[#64748B]">ETB {{ number_format($item->price, 0) }} x {{ $item->quantity }}</p>
                            </div>
                            <span class="font-semibold text-sm text-[#0F172A]">ETB {{ number_format($item->subtotal, 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Totals --}}
                <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-[#64748B]">Subtotal</span><span>ETB {{ number_format($selectedOrder->subtotal, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#64748B]">Shipping</span><span>ETB {{ number_format($selectedOrder->shipping_cost, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#64748B]">Tax (15%)</span><span>ETB {{ number_format($selectedOrder->tax, 0) }}</span></div>
                    @if($selectedOrder->discount > 0)
                    <div class="flex justify-between text-green-600"><span>Discount</span><span>-ETB {{ number_format($selectedOrder->discount, 0) }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-base border-t border-[#E2E8F0] pt-2">
                        <span class="text-[#0F172A]">Total</span>
                        <span class="text-[#0F172A]">ETB {{ number_format($selectedOrder->total, 0) }}</span>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-sm text-[#64748B]">Payment: <strong class="text-[#0F172A]">{{ str_replace('_', ' ', ucwords($selectedOrder->payment_method)) }}</strong></span>
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['pending','paid','failed','refunded'] as $ps)
                        <button wire:click="updatePaymentStatus({{ $selectedOrder->id }}, '{{ $ps }}')"
                                class="px-2 py-1 rounded text-[10px] font-semibold transition-all
                                    {{ $selectedOrder->payment_status === $ps ? 'bg-[#0F172A] text-white' : 'bg-[#F1F5F9] text-[#475569]' }}">
                            {{ ucfirst($ps) }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
