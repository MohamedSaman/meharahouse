{{-- resources/views/livewire/webpage/checkout.blade.php --}}
<div>
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-10">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-2xl text-white">Checkout</h1>
            {{-- Progress Steps --}}
            <div class="flex items-center gap-2 mt-4">
                @foreach(['Shipping' => true, 'Payment' => false, 'Review' => false] as $step => $active)
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full {{ $active ? 'bg-[#F59E0B] text-[#0F172A]' : 'bg-[#1E293B] text-[#64748B]' }} flex items-center justify-center text-xs font-bold">
                        {{ $loop->index + 1 }}
                    </div>
                    <span class="text-sm {{ $active ? 'text-white font-semibold' : 'text-[#64748B]' }}">{{ $step }}</span>
                    @if(!$loop->last)
                    <svg class="w-4 h-4 text-[#334155]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <section class="py-10 container-page">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Shipping Form --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-6">
                    <h2 class="font-[Poppins] font-bold text-[#0F172A] mb-5">Shipping Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-input" placeholder="Selam">
                        </div>
                        <div>
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-input" placeholder="Tadesse">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" placeholder="selam@example.com">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-input" placeholder="+251 911 000 000">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Street Address</label>
                            <input type="text" class="form-input" placeholder="Bole Road, Around...">
                        </div>
                        <div>
                            <label class="form-label">City</label>
                            <select class="form-input">
                                <option>Addis Ababa</option>
                                <option>Hawassa</option>
                                <option>Bahir Dar</option>
                                <option>Mekelle</option>
                                <option>Gondar</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Region</label>
                            <input type="text" class="form-input" placeholder="Addis Ababa City">
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="font-[Poppins] font-bold text-[#0F172A] mb-4">Delivery Method</h2>
                    <div class="space-y-3">
                        @foreach(['Standard Delivery (2-3 days) — Free' => true, 'Express Delivery (Next Day) — ETB 150' => false] as $method => $checked)
                        <label class="flex items-center gap-3 p-4 border-2 {{ $checked ? 'border-[#F59E0B] bg-[#FFFBEB]' : 'border-[#E2E8F0]' }} rounded-xl cursor-pointer hover:border-[#F59E0B] transition-colors">
                            <input type="radio" name="delivery" {{ $checked ? 'checked' : '' }} class="w-4 h-4 accent-[#F59E0B]">
                            <span class="text-sm font-semibold text-[#0F172A]">{{ $method }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button class="btn-primary btn-lg w-full justify-center">
                    Continue to Payment
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>

            {{-- Summary --}}
            <div class="card p-6 self-start sticky top-24">
                <h3 class="font-[Poppins] font-bold text-[#0F172A] mb-4">Order Summary</h3>
                <div class="space-y-3 mb-4">
                    @foreach(['Premium Wireless Headphones' => '2,499', 'Smart Watch Pro' => '4,200', 'Natural Skincare Set (x2)' => '1,780'] as $item => $price)
                    <div class="flex justify-between text-sm">
                        <span class="text-[#64748B] truncate mr-2">{{ $item }}</span>
                        <span class="font-semibold text-[#0F172A] shrink-0">ETB {{ $price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-[#F1F5F9] pt-3 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-[#64748B]">Subtotal</span>
                        <span class="font-semibold">ETB 8,479</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#64748B]">Shipping</span>
                        <span class="font-semibold text-green-600">Free</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span class="text-[#0F172A]">Total</span>
                        <span class="text-[#0F172A] font-[Poppins] text-lg">ETB 8,479</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
