<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayHere — Secure Payment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; }

        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.10); width: 100%; max-width: 420px; overflow: hidden; }

        /* Header */
        .header { background: linear-gradient(135deg, #ff6600, #e55a00); padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .logo span { color: #ffd580; }
        .secure-badge { font-size: 11px; color: rgba(255,255,255,.85); display: flex; align-items: center; gap: 4px; }

        /* Merchant info */
        .merchant { padding: 16px 24px; background: #fff8f3; border-bottom: 1px solid #ffe4cc; display: flex; align-items: center; justify-content: space-between; }
        .merchant-name { font-size: 13px; font-weight: 600; color: #333; }
        .merchant-sub  { font-size: 11px; color: #888; margin-top: 2px; }
        .amount-big { font-size: 24px; font-weight: 800; color: #e55a00; }
        .amount-cur { font-size: 13px; color: #888; }

        /* Order ref */
        .order-ref { padding: 10px 24px; background: #fafafa; border-bottom: 1px solid #eee; font-size: 12px; color: #666; }
        .order-ref span { font-weight: 600; color: #333; }

        /* Form */
        .form-body { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .4px; }
        input[type=text], input[type=email] {
            width: 100%; padding: 11px 14px; border: 1.5px solid #dde1e7; border-radius: 10px;
            font-size: 14px; color: #1a1a1a; outline: none; transition: border-color .2s;
            background: #fafbfc;
        }
        input:focus { border-color: #ff6600; background: #fff; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .card-number-wrap { position: relative; }
        .card-icons { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); display: flex; gap: 4px; }
        .card-icon { width: 28px; height: 18px; border-radius: 3px; font-size: 8px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .visa { background: #1a1f71; color: #fff; }
        .mc   { background: #eb001b; color: #fff; }

        /* Pay button */
        .btn-pay {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #ff6600, #e04e00);
            color: #fff; border: none; border-radius: 12px; font-size: 15px; font-weight: 700;
            cursor: pointer; letter-spacing: .3px; margin-top: 8px;
            transition: opacity .2s, transform .1s;
        }
        .btn-pay:hover { opacity: .93; transform: translateY(-1px); }
        .btn-pay:active { transform: translateY(0); }

        /* Processing overlay */
        .processing { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 99; align-items: center; justify-content: center; flex-direction: column; gap: 16px; }
        .processing.show { display: flex; }
        .spinner { width: 48px; height: 48px; border: 4px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .processing p { color: #fff; font-weight: 600; font-size: 14px; }

        /* Footer */
        .footer { margin-top: 16px; text-align: center; font-size: 11px; color: #aaa; }
        .footer a { color: #ff6600; text-decoration: none; }

        /* SSL badge */
        .ssl { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 12px; font-size: 11px; color: #888; }
        .ssl-icon { width: 14px; height: 14px; fill: #27ae60; }
    </style>
</head>
<body>

<div class="card">

    {{-- Header --}}
    <div class="header">
        <div class="logo">Pay<span>Here</span></div>
        <div class="secure-badge">
            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
            Secure Payment
        </div>
    </div>

    {{-- Merchant + amount --}}
    <div class="merchant">
        <div>
            <div class="merchant-name">{{ \App\Models\Setting::get('site_name', 'Meharahouse') }}</div>
            <div class="merchant-sub">Online Purchase</div>
        </div>
        <div style="text-align:right">
            <div class="amount-cur">LKR / Rs.</div>
            <div class="amount-big">{{ number_format($order->total, 2) }}</div>
        </div>
    </div>

    {{-- Order ref --}}
    <div class="order-ref">Order Reference: <span>{{ $order->order_number }}</span></div>

    {{-- Payment form --}}
    <form class="form-body" method="POST" action="{{ route('payment.payhere.process', $order->order_number) }}"
          onsubmit="showProcessing()">
        @csrf

        <div class="form-group">
            <label>Card Number</label>
            <div class="card-number-wrap">
                <input type="text" placeholder="4111 1111 1111 1111" maxlength="19"
                       oninput="formatCard(this)" required>
                <div class="card-icons">
                    <div class="card-icon visa">VISA</div>
                    <div class="card-icon mc">MC</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Cardholder Name</label>
            <input type="text" placeholder="Name as on card" required>
        </div>

        <div class="row-2">
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="text" placeholder="MM / YY" maxlength="7"
                       oninput="formatExpiry(this)" required>
            </div>
            <div class="form-group">
                <label>CVV</label>
                <input type="text" placeholder="•••" maxlength="3" required>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" value="{{ $order->shipping_address['email'] ?? '' }}" placeholder="your@email.com" required>
        </div>

        <button type="submit" class="btn-pay">
            🔒 &nbsp; Pay Rs. {{ number_format($order->total, 2) }} Securely
        </button>
    </form>
</div>

{{-- Processing overlay --}}
<div class="processing" id="processingOverlay">
    <div class="spinner"></div>
    <p>Processing your payment…</p>
</div>

<div class="footer">
    Powered by <a href="#">PayHere</a> &nbsp;|&nbsp; <a href="#">Privacy Policy</a> &nbsp;|&nbsp; <a href="#">Help</a>
    <div class="ssl">
        <svg class="ssl-icon" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
        256-bit SSL Encrypted &amp; PCI DSS Compliant
    </div>
</div>

<script>
function showProcessing() {
    document.getElementById('processingOverlay').classList.add('show');
}
function formatCard(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
}
function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0,2) + ' / ' + v.substring(2);
    input.value = v;
}
</script>
</body>
</html>
