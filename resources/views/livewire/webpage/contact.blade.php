{{-- resources/views/livewire/webpage/contact.blade.php --}}
<div>
    <div class="bg-gradient-to-r from-[#0F172A] to-[#1E293B] py-12">
        <div class="container-page">
            <h1 class="font-[Poppins] font-bold text-3xl text-white">Contact Us</h1>
            <p class="text-[#64748B] mt-1 text-sm">We'd love to hear from you. Reach out anytime.</p>
        </div>
    </div>

    <section class="py-14 container-page">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Contact Form --}}
            <div class="card p-8">
                <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A] mb-1">Send a Message</h2>
                <p class="text-sm text-[#64748B] mb-6">We respond within 24 hours on business days.</p>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-input" placeholder="Selam">
                        </div>
                        <div>
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-input" placeholder="Tadesse">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" placeholder="selam@example.com">
                    </div>
                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-input" placeholder="+251 911 000 000">
                    </div>
                    <div>
                        <label class="form-label">Subject</label>
                        <select class="form-input">
                            <option>Order Inquiry</option>
                            <option>Product Question</option>
                            <option>Returns & Refunds</option>
                            <option>General Feedback</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Message</label>
                        <textarea rows="5" class="form-input resize-none" placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Send Message
                    </button>
                </form>
            </div>

            {{-- Contact Info --}}
            <div class="space-y-6">
                <div>
                    <span class="section-label">Get in Touch</span>
                    <h2 class="section-title mt-1">We're Here to Help</h2>
                    <div class="gold-divider"></div>
                    <p class="text-[#475569] text-sm leading-relaxed">Have questions about your order, a product, or anything else? Our dedicated support team is ready to assist you every step of the way.</p>
                </div>
                @foreach([
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'UAE Office', 'val' => 'Bustan Tower, Al Nahda, Sharjah, UAE'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Sri Lanka Office', 'val' => '107/9, Quarry Road, Dehiwela, Sri Lanka'],
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Email', 'val' => 'sales@mehrahouse.com'],
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Business Hours', 'val' => 'Mon–Sat: 9 AM – 6 PM'],
                ] as $info)
                <div class="flex items-start gap-4 p-4 card">
                    <div class="w-10 h-10 rounded-xl bg-[#FFFBEB] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-[Poppins] font-bold text-sm text-[#0F172A]">{{ $info['title'] }}</p>
                        <p class="text-sm text-[#475569]">{{ $info['val'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
