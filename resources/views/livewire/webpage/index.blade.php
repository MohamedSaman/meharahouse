{{-- resources/views/livewire/webpage/index.blade.php --}}
<div>

    {{-- ══════════════════════════════════════════════════════
         HERO SECTION
    ══════════════════════════════════════════════════════ --}}
    <section
        x-data="{
            currentSlide: 0,
            slides: [
                {
                    tag: 'New Arrivals 2024',
                    title: 'Discover Premium\nQuality Products',
                    subtitle: 'Ethiopia\'s most trusted online store — handpicked products, unbeatable prices, delivered to your door.',
                    cta: 'Shop Now',
                    ctaLink: '{{ route("webpage.shop") }}',
                    bg: 'from-[#0F172A] to-[#1E293B]',
                    accent: 'bg-[#F59E0B]',
                    image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=700&auto=format&fit=crop&q=80'
                },
                {
                    tag: 'Exclusive Deals',
                    title: 'Up to 40% Off\nSelected Items',
                    subtitle: 'Limited-time offers on top-selling products. Don\'t miss out on our seasonal sale.',
                    cta: 'View Offers',
                    ctaLink: '{{ route("webpage.shop") }}',
                    bg: 'from-[#78350F] to-[#92400E]',
                    accent: 'bg-white',
                    image: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=700&auto=format&fit=crop&q=80'
                },
                {
                    tag: 'Fast Delivery',
                    title: 'Nationwide Delivery\nAcross Ethiopia',
                    subtitle: 'Order today, receive within 2-3 business days. Free shipping on orders above ETB 500.',
                    cta: 'Start Shopping',
                    ctaLink: '{{ route("webpage.shop") }}',
                    bg: 'from-[#134e4a] to-[#0f3d3a]',
                    accent: 'bg-[#F59E0B]',
                    image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&auto=format&fit=crop&q=80'
                }
            ],
            autoPlay: null,
            startAutoPlay() {
                this.autoPlay = setInterval(() => {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                }, 5000);
            },
            stopAutoPlay() { clearInterval(this.autoPlay); }
        }"
        x-init="startAutoPlay()"
        class="relative overflow-hidden"
        style="min-height: 580px;"
    >
        {{-- Slides --}}
        <template x-for="(slide, index) in slides" :key="index">
            <div
                x-show="currentSlide === index"
                x-transition:enter="transition-all duration-700 ease-out"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-500 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="'bg-gradient-to-br ' + slide.bg"
                class="absolute inset-0"
            >
                <div class="container-page h-full flex items-center" style="min-height:580px;">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center w-full py-16 lg:py-0">
                        {{-- Text Content --}}
                        <div class="text-white">
                            <div class="flex items-center gap-2 mb-5">
                                <span :class="slide.accent" class="w-8 h-0.5 rounded-full"></span>
                                <span x-text="slide.tag" class="text-xs font-bold tracking-widest uppercase text-[#F59E0B]"></span>
                            </div>
                            <h1 x-html="slide.title.replace(/\n/g, '<br>')"
                                class="font-[Poppins] font-extrabold text-4xl md:text-5xl xl:text-6xl leading-tight text-white mb-5">
                            </h1>
                            <p x-text="slide.subtitle" class="text-white/70 text-base md:text-lg leading-relaxed mb-8 max-w-lg"></p>
                            <div class="flex flex-wrap gap-3">
                                <a :href="slide.ctaLink" class="btn-primary btn-lg">
                                    <span x-text="slide.cta"></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                                <a href="{{ route('webpage.about') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-lg border-2 border-white/25 text-white text-sm font-semibold hover:border-white/60 transition-all duration-200">
                                    Learn More
                                </a>
                            </div>
                            {{-- Trust Badges --}}
                            <div class="flex flex-wrap items-center gap-5 mt-8 pt-8 border-t border-white/10">
                                @foreach(['100% Genuine', 'Secure Payment', 'Easy Returns'] as $trust)
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-[#F59E0B] flex items-center justify-center">
                                        <svg class="w-3 h-3 text-[#0F172A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-white/70 font-medium">{{ $trust }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Hero Image --}}
                        <div class="hidden lg:block relative">
                            <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl">
                                <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            </div>
                            {{-- Floating Badge --}}
                            <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-xl px-4 py-3 flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#FFFBEB] rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-[#64748B]">Products In Stock</p>
                                    <p class="text-sm font-bold text-[#0F172A]">500+ Items</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Slider Controls --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 z-10">
            <template x-for="(slide, index) in slides" :key="index">
                <button
                    @click="currentSlide = index; stopAutoPlay(); startAutoPlay();"
                    :class="currentSlide === index ? 'w-8 bg-[#F59E0B]' : 'w-2.5 bg-white/40 hover:bg-white/60'"
                    class="h-2.5 rounded-full transition-all duration-300"
                ></button>
            </template>
        </div>

        {{-- Arrow Controls --}}
        <button @click="currentSlide = (currentSlide - 1 + slides.length) % slides.length; stopAutoPlay(); startAutoPlay();"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="currentSlide = (currentSlide + 1) % slides.length; stopAutoPlay(); startAutoPlay();"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </section>

    {{-- ══════════════════════════════════════════════════════
         QUICK STATS BAR
    ══════════════════════════════════════════════════════ --}}
    <section class="bg-white border-y border-[#E2E8F0] py-5">
        <div class="container-page">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-[#E2E8F0]">
                @php
                $stats = [
                    ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'value' => '500+', 'label' => 'Products Available'],
                    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'value' => '12K+', 'label' => 'Happy Customers'],
                    ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'value' => 'ETB 500', 'label' => 'Free Shipping Above'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'value' => '100%', 'label' => 'Authentic Products'],
                ];
                @endphp
                @foreach($stats as $stat)
                <div class="flex items-center gap-3 px-5 py-2 first:pl-0 last:pr-0">
                    <div class="w-9 h-9 bg-[#FFFBEB] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#0F172A] font-[Poppins]">{{ $stat['value'] }}</p>
                        <p class="text-xs text-[#64748B] leading-tight">{{ $stat['label'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════
         CATEGORIES SECTION
    ══════════════════════════════════════════════════════ --}}
    <section class="py-16">
        <div class="container-page">
            {{-- Section Header --}}
            <div class="text-center mb-10">
                <span class="section-label">Browse By</span>
                <h2 class="section-title">Shop by Category</h2>
                <div class="gold-divider mx-auto"></div>
                <p class="section-subtitle max-w-lg mx-auto">Explore our wide range of product categories curated for every need.</p>
            </div>

            {{-- Category Grid — real data from DB --}}
            @php
            $categoryColors = [
                'from-blue-500 to-blue-700',
                'from-pink-500 to-rose-600',
                'from-amber-500 to-orange-600',
                'from-green-500 to-emerald-700',
                'from-purple-500 to-violet-700',
                'from-teal-500 to-cyan-700',
                'from-red-500 to-red-700',
                'from-indigo-500 to-indigo-700',
                'from-yellow-500 to-yellow-700',
                'from-lime-500 to-lime-700',
            ];
            $categoryBgs = ['#EFF6FF','#FFF1F2','#FFFBEB','#F0FDF4','#F5F3FF','#F0FDFA','#FEF2F2','#EEF2FF','#FEFCE8','#F7FEE7'];
            $categoryIcons = [
                'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'M13 10V3L4 14h7v7l9-11h-7z',
                'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
            ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @forelse($categories as $i => $cat)
                @php
                    $color = $categoryColors[$i % count($categoryColors)];
                    $bg    = $categoryBgs[$i % count($categoryBgs)];
                    $icon  = $categoryIcons[$i % count($categoryIcons)];
                @endphp
                <a href="{{ route('webpage.shop', ['category' => $cat->slug]) }}"
                   class="group flex flex-col items-center gap-3 p-5 rounded-2xl border border-[#E2E8F0] hover:border-[#F59E0B] hover:shadow-lg transition-all duration-300 text-center"
                   style="background: {{ $bg }};">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $color }} flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-semibold text-sm text-[#0F172A] group-hover:text-[#D97706] transition-colors duration-200">{{ $cat->name }}</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">{{ $cat->products_count }} Products</p>
                    </div>
                </a>
                @empty
                <p class="col-span-5 text-center text-[#64748B] text-sm py-8">No categories yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════
         FEATURED PRODUCTS
    ══════════════════════════════════════════════════════ --}}
    <section class="py-16 bg-white">
        <div class="container-page">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="section-label">Hand-Picked</span>
                    <h2 class="section-title">Featured Products</h2>
                    <div class="gold-divider"></div>
                </div>
                <a href="{{ route('webpage.shop') }}" class="btn-secondary btn-sm shrink-0 self-end sm:self-auto">
                    View All Products
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Tab Filter --}}
            <div x-data="{ activeTab: 'all' }" class="mb-8">
                <div class="flex flex-wrap gap-2">
                    @foreach(['all' => 'All Products', 'trending' => 'Trending', 'new' => 'New Arrivals', 'sale' => 'On Sale'] as $key => $label)
                    <button
                        @click="activeTab = '{{ $key }}'"
                        :class="activeTab === '{{ $key }}' ? 'bg-[#0F172A] text-white' : 'bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0]'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Products Grid — real data from DB --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse($featuredProducts as $product)
                <div wire:key="featured-{{ $product->id }}" class="product-card group" x-data="{ inWishlist: false }">
                    {{-- Image --}}
                    <div class="product-img-wrap h-56 relative">
                        <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=400&auto=format&fit=crop&q=80'">

                        {{-- Badges --}}
                        @if($product->isOnSale())
                        <span class="absolute top-3 left-3 badge badge-danger z-10">-{{ $product->discountPercent() }}%</span>
                        @elseif($product->created_at->isAfter(now()->subDays(14)))
                        <span class="absolute top-3 left-3 badge badge-info z-10">New</span>
                        @elseif($product->is_featured)
                        <span class="absolute top-3 left-3 badge badge-gold z-10">Featured</span>
                        @endif

                        {{-- Quick Actions --}}
                        <div class="product-actions bg-white/90 backdrop-blur-sm rounded-t-xl">
                            <a href="{{ route('webpage.product-details', $product->slug) }}" class="btn-primary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Product
                            </a>
                            <button @click="inWishlist = !inWishlist"
                                    :class="inWishlist ? 'text-red-500' : 'text-[#475569]'"
                                    class="p-2 rounded-lg bg-white border border-[#E2E8F0] hover:border-red-200 transition-all duration-200">
                                <svg class="w-4 h-4" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <p class="text-[10px] font-semibold text-[#F59E0B] uppercase tracking-wider mb-1">{{ $product->category?->name ?? '' }}</p>
                        <a href="{{ route('webpage.product-details', $product->slug) }}"
                           class="block font-[Poppins] font-semibold text-sm text-[#0F172A] hover:text-[#D97706] transition-colors duration-200 leading-snug mb-2">
                            {{ $product->name }}
                        </a>

                        {{-- Rating --}}
                        @php $rating = $product->averageRating(); $reviewCount = $product->reviewCount(); @endphp
                        <div class="flex items-center gap-1.5 mb-3">
                            <div class="flex">
                                @for($star = 1; $star <= 5; $star++)
                                <svg class="w-3.5 h-3.5 {{ $star <= floor($rating) ? 'text-[#F59E0B]' : 'text-[#CBD5E1]' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <span class="text-xs text-[#64748B]">{{ $rating > 0 ? number_format($rating, 1) : 'No ratings' }} @if($reviewCount > 0)({{ $reviewCount }})@endif</span>
                        </div>

                        {{-- Price --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-baseline gap-2">
                                <span class="font-[Poppins] font-bold text-base text-[#0F172A]">ETB {{ number_format($product->effectivePrice(), 0) }}</span>
                                @if($product->isOnSale())
                                <span class="text-xs text-[#94A3B8] line-through">ETB {{ number_format($product->price, 0) }}</span>
                                @endif
                            </div>
                            @if($product->stock <= 5 && $product->stock > 0)
                            <span class="text-[10px] font-semibold text-orange-500">Only {{ $product->stock }} left</span>
                            @elseif($product->stock === 0)
                            <span class="text-[10px] font-semibold text-red-500">Out of stock</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-4 text-center py-12">
                    <p class="text-[#64748B]">No featured products yet. <a href="{{ route('webpage.shop') }}" class="text-[#F59E0B] font-semibold">Browse all products.</a></p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════
         PROMOTIONAL BANNER
    ══════════════════════════════════════════════════════ --}}
    <section class="py-12">
        <div class="container-page">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Banner 1 --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0F172A] to-[#1E293B] p-8 flex items-center gap-6">
                    <div class="relative z-10">
                        <span class="text-[#F59E0B] text-xs font-bold uppercase tracking-widest">Limited Time</span>
                        <h3 class="font-[Poppins] font-bold text-2xl text-white mt-1 mb-2">Weekend Flash Sale<br>Up to 30% Off</h3>
                        <a href="{{ route('webpage.shop') }}" class="btn-primary btn-sm mt-2">Shop Sale</a>
                    </div>
                    <div class="absolute right-0 top-0 bottom-0 w-40 opacity-10">
                        <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
                        </svg>
                    </div>
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-[#F59E0B]/10 rounded-full"></div>
                </div>

                {{-- Banner 2 --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#134e4a] to-[#065f46] p-8 flex items-center gap-6">
                    <div class="relative z-10">
                        <span class="text-[#6ee7b7] text-xs font-bold uppercase tracking-widest">Free Shipping</span>
                        <h3 class="font-[Poppins] font-bold text-2xl text-white mt-1 mb-2">Orders Above<br>ETB 500 Only</h3>
                        <a href="{{ route('webpage.shop') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#6ee7b7] text-[#064e3b] text-sm font-bold rounded-lg hover:bg-[#34d399] transition-colors duration-200">
                            Start Shopping
                        </a>
                    </div>
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-white/5 rounded-full"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════
         TESTIMONIALS
    ══════════════════════════════════════════════════════ --}}
    <section class="py-16 bg-[#F8FAFC]">
        <div class="container-page">
            <div class="text-center mb-10">
                <span class="section-label">What They Say</span>
                <h2 class="section-title">Customer Reviews</h2>
                <div class="gold-divider mx-auto"></div>
            </div>

            @php
            $testimonials = [
                ['name' => 'Selam Tadesse', 'city' => 'Addis Ababa', 'text' => 'Amazing quality products! The delivery was fast and the packaging was excellent. Definitely my go-to online store now.', 'rating' => 5, 'initial' => 'S'],
                ['name' => 'Yonas Bekele', 'city' => 'Hawassa', 'text' => 'I was skeptical at first, but Meharahouse exceeded all my expectations. Great customer service and genuine products.', 'rating' => 5, 'initial' => 'Y'],
                ['name' => 'Hana Girma', 'city' => 'Bahir Dar', 'text' => 'The return process was seamless when I needed to exchange a size. Very professional and customer-focused team.', 'rating' => 4, 'initial' => 'H'],
            ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $t)
                <div class="card p-6 relative">
                    {{-- Quote Icon --}}
                    <div class="absolute top-5 right-5 text-[#F59E0B]/20">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>

                    {{-- Stars --}}
                    <div class="flex mb-3">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $t['rating'] ? 'text-[#F59E0B]' : 'text-[#E2E8F0]' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>

                    <p class="text-sm text-[#475569] leading-relaxed mb-5">{{ $t['text'] }}</p>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0F172A] to-[#334155] flex items-center justify-center">
                            <span class="text-[#F59E0B] font-bold text-sm">{{ $t['initial'] }}</span>
                        </div>
                        <div>
                            <p class="font-[Poppins] font-semibold text-sm text-[#0F172A]">{{ $t['name'] }}</p>
                            <p class="text-xs text-[#64748B]">{{ $t['city'] }}, Ethiopia</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════
         TRUST BADGES
    ══════════════════════════════════════════════════════ --}}
    <section class="py-12 bg-white border-t border-[#E2E8F0]">
        <div class="container-page">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8">
                @php
                $trust = [
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Secure Shopping', 'desc' => 'SSL encrypted payments'],
                    ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'title' => 'Fast Delivery', 'desc' => 'Nationwide in 2-3 days'],
                    ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'title' => 'Easy Returns', 'desc' => '30-day hassle-free returns'],
                    ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'title' => '24/7 Support', 'desc' => 'Always here to help'],
                ];
                @endphp
                @foreach($trust as $item)
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-[#0F172A] flex items-center justify-center mb-4 shadow-md">
                        <svg class="w-7 h-7 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                        </svg>
                    </div>
                    <h4 class="font-[Poppins] font-bold text-sm text-[#0F172A] mb-1">{{ $item['title'] }}</h4>
                    <p class="text-xs text-[#64748B]">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
