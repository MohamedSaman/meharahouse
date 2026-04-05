{{-- resources/views/livewire/staff/order.blade.php --}}
<div class="space-y-5" x-data="{
    selectedStatus: 'all',
    selectedOrder: null,
    showModal: false,
    orders: [
        { id: '#ORD-1024', customer: 'Selam Tadesse',   phone: '+251 911 001 001', items: 3, amount: '4,200', status: 'pending',    date: 'Apr 5, 2026 09:12', address: 'Bole, Addis Ababa', products: ['Wireless Headphones x1', 'Smart Watch Pro x1', 'Leather Bag x1'] },
        { id: '#ORD-1023', customer: 'Yonas Bekele',    phone: '+251 922 002 002', items: 1, amount: '1,850', status: 'processing', date: 'Apr 5, 2026 08:45', address: 'Piassa, Addis Ababa', products: ['Natural Skincare Set x1'] },
        { id: '#ORD-1022', customer: 'Hana Girma',      phone: '+251 933 003 003', items: 2, amount: '2,750', status: 'pending',    date: 'Apr 5, 2026 08:20', address: 'Kazanchis, Addis Ababa', products: ['Ergonomic Desk Lamp x1', 'Ceramic Mug Set x1'] },
        { id: '#ORD-1021', customer: 'Dawit Alemu',     phone: '+251 944 004 004', items: 5, amount: '8,400', status: 'delivered',  date: 'Apr 4, 2026 17:55', address: 'CMC, Addis Ababa', products: ['Running Shoes x2', 'Sport Bag x1', 'Water Bottle x2'] },
        { id: '#ORD-1020', customer: 'Mekdes Fikre',    phone: '+251 955 005 005', items: 2, amount: '3,100', status: 'cancelled',  date: 'Apr 4, 2026 15:30', address: 'Gerji, Addis Ababa', products: ['Bluetooth Speaker x1', 'USB Cable x1'] },
        { id: '#ORD-1019', customer: 'Bereket Mulatu',  phone: '+251 966 006 006', items: 1, amount: '890',   status: 'processing', date: 'Apr 4, 2026 14:10', address: 'Megenagna, Addis Ababa', products: ['Skincare Set x1'] },
        { id: '#ORD-1018', customer: 'Tigist Hailu',    phone: '+251 977 007 007', items: 4, amount: '6,300', status: 'pending',    date: 'Apr 4, 2026 11:40', address: 'Sarbet, Addis Ababa', products: ['Premium Headphones x2', 'Speaker x1', 'Charger x1'] },
        { id: '#ORD-1017', customer: 'Abel Tesfaye',    phone: '+251 988 008 008', items: 2, amount: '5,200', status: 'delivered',  date: 'Apr 3, 2026 16:22', address: 'Gotera, Addis Ababa', products: ['Smart Watch Pro x1', 'Phone Case x1'] },
    ],
    get filtered() {
        if (this.selectedStatus === 'all') return this.orders;
        return this.orders.filter(o => o.status === this.selectedStatus);
    },
    statusBadge(status) {
        const map = {
            pending:    'bg-yellow-100 text-yellow-700',
            processing: 'bg-blue-100 text-blue-700',
            delivered:  'bg-green-100 text-green-700',
            cancelled:  'bg-red-100 text-red-700',
        };
        return map[status] || 'bg-gray-100 text-gray-700';
    },
    statusLabel(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    },
    openOrder(order) {
        this.selectedOrder = order;
        this.showModal = true;
    }
}">

    {{-- ══════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Order Management Queue</h2>
            <p class="text-sm text-[#64748B]">Process and fulfill customer orders</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
            <button class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         STATUS SUMMARY CARDS
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
        $summaryCards = [
            ['status' => 'all',        'label' => 'Total Orders',   'count' => 8,  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'ring' => 'ring-[#0F172A]', 'bg' => 'bg-[#0F172A]', 'text' => 'text-white'],
            ['status' => 'pending',    'label' => 'Pending',        'count' => 3,  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'ring' => 'ring-yellow-400', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-700'],
            ['status' => 'processing', 'label' => 'Processing',     'count' => 2,  'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'ring' => 'ring-blue-400', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700'],
            ['status' => 'delivered',  'label' => 'Delivered',      'count' => 2,  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'ring' => 'ring-green-400', 'bg' => 'bg-green-50', 'text' => 'text-green-700'],
        ];
        @endphp
        @foreach($summaryCards as $card)
        <button
            @click="selectedStatus = '{{ $card['status'] }}'"
            :class="selectedStatus === '{{ $card['status'] }}' ? 'ring-2 {{ $card['ring'] }} shadow-md' : 'hover:shadow-md'"
            class="card p-4 text-left transition-all duration-200 cursor-pointer">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 {{ $card['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
                <span :class="selectedStatus === '{{ $card['status'] }}' ? '{{ $card['text'] }} font-bold' : 'text-[#94A3B8]'"
                      class="text-2xl font-[Poppins] font-bold text-[#0F172A]">{{ $card['count'] }}</span>
            </div>
            <p class="text-xs font-semibold text-[#475569]">{{ $card['label'] }}</p>
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════
         FILTER BAR + SEARCH
    ══════════════════════════════════════════════════════ --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            {{-- Search --}}
            <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
                <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search order ID or customer..." class="bg-transparent text-sm text-[#475569] outline-none flex-1 placeholder-[#94A3B8]">
            </div>

            {{-- Status Filter Tabs --}}
            <div class="flex flex-wrap items-center gap-2">
                @foreach(['all' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $key => $label)
                <button
                    @click="selectedStatus = '{{ $key }}'"
                    :class="selectedStatus === '{{ $key }}'
                        ? 'bg-[#0F172A] text-white'
                        : 'bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0]'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         ORDERS TABLE
    ══════════════════════════════════════════════════════ --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="rounded border-[#CBD5E1] w-4 h-4">
                        </th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="order in filtered" :key="order.id">
                        <tr class="cursor-pointer" @click="openOrder(order)">
                            <td @click.stop>
                                <input type="checkbox" class="rounded border-[#CBD5E1] w-4 h-4">
                            </td>
                            <td>
                                <span x-text="order.id" class="font-mono text-xs font-bold text-[#0F172A]"></span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#134e4a] flex items-center justify-center shrink-0">
                                        <span x-text="order.customer.charAt(0)" class="text-teal-300 text-xs font-bold"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p x-text="order.customer" class="text-sm font-semibold text-[#0F172A] truncate"></p>
                                        <p x-text="order.phone" class="text-xs text-[#64748B] truncate"></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span x-text="order.items + ' item' + (order.items > 1 ? 's' : '')" class="text-sm text-[#475569]"></span>
                            </td>
                            <td>
                                <span class="font-semibold text-sm text-[#0F172A]">ETB <span x-text="order.amount"></span></span>
                            </td>
                            <td>
                                <span :class="statusBadge(order.status)"
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide">
                                    <span x-text="statusLabel(order.status)"></span>
                                </span>
                            </td>
                            <td>
                                <span x-text="order.date" class="text-xs text-[#94A3B8] whitespace-nowrap"></span>
                            </td>
                            <td @click.stop>
                                <div class="flex items-center gap-1.5">
                                    {{-- Process / Mark Delivered --}}
                                    <template x-if="order.status === 'pending'">
                                        <button @click="order.status = 'processing'"
                                                class="px-2.5 py-1.5 text-xs font-bold bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors whitespace-nowrap">
                                            Process
                                        </button>
                                    </template>
                                    <template x-if="order.status === 'processing'">
                                        <button @click="order.status = 'delivered'"
                                                class="px-2.5 py-1.5 text-xs font-bold bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors whitespace-nowrap">
                                            Mark Delivered
                                        </button>
                                    </template>
                                    {{-- View Detail --}}
                                    <button @click="openOrder(order)"
                                            class="p-1.5 rounded-lg text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0F172A] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty State --}}
                    <template x-if="filtered.length === 0">
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
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Table Footer --}}
        <div class="px-5 py-3.5 border-t border-[#F1F5F9] flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-[#94A3B8]">Showing <span x-text="filtered.length"></span> of 8 orders</p>
            <div class="flex items-center gap-1">
                <button class="p-2 rounded-lg text-[#94A3B8] hover:text-[#0F172A] hover:bg-[#F1F5F9] disabled:opacity-40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <span class="px-3 py-1.5 rounded-lg bg-[#0F172A] text-white text-xs font-bold">1</span>
                <span class="px-3 py-1.5 rounded-lg text-[#475569] text-xs font-semibold hover:bg-[#F1F5F9] cursor-pointer">2</span>
                <button class="p-2 rounded-lg text-[#94A3B8] hover:text-[#0F172A] hover:bg-[#F1F5F9]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         ORDER DETAIL MODAL
    ══════════════════════════════════════════════════════ --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display:none;"
         @keydown.escape.window="showModal = false">

        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="showModal = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" style="display:none;">

            <template x-if="selectedOrder">
                <div>
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-[#F1F5F9] bg-[#F8FAFC]">
                        <div>
                            <h3 class="font-[Poppins] font-bold text-[#0F172A]" x-text="selectedOrder.id"></h3>
                            <span :class="statusBadge(selectedOrder.status)"
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide mt-1">
                                <span x-text="statusLabel(selectedOrder.status)"></span>
                            </span>
                        </div>
                        <button @click="showModal = false" class="p-2 rounded-lg text-[#64748B] hover:bg-[#E2E8F0] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-5">
                        {{-- Customer Info --}}
                        <div class="flex items-center gap-3 p-4 bg-[#F8FAFC] rounded-xl">
                            <div class="w-12 h-12 rounded-full bg-[#134e4a] flex items-center justify-center shrink-0">
                                <span x-text="selectedOrder.customer.charAt(0)" class="text-teal-300 text-lg font-bold"></span>
                            </div>
                            <div class="min-w-0">
                                <p x-text="selectedOrder.customer" class="font-[Poppins] font-bold text-[#0F172A]"></p>
                                <p x-text="selectedOrder.phone" class="text-sm text-[#64748B]"></p>
                                <p x-text="selectedOrder.address" class="text-sm text-[#94A3B8]"></p>
                            </div>
                        </div>

                        {{-- Order Info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-[#F8FAFC] rounded-lg">
                                <p class="text-xs text-[#94A3B8] font-medium mb-1">Order Date</p>
                                <p x-text="selectedOrder.date" class="text-sm font-semibold text-[#0F172A]"></p>
                            </div>
                            <div class="p-3 bg-[#F8FAFC] rounded-lg">
                                <p class="text-xs text-[#94A3B8] font-medium mb-1">Total Amount</p>
                                <p class="text-sm font-semibold text-[#0F172A]">ETB <span x-text="selectedOrder.amount"></span></p>
                            </div>
                        </div>

                        {{-- Products --}}
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-[#64748B] mb-3">Products Ordered</p>
                            <div class="space-y-2">
                                <template x-for="product in selectedOrder.products" :key="product">
                                    <div class="flex items-center gap-3 p-3 border border-[#E2E8F0] rounded-lg">
                                        <div class="w-8 h-8 rounded-lg bg-[#FFFBEB] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <span x-text="product" class="text-sm text-[#475569] font-medium"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Actions --}}
                    <div class="flex items-center gap-3 px-6 py-4 bg-[#F8FAFC] border-t border-[#F1F5F9]">
                        <template x-if="selectedOrder.status === 'pending'">
                            <button @click="selectedOrder.status = 'processing'; showModal = false"
                                    class="btn-primary flex-1 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Start Processing
                            </button>
                        </template>
                        <template x-if="selectedOrder.status === 'processing'">
                            <button @click="selectedOrder.status = 'delivered'; showModal = false"
                                    class="flex-1 justify-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mark as Delivered
                            </button>
                        </template>
                        <template x-if="selectedOrder.status === 'delivered'">
                            <div class="flex-1 text-center py-2 text-green-600 font-bold text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Order Completed
                            </div>
                        </template>
                        <button @click="showModal = false" class="btn-secondary flex-1 justify-center text-sm">Close</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
