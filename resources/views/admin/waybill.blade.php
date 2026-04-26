<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waybill — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; background: #fff; }
        .page { max-width: 210mm; margin: 0 auto; padding: 16px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .logo-area h1 { font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px; }
        .logo-area p { font-size: 11px; color: #64748b; }
        .order-badge { text-align: right; }
        .order-badge .order-num { font-size: 18px; font-weight: 900; color: #0f172a; font-family: monospace; }
        .order-badge .date { font-size: 11px; color: #64748b; margin-top: 2px; }
        .wa-tag { display: inline-block; background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px solid #86efac; font-family: monospace; margin-top: 4px; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .box-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 8px; }
        .box p { font-size: 12px; color: #1e293b; line-height: 1.6; }
        .box strong { font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead th { background: #f8fafc; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 7px 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        .payment-box { border: 2px solid #f59e0b; border-radius: 6px; padding: 12px; background: #fffbeb; margin-bottom: 16px; }
        .payment-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }
        .payment-row.total { font-weight: 900; font-size: 15px; border-top: 1px solid #f59e0b; padding-top: 6px; margin-top: 4px; }
        .balance-due { color: #dc2626; font-weight: 700; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 12px; display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; }
        .sig-box { border: 1px dashed #cbd5e1; border-radius: 4px; padding: 8px 24px; font-size: 11px; color: #94a3b8; text-align: center; }
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Print button (hidden on print) --}}
    <div class="no-print" style="text-align:right;margin-bottom:12px;">
        <button onclick="window.print()" style="background:#f59e0b;color:#0f172a;font-weight:700;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px;">
            Print Waybill
        </button>
        <button onclick="window.close()" style="background:#f1f5f9;color:#475569;font-weight:600;border:1px solid #e2e8f0;padding:8px 16px;border-radius:6px;cursor:pointer;margin-left:8px;font-size:13px;">
            Close
        </button>
    </div>

    {{-- Header --}}
    <div class="header">
        <div class="logo-area">
            <h1>Meharahouse</h1>
            <p>Dubai &rarr; Sri Lanka Fashion</p>
        </div>
        <div class="order-badge">
            <div class="order-num">{{ $order->order_number }}</div>
            <div class="date">{{ $order->created_at->format('d M Y') }}</div>
            @php
                $phone  = $addr['phone'] ?? '';
                $digits = preg_replace('/[^0-9]/', '', $phone);
                $last4  = strlen($digits) >= 4 ? substr($digits, -4) : '';
            @endphp
            @if($last4)
            <div class="wa-tag">WA-{{ $last4 }}</div>
            @endif
        </div>
    </div>

    {{-- Delivery & Order Info --}}
    <div class="grid2">
        <div class="box">
            <div class="box-title">Deliver To</div>
            <p><strong>{{ $addr['full_name'] ?? '' }}</strong></p>
            <p>{{ $addr['phone'] ?? '' }}@if(!empty($addr['alt_phone'])) / {{ $addr['alt_phone'] }}@endif</p>
            <p>{{ $addr['address'] ?? '' }}</p>
            <p>{{ $addr['city'] ?? '' }}@if(!empty($addr['district'])), {{ $addr['district'] }}@endif</p>
            @if(!empty($addr['abaya_size']) || !empty($addr['abaya_model']))
            <p style="margin-top:6px;padding-top:6px;border-top:1px solid #e2e8f0;">
                <strong>Size:</strong> {{ $addr['abaya_size'] ?? '&mdash;' }}<br>
                <strong>Model:</strong> {{ $addr['abaya_model'] ?? '&mdash;' }}
            </p>
            @endif
        </div>
        <div class="box">
            <div class="box-title">Shipment Info</div>
            @if($order->shipmentBatch)
            <p><strong>Batch:</strong> {{ $order->shipmentBatch->batch_number }}</p>
            <p><strong>Batch Name:</strong> {{ $order->shipmentBatch->name }}</p>
            @endif
            @if($order->waybill_number)
            <p><strong>Waybill #:</strong> {{ $order->waybill_number }}</p>
            @endif
            @if($order->delivery_agent)
            <p><strong>Courier:</strong> {{ $order->delivery_agent }}</p>
            @endif
            <p style="margin-top:6px;"><strong>Status:</strong>
                <span class="status-badge" style="background:#dbeafe;color:#1d4ed8;">{{ $order->statusLabel() }}</span>
            </p>
        </div>
    </div>

    {{-- Items --}}
    @php
        // Active backorder item IDs (pending/repurchasing/ready/dispatched — not yet completed)
        $backorderItemIds = $order->backorders
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->pluck('order_item_id')
            ->toArray();

        $shipNow  = $order->items->whereNotIn('id', $backorderItemIds);
        $shipLater = $order->items->whereIn('id', $backorderItemIds);
    @endphp

    {{-- This Shipment --}}
    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:6px;">
        This Shipment
    </p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipNow->values() as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                <td style="text-align:right;">Rs. {{ number_format($item->price, 0) }}</td>
                <td style="text-align:right;">Rs. {{ number_format($item->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Next Shipment (Backorder) Section Removed as per request --}}

    {{-- Payment Summary --}}
    <div class="payment-box">
        <div class="payment-row"><span>Order Total</span><span>Rs. {{ number_format($order->total, 0) }}</span></div>
        <div class="payment-row"><span>Advance Paid</span><span>Rs. {{ number_format($order->advance_amount, 0) }}</span></div>
        <div class="payment-row total">
            <span>Balance Due</span>
            <span class="{{ $order->balanceDue() > 0 ? 'balance-due' : '' }}">
                Rs. {{ number_format($order->balanceDue(), 0) }}
            </span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div>
            <p>Printed: {{ now()->format('d M Y H:i') }}</p>
            @if($order->notes)<p>Note: {{ $order->notes }}</p>@endif
        </div>
        <div style="display:flex;gap:16px;">
            <div class="sig-box">Customer Signature<br><br><br></div>
            <div class="sig-box">Delivery Agent<br><br><br></div>
        </div>
    </div>

</div>
</body>
</html>
