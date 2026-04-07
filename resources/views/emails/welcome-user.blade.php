<x-layout-email>
    <p class="greeting">Welcome to Meharahouse, {{ $user->name }}! 🎉</p>
    <p>Your account has been created successfully. You can now shop our exclusive collections, track your orders, and enjoy a seamless experience.</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Name</span><span class="box-value">{{ $user->name }}</span></div>
        <div class="box-row"><span class="box-label">Email</span><span class="box-value">{{ $user->email }}</span></div>
        <div class="box-row"><span class="box-label">Member Since</span><span class="box-value">{{ $user->created_at->format('d M Y') }}</span></div>
    </div>
    <p>Here's what you can do with your account:</p>
    <div class="box">
        <div class="box-row"><span>🛍️</span><span>Browse and order from our premium collections</span></div>
        <div class="box-row"><span>📦</span><span>Track your orders in real time</span></div>
        <div class="box-row"><span>💳</span><span>Save your delivery details for faster checkout</span></div>
        <div class="box-row"><span>⭐</span><span>Leave reviews and help others discover us</span></div>
    </div>
    <a href="{{ url('/shop') }}" class="btn">Start Shopping →</a>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">If you did not create this account, please ignore this email or contact us immediately.</p>
</x-layout-email>
