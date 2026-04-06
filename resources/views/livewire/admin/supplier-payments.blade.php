{{-- resources/views/livewire/admin/supplier-payments.blade.php --}}
@section('page_title', 'Supplier Payments')
@section('page_subtitle', 'Track invoices and payments made to suppliers')

<div x-data="{ confirmPay: false }">

    {{-- ══════════════════════════════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════════════════════════════ --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm font-medium shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-[#0F172A] font-[Poppins]">Supplier Payments</h2>
            <p class="text-sm text-slate-500 mt-0.5">Manage invoices and payments owed to your suppliers</p>
        </div>
        <button wire:click="openInvoiceModal"
                class="inline-flex items-center gap-2 bg-[#0F172A] hover:bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Invoice
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         STATS CARDS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- Total Invoiced --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Invoiced</p>
                <p class="text-2xl font-bold text-[#0F172A] font-[Poppins] mt-0.5">
                    {{ number_format($stats['total_invoiced'], 2) }} <span class="text-sm font-medium text-slate-400">Rs.</span>
                </p>
            </div>
        </div>

        {{-- Total Paid --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Paid</p>
                <p class="text-2xl font-bold text-emerald-600 font-[Poppins] mt-0.5">
                    {{ number_format($stats['total_paid'], 2) }} <span class="text-sm font-medium text-slate-400">Rs.</span>
                </p>
            </div>
        </div>

        {{-- Total Due --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Due</p>
                <p class="text-2xl font-bold text-red-500 font-[Poppins] mt-0.5">
                    {{ number_format($stats['total_due'], 2) }} <span class="text-sm font-medium text-slate-400">Rs.</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         FILTER BAR
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-5">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search by supplier or invoice number..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30 transition">
            </div>

            {{-- Status Tabs --}}
            <div class="flex rounded-xl border border-slate-200 overflow-hidden shrink-0">
                @foreach (['' => 'All', 'pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label)
                    <button wire:click="$set('filterStatus', '{{ $val }}')"
                            class="px-4 py-2.5 text-sm font-medium transition-colors
                                   {{ $filterStatus === $val
                                      ? 'bg-[#0F172A] text-white'
                                      : 'bg-white text-slate-500 hover:bg-slate-50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         INVOICES TABLE
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide w-8"></th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Supplier</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Invoice #</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Paid</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Due</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($invoices as $invoice)
                        {{-- Main row --}}
                        <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer"
                            wire:click="toggleRow({{ $invoice->id }})">
                            {{-- Expand chevron --}}
                            <td class="px-5 py-4">
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $expandedInvoiceId === $invoice->id ? 'rotate-90' : '' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-[#0F172A]">{{ $invoice->supplier->name }}</div>
                                @if($invoice->supplier->phone)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $invoice->supplier->phone }}</div>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-mono text-slate-700">{{ $invoice->invoice_number }}</span>
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $invoice->invoice_date->format('d M Y') }}
                            </td>

                            <td class="px-5 py-4 text-right font-semibold text-[#0F172A]">
                                {{ number_format($invoice->total_amount, 2) }}
                            </td>

                            <td class="px-5 py-4 text-right text-emerald-600 font-medium">
                                {{ number_format($invoice->paid_amount, 2) }}
                            </td>

                            <td class="px-5 py-4 text-right font-semibold {{ $invoice->due_amount > 0 ? 'text-red-500' : 'text-slate-400' }}">
                                {{ number_format($invoice->due_amount, 2) }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                @php
                                    $colorMap = ['yellow' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                 'blue'   => 'bg-blue-50 text-blue-700 border-blue-200',
                                                 'green'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                 'gray'   => 'bg-slate-50 text-slate-600 border-slate-200'];
                                    $c = $colorMap[$invoice->statusColor()] ?? $colorMap['gray'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $c }}">
                                    {{ $invoice->statusLabel() }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center" wire:click.stop>
                                @if($invoice->status !== 'paid')
                                    <button wire:click="openPayModal({{ $invoice->id }})"
                                            class="inline-flex items-center gap-1.5 bg-[#0F172A] hover:bg-slate-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Pay
                                    </button>
                                @else
                                    <span class="text-xs text-emerald-600 font-medium">Settled</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Expanded payment history row --}}
                        @if ($expandedInvoiceId === $invoice->id)
                            <tr>
                                <td colspan="9" class="bg-slate-50/80 px-5 py-4 border-b border-slate-100">
                                    <div class="pl-4 border-l-2 border-[#D4A017]">
                                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Payment History for {{ $invoice->invoice_number }}</p>

                                        @if ($expandedPayments && $expandedPayments->count() > 0)
                                            <div class="space-y-2">
                                                @foreach ($expandedPayments as $record)
                                                    <div class="flex flex-wrap items-center gap-3 bg-white rounded-xl border border-slate-100 px-4 py-3">
                                                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <span class="font-semibold text-[#0F172A]">{{ number_format($record->amount, 2) }} Rs.</span>
                                                            <span class="mx-1.5 text-slate-300">·</span>
                                                            <span class="text-slate-500 text-sm">{{ $record->methodLabel() }}</span>
                                                            @if($record->reference)
                                                                <span class="mx-1.5 text-slate-300">·</span>
                                                                <span class="text-slate-400 text-xs font-mono">{{ $record->reference }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="text-xs text-slate-400">{{ $record->paid_at->format('d M Y') }}</span>
                                                        @if($record->notes)
                                                            <span class="text-xs text-slate-400 italic">{{ $record->notes }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-slate-400 italic">No payments recorded yet.</p>
                                        @endif

                                        @if ($invoice->notes)
                                            <p class="mt-3 text-xs text-slate-500"><span class="font-medium">Notes:</span> {{ $invoice->notes }}</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium">No supplier invoices found</p>
                                    <p class="text-xs">Click "Add Invoice" to create your first supplier invoice.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($invoices->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         ADD INVOICE MODAL
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($showInvoiceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-on:keydown.escape.window="$wire.set('showInvoiceModal', false)">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('showInvoiceModal', false)"></div>

            {{-- Panel --}}
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A] font-[Poppins]">Add Supplier Invoice</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Record a new purchase invoice from a supplier</p>
                    </div>
                    <button wire:click="$set('showInvoiceModal', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit="createInvoice" class="px-6 py-5 space-y-4">

                    {{-- Supplier select --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Supplier <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <select wire:model="supplierId"
                                    class="flex-1 text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30 bg-white">
                                <option value="">-- Select Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <button type="button"
                                    wire:click="$toggle('showNewSupplierForm')"
                                    class="shrink-0 px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-500 hover:bg-slate-50 transition"
                                    title="Add new supplier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        @error('supplierId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                        {{-- Flash: supplier created --}}
                        @if (session()->has('supplierCreated'))
                            <p class="mt-1 text-xs text-emerald-600">{{ session('supplierCreated') }}</p>
                        @endif
                    </div>

                    {{-- Inline new supplier form --}}
                    @if ($showNewSupplierForm)
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 space-y-3">
                            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Quick-add Supplier</p>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Name <span class="text-red-500">*</span></label>
                                <input wire:model="newSupplierName" type="text" placeholder="Supplier name"
                                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                                @error('newSupplierName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                                <input wire:model="newSupplierPhone" type="text" placeholder="Phone number (optional)"
                                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                            </div>
                            <button type="button" wire:click="createSupplier"
                                    class="w-full bg-[#0F172A] text-white text-sm font-semibold py-2 rounded-lg hover:bg-slate-800 transition">
                                <span wire:loading.remove wire:target="createSupplier">Create Supplier</span>
                                <span wire:loading wire:target="createSupplier">Creating...</span>
                            </button>
                        </div>
                    @endif

                    {{-- Invoice number --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Invoice Number <span class="text-red-500">*</span></label>
                        <input wire:model="invoiceNumber" type="text" placeholder="e.g. INV-2024-001"
                               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                        @error('invoiceNumber') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Invoice date + Amount (2-col) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Invoice Date <span class="text-red-500">*</span></label>
                            <input wire:model="invoiceDate" type="date"
                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                            @error('invoiceDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Total Amount (Rs.) <span class="text-red-500">*</span></label>
                            <input wire:model="totalAmount" type="number" step="0.01" min="0.01" placeholder="0.00"
                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                            @error('totalAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                        <textarea wire:model="invoiceNotes" rows="2" placeholder="Optional notes..."
                                  class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 resize-none focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30"></textarea>
                    </div>

                    {{-- Footer --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="$set('showInvoiceModal', false)"
                                class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 rounded-xl bg-[#0F172A] text-white text-sm font-semibold hover:bg-slate-800 transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="createInvoice">Save Invoice</span>
                            <span wire:loading wire:target="createInvoice" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════
         PAY MODAL
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($showPayModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-on:keydown.escape.window="$wire.set('showPayModal', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('showPayModal', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A] font-[Poppins]">Record Payment</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Record a payment made to the supplier</p>
                    </div>
                    <button wire:click="$set('showPayModal', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit="recordPayment" class="px-6 py-5 space-y-4">

                    {{-- Amount --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount (Rs.) <span class="text-red-500">*</span></label>
                        <input wire:model="payAmount" type="number" step="0.01" min="0.01" placeholder="0.00"
                               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                        @error('payAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Method + Date (2-col) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Method <span class="text-red-500">*</span></label>
                            <select wire:model="payMethod"
                                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                            @error('payMethod') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                            <input wire:model="payDate" type="date"
                                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                            @error('payDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Reference --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Reference / Transaction ID</label>
                        <input wire:model="payReference" type="text" placeholder="Cheque no., bank ref., etc."
                               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                        <textarea wire:model="payNotes" rows="2" placeholder="Optional notes..."
                                  class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 resize-none focus:outline-none focus:ring-2 focus:ring-[#0F172A]/10 focus:border-[#0F172A]/30"></textarea>
                    </div>

                    {{-- Footer --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="$set('showPayModal', false)"
                                class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="recordPayment">Record Payment</span>
                            <span wire:loading wire:target="recordPayment" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
