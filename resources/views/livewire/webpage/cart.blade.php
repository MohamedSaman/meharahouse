{{-- resources/views/livewire/webpage/cart.blade.php --}}
<div>
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-3xl text-white">Shopping Cart</h1>
            <p class="text-[#64748B] mt-1 text-sm">3 items in your cart</p>
        </div>
    </div>

    <section class="py-10 container-page">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @php
                $cartItems = [
                    ['name' => 'Premium Wireless Headphones', 'variant' => 'Black', 'price' => 2499, 'qty' => 1, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Smart Watch Pro', 'variant' => 'Silver', 'price' => 4200, 'qty' => 1, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Natural Skincare Set', 'variant' => 'Standard', 'price' => 890, 'qty' => 2, 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=120&auto=format&fit=crop&q=80'],
                ];
                @endphp

                {{-- Header --}}
                <div class="card overflow-hidden">
                    <div class="px-5 py-3 bg-[#F8FAFC] border-b border-[#E2E8F0] flex items-center justify-between">
                        <span class="text-xs font-bold text-[#64748B] uppercase tracking-wider">Product</span>
                        <div class="hidden sm:flex items-center gap-16 text-xs font-bold text-[#64748B] uppercase tracking-wider">
                            <span>Qty</span>
                            <span>Total</span>
                        </div>
                    </div>

                    @foreach($cartItems as $item)
                    <div x-data="{ qty: {{ $item['qty'] }} }" class="flex items-start gap-4 p-5 border-b border-[#F1F5F9] last:border-0">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-[#F8FAFC] shrink-0">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-[Poppins] font-semibold text-sm text-[#0F172A] mb-0.5">{{ $item['name'] }}</h4>
                            <p class="text-xs text-[#64748B] mb-2">Variant: {{ $item['variant'] }}</p>
                            <p class="text-sm font-bold text-[#0F172A]">ETB {{ number_format($item['price']) }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            {{-- Qty Controls --}}
                            <div class="flex items-center border border-[#E2E8F0] rounded-lg overflow-hidden">
                                <button @click="qty = Math.max(1, qty - 1)" class="w-8 h-8 flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] transition-colors text-lg font-bold">-</button>
                                <span x-text="qty" class="w-8 text-center text-sm font-bold text-[#0F172A]"></span>
                                <button @click="qty++" class="w-8 h-8 flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] transition-colors text-lg font-bold">+</button>
                            </div>
                            {{-- Subtotal --}}
                            <span class="font-[Poppins] font-bold text-sm text-[#0F172A]">ETB <span x-text="({{ $item['price'] }} * qty).toLocaleString()"></span></span>
                            {{-- Remove --}}
                            <button class="text-xs text-red-400 hover:text-red-600 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Remove
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Coupon --}}
                <div class="card p-5">
                    <h3 class="font-[Poppins] font-semibold text-sm text-[#0F172A] mb-3">Have a Coupon Code?</h3>
                    <div class="flex gap-3">
                        <input type="text" placeholder="Enter coupon code" class="form-input flex-1">
                        <button class="btn-secondary shrink-0">Apply</button>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="space-y-4">
                <div class="card p-6">
                    <h3 class="font-[Poppins] font-bold text-[#0F172A] mb-5">Order Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-[#64748B]">Subtotal (3 items)</span>
                            <span class="font-semibold text-[#0F172A]">ETB 8,479</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#64748B]">Shipping</span>
                            <span class="font-semibold text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#64748B]">Discount</span>
                            <span class="font-semibold text-red-500">-ETB 0</span>
                        </div>
                        <div class="border-t border-[#F1F5F9] pt-3 flex justify-between">
                            <span class="font-[Poppins] font-bold text-[#0F172A]">Total</span>
                            <span class="font-[Poppins] font-bold text-lg text-[#0F172A]">ETB 8,479</span>
                        </div>
                    </div>
                    <a href="{{ route('webpage.checkout') }}" class="btn-primary w-full justify-center mt-5 btn-lg">
                        Proceed to Checkout
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('webpage.shop') }}" class="btn-ghost w-full justify-center mt-2">Continue Shopping</a>
                </div>

                <div class="card p-5">
                    <div class="flex items-center gap-2 text-sm text-[#475569] mb-3">
                        <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="font-semibold text-[#0F172A]">Secure Checkout</span>
                    </div>
                    <p class="text-xs text-[#64748B]">Your payment information is processed securely. We do not store credit card details.</p>
                </div>
            </div>
        </div>
    </section>
</div>
