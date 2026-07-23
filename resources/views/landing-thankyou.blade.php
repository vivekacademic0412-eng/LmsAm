<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified — Thank You</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* =========================================================
       Same token system as the rest of the app, so this page
       matches automatically in light or dark mode.
    ========================================================= */
        :root {
            --brand-primary: #0947a8;
            --brand-secondary: #7a5cff;
            --brand-accent: #f0b35a;
            --brand-green: #16a34a;

            --bg: #eaf2ff;
            --bg2: #f0f5ff;
            --bg-card: #ffffff;
            --bg-card2: #f8fbff;

            --text: #0e1f36;
            --text-muted: #5a718a;

            --line: #d6e4f5;

            --primary: #859eff;
            --primary-dark: #0947a8;
            --primary-glow: rgba(13, 93, 209, .12);

            --accent: #f0b35a;
            --accent2: #7a5cff;

            --success: #16a34a;
            --danger: #dc2626;
            --warning: #d97706;
            --info: #0284c7;

            --radius: 20px;
            --radius-sm: 12px;
            --radius-xs: 8px;
            --shadow: 0 20px 50px rgba(13, 93, 209, .08);
            --shadow-card: 0 4px 20px rgba(14, 31, 54, .07);
        }

        [data-theme="dark"] {
            --bg: #08111f;
            --bg2: #0d1728;
            --bg-card: #111d2e;
            --bg-card2: #152338;

            --text: #f5f9ff;
            --text-muted: #91a7c5;

            --line: #1e3250;

            --primary: #7ea8ff;
            --primary-dark: #5f90ff;
            --primary-glow: rgba(126, 168, 255, .14);

            --accent: #f3c576;
            --accent2: #9b84ff;

            --shadow: 0 30px 70px rgba(0, 0, 0, .45);
            --shadow-card: 0 4px 24px rgba(0, 0, 0, .3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 15% 10%, var(--primary-glow), transparent 45%),
                radial-gradient(circle at 85% 90%, color-mix(in srgb, var(--accent2) 12%, transparent), transparent 45%),
                var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .ty-card {
            width: 100%;
            max-width: 480px;
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 48px 40px 40px;
            text-align: center;
            animation: card-in .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        @keyframes card-in {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---- animated check mark ---- */
        .ty-check-wrap {
            width: 92px;
            height: 92px;
            margin: 0 auto 28px;
            position: relative;
        }

        .ty-check-ring {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: color-mix(in srgb, var(--success) 12%, transparent);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: ring-pulse 2.2s ease-in-out infinite;
        }

        @keyframes ring-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 color-mix(in srgb, var(--success) 22%, transparent);
            }

            50% {
                box-shadow: 0 0 0 14px color-mix(in srgb, var(--success) 0%, transparent);
            }
        }

        .ty-check-circle {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ty-check-svg {
            width: 30px;
            height: 30px;
        }

        .ty-check-path {
            fill: none;
            stroke: #fff;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 26;
            stroke-dashoffset: 26;
            animation: draw-check .45s .35s cubic-bezier(.65, 0, .35, 1) forwards;
        }

        @keyframes draw-check {
            to {
                stroke-dashoffset: 0;
            }
        }

        /* ---- copy ---- */
        .ty-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--success);
            background: color-mix(in srgb, var(--success) 12%, transparent);
            padding: 5px 12px;
            border-radius: 999px;
            margin-bottom: 18px;
        }

        .ty-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .ty-body {
            font-size: 15px;
            line-height: 1.65;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .ty-email {
            color: var(--text);
            font-weight: 600;
        }

        /* ---- 24hr assurance strip ---- */
        .ty-eta {
            margin-top: 26px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
            background: var(--bg-card2);
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
        }

        .ty-eta-icon {
            flex: 0 0 auto;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-xs);
            background: color-mix(in srgb, var(--primary-dark) 12%, transparent);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .ty-eta-text {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .ty-eta-text strong {
            color: var(--text);
        }

        /* ---- steps ---- */
        .ty-steps {
            margin-top: 26px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ty-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .ty-step-dot {
            flex: 0 0 auto;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            margin-top: 1px;
        }

        .ty-step.is-done .ty-step-dot {
            background: var(--success);
            color: #fff;
        }

        .ty-step.is-pending .ty-step-dot {
            background: color-mix(in srgb, var(--primary-dark) 14%, transparent);
            color: var(--primary-dark);
        }

        .ty-step strong {
            color: var(--text);
            font-weight: 600;
        }

        /* ---- CTA ---- */
        .ty-actions {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ty-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 18px;
            border-radius: var(--radius-xs);
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: filter .15s ease, box-shadow .15s ease, transform .05s ease;
        }

        .ty-btn:active {
            transform: translateY(1px);
        }

        .ty-btn-primary {
            background: linear-gradient(135deg, var(--primary-dark), color-mix(in srgb, var(--primary-dark) 70%, var(--accent2)));
            color: #fff;
            box-shadow: 0 2px 10px color-mix(in srgb, var(--primary-dark) 35%, transparent);
        }

        .ty-btn-primary:hover {
            filter: brightness(1.05);
        }

        .ty-btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border-color: var(--line);
        }

        .ty-btn-ghost:hover {
            color: var(--text);
            border-color: var(--text-muted);
        }

        .ty-footer-note {
            margin-top: 22px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .ty-footer-note a {
            color: var(--primary-dark);
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .ty-card {
                padding: 36px 24px 30px;
            }

            .ty-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <div class="ty-card">

        <div class="ty-check-wrap">
            <div class="ty-check-ring">
                <div class="ty-check-circle">
                    <svg class="ty-check-svg" viewBox="0 0 24 24">
                        <path class="ty-check-path" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <span class="ty-eyebrow"><i class="fa-solid fa-shield-halved"></i> Email Verified</span>

        <h1 class="ty-title">Thank you, {{ $user->name }}</h1>

        <p class="ty-body">
            Your email{{-- --}}
            @isset($user->email)
                <span class="ty-email">{{ $user->email }}</span>
            @endisset
            has been verified successfully and your demo request is confirmed.
        </p>

        <div class="ty-eta">
            <div class="ty-eta-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="ty-eta-text">
                <strong>Our team will connect with you within 24 hours</strong><br>
                to help you get started with your demo access.
            </div>
        </div>

        <div class="ty-steps">
            <div class="ty-step is-done">
                <span class="ty-step-dot"><i class="fa-solid fa-check"></i></span>
                <span><strong>Email verified</strong> — completed just now</span>
            </div>
            <div class="ty-step is-pending">
                <span class="ty-step-dot">2</span>
                <span><strong>Our team reviews your request</strong> — within 24 hours</span>
            </div>
            <div class="ty-step is-pending">
                <span class="ty-step-dot">3</span>
                <span><strong>Demo access sent to your inbox</strong> — once confirmed</span>
            </div>
        </div>

        <div class="ty-actions">
            <a href="https://www.academicmantraservices.com" class="ty-btn ty-btn-primary">
                <i class="fa-solid fa-house"></i> Back to Home
            </a>

            <a href="mailto:support@academicmantraservices.com" class="ty-btn ty-btn-ghost">
                <i class="fa-regular fa-envelope"></i> Contact Support
            </a>
        </div>

        <p class="ty-footer-note">
            Didn't request this?
            <a href="mailto:support@academicmantraservices.com">
                Contact the Academic Mantra Support Team
            </a>.
        </p>
    </div>

</body>

</html>
