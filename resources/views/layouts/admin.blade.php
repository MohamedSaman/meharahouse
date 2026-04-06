{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Meharahouse Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/meharahouse-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/meharahouse-logo.png') }}">

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
        isDesktop: window.innerWidth >= 1024,
        init() {
            window.addEventListener('resize', () => {
                this.isDesktop = window.innerWidth >= 1024;
                if (this.isDesktop) this.sidebarOpen = true;
            });
        },
        get sidebarWidth() { return this.sidebarCollapsed ? '72px' : '256px' }
    }"
    class="min-h-screen flex"
>
    {{-- ════════════════════════ SIDEBAR ════════════════════════ --}}
    <aside
        :class="{ '-translate-x-full': !sidebarOpen }"
        :style="sidebarCollapsed ? 'width:72px' : 'width:264px'"
        class="fixed top-0 left-0 h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 z-50 flex flex-col transition-all duration-300 shadow-2xl border-r border-white/5 lg:translate-x-0"
    >
        {{-- Sidebar Header --}}
        <div class="relative flex items-center h-16 px-4 border-b border-white/10 shrink-0">
            <a href="{{ route('admin.dashboard') }}"
               class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center justify-center text-center gap-0.5">
                <div class="rounded-xl px-2 py-1 ">
                    <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Mehra House" class="h-18 w-auto">
                </div>            </a>
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    x-show="!sidebarCollapsed"
                    class="absolute right-4 hidden lg:flex w-8 h-8 rounded-xl bg-white/5 items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    x-show="sidebarCollapsed"
                    class="absolute right-4 hidden lg:flex w-8 h-8 rounded-xl bg-white/5 items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all shrink-0"
                    style="display:none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">

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
               class="sidebar-nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Orders' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Orders</span>
                @php $newOrderCount = \App\Models\Order::where('status','new')->count(); @endphp
                @if($newOrderCount > 0)
                <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $newOrderCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.whatsapp-orders') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.whatsapp-orders*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'WhatsApp Orders' : ''">
                {{-- WhatsApp icon --}}
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" style="color:#25D366">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">WhatsApp Orders</span>
                @php $pendingWaTokens = \App\Models\WhatsappOrderToken::where('status','pending')->count(); @endphp
                @if($pendingWaTokens > 0)
                <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $pendingWaTokens }}</span>
                @endif
            </a>

            <a href="{{ route('admin.returns') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.returns*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Returns' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Returns</span>
                @php $openReturns = \App\Models\OrderReturn::whereIn('status',['requested','pickup_arranged','received'])->count(); @endphp
                @if($openReturns > 0)
                <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $openReturns }}</span>
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
            <a href="{{ route('admin.payments') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.payments') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Payments' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Payments</span>
            </a>

         

            {{-- Purchasing --}}
            <p x-show="!sidebarCollapsed" class="sidebar-section-label">Purchasing</p>

            <a href="{{ route('admin.suppliers') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Suppliers' : ''">
                {{-- Factory / building icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Suppliers</span>
            </a>

            <a href="{{ route('admin.purchasing') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.purchasing*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Purchase Orders' : ''">
                {{-- Clipboard / document icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Purchase Orders</span>
                @php $draftPoCount = \App\Models\PurchaseOrder::where('status','draft')->count(); @endphp
                @if($draftPoCount > 0)
                <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $draftPoCount }}</span>
                @endif
            </a>
            {{-- 
            <a href="{{ route('admin.manual-order') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.manual-order*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Manual Order' : ''">
            
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Manual Order</span>
            </a> --}}

            <a href="{{ route('admin.shipments') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.shipments*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Shipments' : ''">
                {{-- Box / shipment icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Shipments</span>
                @php $activeShipments = \App\Models\ShipmentBatch::whereNotIn('status', ['completed'])->count(); @endphp
                @if($activeShipments > 0)
                <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $activeShipments }}</span>
                @endif
            </a>

            <a href="{{ route('admin.supplier-payments') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.supplier-payments*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Supplier Payments' : ''">
                {{-- Arrow up / outgoing money icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12M3 21h18"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Supplier Payments</span>
                @php
                    $supplierDue = \App\Models\SupplierInvoice::whereIn('status', ['pending','partial'])->count();
                @endphp
                @if($supplierDue > 0)
                    <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $supplierDue }}</span>
                @endif
            </a>

            {{-- <a href="{{ route('admin.customer-payments') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.customer-payments*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Customer Payments' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6M3 3h18"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Customer Payments</span>
                @php
                    $customerDue = \App\Models\CustomerAccount::whereIn('status', ['pending','partial'])->count();
                @endphp
                @if($customerDue > 0)
                    <span x-show="!sidebarCollapsed" class="ml-auto badge badge-gold text-[10px] py-0.5 px-2">{{ $customerDue }}</span>
                @endif
            </a> --}}
               {{-- Website --}}
            <p x-show="!sidebarCollapsed" class="sidebar-section-label">Website</p>

            <a href="{{ route('admin.website-settings') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.website-settings*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Website Settings' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Website Settings</span>
                @php $isLive = \App\Models\Setting::get('website_live', '1'); @endphp
                <span x-show="!sidebarCollapsed"
                      class="ml-auto w-2 h-2 rounded-full {{ ($isLive === '1' || $isLive === true) ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
            </a>
                <a href="{{ route('admin.payment-integration') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.payment-integration*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Payment Integration' : ''">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">Payment Integration</span>
            </a>
            <a href="{{ route('admin.whatsapp-integration') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.whatsapp-integration*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'WhatsApp Integration' : ''">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM11.999 2C6.477 2 2 6.477 2 12c0 1.99.574 3.842 1.563 5.408L2 22l4.703-1.545A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="text-sm">WhatsApp Integration</span>
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
        <div class="border-t border-white/10 p-3 shrink-0 bg-black/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/20">
                    <span class="text-slate-950 font-bold text-sm">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</span>
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0">
                    <p class="text-sm font-semibold text-slate-100 truncate">{{ auth()->user()?->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()?->email ?? 'admin@meharahouse.com' }}</p>
                </div>
                <form x-show="!sidebarCollapsed" method="POST" action="{{ route('auth.logout') }}" class="ml-auto shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-all" title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Sidebar Overlay (mobile only) --}}
    <div x-show="sidebarOpen && !isDesktop" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40" style="display:none;"></div>

    {{-- ════════════════════════ MAIN AREA ════════════════════════ --}}
    <div :style="isDesktop ? 'margin-left:' + sidebarWidth : 'margin-left:0px'"
         class="flex-1 min-w-0 flex flex-col transition-all duration-300">

        {{-- Top Header --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-slate-200 h-16 flex items-center px-4 md:px-6 gap-4 shadow-sm">
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
                @php
                    $newOrders      = \App\Models\Order::where('status', 'new')->count();
                    $pendingPayments = \App\Models\OrderPayment::where('status', 'pending')->count();
                    $lowStock       = \App\Models\Product::where('stock', '<=', 5)->where('stock', '>', 0)->count();
                    $outOfStock     = \App\Models\Product::where('stock', 0)->count();
                    $totalAlerts    = $newOrders + $pendingPayments + $lowStock + $outOfStock;
                    $recentOrders   = \App\Models\Order::where('status', 'new')->latest()->take(5)->get();
                @endphp
                <div x-data="{ open: false, cleared: false }" class="relative">
                    <button @click="open = !open"
                            class="relative p-2 rounded-lg text-[#475569] hover:bg-[#F1F5F9] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($totalAlerts > 0)
                        <span x-show="!cleared"
                              class="absolute top-1 right-1 min-w-[16px] h-4 px-0.5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                            {{ $totalAlerts > 99 ? '99+' : $totalAlerts }}
                        </span>
                        @endif
                    </button>

                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl border border-[#E2E8F0] shadow-xl z-50"
                         style="display:none;">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-[#F1F5F9]">
                            <p class="text-sm font-bold text-[#0F172A]">Notifications</p>
                            <div class="flex items-center gap-2">
                                @if($totalAlerts > 0)
                                <span x-show="!cleared" class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">{{ $totalAlerts }} alerts</span>
                                <button x-show="!cleared"
                                        @click="cleared = true; open = false"
                                        class="text-xs text-[#64748B] hover:text-red-500 font-medium transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Clear all
                                </button>
                                @else
                                <span class="text-xs text-[#94A3B8]">All clear</span>
                                @endif
                            </div>
                        </div>

                        {{-- Alert rows --}}
                        <div x-show="!cleared" class="divide-y divide-[#F1F5F9]">

                            @if($newOrders > 0)
                            <a href="{{ route('admin.orders') }}" @click="open = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-[#F8FAFC] transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $newOrders }} New {{ Str::plural('Order', $newOrders) }}</p>
                                    <p class="text-xs text-[#64748B]">Waiting for confirmation</p>
                                </div>
                                <span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">{{ $newOrders }}</span>
                            </a>
                            @endif

                            @if($pendingPayments > 0)
                            <a href="{{ route('admin.orders') }}" @click="open = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-[#F8FAFC] transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $pendingPayments }} Pending {{ Str::plural('Receipt', $pendingPayments) }}</p>
                                    <p class="text-xs text-[#64748B]">Payment receipts to review</p>
                                </div>
                                <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">{{ $pendingPayments }}</span>
                            </a>
                            @endif

                            @if($lowStock > 0)
                            <a href="{{ route('admin.products') }}" @click="open = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-[#F8FAFC] transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $lowStock }} Low Stock {{ Str::plural('Item', $lowStock) }}</p>
                                    <p class="text-xs text-[#64748B]">Stock ≤ 5 units remaining</p>
                                </div>
                                <span class="text-xs bg-orange-100 text-orange-700 font-bold px-2 py-0.5 rounded-full">{{ $lowStock }}</span>
                            </a>
                            @endif

                            @if($outOfStock > 0)
                            <a href="{{ route('admin.products') }}" @click="open = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-[#F8FAFC] transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $outOfStock }} Out of Stock</p>
                                    <p class="text-xs text-[#64748B]">Products with zero inventory</p>
                                </div>
                                <span class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">{{ $outOfStock }}</span>
                            </a>
                            @endif

                            @if($totalAlerts === 0)
                            <div class="px-4 py-6 text-center">
                                <svg class="w-8 h-8 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-medium text-[#64748B]">Everything looks good!</p>
                            </div>
                            @endif
                        </div>

                        {{-- Cleared state --}}
                        <div x-show="cleared" class="px-4 py-6 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-[#64748B]">All notifications cleared!</p>
                        </div>

                        {{-- Recent new orders --}}
                        @if($recentOrders->isNotEmpty())
                        <div x-show="!cleared" class="border-t border-[#F1F5F9] px-4 py-3">
                            <p class="text-[10px] uppercase tracking-widest font-semibold text-[#94A3B8] mb-2">Recent New Orders</p>
                            <div class="space-y-1.5">
                                @foreach($recentOrders as $ro)
                                <a href="{{ route('admin.orders') }}" @click="open = false"
                                   class="flex items-center justify-between text-xs hover:text-[#F59E0B] transition-colors">
                                    <span class="font-mono font-semibold text-[#0F172A]">{{ $ro->order_number }}</span>
                                    <span class="text-[#64748B]">Rs. {{ number_format($ro->total) }}</span>
                                    <span class="text-[#94A3B8]">{{ $ro->created_at->diffForHumans() }}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- Admin Avatar --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="w-9 h-9 rounded-full bg-[#0F172A] flex items-center justify-center overflow-hidden ring-2 ring-[#F59E0B]/40 hover:ring-[#F59E0B]/80 transition-all">
                        @if(auth()->check() && auth()->user()->profile_photo_path)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-[#F59E0B] font-bold text-sm">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl border border-[#E2E8F0] shadow-xl py-1 z-50" style="display:none;">
                        <div class="px-4 py-3 border-b border-[#F1F5F9] flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#0F172A] flex items-center justify-center overflow-hidden ring-2 ring-[#F59E0B]/40 shrink-0">
                                @if(auth()->check() && auth()->user()->profile_photo_path)
                                    <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-[#F59E0B] font-bold text-sm">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">{{ auth()->user()?->name ?? 'Admin' }}</p>
                                <p class="text-xs text-[#64748B] truncate">{{ auth()->user()?->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#F8FAFC] hover:text-[#0F172A]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
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
@stack('scripts')
</body>
</html>
