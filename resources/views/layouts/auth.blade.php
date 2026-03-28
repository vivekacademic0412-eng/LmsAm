<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Academic Mantra Login' }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    <link rel="shortcut icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.webp') }}">
    <style>
        :root {
            --bg1: #eaf2ff;
            --bg2: #f2f4ff;
            --bg3: #ffffff;
            --card: #ffffff;
            --line: #d6e1f1;
            --text: #1a283f;
            --muted: #60728c;
            --primary: #0d5dd1;
            --accent: #f0b35a;
            --accent-2: #7a5cff;
        }
        html[data-theme="dark"] {
            --bg1: #0a0f1a;
            --bg2: #0f1522;
            --bg3: #141c2c;
            --card: #101a2b;
            --line: #24324a;
            --text: #e8effd;
            --muted: #a8bad6;
            --primary: #6ca8ff;
            --accent: #f2c277;
        }
        * { box-sizing: border-box; }
        @keyframes authFade {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 12% 8%, rgba(79, 140, 255, 0.22) 0%, rgba(79, 140, 255, 0) 42%),
                radial-gradient(circle at 88% 12%, rgba(124, 180, 255, 0.16) 0%, rgba(124, 180, 255, 0) 45%),
                radial-gradient(circle at 55% 18%, rgba(122, 92, 255, 0.14) 0%, rgba(122, 92, 255, 0) 48%),
                linear-gradient(160deg, var(--bg1), var(--bg2), var(--bg3));
            display: grid;
            place-items: center;
            padding: 20px;
            color: var(--text);
            animation: authFade 320ms ease-out;
        }
        .auth-shell {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 22px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 40px 90px rgba(7, 18, 34, 0.45);
            animation: authFade 420ms ease-out;
        }
        html[data-theme="dark"] .auth-shell {
            background: rgba(16, 26, 43, 0.95);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 40px 90px rgba(0, 0, 0, 0.6);
        }
        .auth-top-actions {
            position: fixed;
            top: 14px;
            right: 14px;
            z-index: 10;
        }
        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            display: grid;
            place-content: center;
            cursor: pointer;
            transition: 160ms ease;
            padding: 0;
        }
        .icon-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(13, 39, 82, 0.16);
        }
        .theme-toggle {
            position: relative;
            overflow: hidden;
        }
        .theme-toggle .sun-icon,
        .theme-toggle .moon-icon {
            position: absolute;
            left: 50%;
            top: 50%;
            transition: transform 260ms ease, opacity 220ms ease;
        }
        .theme-toggle .sun-icon {
            opacity: 1;
            transform: translate(-50%, -50%) translateY(0) rotate(0deg) scale(1);
        }
        .theme-toggle .moon-icon {
            opacity: 0;
            transform: translate(-50%, -50%) translateY(10px) rotate(-45deg) scale(0.7);
        }
        html[data-theme="dark"] .theme-toggle .sun-icon {
            opacity: 0;
            transform: translate(-50%, -50%) translateY(-10px) rotate(40deg) scale(0.7);
        }
        html[data-theme="dark"] .theme-toggle .moon-icon {
            opacity: 1;
            transform: translate(-50%, -50%) translateY(0) rotate(0deg) scale(1);
        }
        .auth-brand {
            padding: 36px;
            color: #fff;
            background:
                linear-gradient(160deg, rgba(11, 32, 58, 0.92), rgba(54, 45, 120, 0.82), rgba(15, 45, 80, 0.85)),
                url("https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80");
            background-size: cover;
            background-position: center;
            display: grid;
            align-content: center;
            gap: 18px;
            position: relative;
        }
        .auth-brand::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(6, 18, 34, 0.82), rgba(14, 45, 72, 0.52));
            z-index: 0;
        }
        .auth-brand > * { position: relative; z-index: 1; }
        .auth-logo-shell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background:
                radial-gradient(circle at left top, rgba(255, 194, 96, 0.26), rgba(255, 194, 96, 0) 32%),
                rgba(255, 255, 255, 0.995);
            border: 1px solid rgba(255, 255, 255, 0.54);
            padding: 16px 20px;
            border-radius: 24px;
            width: fit-content;
            backdrop-filter: blur(10px);
            box-shadow:
                0 20px 42px rgba(8, 18, 34, 0.24),
                inset 0 1px 0 rgba(255, 255, 255, 0.96);
        }
        .auth-logo {
            display: block;
            width: min(100%, 360px);
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 12px 20px rgba(12, 36, 72, 0.1));
        }
        .auth-brand h1 {
            margin: 0;
            font-size: 36px;
            line-height: 1.1;
        }
        .auth-brand p {
            margin: 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }
        .brand-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .brand-kpi {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 10px;
            font-size: 12px;
        }
        .brand-kpi b { display: block; font-size: 18px; }
        .auth-card {
            padding: 36px;
            display: grid;
            align-content: center;
        }
        .auth-card h2 {
            margin: 0 0 6px;
            font-size: 30px;
        }
        .muted { color: var(--muted); font-size: 14px; }
        .m-0 { margin: 0; }
        .mt-0 { margin-top: 0; }
        .mt-14 { margin-top: 14px; }
        .field { margin-bottom: 11px; }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .remember-row input[type="checkbox"] {
            width: auto;
        }
        .remember-row label {
            margin: 0;
            font-weight: 500;
        }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input[type="email"], input[type="password"] {
            width: 100%;
            border: 1px solid #cad7e8;
            border-radius: 9px;
            padding: 10px;
            font-size: 14px;
            transition: border-color 160ms ease, box-shadow 160ms ease;
            background: #fff;
            color: #1a283f;
        }
        html[data-theme="dark"] input[type="email"],
        html[data-theme="dark"] input[type="password"] {
            background: #0f1a2b;
            border-color: #22324a;
            color: #e8effd;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #9fc1f1;
            box-shadow: 0 0 0 3px rgba(14, 93, 208, 0.12);
        }
        html[data-theme="dark"] input[type="email"]:focus,
        html[data-theme="dark"] input[type="password"]:focus {
            border-color: #3c5f96;
            box-shadow: 0 0 0 3px rgba(108, 168, 255, 0.18);
        }
        .btn {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: 0;
            border-radius: 9px;
            padding: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 160ms ease, filter 160ms ease, box-shadow 160ms ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.02);
            box-shadow: 0 10px 20px rgba(13, 93, 209, 0.25);
        }
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }
        .error {
            color: #a42020;
            background: #fff1f1;
            border: 1px solid #ffd0d0;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 14px;
        }
        @media (max-width: 980px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-brand { padding: 28px; }
            .auth-card { padding: 28px; }
            .brand-kpis { grid-template-columns: 1fr 1fr; }
            .auth-logo {
                width: min(100%, 300px);
            }
        }
    </style>
</head>
<body>
@php
    $brandLogo = asset('images/logo.webp');
@endphp
<div class="auth-top-actions">
    <button type="button" class="icon-btn theme-toggle" id="themeToggle" aria-label="Toggle theme" title="Toggle theme">
        <svg class="sun-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
        <svg class="moon-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"></path>
        </svg>
    </button>
</div>
<div class="auth-shell">
    <section class="auth-brand">
        <div class="auth-logo-shell">
            <img src="{{ $brandLogo }}" alt="Academic Mantra" class="auth-logo">
        </div>
        <h1>Production-grade learning experience.</h1>
        <p>Secure access for Super Admin, Admin, Trainers, and Learners.</p>
        <div class="brand-kpis">
            <div class="brand-kpi"><b>150+</b> Courses Live</div>
            <div class="brand-kpi"><b>2.5k</b> Active Learners</div>
            <div class="brand-kpi"><b>99.9%</b> Uptime</div>
        </div>
    </section>
    <section class="auth-card">
        @yield('content')
    </section>
</div>
<script src="{{ asset('js/theme.js') }}" defer></script>
</body>
</html>
