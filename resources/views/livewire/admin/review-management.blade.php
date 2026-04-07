{{-- resources/views/livewire/admin/review-management.blade.php --}}
<div>

    {{-- Flash Message --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 mb-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold font-[Poppins]">Review Management</h2>
                <p class="text-slate-300 text-sm mt-0.5">Moderate customer reviews before they go live</p>
            </div>
            @if($counts['pending'] > 0)
            <div class="inline-flex items-center gap-2 bg-amber-400/20 border border-amber-400/40 text-amber-300 text-sm font-semibold px-4 py-2 rounded-xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $counts['pending'] }} pending {{ Str::plural('review', $counts['pending']) }} awaiting moderation
            </div>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        {{-- Pending --}}
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm cursor-pointer hover:border-amber-300 transition-colors"
             wire:click="$set('statusFilter', 'pending')">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['pending'] }}</p>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm cursor-pointer hover:border-emerald-300 transition-colors"
             wire:click="$set('statusFilter', 'approved')">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Approved</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['approved'] }}</p>
        </div>

        {{-- Rejected --}}
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm cursor-pointer hover:border-red-300 transition-colors"
             wire:click="$set('statusFilter', 'rejected')">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Rejected</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['rejected'] }}</p>
        </div>

        {{-- Total --}}
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm cursor-pointer hover:border-blue-300 transition-colors"
             wire:click="$set('statusFilter', 'all')">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $counts['all'] }}</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Status Tabs --}}
            <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1 flex-wrap">
                @foreach([
                    'all'      => 'All',
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ] as $value => $label)
                <button wire:click="$set('statusFilter', '{{ $value }}')"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-all
                               {{ $statusFilter === $value
                                    ? ($value === 'pending'  ? 'bg-amber-100 text-amber-700 border border-amber-300' :
                                       ($value === 'approved' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' :
                                       ($value === 'rejected' ? 'bg-red-100 text-red-700 border border-red-300' :
                                        'bg-white text-slate-700 shadow-sm border border-slate-200')))
                                    : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs opacity-75">({{ $counts[$value] }})</span>
                </button>
                @endforeach
            </div>

            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search by name, email, title, or content..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"/>
            </div>

            {{-- Sort --}}
            <select wire:model.live="sortBy"
                    class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white text-slate-700 shrink-0">
                <option value="latest">Latest First</option>
                <option value="highest">Highest Rated</option>
                <option value="lowest">Lowest Rated</option>
            </select>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Reviewer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Rating</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider min-w-[260px]">Review</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" wire:loading.class="opacity-50 pointer-events-none">

                    @forelse($reviews as $review)
                    <tr class="hover:bg-slate-50 transition-colors">

                        {{-- Reviewer --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-white uppercase">{{ substr($review->customer_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-xs leading-tight">{{ $review->customer_name }}</p>
                                    @if($review->customer_email)
                                    <p class="text-[11px] text-slate-400 leading-tight">{{ $review->customer_email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Product --}}
                        <td class="px-4 py-3">
                            @if($review->product)
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 text-[11px] font-medium px-2 py-1 rounded-md max-w-[140px] truncate">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <span class="truncate">{{ $review->product->name }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-500 text-[11px] font-medium px-2 py-1 rounded-md">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    General
                                </span>
                            @endif
                        </td>

                        {{-- Rating --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                                <span class="text-[11px] text-slate-500 ml-1">{{ $review->rating }}/5</span>
                            </div>
                        </td>

                        {{-- Review Content --}}
                        <td class="px-4 py-3">
                            @if($review->title)
                            <p class="font-semibold text-slate-800 text-xs leading-tight mb-0.5">{{ $review->title }}</p>
                            @endif
                            <p class="text-slate-500 text-xs leading-relaxed">
                                {{ Str::limit($review->description, 80) }}
                            </p>
                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-xs text-slate-600">{{ $review->created_at->format('d M Y') }}</p>
                            <p class="text-[11px] text-slate-400">{{ $review->created_at->format('h:i A') }}</p>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-4 py-3">
                            @if($review->status === 'approved')
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Approved
                                </span>
                            @elseif($review->status === 'rejected')
                                <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-200 text-[11px] font-semibold px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-semibold px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    Pending
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Approve (hidden if already approved) --}}
                                @if($review->status !== 'approved')
                                <button wire:click="approve({{ $review->id }})"
                                        wire:loading.attr="disabled"
                                        title="Approve"
                                        class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[11px] font-semibold px-2 py-1 rounded-lg transition-colors disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve
                                </button>
                                @endif

                                {{-- Reject (hidden if already rejected) --}}
                                @if($review->status !== 'rejected')
                                <button wire:click="reject({{ $review->id }})"
                                        wire:loading.attr="disabled"
                                        title="Reject"
                                        class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-[11px] font-semibold px-2 py-1 rounded-lg transition-colors disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                                @endif

                                {{-- Delete (always shown) --}}
                                <button wire:click="delete({{ $review->id }})"
                                        wire:confirm="Are you sure you want to permanently delete this review? This cannot be undone."
                                        wire:loading.attr="disabled"
                                        title="Delete"
                                        class="inline-flex items-center justify-center w-7 h-7 bg-slate-50 hover:bg-red-50 text-slate-400 hover:text-red-600 border border-slate-200 hover:border-red-200 rounded-lg transition-colors disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-600">No reviews found</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        @if($search)
                                            No reviews match "{{ $search }}" — try a different search term.
                                        @else
                                            There are no {{ $statusFilter !== 'all' ? $statusFilter : '' }} reviews yet.
                                        @endif
                                    </p>
                                </div>
                                @if($search)
                                <button wire:click="$set('search', '')"
                                        class="text-xs text-amber-600 hover:text-amber-700 font-medium underline underline-offset-2">
                                    Clear search
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Loading overlay on table --}}
        <div wire:loading.flex class="hidden items-center justify-center py-3 border-t border-slate-100 bg-slate-50/50">
            <svg class="w-4 h-4 text-amber-500 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-xs text-slate-500">Loading...</span>
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>

</div>
