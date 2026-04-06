{{-- resources/views/livewire/staff/notifications.blade.php --}}
<div x-data="{ waOpen: @entangle('showWaModal'), waLink: '', waMsg: '', copied: false }">

    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-teal-800 to-teal-700 rounded-2xl p-5 mb-6 text-white shadow-lg">
        <h2 class="text-xl font-bold font-[Poppins]">Customer Notifications</h2>
        <p class="text-teal-200 text-sm mt-0.5">Send WhatsApp messages to customers using ready-made templates</p>
    </div>

    {{-- Template Tabs --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-5 p-1 flex gap-1 overflow-x-auto">
        @php
            $tabs = [
                ['key' => 'completed', 'label' => 'Order Completed', 'color' => 'emerald'],
                ['key' => 'due',       'label' => 'Payment Due',     'color' => 'amber'],
                ['key' => 'thankyou',  'label' => 'Thank You',       'color' => 'blue'],
                ['key' => 'review',    'label' => 'Ask for Review',  'color' => 'purple'],
            ];
        @endphp
        @foreach($tabs as $t)
        <button wire:click="$set('tab', '{{ $t['key'] }}')"
                class="flex-1 min-w-[120px] text-sm font-semibold px-4 py-2.5 rounded-lg transition-all whitespace-nowrap
                       {{ $tab === $t['key'] ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Tab Description --}}
    <div class="mb-5 px-1">
        @if($tab === 'completed')
        <p class="text-sm text-slate-500">Send a completion/delivery message to customers whose orders are <strong>delivered or completed</strong>.</p>
        @elseif($tab === 'due')
        <p class="text-sm text-slate-500">Remind customers with <strong>outstanding balance</strong> to complete their payment.</p>
        @elseif($tab === 'thankyou')
        <p class="text-sm text-slate-500">Send a thank-you note to customers with <strong>confirmed/processing</strong> orders.</p>
        @elseif($tab === 'review')
        <p class="text-sm text-slate-500">Ask customers with <strong>completed orders</strong> to leave a review on your site.</p>
        @endif
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-slate-200 p-3 mb-4 shadow-sm">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.400ms="search" type="text"
                   placeholder="Search by order number or customer name..."
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"/>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Total</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Send</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    @php
                        $addr  = $order->shipping_address ?? [];
                        $phone = $addr['phone'] ?? '';
                        $name  = $addr['full_name'] ?? '—';
                        $statusColors = [
                            'new'              => 'bg-slate-100 text-slate-600',
                            'payment_received' => 'bg-blue-100 text-blue-700',
                            'confirmed'        => 'bg-indigo-100 text-indigo-700',
                            'sourcing'         => 'bg-yellow-100 text-yellow-700',
                            'dispatched'       => 'bg-orange-100 text-orange-700',
                            'delivered'        => 'bg-teal-100 text-teal-700',
                            'completed'        => 'bg-emerald-100 text-emerald-700',
                        ];
                        $sc = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $name }}</td>
                        <td class="px-4 py-3 text-slate-500 hidden sm:table-cell font-mono text-xs">{{ $phone ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                                {{ str_replace('_', ' ', ucfirst($order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 hidden md:table-cell">Rs. {{ number_format($order->total) }}</td>
                        <td class="px-4 py-3">
                            @if($phone)
                            <button wire:click="openWhatsApp({{ $order->id }}, '{{ $tab }}')"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#25D366] hover:bg-[#1da851] px-3 py-1.5 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                                </svg>
                                WhatsApp
                            </button>
                            @else
                            <span class="text-xs text-slate-400">No phone</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="font-medium">No orders found for this category</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="border-t border-slate-100 px-4 py-3">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════════ WHATSAPP MODAL ═══════════════ --}}
    <div x-show="waOpen" x-transition.opacity
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" style="display:none;"
         x-init="
            $watch('waOpen', val => {
                if (val) {
                    let phone = '{{ $waPhone }}';
                    let msg   = encodeURIComponent('{{ addslashes($waMessage) }}');
                    // Strip non-digits, add country code if needed
                    phone = phone.replace(/\D/g, '');
                    if (phone.startsWith('0')) phone = '94' + phone.slice(1);
                    waLink = 'https://wa.me/' + phone + '?text=' + msg;
                    waMsg  = @json($waMessage);
                }
            })
         ">
        <div @click.outside="waOpen = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#25D366]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Send WhatsApp Message</h3>
                        <p class="text-xs text-slate-400">Order {{ $waOrderNum }}</p>
                    </div>
                </div>
                <button @click="waOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-4">
                {{-- Phone --}}
                <div class="flex items-center gap-2 text-sm bg-slate-50 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="font-mono text-slate-700">{{ $waPhone }}</span>
                </div>

                {{-- Message Preview --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Message Preview</p>
                    <div class="bg-[#dcf8c6] rounded-xl p-4 text-sm text-slate-800 whitespace-pre-line font-sans leading-relaxed border border-[#c5e8b3]">{{ $waMessage }}</div>
                </div>

                {{-- Copy --}}
                <button @click="navigator.clipboard.writeText($el.closest('div[x-data]').querySelector('div.bg-\\[\\#dcf8c6\\]').innerText); copied = true; setTimeout(()=>copied=false,2000)"
                        class="w-full flex items-center justify-center gap-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-lg px-4 py-2.5 hover:bg-slate-50 transition-colors">
                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg x-show="copied" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="copied ? 'Copied!' : 'Copy Message'"></span>
                </button>
            </div>
            <div class="px-5 pb-5">
                <a :href="waLink" target="_blank" rel="noopener noreferrer"
                   class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1da851] text-white font-bold text-sm px-4 py-3 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                    </svg>
                    Open in WhatsApp
                </a>
            </div>
        </div>
    </div>

</div>
