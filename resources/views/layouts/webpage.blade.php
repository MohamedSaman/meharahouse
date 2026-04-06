{{-- resources/views/layouts/webpage.blade.php | Mehra House — Modest Fashion --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mehra House') — Elegance in Every Thread</title>
    <meta name="description" content="@yield('meta_description', 'Mehra House — Ethiopia\'s premier destination for premium abaya dresses and modest innerwear for women.')">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/meharahouse-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/meharahouse-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white text-[#1E293B] antialiased">

    {{-- ══════════════════════════ ANNOUNCEMENT BAR ══════════════════════════ --}}
    <div class="bg-[#D4A017] text-white text-xs py-2.5 text-center font-medium tracking-wide">
        <div class="container-page flex items-center justify-center gap-2">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            <span>Free Delivery on Orders Over <strong>Rs. 500</strong> &nbsp;|&nbsp; New Arrivals Every Week &nbsp;|&nbsp; Modest Fashion for Every Woman</span>
        </div>
    </div>

    {{-- ══════════════════════════ NAVBAR ══════════════════════════ --}}
    <header
        x-data="{
            open: false,
            scrolled: false,
            searchOpen: false,
            init() { window.addEventListener('scroll', () => { this.scrolled = window.scrollY > 20; }); }
        }"
        :class="scrolled ? 'shadow-[0_4px_24px_rgba(15,23,42,0.10)] bg-white/98 backdrop-blur-md' : 'bg-white'"
        class="sticky top-0 z-50 transition-all duration-300 border-b border-[#F0EDE8]"
    >
        <div class="container-page">
            <div class="flex items-center justify-between h-20 md:h-24 lg:h-28">

                {{-- Logo --}}
                {{-- Logo --}}
                <a href="{{ route('webpage.home') }}" class="flex items-center gap-3 shrink-0 group" aria-label="Mehra House home">
                    <div class="flex items-center justify-center transition-transform duration-300 group-hover:scale-[1.02]">
                        <img
                            src="{{ asset('images/meharahouse-logo.png') }}"
                            alt="Mehra House Logo"
                            class="h-16 w-auto md:h-20 lg:h-24 max-w-[240px] object-contain"
                        >
                    </div>

                </a>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-0.5">
                    <a href="{{ route('webpage.home') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#0F172A] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200">
                        Home
                    </a>
                    <a href="{{ route('webpage.shop') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200">
                        Shop
                    </a>
                    <a href="{{ route('webpage.shop', ['category' => 'abaya']) }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200">
                        Abayas
                    </a>

                    <a href="{{ route('webpage.about') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200">
                        About
                    </a>
                    <a href="{{ route('webpage.contact') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200">
                        Contact
                    </a>
                    <a href="{{ route('webpage.reviews') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-1.5
                              {{ request()->routeIs('webpage.reviews') ? 'text-[#D4A017] bg-[#FFFDF5]' : 'text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5]' }}">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Reviews
                    </a>
                </nav>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2 md:gap-3">
                    {{-- Search --}}
                    <button @click="searchOpen = !searchOpen"
                            class="p-2 rounded-lg text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200" title="Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    {{-- Cart --}}
                    @php
                        $cartCount = auth()->check()
                            ? \App\Models\Cart::where('user_id', auth()->id())->sum('quantity')
                            : collect(session()->get('cart', []))->sum('quantity');
                    @endphpincrese <i class="fa fa-text-width" aria-hidden="true"></i>
                    <a href="{{ route('webpage.cart') }}"
                       class="relative p-2 rounded-lg text-[#475569] hover:text-[#D4A017] hover:bg-[#FFFDF5] transition-all duration-200" title="Cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if($cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#D4A017] text-white text-[9px] font-black rounded-full flex items-center justify-center leading-none">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                        @endif
                    </a>

                    {{-- Auth Button --}}
                    @auth
                    <div x-data="{ userOpen: false }" class="hidden md:block relative">
                        <button @click="userOpen = !userOpen"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold text-[#0F172A] hover:bg-[#FFFDF5] transition-all duration-200">
                            <div class="w-7 h-7 rounded-full bg-[#0F172A] flex items-center justify-center">
                                <span class="text-[#D4A017] font-black text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="hidden lg:block text-sm">{{ Str::limit(auth()->user()->name, 12) }}</span>
                            <svg class="w-3.5 h-3.5 text-[#94A3B8] transition-transform duration-200" :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="userOpen" @click.outside="userOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl border border-[#F0EDE8] shadow-2xl shadow-black/10 py-2 z-50"
                             style="display:none;">
                            <div class="px-4 py-3 border-b border-[#F8F5F0]">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-[#94A3B8] truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('webpage.orders') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Admin Panel
                            </a>
                            @elseif(auth()->user()->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Staff Panel
                            </a>
                            @endif
                            <div class="border-t border-[#F8F5F0] mt-1 pt-1">
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('auth.login') }}" class="hidden md:inline-flex btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Sign In
                    </a>
                    @endauth

                    {{-- Mobile Hamburger --}}
                    <button @click="open = !open" class="lg:hidden p-2 rounded-lg text-[#475569] hover:bg-[#FFFDF5] transition-all duration-200" aria-label="Toggle menu">
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Expandable Search Bar --}}
            <div x-show="searchOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="pb-4 pt-2 border-t border-[#F8F5F0]"
                 style="display:none;">
                <form action="{{ route('webpage.shop') }}" method="GET" class="flex gap-2 max-w-xl mx-auto">
                    <input type="text" name="search" placeholder="Search abayas, innerwear, jilbabs..."
                           class="flex-1 px-4 py-2.5 bg-[#FDF8F0] border border-[#E8DDD0] rounded-xl text-sm placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/30 focus:border-[#D4A017] transition-all"
                           autofocus>
                    <button type="submit" class="btn-primary btn-sm px-5">Search</button>
                </form>
            </div>

            {{-- Mobile Slide-in Menu --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-3"
                 class="lg:hidden pb-5 border-t border-[#F8F5F0] mt-1 pt-4"
                 style="display:none;">
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('webpage.home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-all duration-200">
                        <svg class="w-4 h-4 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Home
                    </a>
                    <a href="{{ route('webpage.shop') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-all duration-200">
                        <svg class="w-4 h-4 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Shop All
                    </a>
                    <a href="{{ route('webpage.shop', ['category' => 'abaya']) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-all duration-200">
                        <svg class="w-4 h-4 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        Abayas
                    </a>
                    <a href="{{ route('webpage.about') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-all duration-200">
                        <svg class="w-4 h-4 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        About
                    </a>
                    <a href="{{ route('webpage.contact') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017] transition-all duration-200">
                        <svg class="w-4 h-4 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Contact
                    </a>
                    <a href="{{ route('webpage.reviews') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200
                              {{ request()->routeIs('webpage.reviews') ? 'bg-[#FFFDF5] text-[#D4A017]' : 'text-[#475569] hover:bg-[#FFFDF5] hover:text-[#D4A017]' }}">
                        <svg class="w-4 h-4 text-[#D4A017]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Reviews
                    </a>
                    <div class="mt-3 px-4 pt-4 border-t border-[#F0EDE8]">
                        @auth
                        <a href="{{ route('webpage.orders') }}" class="btn-primary w-full justify-center">My Orders</a>
                        @else
                        <a href="{{ route('auth.login') }}" class="btn-primary w-full justify-center">Sign In</a>
                        @endauth
                    </div>
                </nav>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════ MAIN CONTENT ══════════════════════════ --}}
    <main>
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- ══════════════════════════ FOOTER ══════════════════════════ --}}
    <footer class="bg-[#0F172A] text-[#94A3B8]">

        {{-- Newsletter Strip --}}
        <div class="border-b border-[#1E293B]">
            <div class="container-page py-14">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div>
                        <span class="text-[#D4A017] text-xs font-bold uppercase tracking-[0.15em]">Stay Connected</span>
                        <h3 class="font-[Poppins] font-bold text-2xl md:text-3xl text-white mt-1 mb-2">Exclusive Deals, Delivered to You</h3>
                        <p class="text-sm text-[#64748B] max-w-md">Be first to know about new abaya arrivals, seasonal sales, and members-only offers.</p>
                    </div>
                    <form class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto lg:min-w-[420px]">
                        <input type="email" placeholder="Your email address"
                               class="flex-1 px-4 py-3 rounded-xl bg-[#1E293B] border border-[#334155] text-white text-sm placeholder-[#475569] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all duration-200">
                        <button type="submit" class="btn-primary whitespace-nowrap px-6 py-3">
                            Subscribe
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Link Columns --}}
        <div class="py-16">
            <div class="container-page grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <a href="{{ route('webpage.home') }}" class="flex items-center" aria-label="Mehra House home">
                            <img
                                src="{{ asset('images/meharahouse-logo.png') }}"
                                alt="Mehra House Logo"
                                class="h-16 w-auto max-w-[180px] object-contain"
                            >
                        </a>
                    </div>
                    <p class="text-sm leading-relaxed mb-6 text-[#64748B]">Ethiopia's premier destination for premium abaya dresses and modest innerwear. Elegance crafted for the modern woman of faith.</p>
                    <div class="flex gap-2.5">
                        @foreach(['facebook','instagram','twitter','tiktok'] as $social)
                        <a href="#" class="w-9 h-9 rounded-xl bg-[#1E293B] hover:bg-[#D4A017] flex items-center justify-center text-[#475569] hover:text-white transition-all duration-200" title="{{ ucfirst($social) }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                @if($social === 'facebook')
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                @elseif($social === 'instagram')
                                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12c0 3.259.014 3.668.072 4.948.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24c3.259 0 3.668-.014 4.948-.072 1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.689.072-4.948 0-3.259-.014-3.667-.072-4.947-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                @elseif($social === 'twitter')
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                @else
                                    <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.16 8.16 0 004.79 1.52V6.76a4.85 4.85 0 01-1.03-.07z"/>
                                @endif
                            </svg>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-[Poppins] font-bold text-white text-sm uppercase tracking-[0.12em] mb-5">Quick Links</h4>
                    <ul class="space-y-3">
                        @foreach(['Home' => 'webpage.home', 'Shop All' => 'webpage.shop', 'About Us' => 'webpage.about', 'Contact Us' => 'webpage.contact'] as $label => $routeName)
                        <li>
                            <a href="{{ route($routeName) }}" class="flex items-center gap-2.5 text-sm text-[#64748B] hover:text-[#D4A017] transition-colors duration-200 group">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#D4A017]/30 group-hover:bg-[#D4A017] transition-colors duration-200 shrink-0"></span>
                                {{ $label }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Collections --}}
                <div>
                    <h4 class="font-[Poppins] font-bold text-white text-sm uppercase tracking-[0.12em] mb-5">Collections</h4>
                    <ul class="space-y-3">
                        @foreach(['Abaya' => 'abaya', 'Jilbab' => 'jilbab', 'Casual Abaya' => 'casual-abaya', 'Formal Abaya' => 'formal-abaya', 'Inner Dress' => 'inner-dress', 'Innerwear' => 'innerwear'] as $label => $slug)
                        <li>
                            <a href="{{ route('webpage.shop', ['category' => $slug]) }}" class="flex items-center gap-2.5 text-sm text-[#64748B] hover:text-[#D4A017] transition-colors duration-200 group">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#D4A017]/30 group-hover:bg-[#D4A017] transition-colors duration-200 shrink-0"></span>
                                {{ $label }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="font-[Poppins] font-bold text-white text-sm uppercase tracking-[0.12em] mb-5">Contact Us</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-[#D4A017]/10 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-sm text-[#64748B] leading-relaxed">Bole Road, Addis Ababa<br>Ethiopia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-[#D4A017]/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <a href="tel:+251911000000" class="text-sm text-[#64748B] hover:text-[#D4A017] transition-colors">+251 911 000 000</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-[#D4A017]/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <a href="mailto:support@mehrahouse.com" class="text-sm text-[#64748B] hover:text-[#D4A017] transition-colors">support@mehrahouse.com</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-[#D4A017]/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-sm text-[#64748B]">Mon &ndash; Sat: 9am &ndash; 7pm</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-[#1E293B] py-5">
            <div class="container-page flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[#475569]">&copy; {{ date('Y') }} <span class="text-[#D4A017] font-semibold">Mehra House</span>. All rights reserved. &mdash; Elegance in Every Thread.</p>
                <div class="flex items-center gap-2">
                    @foreach(['CBE Birr', 'Telebirr', 'Visa', 'Mastercard'] as $method)
                    <span class="px-2.5 py-1 bg-[#1E293B] border border-[#334155] rounded-lg text-[10px] text-[#64748B] font-bold tracking-wide">{{ $method }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>
