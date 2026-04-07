<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Meharahouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0F172A] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('webpage.home') }}">
                <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Meharahouse" class="h-16 mx-auto object-contain">
            </a>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-[#D4A017]/10 mx-auto mb-5">
                <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="text-center font-bold text-xl text-[#0F172A] mb-2">Set new password</h2>
            <p class="text-center text-sm text-[#64748B] mb-6">Choose a strong new password for your account.</p>
            @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all"
                           placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">New Password</label>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all"
                           placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all"
                           placeholder="••••••••">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-[#D4A017] hover:bg-[#B8860B] text-[#0F172A] font-bold text-sm transition-colors">
                    Reset Password
                </button>
            </form>
            <p class="text-center text-sm text-[#64748B] mt-6">
                <a href="{{ route('auth.login') }}" class="font-semibold text-[#D4A017] hover:text-[#B8860B]">← Back to Sign In</a>
            </p>
        </div>
        <p class="text-center text-xs text-[#475569] mt-6">© {{ date('Y') }} Meharahouse. All rights reserved.</p>
    </div>
</body>
</html>
