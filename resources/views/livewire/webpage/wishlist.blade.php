{{-- resources/views/livewire/webpage/wishlist.blade.php --}}
<div>
    {{-- Page Hero --}}
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <div class="flex items-center gap-3">
                <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <div>
                    <h1 class="font-[Poppins] font-bold text-3xl text-white">My Wishlist</h1>
                    <p class="text-[#64748B] mt-0.5 text-sm">Products you love, saved for later</p>
                </div>
            </div>
        </div>
    </div>

    <section class="py-10 container-page max-w-5xl">

        {{-- Flash message --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @guest
        {{-- Not logged in --}}
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-[#F1F5F9] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-2">Sign in to view your wishlist</h3>
            <p class="text-[#64748B] mb-6">Please log in to see the products you have saved.</p>
            <a href="{{ route('auth.login') }}" class="btn-primary btn-lg">Sign In</a>
        </div>
        @else

        @if($wishlistItems->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-[#FFF9EE] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-[#D4A017]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-2">Your wishlist is empty</h3>
            <p class="text-[#64748B] mb-6">Browse our collection and tap the heart icon to save products you love.</p>
            <a href="{{ route('webpage.shop') }}" class="btn-primary btn-lg">Browse Shop</a>
        </div>
        @else

        {{-- Wishlist count --}}
        <p class="text-sm text-[#64748B] mb-5">{{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }} saved</p>

        {{-- Product Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
            @foreach($wishlistItems as $item)
            @php $product = $item->product; @endphp
            @if(!$product) @continue @endif

            <div class="group bg-white rounded-2xl border border-[#F0EDE8] overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                 wire:key="wish-{{ $item->id }}">

                {{-- Product Image --}}
                <a href="{{ route('webpage.product-details', $product->slug) }}" class="block relative overflow-hidden aspect-square bg-[#FDF8F0]">
                    @if($product->primaryImage())
                    <img src="{{ $product->primaryImage() }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         onerror="this.style.display='none'">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-[#D4A017]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif

                    {{-- Category badge --}}
                    @if($product->category)
                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-[#0F172A]/70 backdrop-blur-sm text-white text-[10px] font-semibold rounded-full">
                        {{ $product->category->name }}
                    </span>
                    @endif
                </a>

                {{-- Product Info --}}
                <div class="p-3">
                    <a href="{{ route('webpage.product-details', $product->slug) }}"
                       class="block text-sm font-semibold text-[#0F172A] hover:text-[#D4A017] transition-colors leading-tight mb-1.5 line-clamp-2">
                        {{ $product->name }}
                    </a>

                    <p class="text-base font-bold text-[#D4A017] mb-3">
                        Rs. {{ number_format($product->effectivePrice(), 0) }}
                    </p>

                    <div class="flex flex-col gap-2">
                        {{-- Add to Cart --}}
                        <a href="{{ route('webpage.product-details', $product->slug) }}"
                           class="flex items-center justify-center gap-1.5 w-full py-2 rounded-xl bg-[#0F172A] text-white text-xs font-bold hover:bg-[#1E293B] transition-colors duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Add to Cart
                        </a>

                        {{-- Remove --}}
                        <button wire:click="removeFromWishlist({{ $item->id }})"
                                wire:confirm="Remove this item from your wishlist?"
                                class="flex items-center justify-center gap-1.5 w-full py-1.5 rounded-xl border border-red-200 text-red-500 text-xs font-semibold hover:bg-red-50 hover:border-red-300 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Bottom CTA --}}
        <div class="mt-10 pt-8 border-t border-[#F0EDE8] text-center">
            <a href="{{ route('webpage.shop') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Continue Shopping
            </a>
        </div>

        @endif
        @endguest
    </section>
</div>
