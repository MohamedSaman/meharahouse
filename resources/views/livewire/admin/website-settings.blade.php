{{-- resources/views/livewire/admin/website-settings.blade.php --}}
<div class="space-y-6">

    {{-- ══════════════════════ PAGE HEADER ══════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 sm:p-6 shadow-xl">
        <div class="absolute -top-14 -right-10 h-44 w-44 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-10 h-36 w-36 rounded-full bg-blue-400/10 blur-3xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-amber-300">Admin &rarr; Settings</p>
                <h2 class="font-[Poppins] font-bold text-2xl text-white flex items-center gap-3 mt-0.5">
                    <svg class="w-6 h-6 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    Website Settings
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    Control your storefront — live status, announcements, site info and social channels.
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ FLASH MESSAGES ══════════════════════ --}}
    @foreach(['success_live' => 'emerald', 'success_announcement' => 'emerald', 'success_siteinfo' => 'emerald', 'success_social' => 'emerald', 'success_order_settings' => 'emerald'] as $key => $color)
        @if(session($key))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 p-4 bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-xl text-{{ $color }}-800 text-sm font-medium">
            <svg class="w-5 h-5 shrink-0 text-{{ $color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session($key) }}
        </div>
        @endif
    @endforeach

    {{-- ══════════════════════════════════════════════════════════════
         1. LIVE STATUS — most prominent card
    ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border-2 {{ $websiteLive ? 'border-emerald-200' : 'border-red-300' }} bg-white shadow-sm overflow-hidden transition-colors duration-300">

        {{-- Card header --}}
        <div class="px-5 sm:px-6 py-5 border-b {{ $websiteLive ? 'border-emerald-100 bg-emerald-50/40' : 'border-red-100 bg-red-50/40' }} transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    {{-- Pulsing status dot --}}
                    <div class="relative shrink-0">
                        <span class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $websiteLive ? 'bg-emerald-100' : 'bg-red-100' }}">
                            <svg class="w-7 h-7 {{ $websiteLive ? 'text-emerald-600' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($websiteLive)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                @endif
                            </svg>
                        </span>
                        {{-- Animated pulse ring --}}
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $websiteLive ? 'bg-emerald-500' : 'bg-red-500' }} flex items-center justify-center">
                            @if(!$websiteLive)
                            <span class="absolute inline-flex w-full h-full rounded-full {{ $websiteLive ? 'bg-emerald-400' : 'bg-red-400' }} opacity-75 animate-ping"></span>
                            @endif
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="font-[Poppins] font-bold text-lg text-slate-800">Website Live Status</h3>
                            {{-- Pill badge --}}
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase
                                         {{ $websiteLive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $websiteLive ? 'bg-emerald-500' : 'bg-red-500' }} {{ $websiteLive ? '' : 'animate-pulse' }}"></span>
                                {{ $websiteLive ? 'LIVE' : 'OFFLINE / MAINTENANCE' }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 mt-0.5">Toggle to take your storefront online or offline for maintenance.</p>
                    </div>
                </div>

                {{-- Toggle switch --}}
                <div class="shrink-0">
                    <button wire:click="$toggle('websiteLive')"
                            x-data
                            @click.stop
                            :style="'display:inline-flex;align-items:center;width:56px;height:30px;border-radius:9999px;padding:3px;border:none;cursor:pointer;transition:background .25s;background:' + ($wire.websiteLive ? '#22c55e' : '#ef4444')"
                            role="switch"
                            :aria-checked="$wire.websiteLive.toString()"
                            aria-label="Toggle website live status">
                        <span :style="'display:inline-block;width:24px;height:24px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.25);transition:transform .25s;transform:translateX(' + ($wire.websiteLive ? '26px' : '0') + ')'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Offline warning banner --}}
        @if(!$websiteLive)
        <div class="flex items-start gap-3 px-5 sm:px-6 py-3 bg-red-600 text-white text-sm font-medium">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <span>Your website is currently <strong>DOWN</strong> for visitors. Only admin and staff can access the storefront.</span>
                <span class="block mt-1 text-red-200 text-xs">
                    You see the site normally because you are logged in as admin.
                    Open an <strong>Incognito / Private browser window</strong> to see the maintenance page visitors see.
                </span>
            </div>
        </div>
        @endif

        {{-- Card body --}}
        <div class="px-5 sm:px-6 py-5 space-y-4">

            {{-- Maintenance fields — always visible so admin can pre-fill before toggling --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Maintenance Page Title</label>
                    <input type="text"
                           wire:model="maintenanceTitle"
                           class="form-input w-full"
                           placeholder="e.g. Site Under Maintenance">
                    @error('maintenanceTitle') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Maintenance Message</label>
                    <input type="text"
                           wire:model="maintenanceMessage"
                           class="form-input w-full"
                           placeholder="e.g. We are performing maintenance. Back shortly!">
                    @error('maintenanceMessage') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-400">
                    Tip: Admins always bypass maintenance mode. To see the maintenance page, open an <strong>Incognito window</strong>.
                </p>
                <button wire:click="saveLiveStatus"
                        class="btn-primary inline-flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Live Status
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         2. ANNOUNCEMENT BAR
    ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        {{-- Card header --}}
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 text-sm">Announcement Bar</h3>
                    <p class="text-xs text-slate-400">The gold top banner shown to all storefront visitors.</p>
                </div>
            </div>
            {{-- Toggle --}}
            <button wire:click="$toggle('announcementEnabled')"
                    x-data
                    @click.stop
                    :style="'display:inline-flex;align-items:center;width:52px;height:28px;border-radius:9999px;padding:3px;border:none;cursor:pointer;transition:background .25s;background:' + ($wire.announcementEnabled ? '#22c55e' : '#94a3b8')"
                    role="switch"
                    :aria-checked="$wire.announcementEnabled.toString()"
                    aria-label="Toggle announcement bar">
                <span :style="'display:inline-block;width:22px;height:22px;border-radius:9999px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .25s;transform:translateX(' + ($wire.announcementEnabled ? '24px' : '0') + ')'"></span>
            </button>
        </div>

        {{-- Card body --}}
        <div class="px-5 sm:px-6 py-5 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Announcement Text</label>
                <input type="text"
                       wire:model.live="announcementText"
                       class="form-input w-full"
                       placeholder="Free Delivery on Orders Over Rs. 500 | New Arrivals Every Week">
                @error('announcementText') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Live preview --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Live Preview</p>
                <div class="rounded-lg overflow-hidden border border-slate-200">
                    @if($announcementEnabled)
                    <div class="w-full py-2 px-4 text-center text-sm font-medium"
                         style="background:linear-gradient(90deg,#0F172A,#1E293B,#0F172A);color:#F59E0B;">
                        {{ $announcementText ?: 'Your announcement text will appear here.' }}
                    </div>
                    @else
                    <div class="w-full py-2 px-4 text-center text-xs text-slate-400 bg-slate-50 italic">
                        Announcement bar is disabled — it will not appear on the storefront.
                    </div>
                    @endif
                    <div class="h-8 bg-slate-100 flex items-center px-4">
                        <div class="flex gap-2">
                            <div class="w-12 h-2 bg-slate-300 rounded-full"></div>
                            <div class="w-20 h-2 bg-slate-300 rounded-full"></div>
                            <div class="w-16 h-2 bg-slate-300 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-1 border-t border-slate-100">
                <button wire:click="saveAnnouncement" class="btn-primary inline-flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Announcement
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         3. SITE INFORMATION
    ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Site Information</h3>
                <p class="text-xs text-slate-400">Business name, contact details, and address displayed across the storefront.</p>
            </div>
        </div>

        <div class="px-5 sm:px-6 py-5 space-y-4">
            {{-- Row 1: Name + Tagline --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Site Name <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="siteName" class="form-input w-full" placeholder="Meharahouse">
                    @error('siteName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tagline</label>
                    <input type="text" wire:model="siteTagline" class="form-input w-full" placeholder="Elegance in Every Thread">
                    @error('siteTagline') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Row 2: Email + Phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email Address
                        </span>
                    </label>
                    <input type="email" wire:model="siteEmail" class="form-input w-full" placeholder="hello@meharahouse.com">
                    @error('siteEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Phone Number
                        </span>
                    </label>
                    <input type="text" wire:model="sitePhone" class="form-input w-full" placeholder="+92 300 000 0000">
                    @error('sitePhone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Row 3: WhatsApp + Address --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" style="color:#25D366" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                            </svg>
                            WhatsApp Number
                        </span>
                    </label>
                    <input type="text" wire:model="siteWhatsapp" class="form-input w-full" placeholder="+92 300 000 0000">
                    @error('siteWhatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Business Address
                        </span>
                    </label>
                    <input type="text" wire:model="siteAddress" class="form-input w-full" placeholder="123 Main Street, City, Country">
                    @error('siteAddress') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-1 border-t border-slate-100">
                <button wire:click="saveSiteInfo" class="btn-primary inline-flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Site Info
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         4. SOCIAL MEDIA LINKS
    ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Social Media Links</h3>
                <p class="text-xs text-slate-400">These appear in the storefront footer and contact page.</p>
            </div>
        </div>

        <div class="px-5 sm:px-6 py-5 space-y-4">
            {{-- Facebook --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded flex items-center justify-center" style="background:#1877F2">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </span>
                        Facebook
                    </span>
                </label>
                <input type="url" wire:model="socialFacebook" class="form-input w-full" placeholder="https://facebook.com/meharahouse">
                @error('socialFacebook') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Instagram --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded flex items-center justify-center" style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </span>
                        Instagram
                    </span>
                </label>
                <input type="url" wire:model="socialInstagram" class="form-input w-full" placeholder="https://instagram.com/meharahouse">
                @error('socialInstagram') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- TikTok --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded flex items-center justify-center" style="background:#000">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.31 6.31 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.78a4.85 4.85 0 01-1.01-.09z"/>
                            </svg>
                        </span>
                        TikTok
                    </span>
                </label>
                <input type="url" wire:model="socialTikTok" class="form-input w-full" placeholder="https://tiktok.com/@meharahouse">
                @error('socialTikTok') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- YouTube --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded flex items-center justify-center" style="background:#FF0000">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                            </svg>
                        </span>
                        YouTube
                    </span>
                </label>
                <input type="url" wire:model="socialYoutube" class="form-input w-full" placeholder="https://youtube.com/@meharahouse">
                @error('socialYoutube') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-1 border-t border-slate-100">
                <button wire:click="saveSocialLinks" class="btn-primary inline-flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Social Links
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         5. ORDER SETTINGS
    ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Order Settings</h3>
                <p class="text-xs text-slate-400">Configure advance payment percentage and bank transfer details shown to customers.</p>
            </div>
        </div>

        <div class="px-5 sm:px-6 py-5 space-y-5">

            {{-- Advance Payment Percentage --}}
            <div class="max-w-xs">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Advance Payment Percentage <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number"
                           wire:model="advancePaymentPercentage"
                           min="1"
                           max="100"
                           class="form-input w-28 text-center font-bold text-lg"
                           placeholder="50">
                    <span class="text-slate-500 text-sm font-semibold">%</span>
                    <span class="text-xs text-slate-400">of order total required upfront</span>
                </div>
                @error('advancePaymentPercentage')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-slate-400">
                    This percentage is used when generating WhatsApp order links and shown to website customers at checkout. Default is 50%.
                </p>
            </div>

            {{-- Bank Transfer Details --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bank Transfer Details</label>
                <textarea wire:model="bankTransferDetails"
                          rows="4"
                          class="form-input w-full resize-none font-mono text-sm"
                          placeholder="Bank: Meezan Bank&#10;Account Name: Meharahouse&#10;Account Number: 0123456789&#10;Branch: Main Branch"></textarea>
                @error('bankTransferDetails')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-slate-400">
                    Shown to customers who need to make bank transfers for advance or balance payments.
                </p>
            </div>

            {{-- Live preview --}}
            @if($bankTransferDetails)
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-2">Customer Preview</p>
                <div class="text-sm text-slate-700 whitespace-pre-line font-mono">{{ $bankTransferDetails }}</div>
            </div>
            @endif

            {{-- Delivery Fee --}}
            <div class="rounded-xl border border-slate-200 p-4 space-y-4"
                 x-data="{ enabled: @js($deliveryFeeEnabled) }">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Delivery Fee</p>
                        <p class="text-xs text-slate-400 mt-0.5">Charge a fixed delivery fee on every order</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="deliveryFeeEnabled" @change="enabled = $event.target.checked" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-[#D4A017]/30 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A017]"></div>
                    </label>
                </div>

                <div x-show="enabled" style="display:none;">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Delivery Fee Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">Rs.</span>
                        <input wire:model="deliveryFeeAmount"
                               type="number" min="0" step="0.01"
                               placeholder="0.00"
                               class="form-input w-full pl-10">
                    </div>
                    @error('deliveryFeeAmount')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-400">This amount is added to every order at checkout.</p>
                </div>

                <div x-show="!enabled" class="text-xs text-slate-400 italic" style="display:none;">
                    Delivery fee is currently <strong>disabled</strong> — no delivery charge is added to orders.
                </div>
            </div>

            <div class="flex justify-end pt-1 border-t border-slate-100">
                <button wire:click="saveOrderSettings" class="btn-primary inline-flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Order Settings
                </button>
            </div>
        </div>
    </div>

</div>
