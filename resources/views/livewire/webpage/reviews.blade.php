{{-- resources/views/livewire/webpage/reviews.blade.php --}}
<div x-data="{ showForm: false, submitted: false }"
     @review-submitted.window="showForm = false; submitted = true; setTimeout(() => submitted = false, 8000)">

    {{-- ══════════════════════ HERO ══════════════════════ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0F172A] py-16 md:py-20">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-[#D4A017]/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>
        <div class="container-page relative z-10 text-center">
            {{-- Breadcrumb --}}
            <nav class="flex items-center justify-center gap-2 text-xs text-slate-400 mb-6">
                <a href="{{ route('webpage.home') }}" class="hover:text-[#D4A017] transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#D4A017]">Reviews</span>
            </nav>

            {{-- Stars decoration --}}
            <div class="flex items-center justify-center gap-1 mb-4">
                @for($i = 0; $i < 5; $i++)
                <svg class="w-6 h-6 text-[#D4A017]" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                @endfor
            </div>

            <h1 class="font-[Poppins] font-extrabold text-4xl md:text-5xl text-white mb-3">Customer Reviews</h1>
            <p class="text-slate-300 text-base md:text-lg max-w-xl mx-auto">Real stories from real customers — honest feedback from our Meharahouse community</p>
        </div>
    </section>

    {{-- ══════════════════════ SUCCESS TOAST ══════════════════════ --}}
    <div x-show="submitted"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none;"
         class="fixed top-24 right-4 z-50 max-w-sm w-full">
        <div class="flex items-start gap-3 p-4 bg-emerald-600 text-white rounded-2xl shadow-2xl">
            <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-bold text-sm">Thank you for your review!</p>
                <p class="text-emerald-100 text-xs mt-0.5">Your review has been submitted and is pending approval. It will appear here once approved.</p>
            </div>
            <button @click="submitted = false" class="ml-auto text-emerald-200 hover:text-white shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div class="container-page py-10 space-y-8">

        {{-- ══════════════════════ STATS + WRITE REVIEW ══════════════════════ --}}
        <div class="bg-white rounded-2xl border border-[#F0EDE8] shadow-sm overflow-hidden">
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-[#F0EDE8]">

                {{-- Left: Average rating + distribution --}}
                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-6 mb-6">
                        {{-- Big average number --}}
                        <div class="text-center shrink-0">
                            <p class="font-[Poppins] font-extrabold text-6xl text-[#0F172A] leading-none">{{ number_format($stats['average'], 1) }}</p>
                            <div class="flex items-center justify-center gap-0.5 mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($stats['average']) ? 'text-[#D4A017]' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <p class="text-xs text-slate-400 mt-1">{{ $stats['total'] }} {{ Str::plural('review', $stats['total']) }}</p>
                        </div>

                        {{-- Star distribution bars --}}
                        <div class="flex-1 space-y-1.5">
                            @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $stats['counts'][$star] ?? 0;
                                $pct   = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
                            @endphp
                            <button wire:click="$set('filterRating', '{{ $filterRating == $star ? '' : $star }}')"
                                    class="flex items-center gap-2 w-full group hover:opacity-80 transition-opacity">
                                <span class="text-xs font-semibold text-slate-500 w-4 shrink-0">{{ $star }}</span>
                                <svg class="w-3.5 h-3.5 text-[#D4A017] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <div class="flex-1 h-2 bg-[#F0EDE8] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#D4A017] rounded-full transition-all duration-500"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-slate-400 w-8 text-right shrink-0">{{ $pct }}%</span>
                            </button>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Right: CTA --}}
                <div class="p-6 md:p-8 flex flex-col items-center justify-center text-center gap-4 bg-[#FFFDF9]">
                    <div class="w-16 h-16 rounded-2xl bg-[#D4A017]/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Share Your Experience</h3>
                        <p class="text-sm text-slate-500 mt-1">Bought from us? Let others know what you think.</p>
                    </div>
                    <button @click="showForm = true"
                            class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Write a Review
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- close container-page --}}

    {{-- ══════════════════════ WRITE REVIEW MODAL ══════════════════════ --}}
    <div x-show="showForm"
         x-on:keydown.escape.window="showForm = false"
         wire:ignore
         style="display:none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="showForm = false"
         x-data="{
            name: '', email: '', titleVal: '', descVal: '', productId: 0,
            starRating: 5, hovered: 0, charCount: 0, submitting: false,
            errors: {},
            starLabels: ['','Poor','Fair','Good','Very Good','Excellent'],
            reset() {
                this.name = ''; this.email = ''; this.titleVal = '';
                this.descVal = ''; this.productId = 0; this.starRating = 5;
                this.hovered = 0; this.charCount = 0; this.errors = {};
                this.submitting = false;
            },
            validate() {
                this.errors = {};
                if (!this.name.trim())                         this.errors.name = 'Name is required.';
                if (this.descVal.trim().length < 10)           this.errors.desc = 'Review must be at least 10 characters.';
                if (this.descVal.trim().length > 1000)         this.errors.desc = 'Review must not exceed 1000 characters.';
                if (this.starRating < 1 || this.starRating > 5) this.errors.rating = 'Please select a rating.';
                return Object.keys(this.errors).length === 0;
            },
            async submit() {
                if (!this.validate()) return;
                this.submitting = true;
                try {
                    await $wire.call(
                        'submitReview',
                        this.name.trim(),
                        this.email.trim(),
                        this.starRating,
                        this.titleVal.trim(),
                        this.descVal.trim(),
                        parseInt(this.productId) || 0
                    );
                } catch(e) {
                    console.error('Review submit error:', e);
                } finally {
                    this.submitting = false;
                }
            }
         }"
         @review-submitted.window="showForm = false; reset()">

        {{-- Modal panel --}}
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden"
             @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0EDE8] bg-gradient-to-r from-[#FFFDF5] to-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#D4A017]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Write a Review</h3>
                        <p class="text-xs text-slate-500">Your review will appear after approval</p>
                    </div>
                </div>
                <button @click="showForm = false" type="button"
                        class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-6 py-6 space-y-5">

                {{-- Row 1: Name + Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="name" placeholder="e.g. Fatima Ahmed"
                               :class="errors.name ? 'border-red-400 bg-red-50' : ''"
                               class="form-input w-full">
                        <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-xs mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email <span class="text-slate-400 font-normal">(optional)</span>
                        </label>
                        <input type="email" x-model="email" placeholder="your@email.com" class="form-input w-full">
                    </div>
                </div>

                {{-- Row 2: Product + Rating --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Product <span class="text-slate-400 font-normal">(optional)</span></label>
                        <select x-model="productId" class="form-input w-full">
                            <option value="0">— Select a product —</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Your Rating <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-1 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    @mouseenter="hovered = {{ $i }}"
                                    @mouseleave="hovered = 0"
                                    @click="starRating = {{ $i }}"
                                    class="transition-transform hover:scale-125 focus:outline-none">
                                <svg class="w-9 h-9 transition-colors duration-100"
                                     :class="(hovered >= {{ $i }} || (starRating >= {{ $i }} && hovered === 0)) ? 'text-[#D4A017]' : 'text-slate-200'"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                            @endfor
                            <span class="ml-2 text-sm font-bold"
                                  :class="starRating >= 4 ? 'text-emerald-600' : starRating >= 3 ? 'text-amber-500' : 'text-red-500'"
                                  x-text="starLabels[starRating]"></span>
                        </div>
                        <p x-show="errors.rating" x-text="errors.rating" class="text-red-500 text-xs mt-1"></p>
                    </div>
                </div>

                {{-- Review Headline --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Review Headline <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" x-model="titleVal" placeholder="e.g. Beautiful quality abaya!" class="form-input w-full">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Your Review <span class="text-red-500">*</span>
                    </label>
                    <textarea x-model="descVal"
                              @input="charCount = $el.value.length"
                              rows="5"
                              placeholder="Tell us about your experience — the quality, fit, delivery, and anything else you'd like to share..."
                              :class="errors.desc ? 'border-red-400 bg-red-50' : ''"
                              class="form-input w-full resize-none"></textarea>
                    <div class="flex items-center justify-between mt-1">
                        <p x-show="errors.desc" x-text="errors.desc" class="text-red-500 text-xs"></p>
                        <span x-show="!errors.desc" class="text-xs text-slate-400">Minimum 10 characters</span>
                        <span class="text-xs ml-auto"
                              :class="charCount > 900 ? 'text-red-400 font-semibold' : 'text-slate-400'"
                              x-text="charCount + '/1000'"></span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-[#F0EDE8] bg-slate-50 rounded-b-2xl shrink-0">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Moderated before publishing
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" @click="showForm = false" class="btn-secondary text-sm">Cancel</button>
                    <button type="button"
                            @click="submit()"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-75 cursor-not-allowed' : ''"
                            class="btn-primary inline-flex items-center gap-2 text-sm">
                        <template x-if="!submitting">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        <template x-if="submitting">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </template>
                        <span x-text="submitting ? 'Submitting...' : 'Submit Review'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    {{-- end modal --}}

    <div class="container-page py-0 pb-10 space-y-8">
        {{-- ══════════════════════ FILTER + SORT ══════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white rounded-2xl border border-[#F0EDE8] px-5 py-4 shadow-sm">
            {{-- Star filter --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-slate-500 shrink-0">Filter:</span>
                <button wire:click="$set('filterRating', '')"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200
                               {{ $filterRating === '' ? 'bg-[#D4A017] text-white shadow-sm' : 'bg-[#FDF8F0] text-slate-600 hover:bg-[#D4A017]/10' }}">
                    All Stars
                </button>
                @for($s = 5; $s >= 1; $s--)
                <button wire:click="$set('filterRating', '{{ $s }}')"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-1
                               {{ $filterRating == $s ? 'bg-[#D4A017] text-white shadow-sm' : 'bg-[#FDF8F0] text-slate-600 hover:bg-[#D4A017]/10' }}">
                    {{ $s }}
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </button>
                @endfor
            </div>

            {{-- Sort --}}
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs font-semibold text-slate-500">Sort:</span>
                <select wire:model.live="sortBy" class="form-input py-1.5 text-sm pr-8">
                    <option value="latest">Latest First</option>
                    <option value="highest">Highest Rated</option>
                    <option value="lowest">Lowest Rated</option>
                </select>
            </div>
        </div>

        {{-- ══════════════════════ REVIEWS GRID ══════════════════════ --}}
        <div wire:loading.class="opacity-60 pointer-events-none" class="transition-opacity duration-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($reviews as $review)
                <div wire:key="review-{{ $review->id }}"
                     x-data="{ expanded: false }"
                     class="bg-white rounded-2xl border border-[#F0EDE8] shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col">

                    {{-- Card top --}}
                    <div class="p-5 flex-1">
                        {{-- Reviewer + date --}}
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                {{-- Avatar initials --}}
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0F172A] to-[#1E293B] flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="text-[#D4A017] font-bold text-sm">{{ strtoupper(substr($review->customer_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-[#0F172A]">{{ $review->customer_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            {{-- Stars --}}
                            <div class="flex items-center gap-0.5 shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-[#D4A017]' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                        </div>

                        {{-- Product tag --}}
                        @if($review->product)
                        <a href="{{ route('webpage.product-details', $review->product->slug) }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#D4A017]/8 text-[#92400E] text-xs font-semibold mb-3 hover:bg-[#D4A017]/15 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ Str::limit($review->product->name, 30) }}
                        </a>
                        @endif

                        {{-- Review title --}}
                        @if($review->title)
                        <h4 class="font-[Poppins] font-semibold text-sm text-[#0F172A] mb-2">{{ $review->title }}</h4>
                        @endif

                        {{-- Description --}}
                        <p class="text-sm text-slate-600 leading-relaxed"
                           x-show="!expanded || {{ strlen($review->description) <= 200 ? 'true' : 'false' }}">
                            {{ strlen($review->description) > 200 ? Str::limit($review->description, 200) : $review->description }}
                        </p>
                        @if(strlen($review->description) > 200)
                        <p class="text-sm text-slate-600 leading-relaxed" x-show="expanded" style="display:none;">
                            {{ $review->description }}
                        </p>
                        <button @click="expanded = !expanded"
                                class="text-[#D4A017] text-xs font-semibold mt-1 hover:underline focus:outline-none"
                                x-text="expanded ? 'Show less' : 'Read more'">
                        </button>
                        @endif
                    </div>

                    {{-- Card footer --}}
                    <div class="px-5 py-3 bg-[#FAFAF8] border-t border-[#F0EDE8] flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs text-slate-500">Verified Review</span>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center flex flex-col items-center justify-center">
                    <div class="w-20 h-20 rounded-2xl bg-[#FDF8F0] flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-[#D4A017]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A] mb-2">No reviews yet</h3>
                    <p class="text-slate-500 text-sm mb-5">
                        {{ $filterRating ? "No {$filterRating}-star reviews found. Try a different filter." : 'Be the first to share your experience with Meharahouse!' }}
                    </p>
                    @if(!$filterRating)
                    <button @click="showForm = true" class="btn-primary">Write the First Review</button>
                    @else
                    <button wire:click="$set('filterRating', '')" class="btn-secondary">Clear Filter</button>
                    @endif
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($reviews->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $reviews->links() }}
            </div>
            @endif
        </div>

    </div>

    {{-- Mobile floating button --}}
    <div x-show="!showForm && !submitted"
         style="display:none;"
         class="fixed bottom-6 right-6 z-40 lg:hidden">
        <button @click="showForm = true"
                class="flex items-center gap-2 px-5 py-3 bg-[#D4A017] text-white rounded-full shadow-2xl shadow-[#D4A017]/40 font-semibold text-sm hover:bg-[#B8860B] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Write Review
        </button>
    </div>

</div>
