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
                        <li>
                            <button wire:click="setCategory('')" class="flex items-center justify-between w-full group">
                                <span class="text-sm {{ $categorySlug === '' ? 'text-[#F59E0B] font-semibold' : 'text-[#475569] group-hover:text-[#0F172A]' }} transition-colors">All Products</span>
                            </button>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <button wire:click="setCategory('{{ $cat->slug }}')" class="flex items-center justify-between w-full group">
                                <span class="text-sm {{ $categorySlug === $cat->slug ? 'text-[#F59E0B] font-semibold' : 'text-[#475569] group-hover:text-[#0F172A]' }} transition-colors">{{ $cat->name }}</span>
                                <span class="text-xs text-[#94A3B8] badge badge-navy">{{ $cat->products_count }}</span>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card p-5">
                    <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-4 uppercase tracking-wider">Price Range (Rs.)</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <input type="number" wire:model.live.debounce.500ms="priceMin" placeholder="Min" class="form-input text-xs">
                            <span class="text-[#94A3B8] text-xs shrink-0">to</span>
                            <input type="number" wire:model.live.debounce.500ms="priceMax" placeholder="Max" class="form-input text-xs">
                        </div>
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
                    <p class="text-sm text-[#64748B]">
                        Showing <strong class="text-[#0F172A]">{{ $products->count() }}</strong>
                        of <strong class="text-[#0F172A]">{{ $products->total() }}</strong> products
                    </p>
                    <div class="flex items-center gap-3">
                        <select wire:model.live="sortBy" class="form-input text-sm py-2 w-auto">
                            <option value="latest">Sort: Newest</option>
                            <option value="featured">Sort: Featured</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="name_asc">Name: A–Z</option>
                        </select>
                    </div>
                </div>

                @if($products->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-16 h-16 text-[#CBD5E1] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[#64748B] font-medium">No products found.</p>
                    <button wire:click="clearFilters" class="mt-3 text-sm text-[#F59E0B] hover:underline">Clear filters</button>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($products as $product)
                    <div wire:key="{{ $product->id }}" class="product-card group" x-data="{ inWishlist: @json(auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists()) }">
                        <div class="product-img-wrap h-52 relative">
                            @if(!empty($product->images))
                            <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-[#F1F5F9] flex items-center justify-center">
                                <svg class="w-14 h-14 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            @if($product->isOnSale())
                            <span class="absolute top-3 left-3 badge badge-danger">Sale</span>
                            @elseif($product->created_at->gt(now()->subDays(14)))
                            <span class="absolute top-3 left-3 badge badge-info">New</span>
                            @endif
                            <div class="product-actions bg-white/90 backdrop-blur-sm rounded-t-xl">
                                <a href="{{ route('webpage.product-details', $product->slug) }}" class="btn-primary btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>
                                <button wire:click="toggleWishlist({{ $product->id }})" @click="inWishlist = !inWishlist" :class="inWishlist ? 'text-red-500' : 'text-[#475569]'" class="p-2 rounded-lg bg-white border border-[#E2E8F0] hover:border-red-200 transition-all duration-200">
                                    <svg class="w-4 h-4" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-[#94A3B8] mb-1">{{ $product->category->name ?? '' }}</p>
                            <a href="{{ route('webpage.product-details', $product->slug) }}" class="block font-[Poppins] font-semibold text-sm text-[#0F172A] hover:text-[#D97706] transition-colors leading-snug mb-2">{{ $product->name }}</a>
                            <div class="flex items-baseline gap-2">
                                <span class="font-[Poppins] font-bold text-[#0F172A]">Rs. {{ number_format($product->effectivePrice()) }}</span>
                                @if($product->isOnSale())
                                <span class="text-xs text-[#94A3B8] line-through">Rs. {{ number_format($product->price) }}</span>
                                <span class="text-xs text-green-600 font-semibold">{{ $product->discountPercent() }}% off</span>
                                @endif
                            </div>

                            {{-- Sizes --}}
                            @if(!empty($product->sizes))
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($product->sizes as $sz)
                                <span class="px-2 py-0.5 rounded border border-slate-300 text-[10px] font-semibold text-slate-600 bg-white">{{ $sz }}</span>
                                @endforeach
                            </div>
                            @endif

                            {{-- Colors --}}
                            @if(!empty($product->colors))
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                @foreach($product->colors as $col)
                                <span class="w-4 h-4 rounded-full border border-slate-300 shrink-0" style="background:{{ $col['hex'] }}" title="{{ $col['name'] }}"></span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
