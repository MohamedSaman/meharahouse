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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
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

        @php
        $statusColors = [
            'new'              => 'bg-slate-100 text-slate-700',
            'payment_received' => 'bg-blue-100 text-blue-700',
            'confirmed'        => 'bg-indigo-100 text-indigo-700',
            'sourcing'         => 'bg-orange-100 text-orange-700',
            'dispatched'       => 'bg-purple-100 text-purple-700',
            'delivered'        => 'bg-teal-100 text-teal-700',
            'completed'        => 'bg-green-100 text-green-700',
            'cancelled'        => 'bg-red-100 text-red-700',
            'refunded'         => 'bg-pink-100 text-pink-700',
        ];
        $statusLabels = [
            'new'              => 'New',
            'payment_received' => 'Payment Received',
            'confirmed'        => 'Confirmed',
            'sourcing'         => 'Sourcing',
            'dispatched'       => 'Dispatched',
            'delivered'        => 'Delivered',
            'completed'        => 'Completed',
            'cancelled'        => 'Cancelled',
            'refunded'         => 'Refunded',
        ];
        $paymentColors = [
            'pending' => 'text-amber-600',
            'partial' => 'text-blue-600',
            'paid'    => 'text-green-600',
            'failed'  => 'text-red-600',
            'refunded'=> 'text-pink-600',
        ];
        @endphp

        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="card overflow-hidden" wire:key="{{ $order->id }}">
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
                            <p class="font-bold text-[#0F172A]">Rs. {{ number_format($order->total, 0) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>
                        <button wire:click="viewOrder({{ $order->id }})" class="text-xs text-[#F59E0B] font-semibold hover:underline">Details</button>
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
        @php
            $selOrder  = $selectedOrder;
            $payments  = $selOrder->payments ?? collect();
            $isBankTransfer = $selOrder->payment_method === 'bank_transfer';
            $balanceDue     = $selOrder->balanceDue();
            $canUploadBalance = $isBankTransfer
                && $balanceDue > 0
                && !in_array($selOrder->status, ['cancelled', 'completed', 'refunded'])
                && $payments->where('type', 'balance')->where('status', 'pending')->isEmpty();

            $bankDetails = [
                'account_name'   => \App\Models\Setting::get('payment_bank_account_name', ''),
                'account_number' => \App\Models\Setting::get('payment_bank_account_number', ''),
                'bank_name'      => \App\Models\Setting::get('payment_bank_name', ''),
            ];

            $paymentStatusLabel = [
                'pending' => 'Awaiting Payment',
                'partial' => 'Partially Paid',
                'paid'    => 'Fully Paid',
                'failed'  => 'Payment Failed',
                'refunded'=> 'Refunded',
            ];
            $paymentStatusColor = [
                'pending' => 'bg-amber-100 text-amber-700',
                'partial' => 'bg-blue-100 text-blue-700',
                'paid'    => 'bg-green-100 text-green-700',
                'failed'  => 'bg-red-100 text-red-700',
                'refunded'=> 'bg-pink-100 text-pink-700',
            ];
            $receiptStatusColor = [
                'pending'   => 'bg-amber-100 text-amber-700',
                'confirmed' => 'bg-green-100 text-green-700',
                'rejected'  => 'bg-red-100 text-red-700',
            ];
            $receiptTypeLabel = [
                'advance' => 'Advance',
                'balance' => 'Balance',
                'full'    => 'Full',
            ];
        @endphp
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white z-10">
                    <div>
                        <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">{{ $selOrder->order_number }}</h3>
                        <p class="text-xs text-[#64748B]">{{ $selOrder->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <button wire:click="$set('showDetail', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Status row --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$selOrder->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$selOrder->status] ?? ucfirst($selOrder->status) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $paymentStatusColor[$selOrder->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $paymentStatusLabel[$selOrder->payment_status] ?? ucfirst($selOrder->payment_status) }}
                        </span>
                        @if($isBankTransfer)
                        <span class="text-xs text-slate-500">Bank Transfer</span>
                        @endif
                    </div>

                    {{-- Items --}}
                    <div>
                        <h4 class="font-semibold text-sm text-[#0F172A] mb-3">Items</h4>
                        <div class="space-y-2">
                            @foreach($selOrder->items as $item)
                            <div class="flex justify-between text-sm py-2 border-b border-[#F1F5F9] last:border-0">
                                <div>
                                    <p class="font-medium text-[#0F172A]">{{ $item->product_name }}</p>
                                    <p class="text-xs text-[#64748B]">Rs. {{ number_format($item->price, 0) }} x {{ $item->quantity }}</p>
                                </div>
                                <span class="font-semibold">Rs. {{ number_format($item->subtotal, 0) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm">
                        <div class="flex justify-between text-[#64748B]"><span>Subtotal</span><span>Rs. {{ number_format($selOrder->subtotal, 0) }}</span></div>
                        <div class="flex justify-between text-[#64748B]"><span>Shipping</span><span>Rs. {{ number_format($selOrder->shipping_cost, 0) }}</span></div>
                        <div class="flex justify-between text-[#64748B]"><span>Tax</span><span>Rs. {{ number_format($selOrder->tax, 0) }}</span></div>
                        @if($selOrder->discount > 0)
                        <div class="flex justify-between text-green-600 font-semibold"><span>Discount</span><span>-Rs. {{ number_format($selOrder->discount, 0) }}</span></div>
                        @endif
                        <div class="flex justify-between font-bold border-t border-[#E2E8F0] pt-2">
                            <span>Total</span><span class="text-[#F59E0B]">Rs. {{ number_format($selOrder->total, 0) }}</span>
                        </div>
                        @if($isBankTransfer && $selOrder->advance_amount > 0 && $selOrder->balance_amount > 0)
                        <div class="flex justify-between text-[#64748B] text-xs pt-1 border-t border-[#E2E8F0]">
                            <span>Advance Paid ({{ $selOrder->advance_percentage }}%)</span>
                            <span>Rs. {{ number_format($selOrder->advance_amount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-xs font-semibold {{ $balanceDue > 0 ? 'text-amber-600' : 'text-green-600' }}">
                            <span>Balance Due</span>
                            <span>Rs. {{ number_format($balanceDue, 0) }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- ── Payment Section ─────────────────────────────── --}}
                    @if($isBankTransfer)
                    <div class="space-y-3">
                        <h4 class="font-semibold text-sm text-[#0F172A]">Payment Records</h4>

                        @if($payments->isEmpty())
                        <p class="text-xs text-slate-400 italic">No payment receipts uploaded yet.</p>
                        @else
                        <div class="space-y-2">
                            @foreach($payments as $payment)
                            <div class="rounded-xl border bg-slate-50 text-sm overflow-hidden" wire:key="pay-{{ $payment->id }}">
                                <div class="flex items-center justify-between p-3 {{ $payment->status === 'rejected' ? 'border-b border-red-100' : '' }}">
                                    <div>
                                        <p class="font-semibold text-[#0F172A]">
                                            {{ $receiptTypeLabel[$payment->type] ?? ucfirst($payment->type) }} Payment
                                            &bull; Rs. {{ number_format($payment->amount, 0) }}
                                        </p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $payment->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $receiptStatusColor[$payment->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        {{-- Bug 6: Re-upload button for rejected payments --}}
                                        @if($payment->status === 'rejected' && !in_array($selOrder->status, ['cancelled', 'completed', 'refunded']))
                                        <button wire:click="openReupload({{ $payment->id }})"
                                                class="text-xs px-2.5 py-1 rounded-lg bg-red-50 border border-red-200 text-red-700 font-semibold hover:bg-red-100 transition-colors">
                                            Re-upload
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Re-upload form inline, shown when this payment is selected for re-upload --}}
                                @if($payment->status === 'rejected' && $reuploadPaymentId === $payment->id)
                                <div class="p-3 bg-red-50 space-y-3">
                                    <p class="text-xs font-semibold text-red-700">Your receipt was rejected. Please upload a new, clear photo of your payment receipt.</p>

                                    <div x-data="{ dragging: false }"
                                         @dragover.prevent="dragging = true"
                                         @dragleave="dragging = false"
                                         @drop.prevent="dragging = false; $refs.reInput{{ $payment->id }}.files = $event.dataTransfer.files; $refs.reInput{{ $payment->id }}.dispatchEvent(new Event('change'))"
                                         :class="dragging ? 'border-red-500 bg-red-100' : 'border-red-300 bg-white'"
                                         class="border-2 border-dashed rounded-xl p-4 text-center transition-colors cursor-pointer"
                                         @click="$refs.reInput{{ $payment->id }}.click()">
                                        <input type="file" x-ref="reInput{{ $payment->id }}" wire:model="reuploadProofFile"
                                               accept="image/*" class="hidden">
                                        <template x-if="!$wire.reuploadProofFile">
                                            <div>
                                                <svg class="w-8 h-8 text-red-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                <p class="text-xs text-slate-600 font-medium">Click or drag to upload new receipt</p>
                                            </div>
                                        </template>
                                        <div wire:loading wire:target="reuploadProofFile" class="flex items-center justify-center gap-2 text-red-600">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            <span class="text-xs">Processing...</span>
                                        </div>
                                    </div>

                                    @if($reuploadProofFile)
                                    <div class="flex items-center gap-3 bg-white border border-red-200 rounded-xl p-2">
                                        <img src="{{ $reuploadProofFile->temporaryUrl() }}" class="w-12 h-12 object-cover rounded-lg shrink-0">
                                        <p class="text-xs text-slate-600 truncate flex-1">{{ $reuploadProofFile->getClientOriginalName() }}</p>
                                    </div>
                                    @endif

                                    @error('reuploadProofFile')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                    <div class="flex gap-2">
                                        <button wire:click="$set('reuploadPaymentId', null)"
                                                class="flex-1 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                            Cancel
                                        </button>
                                        <button wire:click="submitReupload" wire:loading.attr="disabled"
                                                class="flex-1 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">
                                            <span wire:loading.remove wire:target="submitReupload">Submit New Receipt</span>
                                            <span wire:loading wire:target="submitReupload">Uploading...</span>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Balance Due Badge --}}
                        @if($balanceDue > 0)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200">
                            <div>
                                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Balance Due</p>
                                <p class="text-xs text-amber-600 mt-0.5">Please transfer to our bank account and upload your receipt.</p>
                            </div>
                            <span class="font-bold text-amber-700 text-base">Rs. {{ number_format($balanceDue, 0) }}</span>
                        </div>
                        @endif

                        {{-- Balance Upload Form --}}
                        @if($canUploadBalance)
                        <div x-data="{ open: @entangle('uploadingBalanceOrderId').live !== null }" class="space-y-3">

                            @if(!$balanceProofUploaded)
                            {{-- Bank details reminder --}}
                            @if($bankDetails['account_number'])
                            <div class="rounded-xl bg-white border border-amber-200 divide-y divide-amber-100">
                                @if($bankDetails['bank_name'])
                                <div class="flex justify-between items-center px-4 py-2.5 text-sm">
                                    <span class="text-slate-500 text-xs">Bank</span>
                                    <span class="font-semibold text-[#0F172A]">{{ $bankDetails['bank_name'] }}</span>
                                </div>
                                @endif
                                @if($bankDetails['account_name'])
                                <div class="flex justify-between items-center px-4 py-2.5 text-sm">
                                    <span class="text-slate-500 text-xs">Account Name</span>
                                    <span class="font-semibold text-[#0F172A]">{{ $bankDetails['account_name'] }}</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between px-4 py-2.5 text-sm">
                                    <span class="text-slate-500 text-xs">Account Number</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[#0F172A] font-mono tracking-wider">{{ $bankDetails['account_number'] }}</span>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $bankDetails['account_number'] }}').then(() => { this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 1500); })"
                                                class="text-xs text-amber-600 hover:text-amber-800 font-semibold px-2 py-0.5 rounded border border-amber-300 hover:border-amber-500 transition-colors">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between px-4 py-2.5 bg-amber-50">
                                    <span class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Transfer Amount</span>
                                    <span class="font-bold text-amber-700">Rs. {{ number_format($balanceDue, 0) }}</span>
                                </div>
                            </div>
                            @endif

                            {{-- Upload area --}}
                            <p class="text-sm font-semibold text-[#0F172A]">Upload Balance Payment Receipt <span class="text-red-500">*</span></p>

                            <div x-data="{ dragging: false }"
                                 @dragover.prevent="dragging = true"
                                 @dragleave="dragging = false"
                                 @drop.prevent="dragging = false; $refs.balInput.files = $event.dataTransfer.files; $refs.balInput.dispatchEvent(new Event('change'))"
                                 :class="dragging ? 'border-amber-500 bg-amber-100' : 'border-amber-300 bg-white'"
                                 class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                                 @click="$refs.balInput.click()">
                                <input type="file" x-ref="balInput" wire:model="balanceProofFile"
                                       accept="image/*" class="hidden">
                                <template x-if="!$wire.balanceProofFile">
                                    <div>
                                        <svg class="w-10 h-10 text-amber-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm text-slate-600 font-medium">Click or drag to upload receipt</p>
                                        <p class="text-xs text-slate-400 mt-1">JPG, PNG up to 5MB</p>
                                    </div>
                                </template>
                                <div wire:loading wire:target="balanceProofFile" class="flex items-center justify-center gap-2 text-amber-600">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    <span class="text-sm">Processing...</span>
                                </div>
                            </div>

                            @if($balanceProofFile)
                            <div class="flex items-center gap-3 bg-white border border-amber-200 rounded-xl p-3">
                                <img src="{{ $balanceProofFile->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-lg shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">{{ $balanceProofFile->getClientOriginalName() }}</p>
                                    <p class="text-xs text-slate-400">{{ number_format($balanceProofFile->getSize() / 1024, 0) }} KB</p>
                                </div>
                            </div>
                            @endif

                            @error('balanceProofFile')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror

                            <button wire:click="uploadBalanceProof" wire:loading.attr="disabled"
                                    class="btn-primary w-full justify-center">
                                <svg wire:loading wire:target="uploadBalanceProof" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span wire:loading.remove wire:target="uploadBalanceProof">Submit Balance Receipt</span>
                                <span wire:loading wire:target="uploadBalanceProof">Uploading...</span>
                            </button>
                            @endif

                            {{-- Uploaded confirmation --}}
                            @if($balanceProofUploaded)
                            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                                <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-sm text-green-800">Balance Receipt Uploaded</p>
                                    <p class="text-xs text-green-600">Our team will verify and update your order shortly.</p>
                                </div>
                            </div>
                            @endif

                        </div>
                        @elseif($isBankTransfer && $payments->where('type', 'balance')->where('status', 'pending')->isNotEmpty())
                        {{-- A balance receipt is pending admin review --}}
                        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-amber-700 font-semibold">Balance receipt under review by our team.</p>
                        </div>
                        @endif

                    </div>
                    @endif

                    {{-- Shipping --}}
                    <div class="bg-[#F8FAFC] rounded-xl p-4 text-sm">
                        <h4 class="font-semibold text-[#0F172A] mb-2">Delivery Address</h4>
                        <p class="text-[#475569]">{{ $selOrder->shipping_address['full_name'] ?? '' }}</p>
                        <p class="text-[#475569]">{{ $selOrder->shipping_address['address'] ?? '' }}, {{ $selOrder->shipping_address['city'] ?? '' }}</p>
                        <p class="text-[#475569]">{{ $selOrder->shipping_address['phone'] ?? '' }}</p>
                    </div>

                </div>
            </div>
        </div>
        @endif
    </section>
</div>
