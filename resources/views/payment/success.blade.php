<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful — {{ \App\Models\Setting::get('site_name', 'Meharahouse') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f0fdf4; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,.10); padding: 40px 32px; max-width: 420px; width: 100%; text-align: center; }
        .check { width: 80px; height: 80px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 24px rgba(34,197,94,.3); }
        .check svg { width: 40px; height: 40px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
        h1 { font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .sub { font-size: 14px; color: #64748b; margin-bottom: 24px; }
        .order-box { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px; margin-bottom: 24px; }
        .order-label { font-size: 11px; text-transform: uppercase; letter-spacing: .8px; font-weight: 600; color: #94a3b8; margin-bottom: 4px; }
        .order-number { font-family: monospace; font-size: 22px; font-weight: 800; color: #f59e0b; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .detail-row:last-child { border: none; }
        .detail-row .lbl { color: #64748b; }
        .detail-row .val { font-weight: 600; color: #0f172a; }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; margin-bottom: 20px; }
        .status-dot { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 12px; font-size: 14px; font-weight: 700; text-decoration: none; transition: opacity .2s; margin: 4px; }
        .btn-primary { background: #0f172a; color: #fff; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn:hover { opacity: .88; }
        .note { margin-top: 20px; font-size: 12px; color: #94a3b8; line-height: 1.6; }
    </style>
</head>
<body>
<div class="card">
    <div class="check">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>

    <h1>Payment Successful!</h1>
    <p class="sub">Your payment has been confirmed and your order is being processed.</p>

    <div class="status-badge">
        <span class="status-dot"></span> Payment Confirmed
    </div>

    <div class="order-box">
        <div class="order-label">Order Number</div>
        <div class="order-number">{{ $order->order_number }}</div>
    </div>

    <div style="text-align:left; margin-bottom:20px;">
        <div class="detail-row">
            <span class="lbl">Amount Paid</span>
            <span class="val">Rs. {{ number_format($order->total, 2) }}</span>
        </div>
        <div class="detail-row">
            <span class="lbl">Payment Method</span>
            <span class="val">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
        </div>
        <div class="detail-row">
            <span class="lbl">Customer</span>
            <span class="val">{{ $order->shipping_address['full_name'] ?? 'Guest' }}</span>
        </div>
        <div class="detail-row">
            <span class="lbl">Status</span>
            <span class="val" style="color:#22c55e;">Payment Received</span>
        </div>
    </div>

    <a href="{{ route('webpage.shop') }}" class="btn btn-primary">Continue Shopping</a>
    <a href="{{ route('webpage.home') }}" class="btn btn-secondary">Home</a>

    <p class="note">
        A confirmation will be sent to you shortly.<br>
        Our team will process your order and keep you updated.
    </p>
</div>
</body>
</html>
