{{-- resources/views/layouts/staff.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Panel') — Meharahouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#F1F5F9] antialiased">

<div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" class="min-h-screen flex">

    {{-- ════════════════════════ SIDEBAR (Staff — Teal Theme) ════════════════════════ --}}
    <aside :class="{ '-translate-x-full': !sidebarOpen }"
           class="fixed top-0 left-0 w-60 h-screen bg-[#134e4a] z-50 flex flex-col transition-transform duration-300 shadow-2xl lg:translate-x-0">

        {{-- Header --}}
        <div class="flex items-center gap-3 h-16 px-4 border-b border-[#0f3d3a] shrink-0">
            <div class="bg-white rounded-lg p-1 shrink-0">
                <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Mehra House" class="h-8 w-auto">
            </div>
            <div>
                <span class="block text-[#5eead4] text-[10px] font-bold tracking-widest uppercase">Staff Portal</span>
            </div>
        </div>

        {{-- Staff Badge --}}
        <div class="mx-3 mt-4 mb-2 px-3 py-2 bg-[#0f3d3a] rounded-lg">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-teal-400 flex items-center justify-center">
                    <span class="text-[#134e4a] font-bold text-xs">S</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">Staff Member</p>
                    <p class="text-xs text-teal-400 truncate">staff@meharahouse.com</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-2 px-3 space-y-0.5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-teal-700 px-2 py-2 mt-1">Operations</p>

            @php
                $staffNavItems = [
                    ['route' => 'staff.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'staff.orders', 'label' => 'Order Queue', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'badge' => '8'],
                    ['route' => 'staff.customers', 'label' => 'Customers', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
            @endphp

            @foreach($staffNavItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs($item['route']) ? 'bg-[#F59E0B] text-[#0F172A] font-bold' : 'text-teal-200 hover:bg-[#0f3d3a] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span>{{ $item['label'] }}</span>
                @if(isset($item['badge']))
                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold
                             {{ request()->routeIs($item['route']) ? 'bg-[#0F172A] text-[#F59E0B]' : 'bg-teal-700 text-teal-200' }}">
                    {{ $item['badge'] }}
                </span>
                @endif
            </a>
            @endforeach

            <p class="text-[10px] font-bold uppercase tracking-widest text-teal-700 px-2 py-2 mt-3">Account</p>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-teal-200 hover:bg-[#0f3d3a] hover:text-white transition-all duration-200">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-all duration-200">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </a>
        </nav>

        {{-- Quick Status --}}
        <div class="border-t border-[#0f3d3a] p-3 shrink-0">
            <div class="text-center">
                <span class="text-xs text-teal-400">{{ now()->format('D, d M Y') }}</span>
                <div class="flex items-center justify-center gap-1.5 mt-1">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-xs text-teal-300 font-medium">On Shift</span>
                </div>
            </div>
        </div>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" style="display:none;"></div>

    {{-- ════════════════════════ MAIN AREA ════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col lg:ml-60 transition-all duration-300">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 bg-white border-b border-[#E2E8F0] h-14 flex items-center px-4 md:px-6 gap-4 shadow-sm">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-[#475569] hover:bg-[#F1F5F9]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1 min-w-0">
                <h1 class="text-sm font-bold text-[#0F172A] font-[Poppins] truncate">@yield('page_title', 'Staff Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="hidden sm:flex items-center gap-1.5 text-xs text-[#64748B]">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    System Online
                </span>
                <button class="relative p-2 rounded-lg text-[#475569] hover:bg-[#F1F5F9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-[#F59E0B] rounded-full"></span>
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-4 md:p-6 overflow-y-auto">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>
</div>

@livewireScripts
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
