{{-- resources/views/livewire/webpage/product-details.blade.php --}}
<div>
    <section class="container-page py-10">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-[#64748B] mb-6 flex items-center gap-2">
            <a href="{{ route('webpage.home') }}" class="hover:text-[#F59E0B]">Home</a>
            <span>/</span>
            <a href="{{ route('webpage.shop') }}" class="hover:text-[#F59E0B]">Shop</a>
            <span>/</span>
            <span class="text-[#0F172A] font-semibold">Premium Wireless Headphones</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Image Gallery --}}
            <div x-data="{ active: 0, imgs: [
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=700&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=700&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1545127398-14699f92334b?w=700&auto=format&fit=crop&q=80',
            ] }">
                <div class="aspect-square rounded-2xl overflow-hidden bg-[#F8FAFC] mb-4">
                    <img :src="imgs[active]" alt="Product" class="w-full h-full object-cover transition-all duration-300">
                </div>
                <div class="flex gap-3">
                    <template x-for="(img, i) in imgs" :key="i">
                        <button @click="active = i" :class="active === i ? 'ring-2 ring-[#F59E0B]' : 'opacity-60 hover:opacity-100'" class="w-20 h-20 rounded-xl overflow-hidden bg-[#F8FAFC] transition-all duration-200">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Product Info --}}
            <div x-data="{ qty: 1, inWishlist: false }">
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge badge-danger">Sale</span>
                    <span class="badge badge-navy">Electronics</span>
                </div>
                <h1 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-3">Premium Wireless Headphones</h1>

                {{-- Rating --}}
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= 4 ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-sm text-[#64748B]">4.8 (124 reviews)</span>
                </div>

                {{-- Price --}}
                <div class="flex items-baseline gap-3 mb-5">
                    <span class="font-[Poppins] font-bold text-3xl text-[#0F172A]">ETB 2,499</span>
                    <span class="text-lg text-[#94A3B8] line-through">ETB 3,200</span>
                    <span class="badge badge-danger">22% OFF</span>
                </div>

                <p class="text-sm text-[#475569] leading-relaxed mb-6">Experience unparalleled audio quality with our Premium Wireless Headphones. Featuring active noise cancellation, 30-hour battery life, and premium sound engineering — crafted for music lovers and professionals alike.</p>

                {{-- Color --}}
                <div class="mb-5">
                    <label class="form-label">Color</label>
                    <div class="flex gap-2 mt-2">
                        @foreach(['#0F172A' => 'Black', '#F8FAFC' => 'White', '#F59E0B' => 'Gold'] as $color => $name)
                        <button class="w-8 h-8 rounded-full border-2 border-white ring-2 ring-transparent hover:ring-[#F59E0B] transition-all duration-200" style="background:{{ $color }};" title="{{ $name }}"></button>
                        @endforeach
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="mb-6">
                    <label class="form-label">Quantity</label>
                    <div class="flex items-center gap-4 mt-2">
                        <div class="flex items-center border-2 border-[#E2E8F0] rounded-xl overflow-hidden">
                            <button @click="qty = Math.max(1, qty - 1)" class="w-10 h-10 flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] text-xl font-bold transition-colors">-</button>
                            <span x-text="qty" class="w-10 text-center font-bold text-[#0F172A]"></span>
                            <button @click="qty++" class="w-10 h-10 flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] text-xl font-bold transition-colors">+</button>
                        </div>
                        <span class="text-xs text-[#64748B]">32 units in stock</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 mb-6">
                    <button class="btn-primary flex-1 justify-center btn-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Add to Cart
                    </button>
                    <button @click="inWishlist = !inWishlist" :class="inWishlist ? 'text-red-500 border-red-200 bg-red-50' : 'text-[#475569]'" class="p-4 rounded-xl border-2 border-[#E2E8F0] hover:border-red-200 transition-all duration-200">
                        <svg class="w-5 h-5" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                </div>

                {{-- Trust --}}
                <div class="flex flex-wrap gap-4 pt-5 border-t border-[#F1F5F9]">
                    @foreach(['Free Delivery on this order', '30-Day Easy Returns', '2 Year Warranty'] as $t)
                    <div class="flex items-center gap-2 text-xs text-[#64748B]">
                        <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $t }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
