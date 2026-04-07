{{-- resources/views/livewire/admin/whatsapp-integration.blade.php --}}
<div class="space-y-5">

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6">
        <div class="absolute -top-16 -right-12 h-44 w-44 rounded-full bg-green-400/20 blur-3xl pointer-events-none"></div>
        <div class="relative">
            <p class="text-[11px] tracking-[0.16em] uppercase font-semibold text-amber-300 mb-1">Admin → Settings</p>
            <h2 class="font-[Poppins] font-bold text-2xl text-white">WhatsApp Integration</h2>
            <p class="text-slate-400 text-sm mt-1">Connect a WhatsApp provider to send order notifications, confirmations, and support messages to customers.</p>
        </div>
    </div>

    {{-- Info banner — only one provider active --}}
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span><strong>Only one provider can be active at a time.</strong> Enabling a provider automatically disables the other two. Toggle ON, fill in the required credentials, then click Save.</span>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         1. TWILIO
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($twilio_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        {{-- Card header --}}
        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                {{-- Provider icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#f0fdf4">
                    <svg class="w-5 h-5" style="color:#25D366" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM11.999 2C6.477 2 2 6.477 2 12c0 1.99.574 3.842 1.563 5.408L2 22l4.703-1.545A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-slate-800 text-sm">Twilio</p>
                        @if($twilio_enabled)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                  style="background:#dcfce7;color:#166534">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                  style="background:#f1f5f9;color:#64748b">Inactive</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">Send WhatsApp messages via Twilio's Messaging API — global reach.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Twilio toggle --}}
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('twilio_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.twilio_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.twilio_enabled ? '#25D366' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.twilio_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.twilio_enabled ? '22px' : '0') + ')'"></span>
                    </button>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Expandable body --}}
        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Account SID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Account SID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="twilio_account_sid"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           style="--tw-ring-color:#25D366"
                           placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                    @error('twilio_account_sid') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Auth Token (password + show/hide) --}}
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Auth Token <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="twilio_auth_token"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                               placeholder="Your Twilio Auth Token">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('twilio_auth_token') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- From Number --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">From Number (WhatsApp-enabled) <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="twilio_from_number"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           placeholder="e.g. +14155238886">
                    @error('twilio_from_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Credentials from <a href="https://console.twilio.com" target="_blank" class="underline text-slate-500 hover:text-slate-700">console.twilio.com</a>. Use the WhatsApp Sandbox number for testing.</p>
                <button type="button" wire:click="saveTwilio" wire:loading.attr="disabled" wire:target="saveTwilio"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition disabled:opacity-60"
                        style="background:#25D366">
                    <svg wire:loading.remove wire:target="saveTwilio" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg wire:loading wire:target="saveTwilio" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Save Twilio
                </button>
            </div>
            @if($savedProvider === 'twilio')
                <p class="text-xs font-medium" style="color:#166534">Twilio settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. 360DIALOG
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($dialog360_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#f0fdf4">
                    <svg class="w-5 h-5" style="color:#25D366" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM11.999 2C6.477 2 2 6.477 2 12c0 1.99.574 3.842 1.563 5.408L2 22l4.703-1.545A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-slate-800 text-sm">360dialog</p>
                        @if($dialog360_enabled)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                  style="background:#dcfce7;color:#166534">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                  style="background:#f1f5f9;color:#64748b">Inactive</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">Official WhatsApp Business API partner — simple API key setup.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('dialog360_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.dialog360_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.dialog360_enabled ? '#25D366' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.dialog360_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.dialog360_enabled ? '22px' : '0') + ')'"></span>
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
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">API Key <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="dialog360_api_key"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                               placeholder="Your 360dialog API Key">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('dialog360_api_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="dialog360_phone_number"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           placeholder="e.g. +251912345678">
                    @error('dialog360_phone_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Get your API key from <a href="https://hub.360dialog.com" target="_blank" class="underline text-slate-500 hover:text-slate-700">hub.360dialog.com</a>.</p>
                <button type="button" wire:click="saveDialog360" wire:loading.attr="disabled" wire:target="saveDialog360"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition disabled:opacity-60"
                        style="background:#25D366">
                    <svg wire:loading.remove wire:target="saveDialog360" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg wire:loading wire:target="saveDialog360" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Save 360dialog
                </button>
            </div>
            @if($savedProvider === 'dialog360')
                <p class="text-xs font-medium" style="color:#166534">360dialog settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         3. META CLOUD API
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @js($meta_enabled) }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div @click="open = !open"
             class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#f0fdf4">
                    <svg class="w-5 h-5" style="color:#25D366" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM11.999 2C6.477 2 2 6.477 2 12c0 1.99.574 3.842 1.563 5.408L2 22l4.703-1.545A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-slate-800 text-sm">Meta Cloud API</p>
                        @if($meta_enabled)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                  style="background:#dcfce7;color:#166534">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                  style="background:#f1f5f9;color:#64748b">Inactive</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">WhatsApp Business Platform hosted directly by Meta — highest deliverability.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div @click.stop>
                    <button type="button"
                            wire:click="$toggle('meta_enabled')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:2px solid ' + ($wire.meta_enabled ? 'transparent' : '#94a3b8') + ';cursor:pointer;transition:background .25s,border .25s;background:' + ($wire.meta_enabled ? '#25D366' : '#e2e8f0')"
                            role="switch"
                            :aria-checked="$wire.meta_enabled.toString()">
                        <span :style="'display:block;width:20px;height:20px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.3);transition:transform .25s;transform:translateX(' + ($wire.meta_enabled ? '22px' : '0') + ')'"></span>
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

                {{-- Phone Number ID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number ID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="meta_phone_number_id"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           placeholder="e.g. 123456789012345">
                    @error('meta_phone_number_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Business Account ID --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">WhatsApp Business Account ID <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="meta_business_account_id"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                           placeholder="e.g. 987654321098765">
                    @error('meta_business_account_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Access Token --}}
                <div x-data="{ show: false }" class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Permanent Access Token <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="meta_access_token"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                               placeholder="EAAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('meta_access_token') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400">Credentials from <a href="https://developers.facebook.com/apps" target="_blank" class="underline text-slate-500 hover:text-slate-700">developers.facebook.com</a>. Use a System User token for production.</p>
                <button type="button" wire:click="saveMeta" wire:loading.attr="disabled" wire:target="saveMeta"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition disabled:opacity-60"
                        style="background:#25D366">
                    <svg wire:loading.remove wire:target="saveMeta" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg wire:loading wire:target="saveMeta" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Save Meta
                </button>
            </div>
            @if($savedProvider === 'meta')
                <p class="text-xs font-medium" style="color:#166534">Meta Cloud API settings saved successfully.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         TEST SEND
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:#dcfce7">
                <svg class="w-5 h-5" style="color:#16a34a" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 2C6.477 2 2 6.477 2 12c0 1.99.574 3.842 1.563 5.408L2 22l4.703-1.545A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800 text-sm">Send Test Message</p>
                <p class="text-xs text-slate-400">Verify your active provider works by sending a test message to a phone number.</p>
            </div>
        </div>

        <div class="flex items-start gap-3">
            <div class="flex-1">
                <input type="text" wire:model="testPhone"
                       placeholder="+251911234567 or +94761265772"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-400/40 focus:border-green-400">
                @error('testPhone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <button type="button" wire:click="sendTest" wire:loading.attr="disabled" wire:target="sendTest"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition disabled:opacity-60 shrink-0"
                    style="background:#25D366">
                <svg wire:loading.remove wire:target="sendTest" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg wire:loading wire:target="sendTest" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Send Test
            </button>
        </div>

        @if($testResult)
        <div class="mt-3 flex items-center gap-2 px-4 py-3 rounded-lg text-sm font-medium
                    {{ $testSuccess ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-700' }}">
            @if($testSuccess)
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            @endif
            {{ $testResult }}
        </div>
        @endif
    </div>

</div>
