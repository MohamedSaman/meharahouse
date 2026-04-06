{{-- resources/views/livewire/admin/profile.blade.php --}}
@section('page_title', 'My Profile')
@section('page_subtitle', 'Manage your account information and security settings')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ── Profile Header Banner ──────────────────────────────────────── --}}
    <div class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-white/10 shadow-xl">
        {{-- Decorative amber accent bar --}}
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400"></div>

        <div class="px-6 py-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            {{-- Avatar --}}
            <div class="relative shrink-0">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ auth()->user()->profile_photo_url }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-24 h-24 rounded-full object-cover ring-4 ring-amber-400/40 shadow-xl">
                @else
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center ring-4 ring-amber-400/40 shadow-xl">
                        <span class="text-3xl font-bold text-slate-900">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                @endif
                <span class="absolute bottom-1 right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-slate-800"></span>
            </div>

            {{-- Info --}}
            <div class="text-center sm:text-left">
                <h2 class="text-2xl font-bold text-white font-[Poppins]">{{ auth()->user()->name }}</h2>
                <p class="text-slate-400 text-sm mt-0.5">{{ auth()->user()->email }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-3 justify-center sm:justify-start">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                        {{ auth()->user()->isAdmin() ? 'bg-amber-400/20 text-amber-400 border border-amber-400/30' : 'bg-teal-400/20 text-teal-400 border border-teal-400/30' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->isAdmin() ? 'bg-amber-400' : 'bg-teal-400' }}"></span>
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                    @if(auth()->user()->two_factor_confirmed_at)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-green-400/20 text-green-400 border border-green-400/30">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        2FA Active
                    </span>
                    @endif
                    @if(auth()->user()->phone)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-700/60 text-slate-300 border border-slate-600/40">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ auth()->user()->phone }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="sm:ml-auto flex gap-6 text-center">
                <div>
                    <p class="text-2xl font-bold text-white">{{ auth()->user()->orders()->count() }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Orders</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Member since</p>
                    <p class="text-sm font-semibold text-slate-200 mt-0.5">{{ auth()->user()->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Two-Column: Profile Info + Password ────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Update Profile Information --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Profile Information</h3>
                    <p class="text-xs text-slate-500">Update your name, email, and photo</p>
                </div>
            </div>
            <div class="p-6">
                @livewire('profile.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Update Password</h3>
                    <p class="text-xs text-slate-500">Use a long, random password for security</p>
                </div>
            </div>
            <div class="p-6">
                @livewire('profile.update-password-form')
            </div>
        </div>
    </div>

    {{-- ── Two-Factor Authentication ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Two-Factor Authentication</h3>
                <p class="text-xs text-slate-500">Add extra security with a TOTP authenticator app</p>
            </div>
            @if(auth()->user()->two_factor_confirmed_at)
            <span class="ml-auto px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Enabled</span>
            @else
            <span class="ml-auto px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500">Disabled</span>
            @endif
        </div>
        <div class="p-6">
            @livewire('profile.two-factor-authentication-form')
        </div>
    </div>

    {{-- ── Browser Sessions ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Browser Sessions</h3>
                <p class="text-xs text-slate-500">Manage and sign out active sessions on other devices</p>
            </div>
        </div>
        <div class="p-6">
            @livewire('profile.logout-other-browser-sessions-form')
        </div>
    </div>

    {{-- ── Delete Account ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-red-700">Delete Account</h3>
                <p class="text-xs text-red-400">Permanently remove your account and all data</p>
            </div>
        </div>
        <div class="p-6">
            @livewire('profile.delete-user-form')
        </div>
    </div>

</div>
