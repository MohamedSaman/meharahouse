{{-- resources/views/livewire/admin/category.blade.php --}}
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Categories</h2>
            <p class="text-sm text-[#64748B]">Manage your product taxonomy</p>
        </div>
        <button class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php
        $categories = [
            ['name' => 'Electronics',   'count' => 120, 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'bg-blue-100 text-blue-600'],
            ['name' => 'Fashion',       'count' => 85,  'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'color' => 'bg-pink-100 text-pink-600'],
            ['name' => 'Home & Living', 'count' => 64,  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'bg-amber-100 text-amber-600'],
            ['name' => 'Sports',        'count' => 47,  'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'bg-green-100 text-green-600'],
            ['name' => 'Beauty',        'count' => 93,  'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color' => 'bg-purple-100 text-purple-600'],
            ['name' => 'Books',         'count' => 38,  'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'bg-teal-100 text-teal-600'],
        ];
        @endphp
        @foreach($categories as $cat)
        <div class="card p-5 card-hover">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl {{ $cat['color'] }} flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $cat['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-bold text-[#0F172A]">{{ $cat['name'] }}</h3>
                    <p class="text-xs text-[#64748B]">{{ $cat['count'] }} products</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary btn-sm flex-1 justify-center">Edit</button>
                <button class="btn-danger btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
