<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Checkout</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=PayPalSansBig-Regular:wght@400;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; }

        .card { background: #fff; border-radius: 16px; box-shadow: 0 2px 20px rgba(0,0,0,.09); width: 100%; max-width: 400px; overflow: hidden; }

        /* Header */
        .header { background: #003087; padding: 18px 24px; display: flex; align-items: center; justify-content: center; }
        .pp-logo { display: flex; align-items: center; gap: 0; }
        .pp-logo .p1 { font-size: 26px; font-weight: 900; color: #009cde; font-style: italic; letter-spacing: -1px; }
        .pp-logo .p2 { font-size: 26px; font-weight: 900; color: #012169; font-style: italic; letter-spacing: -1px; background: #fff; padding: 0 2px; border-radius: 4px; }
        .pp-logo .text { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -.5px; margin-left: 6px; font-style: italic; }

        /* Amount panel */
        .amount-panel { padding: 20px 24px; background: #f5f7fa; border-bottom: 1px solid #e8ecef; text-align: center; }
        .merchant-name { font-size: 16px; font-weight: 700; color: #2c2e2f; }
        .amount-label  { font-size: 12px; color: #6c7378; margin-top: 4px; }
        .amount-value  { font-size: 32px; font-weight: 800; color: #2c2e2f; margin-top: 6px; }
        .amount-cur    { font-size: 16px; font-weight: 600; color: #6c7378; }
        .order-num     { font-size: 12px; color: #6c7378; margin-top: 6px; }

        /* Tabs */
        .tabs { display: flex; border-bottom: 2px solid #e8ecef; }
        .tab  { flex: 1; padding: 14px; text-align: center; font-size: 13px; font-weight: 600; color: #6c7378; cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all .2s; }
        .tab.active { color: #003087; border-bottom-color: #003087; }

        /* Form */
        .form-body { padding: 24px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; font-size: 12px; font-weight: 600; color: #6c7378; margin-bottom: 5px; }
        input[type=email], input[type=password], input[type=text] {
            width: 100%; padding: 11px 14px; border: 1.5px solid #c8d0da; border-radius: 8px;
            font-size: 14px; color: #2c2e2f; outline: none; transition: border-color .2s; background: #fff;
        }
        input:focus { border-color: #009cde; box-shadow: 0 0 0 3px rgba(0,156,222,.12); }

        /* Buttons */
        .btn-paypal {
            width: 100%; padding: 13px; background: #ffc439; border: none; border-radius: 25px;
            font-size: 15px; font-weight: 700; color: #003087; cursor: pointer;
            transition: background .2s, transform .1s; margin-top: 6px;
        }
        .btn-paypal:hover { background: #f5ba31; }
        .btn-card {
            width: 100%; padding: 13px; background: #003087; border: none; border-radius: 25px;
            font-size: 15px; font-weight: 700; color: #fff; cursor: pointer;
            transition: background .2s; margin-top: 6px;
        }
        .btn-card:hover { background: #002570; }
        .divider { display: flex; align-items: center; gap: 12px; margin: 16px 0; }
        .divider hr { flex: 1; border: none; border-top: 1px solid #e8ecef; }
        .divider span { font-size: 12px; color: #9da3a9; }

        /* Card fields inside PayPal tab */
        .card-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        /* Processing */
        .processing { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 99; align-items: center; justify-content: center; flex-direction: column; gap: 16px; }
        .processing.show { display: flex; }
        .pp-spin { width: 52px; height: 52px; border: 4px solid rgba(255,196,57,.3); border-top-color: #ffc439; border-radius: 50%; animation: spin .9s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .processing p { color: #fff; font-weight: 600; font-size: 15px; }

        /* Footer */
        .card-footer { padding: 14px 24px; background: #f5f7fa; border-top: 1px solid #e8ecef; text-align: center; font-size: 11px; color: #9da3a9; }
        .card-footer a { color: #009cde; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">

    {{-- Header --}}
    <div class="header">
        <div class="pp-logo">
            <span class="p1" style="color:#009cde;background:none;">P</span>
            <span class="p1" style="color:#012169;background:none;">P</span>
            <span class="text">PayPal</span>
        </div>
    </div>

    {{-- Amount --}}
    <div class="amount-panel">
        <div class="merchant-name">{{ \App\Models\Setting::get('site_name', 'Meharahouse') }}</div>
        <div class="amount-label">Amount to pay</div>
        <div class="amount-value"><span class="amount-cur">Rs. </span>{{ number_format($order->total, 2) }}</div>
        <div class="order-num">Order #{{ $order->order_number }}</div>
    </div>

    {{-- Tabs --}}
    <div class="tabs" id="tabs">
        <div class="tab active" onclick="switchTab('paypal')">PayPal</div>
        <div class="tab" onclick="switchTab('card')">Debit / Credit Card</div>
    </div>

    {{-- PayPal login tab --}}
    <div id="tab-paypal" class="form-body">
        <form method="POST" action="{{ route('payment.paypal.process', $order->order_number) }}"
              onsubmit="showProcessing()">
            @csrf
            <div class="form-group">
                <label>Email or mobile number</label>
                <input type="email" value="{{ $order->shipping_address['email'] ?? '' }}" placeholder="Email or mobile number" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-paypal">Log In</button>
        </form>
        <div class="divider"><hr><span>or</span><hr></div>
        <p style="text-align:center;font-size:12px;color:#6c7378;">Don't have a PayPal account? <a href="#" onclick="switchTab('card');return false;" style="color:#009cde;font-weight:600;">Pay with card</a></p>
    </div>

    {{-- Card tab --}}
    <div id="tab-card" class="form-body" style="display:none">
        <form method="POST" action="{{ route('payment.paypal.process', $order->order_number) }}"
              onsubmit="showProcessing()">
            @csrf
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" placeholder="1234 5678 9012 3456" oninput="formatCard(this)" maxlength="19" required>
            </div>
            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" placeholder="Full name" required>
            </div>
            <div class="card-row">
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="text" placeholder="MM / YY" maxlength="7" oninput="formatExpiry(this)" required>
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" placeholder="•••" maxlength="4" required>
                </div>
            </div>
            <div class="form-group">
                <label>Billing Email</label>
                <input type="email" value="{{ $order->shipping_address['email'] ?? '' }}" placeholder="your@email.com" required>
            </div>
            <button type="submit" class="btn-card">Pay Rs. {{ number_format($order->total, 2) }}</button>
        </form>
    </div>

    <div class="card-footer">
        <a href="#">Privacy</a> &nbsp;·&nbsp; <a href="#">Legal</a> &nbsp;·&nbsp; <a href="#">Contact</a>
        &nbsp;·&nbsp; © PayPal, Inc. All rights reserved.
    </div>
</div>

{{-- Processing overlay --}}
<div class="processing" id="processingOverlay">
    <div class="pp-spin"></div>
    <p>Processing your payment…</p>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tab-paypal').style.display = tab === 'paypal' ? 'block' : 'none';
    document.getElementById('tab-card').style.display   = tab === 'card'   ? 'block' : 'none';
    document.querySelectorAll('.tab').forEach((el, i) => {
        el.classList.toggle('active', (tab === 'paypal' && i === 0) || (tab === 'card' && i === 1));
    });
}
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
