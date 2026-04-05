<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — Meharahouse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%);
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* ── Decorative background blurs ── */
        .blur-orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(80px);
            opacity: 0.25;
        }
        .blur-orb-gold {
            width: 480px; height: 480px;
            background: radial-gradient(circle, #F59E0B, transparent 70%);
            top: -120px; right: -120px;
        }
        .blur-orb-blue {
            width: 360px; height: 360px;
            background: radial-gradient(circle, #3B82F6, transparent 70%);
            bottom: -100px; left: -80px;
        }
        .blur-orb-amber-sm {
            width: 220px; height: 220px;
            background: radial-gradient(circle, #F59E0B, transparent 70%);
            bottom: 10%; right: 15%;
            opacity: 0.12;
        }

        /* ── Main card ── */
        .card {
            position: relative;
            z-index: 10;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 24px;
            padding: 56px 48px;
            max-width: 520px;
            width: calc(100% - 32px);
            text-align: center;
            box-shadow:
                0 32px 64px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.04) inset,
                0 1px 0 rgba(255, 255, 255, 0.08) inset;
        }

        /* ── Logo ── */
        .logo-wrap {
            margin: 0 auto 28px;
            text-align: center;
        }
        .logo-wrap img {
            height: 90px;
            width: auto;
            display: inline-block;
            filter: drop-shadow(0 4px 16px rgba(212, 160, 23, 0.35));
        }

        /* ── Status badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 9999px;
            padding: 6px 16px;
            margin-bottom: 24px;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #EF4444;
            animation: pulse-red 1.8s ease-in-out infinite;
        }
        @keyframes pulse-red {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.85); }
        }
        .status-text {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #FCA5A5;
        }

        /* ── Heading & message ── */
        .main-title {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #F8FAFC;
            line-height: 1.2;
            margin-bottom: 14px;
            letter-spacing: -0.3px;
        }
        .main-message {
            font-size: 15px;
            color: #94A3B8;
            line-height: 1.65;
            margin-bottom: 36px;
        }

        /* ── Animated hourglass / loading dots ── */
        .loading-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 36px;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #F59E0B;
            animation: bounce-dot 1.4s ease-in-out infinite;
        }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce-dot {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.5; }
            40%            { transform: translateY(-10px); opacity: 1; }
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            margin-bottom: 28px;
        }

        /* ── Back soon text ── */
        .back-soon {
            font-size: 13px;
            color: #64748B;
            margin-bottom: 0;
        }
        .back-soon strong {
            color: #F59E0B;
            font-weight: 600;
        }

        /* ── WhatsApp contact button ── */
        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 24px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.25);
        }
        .whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.35);
        }
        .whatsapp-btn svg { flex-shrink: 0; }

        /* ── Footer brand line ── */
        .brand-footer {
            margin-top: 40px;
            font-size: 12px;
            color: rgba(100, 116, 139, 0.7);
            letter-spacing: 0.06em;
        }
        .brand-footer span {
            color: rgba(245, 158, 11, 0.6);
            font-weight: 600;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .card { padding: 40px 28px; }
            .main-title { font-size: 22px; }
        }
    </style>
</head>
<body>

    {{-- Decorative background blurs --}}
    <div class="blur-orb blur-orb-gold"></div>
    <div class="blur-orb blur-orb-blue"></div>
    <div class="blur-orb blur-orb-amber-sm"></div>

    {{-- Main card --}}
    <div class="card">

        {{-- Logo --}}
        <div class="logo-wrap">
            <img src="/images/meharahouse-logo.png" alt="Mehra House">
        </div>

        {{-- Status badge --}}
        <div>
            <span class="status-badge">
                <span class="status-dot"></span>
                <span class="status-text">Temporarily Offline</span>
            </span>
        </div>

        {{-- Title --}}
        <h1 class="main-title">{{ $title }}</h1>

        {{-- Message --}}
        <p class="main-message">{{ $message }}</p>

        {{-- Animated loading dots --}}
        <div class="loading-dots" aria-label="Loading indicator">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>

        <hr class="divider">

        {{-- Back soon note --}}
        <p class="back-soon">
            We appreciate your patience. <strong>Meharahouse</strong> will be back online shortly.
        </p>

        {{-- WhatsApp contact — only shown when site_whatsapp setting is set --}}
        @if(!empty($whatsapp))
        <div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="whatsapp-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Contact Us on WhatsApp
            </a>
        </div>
        @endif

        {{-- Brand footer --}}
        <p class="brand-footer">&copy; {{ date('Y') }} <span>Meharahouse</span>. All rights reserved.</p>

    </div>

</body>
</html>
