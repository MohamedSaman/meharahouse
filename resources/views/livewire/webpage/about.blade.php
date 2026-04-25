{{-- resources/views/livewire/webpage/about.blade.php --}}
<div>
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-16">
        <div class="container-page text-center">
            <span class="section-label">Our Story</span>
            <h1 class="font-[Poppins] font-bold text-4xl text-white mt-2">About Mehra House</h1>
            <p class="text-[#64748B] mt-3 max-w-xl mx-auto">Redefining modest fashion with elegance — bringing premium quality, timeless designs, and confidence to modern women.</p>
        </div>
    </div>

    {{-- Mission --}}
    <section class="py-16 container-page">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="section-label">Who We Are</span>
                <h2 class="section-title mt-1">From a Humble Initiative<br>To a Trusted Brand</h2>
                <div class="gold-divider"></div>
                <p class="text-[#475569] leading-relaxed mb-4">At Mehra House, our journey began with passion, purpose, and a vision to redefine modest fashion with elegance. What started as a humble initiative through a WhatsApp community has now grown into a trusted brand, supported by over 13,000 loyal followers and a thriving social media presence exceeding 50,000 across platforms.</p>
                <p class="text-[#475569] leading-relaxed mb-4">Our inspiration is deeply rooted in creativity and connection. Zahra’s Day, our YouTube channel, achieved the remarkable milestone of earning the Silver Creator Award within just one year—reflecting the trust and engagement of a global audience who resonate with our story and style.</p>
                <p class="text-[#475569] leading-relaxed mb-6">From our beginnings in the UAE with a focused abaya collection, we have proudly expanded our reach, delivering to customers across the UAE, Sri Lanka, and Qatar. With the launch of MehraHouse.com, we step into a new chapter—bringing together a curated range of ladies’ wear and accessories designed for modern, confident women who value both modesty and sophistication.</p>
                <div class="grid grid-cols-3 gap-5">
                    @foreach(['50K+' => 'Social Followers', '13K+' => 'Loyal Customers', 'Silver' => 'Creator Award'] as $val => $label)
                    <div class="text-center p-4 bg-[#FFFBEB] rounded-xl">
                        <p class="font-[Poppins] font-bold text-xl text-[#0F172A]">{{ $val }}</p>
                        <p class="text-xs text-[#64748B] mt-0.5">{{ $label }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600&auto=format&fit=crop&q=80"
                     alt="Mehra House Team" class="w-full rounded-2xl shadow-xl object-cover h-full min-h-[400px]">
                <div class="absolute -bottom-5 -left-5 bg-white rounded-xl shadow-lg p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#F59E0B] flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#0F172A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-[#64748B]">Roots in</p>
                        <p class="font-bold text-sm text-[#0F172A]">UAE & Sri Lanka</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Values --}}
    <section class="py-16 bg-[#F8FAFC]">
        <div class="container-page text-center mb-10">
            <span class="section-label">Our Philosophy</span>
            <h2 class="section-title mt-1">Fashion as an Expression</h2>
            <div class="gold-divider mx-auto"></div>
            <p class="text-[#475569] mt-4 max-w-2xl mx-auto">At Mehra House, we believe fashion is more than clothing—it is an expression of identity, grace, and individuality. Our commitment is to offer premium quality, timeless designs, and a seamless shopping experience as we continue to grow and evolve with our community.</p>
        </div>
        <div class="container-page grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Premium Quality', 'desc' => 'We never compromise on quality. Every piece is carefully crafted and vetted for excellence.', 'color' => 'bg-[#FFFBEB] text-[#F59E0B]'],
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Community Driven', 'desc' => 'Our community is the heart of everything we do. We evolve and grow with you.', 'color' => 'bg-[#EFF6FF] text-blue-600'],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Timeless Elegance', 'desc' => 'Sophistication meets modesty in curated collections for the modern confident woman.', 'color' => 'bg-[#F0FDF4] text-green-600'],
            ] as $v)
            <div class="card p-6 text-center card-hover">
                <div class="w-14 h-14 rounded-2xl {{ $v['color'] }} flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $v['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-[Poppins] font-bold text-[#0F172A] mb-2">{{ $v['title'] }}</h3>
                <p class="text-sm text-[#475569] leading-relaxed">{{ $v['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </section>
</div>
