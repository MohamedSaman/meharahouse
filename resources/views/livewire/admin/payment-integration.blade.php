{{-- resources/views/livewire/admin/payment-integration.blade.php --}}
<div class="space-y-5">

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6">
        <div class="absolute -top-16 -right-12 h-44 w-44 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="relative">
            <p class="text-[11px] tracking-[0.16em] uppercase font-semibold text-amber-300 mb-1">Admin → Settings</p>
            <h2 class="font-[Poppins] font-bold text-2xl text-white">Payment Integration</h2>
            <p class="text-slate-400 text-sm mt-1">Enable a gateway, fill in your credentials, click Save — it instantly appears at checkout.</p>
        </div>
    </div>

    {{-- Info banner --}}
    <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span><strong>Toggle ON</strong> → fill the required fields → <strong>Save</strong>. Credentials are stored securely and the gateway appears for customers automatically.</span>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         1. CASH ON DELIVERY
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($cod_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        {{-- Card header --}}
        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#f0fdf4">
                    <svg class="w-5 h-5" style="color:#22c55e" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">Cash on Delivery</p>
                    <p class="text-xs text-slate-400">No credentials needed — simply enable to allow COD at checkout.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- COD toggle --}}
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('cod_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.cod_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.cod_enabled ? '#22c55e' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.cod_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.cod_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Expandable body --}}
        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-600">Cash on Delivery requires no API credentials. Customers pay when the order is delivered.</p>
                <button type="button" wire:click="saveCod"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#22c55e">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save
                </button>
            </div>
            @if($savedGateway === 'cod')
                <p class="mt-3 text-xs text-green-600 font-medium">COD setting saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. TELEBIRR
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($telebirr_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#faf5ff">
                    <svg class="w-5 h-5" style="color:#a855f7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">TeleBirr</p>
                    <p class="text-xs text-slate-400">Ethio Telecom mobile payment — widely used in Ethiopia.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('telebirr_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.telebirr_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.telebirr_enabled ? '#a855f7' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.telebirr_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.telebirr_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- App ID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">App ID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="telebirr_app_id"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                           placeholder="e.g. YOUR_APP_ID">
                    @error('telebirr_app_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- App Key --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">App Key <span class="text-red-400">*</span></label>
                    <input type="password" wire:model="telebirr_app_key"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                           placeholder="Your TeleBirr App Key">
                    @error('telebirr_app_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Short Code --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Short Code <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="telebirr_short_code"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                           placeholder="e.g. 1000">
                    @error('telebirr_short_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Public Key --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Public Key <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="text" wire:model="telebirr_public_key"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                           placeholder="RSA Public Key for response verification">
                    @error('telebirr_public_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Credentials provided by Ethio Telecom TeleBirr merchant portal.</p>
                <button type="button" wire:click="saveTelebirr"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#a855f7">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save TeleBirr
                </button>
            </div>
            @if($savedGateway === 'telebirr')
                <p class="text-xs text-green-600 font-medium">TeleBirr settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         3. CBE BIRR
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($cbebirr_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#eff6ff">
                    <svg class="w-5 h-5" style="color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">CBE Birr</p>
                    <p class="text-xs text-slate-400">Commercial Bank of Ethiopia mobile payment gateway.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('cbebirr_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.cbebirr_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.cbebirr_enabled ? '#3b82f6' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.cbebirr_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.cbebirr_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- API Key --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">API Key <span class="text-red-400">*</span></label>
                    <input type="password" wire:model="cbebirr_api_key"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="CBE Birr API Key">
                    @error('cbebirr_api_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Merchant ID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Merchant ID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="cbebirr_merchant_id"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="Your CBE Merchant ID">
                    @error('cbebirr_merchant_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Secret --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Secret <span class="text-red-400">*</span></label>
                    <input type="password" wire:model="cbebirr_secret"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="CBE Birr Secret">
                    @error('cbebirr_secret') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Credentials provided by CBE merchant onboarding team.</p>
                <button type="button" wire:click="saveCbebirr"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#3b82f6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save CBE Birr
                </button>
            </div>
            @if($savedGateway === 'cbebirr')
                <p class="text-xs text-green-600 font-medium">CBE Birr settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         4. PAYPAL
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($paypal_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#e8f4fd">
                    <svg class="w-5 h-5" style="color:#0070ba" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.941.895A.78.78 0 015.68.287h7.828c2.6 0 4.512.59 5.569 1.657 1.018.99 1.393 2.367 1.108 4.1l-.003.02c-.52 3.07-2.304 5.025-5.308 5.8l-.134.033c.618.8.861 1.847.715 3.097l-.003.022C15.058 18.7 13.16 21.337 7.076 21.337zm.796-14.43a.284.284 0 00-.28.238l-1.286 8.14a.164.164 0 00.162.19h2.232c.13 0 .24-.094.26-.223l.39-2.467c.15-.952.743-1.48 1.73-1.48h.675c2.093 0 3.718-.996 4.196-3.073.19-.825.073-1.505-.337-1.97-.44-.5-1.25-.758-2.386-.758H7.872z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">PayPal</p>
                    <p class="text-xs text-slate-400">Accept PayPal and credit/debit cards globally.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('paypal_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.paypal_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.paypal_enabled ? '#0070ba' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.paypal_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.paypal_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Client ID --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Client ID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="paypal_client_id"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           style="--tw-ring-color:#0070ba"
                           placeholder="AXxxx...">
                    @error('paypal_client_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Client Secret --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Client Secret <span class="text-red-400">*</span></label>
                    <input type="password" wire:model="paypal_client_secret"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           placeholder="Your PayPal Client Secret">
                    @error('paypal_client_secret') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Mode --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mode</label>
                    <select wire:model="paypal_mode"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent">
                        <option value="sandbox">Sandbox (Testing)</option>
                        <option value="live">Live (Production)</option>
                    </select>
                    @error('paypal_mode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Credentials from <strong>developer.paypal.com</strong> → My Apps &amp; Credentials.</p>
                <button type="button" wire:click="savePaypal"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#0070ba">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save PayPal
                </button>
            </div>
            @if($savedGateway === 'paypal')
                <p class="text-xs text-green-600 font-medium">PayPal settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         5. STRIPE
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($stripe_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#eef2ff">
                    <svg class="w-5 h-5" style="color:#4f46e5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">Stripe</p>
                    <p class="text-xs text-slate-400">Accept cards, Apple Pay, Google Pay, and more via Stripe.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('stripe_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.stripe_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.stripe_enabled ? '#4f46e5' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.stripe_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.stripe_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Publishable Key --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Publishable Key <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="stripe_publishable_key"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                           placeholder="pk_live_... or pk_test_...">
                    @error('stripe_publishable_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Secret Key --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Secret Key <span class="text-red-400">*</span></label>
                    <input type="password" wire:model="stripe_secret_key"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                           placeholder="sk_live_... or sk_test_...">
                    @error('stripe_secret_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Keys from <strong>dashboard.stripe.com</strong> → Developers → API Keys. Use <code class="bg-slate-100 px-1 rounded">pk_test_</code> / <code class="bg-slate-100 px-1 rounded">sk_test_</code> for testing.</p>
                <button type="button" wire:click="saveStripe"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#4f46e5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Stripe
                </button>
            </div>
            @if($savedGateway === 'stripe')
                <p class="text-xs text-green-600 font-medium">Stripe settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         6. PAYHERE
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($payhere_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#ecfdf5">
                    <svg class="w-5 h-5" style="color:#059669" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">PayHere</p>
                    <p class="text-xs text-slate-400">Sri Lanka's leading payment gateway for cards and local methods.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('payhere_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.payhere_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.payhere_enabled ? '#059669' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.payhere_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.payhere_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Merchant ID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Merchant ID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="payhere_merchant_id"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent"
                           placeholder="Your PayHere Merchant ID">
                    @error('payhere_merchant_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Secret --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Merchant Secret <span class="text-red-400">*</span></label>
                    <input type="password" wire:model="payhere_secret"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent"
                           placeholder="Your PayHere Merchant Secret">
                    @error('payhere_secret') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Mode --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mode</label>
                    <select wire:model="payhere_mode"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                        <option value="sandbox">Sandbox (Testing)</option>
                        <option value="live">Live (Production)</option>
                    </select>
                    @error('payhere_mode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Credentials from <strong>payhere.lk</strong> → Business Portal → Integrations.</p>
                <button type="button" wire:click="savePayhere"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#059669">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save PayHere
                </button>
            </div>
            @if($savedGateway === 'payhere')
                <p class="text-xs text-green-600 font-medium">PayHere settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         7. BANK TRANSFER
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($bank_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#fffbeb">
                    <svg class="w-5 h-5" style="color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">Bank Transfer</p>
                    <p class="text-xs text-slate-400">Manual bank transfer — customers pay directly to your account.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('bank_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.bank_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.bank_enabled ? '#d97706' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.bank_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.bank_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Account Name --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Account Name <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="bank_account_name"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                           placeholder="e.g. Meharahouse PLC">
                    @error('bank_account_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Account Number --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Account Number <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="bank_account_number"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                           placeholder="e.g. 1000123456789">
                    @error('bank_account_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Bank Name --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bank Name <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="bank_name"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                           placeholder="e.g. Commercial Bank of Ethiopia">
                    @error('bank_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Branch --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Branch <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="text" wire:model="bank_branch"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                           placeholder="e.g. Bole Branch">
                    @error('bank_branch') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Advance Payment Percentage --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Advance Payment % <span class="text-red-400">*</span></label>
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="bank_advance_pct" min="10" max="90"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                               placeholder="50">
                        <span class="text-sm font-bold text-slate-500 shrink-0">%</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Customers can pay this % as advance and the rest on delivery (10–90%)</p>
                    @error('bank_advance_pct') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Instructions --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Payment Instructions <span class="text-slate-400 font-normal">(shown to customer at checkout)</span></label>
                    <textarea wire:model="bank_instructions" rows="3"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent resize-none"
                              placeholder="e.g. Please transfer the exact order amount and use your Order ID as the payment reference. Send proof of payment to orders@meharahouse.com."></textarea>
                    @error('bank_instructions') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">These details are displayed to the customer after placing a bank transfer order.</p>
                <button type="button" wire:click="saveBank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background:#d97706">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Bank Transfer
                </button>
            </div>
            @if($savedGateway === 'bank')
                <p class="text-xs text-green-600 font-medium">Bank Transfer settings saved successfully.</p>
            @endif
        </div>
    </div>

</div>
