<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Meharahouse' }}</title>
    <style>
        body { margin: 0; padding: 0; background: #F8FAFC; font-family: 'Helvetica Neue', Arial, sans-serif; color: #334155; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #E2E8F0; }
        .header { background: #0F172A; padding: 28px 40px; text-align: center; }
        .header img { height: 50px; }
        .header h1 { color: #D4A017; font-size: 22px; margin: 8px 0 0; letter-spacing: 0.5px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 17px; font-weight: 700; color: #0F172A; margin-bottom: 12px; }
        p { font-size: 14px; line-height: 1.75; margin: 0 0 14px; color: #475569; }
        .box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 24px; margin: 20px 0; }
        .box-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #F1F5F9; font-size: 13px; }
        .box-row:last-child { border-bottom: none; }
        .box-label { color: #64748B; }
        .box-value { font-weight: 700; color: #0F172A; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge-gold { background: #FEF3C7; color: #92400E; }
        .badge-green { background: #DCFCE7; color: #166534; }
        .badge-blue  { background: #DBEAFE; color: #1E40AF; }
        .btn { display: inline-block; margin: 20px 0 10px; padding: 13px 32px; background: #D4A017; color: #0F172A; text-decoration: none; border-radius: 999px; font-weight: 800; font-size: 14px; }
        .divider { border: none; border-top: 1px solid #F1F5F9; margin: 24px 0; }
        .footer { background: #0F172A; padding: 20px 40px; text-align: center; }
        .footer p { color: #475569; font-size: 12px; margin: 0; }
        .footer a { color: #D4A017; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>MEHARAHOUSE</h1>
    </div>
    <div class="body">
        {{ $slot }}
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Meharahouse · <a href="{{ url('/') }}">mehrahouse.com</a></p>
        <p style="margin-top:6px;">Questions? Reply to this email or contact us on WhatsApp.</p>
    </div>
</div>
</body>
</html>
