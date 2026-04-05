{{-- resources/views/livewire/webpage/product-details.blade.php --}}
<div>
    <section class="container-page py-10">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-[#64748B] mb-6 flex items-center gap-2">
            <a href="{{ route('webpage.home') }}" class="hover:text-[#F59E0B]">Home</a>
            <span>/</span>
            <a href="{{ route('webpage.shop') }}" class="hover:text-[#F59E0B]">Shop</a>
            @if($product->category)
            <span>/</span>
            <a href="{{ route('webpage.shop', ['category' => $product->category->slug]) }}" class="hover:text-[#F59E0B]">{{ $product->category->name }}</a>
            @endif
            <span>/</span>
            <span class="text-[#0F172A] font-semibold truncate max-w-[200px]">{{ $product->name }}</span>
        </nav>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Image Gallery --}}
            <div x-data="{ active: {{ $activeImage }} }">
                {{-- Main Image --}}
                <div class="aspect-square rounded-2xl overflow-hidden bg-[#F8FAFC] mb-4">
                    @if(!empty($product->images))
                    <img :src="imgs[active]"
                         x-data="{ imgs: {{ json_encode(collect($product->images)->map(fn($img) => asset('storage/' . $img))->values()) }} }"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-all duration-300">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    @endif
                </div>
                {{-- Thumbnails --}}
                @if(!empty($product->images) && count($product->images) > 1)
                <div class="flex gap-3 overflow-x-auto pb-1"
                     x-data="{ imgs: {{ json_encode(collect($product->images)->map(fn($img) => asset('storage/' . $img))->values()) }} }">
                    @foreach($product->images as $index => $img)
                    <button @click="active = {{ $index }}" wire:click="$set('activeImage', {{ $index }})"
                            :class="active === {{ $index }} ? 'ring-2 ring-[#F59E0B]' : 'opacity-60 hover:opacity-100'"
                            class="w-20 h-20 shrink-0 rounded-xl overflow-hidden bg-[#F8FAFC] transition-all duration-200">
                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }} image {{ $index + 1 }}" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div>
                {{-- Badges --}}
                <div class="flex items-center gap-2 mb-3">
                    @if($product->isOnSale())
                    <span class="badge badge-danger">Sale</span>
                    @endif
                    @if($product->is_featured)
                    <span class="badge badge-gold">Featured</span>
                    @endif
                    @if($product->category)
                    <span class="badge badge-navy">{{ $product->category->name }}</span>
                    @endif
                    @if($product->stock <= 0)
                    <span class="badge badge-danger">Out of Stock</span>
                    @elseif($product->stock <= 5)
                    <span class="badge" style="background:#FFF7ED;color:#C2410C;">Only {{ $product->stock }} left</span>
                    @endif
                </div>

                <h1 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-3">{{ $product->name }}</h1>

                {{-- Rating --}}
                @php $avgRating = $product->averageRating(); $reviewCount = $product->reviewCount(); @endphp
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                    <span class="text-sm text-[#64748B]">
                        {{ $avgRating > 0 ? number_format($avgRating, 1) : 'No ratings' }}
                        @if($reviewCount > 0) ({{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }}) @endif
                    </span>
                </div>

                {{-- Price --}}
                <div class="flex items-baseline gap-3 mb-5">
                    <span class="font-[Poppins] font-bold text-3xl text-[#0F172A]">ETB {{ number_format($product->effectivePrice(), 0) }}</span>
                    @if($product->isOnSale())
                    <span class="text-lg text-[#94A3B8] line-through">ETB {{ number_format($product->price, 0) }}</span>
                    <span class="badge badge-danger">{{ $product->discountPercent() }}% OFF</span>
                    @endif
                </div>

                @if($product->description)
                <p class="text-sm text-[#475569] leading-relaxed mb-6">{{ $product->description }}</p>
                @endif

                {{-- SKU --}}
                @if($product->sku)
                <p class="text-xs text-[#94A3B8] mb-4">SKU: <span class="font-mono text-[#64748B]">{{ $product->sku }}</span></p>
                @endif

                {{-- Quantity --}}
                @if($product->stock > 0)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-[#374151] mb-2">Quantity</label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border-2 border-[#E2E8F0] rounded-xl overflow-hidden">
                            <button wire:click="decrementQty"
                                    class="w-10 h-10 flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] text-xl font-bold transition-colors">-</button>
                            <span class="w-10 text-center font-bold text-[#0F172A]">{{ $quantity }}</span>
                            <button wire:click="incrementQty"
                                    class="w-10 h-10 flex items-center justify-center text-[#64748B] hover:bg-[#F1F5F9] text-xl font-bold transition-colors">+</button>
                        </div>
                        <span class="text-xs text-[#64748B]">{{ $product->stock }} units in stock</span>
                    </div>
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-3 mb-6">
                    @if($product->stock > 0)
                    <button wire:click="addToCart" wire:loading.attr="disabled"
                            class="btn-primary flex-1 justify-center btn-lg">
                        <svg wire:loading wire:target="addToCart" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                        <span wire:loading wire:target="addToCart">Adding...</span>
                    </button>
                    @else
                    <button disabled class="btn-secondary flex-1 justify-center btn-lg opacity-60 cursor-not-allowed">
                        Out of Stock
                    </button>
                    @endif

                    <button wire:click="toggleWishlist"
                            class="{{ $this->isInWishlist() ? 'text-red-500 border-red-200 bg-red-50' : 'text-[#475569]' }} p-4 rounded-xl border-2 border-[#E2E8F0] hover:border-red-200 transition-all duration-200">
                        <svg class="w-5 h-5" fill="{{ $this->isInWishlist() ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>

                {{-- Trust Badges --}}
                <div class="flex flex-wrap gap-4 pt-5 border-t border-[#F1F5F9]">
                    @foreach(['Free Delivery on orders above ETB 500', '30-Day Easy Returns', '100% Authentic Product'] as $t)
                    <div class="flex items-center gap-2 text-xs text-[#64748B]">
                        <svg class="w-4 h-4 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $t }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="mt-16 grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Reviews List --}}
            <div class="lg:col-span-2">
                <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-6">Customer Reviews</h2>

                @if($product->reviews->isEmpty())
                <div class="text-center py-10 bg-[#F8FAFC] rounded-2xl">
                    <p class="text-[#64748B]">No reviews yet. Be the first to review this product!</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($product->reviews as $review)
                    <div class="card p-5">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#0F172A] flex items-center justify-center">
                                    <span class="text-[#F59E0B] text-xs font-bold">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $review->user->name ?? 'Anonymous' }}</p>
                                    <p class="text-xs text-[#94A3B8]">{{ $review->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                        <p class="text-sm text-[#475569] leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Write Review --}}
                @auth
                <div class="mt-6">
                    <button wire:click="$toggle('showReviewForm')" class="btn-secondary btn-sm">
                        {{ $showReviewForm ? 'Cancel' : 'Write a Review' }}
                    </button>
                    @if($showReviewForm)
                    <div class="card p-5 mt-4">
                        <h4 class="font-semibold text-[#0F172A] mb-4">Your Review</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-[#374151] mb-2">Rating</label>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <button wire:click="$set('reviewRating', {{ $i }})"
                                        class="text-2xl transition-transform hover:scale-110">
                                    <svg class="w-7 h-7 {{ $i <= $reviewRating ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                                @endfor
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-[#374151] mb-1.5">Comment (optional)</label>
                            <textarea wire:model="reviewComment" rows="3"
                                      class="form-input resize-none"
                                      placeholder="Share your experience with this product..."></textarea>
                        </div>
                        <button wire:click="submitReview" wire:loading.attr="disabled" class="btn-primary btn-sm">
                            Submit Review
                        </button>
                    </div>
                    @endif
                </div>
                @else
                <p class="mt-6 text-sm text-[#64748B]">
                    <a href="{{ route('auth.login') }}" class="text-[#F59E0B] font-semibold hover:underline">Sign in</a> to leave a review.
                </p>
                @endauth
            </div>

            {{-- Related Products --}}
            @if($relatedProducts->isNotEmpty())
            <div>
                <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A] mb-4">You May Also Like</h3>
                <div class="space-y-4">
                    @foreach($relatedProducts as $related)
                    <a href="{{ route('webpage.product-details', $related->slug) }}"
                       class="flex items-center gap-3 card p-3 hover:shadow-md transition-shadow duration-200">
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-[#F1F5F9] shrink-0">
                            <img src="{{ $related->primaryImage() }}" alt="{{ $related->name }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#0F172A] leading-snug truncate">{{ $related->name }}</p>
                            <p class="text-xs text-[#94A3B8] mt-0.5">{{ $related->category?->name }}</p>
                            <p class="text-sm font-bold text-[#F59E0B] mt-1">ETB {{ number_format($related->effectivePrice(), 0) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </section>
</div>
