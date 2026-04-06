{{-- resources/views/livewire/admin/whatsapp-orders.blade.php --}}
<div
    class="space-y-6"
    x-data="{
        copyDone: false,
        copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.copyDone = true;
                setTimeout(() => this.copyDone = false, 2500);
            });
        }
    }"
>

    {{-- ══════════════════════ PAGE HEADER ══════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-14 -right-10 h-40 w-40 rounded-full bg-emerald-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-amber-400/15 blur-3xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300">Admin &rarr; Orders</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-white flex items-center gap-3 mt-0.5">
                    {{-- WhatsApp icon --}}
                    <svg class="w-6 h-6 shrink-0" style="color:#25D366" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                    </svg>
                    WhatsApp Orders
                </h2>
                <p class="text-sm text-slate-400 mt-1">Generate one-time order links to send to WhatsApp customers.</p>
            </div>
            <button
                wire:click="$set('showGenerateModal', true)"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold px-5 py-2.5 text-sm transition-all duration-200 shadow-lg shadow-amber-400/25 hover:shadow-amber-400/40 hover:-translate-y-0.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Generate New Link
            </button>
        </div>
    </div>

    {{-- ══════════════════════ GENERATED LINK BANNER ══════════════════════ --}}
    @if($generatedLink)
    <div class="relative overflow-hidden rounded-2xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 shadow-sm">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-emerald-200/40 -translate-y-10 translate-x-10 pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-[Poppins] font-bold text-emerald-800 text-base">Order link generated!</h3>
                    <p class="text-sm text-emerald-700 mt-0.5">Copy this link and send it to your customer via WhatsApp. It can only be used once.</p>
                    <div class="mt-3 flex items-center gap-2 bg-white rounded-xl border border-emerald-200 px-4 py-2.5">
                        <code class="text-xs text-slate-700 flex-1 truncate font-mono">{{ $generatedLink }}</code>
                        <button
                            @click="copyText('{{ $generatedLink }}')"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition-all">
                            <svg x-show="!copyDone" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copyDone" class="w-3.5 h-3.5" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="copyDone ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                    {{-- Send via WhatsApp --}}
                    @if($generatedPhone)
                    @php
                        $waMsg = urlencode("Hello! Here is your order link for Meharahouse:\n\n" . $generatedLink . "\n\nPlease click the link to complete your order details and upload your payment receipt. Thank you!");
                        $waUrl = "https://wa.me/{$generatedPhone}?text={$waMsg}";
                    @endphp
                    <div class="mt-3">
                        <a href="{{ $waUrl }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white transition-all hover:-translate-y-0.5"
                           style="background-color:#25D366;">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                            </svg>
                            Send via WhatsApp (+{{ $generatedPhone }})
                        </a>
                    </div>
                    @endif
                </div>
                <button wire:click="dismissGeneratedLink" class="shrink-0 p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════ STATS CARDS ══════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold font-[Poppins] text-[#0F172A]">{{ $totalGenerated }}</p>
                <p class="text-xs text-[#64748B] font-medium mt-0.5">Total Generated</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold font-[Poppins] text-[#0F172A]">{{ $totalPending }}</p>
                <p class="text-xs text-[#64748B] font-medium mt-0.5">Pending (Unused)</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold font-[Poppins] text-[#0F172A]">{{ $totalUsed }}</p>
                <p class="text-xs text-[#64748B] font-medium mt-0.5">Used (Completed)</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ DATE RANGE FILTER ══════════════════════ --}}
    <div class="card p-4 flex items-center gap-2 flex-wrap">
        <div class="flex items-center gap-1.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg px-2 py-1.5">
            <svg class="w-4 h-4 text-[#94A3B8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <input wire:model.live="dateFrom" type="date"
                   class="text-xs text-[#475569] bg-transparent border-none outline-none w-32">
            <span class="text-xs text-[#94A3B8]">—</span>
            <input wire:model.live="dateTo" type="date"
                   class="text-xs text-[#475569] bg-transparent border-none outline-none w-32">
        </div>
        {{-- Quick Presets --}}
        <div class="flex gap-1" x-data="{
            setRange(from, to) {
                $wire.set('dateFrom', from);
                $wire.set('dateTo', to);
            },
            today() {
                let d = new Date().toISOString().split('T')[0];
                this.setRange(d, d);
            },
            last7() {
                let to   = new Date();
                let from = new Date(); from.setDate(from.getDate() - 6);
                this.setRange(from.toISOString().split('T')[0], to.toISOString().split('T')[0]);
            },
            thisMonth() {
                let now  = new Date();
                let from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                let to   = now.toISOString().split('T')[0];
                this.setRange(from, to);
            },
            lastMonth() {
                let now  = new Date();
                let from = new Date(now.getFullYear(), now.getMonth()-1, 1).toISOString().split('T')[0];
                let to   = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().split('T')[0];
                this.setRange(from, to);
            }
        }">
            <button @click="today()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Today</button>
            <button @click="last7()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">7d</button>
            <button @click="thisMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Month</button>
            <button @click="lastMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Last Mo</button>
            @if($dateFrom || $dateTo)
            <button wire:click="clearDates"
                    class="px-2 py-1 text-[10px] font-semibold rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                Clear
            </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════ STATUS FILTER TABS ══════════════════════ --}}
    <div class="flex flex-wrap gap-2 p-2 rounded-2xl bg-white/80 border border-slate-200 shadow-sm">
        @foreach(['all' => 'All Tokens', 'pending' => 'Pending', 'used' => 'Used', 'expired' => 'Expired'] as $key => $label)
        <button wire:click="$set('filterStatus', '{{ $key }}')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 border
                    {{ $filterStatus === $key
                        ? 'bg-blue-600 text-white border-blue-700 shadow-lg shadow-blue-600/25 -translate-y-px'
                        : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900 hover:border-slate-300 hover:-translate-y-px' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $filterStatus === $key ? 'bg-white' : 'bg-slate-300' }}"></span>
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════ TOKENS TABLE ══════════════════════ --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Products</th>
                        <th>Advance Due</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Used At</th>
                        <th>Order #</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                    @php
                        $statusBadge = match($token->status) {
                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'used'    => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'expired' => 'bg-red-100 text-red-700 border-red-200',
                            default   => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                        $productNames = collect($token->products)->pluck('product_name')->implode(', ');
                    @endphp
                    <tr wire:key="token-{{ $token->id }}">
                        <td>
                            <code class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                                {{ substr($token->token, 0, 8) }}...
                            </code>
                        </td>
                        <td>
                            <div class="max-w-[200px]">
                                <p class="text-xs text-[#0F172A] truncate" title="{{ $productNames }}">{{ $productNames }}</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">{{ count($token->products) }} {{ count($token->products) === 1 ? 'item' : 'items' }}</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold text-sm text-[#0F172A]">Rs. {{ number_format($token->advance_amount, 0) }}</p>
                                <p class="text-[10px] text-[#94A3B8]">of Rs. {{ number_format($token->subtotal, 0) }} total</p>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide {{ $statusBadge }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $token->status === 'pending' ? 'bg-amber-500' : ($token->status === 'used' ? 'bg-emerald-500' : 'bg-red-500') }}"></span>
                                {{ ucfirst($token->status) }}
                            </span>
                        </td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $token->created_at->format('d M Y') }}</span></td>
                        <td>
                            @if($token->used_at)
                                <span class="text-xs text-[#94A3B8]">{{ $token->used_at->format('d M Y') }}</span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                        <td>
                            @if($token->order)
                                <a href="{{ route('admin.orders') }}"
                                   class="font-mono text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $token->order->order_number }}
                                </a>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                @if($token->status === 'pending')
                                {{-- Copy link button --}}
                                <button
                                    @click="copyText('{{ route('whatsapp.order.form', ['token' => $token->token]) }}')"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[10px] font-semibold transition-all duration-200 hover:-translate-y-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Copy Link
                                </button>
                                @endif
                                @if($token->notes)
                                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 text-slate-500 text-[10px] cursor-help" title="{{ $token->notes }}">
                                    Note
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-16">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <p class="text-[#94A3B8] text-sm">No tokens found for this filter.</p>
                                <button wire:click="$set('showGenerateModal', true)"
                                        class="text-xs font-semibold text-blue-600 hover:text-blue-800">Generate your first link</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tokens->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">{{ $tokens->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         GENERATE TOKEN MODAL
         Note: wire:ignore.self on backdrop prevents Livewire re-renders
         from closing the Alpine-controlled modal.
    ══════════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ open: @entangle('showGenerateModal') }"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        style="display:none;"
        wire:ignore.self
        @click.self="open = false; $wire.set('showGenerateModal', false)"
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            @click.stop
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 sticky top-0 bg-white z-10 rounded-t-2xl">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Generate WhatsApp Order Link</h3>
                    <p class="text-xs text-[#64748B] mt-0.5">Select products and send the link to your customer.</p>
                </div>
                <button
                    @click="open = false; $wire.set('showGenerateModal', false)"
                    class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">

                {{-- Validation error for products --}}
                @error('selectedProducts')
                <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ $message }}
                </div>
                @enderror

                {{-- Product Search --}}
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-2">Search & Add Products</label>
                    <div class="relative">
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20 transition-all">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                wire:model.live.debounce.300ms="searchProduct"
                                type="text"
                                placeholder="Type product name to search..."
                                class="bg-transparent outline-none flex-1 text-sm text-[#0F172A] placeholder-slate-400"
                            >
                            <svg wire:loading wire:target="updatedSearchProduct" class="w-4 h-4 text-amber-500 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>

                        {{-- Search Results Dropdown --}}
                        @if(count($productResults) > 0)
                        <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-20 overflow-hidden">
                            @foreach($productResults as $result)
                            <button
                                wire:click="addProduct({{ $result['id'] }}, '{{ addslashes($result['name']) }}', {{ $result['price'] }})"
                                class="w-full flex items-center justify-between px-4 py-3 hover:bg-amber-50 transition-colors text-left border-b border-slate-50 last:border-0">
                                <span class="text-sm font-medium text-[#0F172A]">{{ $result['name'] }}</span>
                                <span class="text-sm font-bold text-amber-600 shrink-0 ml-4">Rs. {{ number_format($result['price'], 0) }}</span>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Selected Products --}}
                @if(count($selectedProducts) > 0)
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-2">Selected Products</label>
                    <div class="space-y-2">
                        @foreach($selectedProducts as $index => $product)
                        <div wire:key="selected-{{ $index }}-{{ $product['id'] }}"
                             class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">{{ $product['name'] }}</p>
                                <div class="flex items-center gap-1 mt-1">
                                    <span class="text-xs text-slate-400">Rs.</span>
                                    <input type="number"
                                           min="0" step="0.01"
                                           value="{{ $product['price'] }}"
                                           wire:change="updatePrice({{ $index }}, $event.target.value)"
                                           class="w-24 text-xs font-semibold text-amber-700 bg-white border border-slate-200 rounded-lg px-2 py-1 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400/30">
                                    <span class="text-xs text-slate-400">each</span>
                                </div>
                            </div>
                            {{-- Quantity Spinner --}}
                            <div class="flex items-center gap-1 shrink-0">
                                <button
                                    wire:click="updateQuantity({{ $index }}, {{ max(1, ($product['quantity'] ?? 1) - 1) }})"
                                    class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-sm">
                                    -
                                </button>
                                <span class="w-8 text-center text-sm font-bold text-[#0F172A]">{{ $product['quantity'] ?? 1 }}</span>
                                <button
                                    wire:click="updateQuantity({{ $index }}, {{ ($product['quantity'] ?? 1) + 1 }})"
                                    class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-sm">
                                    +
                                </button>
                            </div>
                            {{-- Line Total --}}
                            <span class="text-sm font-bold text-[#0F172A] w-24 text-right shrink-0">
                                Rs. {{ number_format($product['price'] * ($product['quantity'] ?? 1), 0) }}
                            </span>
                            {{-- Remove --}}
                            <button
                                wire:click="removeProduct({{ $index }})"
                                class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Payment Summary --}}
                @if(count($selectedProducts) > 0)
                <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-5">
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4">Payment Summary</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Order Total</span>
                            <span class="font-semibold text-white">Rs. {{ number_format($this->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Advance ({{ $this->advancePercentage }}%)</span>
                            <span class="font-bold text-amber-400 text-base">Rs. {{ number_format($this->advanceAmount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm border-t border-slate-700 pt-2 mt-2">
                            <span class="text-slate-400">Balance Due Later</span>
                            <span class="text-slate-300 font-semibold">Rs. {{ number_format($this->subtotal - $this->advanceAmount, 0) }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-3">Advance % is configured in <strong class="text-slate-400">Website Settings</strong></p>
                </div>
                @endif

                {{-- Customer WhatsApp Number --}}
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Customer WhatsApp Number
                        <span class="text-slate-400 font-normal ml-1">(optional — enables direct send)</span>
                    </label>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-400/20 transition-all">
                        <svg class="w-4 h-4 shrink-0" style="color:#25D366" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                        </svg>
                        <input wire:model="customerPhone" type="tel"
                               placeholder="e.g. +94771234567"
                               class="bg-transparent outline-none flex-1 text-sm text-[#0F172A] placeholder-slate-400">
                    </div>
                    <p class="text-xs text-slate-400 mt-1">If provided, a "Send via WhatsApp" button will appear after generating the link.</p>
                </div>

                {{-- Notes Field --}}
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Admin Note (optional)</label>
                    <input
                        wire:model="notes"
                        type="text"
                        placeholder="e.g. For Ahmed — WhatsApp customer, blue dress"
                        class="form-input w-full">
                    <p class="text-xs text-slate-400 mt-1">This note is only visible to admins. Not shown to the customer.</p>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button
                    @click="open = false; $wire.set('showGenerateModal', false)"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-all">
                    Cancel
                </button>
                <button
                    wire:click="generateToken"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-sm transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed shadow-lg shadow-amber-400/25">
                    <svg wire:loading.remove wire:target="generateToken" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <svg wire:loading wire:target="generateToken" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Generate Link
                </button>
            </div>
        </div>
    </div>

</div>
