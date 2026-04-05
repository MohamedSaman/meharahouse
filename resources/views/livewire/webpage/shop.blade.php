{{-- resources/views/livewire/webpage/shop.blade.php --}}
<div>
    {{-- Page Banner --}}
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <nav class="text-xs text-[#64748B] mb-3 flex items-center gap-2">
                <a href="{{ route('webpage.home') }}" class="hover:text-[#F59E0B]">Home</a>
                <span>/</span>
                <span class="text-[#94A3B8]">Shop</span>
            </nav>
            <h1 class="font-[Poppins] font-bold text-3xl text-white">All Products</h1>
            <p class="text-[#64748B] mt-1 text-sm">Discover our full collection of premium products</p>
        </div>
    </div>

    <div class="container-page py-10">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Sidebar Filters --}}
            <aside class="w-full lg:w-64 shrink-0 space-y-5">
                <div class="card p-5">
                    <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 uppercase tracking-wider">Categories</h3>
                    <ul class="space-y-2">
                        @foreach(['All Products' => '526', 'Electronics' => '120', 'Fashion' => '85', 'Home & Living' => '64', 'Sports' => '47', 'Beauty' => '93', 'Books' => '38'] as $cat => $count)
                        <li>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" @if($cat === 'All Products') checked @endif class="rounded border-[#CBD5E1] w-4 h-4 accent-[#F59E0B]">
                                    <span class="text-sm text-[#475569] group-hover:text-[#0F172A] transition-colors">{{ $cat }}</span>
                                </div>
                                <span class="text-xs text-[#94A3B8] badge badge-navy">{{ $count }}</span>
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card p-5">
                    <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 uppercase tracking-wider">Price Range (ETB)</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <input type="number" placeholder="Min" value="0" class="form-input text-xs">
                            <span class="text-[#94A3B8] text-xs shrink-0">to</span>
                            <input type="number" placeholder="Max" value="10000" class="form-input text-xs">
                        </div>
                        <button class="btn-primary w-full justify-center btn-sm">Apply Filter</button>
                    </div>
                </div>
                <div class="card p-5">
                    <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 uppercase tracking-wider">Rating</h3>
                    <ul class="space-y-2.5">
                        @foreach([5, 4, 3, 2] as $stars)
                        <li>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="rating" class="w-4 h-4 accent-[#F59E0B]">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= $stars ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-[#64748B]">& Up</span>
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            {{-- Products Grid --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <p class="text-sm text-[#64748B]">Showing <strong class="text-[#0F172A]">12</strong> of 526 products</p>
                    <div class="flex items-center gap-3">
                        <select class="form-input text-sm py-2 w-auto">
                            <option>Sort: Featured</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest First</option>
                        </select>
                    </div>
                </div>

                @php
                $products = [
                    ['name' => 'Premium Wireless Headphones', 'price' => '2,499', 'original' => '3,200', 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&auto=format&fit=crop&q=80', 'badge' => 'Sale'],
                    ['name' => 'Leather Weekend Bag', 'price' => '1,850', 'original' => null, 'rating' => 4.9, 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&auto=format&fit=crop&q=80', 'badge' => 'New'],
                    ['name' => 'Smart Watch Pro', 'price' => '4,200', 'original' => '5,500', 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&auto=format&fit=crop&q=80', 'badge' => null],
                    ['name' => 'Natural Skincare Set', 'price' => '890', 'original' => null, 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=400&auto=format&fit=crop&q=80', 'badge' => null],
                    ['name' => 'Running Shoes Pro', 'price' => '3,100', 'original' => '3,800', 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&auto=format&fit=crop&q=80', 'badge' => 'Sale'],
                    ['name' => 'Ergonomic Desk Lamp', 'price' => '650', 'original' => null, 'rating' => 4.5, 'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&auto=format&fit=crop&q=80', 'badge' => null],
                    ['name' => 'Ceramic Coffee Mug Set', 'price' => '480', 'original' => '600', 'rating' => 4.9, 'image' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400&auto=format&fit=crop&q=80', 'badge' => 'Sale'],
                    ['name' => 'Bluetooth Speaker', 'price' => '1,350', 'original' => null, 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&auto=format&fit=crop&q=80', 'badge' => null],
                    ['name' => 'Canvas Backpack', 'price' => '720', 'original' => '900', 'rating' => 4.5, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&auto=format&fit=crop&q=80', 'badge' => 'Sale'],
                    ['name' => 'Yoga Mat Premium', 'price' => '560', 'original' => null, 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&auto=format&fit=crop&q=80', 'badge' => 'New'],
                    ['name' => 'Kitchen Blender Pro', 'price' => '1,890', 'original' => '2,200', 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=400&auto=format&fit=crop&q=80', 'badge' => null],
                    ['name' => 'Sunglasses UV400', 'price' => '380', 'original' => null, 'rating' => 4.4, 'image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&auto=format&fit=crop&q=80', 'badge' => null],
                ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($products as $product)
                    <div class="product-card group" x-data="{ inWishlist: false }">
                        <div class="product-img-wrap h-52 relative">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                            @if($product['badge'])
                            <span class="absolute top-3 left-3 badge {{ $product['badge'] === 'Sale' ? 'badge-danger' : 'badge-info' }}">{{ $product['badge'] }}</span>
                            @endif
                            <div class="product-actions bg-white/90 backdrop-blur-sm rounded-t-xl">
                                <button class="btn-primary btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Add to Cart
                                </button>
                                <button @click="inWishlist = !inWishlist" :class="inWishlist ? 'text-red-500' : 'text-[#475569]'" class="p-2 rounded-lg bg-white border border-[#E2E8F0] hover:border-red-200 transition-all duration-200">
                                    <svg class="w-4 h-4" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <a href="{{ route('webpage.product-details') }}" class="block font-[Poppins] font-semibold text-sm text-[#0F172A] hover:text-[#D97706] transition-colors leading-snug mb-2">{{ $product['name'] }}</a>
                            <div class="flex items-center gap-1.5 mb-3">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= floor($product['rating']) ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-[#64748B]">{{ $product['rating'] }}</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="font-[Poppins] font-bold text-[#0F172A]">ETB {{ $product['price'] }}</span>
                                @if($product['original'])
                                <span class="text-xs text-[#94A3B8] line-through">ETB {{ $product['original'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="flex items-center justify-center gap-2 mt-10">
                    <button class="p-2 rounded-lg border border-[#E2E8F0] text-[#64748B] hover:border-[#F59E0B] hover:text-[#F59E0B] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @foreach([1, 2, 3, '...', 11] as $page)
                    <button class="w-9 h-9 rounded-lg {{ $page === 1 ? 'bg-[#0F172A] text-white font-bold' : 'border border-[#E2E8F0] text-[#475569] hover:border-[#F59E0B] hover:text-[#F59E0B]' }} text-sm transition-colors">{{ $page }}</button>
                    @endforeach
                    <button class="p-2 rounded-lg border border-[#E2E8F0] text-[#64748B] hover:border-[#F59E0B] hover:text-[#F59E0B] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
