{{-- resources/views/livewire/auth/register.blade.php --}}
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
            <h2 class="font-[Poppins] font-bold text-2xl text-[#0F172A] mb-1">Create Your Account</h2>
            <p class="text-[#64748B] text-sm mb-6">Join thousands of happy shoppers on Meharahouse.</p>

            <form wire:submit="register" class="space-y-4">
                @csrf

                {{-- Full Name --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Full Name</label>
                    <input wire:model="name" type="text" placeholder="Abebe Kebede"
                           class="form-input @error('name') border-red-400 bg-red-50 @enderror" autocomplete="name">
                    @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Email Address</label>
                    <input wire:model="email" type="email" placeholder="you@example.com"
                           class="form-input @error('email') border-red-400 bg-red-50 @enderror" autocomplete="email">
                    @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">
                        Phone Number <span class="font-normal text-[#94A3B8]">(optional)</span>
                    </label>
                    <input wire:model="phone" type="tel" placeholder="+251 911 000 000"
                           class="form-input" autocomplete="tel">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Password</label>
                    <div x-data="{ show: false }" class="relative">
                        <input wire:model="password" :type="show ? 'text' : 'password'"
                               placeholder="At least 8 characters"
                               class="form-input pr-10 @error('password') border-red-400 bg-red-50 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#94A3B8] hover:text-[#475569]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1.5">Confirm Password</label>
                    <input wire:model="password_confirmation" type="password"
                           placeholder="Repeat your password"
                           class="form-input">
                </div>

                {{-- Terms --}}
                <p class="text-xs text-[#64748B]">
                    By creating an account you agree to our
                    <a href="#" class="text-[#F59E0B] font-semibold hover:underline">Terms of Service</a> and
                    <a href="#" class="text-[#F59E0B] font-semibold hover:underline">Privacy Policy</a>.
                </p>

                {{-- Submit --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="btn-primary w-full justify-center py-3 text-base">
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove>Create Account</span>
                    <span wire:loading>Creating account...</span>
                </button>
            </form>

            <div class="flex items-center gap-3 my-5">
                <div class="flex-1 h-px bg-[#E2E8F0]"></div>
                <span class="text-xs text-[#94A3B8]">Already have an account?</span>
                <div class="flex-1 h-px bg-[#E2E8F0]"></div>
            </div>

            <a href="{{ route('auth.login') }}"
               class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-[#0F172A] text-[#0F172A] text-sm font-bold rounded-lg hover:bg-[#0F172A] hover:text-white transition-all duration-200">
                Sign In Instead
            </a>
        </div>
    </div>
</div>
