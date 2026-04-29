@php $addr = $order->shipping_address ?? []; $name = $addr['full_name'] ?? 'Customer'; @endphp
<x-layout-email>
    <p class="greeting">Hi {{ $name }}, payment receipt rejected</p>
    <p>Unfortunately, the payment receipt you uploaded for order <strong>{{ $order->order_number }}</strong> could not be verified.</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Order Number</span><span class="box-value">{{ $order->order_number }}</span></div>
        <div class="box-row"><span class="box-label">Order Total</span><span class="box-value">Rs. {{ number_format($order->total) }}</span></div>
        <div class="box-row"><span class="box-label">Status</span><span class="box-value" style="color:#DC2626;">Receipt Rejected</span></div>
    </div>
    <p><strong>What to do next:</strong> Please log in to your account, go to your order details, and re-upload a valid payment receipt. If you believe this is an error, please contact us.</p>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">This is an automated notification from Meharahouse.</p>
</x-layout-email>
