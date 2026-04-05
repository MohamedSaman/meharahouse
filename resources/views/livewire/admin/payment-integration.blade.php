{{-- resources/views/livewire/admin/payment-integration.blade.php --}}
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6">
        <div class="absolute -top-16 -right-12 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-emerald-400/15 blur-3xl"></div>
        <div class="relative">
            <p class="text-[11px] tracking-[0.16em] uppercase font-semibold text-amber-300 mb-1">Admin Settings</p>
            <h2 class="font-[Poppins] font-bold text-2xl text-dark">Payment Integration</h2>
            <p class="text-slate-400 text-sm mt-1">Enable payment gateways and enter your API credentials. Enabled gateways automatically appear at checkout.</p>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <strong>How it works:</strong> Toggle a gateway ON, enter your credentials, and click Save. That gateway will immediately appear as a payment option during customer checkout. Your API keys are stored securely in the database.
        </div>
    </div>

    {{-- ════════════════ GATEWAY CARDS ════════════════ --}}

    {{-- 1. Cash on Delivery --}}
    @php $isSaved = $savedGateway === 'cod'; @endphp
    <div class="card overflow-hidden border {{ $cod_enabled ? 'border-green-300 ring-1 ring-green-200' : 'border-slate-200' }} transition-all duration-200">
        <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Cash on Delivery</h3>
                    <p class="text-xs text-[#64748B]">Customer pays when the order arrives. No credentials needed.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($cod_enabled)
                <span class="badge badge-success text-xs">Active</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <div class="relative">
                        <input wire:model.live="cod_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-green-500 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
            </div>
        </div>
        <div class="px-5 pb-4 border-t border-slate-100 pt-4 flex items-center justify-between">
            <p class="text-xs text-slate-500">No API keys required — just toggle and save.</p>
            <button wire:click="saveCod" class="btn-primary btn-sm">
                <svg wire:loading wire:target="saveCod" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span wire:loading.remove wire:target="saveCod">{{ $isSaved ? '✓ Saved' : 'Save' }}</span>
                <span wire:loading wire:target="saveCod">Saving...</span>
            </button>
        </div>
    </div>

    {{-- 2. TeleBirr --}}
    @php $isSaved = $savedGateway === 'telebirr'; @endphp
    <div class="card overflow-hidden border {{ $telebirr_enabled ? 'border-purple-300 ring-1 ring-purple-200' : 'border-slate-200' }} transition-all duration-200">
        {{-- Header --}}
        <div class="flex items-center justify-between p-5 cursor-pointer" wire:click="toggleGateway('telebirr')">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center shrink-0 text-xl font-bold text-purple-700">T</div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-[Poppins] font-bold text-[#0F172A]">TeleBirr</h3>
                        <span class="text-[10px] bg-purple-100 text-purple-700 font-semibold px-2 py-0.5 rounded-full">Ethiopia</span>
                    </div>
                    <p class="text-xs text-[#64748B]">Ethio Telecom's mobile money payment service.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($telebirr_enabled && $telebirr_app_id)
                <span class="badge badge-success text-xs">Active</span>
                @elseif($telebirr_enabled)
                <span class="badge badge-gold text-xs">Credentials needed</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none" @click.stop>
                    <div class="relative">
                        <input wire:model.live="telebirr_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-purple-500 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $activeGateway === 'telebirr' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        {{-- Expandable Fields --}}
        @if($activeGateway === 'telebirr' || $telebirr_enabled)
        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">App ID <span class="text-red-500">*</span></label>
                    <input wire:model="telebirr_app_id" type="text" class="form-input text-sm @error('telebirr_app_id') border-red-400 @enderror" placeholder="Your TeleBirr App ID">
                    @error('telebirr_app_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">App Key <span class="text-red-500">*</span></label>
                    <input wire:model="telebirr_app_key" type="password" class="form-input text-sm @error('telebirr_app_key') border-red-400 @enderror" placeholder="••••••••••••••••">
                    @error('telebirr_app_key')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Short Code <span class="text-red-500">*</span></label>
                    <input wire:model="telebirr_short_code" type="text" class="form-input text-sm @error('telebirr_short_code') border-red-400 @enderror" placeholder="e.g. 1000">
                    @error('telebirr_short_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Public Key <span class="text-xs text-slate-400 font-normal">(optional)</span></label>
                    <input wire:model="telebirr_public_key" type="text" class="form-input text-sm" placeholder="RSA public key if required">
                </div>
            </div>
            <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg text-xs text-purple-700">
                Get your credentials from the <strong>TeleBirr Merchant Portal</strong> at <span class="font-mono">merchant.ethiotelecom.et</span>
            </div>
            <div class="flex justify-end">
                <button wire:click="saveTelebirr" class="btn-primary btn-sm bg-purple-600 hover:bg-purple-700">
                    <svg wire:loading wire:target="saveTelebirr" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="saveTelebirr">{{ $isSaved ? '✓ Saved!' : 'Save TeleBirr' }}</span>
                    <span wire:loading wire:target="saveTelebirr">Saving...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- 3. CBE Birr --}}
    @php $isSaved = $savedGateway === 'cbebirr'; @endphp
    <div class="card overflow-hidden border {{ $cbebirr_enabled ? 'border-blue-300 ring-1 ring-blue-200' : 'border-slate-200' }} transition-all duration-200">
        <div class="flex items-center justify-between p-5 cursor-pointer" wire:click="toggleGateway('cbebirr')">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 text-xl font-bold text-blue-700">C</div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-[Poppins] font-bold text-[#0F172A]">CBE Birr</h3>
                        <span class="text-[10px] bg-blue-100 text-blue-700 font-semibold px-2 py-0.5 rounded-full">Ethiopia</span>
                    </div>
                    <p class="text-xs text-[#64748B]">Commercial Bank of Ethiopia mobile banking payment.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($cbebirr_enabled && $cbebirr_api_key)
                <span class="badge badge-success text-xs">Active</span>
                @elseif($cbebirr_enabled)
                <span class="badge badge-gold text-xs">Credentials needed</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none" @click.stop>
                    <div class="relative">
                        <input wire:model.live="cbebirr_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-blue-500 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $activeGateway === 'cbebirr' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @if($activeGateway === 'cbebirr' || $cbebirr_enabled)
        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">API Key <span class="text-red-500">*</span></label>
                    <input wire:model="cbebirr_api_key" type="password" class="form-input text-sm @error('cbebirr_api_key') border-red-400 @enderror" placeholder="••••••••••••••••">
                    @error('cbebirr_api_key')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Merchant ID <span class="text-red-500">*</span></label>
                    <input wire:model="cbebirr_merchant_id" type="text" class="form-input text-sm @error('cbebirr_merchant_id') border-red-400 @enderror" placeholder="Your CBE Merchant ID">
                    @error('cbebirr_merchant_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Secret Key <span class="text-red-500">*</span></label>
                    <input wire:model="cbebirr_secret" type="password" class="form-input text-sm @error('cbebirr_secret') border-red-400 @enderror" placeholder="••••••••••••••••">
                    @error('cbebirr_secret')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                Contact <strong>CBE Digital Banking</strong> or visit <span class="font-mono">combanketh.et</span> to register as a merchant and obtain API credentials.
            </div>
            <div class="flex justify-end">
                <button wire:click="saveCbebirr" class="btn-primary btn-sm bg-blue-600 hover:bg-blue-700">
                    <svg wire:loading wire:target="saveCbebirr" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="saveCbebirr">{{ $isSaved ? '✓ Saved!' : 'Save CBE Birr' }}</span>
                    <span wire:loading wire:target="saveCbebirr">Saving...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- 4. PayPal --}}
    @php $isSaved = $savedGateway === 'paypal'; @endphp
    <div class="card overflow-hidden border {{ $paypal_enabled ? 'border-[#0070ba]/40 ring-1 ring-[#0070ba]/20' : 'border-slate-200' }} transition-all duration-200">
        <div class="flex items-center justify-between p-5 cursor-pointer" wire:click="toggleGateway('paypal')">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#EFF6FB] flex items-center justify-center shrink-0">
                    <span class="font-bold text-lg text-[#003087]">P</span><span class="font-bold text-lg text-[#009cde]">P</span>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">PayPal</h3>
                    <p class="text-xs text-[#64748B]">Accept international payments via PayPal. Supports sandbox & live modes.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($paypal_enabled && $paypal_client_id)
                <span class="badge badge-success text-xs">Active — {{ strtoupper($paypal_mode) }}</span>
                @elseif($paypal_enabled)
                <span class="badge badge-gold text-xs">Credentials needed</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none" @click.stop>
                    <div class="relative">
                        <input wire:model.live="paypal_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-[#0070ba] rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $activeGateway === 'paypal' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @if($activeGateway === 'paypal' || $paypal_enabled)
        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/50">
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Mode</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="paypal_mode" type="radio" value="sandbox" class="w-4 h-4 text-[#0070ba]">
                        <span class="text-sm font-medium text-slate-700">Sandbox <span class="text-xs text-slate-400">(testing)</span></span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="paypal_mode" type="radio" value="live" class="w-4 h-4 text-[#0070ba]">
                        <span class="text-sm font-medium text-slate-700">Live <span class="text-xs text-slate-400">(production)</span></span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Client ID <span class="text-red-500">*</span></label>
                <input wire:model="paypal_client_id" type="text" class="form-input text-sm font-mono @error('paypal_client_id') border-red-400 @enderror" placeholder="AaBbCcDd...">
                @error('paypal_client_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Client Secret <span class="text-red-500">*</span></label>
                <input wire:model="paypal_client_secret" type="password" class="form-input text-sm @error('paypal_client_secret') border-red-400 @enderror" placeholder="••••••••••••••••">
                @error('paypal_client_secret')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                Get credentials from <strong>developer.paypal.com</strong> → My Apps &amp; Credentials → Create App.
            </div>
            <div class="flex justify-end">
                <button wire:click="savePaypal" class="btn-primary btn-sm bg-[#0070ba] hover:bg-[#005ea6]">
                    <svg wire:loading wire:target="savePaypal" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="savePaypal">{{ $isSaved ? '✓ Saved!' : 'Save PayPal' }}</span>
                    <span wire:loading wire:target="savePaypal">Saving...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- 5. Stripe --}}
    @php $isSaved = $savedGateway === 'stripe'; @endphp
    <div class="card overflow-hidden border {{ $stripe_enabled ? 'border-indigo-300 ring-1 ring-indigo-200' : 'border-slate-200' }} transition-all duration-200">
        <div class="flex items-center justify-between p-5 cursor-pointer" wire:click="toggleGateway('stripe')">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
                    <span class="font-bold text-white text-xl">S</span>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Stripe</h3>
                    <p class="text-xs text-[#64748B]">Accept cards, Apple Pay, Google Pay globally via Stripe.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($stripe_enabled && $stripe_publishable_key)
                <span class="badge badge-success text-xs">Active</span>
                @elseif($stripe_enabled)
                <span class="badge badge-gold text-xs">Credentials needed</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none" @click.stop>
                    <div class="relative">
                        <input wire:model.live="stripe_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $activeGateway === 'stripe' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @if($activeGateway === 'stripe' || $stripe_enabled)
        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/50">
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Publishable Key <span class="text-red-500">*</span> <span class="text-[10px] text-slate-400 font-normal">starts with pk_</span></label>
                <input wire:model="stripe_publishable_key" type="text" class="form-input text-sm font-mono @error('stripe_publishable_key') border-red-400 @enderror" placeholder="pk_test_... or pk_live_...">
                @error('stripe_publishable_key')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Secret Key <span class="text-red-500">*</span> <span class="text-[10px] text-slate-400 font-normal">starts with sk_</span></label>
                <input wire:model="stripe_secret_key" type="password" class="form-input text-sm @error('stripe_secret_key') border-red-400 @enderror" placeholder="sk_test_... or sk_live_...">
                @error('stripe_secret_key')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-xs text-indigo-700">
                Get API keys from <strong>dashboard.stripe.com</strong> → Developers → API Keys.
                Use <span class="font-mono">pk_test_</span> / <span class="font-mono">sk_test_</span> for testing, and <span class="font-mono">pk_live_</span> / <span class="font-mono">sk_live_</span> for production.
            </div>
            <div class="flex justify-end">
                <button wire:click="saveStripe" class="btn-primary btn-sm bg-indigo-600 hover:bg-indigo-700">
                    <svg wire:loading wire:target="saveStripe" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="saveStripe">{{ $isSaved ? '✓ Saved!' : 'Save Stripe' }}</span>
                    <span wire:loading wire:target="saveStripe">Saving...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- 6. PayHere --}}
    @php $isSaved = $savedGateway === 'payhere'; @endphp
    <div class="card overflow-hidden border {{ $payhere_enabled ? 'border-emerald-300 ring-1 ring-emerald-200' : 'border-slate-200' }} transition-all duration-200">
        <div class="flex items-center justify-between p-5 cursor-pointer" wire:click="toggleGateway('payhere')">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <span class="font-bold text-emerald-700 text-sm">PH</span>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">PayHere</h3>
                    <p class="text-xs text-[#64748B]">Popular payment gateway. Supports cards &amp; mobile payments.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($payhere_enabled && $payhere_merchant_id)
                <span class="badge badge-success text-xs">Active — {{ strtoupper($payhere_mode) }}</span>
                @elseif($payhere_enabled)
                <span class="badge badge-gold text-xs">Credentials needed</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none" @click.stop>
                    <div class="relative">
                        <input wire:model.live="payhere_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-emerald-500 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $activeGateway === 'payhere' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @if($activeGateway === 'payhere' || $payhere_enabled)
        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/50">
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Mode</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="payhere_mode" type="radio" value="sandbox" class="w-4 h-4 text-emerald-500">
                        <span class="text-sm font-medium text-slate-700">Sandbox <span class="text-xs text-slate-400">(testing)</span></span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="payhere_mode" type="radio" value="live" class="w-4 h-4 text-emerald-500">
                        <span class="text-sm font-medium text-slate-700">Live <span class="text-xs text-slate-400">(production)</span></span>
                    </label>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Merchant ID <span class="text-red-500">*</span></label>
                    <input wire:model="payhere_merchant_id" type="text" class="form-input text-sm @error('payhere_merchant_id') border-red-400 @enderror" placeholder="Your Merchant ID">
                    @error('payhere_merchant_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Merchant Secret <span class="text-red-500">*</span></label>
                    <input wire:model="payhere_secret" type="password" class="form-input text-sm @error('payhere_secret') border-red-400 @enderror" placeholder="••••••••••••••••">
                    @error('payhere_secret')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-700">
                Log into <strong>payhere.lk</strong> → Settings → Domains &amp; Credentials to find your Merchant ID and Secret.
            </div>
            <div class="flex justify-end">
                <button wire:click="savePayhere" class="btn-primary btn-sm bg-emerald-600 hover:bg-emerald-700">
                    <svg wire:loading wire:target="savePayhere" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="savePayhere">{{ $isSaved ? '✓ Saved!' : 'Save PayHere' }}</span>
                    <span wire:loading wire:target="savePayhere">Saving...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- 7. Bank Transfer --}}
    @php $isSaved = $savedGateway === 'bank'; @endphp
    <div class="card overflow-hidden border {{ $bank_enabled ? 'border-amber-300 ring-1 ring-amber-200' : 'border-slate-200' }} transition-all duration-200">
        <div class="flex items-center justify-between p-5 cursor-pointer" wire:click="toggleGateway('bank')">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">Bank Transfer</h3>
                    <p class="text-xs text-[#64748B]">Customer transfers to your bank account manually. You confirm after payment received.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($bank_enabled && $bank_account_number)
                <span class="badge badge-success text-xs">Active</span>
                @elseif($bank_enabled)
                <span class="badge badge-gold text-xs">Account details needed</span>
                @endif
                <label class="flex items-center gap-2 cursor-pointer select-none" @click.stop>
                    <div class="relative">
                        <input wire:model.live="bank_enabled" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-checked:bg-amber-500 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                </label>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $activeGateway === 'bank' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @if($activeGateway === 'bank' || $bank_enabled)
        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Account Holder Name <span class="text-red-500">*</span></label>
                    <input wire:model="bank_account_name" type="text" class="form-input text-sm @error('bank_account_name') border-red-400 @enderror" placeholder="e.g. Mehra House PLC">
                    @error('bank_account_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Account Number <span class="text-red-500">*</span></label>
                    <input wire:model="bank_account_number" type="text" class="form-input text-sm @error('bank_account_number') border-red-400 @enderror" placeholder="e.g. 1000123456789">
                    @error('bank_account_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Bank Name <span class="text-red-500">*</span></label>
                    <input wire:model="bank_name" type="text" class="form-input text-sm @error('bank_name') border-red-400 @enderror" placeholder="e.g. Commercial Bank of Ethiopia">
                    @error('bank_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Branch <span class="text-xs text-slate-400 font-normal">(optional)</span></label>
                    <input wire:model="bank_branch" type="text" class="form-input text-sm" placeholder="e.g. Bole Branch">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Transfer Instructions <span class="text-xs text-slate-400 font-normal">(shown to customer)</span></label>
                    <textarea wire:model="bank_instructions" rows="2" class="form-input text-sm resize-none" placeholder="e.g. Transfer the exact order total and send your receipt screenshot to our WhatsApp: +251 911 000 000"></textarea>
                </div>
            </div>
            <div class="flex justify-end">
                <button wire:click="saveBank" class="btn-primary btn-sm bg-amber-600 hover:bg-amber-700">
                    <svg wire:loading wire:target="saveBank" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="saveBank">{{ $isSaved ? '✓ Saved!' : 'Save Bank Transfer' }}</span>
                    <span wire:loading wire:target="saveBank">Saving...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- Active Summary --}}
    <div class="card p-5 border border-slate-200">
        <h3 class="font-[Poppins] font-semibold text-sm text-[#0F172A] mb-3">Active Payment Methods at Checkout</h3>
        <div class="flex flex-wrap gap-2">
            @if($cod_enabled)      <span class="badge badge-success">Cash on Delivery</span> @endif
            @if($telebirr_enabled && $telebirr_app_id) <span class="badge badge-success">TeleBirr</span> @endif
            @if($cbebirr_enabled && $cbebirr_api_key)  <span class="badge badge-success">CBE Birr</span> @endif
            @if($paypal_enabled && $paypal_client_id)  <span class="badge badge-success">PayPal</span> @endif
            @if($stripe_enabled && $stripe_publishable_key) <span class="badge badge-success">Stripe</span> @endif
            @if($payhere_enabled && $payhere_merchant_id)   <span class="badge badge-success">PayHere</span> @endif
            @if($bank_enabled && $bank_account_number)  <span class="badge badge-success">Bank Transfer</span> @endif
            @if(!$cod_enabled && !($telebirr_enabled && $telebirr_app_id) && !($cbebirr_enabled && $cbebirr_api_key) && !($paypal_enabled && $paypal_client_id) && !($stripe_enabled && $stripe_publishable_key) && !($payhere_enabled && $payhere_merchant_id) && !($bank_enabled && $bank_account_number))
            <span class="text-sm text-slate-400 italic">No payment methods active — enable at least one above.</span>
            @endif
        </div>
    </div>

</div>
