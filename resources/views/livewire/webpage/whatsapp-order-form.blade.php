{{-- resources/views/livewire/webpage/whatsapp-order-form.blade.php --}}
{{-- Public-facing page. No auth required. Optimised for mobile. --}}
<div class="min-h-screen bg-[#F8FAFC]">

    <main class="max-w-lg mx-auto px-4 py-6 space-y-5 pb-16">

        {{-- ══ PAGE TITLE ══ --}}
        <div class="text-center pt-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-100 mb-3 shadow-sm">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h1 class="font-[Poppins] font-bold text-2xl text-[#0F172A]">Complete Your Order</h1>
            <p class="text-sm text-[#64748B] mt-1">Fill in your details and upload your advance payment receipt.</p>
        </div>

        {{-- ══ INVALID / USED TOKEN STATE ══ --}}
        @if($tokenInvalid)
        <div class="rounded-2xl border-2 border-red-200 bg-red-50 p-6 text-center space-y-3">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 class="font-[Poppins] font-bold text-xl text-red-800">Link Not Available</h2>
            <p class="text-sm text-red-700 leading-relaxed">
                This order link has already been used or has expired.<br>
                Please contact us on WhatsApp to receive a new link.
            </p>
            @php $whatsapp = \App\Models\Setting::get('site_whatsapp', ''); @endphp
            @if($whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}"
               target="_blank"
               class="inline-flex items-center gap-2 mt-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-white transition-all"
               style="background:#25D366">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413A11.815 11.815 0 0012.05 0z"/>
                </svg>
                Contact Us on WhatsApp
            </a>
            @endif
        </div>

        {{-- ══ SUCCESS STATE ══ --}}
        @elseif($submitted)
        <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-6 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-500 flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h2 class="font-[Poppins] font-bold text-2xl text-emerald-800">Order Placed!</h2>
                <p class="text-emerald-700 text-sm mt-1">Thank you for your order.</p>
            </div>
            <div class="bg-white/80 rounded-xl border border-emerald-200 p-4">
                <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide mb-1">Your Order Number</p>
                <p class="font-mono font-bold text-xl text-[#0F172A]">{{ $createdOrderNumber }}</p>
            </div>
            <p class="text-sm text-emerald-700 leading-relaxed">
                We have received your order and payment receipt. Our team will review it and contact you shortly on WhatsApp.
            </p>
            <div class="flex items-center justify-center gap-2 pt-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <span class="text-xs text-emerald-600 font-medium">Admin is reviewing your receipt</span>
            </div>
        </div>

        {{-- ══ ORDER FORM ══ --}}
        @elseif($tokenModel)

        {{-- 1. Products Summary --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#0F172A] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A]">Your Order Items</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($tokenModel->products as $index => $product)
                @php $variants = $productVariants[$index] ?? []; @endphp
                <div class="px-4 py-3 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#0F172A] truncate">{{ $product['product_name'] }}</p>
                            <p class="text-xs text-[#94A3B8] mt-0.5">
                                Rs. {{ number_format($product['price'], 0) }}
                                &times; {{ $product['quantity'] }}
                            </p>
                        </div>
                        <span class="font-bold text-sm text-[#0F172A] ml-4 shrink-0">
                            Rs. {{ number_format($product['price'] * $product['quantity'], 0) }}
                        </span>
                    </div>

                    {{-- Size chips (only if product has sizes) --}}
                    @if(!empty($variants['sizes']))
                    <div>
                        <p class="text-xs font-semibold text-[#475569] mb-2 flex items-center gap-1">
                            <span class="text-sm">📏</span> Size
                            <span class="text-red-400 font-bold">*</span>
                            @if($productSizes[$index] ?? '')
                                <span class="ml-1 text-amber-700">— {{ $productSizes[$index] }}</span>
                            @endif
                        </p>
                        @error('productSizes.'.$index)
                            <p class="text-xs text-red-500 mb-1.5">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-wrap gap-2">
                            @foreach($variants['sizes'] as $sz)
                            <button
                                type="button"
                                wire:click="selectSize({{ $index }}, '{{ $sz }}')"
                                class="px-3.5 py-1.5 rounded-lg border text-sm font-semibold transition-all
                                    {{ ($productSizes[$index] ?? '') === $sz
                                        ? 'bg-[#0F172A] border-[#0F172A] text-white shadow-sm scale-105'
                                        : 'bg-white border-slate-300 text-[#475569] hover:border-amber-400 hover:text-amber-700' }}">
                                {{ $sz }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Color swatches (only if product has colors) --}}
                    @if(!empty($variants['colors']))
                    <div>
                        <p class="text-xs font-semibold text-[#475569] mb-2 flex items-center gap-1">
                            <span class="text-sm">🎨</span> Color
                            <span class="text-red-400 font-bold">*</span>
                            @if($productColors[$index] ?? '')
                                <span class="ml-1 text-amber-700">— {{ $productColors[$index] }}</span>
                            @endif
                        </p>
                        @error('productColors.'.$index)
                            <p class="text-xs text-red-500 mb-1.5">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-wrap gap-2">
                            @foreach($variants['colors'] as $clr)
                            <button
                                type="button"
                                wire:click="selectColor({{ $index }}, '{{ $clr['name'] }}')"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm font-semibold transition-all
                                    {{ ($productColors[$index] ?? '') === $clr['name']
                                        ? 'border-[#0F172A] bg-[#0F172A]/5 text-[#0F172A] shadow-sm scale-105'
                                        : 'border-slate-300 bg-white text-[#475569] hover:border-amber-400 hover:text-amber-700' }}">
                                <span class="w-4 h-4 rounded-full border border-slate-200 shrink-0" style="background-color:{{ $clr['hex'] }}"></span>
                                {{ $clr['name'] }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- 2. Payment Summary --}}
        <div class="bg-gradient-to-br from-[#0F172A] to-slate-800 rounded-2xl p-5 shadow-xl">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4">Payment Summary</h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">Order Total</span>
                    <span class="text-white font-semibold">Rs. {{ number_format($tokenModel->subtotal, 0) }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-700/50 pt-2.5">
                    <div>
                        <p class="text-amber-300 font-bold text-sm">Advance Due Now</p>
                        <p class="text-xs text-slate-500">({{ $tokenModel->advance_percentage }}% of total)</p>
                    </div>
                    <span class="text-amber-400 font-bold text-2xl">Rs. {{ number_format($tokenModel->advance_amount, 0) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Balance (paid on delivery)</span>
                    <span class="text-slate-400">Rs. {{ number_format($tokenModel->subtotal - $tokenModel->advance_amount, 0) }}</span>
                </div>
            </div>

            {{-- Bank Transfer Details --}}
            @if($bankDetails)
            <div class="mt-4 rounded-xl bg-slate-800/60 border border-slate-700/50 p-4">
                <p class="text-xs font-semibold text-amber-300 uppercase tracking-widest mb-2">Transfer Advance To</p>
                <p class="text-sm text-slate-300 whitespace-pre-line font-mono leading-relaxed">{{ $bankDetails }}</p>
            </div>
            @endif

            <div class="mt-3 flex items-start gap-2 bg-amber-400/10 rounded-xl px-3 py-2.5 border border-amber-400/20">
                <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-amber-300 leading-relaxed">
                    Please transfer the advance amount to the bank account above, then upload your receipt (screenshot or photo) below.
                </p>
            </div>
        </div>

        {{-- 3. Receipt Upload --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-sm text-[#0F172A]">Payment Receipt</h3>
                    <p class="text-xs text-[#64748B]">Upload a photo or screenshot of your transfer confirmation</p>
                </div>
            </div>

            <label
                for="receiptInput"
                class="block cursor-pointer"
                x-data="{ hasFile: false }"
            >
                <input
                    id="receiptInput"
                    wire:model="receiptFile"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    x-on:change="hasFile = !!$event.target.files.length"
                >
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-amber-400 hover:bg-amber-50/30 transition-all duration-200"
                     :class="hasFile ? 'border-emerald-400 bg-emerald-50/30' : 'border-slate-200'">
                    <div x-show="!hasFile">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-[#64748B]">Tap to upload receipt</p>
                        <p class="text-xs text-[#94A3B8] mt-1">JPG, PNG, WebP — up to 5 MB</p>
                    </div>
                    <div x-show="hasFile" style="display:none">
                        <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-semibold text-emerald-700">Receipt selected!</p>
                        <p class="text-xs text-emerald-600 mt-1">Tap to change</p>
                    </div>
                </div>
            </label>

            {{-- Upload progress indicator --}}
            <div wire:loading wire:target="receiptFile" class="mt-2 flex items-center gap-2 text-xs text-amber-600">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Uploading...
            </div>

            @error('receiptFile')
                <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- 4. Order & Delivery Details --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Section header --}}
            <div class="px-5 py-3.5 bg-[#0F172A] flex items-center gap-2.5">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="font-[Poppins] font-bold text-sm text-white">✨ Order Confirmation Details</h3>
            </div>

            <div class="p-5 space-y-4">

                @php
                $inputClass = 'w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-[#0F172A] placeholder-slate-400 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all';
                $labelClass = 'block text-xs font-semibold text-[#475569] mb-1.5 flex items-center gap-1.5';
                @endphp

                {{-- 👤 Name --}}
                <div>
                    <label class="{{ $labelClass }}">
                        <span class="text-base">👤</span> Name <span class="text-red-400">*</span>
                    </label>
                    <input wire:model="customerName" type="text" placeholder="Your full name"
                           class="{{ $inputClass }} @error('customerName') border-red-400 bg-red-50 @enderror">
                    @error('customerName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- 🏠 Address --}}
                <div>
                    <label class="{{ $labelClass }}">
                        <span class="text-base">🏠</span> Address <span class="text-red-400">*</span>
                    </label>
                    <input wire:model="addressLine" type="text" placeholder="House/Flat #, Street, Area"
                           class="{{ $inputClass }} @error('addressLine') border-red-400 bg-red-50 @enderror">
                    @error('addressLine') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- 📍 District + City --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $labelClass }}">
                            <span class="text-base">📍</span> District
                        </label>
                        <input wire:model="district" type="text" placeholder="e.g. Colombo"
                               class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">
                            <span class="text-base">🏙️</span> City <span class="text-red-400">*</span>
                        </label>
                        <input wire:model="city" type="text" placeholder="e.g. Colombo 03"
                               class="{{ $inputClass }} @error('city') border-red-400 bg-red-50 @enderror">
                        @error('city') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Region/Province (hidden but kept for backend) --}}
                <input wire:model="region" type="hidden" value="N/A">

                {{-- 📞 Phone --}}
                <div>
                    <label class="{{ $labelClass }}">
                        <span class="text-base">📞</span> Phone No <span class="text-red-400">*</span>
                    </label>
                    <input wire:model="customerPhone" type="tel" placeholder="+94 77 000 0000"
                           class="{{ $inputClass }} @error('customerPhone') border-red-400 bg-red-50 @enderror">
                    @error('customerPhone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- 📞 Alternate Phone --}}
                <div>
                    <label class="{{ $labelClass }}">
                        <span class="text-base">📞</span> Alternate Phone No
                        <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input wire:model="altPhone" type="tel" placeholder="+94 71 000 0000"
                           class="{{ $inputClass }}">
                </div>

                {{-- 💬 Notes --}}
                <div>
                    <label class="{{ $labelClass }}">
                        <span class="text-base">💬</span> Special Instructions
                        <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <textarea wire:model="notes" rows="3"
                              placeholder="Any special requests or delivery instructions..."
                              class="{{ $inputClass }} resize-none"></textarea>
                </div>

            </div>
        </div>

        {{-- Balance payment reminder banner --}}
        @php $balanceAmt = number_format($tokenModel->subtotal - $tokenModel->advance_amount, 0); @endphp
        <div class="rounded-2xl border-2 border-red-200 bg-red-50 px-5 py-4 flex items-start gap-3">
            <span class="text-xl mt-0.5">🔴</span>
            <div class="text-sm text-red-800">
                <p class="font-bold">Balance Payment Reminder</p>
                <p class="mt-0.5 leading-relaxed">
                    Your balance amount of <strong>Rs. {{ $balanceAmt }}</strong> is due before delivery.
                    Our team will contact you with payment details once your order is dispatched.
                </p>
            </div>
        </div>

        {{-- 5. Submit Button --}}
        <div class="space-y-3">
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                class="w-full py-4 rounded-2xl bg-[#0F172A] hover:bg-slate-800 text-white font-bold text-base font-[Poppins] transition-all duration-200 shadow-xl shadow-slate-900/20 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                <svg wire:loading.remove wire:target="submit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg wire:loading wire:target="submit" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove wire:target="submit">Confirm My Order</span>
                <span wire:loading wire:target="submit">Placing Order...</span>
            </button>

            <div class="flex items-center justify-center gap-4 text-xs text-[#94A3B8]">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Secure Form
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Trusted by Mehra House
                </span>
            </div>
        </div>

        @endif

    </main>


</div>
