{{-- resources/views/auth/two-factor-challenge.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Two-Factor Authentication — Meharahouse</title>
    <link rel="icon" type="image/png" href="{{ asset('images/meharahouse-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] min-h-screen flex items-center justify-center p-4" style="font-family: 'Inter', sans-serif;">

<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('webpage.home') }}" class="inline-flex flex-col items-center gap-2">
            <div class="w-16 h-16 rounded-2xl bg-[#0F172A] flex items-center justify-center shadow-xl">
                <img src="{{ asset('images/meharahouse-logo.png') }}" alt="Meharahouse" class="h-10 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span class="hidden text-[#F59E0B] font-bold text-xl font-[Poppins]">MH</span>
            </div>
            <span class="text-[#0F172A] font-bold text-xl font-[Poppins] tracking-tight">Meharahouse</span>
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-lg border border-[#E2E8F0] overflow-hidden" x-data="{ recovery: false }">

        {{-- Top accent bar --}}
        <div class="h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400"></div>

        <div class="px-8 py-8">

            {{-- Icon + Title --}}
            <div class="flex flex-col items-center mb-6">
                <div class="w-14 h-14 rounded-full bg-amber-50 border-2 border-amber-100 flex items-center justify-center mb-3 shadow-sm">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-[#0F172A] font-[Poppins]">Two-Factor Verification</h1>
                <p class="text-sm text-[#64748B] mt-1 text-center" x-show="!recovery">
                    Enter the code from your authenticator app to continue.
                </p>
                <p class="text-sm text-[#64748B] mt-1 text-center" x-cloak x-show="recovery">
                    Enter one of your emergency recovery codes to continue.
                </p>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li class="text-sm text-red-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                {{-- TOTP Code Input --}}
                <div x-show="!recovery">
                    <label for="code" class="block text-sm font-medium text-[#374151] mb-1.5">Authentication Code</label>
                    <input id="code"
                           type="text"
                           inputmode="numeric"
                           name="code"
                           autofocus
                           x-ref="code"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           class="w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-bold text-[#0F172A] border border-[#D1D5DB] rounded-xl focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none transition-all bg-[#F8FAFC] placeholder:text-[#CBD5E1] placeholder:text-base placeholder:tracking-normal">
                </div>

                {{-- Recovery Code Input --}}
                <div x-cloak x-show="recovery">
                    <label for="recovery_code" class="block text-sm font-medium text-[#374151] mb-1.5">Recovery Code</label>
                    <input id="recovery_code"
                           type="text"
                           name="recovery_code"
                           x-ref="recovery_code"
                           autocomplete="one-time-code"
                           placeholder="xxxxx-xxxxx"
                           class="w-full px-4 py-3 font-mono text-[#0F172A] border border-[#D1D5DB] rounded-xl focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none transition-all bg-[#F8FAFC]">
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="mt-5 w-full py-3 px-6 bg-[#0F172A] hover:bg-slate-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg font-[Poppins] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Verify & Sign In
                </button>
            </form>

            {{-- Toggle recovery / code --}}
            <div class="mt-4 text-center">
                <button type="button"
                        class="text-sm text-amber-600 hover:text-amber-700 font-medium underline underline-offset-2 transition-colors"
                        x-show="!recovery"
                        x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">
                    Use a recovery code instead
                </button>
                <button type="button"
                        class="text-sm text-amber-600 hover:text-amber-700 font-medium underline underline-offset-2 transition-colors"
                        x-cloak
                        x-show="recovery"
                        x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                    Use authenticator app instead
                </button>
            </div>
        </div>
    </div>

    {{-- Back to login --}}
    <p class="text-center mt-6 text-sm text-[#64748B]">
        Having trouble?
        <a href="{{ route('auth.login') }}" class="text-[#0F172A] font-semibold hover:text-amber-600 transition-colors">
            Return to login
        </a>
    </p>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
