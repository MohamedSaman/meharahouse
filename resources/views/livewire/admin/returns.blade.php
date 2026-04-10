{{-- resources/views/livewire/admin/returns.blade.php --}}
<div x-data="{ createOpen: @entangle('showCreateModal'), detailOpen: @entangle('showDetailModal') }">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 mb-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold font-[Poppins]">Returns Management</h2>
                <p class="text-slate-300 text-sm mt-0.5">Track and manage customer return requests</p>
            </div>
            <button wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-sm px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Return
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @php
            $statCards = [
                ['label' => 'Requested', 'key' => 'requested', 'color' => 'amber', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Pickup Arranged', 'key' => 'pickup_arranged', 'color' => 'blue', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'Received', 'key' => 'received', 'color' => 'indigo', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['label' => 'Resolved', 'key' => 'resolved', 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $card['label'] }}</span>
                <div class="w-8 h-8 rounded-lg bg-{{ $card['color'] }}-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-{{ $card['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $stats[$card['key']] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-4 shadow-sm flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search order number..."
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"/>
        </div>
        <select wire:model.live="filterStatus"
                class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white text-slate-700">
            <option value="">All Statuses</option>
            <option value="requested">Requested</option>
            <option value="pickup_arranged">Pickup Arranged</option>
            <option value="received">Received</option>
            <option value="resold">Resold</option>
            <option value="sent_back_dubai">Sent Back to Dubai</option>
            <option value="closed">Closed</option>
        </select>
    </div>

    {{-- Returns Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Reason</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Pickup Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Condition</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Created</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $ret)
                    @php
                        $statusColors = [
                            'requested'       => 'bg-amber-100 text-amber-700',
                            'pickup_arranged' => 'bg-blue-100 text-blue-700',
                            'received'        => 'bg-indigo-100 text-indigo-700',
                            'resold'          => 'bg-emerald-100 text-emerald-700',
                            'sent_back_dubai' => 'bg-purple-100 text-purple-700',
                            'closed'          => 'bg-slate-100 text-slate-600',
                        ];
                        $statusLabel = str_replace('_', ' ', ucfirst($ret->status));
                        $colorClass  = $statusColors[$ret->status] ?? 'bg-slate-100 text-slate-600';
                        $addr        = $ret->order->shipping_address ?? [];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-semibold text-slate-800">{{ $ret->order->order_number }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $addr['full_name'] ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 hidden md:table-cell max-w-xs">
                            <span class="truncate block">{{ Str::limit($ret->reason, 50) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colorClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 hidden lg:table-cell">
                            {{ $ret->pickup_date ? \Carbon\Carbon::parse($ret->pickup_date)->format('d M Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($ret->condition)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $ret->condition === 'good' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($ret->condition) }}
                            </span>
                            @else
                            <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">
                            {{ $ret->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="openDetail({{ $ret->id }})"
                                    class="text-xs font-semibold text-teal-600 hover:text-teal-800 transition-colors px-2 py-1 rounded hover:bg-teal-50">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <p class="font-medium">No returns found</p>
                            <p class="text-sm mt-1">Create a new return request to get started</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
        <div class="border-t border-slate-100 px-4 py-3">
            {{ $returns->links() }}
        </div>
        @endif
    </div>

    {{-- ════════════════ CREATE RETURN MODAL ════════════════ --}}
    <div x-show="createOpen" x-transition.opacity
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click.outside="createOpen = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 shrink-0">
                <h3 class="text-base font-bold text-slate-800">Create Return Request</h3>
                <button @click="createOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-4 overflow-y-auto flex-1">

                {{-- Order Search --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Search Order</label>
                    <div class="relative">
                        <input wire:model.live.debounce.400ms="searchOrder" type="text"
                               placeholder="Order number or customer name..."
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"/>
                        @if($orderResults)
                        <div class="absolute top-full left-0 right-0 bg-white border border-slate-200 rounded-lg shadow-lg z-10 mt-1 max-h-48 overflow-y-auto">
                            @foreach($orderResults as $res)
                            <button wire:click="selectOrder({{ $res['id'] }}, '{{ $res['number'] }}')"
                                    class="w-full text-left px-3 py-2.5 hover:bg-slate-50 border-b border-slate-50 last:border-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-sm text-slate-800">{{ $res['number'] }}</span>
                                    <span class="text-xs text-slate-500">Rs. {{ number_format($res['total']) }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ $res['name'] }}</span>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @if($selectedOrderNum)
                    <div class="mt-2 flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Selected: <span class="font-bold">{{ $selectedOrderNum }}</span>
                    </div>
                    @endif
                    @error('selectedOrderId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Return Reason *</label>
                    <textarea wire:model="reason" rows="3" placeholder="Describe the reason for return..."
                              class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                    @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pickup Info --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pickup Date</label>
                        <input wire:model="pickupDate" type="date"
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pickup Address</label>
                        <input wire:model="pickupAddress" type="text" placeholder="Optional..."
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"/>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Internal Notes</label>
                    <textarea wire:model="createNotes" rows="2" placeholder="Optional notes..."
                              class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 flex gap-3 justify-end shrink-0">
                <button @click="createOpen = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button wire:click="createReturn"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="px-4 py-2 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="createReturn">Create Return</span>
                    <span wire:loading wire:target="createReturn">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════ DETAIL / UPDATE MODAL ════════════════ --}}
    <div x-show="detailOpen" x-transition.opacity
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click.outside="detailOpen = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
            @if($selectedReturn)
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Return — {{ $selectedReturn->order->order_number }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Created {{ $selectedReturn->created_at->format('d M Y H:i') }}</p>
                </div>
                <button @click="detailOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-4">

                {{-- Customer & Order Info --}}
                @php $addr = $selectedReturn->order->shipping_address ?? []; @endphp
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Customer</p>
                        <p class="font-semibold text-slate-800">{{ $addr['full_name'] ?? '—' }}</p>
                        <p class="text-slate-500 text-xs">{{ $addr['phone'] ?? '' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Order Total</p>
                        <p class="font-semibold text-slate-800">Rs. {{ number_format($selectedReturn->order->total) }}</p>
                        <p class="text-slate-500 text-xs capitalize">{{ str_replace('_',' ',$selectedReturn->order->status) }}</p>
                    </div>
                </div>

                {{-- Return Reason --}}
                <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                    <p class="text-xs text-amber-600 font-semibold uppercase tracking-wide mb-1">Return Reason</p>
                    <p class="text-sm text-slate-700">{{ $selectedReturn->reason }}</p>
                </div>

                {{-- Items --}}
                @if($selectedReturn->order->items->isNotEmpty())
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Order Items</p>
                    <div class="space-y-1">
                        @foreach($selectedReturn->order->items as $item)
                        <div class="flex items-center justify-between text-sm bg-slate-50 rounded-lg px-3 py-2">
                            <span class="text-slate-700">{{ $item->product_name ?? $item->product?->name ?? 'Product' }}</span>
                            <span class="text-slate-500">× {{ $item->quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Notes History --}}
                @if($selectedReturn->notes)
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Notes</p>
                    <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-600 whitespace-pre-line">{{ $selectedReturn->notes }}</div>
                </div>
                @endif

                {{-- Update Form --}}
                <div class="border-t border-slate-100 pt-4 space-y-3">
                    <p class="text-xs font-bold text-slate-600 uppercase tracking-wide">Update Return</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                            <select wire:model="updateStatus"
                                    class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                                <option value="requested">Requested</option>
                                <option value="pickup_arranged">Pickup Arranged</option>
                                <option value="received">Received</option>
                                <option value="resold">Resold (restore stock)</option>
                                <option value="sent_back_dubai">Sent Back to Dubai</option>
                                <option value="closed">Closed</option>
                            </select>
                            @error('updateStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Item Condition</label>
                            <select wire:model="updateCondition"
                                    class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                                <option value="">— Not Set —</option>
                                <option value="good">Good</option>
                                <option value="defective">Defective</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Add Note</label>
                        <textarea wire:model="updateNotes" rows="2" placeholder="Add a note (will be appended to history)..."
                                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                    </div>
                </div>
            </div>
            <div class="px-5 pb-5 flex gap-3 justify-end">
                <button @click="detailOpen = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Close
                </button>
                <button wire:click="updateReturn"
                        class="px-4 py-2 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition-colors">
                    Save Changes
                </button>
            </div>
            @endif
        </div>
    </div>

</div>
