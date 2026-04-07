@php $addr = $order->shipping_address ?? []; $name = $addr['full_name'] ?? 'Customer'; @endphp
<x-layout-email>
    <p class="greeting">Hi {{ $name }}, your order has been delivered! 🎉</p>
    <p>Your Meharahouse order <strong>{{ $order->order_number }}</strong> has been delivered. We hope you love your new purchase!</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Order Number</span><span class="box-value">{{ $order->order_number }}</span></div>
        <div class="box-row"><span class="box-label">Total Paid</span><span class="box-value">Rs. {{ number_format($order->total) }}</span></div>
        <div class="box-row"><span class="box-label">Status</span><span class="box-value" style="color:#16A34A;">Delivered ✓</span></div>
    </div>
    <p>We'd love to hear what you think! Leave a review and help other shoppers discover our collections.</p>
    <a href="{{ url('/reviews') }}" class="btn">Leave a Review →</a>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">Thank you for shopping with Meharahouse. We hope to see you again soon!</p>
</x-layout-email>
