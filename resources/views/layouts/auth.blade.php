<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'LMS Login' }}</title>
    <style>
        :root {
            --bg1: #edf3fd;
            --bg2: #f8fbff;
            --card: #ffffff;
            --line: #d6e1f1;
            --text: #1a283f;
            --muted: #60728c;
            --primary: #0d5dd1;
        }
        html[data-theme="dark"] {
            --bg1: #0f1b31;
            --bg2: #101f39;
            --card: #152742;
            --line: #2b4269;
            --text: #e8effd;
            --muted: #a8bad6;
            --primary: #6ca8ff;
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
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 12% 10%, rgba(122, 162, 255, 0.18) 0%, rgba(122, 162, 255, 0) 34%),
                radial-gradient(circle at 95% -10%, rgba(51, 180, 166, 0.12) 0%, rgba(51, 180, 166, 0) 36%),
                linear-gradient(160deg, var(--bg1), var(--bg2));
            display: grid;
            place-items: center;
            padding: 20px;
            color: var(--text);
            animation: authFade 320ms ease-out;
        }
        html[data-theme="dark"] body {
            background:
                radial-gradient(circle at 12% 8%, rgba(96, 136, 228, 0.26) 0%, rgba(96, 136, 228, 0) 38%),
                radial-gradient(circle at 90% -8%, rgba(39, 162, 150, 0.15) 0%, rgba(39, 162, 150, 0) 36%),
                linear-gradient(160deg, var(--bg1), var(--bg2));
        }
        .auth-shell {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            background: var(--card);
            box-shadow: 0 30px 60px rgba(13, 33, 66, 0.14);
            animation: authFade 420ms ease-out;
        }
        .auth-top-actions {
            position: fixed;
            top: 14px;
            right: 14px;
            z-index: 10;
        }
        .auth-topbar {
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 10;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 10px;
            padding: 8px 12px;
            display: flex;
            gap: 8px;
            align-items: center;
            box-shadow: 0 10px 20px rgba(13, 39, 82, 0.12);
        }
        .auth-topbar strong {
            font-size: 13px;
        }
        .auth-topbar span {
            font-size: 11px;
            color: var(--muted);
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
            padding: 30px;
            background: linear-gradient(140deg, #0d3d90 0%, #1666d2 100%);
            color: #fff;
            display: grid;
            align-content: center;
            gap: 12px;
        }
        .auth-brand h1 {
            margin: 0;
            font-size: 34px;
            line-height: 1.1;
        }
        .auth-brand p {
            margin: 0;
            font-size: 14px;
            color: #d8e7ff;
        }
        .auth-card {
            padding: 30px;
            display: grid;
            align-content: center;
        }
        .auth-card h2 {
            margin: 0 0 6px;
            font-size: 28px;
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
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #9fc1f1;
            box-shadow: 0 0 0 3px rgba(14, 93, 208, 0.12);
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
        @media (max-width: 900px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-brand { padding: 22px; }
            .auth-card { padding: 22px; }
        }
    </style>
</head>
<body>
<div class="auth-topbar">
    <strong>Academy</strong>
    <span>Secure Login</span>
</div>
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
        <h1>LMS Control Center</h1>
        <p>Modern learning operations for every role: Super Admin, Admin, Trainer, and Student.</p>
        <p>Secure login, role-based dashboards, and complete course lifecycle management.</p>
    </section>
    <section class="auth-card">
        @yield('content')
    </section>
</div>
<script src="{{ asset('js/theme.js') }}" defer></script>
</body>
</html>
