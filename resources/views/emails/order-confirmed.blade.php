@php $addr = $order->shipping_address ?? []; $name = $addr['full_name'] ?? 'Customer'; @endphp
<x-layout-email>
    <p class="greeting">Hi {{ $name }}, your order is confirmed! ✅</p>
    <p>Thank you for shopping with Meharahouse. We've received your order and it's being processed with care.</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Order Number</span><span class="box-value">{{ $order->order_number }}</span></div>
        <div class="box-row"><span class="box-label">Date</span><span class="box-value">{{ $order->created_at->format('d M Y') }}</span></div>
        <div class="box-row"><span class="box-label">Total</span><span class="box-value">Rs. {{ number_format($order->total) }}</span></div>
        <div class="box-row"><span class="box-label">Payment Status</span><span class="box-value">{{ ucfirst($order->payment_status) }}</span></div>
        <div class="box-row"><span class="box-label">Ship To</span><span class="box-value">{{ $addr['address'] ?? '' }}, {{ $addr['city'] ?? '' }}</span></div>
    </div>
    @if($order->items && $order->items->count())
    <p style="font-weight:700;color:#0F172A;margin-bottom:8px;">Items Ordered</p>
    <div class="box">
        @foreach($order->items as $item)
        <div class="box-row">
            <span class="box-label">{{ $item->product_name }} × {{ $item->quantity }}</span>
            <span class="box-value">Rs. {{ number_format($item->price * $item->quantity) }}</span>
        </div>
        @endforeach
    </div>
    @endif
    <p>We'll notify you when your order is dispatched. If you have any questions, feel free to contact us.</p>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">This is an automated confirmation email from Meharahouse.</p>
</x-layout-email>
