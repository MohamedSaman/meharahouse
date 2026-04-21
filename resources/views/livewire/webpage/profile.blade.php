{{-- resources/views/livewire/webpage/profile.blade.php --}}
<div>
    {{-- Page Hero --}}
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <div class="flex items-center gap-3">
                <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div>
                    <h1 class="font-[Poppins] font-bold text-3xl text-white">My Profile</h1>
                    <p class="text-[#64748B] mt-0.5 text-sm">Manage your account details and password</p>
                </div>
            </div>
        </div>
    </div>

    <section class="py-10 container-page max-w-2xl">

        {{-- Flash message --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="space-y-6">

            {{-- ── Section 1: Edit Profile ────────────────────────── --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-[#FFF9EE] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Edit Profile</h2>
                        <p class="text-xs text-[#64748B]">Update your name, email and phone number</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text"
                               class="form-input @error('name') border-red-400 @enderror"
                               placeholder="Your full name">
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input wire:model="email" type="email"
                               class="form-input @error('email') border-red-400 @enderror"
                               placeholder="you@example.com">
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Phone Number <span class="font-normal text-[#94A3B8]">(optional)</span></label>
                        <input wire:model="phone" type="tel"
                               class="form-input @error('phone') border-red-400 @enderror"
                               placeholder="+251 911 000 000">
                        @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button wire:click="saveProfile" wire:loading.attr="disabled"
                                class="btn-primary">
                            <svg wire:loading wire:target="saveProfile" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="saveProfile">Save Profile</span>
                            <span wire:loading wire:target="saveProfile">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Change Password ──────────────────────── --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-[#FFF9EE] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Change Password</h2>
                        <p class="text-xs text-[#64748B]">Choose a strong password with at least 8 characters</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Current Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Current Password <span class="text-red-500">*</span></label>
                        <input wire:model="currentPassword" type="password"
                               class="form-input @error('currentPassword') border-red-400 @enderror"
                               placeholder="Your current password"
                               autocomplete="current-password">
                        @error('currentPassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">New Password <span class="text-red-500">*</span></label>
                        <input wire:model="newPassword" type="password"
                               class="form-input @error('newPassword') border-red-400 @enderror"
                               placeholder="At least 8 characters"
                               autocomplete="new-password">
                        @error('newPassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1.5">Confirm New Password <span class="text-red-500">*</span></label>
                        <input wire:model="confirmPassword" type="password"
                               class="form-input @error('confirmPassword') border-red-400 @enderror"
                               placeholder="Repeat your new password"
                               autocomplete="new-password">
                        @error('confirmPassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button wire:click="changePassword" wire:loading.attr="disabled"
                                class="btn-primary">
                            <svg wire:loading wire:target="changePassword" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="changePassword">Change Password</span>
                            <span wire:loading wire:target="changePassword">Updating...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <a href="{{ route('webpage.orders') }}" class="flex items-center gap-1.5 text-[#64748B] hover:text-[#D4A017] transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Orders
                </a>
                <span class="text-[#E2E8F0]">|</span>
                <a href="{{ route('webpage.wishlist') }}" class="flex items-center gap-1.5 text-[#64748B] hover:text-[#D4A017] transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    My Wishlist
                </a>
            </div>

        </div>
    </section>
</div>
