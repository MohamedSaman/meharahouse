{{-- resources/views/livewire/webpage/index.blade.php --}}
<div>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 1 — HERO CAROUSEL SLIDER (Alpine.js, no external libs)
    ══════════════════════════════════════════════════════════════════ --}}
    <section
        class="relative w-full overflow-hidden"
        style="height: 100vh; min-height: 560px;"
        x-data="{
            current: 0,
            total: 3,
            timer: null,
            init() { this.timer = setInterval(() => this.next(), 5000) },
            next() { this.current = (this.current + 1) % this.total },
            prev() { this.current = (this.current - 1 + this.total) % this.total },
            go(i) { this.current = i; clearInterval(this.timer); this.timer = setInterval(() => this.next(), 5000) }
        }"
    >

        {{-- ── Slide 1 ── --}}
        <div
            class="absolute inset-0 w-full h-full"
            x-show="current === 0"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-700"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="background-image: url('https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;"
        >
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-6 md:px-12 w-full">
                    <div class="max-w-2xl">
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase mb-5" style="background-color: #D4A017; color: #0F172A;">
                            New Collection 2025
                        </span>
                        <h1 class="font-[Poppins] font-extrabold text-white leading-[1.1] mb-5" style="font-size: clamp(2.5rem, 6vw, 4.5rem);">
                            Elegance in<br>Every Thread
                        </h1>
                        <p class="text-white/80 text-lg md:text-xl mb-8 max-w-xl font-[Inter]">
                            Discover premium abayas crafted for the modern modest woman
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-semibold text-sm tracking-wide transition-all duration-300 hover:scale-105 hover:shadow-lg"
                               style="background-color: #D4A017; color: #0F172A;">
                                Shop Collection
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-semibold text-sm tracking-wide border-2 border-white/60 text-white hover:border-white hover:bg-white/10 transition-all duration-300">
                                New Arrivals
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Slide 2 ── --}}
        <div
            class="absolute inset-0 w-full h-full"
            x-show="current === 1"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-700"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="background-image: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;"
        >
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-6 md:px-12 w-full">
                    <div class="max-w-2xl">
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase mb-5" style="background-color: #D4A017; color: #0F172A;">
                            Modest Essentials
                        </span>
                        <h1 class="font-[Poppins] font-extrabold text-white leading-[1.1] mb-5" style="font-size: clamp(2.5rem, 6vw, 4.5rem);">
                            Comfort Meets<br>Modern Style
                        </h1>
                        <p class="text-white/80 text-lg md:text-xl mb-8 max-w-xl font-[Inter]">
                            Our innerwear collection — soft, breathable fabrics for everyday confidence
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-semibold text-sm tracking-wide transition-all duration-300 hover:scale-105 hover:shadow-lg"
                               style="background-color: #D4A017; color: #0F172A;">
                                Shop Innerwear
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Slide 3 ── --}}
        <div
            class="absolute inset-0 w-full h-full"
            x-show="current === 2"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-700"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="background-image: url('https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;"
        >
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-6 md:px-12 w-full">
                    <div class="max-w-2xl">
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase mb-5" style="background-color: #D4A017; color: #0F172A;">
                            Premium Quality
                        </span>
                        <h1 class="font-[Poppins] font-extrabold text-white leading-[1.1] mb-5" style="font-size: clamp(2.5rem, 6vw, 4.5rem);">
                            Your Style,<br>Your Story
                        </h1>
                        <p class="text-white/80 text-lg md:text-xl mb-8 max-w-xl font-[Inter]">
                            Handpicked abayas from the finest fabric houses — delivered to your door
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full font-semibold text-sm tracking-wide transition-all duration-300 hover:scale-105 hover:shadow-lg"
                               style="background-color: #D4A017; color: #0F172A;">
                                Explore Now
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Prev / Next Arrows ── --}}
        <button
            @click="prev()"
            class="absolute left-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full flex items-center justify-center border border-white/40 bg-black/30 text-white hover:bg-black/60 transition-all duration-200"
            aria-label="Previous slide"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button
            @click="next()"
            class="absolute right-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full flex items-center justify-center border border-white/40 bg-black/30 text-white hover:bg-black/60 transition-all duration-200"
            aria-label="Next slide"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- ── Dot Indicators ── --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2.5">
            <button @click="go(0)" class="w-2.5 h-2.5 rounded-full border-2 border-white transition-all duration-300"
                    :class="current === 0 ? 'scale-125' : 'opacity-60'"
                    :style="current === 0 ? 'background-color: #D4A017; border-color: #D4A017;' : 'background-color: transparent;'"
                    aria-label="Go to slide 1"></button>
            <button @click="go(1)" class="w-2.5 h-2.5 rounded-full border-2 border-white transition-all duration-300"
                    :class="current === 1 ? 'scale-125' : 'opacity-60'"
                    :style="current === 1 ? 'background-color: #D4A017; border-color: #D4A017;' : 'background-color: transparent;'"
                    aria-label="Go to slide 2"></button>
            <button @click="go(2)" class="w-2.5 h-2.5 rounded-full border-2 border-white transition-all duration-300"
                    :class="current === 2 ? 'scale-125' : 'opacity-60'"
                    :style="current === 2 ? 'background-color: #D4A017; border-color: #D4A017;' : 'background-color: transparent;'"
                    aria-label="Go to slide 3"></button>
        </div>

        {{-- ── Scroll Indicator ── --}}
        <div class="absolute bottom-8 right-8 z-20 hidden md:flex flex-col items-center gap-2 text-white/60">
            <span class="text-xs tracking-widest uppercase font-[Inter]" style="writing-mode: vertical-lr; transform: rotate(180deg);">Scroll</span>
            <div class="w-px h-12 bg-white/30"></div>
        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 2 — TRUST STRIP
    ══════════════════════════════════════════════════════════════════ --}}
    <section style="background-color: #FDF8F0;" class="border-y border-amber-100 py-6">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-5 gap-x-4">

                {{-- Free Delivery --}}
                <div class="flex items-center gap-3 md:justify-center">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: #D4A017; opacity: 0.9;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-[Poppins] font-bold text-sm" style="color: #0F172A;">Free Delivery</p>
                        <p class="text-xs font-[Inter]" style="color: #64748B;">On orders over Rs. 500</p>
                    </div>
                </div>

                {{-- Customers --}}
                <div class="flex items-center gap-3 md:justify-center">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: #D4A017; opacity: 0.9;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-[Poppins] font-bold text-sm" style="color: #0F172A;">12,000+</p>
                        <p class="text-xs font-[Inter]" style="color: #64748B;">Happy Customers</p>
                    </div>
                </div>

                {{-- Authentic --}}
                <div class="flex items-center gap-3 md:justify-center">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: #D4A017; opacity: 0.9;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-[Poppins] font-bold text-sm" style="color: #0F172A;">100% Authentic</p>
                        <p class="text-xs font-[Inter]" style="color: #64748B;">Premium quality fabrics</p>
                    </div>
                </div>

                {{-- Easy Returns --}}
                <div class="flex items-center gap-3 md:justify-center">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: #D4A017; opacity: 0.9;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-[Poppins] font-bold text-sm" style="color: #0F172A;">Easy Returns</p>
                        <p class="text-xs font-[Inter]" style="color: #64748B;">30-day hassle-free policy</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 3 — CATEGORIES — Editorial Image Grid
    ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 md:px-12">

            {{-- Section Header --}}
            <div class="mb-10 md:mb-12 text-center">
                <p class="text-xs font-bold tracking-widest uppercase mb-3 font-[Inter]" style="color: #D4A017;">Explore Our Range</p>
                <h2 class="font-[Poppins] font-extrabold text-3xl md:text-4xl" style="color: #0F172A;">Shop by Category</h2>
                <div class="flex justify-center mt-4">
                    <div class="h-0.5 w-16 rounded-full" style="background-color: #D4A017;"></div>
                </div>
            </div>

            @php
            $catImages = [
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80',
            ];
            @endphp

            @if($categories->isNotEmpty())
                {{-- Desktop: editorial grid — first card tall, rest normal --}}
                <div class="hidden md:grid grid-cols-3 gap-4" style="grid-auto-rows: 248px;">

                    @foreach($categories->take(7) as $index => $category)
                        @php
                            $imgUrl = !empty($category->image)
                                ? $category->imageUrl()
                                : ($catImages[$index % count($catImages)] ?? $catImages[0]);
                            $catLink = route('webpage.shop', ['category' => $category->slug]);
                        @endphp

                        <a href="{{ $catLink }}"
                           wire:key="cat-desktop-{{ $category->id }}"
                           class="group relative rounded-2xl overflow-hidden cursor-pointer block"
                           style="{{ $index === 0 ? 'grid-row: span 2; height: 520px;' : 'height: 248px;' }}"
                           x-data="{ hovered: false }"
                           @mouseenter="hovered = true"
                           @mouseleave="hovered = false"
                        >
                            {{-- Background Image --}}
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                                 style="background-image: url('{{ $imgUrl }}');"
                                 onerror="this.style.background='linear-gradient(135deg, #0F172A 0%, #1E293B 100%)'">
                            </div>

                            {{-- Dark overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent transition-opacity duration-300 group-hover:from-black/75"></div>

                            {{-- Gold border on hover --}}
                            <div class="absolute inset-0 rounded-2xl border-2 border-transparent transition-all duration-300 group-hover:border-[#D4A017]"></div>

                            {{-- Content --}}
                            <div class="absolute bottom-0 left-0 right-0 p-5">
                                <h3 class="font-[Poppins] font-bold text-white text-lg leading-tight mb-1">
                                    {{ $category->name }}
                                </h3>
                                @if(!empty($category->products_count) || true)
                                <p class="text-white/60 text-xs font-[Inter] mb-3">
                                    {{ $category->products_count ?? '' }} styles
                                </p>
                                @endif
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold font-[Inter] opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0"
                                      style="color: #D4A017;">
                                    Shop Now
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach

                </div>

                {{-- Mobile: 2-col equal grid --}}
                <div class="grid md:hidden grid-cols-2 gap-3">
                    @foreach($categories->take(6) as $index => $category)
                        @php
                            $imgUrl = !empty($category->image)
                                ? $category->imageUrl()
                                : ($catImages[$index % count($catImages)] ?? $catImages[0]);
                        @endphp
                        <a href="{{ route('webpage.shop', ['category' => $category->slug]) }}"
                           wire:key="cat-mobile-{{ $category->id }}"
                           class="group relative rounded-xl overflow-hidden block"
                           style="height: 180px;">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105"
                                 style="background-image: url('{{ $imgUrl }}');"
                                 onerror="this.style.background='linear-gradient(135deg, #0F172A 0%, #1E293B 100%)'">
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-3">
                                <h3 class="font-[Poppins] font-bold text-white text-sm leading-tight">{{ $category->name }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>

            @else
                {{-- Fallback when no categories --}}
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="font-[Inter] text-slate-400">No categories available yet.</p>
                </div>
            @endif

        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 4 — FEATURED PRODUCTS — 4-col grid
    ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-20" style="background-color: #FDF8F0;">
        <div class="max-w-7xl mx-auto px-6 md:px-12">

            {{-- Section Header --}}
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="text-xs font-bold tracking-widest uppercase mb-2 font-[Inter]" style="color: #D4A017;">Handpicked for You</p>
                    <h2 class="font-[Poppins] font-extrabold text-3xl md:text-4xl" style="color: #0F172A;">Featured Collection</h2>
                    <div class="mt-3 h-0.5 w-16 rounded-full" style="background-color: #D4A017;"></div>
                </div>
                <a href="{{ route('webpage.shop') }}"
                   class="hidden md:inline-flex items-center gap-2 text-sm font-semibold font-[Inter] transition-colors duration-200 hover:underline"
                   style="color: #D4A017;">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            @if($featuredProducts->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @forelse($featuredProducts as $product)
                        @php $imgSrc = $product->primaryImage() ?: 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=400&q=80'; @endphp
                        <div wire:key="feat-{{ $product->id }}"
                             class="group relative bg-white rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                             style="box-shadow: 0 2px 12px rgba(0,0,0,0.06);"
                             x-data="{ wishlisted: false }">

                            {{-- Image wrapper --}}
                            <div class="relative overflow-hidden" style="height: 320px;">
                                <img src="{{ $imgSrc }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                {{-- Image fallback --}}
                                <div class="hidden w-full h-full items-center justify-center" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);">
                                    <svg class="w-14 h-14 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>

                                {{-- Badge top-left --}}
                                @if($product->isOnSale())
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #D4A017; color: #0F172A;">
                                        -{{ $product->discountPercent() }}%
                                    </span>
                                @elseif($product->is_featured)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #0F172A; color: #D4A017;">
                                        Featured
                                    </span>
                                @else
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background-color: #16A34A;">
                                        New
                                    </span>
                                @endif

                                {{-- Wishlist heart button top-right --}}
                                <button
                                    @click="wishlisted = !wishlisted"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 bg-white/90 hover:bg-white shadow-sm"
                                    aria-label="Add to wishlist">
                                    <svg class="w-4 h-4 transition-colors duration-200"
                                         :class="wishlisted ? 'text-red-500' : 'text-slate-400'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         :fill="wishlisted ? 'currentColor' : 'none'">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>

                                {{-- Add to Cart — slides up on hover --}}
                                <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <a href="{{ route('webpage.product-details', $product->slug) }}"
                                       class="flex items-center justify-center gap-2 w-full py-3 text-sm font-semibold font-[Inter] transition-colors duration-200"
                                       style="background-color: #0F172A; color: #D4A017;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Add to Cart
                                    </a>
                                </div>
                            </div>

                            {{-- Card body --}}
                            <div class="p-4">
                                @if($product->category)
                                    <p class="text-xs font-semibold font-[Inter] mb-1" style="color: #D4A017;">{{ $product->category->name }}</p>
                                @endif
                                <h3 class="font-[Poppins] font-semibold text-sm leading-snug mb-2 truncate" style="color: #0F172A;">
                                    <a href="{{ route('webpage.product-details', $product->slug) }}" class="hover:underline">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span class="font-[Poppins] font-bold text-base" style="color: #0F172A;">
                                        Rs. {{ number_format($product->effectivePrice(), 0) }}
                                    </span>
                                    @if($product->isOnSale())
                                        <span class="text-xs font-[Inter] line-through" style="color: #94A3B8;">
                                            Rs. {{ number_format($product->price, 0) }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Quick View --}}
                                <a href="{{ route('webpage.product-details', $product->slug) }}"
                                   class="mt-2 block text-xs font-[Inter] underline-offset-2 hover:underline" style="color: #64748B;">
                                    Quick View
                                </a>
                            </div>
                        </div>
                    @empty
                        {{-- Empty state --}}
                        <div class="col-span-full py-20 text-center">
                            <div class="w-20 h-20 mx-auto mb-5 rounded-full flex items-center justify-center" style="background-color: #F1F5F9;">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                            </div>
                            <h3 class="font-[Poppins] font-semibold text-lg mb-2" style="color: #0F172A;">No Featured Products Yet</h3>
                            <p class="font-[Inter] text-sm mb-6" style="color: #64748B;">Check back soon — new arrivals are on their way.</p>
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold font-[Inter] transition-all duration-200 hover:opacity-90"
                               style="background-color: #D4A017; color: #0F172A;">
                                Browse All Products
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Mobile "View All" --}}
                <div class="mt-8 text-center md:hidden">
                    <a href="{{ route('webpage.shop') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold font-[Inter] border-2 transition-all duration-200 hover:opacity-80"
                       style="border-color: #D4A017; color: #D4A017;">
                        View All Products
                    </a>
                </div>
            @else
                <div class="text-center py-20">
                    <p class="font-[Inter] text-slate-400">No featured products available.</p>
                </div>
            @endif

        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 5 — BRAND STORY / WHY US — dark navy
    ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-24" style="background-color: #0F172A;">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid md:grid-cols-2 gap-12 md:gap-16 items-center">

                {{-- Left: brand text --}}
                <div>
                    <span class="inline-block text-xs font-bold tracking-widest uppercase mb-4 font-[Inter]" style="color: #D4A017;">
                        About Meharahouse
                    </span>
                    <h2 class="font-[Poppins] font-extrabold text-3xl md:text-4xl lg:text-5xl leading-tight text-white mb-6">
                        Modest Fashion,<br>
                        <span style="color: #D4A017;">Maximum Elegance</span>
                    </h2>
                    <p class="font-[Inter] text-slate-300 text-base md:text-lg leading-relaxed mb-8">
                        Meharahouse was founded on a single belief — that modest dressing is not a compromise, it is a statement. We curate the finest abayas and innerwear from premium fabric houses, bringing world-class quality directly to your door. Every piece is selected with care, crafted for comfort, and designed to make you feel beautiful.
                    </p>
                    <a href="#"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold font-[Inter] border-2 transition-all duration-300 hover:bg-[#D4A017]/10"
                       style="border-color: #D4A017; color: #D4A017;">
                        Our Story
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                {{-- Right: 2x2 stats grid --}}
                <div class="grid grid-cols-2 gap-4">

                    <div class="rounded-2xl p-6" style="background-color: #1E293B;">
                        <div class="text-3xl md:text-4xl font-[Poppins] font-extrabold mb-1" style="color: #D4A017;">25+</div>
                        <div class="text-sm font-semibold text-white font-[Inter]">Years Experience</div>
                        <div class="text-xs text-slate-400 mt-1 font-[Inter]">Crafting modest fashion</div>
                    </div>

                    <div class="rounded-2xl p-6" style="background-color: #1E293B;">
                        <div class="text-3xl md:text-4xl font-[Poppins] font-extrabold mb-1" style="color: #D4A017;">12,000+</div>
                        <div class="text-sm font-semibold text-white font-[Inter]">Happy Customers</div>
                        <div class="text-xs text-slate-400 mt-1 font-[Inter]">Across the globe</div>
                    </div>

                    <div class="rounded-2xl p-6" style="background-color: #1E293B;">
                        <div class="text-3xl md:text-4xl font-[Poppins] font-extrabold mb-1" style="color: #D4A017;">500+</div>
                        <div class="text-sm font-semibold text-white font-[Inter]">Styles Available</div>
                        <div class="text-xs text-slate-400 mt-1 font-[Inter]">New drops every week</div>
                    </div>

                    <div class="rounded-2xl p-6" style="background-color: #1E293B;">
                        <div class="text-3xl md:text-4xl font-[Poppins] font-extrabold mb-1" style="color: #D4A017;">50+</div>
                        <div class="text-sm font-semibold text-white font-[Inter]">Countries Delivered</div>
                        <div class="text-xs text-slate-400 mt-1 font-[Inter]">Worldwide shipping</div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 6 — NEW ARRIVALS — horizontal scroll row
    ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-20 bg-white overflow-hidden" x-data="{ scrollRef: null }">
        <div class="max-w-7xl mx-auto px-6 md:px-12">

            {{-- Section Header --}}
            <div class="flex items-end justify-between mb-8">
                <div>
                    <p class="text-xs font-bold tracking-widest uppercase mb-2 font-[Inter]" style="color: #D4A017;">Just Landed</p>
                    <h2 class="font-[Poppins] font-extrabold text-3xl md:text-4xl" style="color: #0F172A;">New Arrivals</h2>
                    <div class="mt-3 h-0.5 w-16 rounded-full" style="background-color: #D4A017;"></div>
                </div>

                {{-- Scroll arrows --}}
                <div class="flex items-center gap-2">
                    <button
                        @click="$refs.newArrivalsScroll.scrollBy({ left: -280, behavior: 'smooth' })"
                        class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-200 hover:bg-[#0F172A] hover:text-white hover:border-[#0F172A]"
                        style="border-color: #0F172A; color: #0F172A;"
                        aria-label="Scroll left">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button
                        @click="$refs.newArrivalsScroll.scrollBy({ left: 280, behavior: 'smooth' })"
                        class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-200 hover:bg-[#0F172A] hover:text-white hover:border-[#0F172A]"
                        style="border-color: #0F172A; color: #0F172A;"
                        aria-label="Scroll right">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            @if($newArrivals->isNotEmpty())
                {{-- Horizontal scroll container --}}
                <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide"
                     x-ref="newArrivalsScroll"
                     style="-webkit-overflow-scrolling: touch; scrollbar-width: none; msOverflowStyle: none;">

                    @forelse($newArrivals as $product)
                        @php $imgSrc = $product->primaryImage() ?: 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=400&q=80'; @endphp
                        <div wire:key="new-{{ $product->id }}"
                             class="snap-start shrink-0 w-56 md:w-64 group bg-white rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                             style="box-shadow: 0 2px 12px rgba(0,0,0,0.06);"
                             x-data="{ wishlisted: false }">

                            {{-- Image --}}
                            <div class="relative overflow-hidden" style="height: 280px;">
                                <img src="{{ $imgSrc }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="hidden w-full h-full items-center justify-center" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);">
                                    <svg class="w-10 h-10 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>

                                {{-- New badge --}}
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background-color: #0F172A;">
                                    New
                                </span>

                                {{-- Sale badge override --}}
                                @if($product->isOnSale())
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #D4A017; color: #0F172A;">
                                        -{{ $product->discountPercent() }}%
                                    </span>
                                @endif

                                {{-- Wishlist --}}
                                <button
                                    @click="wishlisted = !wishlisted"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center bg-white/90 hover:bg-white shadow-sm transition-all duration-200"
                                    aria-label="Wishlist">
                                    <svg class="w-4 h-4 transition-colors duration-200"
                                         :class="wishlisted ? 'text-red-500' : 'text-slate-400'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         :fill="wishlisted ? 'currentColor' : 'none'">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>

                                {{-- Add to cart hover --}}
                                <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <a href="{{ route('webpage.product-details', $product->slug) }}"
                                       class="flex items-center justify-center w-full py-3 text-sm font-semibold font-[Inter]"
                                       style="background-color: #0F172A; color: #D4A017;">
                                        Add to Cart
                                    </a>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="p-4">
                                @if($product->category)
                                    <p class="text-xs font-semibold font-[Inter] mb-1" style="color: #D4A017;">{{ $product->category->name }}</p>
                                @endif
                                <h3 class="font-[Poppins] font-semibold text-sm leading-snug mb-2 truncate" style="color: #0F172A;">
                                    <a href="{{ route('webpage.product-details', $product->slug) }}" class="hover:underline">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span class="font-[Poppins] font-bold text-base" style="color: #0F172A;">
                                        Rs. {{ number_format($product->effectivePrice(), 0) }}
                                    </span>
                                    @if($product->isOnSale())
                                        <span class="text-xs font-[Inter] line-through" style="color: #94A3B8;">
                                            Rs. {{ number_format($product->price, 0) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 font-[Inter] py-8">No new arrivals at the moment.</p>
                    @endforelse

                </div>
            @else
                <div class="text-center py-16">
                    <p class="font-[Inter] text-slate-400">No new arrivals available yet.</p>
                </div>
            @endif

        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 7 — PROMOTIONAL BANNERS — side by side editorial
    ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-12 md:py-16" style="background-color: #FDF8F0;">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid md:grid-cols-2 gap-5">

                {{-- Left: Abaya Sale — dark navy --}}
                <div class="relative rounded-2xl overflow-hidden group" style="min-height: 320px;">
                    {{-- BG image --}}
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                         style="background-image: url('https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=800&q=80');">
                    </div>
                    {{-- Overlay --}}
                    <div class="absolute inset-0" style="background-color: rgba(15,23,42,0.82);"></div>
                    {{-- Content --}}
                    <div class="relative z-10 p-8 md:p-10 h-full flex flex-col justify-between" style="min-height: 320px;">
                        <div>
                            <span class="inline-block text-xs font-bold tracking-widest uppercase mb-3 font-[Inter]" style="color: #D4A017;">Limited Time</span>
                            <h3 class="font-[Poppins] font-extrabold text-white text-2xl md:text-3xl leading-tight mb-3">
                                Abaya Sale<br>
                                <span style="color: #D4A017;">Up to 40% OFF</span>
                            </h3>
                            <p class="font-[Inter] text-slate-300 text-sm mb-6">
                                Premium abayas at unbeatable prices — for a limited time only.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold font-[Inter] transition-all duration-200 hover:opacity-90 hover:shadow-lg"
                               style="background-color: #D4A017; color: #0F172A;">
                                Shop the Sale
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right: New Innerwear — cream --}}
                <div class="relative rounded-2xl overflow-hidden group" style="min-height: 320px;">
                    {{-- BG image --}}
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                         style="background-image: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=800&q=80');">
                    </div>
                    {{-- Cream overlay --}}
                    <div class="absolute inset-0" style="background-color: rgba(253,248,240,0.88);"></div>
                    {{-- Content --}}
                    <div class="relative z-10 p-8 md:p-10 h-full flex flex-col justify-between" style="min-height: 320px;">
                        <div>
                            <span class="inline-block text-xs font-bold tracking-widest uppercase mb-3 font-[Inter]" style="color: #D4A017;">New In</span>
                            <h3 class="font-[Poppins] font-extrabold text-3xl leading-tight mb-3" style="color: #0F172A;">
                                New Innerwear<br>Collection
                            </h3>
                            <p class="font-[Inter] text-sm mb-6" style="color: #475569;">
                                Soft, breathable and beautifully designed — essentials for every day.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('webpage.shop') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold font-[Inter] border-2 transition-all duration-200 hover:bg-[#0F172A] hover:text-white"
                               style="border-color: #0F172A; color: #0F172A;">
                                Explore Collection
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 8 — NEWSLETTER — cream, elegant
    ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-20" style="background-color: #FDF8F0; border-top: 1px solid #F0E8D8;">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="max-w-2xl mx-auto text-center">

                {{-- Icon --}}
                <div class="w-14 h-14 mx-auto mb-5 rounded-full flex items-center justify-center" style="background-color: #D4A017;">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <p class="text-xs font-bold tracking-widest uppercase mb-3 font-[Inter]" style="color: #D4A017;">Stay Connected</p>
                <h2 class="font-[Poppins] font-extrabold text-3xl md:text-4xl mb-4" style="color: #0F172A;">Stay in Style</h2>
                <p class="font-[Inter] text-base mb-8" style="color: #475569;">
                    Subscribe to our newsletter and be the first to know about new collections, exclusive offers, and style inspiration delivered straight to your inbox.
                </p>

                {{-- Success state --}}
                @if($subscribed)
                <div class="flex items-center justify-center gap-3 max-w-md mx-auto px-5 py-4 rounded-full mb-4"
                     style="background-color: #D4A017/10; border: 1px solid #D4A017;">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4A017;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold font-[Inter]" style="color: #0F172A;">Thank you for subscribing!</span>
                </div>
                @else
                {{-- Form --}}
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input
                        wire:model="subscribeEmail"
                        type="email"
                        placeholder="Your email address"
                        class="flex-1 px-5 py-3.5 rounded-full border font-[Inter] text-sm outline-none focus:ring-2 focus:ring-[#D4A017]/40 transition-all"
                        style="border-color: #D4A017; background-color: white; color: #0F172A;"
                    >
                    <button
                        wire:click="subscribe"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold font-[Inter] transition-all duration-200 hover:opacity-90 hover:shadow-md whitespace-nowrap"
                        style="background-color: #D4A017; color: #0F172A;">
                        <span wire:loading.remove wire:target="subscribe">Subscribe</span>
                        <span wire:loading wire:target="subscribe">Subscribing...</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="subscribe">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
                @if($subscribeError)
                <p class="mt-2 text-sm font-medium font-[Inter]" style="color: #EF4444;">{{ $subscribeError }}</p>
                @endif
                @error('subscribeEmail')
                <p class="mt-2 text-sm font-medium font-[Inter]" style="color: #EF4444;">{{ $message }}</p>
                @enderror
                @endif

                {{-- Privacy note --}}
                <p class="mt-4 text-xs font-[Inter]" style="color: #94A3B8;">
                    We respect your privacy. Unsubscribe at any time. No spam, ever.
                </p>
            </div>
        </div>
    </section>


</div>
