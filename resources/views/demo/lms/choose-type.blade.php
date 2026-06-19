{{-- resources/views/demo/lms/choose-type.blade.php --}}
@extends('demo.layout')
@section('title', 'Choose Your Demo Type')

@section('content')
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #1a3faa;
            --brand-secondary: #7a5cff;
            --brand-accent: #f0b35a;
            --brand-green: #16a34a;
            --primary-glow: rgba(26, 63, 170, .12);
            --bg: #f4f6fb;
            --bg-card: #ffffff;
            --text: #0f1724;
            --text-muted: #6b7a99;
            --line: #e2e7f0;
        }

        /* ══ PAGE SHELL ══════════════════════════════════════════ */
        .ct-page {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            align-items: stretch;
        }

        /* ══ LEFT — FORM PANEL ═══════════════════════════════════ */
        .ct-form-panel {
            width: 52%;
            display: flex;
            flex-direction: column;
            padding: 44px 52px;
            background: var(--bg-card);
            overflow-y: auto;
        }

        .ct-form-logo {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 36px;
        }

        .ct-form-logo span {
            font-size: 14px;
            font-weight: 800;
            color: var(--brand-primary);
            letter-spacing: .2px;
        }

        .ct-form-logo .logo-dot {
            width: 30px;
            height: 30px;
            background: var(--brand-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ct-form-logo .logo-dot svg {
            color: #fff;
        }

        /* STEP BAR */
        .steps-bar {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .step-dot {
            width: 27px;
            height: 27px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            border: 2px solid var(--line);
            color: var(--text-muted);
            background: var(--bg);
            flex-shrink: 0;
            transition: all .2s;
        }

        .step-item.active .step-dot {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            color: #fff;
        }

        .step-item.done .step-dot {
            background: var(--brand-green);
            border-color: var(--brand-green);
            color: #fff;
        }

        .step-label {
            color: var(--text-muted);
            font-size: 11.5px;
            font-weight: 600;
        }

        .step-item.active .step-label {
            color: var(--text);
            font-weight: 700;
        }

        .step-line {
            flex: 1;
            height: 1.5px;
            background: var(--line);
            margin: 0 10px;
            min-width: 22px;
        }

        /* HEADER */
        .ct-form-header {
            margin-bottom: 24px;
        }

        .ct-form-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.4px;
            margin-bottom: 5px;
        }

        .ct-form-header p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* CARDS */
        .type-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .type-card {
            background: var(--bg-card);
            border: 2px solid var(--line);
            border-radius: 16px;
            padding: 20px 18px 18px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s, transform .18s;
            text-align: left;
        }

        .type-card:hover {
            transform: translateY(-3px);
        }

        .paid-card:hover {
            border-color: var(--brand-primary);
            box-shadow: 0 10px 32px var(--primary-glow);
        }

        .qr-card:hover {
            border-color: var(--brand-secondary);
            box-shadow: 0 10px 32px rgba(122, 92, 255, .15);
        }

        .skip-card:hover {
            border-color: var(--brand-green);
            box-shadow: 0 10px 32px rgba(22, 163, 74, .12);
        }

        .paid-card.selected {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .qr-card.selected {
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 4px rgba(122, 92, 255, .15);
        }

        .skip-card.selected {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
        }

        .card-ribbon {
            position: absolute;
            top: 11px;
            right: -26px;
            background: var(--brand-accent);
            color: #4a2600;
            font-size: 9px;
            font-weight: 800;
            padding: 4px 36px;
            transform: rotate(35deg);
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .card-radio {
            position: absolute;
            top: 14px;
            left: 14px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid var(--line);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .18s;
        }

        .paid-card.selected .card-radio {
            border-color: var(--brand-primary);
            background: var(--brand-primary);
        }

        .qr-card.selected .card-radio {
            border-color: var(--brand-secondary);
            background: var(--brand-secondary);
        }

        .skip-card.selected .card-radio {
            border-color: var(--brand-green);
            background: var(--brand-green);
        }

        .card-radio-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #fff;
            opacity: 0;
            transition: opacity .18s;
        }

        .type-card.selected .card-radio-dot {
            opacity: 1;
        }

        .card-body {
            padding-left: 28px;
        }

        .card-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 11px;
        }

        .paid-card .card-icon-wrap {
            background: var(--primary-glow);
            color: var(--brand-primary);
        }

        .qr-card .card-icon-wrap {
            background: rgba(122, 92, 255, .1);
            color: var(--brand-secondary);
        }

        .skip-card .card-icon-wrap {
            background: rgba(22, 163, 74, .1);
            color: var(--brand-green);
        }

        .card-name {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 3px;
        }

        .card-price {
            font-size: 19px;
            font-weight: 900;
            margin-bottom: 12px;
            line-height: 1.1;
        }

        .paid-card .card-price {
            color: var(--brand-primary);
        }

        .qr-card .card-price {
            color: var(--brand-secondary);
        }

        .skip-card .card-price {
            color: var(--brand-green);
        }

        .card-price span {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .card-feature {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .card-feature i {
            font-size: 13px;
            color: var(--brand-green);
        }

        /* FREE OPTION — hidden by default */
        .free-section {
            display: none;
            margin-bottom: 14px;
        }

        .free-section.visible {
            display: block;
        }

        .free-card-full {
            background: var(--bg-card);
            border: 2px solid var(--line);
            border-radius: 16px;
            padding: 18px 18px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s, transform .18s;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .free-card-full:hover {
            transform: translateY(-2px);
            border-color: var(--brand-green);
            box-shadow: 0 8px 24px rgba(22, 163, 74, .12);
        }

        .free-card-full.selected {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
        }

        .free-card-radio {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--line);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .18s;
        }

        .free-card-full.selected .free-card-radio {
            border-color: var(--brand-green);
            background: var(--brand-green);
        }

        .free-card-radio-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fff;
            opacity: 0;
            transition: opacity .18s;
        }

        .free-card-full.selected .free-card-radio-dot {
            opacity: 1;
        }

        .free-icon {
            width: 42px;
            height: 42px;
            background: rgba(22, 163, 74, .1);
            color: var(--brand-green);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .free-info {
            flex: 1;
        }

        .free-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 2px;
        }

        .free-sub {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .free-badge {
            background: rgba(22, 163, 74, .12);
            color: #0d6e31;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 99px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* SKIP BUTTON */
        .skip-trigger {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 11px 18px;
            background: transparent;
            border: 1.5px dashed var(--line);
            border-radius: 12px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s;
            margin-bottom: 18px;
        }

        .skip-trigger:hover {
            border-color: var(--brand-green);
            color: var(--brand-green);
            background: rgba(22, 163, 74, .04);
        }

        .skip-trigger i {
            font-size: 15px;
        }

        /* SUBMIT BTN */
        .submit-btn {
            width: 100%;
            padding: 14px 20px;
            background: var(--brand-primary);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: filter .18s, transform .18s;
            margin-bottom: 16px;
        }

        .submit-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .submit-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
        }

        .form-note {
            font-size: 11.5px;
            color: var(--text-muted);
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .form-note i {
            font-size: 13px;
            color: var(--brand-green);
        }

        /* ══ RIGHT HERO ══════════════════════════════════════════ */
        .ct-hero {
            flex: 1;
            background: linear-gradient(160deg, #1a3faa 0%, #062f75 55%, #050f28 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 44px;
            position: relative;
            overflow: hidden;
        }

        .ct-hero::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(122, 92, 255, .18);
            pointer-events: none;
        }

        .ct-hero::after {
            content: '';
            position: absolute;
            bottom: -70px;
            right: -60px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(240, 179, 90, .10);
            pointer-events: none;
        }

        /* Eyebrow */
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, .1);
            color: rgba(255, 255, 255, .88);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 99px;
            margin-bottom: 22px;
            width: fit-content;
            backdrop-filter: blur(4px);
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow .pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-live 2s infinite;
        }

        @keyframes pulse-live {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, .5);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
            }
        }

        .hero-heading {
            font-size: clamp(1.6rem, 2.4vw, 2.3rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -.4px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .hero-heading em {
            font-style: normal;
            color: var(--brand-accent);
        }

        .hero-sub {
            font-size: 13.5px;
            color: rgba(255, 255, 255, .65);
            line-height: 1.75;
            margin-bottom: 32px;
            max-width: 300px;
            position: relative;
            z-index: 1;
        }

        /* MENTOR CARD */
        .mentor-card {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 22px;
            padding: 26px 24px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(6px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .mentor-avatar-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-accent), #7a5cff);
            padding: 3px;
            margin-bottom: 14px;
            position: relative;
            flex-shrink: 0;
        }

        .mentor-avatar-ring img,
        .mentor-avatar-ring .mentor-initials {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid #062f75;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a3faa, #7a5cff);
            font-size: 22px;
            font-weight: 800;
            color: #fff;
        }

        .mentor-online {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #22c55e;
            border: 2.5px solid #062f75;
            animation: pulse-live 2s infinite;
        }

        .mentor-tag {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--brand-accent);
            margin-bottom: 5px;
        }

        .mentor-name {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }

        .mentor-role {
            font-size: 12px;
            color: rgba(255, 255, 255, .55);
            margin-bottom: 14px;
        }

        .mentor-quote {
            background: rgba(255, 255, 255, .06);
            border-left: 3px solid var(--brand-accent);
            border-radius: 0 10px 10px 0;
            padding: 10px 14px;
            font-size: 12px;
            color: rgba(255, 255, 255, .75);
            line-height: 1.65;
            font-style: italic;
            text-align: left;
            margin-bottom: 16px;
            width: 100%;
        }

        .mentor-chips {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 14px;
        }

        .mentor-chip {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .13);
            color: rgba(255, 255, 255, .75);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 11px;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .mentor-chip i {
            font-size: 12px;
            color: var(--brand-accent);
        }

        .mentor-cta {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: linear-gradient(135deg, var(--brand-accent), #e09230);
            color: #3d1f00;
            font-size: 12px;
            font-weight: 800;
            padding: 9px 20px;
            border-radius: 99px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s, filter .15s;
        }

        .mentor-cta:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        /* STATS */
        .hero-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 11px;
            position: relative;
            z-index: 1;
        }

        .hero-stat {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            padding: 14px 15px;
            backdrop-filter: blur(4px);
        }

        .hero-stat .val {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            display: block;
        }

        .hero-stat .lbl {
            font-size: 11px;
            color: rgba(255, 255, 255, .5);
            margin-top: 2px;
            display: block;
        }

        /* ══ RESPONSIVE ══════════════════════════════════════════ */
        @media(max-width: 900px) {
            .ct-page {
                flex-direction: column;
            }

            .ct-form-panel {
                width: 100%;
                padding: 32px 24px;
            }

            .ct-hero {
                padding: 40px 24px;
            }

            .type-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="ct-page">

        {{-- ══ LEFT: FORM ══════════════════════════════════════════ --}}
        <div class="ct-form-panel">

            {{-- Logo --}}
            <div class="ct-form-logo">
                <div class="logo-dot">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                </div>
                <span>LearnPro LMS</span>
            </div>

            {{-- Step bar --}}
            <div class="steps-bar">
                <div class="step-item active">
                    <div class="step-dot">1</div>
                    <span class="step-label">Demo Type</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-dot">2</div>
                    <span class="step-label">Your Info</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-dot">3</div>
                    <span class="step-label">Confirm</span>
                </div>
            </div>

            <div class="ct-form-header">
                <h2>Book Your LMS Demo</h2>
                <p>Choose how you'd like to experience the platform — instantly or with a dedicated session.</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div
                    style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 15px;margin-bottom:16px;font-size:13px;color:#991b1b;">
                    {{ $errors->first() }}
                </div>
            @endif
            @if (session('error'))
                <div
                    style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 15px;margin-bottom:16px;font-size:13px;color:#991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('lms.choose-type.store') }}" method="POST" id="demoTypeForm">
                @csrf
                <input type="hidden" name="demo_type" id="demoTypeInput" value="{{ old('demo_type', '') }}">

                {{-- PAID + QR cards (always visible) --}}
                <div class="type-cards">

                    {{-- Paid — Online Payment --}}
                    <div class="type-card paid-card {{ old('demo_type') === 'paid_online' ? 'selected' : '' }}"
                        onclick="selectType('paid_online', this)">
                        <div class="card-ribbon">Popular</div>
                        <div class="card-radio">
                            <div class="card-radio-dot"></div>
                        </div>
                        <div class="card-body">
                            <div class="card-icon-wrap">
                                <i class="ti ti-credit-card" aria-hidden="true"></i>
                            </div>
                            <div class="card-name">Online Payment</div>
                            <div class="card-price">₹{{ number_format($paidPrice, 0) }} <span>one-time</span></div>
                            <div class="card-feature"><i class="ti ti-check"></i> Instant booking confirmation</div>
                            <div class="card-feature"><i class="ti ti-check"></i> 1-on-1 dedicated session</div>
                            <div class="card-feature"><i class="ti ti-check"></i> Secure UPI / Card / Net banking</div>
                        </div>
                    </div>

                    {{-- QR Payment --}}
                    <div class="type-card qr-card {{ old('demo_type') === 'paid_qr' ? 'selected' : '' }}"
                        onclick="selectType('paid_qr', this)">
                        <div class="card-radio">
                            <div class="card-radio-dot"></div>
                        </div>
                        <div class="card-body">
                            <div class="card-icon-wrap">
                                <i class="ti ti-qrcode" aria-hidden="true"></i>
                            </div>
                            <div class="card-name">Pay via QR</div>
                            <div class="card-price">₹{{ number_format($paidPrice, 0) }} <span>one-time</span></div>
                            <div class="card-feature"><i class="ti ti-check"></i> Scan & pay with any UPI app</div>
                            <div class="card-feature"><i class="ti ti-check"></i> 1-on-1 dedicated session</div>
                            <div class="card-feature"><i class="ti ti-check"></i> Instant booking on payment</div>
                        </div>
                    </div>

                </div>

                {{-- FREE section — hidden until Skip clicked --}}
                <div class="free-section" id="freeSection">
                    <div class="free-card-full {{ old('demo_type') === 'free' ? 'selected' : '' }}"
                        onclick="selectType('free', this)" id="freeCard">
                        <div class="free-card-radio">
                            <div class="free-card-radio-dot"></div>
                        </div>
                        <div class="free-icon"><i class="ti ti-gift" aria-hidden="true"></i></div>
                        <div class="free-info">
                            <div class="free-name">Free Self-Guided Demo</div>
                            <div class="free-sub">Explore the platform at your own pace — no payment, no commitment.</div>
                        </div>
                        <div class="free-badge">₹0 Free</div>
                    </div>
                </div>

                {{-- Skip trigger --}}
                <button type="button" class="skip-trigger" id="skipBtn" onclick="revealFree()">
                    <i class="ti ti-arrow-right" aria-hidden="true"></i>
                    Skip — I'll try the free demo instead
                </button>
                @if (!$submitDetails)
                    <button type="submit" class="submit-btn" id="submitBtn" disabled>
                        <i class="ti ti-arrow-right" aria-hidden="true"></i>
                        <span id="submitLabel">Select a demo type to continue</span>
                    </button>
                @else
                    <div class="alert alert-success text-center mt-3">
                        <h5 class="mb-2">
                            <i class="fas fa-check-circle"></i>
                            Payment Successful !
                        </h5>

                        <p class="mb-0 mt-1">
                            Thank you! Your demo request has been successfully submitted.
                            Our team will contact you shortly with your demo access details.
                        </p>
                    </div>
                @endif
                <p class="form-note">
                    <i class="ti ti-lock-filled" aria-hidden="true"></i>
                    Secure checkout &nbsp;·&nbsp; No hidden charges &nbsp;·&nbsp; Cancel anytime
                </p>
            </form>

        </div>{{-- /ct-form-panel --}}

        {{-- ══ RIGHT: HERO ══════════════════════════════════════════ --}}
        <div class="ct-hero">

            <div class="hero-eyebrow">
                <div class="pulse"></div>
                Live Demo Available Now
            </div>

            <h1 class="hero-heading">
                Experience <em>smarter</em><br>learning — live
            </h1>

            <p class="hero-sub">
                See how LearnPro transforms training into measurable impact with AI-driven insights and beautiful
                interfaces.
            </p>

            {{-- Mentor card --}}
            {{-- <div class="mentor-card">
                <div class="mentor-avatar-ring">
                   
                    <div class="mentor-initials">RS</div>
                    <div class="mentor-online"></div>
                </div>
                <div class="mentor-tag">Your Demo Mentor</div>
                <div class="mentor-name">Rahul Sharma</div>
                <div class="mentor-role">Senior LMS Consultant · 8 yrs exp</div>
                <div class="mentor-quote">
                    "I'll walk you through the exact workflows your team will use daily — from course creation to analytics
                    dashboards."
                </div>
                <div class="mentor-chips">
                    <span class="mentor-chip"><i class="ti ti-school"></i> 500+ demos</span>
                    <span class="mentor-chip"><i class="ti ti-star"></i> 4.9 rating</span>
                    <span class="mentor-chip"><i class="ti ti-clock"></i> 45 min session</span>
                </div>
                <a href="#" class="mentor-cta">
                    <i class="ti ti-message-circle" aria-hidden="true"></i>
                    Chat before booking
                </a>
            </div> --}}

            {{-- Stats --}}
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="val">12,000+</span>
                    <span class="lbl">Students onboarded</span>
                </div>
                <div class="hero-stat">
                    <span class="val">98%</span>
                    <span class="lbl">Satisfaction rate</span>
                </div>
                <div class="hero-stat">
                    <span class="val">3 min</span>
                    <span class="lbl">Avg. setup time</span>
                </div>
                <div class="hero-stat">
                    <span class="val">50+</span>
                    <span class="lbl">Enterprise clients</span>
                </div>
            </div>

        </div>{{-- /ct-hero --}}

    </div>{{-- /ct-page --}}

    <script>
        const typeInput = document.getElementById('demoTypeInput');
        const submitBtn = document.getElementById('submitBtn');
        const submitLbl = document.getElementById('submitLabel');
        const skipBtn = document.getElementById('skipBtn');
        const freeSection = document.getElementById('freeSection');

        const labels = {
            paid_online: 'Continue to Online Payment →',
            paid_qr: 'Continue to QR Payment →',
            free: 'Continue with Free Demo →',
        };

        function selectType(type, card) {
            document.querySelectorAll('.type-card, .free-card-full').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            typeInput.value = type;
            submitBtn.disabled = false;
            submitLbl.textContent = labels[type] || 'Continue →';
        }

        function revealFree() {
            freeSection.classList.add('visible');
            skipBtn.style.display = 'none';
            freeSection.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            /* Auto-select free */
            const freeCard = document.getElementById('freeCard');
            selectType('free', freeCard);
        }

        /* Restore selection on validation error */
        const oldType = typeInput.value;
        if (oldType) {
            if (oldType === 'free') revealFree();
            const card = document.querySelector(`[onclick*="${oldType}"]`);
            if (card) selectType(oldType, card);
        }
    </script>
@endsection
