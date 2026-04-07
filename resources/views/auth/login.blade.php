<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Meharahouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0F172A] flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('webpage.home') }}">
                <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Meharahouse" class="h-16 mx-auto object-contain">
            </a>
            <p class="text-[#64748B] text-sm mt-3">Sign in to your account</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            {{-- Errors --}}
            @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            @session('status')
            <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">
                {{ $value }}
            </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-sm text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all @error('email') border-red-400 bg-red-50 @enderror"
                           placeholder="your@email.com">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-[#0F172A] mb-1.5">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-sm text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017] transition-all @error('password') border-red-400 bg-red-50 @enderror"
                           placeholder="••••••••">
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E2E8F0] text-[#D4A017] focus:ring-[#D4A017]">
                        <span class="text-sm text-[#64748B]">Remember me</span>
                    </label>
                    <a href="/forgot-password" class="text-sm font-medium text-[#D4A017] hover:text-[#B8860B] transition-colors">
                        Forgot password?
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-[#D4A017] hover:bg-[#B8860B] text-[#0F172A] font-bold text-sm transition-colors shadow-sm">
                    Sign In
                </button>
            </form>

            {{-- Register link --}}
            @if (Route::has('auth.register'))
            <p class="text-center text-sm text-[#64748B] mt-6">
                Don't have an account?
                <a href="{{ route('auth.register') }}" class="font-semibold text-[#D4A017] hover:text-[#B8860B] transition-colors">Create one</a>
            </p>
            @endif

        </div>

        <p class="text-center text-xs text-[#475569] mt-6">
            © {{ date('Y') }} Meharahouse. All rights reserved.
        </p>
    </div>

</body>
</html>
