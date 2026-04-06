{{-- resources/views/livewire/webpage/cart.blade.php --}}
<div>
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-3xl text-white">Shopping Cart</h1>
            <p class="text-[#64748B] mt-1 text-sm">{{ $this->cartItems->count() }} item(s) in your cart</p>
        </div>
    </div>

    <section class="py-10 container-page">

        {{-- Flash --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($this->cartItems->isEmpty())
        {{-- Empty Cart --}}
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-[#F1F5F9] rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-12 h-12 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-2">Your Cart is Empty</h3>
            <p class="text-[#64748B] mb-6">Browse our collection and add items you love.</p>
            <a href="{{ route('webpage.shop') }}" class="btn-primary btn-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Start Shopping
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="card overflow-hidden">
                    <div class="px-5 py-3 bg-[#F8FAFC] border-b border-[#E2E8F0] flex items-center justify-between">
                        <span class="text-xs font-bold text-[#64748B] uppercase tracking-wider">Product</span>
                        <div class="hidden sm:flex items-center gap-16 text-xs font-bold text-[#64748B] uppercase tracking-wider">
                            <span>Qty</span>
                            <span>Total</span>
                        </div>
                    </div>

                    <div class="divide-y divide-[#F1F5F9]">
                        @foreach($this->cartItems as $item)
                        <div class="px-5 py-4 flex items-center gap-4" wire:key="{{ $item->id ?? $item->product->id }}">
                            {{-- Image --}}
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-[#F1F5F9] shrink-0">
                                <img src="{{ $item->product->primaryImage() }}" alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=200&auto=format&fit=crop&q=80'">
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('webpage.product-details', $item->product->slug) }}"
                                   class="font-[Poppins] font-semibold text-sm text-[#0F172A] hover:text-[#D97706] leading-snug block truncate">
                                    {{ $item->product->name }}
                                </a>
                                <p class="text-xs text-[#64748B] mt-0.5">{{ $item->product->category?->name ?? '' }}</p>
                                <p class="font-semibold text-sm text-[#0F172A] mt-1">Rs. {{ number_format($item->product->effectivePrice(), 0) }}</p>
                                {{-- Remove (mobile) --}}
                                <button wire:click="remove({{ $item->id ?? $item->product->id }})"
                                        class="text-xs text-red-400 hover:text-red-600 mt-1 sm:hidden">Remove</button>
                            </div>
                            {{-- Quantity --}}
                            <div class="flex items-center gap-2 shrink-0">
                                <button wire:click="updateQuantity({{ $item->id ?? $item->product->id }}, {{ $item->quantity - 1 }})"
                                        class="w-7 h-7 rounded-lg border border-[#E2E8F0] flex items-center justify-center text-[#475569] hover:border-[#F59E0B] hover:text-[#F59E0B] transition-colors text-sm font-bold">-</button>
                                <span class="w-8 text-center text-sm font-bold text-[#0F172A]">{{ $item->quantity }}</span>
                                <button wire:click="updateQuantity({{ $item->id ?? $item->product->id }}, {{ $item->quantity + 1 }})"
                                        class="w-7 h-7 rounded-lg border border-[#E2E8F0] flex items-center justify-center text-[#475569] hover:border-[#F59E0B] hover:text-[#F59E0B] transition-colors text-sm font-bold">+</button>
                            </div>
                            {{-- Subtotal + Remove --}}
                            <div class="text-right shrink-0 hidden sm:block">
                                <p class="font-bold text-sm text-[#0F172A]">Rs. {{ number_format($item->product->effectivePrice() * $item->quantity, 0) }}</p>
                                <button wire:click="remove({{ $item->id ?? $item->product->id }})"
                                        class="text-xs text-red-400 hover:text-red-600 mt-1">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="card p-5">
                    <h3 class="font-semibold text-sm text-[#0F172A] mb-3">Have a Coupon Code?</h3>
                    @if($appliedCoupon)
                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div>
                            <p class="text-sm font-bold text-green-700">{{ $appliedCoupon->code }}</p>
                            <p class="text-xs text-green-600">{{ $couponSuccess }}</p>
                        </div>
                        <button wire:click="removeCoupon" class="text-xs text-red-500 font-semibold hover:underline">Remove</button>
                    </div>
                    @else
                    <div class="flex gap-2">
                        <input wire:model="couponCode" type="text" placeholder="Enter coupon code" class="form-input flex-1 uppercase" style="text-transform: uppercase;">
                        <button wire:click="applyCoupon" class="btn-secondary btn-sm shrink-0">Apply</button>
                    </div>
                    @if($couponError)
                    <p class="text-xs text-red-500 mt-1.5">{{ $couponError }}</p>
                    @endif
                    @endif
                </div>

                {{-- Continue Shopping --}}
                <a href="{{ route('webpage.shop') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#475569] hover:text-[#F59E0B] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Continue Shopping
                </a>
            </div>

            {{-- Order Summary --}}
            <div class="space-y-4">
                <div class="card p-6 sticky top-24">
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A] mb-5">Order Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-[#475569]">
                            <span>Subtotal ({{ $this->cartItems->count() }} items)</span>
                            <span>Rs. {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-[#475569]">
                            <span>Shipping</span>
                            <span class="{{ $shipping === 0 ? 'text-green-600 font-semibold' : '' }}">
                                {{ $shipping === 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-[#475569]">
                            <span>Tax (15%)</span>
                            <span>Rs. {{ number_format($tax, 0) }}</span>
                        </div>
                        @if($discountAmount > 0)
                        <div class="flex justify-between text-green-600 font-semibold">
                            <span>Discount</span>
                            <span>-Rs. {{ number_format($discountAmount, 0) }}</span>
                        </div>
                        @endif
                        @if($shipping > 0)
                        <p class="text-[10px] text-[#94A3B8] bg-[#F8FAFC] rounded-lg px-3 py-2">
                            Add Rs. {{ number_format(500 - $subtotal, 0) }} more for free shipping!
                        </p>
                        @endif
                    </div>
                    <div class="border-t border-[#E2E8F0] mt-4 pt-4">
                        <div class="flex justify-between font-bold text-lg">
                            <span class="text-[#0F172A]">Total</span>
                            <span class="text-[#0F172A]">Rs. {{ number_format($total, 0) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('webpage.checkout') }}" class="btn-primary w-full justify-center mt-5 py-3 text-base">
                        Proceed to Checkout
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    {{-- Trust badges --}}
                    <div class="mt-4 flex flex-col gap-2 text-xs text-[#64748B]">
                        @foreach(['Secure SSL Checkout', 'Easy 30-Day Returns', '100% Genuine Products'] as $badge)
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
