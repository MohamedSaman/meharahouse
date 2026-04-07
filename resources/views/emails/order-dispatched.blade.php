@php $addr = $order->shipping_address ?? []; $name = $addr['full_name'] ?? 'Customer'; @endphp
<x-layout-email>
    <p class="greeting">Hi {{ $name }}, your order is on the way! 🚚</p>
    <p>Great news! Your Meharahouse order has been dispatched and is heading your way.</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Order Number</span><span class="box-value">{{ $order->order_number }}</span></div>
        <div class="box-row"><span class="box-label">Dispatched On</span><span class="box-value">{{ now()->format('d M Y') }}</span></div>
        <div class="box-row"><span class="box-label">Deliver To</span><span class="box-value">{{ $addr['address'] ?? '' }}, {{ $addr['city'] ?? '' }}</span></div>
        @if($order->shipmentBatch)
        <div class="box-row"><span class="box-label">Courier</span><span class="box-value">{{ $order->shipmentBatch->courier ?? '—' }}</span></div>
        <div class="box-row"><span class="box-label">Tracking</span><span class="box-value">{{ $order->shipmentBatch->tracking_number ?? '—' }}</span></div>
        @endif
    </div>
    <p>Please ensure someone is available to receive your parcel. If you have any questions, contact us and we'll help right away.</p>
    <a href="{{ url('/orders') }}" class="btn">Track Your Order →</a>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">This is an automated dispatch notification from Meharahouse.</p>
</x-layout-email>
