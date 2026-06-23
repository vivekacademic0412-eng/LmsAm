{{-- resources/views/demo/lms/thankyou.blade.php --}}
@extends('layouts.app')
@section('title', 'Booking Confirmed!')

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
            --brand-accent: #f0b35a;
            --brand-green: #16a34a;
            --brand-secondary: #7a5cff;
            --bg: #f4f6fb;
            --bg-card: #ffffff;
            --text: #0f1724;
            --text-muted: #6b7a99;
            --line: #e2e7f0;
        }

        .ty-page {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
        }

        .ty-shell {
            width: 100%;
            /* max-width:920px; */
        }

        /* ── TOP CONFETTI CARD ── */
        .ty-hero-card {
            background: linear-gradient(140deg, #1a3faa 0%, #062f75 60%, #050f28 100%);
            border-radius: 24px;
            padding: 48px 40px 42px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .ty-hero-card::before {
            content: '';
            position: absolute;
            top: -90px;
            right: -90px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(122, 92, 255, .2);
            pointer-events: none;
        }

        .ty-hero-card::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(240, 179, 90, .12);
            pointer-events: none;
        }

        .ty-check-ring {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .1);
            border: 2px solid rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
            animation: pop-in .45s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        @keyframes pop-in {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .ty-check-ring i {
            font-size: 36px;
            color: #22c55e;
        }

        .ty-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(34, 197, 94, .15);
            border: 1px solid rgba(34, 197, 94, .3);
            color: #86efac;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 99px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .ty-badge .dot {
            width: 6px;
            height: 6px;
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
                box-shadow: 0 0 0 5px rgba(34, 197, 94, 0);
            }
        }

        .ty-title {
            font-size: clamp(1.5rem, 3vw, 2.1rem);
            font-weight: 800;
            color: #fff;
            letter-spacing: -.4px;
            line-height: 1.2;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .ty-title em {
            font-style: normal;
            color: var(--brand-accent);
        }

        .ty-sub {
            font-size: 14px;
            color: rgba(255, 255, 255, .65);
            line-height: 1.7;
            max-width: 420px;
            margin: 0 auto 28px;
            position: relative;
            z-index: 1;
        }

        /* TYPE PILL */
        .ty-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 99px;
            padding: 8px 20px;
            color: rgba(255, 255, 255, .9);
            font-size: 13px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .ty-type-pill i {
            font-size: 16px;
            color: var(--brand-accent);
        }

        /* ── DETAIL CARDS ROW ── */
        .ty-details-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .ty-detail-card {
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px 20px;
        }

        .ty-detail-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ty-detail-label i {
            font-size: 14px;
        }

        /* Mentor mini */
        .mentor-mini {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mentor-mini-av {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            position: relative;
        }

        .mentor-mini-av .online {
            position: absolute;
            bottom: 1px;
            right: 1px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #22c55e;
            border: 2px solid #fff;
        }

        .mentor-mini-info .name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
        }

        .mentor-mini-info .role {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .mentor-mini-info .rating {
            font-size: 12px;
            color: var(--brand-accent);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Session info */
        .session-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }

        .session-row:last-child {
            margin-bottom: 0;
        }

        .session-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--bg);
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--brand-primary);
            flex-shrink: 0;
        }

        .session-row .si-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 1px;
        }

        .session-row .si-val {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text);
        }

        /* ── WHAT HAPPENS NEXT ── */
        .ty-next-card {
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px 22px;
            margin-bottom: 14px;
        }

        .ty-next-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ty-next-title i {
            font-size: 16px;
            color: var(--brand-primary);
        }

        .next-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .next-step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            position: relative;
            padding-bottom: 18px;
        }

        .next-step:last-child {
            padding-bottom: 0;
        }

        .next-step::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 34px;
            width: 1.5px;
            height: calc(100% - 14px);
            background: var(--line);
        }

        .next-step:last-child::before {
            display: none;
        }

        .ns-num {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--bg);
            border: 2px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--brand-primary);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .ns-num.done {
            background: var(--brand-green);
            border-color: var(--brand-green);
            color: #fff;
        }

        .ns-content .ns-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
            margin-top: 7px;
        }

        .ns-content .ns-desc {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ── ACTION BUTTONS ── */
        .ty-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .ty-btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--brand-primary);
            color: #fff;
            font-size: 13.5px;
            font-weight: 800;
            padding: 13px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: filter .18s, transform .18s;
        }

        .ty-btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .ty-btn-outline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: transparent;
            color: var(--text);
            font-size: 13.5px;
            font-weight: 700;
            padding: 13px 20px;
            border-radius: 12px;
            border: 1.5px solid var(--line);
            cursor: pointer;
            text-decoration: none;
            transition: border-color .18s, background .18s;
        }

        .ty-btn-outline:hover {
            border-color: var(--brand-primary);
            background: var(--bg);
        }

        /* FOOTER NOTE */
        .ty-footer {
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ty-footer i {
            font-size: 13px;
            color: var(--brand-green);
        }

        @media(max-width: 600px) {

            .ty-details-row,
            .ty-actions {
                grid-template-columns: 1fr;
            }

            .ty-hero-card {
                padding: 36px 22px 32px;
            }
        }
    </style>

    <div class="ty-page">
        <div class="ty-shell">

            {{-- ── HERO ── --}}
            <div class="ty-hero-card">
                <div class="ty-check-ring">
                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                </div>

                <div class="ty-badge">
                    <div class="dot"></div>
                    Booking Confirmed
                </div>

                <h1 class="ty-title">You're all set, <em>{{ $name ?? 'Explorer' }}!</em></h1>

                <p class="ty-sub">
                    Your LMS demo has been confirmed. Get ready to see how LearnPro transforms the way your team learns.
                </p>

                <div class="ty-type-pill">
                    @if (($demoType ?? '') === 'paid_online')
                        <i class="fas fa-credit-card" aria-hidden="true"></i> Paid Demo · Online Payment
                    @elseif(($demoType ?? '') === 'paid_qr')
                        <i class="fas fa-qrcode" aria-hidden="true"></i> Paid Demo · QR Payment
                    @else
                        <i class="fas fa-gift" aria-hidden="true"></i> Free Self-Guided Demo
                    @endif
                </div>
            </div>

            {{-- ── DETAILS ROW ── --}}
            {{-- <div class="ty-details-row">

             
                <div class="ty-detail-card">
                    <div class="ty-detail-label">
                        <i class="fas fa-user-circle" aria-hidden="true"></i>
                        Your Mentor
                    </div>
                    <div class="mentor-mini">
                        <div class="mentor-mini-av">
                            RS
                            <div class="online"></div>
                        </div>
                        <div class="mentor-mini-info">
                            <div class="name">Rahul Sharma</div>
                            <div class="role">Senior LMS Consultant</div>
                            <div class="rating">
                                <i class="fas fa-star-filled" style="font-size:12px"></i>
                                4.9 · 500+ demos
                            </div>
                        </div>
                    </div>
                </div>

              
                <div class="ty-detail-card">
                    <div class="ty-detail-label">
                        <i class="fas fa-calendar" aria-hidden="true"></i>
                        Session Details
                    </div>
                    <div class="session-row">
                        <div class="session-icon"><i class="fas fa-clock" aria-hidden="true"></i></div>
                        <div>
                            <div class="si-label">Duration</div>
                            <div class="si-val">45 minutes</div>
                        </div>
                    </div>
                    <div class="session-row">
                        <div class="session-icon"><i class="fas fa-device-laptop" aria-hidden="true"></i></div>
                        <div>
                            <div class="si-label">Format</div>
                            <div class="si-val">
                                @if (($demoType ?? '') === 'free')
                                    Self-guided platform walkthrough
                                @else
                                    Live 1-on-1 video session
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="session-row">
                        <div class="session-icon"><i class="fas fa-mail" aria-hidden="true"></i></div>
                        <div>
                            <div class="si-label">Confirmation sent to</div>
                            <div class="si-val">{{ $email ?? 'your email' }}</div>
                        </div>
                    </div>
                </div>

            </div> --}}

            {{-- ── WHAT HAPPENS NEXT ── --}}
            <div class="ty-next-card">
                <div class="ty-next-title">
                    <i class="fas fa-check" aria-hidden="true"></i>
                    What happens next
                </div>
                <div class="next-steps">
                    <div class="next-step">
                        <div class="ns-num done"><i class="fas fa-check" style="font-size:13px"></i></div>
                        <div class="ns-content">
                            <div class="ns-title">Booking confirmed</div>
                            <div class="ns-desc">Your demo slot is reserved and a confirmation email is on its way.</div>
                        </div>
                    </div>

                    <div class="next-step">
                        <div class="ns-num">2</div>
                        <div class="ns-content">
                            <div class="ns-title">
                              
                               
                                    Receive Your Demo Access Link
                              
                            </div>
                            <div class="ns-desc">
                              
                                    A secure demo access link will be shared with you via email.
                               
                            </div>
                        </div>
                    </div>

                    <div class="next-step">
                        <div class="ns-num">3</div>
                        <div class="ns-content">
                            <div class="ns-title">
                                Watch Your Demo Session
                            </div>
                            <div class="ns-desc">
                                Access the demo session video and explore the platform features, workflow, and assignment
                                submission process at your convenience.
                            </div>
                        </div>
                    </div>
                    @if (($demoType ?? '') === 'paid_qr')
                        <div class="next-step">
                            <div class="ns-num">4</div>
                            <div class="ns-content">
                                <div class="ns-title">
                                    Submit Assignment & Earn Certificate
                                </div>
                                <div class="ns-desc">
                                    Complete and submit the assignment provided during the demo. Upon successful submission,
                                    you
                                    will receive your completion certificate.
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ── CTA BUTTONS ── --}}
            <div class="ty-actions">
                <a href="{{ route('dashboard') }}" class="ty-btn-primary">
                    <i class="fas fa-layout-dashboard" aria-hidden="true"></i>
                    Go to Dashboard
                </a>
                <a href="{{ route('login') }}" class="ty-btn-outline">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    Back to Home
                </a>
            </div>

            <div class="ty-footer">
                <i class="fas fa-headset" aria-hidden="true"></i>
                Questions? Reach us at <strong>support@learnpro.in</strong>
                &nbsp;·&nbsp;
                <i class="fas fa-lock" aria-hidden="true"></i>
                All data secured by 256-bit SSL
            </div>

        </div>
    </div>
@endsection
