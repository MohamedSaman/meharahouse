{{-- resources/views/livewire/auth/login.blade.php --}}
<div class="min-h-screen flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('webpage.home') }}" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 bg-[#0F172A] rounded-xl flex items-center justify-center">
                    <span class="text-[#F59E0B] font-black text-lg font-[Poppins]">MH</span>
                </div>
                <div class="text-left">
                    <span class="block font-black text-[#0F172A] text-xl leading-none font-[Poppins]">Meharahouse</span>
                    <span class="block text-[#F59E0B] text-xs font-bold tracking-widest uppercase">Quality You Can Trust</span>
                </div>
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-[#E2E8F0] p-8">
            <h2 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-1">Welcome Back</h2>
            <p class="text-[#64748B] text-sm mb-6">Sign in to your Meharahouse account.</p>

            {{-- Flash Messages --}}
            @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                {{ session('error') }}
            </div>
            @endif

            <form wire:submit="login" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Email Address</label>
                    <input
                        wire:model="email"
                        type="email"
                        placeholder="you@example.com"
                        class="form-input @error('email') border-red-400 bg-red-50 @enderror"
                        autocomplete="email"
                    >
                    @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Password</label>
                    <div x-data="{ show: false }" class="relative">
                        <input
                            wire:model="password"
                            :type="show ? 'text' : 'password'"
                            placeholder="Enter your password"
                            class="form-input pr-10 @error('password') border-red-400 bg-red-50 @enderror"
                            autocomplete="current-password"
                        >
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#94A3B8] hover:text-[#475569]">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded border-[#CBD5E1] text-[#F59E0B] focus:ring-[#F59E0B]">
                        <span class="text-sm text-[#475569]">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-[#F59E0B] font-semibold hover:text-[#D97706]">Forgot password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="btn-primary w-full justify-center py-3 text-base mt-2">
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading>Signing in...</span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-5">
                <div class="flex-1 h-px bg-[#E2E8F0]"></div>
                <span class="text-xs text-[#94A3B8]">New to Meharahouse?</span>
                <div class="flex-1 h-px bg-[#E2E8F0]"></div>
            </div>

            <a href="{{ route('auth.register') }}"
               class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-[#0F172A] text-[#0F172A] text-sm font-bold rounded-lg hover:bg-[#0F172A] hover:text-white transition-all duration-200">
                Create an Account
            </a>
        </div>

        <p class="text-center text-xs text-[#94A3B8] mt-6">
            &copy; {{ date('Y') }} Meharahouse. All rights reserved.
        </p>
    </div>
</div>
