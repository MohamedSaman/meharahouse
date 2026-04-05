{{-- resources/views/layouts/webpage.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meharahouse') — Quality You Can Trust</title>
    <meta name="description" content="@yield('meta_description', 'Meharahouse — Ethiopia\'s premier online store for quality products at unbeatable prices.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] antialiased">

    {{-- ══════════════════════════ TOP BAR ══════════════════════════ --}}
    <div class="bg-[#0F172A] text-[#94A3B8] text-xs py-2 hidden md:block">
        <div class="container-page flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                support@meharahouse.com
            </span>
            <span>Free shipping on orders over ETB 500</span>
            <span class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                +251 911 000 000
            </span>
        </div>
    </div>

    {{-- ══════════════════════════ NAVBAR ══════════════════════════ --}}
    <header
        x-data="{ open: false, scrolled: false }"
        x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
        :class="scrolled ? 'shadow-lg bg-white/98 backdrop-blur-sm' : 'bg-white'"
        class="sticky top-0 z-50 transition-all duration-300 border-b border-[#E2E8F0]"
    >
        <div class="container-page">
            <div class="flex items-center justify-between h-16 md:h-20">

                {{-- Logo --}}
                <a href="{{ route('webpage.home') }}" class="flex items-center shrink-0">
                    <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Mehra House" class="h-12 w-auto">
                </a>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('webpage.home') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#0F172A] hover:text-[#F59E0B] hover:bg-[#FFFBEB] transition-all duration-200">
                        Home
                    </a>
                    <a href="{{ route('webpage.shop') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#F59E0B] hover:bg-[#FFFBEB] transition-all duration-200">
                        Shop
                    </a>
                    <a href="{{ route('webpage.about') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#F59E0B] hover:bg-[#FFFBEB] transition-all duration-200">
                        About
                    </a>
                    <a href="{{ route('webpage.contact') }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold text-[#475569] hover:text-[#F59E0B] hover:bg-[#FFFBEB] transition-all duration-200">
                        Contact
                    </a>
                </nav>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2 md:gap-3">
                    {{-- Search --}}
                    <button class="p-2 rounded-lg text-[#475569] hover:text-[#0F172A] hover:bg-[#F1F5F9] transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    {{-- Cart --}}
                    <a href="{{ route('webpage.cart') }}"
                       class="relative p-2 rounded-lg text-[#475569] hover:text-[#0F172A] hover:bg-[#F1F5F9] transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#F59E0B] text-[#0F172A] text-[10px] font-black rounded-full flex items-center justify-center">3</span>
                    </a>

                    {{-- Auth Button --}}
                    @auth
                    <div x-data="{ open: false }" class="hidden md:block relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-[#0F172A] hover:bg-[#F1F5F9] transition-colors duration-200">
                            <div class="w-7 h-7 rounded-full bg-[#0F172A] flex items-center justify-center">
                                <span class="text-[#F59E0B] font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="hidden lg:block">{{ Str::limit(auth()->user()->name, 12) }}</span>
                            <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl border border-[#E2E8F0] shadow-xl py-1 z-50" style="display:none;">
                            <div class="px-4 py-3 border-b border-[#F1F5F9]">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-[#64748B] truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('webpage.orders') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#F8FAFC] hover:text-[#0F172A]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#F8FAFC] hover:text-[#0F172A]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Admin Panel
                            </a>
                            @elseif(auth()->user()->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#F8FAFC] hover:text-[#0F172A]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Staff Panel
                            </a>
                            @endif
                            <div class="border-t border-[#F1F5F9] mt-1">
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50">
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

                    {{-- Mobile Menu Toggle --}}
                    <button @click="open = !open" class="lg:hidden p-2 rounded-lg text-[#475569] hover:bg-[#F1F5F9] transition-colors duration-200">
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden pb-4 border-t border-[#F1F5F9] mt-1 pt-3" style="display:none;">
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('webpage.home') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-[#0F172A] hover:bg-[#FFFBEB] hover:text-[#F59E0B]">Home</a>
                    <a href="{{ route('webpage.shop') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-[#475569] hover:bg-[#FFFBEB] hover:text-[#F59E0B]">Shop</a>
                    <a href="{{ route('webpage.about') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-[#475569] hover:bg-[#FFFBEB] hover:text-[#F59E0B]">About</a>
                    <a href="{{ route('webpage.contact') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-[#475569] hover:bg-[#FFFBEB] hover:text-[#F59E0B]">Contact</a>
                    <div class="mt-2 px-4">
                        <a href="{{ route('auth.login') }}" class="btn-primary w-full justify-center">Sign In</a>
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
    <footer class="bg-[#0F172A] text-[#94A3B8] mt-20">
        {{-- Newsletter Banner --}}
        <div class="bg-gradient-to-r from-[#F59E0B] to-[#FBBF24] py-12">
            <div class="container-page text-center">
                <h3 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-2">Get Exclusive Deals in Your Inbox</h3>
                <p class="text-[#0F172A]/70 mb-6 text-sm">Subscribe and be the first to know about new arrivals and promotions.</p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" placeholder="Enter your email address"
                           class="flex-1 px-4 py-3 rounded-lg border-0 text-[#0F172A] text-sm placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-[#0F172A]/20">
                    <button type="submit" class="px-6 py-3 bg-[#0F172A] text-white font-bold text-sm rounded-lg hover:bg-[#1E293B] transition-colors duration-200 whitespace-nowrap">
                        Subscribe Now
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer Links --}}
        <div class="py-16">
            <div class="container-page grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center mb-5">
                        <div class="bg-white rounded-xl p-1.5 inline-block">
                            <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Mehra House" class="h-10 w-auto">
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed mb-5">Your trusted destination for quality products across Ethiopia. We bring you the best at fair prices.</p>
                    <div class="flex gap-3">
                        @foreach(['facebook','twitter','instagram','youtube'] as $social)
                        <a href="#" class="w-9 h-9 rounded-lg bg-[#1E293B] flex items-center justify-center text-[#64748B] hover:text-[#F59E0B] hover:bg-[#334155] transition-all duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                @if($social === 'facebook')
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                @elseif($social === 'twitter')
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                @elseif($social === 'instagram')
                                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12c0 3.259.014 3.668.072 4.948.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24c3.259 0 3.668-.014 4.948-.072 1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.689.072-4.948 0-3.259-.014-3.667-.072-4.947-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                @else
                                    <path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                                @endif
                            </svg>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-[Poppins] font-bold text-white text-sm uppercase tracking-wider mb-5">Quick Links</h4>
                    <ul class="space-y-3">
                        @foreach(['Home' => 'webpage.home', 'Shop All' => 'webpage.shop', 'About Us' => 'webpage.about', 'Contact' => 'webpage.contact'] as $label => $route)
                        <li>
                            <a href="{{ route($route) }}" class="text-sm hover:text-[#F59E0B] transition-colors duration-200 flex items-center gap-2">
                                <span class="w-1 h-1 rounded-full bg-[#F59E0B]"></span>
                                {{ $label }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Customer Service --}}
                <div>
                    <h4 class="font-[Poppins] font-bold text-white text-sm uppercase tracking-wider mb-5">Customer Service</h4>
                    <ul class="space-y-3">
                        @foreach(['My Account', 'Order Tracking', 'Returns & Refunds', 'FAQ', 'Privacy Policy', 'Terms of Service'] as $link)
                        <li>
                            <a href="#" class="text-sm hover:text-[#F59E0B] transition-colors duration-200 flex items-center gap-2">
                                <span class="w-1 h-1 rounded-full bg-[#F59E0B]"></span>
                                {{ $link }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="font-[Poppins] font-bold text-white text-sm uppercase tracking-wider mb-5">Contact Us</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-[#F59E0B] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm leading-relaxed">Bole Road, Addis Ababa<br>Ethiopia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm">+251 911 000 000</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm">support@meharahouse.com</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-[#1E293B] py-6">
            <div class="container-page flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[#475569]">&copy; {{ date('Y') }} Meharahouse. All rights reserved.</p>
                <div class="flex items-center gap-3">
                    @foreach(['Visa', 'MC', 'PayPal', 'CBE'] as $method)
                    <span class="px-2.5 py-1 bg-[#1E293B] rounded text-xs text-[#64748B] font-semibold">{{ $method }}</span>
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
