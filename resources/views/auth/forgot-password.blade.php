<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Meharahouse</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-center font-bold text-xl text-[#0F172A] mb-2">Forgot your password?</h2>
            <p class="text-center text-sm text-[#64748B] mb-6">Enter your email and we'll send you a reset link.</p>
            @session('status')
            <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $value }}
            </div>
            @endsession
            @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-sm focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all"
                           placeholder="your@email.com">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-[#D4A017] hover:bg-[#B8860B] text-[#0F172A] font-bold text-sm transition-colors">
                    Send Reset Link
                </button>
            </form>
            <p class="text-center text-sm text-[#64748B] mt-6">
                Remember your password? <a href="{{ route('auth.login') }}" class="font-semibold text-[#D4A017] hover:text-[#B8860B]">Sign in</a>
            </p>
        </div>
        <p class="text-center text-xs text-[#475569] mt-6">© {{ date('Y') }} Meharahouse. All rights reserved.</p>
    </div>
</body>
</html>
