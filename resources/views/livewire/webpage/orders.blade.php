{{-- resources/views/livewire/webpage/orders.blade.php --}}
<div>
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-3xl text-white">My Orders</h1>
            <p class="text-[#64748B] mt-1 text-sm">Track and manage your order history</p>
        </div>
    </div>

    <section class="py-10 container-page max-w-4xl">
        <div class="space-y-4">
            @php
            $orders = [
                ['id' => '#ORD-1024', 'date' => 'Apr 5, 2026', 'items' => 3, 'total' => '8,479', 'status' => 'Processing', 'sc' => 'badge-info'],
                ['id' => '#ORD-1019', 'date' => 'Mar 28, 2026', 'items' => 1, 'total' => '2,499', 'status' => 'Delivered', 'sc' => 'badge-success'],
                ['id' => '#ORD-1012', 'date' => 'Mar 15, 2026', 'items' => 2, 'total' => '5,090', 'status' => 'Delivered', 'sc' => 'badge-success'],
                ['id' => '#ORD-1005', 'date' => 'Feb 28, 2026', 'items' => 1, 'total' => '890', 'status' => 'Cancelled', 'sc' => 'badge-danger'],
            ];
            @endphp

            @foreach($orders as $order)
            <div class="card p-5" x-data="{ expanded: false }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-[#FFFBEB] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="font-mono font-bold text-sm text-[#0F172A]">{{ $order['id'] }}</p>
                            <p class="text-xs text-[#64748B]">{{ $order['date'] }} &bull; {{ $order['items'] }} item{{ $order['items'] > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-[Poppins] font-bold text-[#0F172A]">ETB {{ $order['total'] }}</span>
                        <span class="badge {{ $order['sc'] }}">{{ $order['status'] }}</span>
                        <button @click="expanded = !expanded" class="p-1.5 rounded-lg text-[#64748B] hover:bg-[#F1F5F9] transition-colors">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition class="mt-4 pt-4 border-t border-[#F1F5F9]" style="display:none;">
                    <div class="flex flex-wrap gap-3">
                        <button class="btn-secondary btn-sm">Track Order</button>
                        <button class="btn-ghost btn-sm">Download Invoice</button>
                        @if($order['status'] === 'Delivered')
                        <button class="btn-ghost btn-sm">Leave Review</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>
