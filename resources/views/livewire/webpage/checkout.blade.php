{{-- resources/views/livewire/webpage/checkout.blade.php --}}
<div>
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-10">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-2xl text-white">Checkout</h1>
            {{-- Progress Steps --}}
            @if($step < 4)
            <div class="flex items-center gap-2 mt-4">
                @php $steps = ['Shipping', 'Payment', 'Review']; @endphp
                @foreach($steps as $i => $label)
                @php $stepNum = $i + 1; @endphp
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full {{ $step >= $stepNum ? 'bg-[#F59E0B] text-[#0F172A]' : 'bg-[#1E293B] text-[#64748B]' }} flex items-center justify-center text-xs font-bold">
                        {{ $step > $stepNum ? '✓' : $stepNum }}
                    </div>
                    <span class="text-xs font-semibold {{ $step >= $stepNum ? 'text-white' : 'text-[#475569]' }}">{{ $label }}</span>
                </div>
                @if($stepNum < 3)
                <div class="w-12 h-px {{ $step > $stepNum ? 'bg-[#F59E0B]' : 'bg-[#1E293B]' }}"></div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <section class="py-10 container-page">

        {{-- Step 4: Success --}}
        @if($step === 4)
        <div class="max-w-xl mx-auto py-10 space-y-6">

            {{-- Success header --}}
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-2">Order Placed Successfully!</h2>
                <p class="text-[#64748B] mb-1">Your order number is:</p>
                <p class="font-mono font-bold text-2xl text-[#F59E0B]">{{ $orderNumber }}</p>
            </div>

            {{-- Bank Transfer Block (only if bank_transfer selected) --}}
            @if($paymentMethod === 'bank_transfer')
            <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    <h3 class="font-[Poppins] font-bold text-base text-amber-900">Bank Transfer Instructions</h3>
                </div>
                @if($advanceOption === 'advance')
                <p class="text-sm text-amber-800">
                    Please transfer the <strong>advance amount</strong> to the bank account below, then upload your payment receipt.
                    The remaining balance will be collected on delivery.
                </p>
                @else
                <p class="text-sm text-amber-800">Please transfer the full order amount to the bank account below, then upload your payment receipt.</p>
                @endif

                {{-- Bank details card --}}
                <div class="bg-white rounded-xl border border-amber-200 divide-y divide-amber-100">
                    @if($bankDetails['bank_name'])
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Bank</span>
                        <span class="font-bold text-[#0F172A] text-sm">{{ $bankDetails['bank_name'] }}</span>
                    </div>
                    @endif
                    @if($bankDetails['account_name'])
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Account Name</span>
                        <span class="font-bold text-[#0F172A] text-sm">{{ $bankDetails['account_name'] }}</span>
                    </div>
                    @endif
                    @if($bankDetails['account_number'])
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Account Number</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-[#0F172A] font-mono tracking-widest text-base" id="acct-num">{{ $bankDetails['account_number'] }}</span>
                            <button type="button"
                                    onclick="navigator.clipboard.writeText('{{ $bankDetails['account_number'] }}').then(() => { this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 1500); })"
                                    class="text-xs text-amber-600 hover:text-amber-800 font-semibold px-2 py-0.5 rounded border border-amber-300 hover:border-amber-500 transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>
                    @endif
                    {{-- Amount to transfer — dynamically reflects advance or full choice --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-amber-50">
                        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Amount to Transfer</span>
                        <span class="font-bold text-amber-700 text-base">
                            @php
                                $order = \App\Models\Order::where('order_number', $orderNumber)->first();
                            @endphp
                            Rs. {{ $advanceOption === 'advance'
                                    ? number_format($order?->advance_amount ?? 0, 0)
                                    : number_format($order?->total ?? 0, 0) }}
                        </span>
                    </div>
                </div>

                @if($bankDetails['instructions'])
                <p class="text-xs text-amber-700">{{ $bankDetails['instructions'] }}</p>
                @endif

                {{-- Proof Upload --}}
                @if($proofUploaded)
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                    <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm text-green-800">Payment Receipt Uploaded</p>
                        <p class="text-xs text-green-600">Our team will verify and confirm your order shortly.</p>
                    </div>
                </div>
                @else
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-amber-900">Upload Payment Receipt <span class="text-red-500">*</span></p>
                    <div x-data="{ dragging: false }"
                         @dragover.prevent="dragging = true"
                         @dragleave="dragging = false"
                         @drop.prevent="dragging = false; $refs.proofInput.files = $event.dataTransfer.files; $refs.proofInput.dispatchEvent(new Event('change'))"
                         :class="dragging ? 'border-amber-500 bg-amber-100' : 'border-amber-300 bg-white'"
                         class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                         @click="$refs.proofInput.click()">
                        <input type="file" x-ref="proofInput" wire:model="paymentProofFile"
                               accept="image/*" class="hidden">
                        <template x-if="!$wire.paymentProofFile">
                            <div>
                                <svg class="w-10 h-10 text-amber-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-slate-600 font-medium">Click or drag to upload receipt</p>
                                <p class="text-xs text-slate-400 mt-1">JPG, PNG up to 5MB</p>
                            </div>
                        </template>
                        <div wire:loading wire:target="paymentProofFile" class="flex items-center justify-center gap-2 text-amber-600">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-sm">Uploading...</span>
                        </div>
                    </div>
                    @if($paymentProofFile)
                    <div class="flex items-center gap-3 bg-white border border-amber-200 rounded-xl p-3">
                        <img src="{{ $paymentProofFile->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-lg shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#0F172A] truncate">{{ $paymentProofFile->getClientOriginalName() }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($paymentProofFile->getSize() / 1024, 0) }} KB</p>
                        </div>
                    </div>
                    @endif
                    @error('paymentProofFile')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <button wire:click="uploadProof" wire:loading.attr="disabled"
                            class="btn-primary w-full justify-center">
                        <svg wire:loading wire:target="uploadProof" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="uploadProof">Submit Payment Proof</span>
                        <span wire:loading wire:target="uploadProof">Uploading...</span>
                    </button>
                </div>
                @endif
            </div>
            @else
            <p class="text-sm text-center text-[#64748B]">Our team will review your order and confirm it shortly.</p>
            @endif

            <div class="text-center">
                <a href="{{ route('webpage.shop') }}" class="btn-secondary">Continue Shopping</a>
            </div>
        </div>

        @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Area --}}
            <div class="lg:col-span-2">

                {{-- Step 1: Shipping --}}
                @if($step === 1)
                <div class="card p-6 space-y-4">
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Shipping Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Full Name *</label>
                            <input wire:model="fullName" type="text" class="form-input @error('fullName') border-red-400 @enderror" placeholder="Your full name">
                            @error('fullName')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Email *</label>
                            <input wire:model="email" type="email" class="form-input @error('email') border-red-400 @enderror" placeholder="you@example.com">
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        {{-- Country --}}
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Country *</label>
                            <select wire:model.live="country" class="form-input @error('country') border-red-400 @enderror">
                                <option value="LK">🇱🇰 Sri Lanka</option>
                                <option value="AE">🇦🇪 UAE (Dubai)</option>
                            </select>
                            @error('country')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- Phone with auto country code --}}
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Phone *</label>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-3 py-2 rounded-lg border border-[#E5E7EB] bg-slate-50 text-sm font-semibold text-slate-600 shrink-0">
                                    @if($country === 'LK') 🇱🇰 +94
                                    @elseif($country === 'AE') 🇦🇪 +971
                                    @endif
                                </span>
                                <input wire:model="phoneNumber" type="tel"
                                       class="form-input flex-1 @error('phoneNumber') border-red-400 @enderror"
                                       placeholder="{{ $country === 'LK' ? '761265772' : '501234567' }}">
                            </div>
                            @error('phoneNumber')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Street Address *</label>
                            <input wire:model="addressLine" type="text" class="form-input @error('addressLine') border-red-400 @enderror" placeholder="Building, Street Name, Area">
                            @error('addressLine')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">City *</label>
                            <input wire:model="city" type="text" class="form-input @error('city') border-red-400 @enderror"
                                   placeholder="{{ $country === 'AE' ? 'Dubai' : 'Colombo' }}">
                            @error('city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">{{ $country === 'AE' ? 'Emirate' : 'Province' }} *</label>
                            <select wire:model="region" class="form-input @error('region') border-red-400 @enderror">
                                <option value="">Select...</option>
                                @if($country === 'LK')
                                    @foreach(['Western','Central','Southern','Northern','Eastern','North Western','North Central','Uva','Sabaragamuwa'] as $r)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endforeach
                                @elseif($country === 'AE')
                                    @foreach(['Dubai','Abu Dhabi','Sharjah','Ajman','Fujairah','Ras Al Khaimah','Umm Al Quwain'] as $r)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('region')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Order Notes <span class="font-normal text-[#94A3B8]">(optional)</span></label>
                            <textarea wire:model="notes" rows="2" class="form-input resize-none" placeholder="Special delivery instructions..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button wire:click="nextStep" class="btn-primary">
                            Continue to Payment
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- Step 2: Payment --}}
                @if($step === 2)
                <div class="card p-6 space-y-4">
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Payment Method</h3>

                    <div class="space-y-3">
                        @if(empty($paymentMethods))
                        <p class="text-sm text-slate-500 text-center py-4">No payment methods are currently available. Please contact us.</p>
                        @endif
                        @foreach($paymentMethods as $key => $method)
                        <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $paymentMethod === $key ? 'border-[#F59E0B] bg-[#FFFBEB]' : 'border-[#E2E8F0] hover:border-[#F59E0B]/50' }}">
                            <input wire:model="paymentMethod" type="radio" name="paymentMethod" value="{{ $key }}" class="mt-1 text-[#F59E0B] focus:ring-[#F59E0B]">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-[#F1F5F9] flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $method['icon'] }}"/></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-[#0F172A]">{{ $method['label'] }}</p>
                                    <p class="text-xs text-[#64748B] mt-0.5">{{ $method['desc'] }}</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Advance / Full payment selector (bank transfer only) --}}
                    @if($paymentMethod === 'bank_transfer')
                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <label class="flex items-center gap-4 p-4 cursor-pointer transition-colors {{ $advanceOption === 'full' ? 'bg-amber-50 border-l-4 border-l-amber-400' : 'hover:bg-slate-50' }}"
                               wire:click="$set('advanceOption', 'full')">
                            <input type="radio" wire:model="advanceOption" value="full"
                                   class="text-amber-500 focus:ring-amber-400 shrink-0">
                            <div class="flex-1">
                                <p class="font-semibold text-sm text-[#0F172A]">Full Payment</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Pay the complete amount now:
                                    <strong class="text-[#0F172A]">Rs. {{ number_format($total, 0) }}</strong>
                                </p>
                            </div>
                        </label>
                        <div class="border-t border-slate-100"></div>
                        <label class="flex items-center gap-4 p-4 cursor-pointer transition-colors {{ $advanceOption === 'advance' ? 'bg-amber-50 border-l-4 border-l-amber-400' : 'hover:bg-slate-50' }}"
                               wire:click="$set('advanceOption', 'advance')">
                            <input type="radio" wire:model="advanceOption" value="advance"
                                   class="text-amber-500 focus:ring-amber-400 shrink-0">
                            <div class="flex-1">
                                <p class="font-semibold text-sm text-[#0F172A]">Advance Payment ({{ $advancePct }}%)</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Pay now: <strong class="text-amber-600">Rs. {{ number_format($total * $advancePct / 100, 0) }}</strong>
                                    &bull; Balance on delivery: <strong class="text-[#0F172A]">Rs. {{ number_format($total - ($total * $advancePct / 100), 0) }}</strong>
                                </p>
                            </div>
                        </label>
                    </div>
                    @endif

                    {{-- Bank Transfer Details (shown when bank_transfer selected) --}}
                    @if($paymentMethod === 'bank_transfer' && !empty($bankDetails['account_number']))
                    <div class="rounded-xl border-2 border-amber-300 bg-amber-50 p-4 space-y-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                            <p class="font-semibold text-sm text-amber-800">Bank Account Details</p>
                        </div>
                        <div class="grid grid-cols-1 gap-2 text-sm">
                            @if($bankDetails['bank_name'])
                            <div class="flex justify-between">
                                <span class="text-slate-500">Bank</span>
                                <span class="font-semibold text-[#0F172A]">{{ $bankDetails['bank_name'] }}</span>
                            </div>
                            @endif
                            @if($bankDetails['account_name'])
                            <div class="flex justify-between">
                                <span class="text-slate-500">Account Name</span>
                                <span class="font-semibold text-[#0F172A]">{{ $bankDetails['account_name'] }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Account Number</span>
                                <span class="font-bold text-[#0F172A] font-mono tracking-wider">{{ $bankDetails['account_number'] }}</span>
                            </div>
                        </div>
                        {{-- Dynamic amount to transfer based on advance/full selection --}}
                        <div class="flex items-center justify-between px-3 py-2.5 bg-amber-100 rounded-lg">
                            <span class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Amount to Transfer</span>
                            <span class="font-bold text-amber-700 text-base">
                                Rs. {{ $advanceOption === 'advance' ? number_format($total * $advancePct / 100, 0) : number_format($total, 0) }}
                            </span>
                        </div>
                        @if($bankDetails['instructions'])
                        <p class="text-xs text-amber-700 border-t border-amber-200 pt-2">{{ $bankDetails['instructions'] }}</p>
                        @endif
                        <p class="text-xs text-slate-500">After placing your order, you'll be asked to upload your payment receipt.</p>
                    </div>
                    @endif

                    <div class="flex justify-between pt-2">
                        <button wire:click="prevStep" class="btn-secondary">Back</button>
                        <button wire:click="nextStep" class="btn-primary">
                            Review Order
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- Step 3: Review --}}
                @if($step === 3)
                <div class="space-y-4">
                    {{-- Shipping Summary --}}
                    <div class="card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-sm text-[#0F172A]">Shipping Address</h4>
                            <button wire:click="$set('step', 1)" class="text-xs text-[#F59E0B] font-semibold hover:underline">Edit</button>
                        </div>
                        <p class="text-sm text-[#475569]">{{ $fullName }} &bull; {{ $phone }}</p>
                        <p class="text-sm text-[#475569]">{{ $addressLine }}, {{ $city }}, {{ $region }}</p>
                    </div>

                    {{-- Payment Summary --}}
                    <div class="card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-sm text-[#0F172A]">Payment Method</h4>
                            <button wire:click="$set('step', 2)" class="text-xs text-[#F59E0B] font-semibold hover:underline">Edit</button>
                        </div>
                        <p class="text-sm text-[#475569]">{{ str_replace('_', ' ', ucwords($paymentMethod)) }}</p>
                    </div>

                    {{-- Items --}}
                    <div class="card p-5">
                        <h4 class="font-semibold text-sm text-[#0F172A] mb-3">Order Items ({{ $this->cartItems->count() }})</h4>
                        <div class="space-y-3">
                            @foreach($this->cartItems as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-[#F1F5F9] shrink-0">
                                    <img src="{{ $item->product->primaryImage() }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover"
                                         onerror="this.style.display='none'">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#0F172A] truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-[#64748B]">Qty: {{ $item->quantity }}@if(!empty($item->size)) &bull; Size: {{ $item->size }}@endif</p>
                                </div>
                                <span class="text-sm font-semibold text-[#0F172A] shrink-0">Rs. {{ number_format($item->product->effectivePrice() * $item->quantity, 0) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button wire:click="prevStep" class="btn-secondary">Back</button>
                        <button wire:click="placeOrder" wire:loading.attr="disabled" class="btn-primary btn-lg">
                            <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove>Place Order — Rs. {{ number_format($total, 0) }}</span>
                            <span wire:loading>Placing order...</span>
                        </button>
                    </div>
                </div>
                @endif
            </div>

            {{-- Order Summary Sidebar --}}
            <div>
                <div class="card p-5 sticky top-24 space-y-4">
                    <h3 class="font-[Poppins] font-bold text-base text-[#0F172A]">Order Summary</h3>

                    {{-- Product Cards --}}
                    <div class="space-y-3">
                        @foreach($this->cartItems as $item)
                        <a href="{{ route('webpage.product-details', $item->product->slug) }}"
                           target="_blank"
                           class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-[#F8FAFC] transition-colors group">
                            {{-- Product image --}}
                            <div class="w-14 h-14 rounded-lg overflow-hidden bg-[#F1F5F9] shrink-0 border border-[#E2E8F0]">
                                @if($item->product->primaryImage())
                                <img src="{{ $item->product->primaryImage() }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                     onerror="this.style.display='none'">
                                @endif
                            </div>
                            {{-- Product info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[#0F172A] truncate group-hover:text-[#F59E0B] transition-colors">
                                    {{ $item->product->name }}
                                </p>
                                <p class="text-xs text-[#94A3B8] mt-0.5">Qty: {{ $item->quantity }}@if(!empty($item->size)) &bull; Size: {{ $item->size }}@endif</p>
                            </div>
                            {{-- Price --}}
                            <span class="text-sm font-bold text-[#0F172A] shrink-0">
                                Rs. {{ number_format($item->product->effectivePrice() * $item->quantity, 0) }}
                            </span>
                        </a>
                        @endforeach
                    </div>

                    {{-- Price breakdown --}}
                    <div class="border-t border-[#E2E8F0] pt-3 space-y-2 text-sm">
                        <div class="flex justify-between text-[#475569]"><span>Subtotal</span><span>Rs. {{ number_format($subtotal, 0) }}</span></div>
                        <div class="flex justify-between text-[#475569]">
                            <span>Delivery</span>
                            <span class="{{ $shipping == 0 ? 'text-green-600 font-semibold' : '' }}">
                                {{ $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-[#475569]"><span>Tax</span><span>Rs. {{ number_format($tax, 0) }}</span></div>
                        @if($discountAmount > 0)
                        <div class="flex justify-between text-green-600 font-semibold"><span>Discount</span><span>-Rs. {{ number_format($discountAmount, 0) }}</span></div>
                        @endif
                        <div class="border-t border-[#E2E8F0] pt-2 flex justify-between font-bold text-base">
                            <span class="text-[#0F172A]">Total</span>
                            <span class="text-[#F59E0B]">Rs. {{ number_format($total, 0) }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 text-xs text-[#64748B]">
                        @foreach(['Secure SSL Checkout', 'Easy 30-Day Returns'] as $badge)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            {{ $badge }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>
</div>
