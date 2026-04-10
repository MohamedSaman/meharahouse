@php $addr = $order->shipping_address ?? []; $name = $addr['full_name'] ?? 'Customer'; @endphp
<x-layout-email>
    <p class="greeting">Hi {{ $name }}, your refund has been processed.</p>
    <p>We have successfully processed your refund for order <strong>{{ $order->order_number }}</strong>.</p>
    <div class="box">
        <div class="box-row"><span class="box-label">Order Number</span><span class="box-value">{{ $order->order_number }}</span></div>
        <div class="box-row"><span class="box-label">Refund Amount</span><span class="box-value" style="color:#16A34A;">Rs. {{ number_format($refund->amount) }}</span></div>
        <div class="box-row"><span class="box-label">Refund Method</span><span class="box-value">{{ ucfirst(str_replace('_', ' ', $refund->method)) }}</span></div>
        @if($refund->customer_bank_account)
        <div class="box-row"><span class="box-label">Transfer To Account</span><span class="box-value">{{ $refund->customer_bank_account }}</span></div>
        @endif
        @if($refund->reference_number)
        <div class="box-row"><span class="box-label">Reference / Transaction ID</span><span class="box-value">{{ $refund->reference_number }}</span></div>
        @endif
    </div>
    <p>The refund amount will be transferred to your bank account within 3–5 business days. If you have any questions, please contact us.</p>
    <hr class="divider">
    <p style="font-size:12px;color:#94A3B8;">This is an automated refund confirmation from Meharahouse.</p>
</x-layout-email>
