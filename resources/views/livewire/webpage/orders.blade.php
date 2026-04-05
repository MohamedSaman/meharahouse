{{-- resources/views/livewire/webpage/orders.blade.php --}}
<div>
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-3xl text-white">My Orders</h1>
            <p class="text-[#64748B] mt-1 text-sm">Track and manage your order history</p>
        </div>
    </div>

    <section class="py-10 container-page max-w-4xl">

        {{-- Flash --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if($orders->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-[#F1F5F9] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-2">No Orders Yet</h3>
            <p class="text-[#64748B] mb-6">You haven't placed any orders. Start shopping now!</p>
            <a href="{{ route('webpage.shop') }}" class="btn-primary btn-lg">Browse Products</a>
        </div>
        @else

        <div class="space-y-4">
            @foreach($orders as $order)
            @php $statusColors = ['pending'=>'bg-yellow-100 text-yellow-700','processing'=>'bg-blue-100 text-blue-700','shipped'=>'bg-purple-100 text-purple-700','delivered'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700']; @endphp
            <div class="card overflow-hidden">
                {{-- Order Header --}}
                <div class="px-5 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <div>
                            <p class="text-xs text-[#64748B]">Order Number</p>
                            <p class="font-mono font-bold text-[#0F172A]">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-[#64748B]">Date</p>
                            <p class="font-semibold text-[#0F172A]">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-[#64748B]">Total</p>
                            <p class="font-bold text-[#0F172A]">ETB {{ number_format($order->total, 0) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        <button wire:click="viewOrder({{ $order->id }})" class="text-xs text-[#F59E0B] font-semibold hover:underline">Details</button>
                        @if($order->status === 'pending')
                        <button wire:click="cancelOrder({{ $order->id }})"
                                wire:confirm="Are you sure you want to cancel this order?"
                                class="text-xs text-red-500 font-semibold hover:underline">Cancel</button>
                        @endif
                    </div>
                </div>

                {{-- Items preview --}}
                <div class="px-5 py-4 flex gap-3 overflow-x-auto">
                    @foreach($order->items->take(3) as $item)
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="w-10 h-10 rounded-lg bg-[#F1F5F9] overflow-hidden shrink-0">
                            <img src="{{ $item->product?->primaryImage() ?? asset('images/placeholder-product.jpg') }}"
                                 alt="{{ $item->product_name }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'">
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-[#0F172A] truncate max-w-[120px]">{{ Str::limit($item->product_name, 20) }}</p>
                            <p class="text-xs text-[#64748B]">Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    @endforeach
                    @if($order->items->count() > 3)
                    <div class="w-10 h-10 rounded-lg bg-[#F1F5F9] flex items-center justify-center shrink-0 text-xs font-bold text-[#64748B]">
                        +{{ $order->items->count() - 3 }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">{{ $orders->links() }}</div>
        @endif

        {{-- Order Detail Modal --}}
        @if($showDetail && $selectedOrder)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white">
                    <div>
                        <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">{{ $selectedOrder->order_number }}</h3>
                        <p class="text-xs text-[#64748B]">{{ $selectedOrder->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <button wire:click="$set('showDetail', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Status --}}
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$selectedOrder->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($selectedOrder->status) }}
                        </span>
                        <span class="text-xs text-[#64748B]">Payment: {{ ucfirst($selectedOrder->payment_status) }}</span>
                    </div>

                    {{-- Items --}}
                    <div>
                        <h4 class="font-semibold text-sm text-[#0F172A] mb-3">Items</h4>
                        <div class="space-y-2">
                            @foreach($selectedOrder->items as $item)
                            <div class="flex justify-between text-sm py-2 border-b border-[#F1F5F9] last:border-0">
                                <div>
                                    <p class="font-medium text-[#0F172A]">{{ $item->product_name }}</p>
                                    <p class="text-xs text-[#64748B]">ETB {{ number_format($item->price, 0) }} x {{ $item->quantity }}</p>
                                </div>
                                <span class="font-semibold">ETB {{ number_format($item->subtotal, 0) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm">
                        <div class="flex justify-between text-[#64748B]"><span>Subtotal</span><span>ETB {{ number_format($selectedOrder->subtotal, 0) }}</span></div>
                        <div class="flex justify-between text-[#64748B]"><span>Shipping</span><span>ETB {{ number_format($selectedOrder->shipping_cost, 0) }}</span></div>
                        <div class="flex justify-between text-[#64748B]"><span>Tax</span><span>ETB {{ number_format($selectedOrder->tax, 0) }}</span></div>
                        <div class="flex justify-between font-bold border-t border-[#E2E8F0] pt-2">
                            <span>Total</span><span>ETB {{ number_format($selectedOrder->total, 0) }}</span>
                        </div>
                    </div>

                    {{-- Shipping --}}
                    <div class="bg-[#F8FAFC] rounded-xl p-4 text-sm">
                        <h4 class="font-semibold text-[#0F172A] mb-2">Delivery Address</h4>
                        <p class="text-[#475569]">{{ $selectedOrder->shipping_address['full_name'] ?? '' }}</p>
                        <p class="text-[#475569]">{{ $selectedOrder->shipping_address['address'] ?? '' }}, {{ $selectedOrder->shipping_address['city'] ?? '' }}</p>
                        <p class="text-[#475569]">{{ $selectedOrder->shipping_address['phone'] ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>
</div>
