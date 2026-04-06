{{-- resources/views/livewire/staff/notifications.blade.php --}}
<div class="space-y-5">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-900 via-teal-800 to-slate-900 p-6">
        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-teal-400/20 blur-3xl pointer-events-none"></div>
        <div class="relative">
            <p class="text-[11px] tracking-[0.16em] uppercase font-semibold text-teal-300 mb-1">Staff → Notifications</p>
            <h2 class="font-[Poppins] font-bold text-2xl text-white">Customer Notifications</h2>
            <p class="text-teal-200/70 text-sm mt-1">Select customers and send WhatsApp messages via Twilio API</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('error'))
    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
         class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Send Results --}}
    @if($showResults && !empty($sendResults))
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-slate-700">Send Results</span>
                <span class="text-xs bg-teal-100 text-teal-700 font-bold px-2 py-0.5 rounded-full">
                    {{ collect($sendResults)->where('success', true)->count() }}/{{ count($sendResults) }} sent
                </span>
            </div>
            <button wire:click="$set('showResults', false)" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
            @foreach($sendResults as $r)
            <div class="flex items-center gap-3 px-5 py-2.5 text-sm">
                @if($r['success'])
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
                <span class="font-mono text-xs text-slate-500 w-36 shrink-0">{{ $r['order'] }}</span>
                <span class="font-medium text-slate-700 flex-1">{{ $r['name'] }}</span>
                <span class="text-xs text-slate-400 font-mono">{{ $r['phone'] }}</span>
                <span class="text-xs {{ $r['success'] ? 'text-emerald-600' : 'text-red-500' }} truncate max-w-[180px]">{{ $r['message'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-1 flex gap-1 overflow-x-auto">
        @foreach([
            ['key'=>'completed','label'=>'Order Completed','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['key'=>'due',      'label'=>'Payment Due',    'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['key'=>'thankyou', 'label'=>'Thank You',      'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['key'=>'review',   'label'=>'Ask Review',     'icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        ] as $t)
        <button wire:click="$set('tab','{{ $t['key'] }}')"
                class="flex-1 min-w-[130px] flex items-center justify-center gap-1.5 text-sm font-semibold px-4 py-2.5 rounded-lg transition-all whitespace-nowrap
                       {{ $tab === $t['key'] ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
            </svg>
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        {{-- Search --}}
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.400ms="search" type="text"
                   placeholder="Search order or customer..."
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400/50 bg-white">
        </div>

        {{-- Bulk actions --}}
        <div class="flex items-center gap-2">
            @if(!empty($selected))
            <span class="text-xs text-slate-500 font-medium">{{ count($selected) }} selected</span>
            <button wire:click="sendBulk" wire:loading.attr="disabled" wire:target="sendBulk"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#25D366] hover:bg-[#1da851] text-white text-sm font-bold transition-all disabled:opacity-60 shadow-sm">
                <svg wire:loading.remove wire:target="sendBulk" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.862L.057 23.571a.5.5 0 00.614.614l5.709-1.476A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
                </svg>
                <svg wire:loading wire:target="sendBulk" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Send to {{ count($selected) }}
            </button>
            <button wire:click="$set('selected', [])" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition-colors">
                Clear
            </button>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll" wire:change="toggleSelectAll"
                                   class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Total</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    @php
                        $addr  = $order->shipping_address ?? [];
                        $phone = $addr['phone'] ?? '';
                        $name  = $addr['full_name'] ?? '—';
                        $sc = match($order->status) {
                            'new'              => 'bg-slate-100 text-slate-600',
                            'payment_received' => 'bg-blue-100 text-blue-700',
                            'confirmed'        => 'bg-indigo-100 text-indigo-700',
                            'sourcing'         => 'bg-yellow-100 text-yellow-700',
                            'dispatched'       => 'bg-orange-100 text-orange-700',
                            'delivered'        => 'bg-teal-100 text-teal-700',
                            'completed'        => 'bg-emerald-100 text-emerald-700',
                            default            => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ in_array((string)$order->id, $selected) ? 'bg-teal-50/50' : '' }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selected" value="{{ $order->id }}"
                                   class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                        </td>
                        <td class="px-4 py-3 font-mono font-semibold text-slate-800 text-xs">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-slate-700 font-medium">{{ $name }}</td>
                        <td class="px-4 py-3 text-slate-500 hidden sm:table-cell font-mono text-xs">{{ $phone ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                                {{ str_replace('_', ' ', ucfirst($order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 hidden md:table-cell font-medium">Rs. {{ number_format($order->total) }}</td>
                        <td class="px-4 py-3">
                            @if($phone)
                            <button wire:click="previewMessage({{ $order->id }})"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#25D366] hover:bg-[#1da851] px-3 py-1.5 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Send
                            </button>
                            @else
                            <span class="text-xs text-slate-400 italic">No phone</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-14 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="font-medium text-sm">No orders found for this category</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="border-t border-slate-100 px-4 py-3">{{ $orders->links() }}</div>
        @endif
    </div>

    {{-- ── Preview / Send Modal ─────────────────────────────────────── --}}
    @if($showPreview)
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#25D366]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.862L.057 23.571a.5.5 0 00.614.614l5.709-1.476A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Preview Message</p>
                        <p class="text-xs text-slate-400">{{ $previewName }} · {{ $previewPhone }}</p>
                    </div>
                </div>
                <button wire:click="$set('showPreview', false)" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Message bubble --}}
            <div class="p-5">
                <div class="bg-[#dcf8c6] rounded-xl p-4 text-sm text-slate-800 whitespace-pre-line leading-relaxed border border-[#c5e8b3] max-h-64 overflow-y-auto">{{ $previewMsg }}</div>
            </div>

            {{-- Actions --}}
            <div class="px-5 pb-5 flex gap-3">
                <button wire:click="$set('showPreview', false)"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="sendSingle({{ $previewOrder }})" wire:loading.attr="disabled" wire:target="sendSingle({{ $previewOrder }})"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#1da851] text-white text-sm font-bold transition-colors disabled:opacity-60">
                    <svg wire:loading.remove wire:target="sendSingle({{ $previewOrder }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <svg wire:loading wire:target="sendSingle({{ $previewOrder }})" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Send via Twilio
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
