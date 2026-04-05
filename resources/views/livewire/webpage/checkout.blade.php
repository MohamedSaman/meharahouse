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
        <div class="text-center py-16 max-w-lg mx-auto">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-2">Order Placed Successfully!</h2>
            <p class="text-[#64748B] mb-1">Your order number is:</p>
            <p class="font-mono font-bold text-xl text-[#F59E0B] mb-5">{{ $orderNumber }}</p>
            <p class="text-sm text-[#64748B] mb-8">We'll send a confirmation to your email. Expect delivery within 2-3 business days.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('webpage.orders') }}" class="btn-primary btn-lg">View My Orders</a>
                <a href="{{ route('webpage.shop') }}" class="btn-secondary btn-lg">Continue Shopping</a>
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
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Phone *</label>
                            <input wire:model="phone" type="tel" class="form-input @error('phone') border-red-400 @enderror" placeholder="+251 911 000 000">
                            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Street Address *</label>
                            <input wire:model="addressLine" type="text" class="form-input @error('addressLine') border-red-400 @enderror" placeholder="Building, Street Name, Area">
                            @error('addressLine')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">City *</label>
                            <input wire:model="city" type="text" class="form-input @error('city') border-red-400 @enderror" placeholder="Addis Ababa">
                            @error('city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Region *</label>
                            <select wire:model="region" class="form-input @error('region') border-red-400 @enderror">
                                <option value="">Select region...</option>
                                @foreach(['Addis Ababa','Amhara','Oromia','Tigray','SNNPR','Afar','Somali','Gambela','Benishangul-Gumuz','Harari','Dire Dawa'] as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
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
                        @php $methods = ['cash_on_delivery'=>['label'=>'Cash on Delivery','desc'=>'Pay when your order arrives at your door.','icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z'], 'bank_transfer'=>['label'=>'Bank Transfer','desc'=>'Transfer to our bank account (CBE, Awash, Abyssinia).','icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'], 'mobile_money'=>['label'=>'Mobile Money','desc'=>'Pay via Telebirr or other mobile payment services.','icon'=>'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z']]; @endphp
                        @foreach($methods as $key => $method)
                        <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $paymentMethod === $key ? 'border-[#F59E0B] bg-[#FFFBEB]' : 'border-[#E2E8F0] hover:border-[#F59E0B]/50' }}">
                            <input wire:model="paymentMethod" type="radio" value="{{ $key }}" class="mt-1 text-[#F59E0B] focus:ring-[#F59E0B]">
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
                                    <p class="text-xs text-[#64748B]">Qty: {{ $item->quantity }}</p>
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
                <div class="card p-5 sticky top-24">
                    <h3 class="font-[Poppins] font-bold text-base text-[#0F172A] mb-4">Order Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-[#475569]"><span>Subtotal</span><span>Rs. {{ number_format($subtotal, 0) }}</span></div>
                        <div class="flex justify-between text-[#475569]"><span>Shipping</span><span class="{{ $shipping === 0 ? 'text-green-600 font-semibold' : '' }}">{{ $shipping === 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 0) }}</span></div>
                        <div class="flex justify-between text-[#475569]"><span>Tax (15%)</span><span>Rs. {{ number_format($tax, 0) }}</span></div>
                        @if($discountAmount > 0)
                        <div class="flex justify-between text-green-600 font-semibold"><span>Discount</span><span>-Rs. {{ number_format($discountAmount, 0) }}</span></div>
                        @endif
                        <div class="border-t border-[#E2E8F0] pt-2 flex justify-between font-bold text-base">
                            <span class="text-[#0F172A]">Total</span>
                            <span class="text-[#0F172A]">Rs. {{ number_format($total, 0) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-2 text-xs text-[#64748B]">
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
