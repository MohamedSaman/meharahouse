@php $addr = $order->shipping_address ?? []; $name = $addr['full_name'] ?? 'Customer'; @endphp
<x-layout-email>
    <p class="greeting">Hi {{ $name }}, payment received! 💳</p>
    <p>We've successfully received your payment for order <strong>{{ $order->order_number }}</strong>. Thank you!</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Order Number</span><span class="box-value">{{ $order->order_number }}</span></div>
        <div class="box-row"><span class="box-label">Amount Received</span><span class="box-value" style="color:#16A34A;">Rs. {{ number_format($amount) }}</span></div>
        <div class="box-row"><span class="box-label">Order Total</span><span class="box-value">Rs. {{ number_format($order->total) }}</span></div>
        @if(($order->balance_amount ?? 0) > 0)
        <div class="box-row"><span class="box-label">Remaining Balance</span><span class="box-value" style="color:#DC2626;">Rs. {{ number_format($order->balance_amount) }}</span></div>
        @else
        <div class="box-row"><span class="box-label">Balance</span><span class="box-value" style="color:#16A34A;">Fully Paid ✓</span></div>
        @endif
    </div>
    <p>Your order is being processed. We'll notify you when it's dispatched.</p>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">This is an automated payment confirmation from Meharahouse.</p>
</x-layout-email>
