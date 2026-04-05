{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Meharahouse Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#F1F5F9] antialiased">

<div
    x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        sidebarCollapsed: false,
        get sidebarWidth() { return this.sidebarCollapsed ? '72px' : '256px' }
    }"
    class="min-h-screen flex"
>
    {{-- ════════════════════════ SIDEBAR ════════════════════════ --}}
    <aside
        :class="{ '-translate-x-full': !sidebarOpen }"
        :style="sidebarCollapsed ? 'width:72px' : 'width:256px'"
        class="fixed top-0 left-0 h-screen bg-[#0F172A] z-50 flex flex-col transition-all duration-300 shadow-2xl lg:translate-x-0"
    >
        {{-- Sidebar Header --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-[#1E293B] shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 bg-[#F59E0B] rounded-lg flex items-center justify-center shrink-0">
                    <span class="text-[#0F172A] font-black text-sm font-[Poppins]">MH</span>
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0">
                    <span class="block font-black text-white text-base leading-none font-[Poppins] truncate">Meharahouse</span>
                    <span class="block text-[#F59E0B] text-[10px] font-bold tracking-widest uppercase">Admin Panel</span>
                </div>
            </a>
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    x-show="!sidebarCollapsed"
                    class="hidden lg:flex w-7 h-7 rounded-md bg-[#1E293B] items-center justify-center text-[#64748B] hover:text-white transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    x-show="sidebarCollapsed"
                    class="hidden lg:flex w-7 h-7 rounded-md bg-[#1E293B] items-center justify-center text-[#64748B] hover:text-white transition-colors shrink-0"
                    style="display:none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            {{-- Main --}}
            <p x-show="!sidebarCollapsed" class="sidebar-section-label">Main</p>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Dashboard' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Dashboard</span>
            </a>

            <a href="{{ route('admin.orders') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Orders' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Orders</span>
                @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $pendingCount }}</span>
                @endif
            </a>

            {{-- Catalog --}}
            <p x-show="!sidebarCollapsed" class="sidebar-section-label">Catalog</p>

            <a href="{{ route('admin.products') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Products' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Products</span>
            </a>

            <a href="{{ route('admin.categories') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Categories' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Categories</span>
            </a>

            {{-- Users --}}
            <p x-show="!sidebarCollapsed" class="sidebar-section-label">Users</p>

            <a href="{{ route('admin.customers') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Customers' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Customers</span>
            </a>

            {{-- Finance --}}
            <p x-show="!sidebarCollapsed" class="sidebar-section-label">Finance</p>

            <a href="{{ route('admin.payments') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Payments' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Payments</span>
            </a>

            <a href="{{ route('admin.reports') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Reports' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Reports</span>
            </a>
        </nav>

        {{-- Sidebar Footer --}}
        <div class="border-t border-[#1E293B] p-3 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#F59E0B] flex items-center justify-center shrink-0">
                    <span class="text-[#0F172A] font-bold text-sm">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</span>
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()?->name ?? 'Admin' }}</p>
                    <p class="text-xs text-[#64748B] truncate">{{ auth()->user()?->email ?? 'admin@meharahouse.com' }}</p>
                </div>
                <form x-show="!sidebarCollapsed" method="POST" action="{{ route('auth.logout') }}" class="ml-auto shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-md text-[#64748B] hover:text-white hover:bg-[#1E293B] transition-colors" title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Sidebar Overlay (mobile) --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" style="display:none;"></div>

    {{-- ════════════════════════ MAIN AREA ════════════════════════ --}}
    <div :style="'margin-left:' + (window.innerWidth >= 1024 ? sidebarWidth : '0px')"
         class="flex-1 min-w-0 flex flex-col transition-all duration-300">

        {{-- Top Header --}}
        <header class="sticky top-0 z-30 bg-white border-b border-[#E2E8F0] h-16 flex items-center px-4 md:px-6 gap-4 shadow-sm">
            {{-- Mobile menu toggle --}}
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-[#475569] hover:bg-[#F1F5F9]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-bold text-[#0F172A] font-[Poppins] truncate">@yield('page_title', 'Dashboard')</h1>
                <p class="text-xs text-[#64748B] truncate hidden sm:block">@yield('page_subtitle', 'Welcome back, Admin')</p>
            </div>

            {{-- Header Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                {{-- Search --}}
                <div class="hidden md:flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 w-56">
                    <svg class="w-4 h-4 text-[#64748B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Search..." class="bg-transparent text-sm text-[#475569] outline-none flex-1 min-w-0 placeholder-[#94A3B8]">
                </div>

                {{-- Notifications --}}
                <button class="relative p-2 rounded-lg text-[#475569] hover:bg-[#F1F5F9] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                {{-- Admin Avatar --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="w-9 h-9 rounded-full bg-[#0F172A] flex items-center justify-center">
                        <span class="text-[#F59E0B] font-bold text-sm">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</span>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl border border-[#E2E8F0] shadow-xl py-1 z-50" style="display:none;">
                        <div class="px-4 py-3 border-b border-[#F1F5F9]">
                            <p class="text-sm font-semibold text-[#0F172A]">{{ auth()->user()?->name ?? 'Admin' }}</p>
                            <p class="text-xs text-[#64748B]">{{ auth()->user()?->email }}</p>
                        </div>
                        <a href="{{ route('webpage.home') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#F8FAFC] hover:text-[#0F172A]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Visit Storefront
                        </a>
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
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-y-auto">
            @yield('content')
            {{ $slot ?? '' }}
        </main>

        {{-- Admin Footer --}}
        <footer class="border-t border-[#E2E8F0] bg-white px-6 py-3">
            <p class="text-xs text-[#94A3B8] text-center">
                &copy; {{ date('Y') }} Meharahouse Admin Panel. All rights reserved. &mdash; v1.0.0
            </p>
        </footer>
    </div>
</div>

@livewireScripts
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
