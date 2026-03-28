<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Academic Mantra' }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    <link rel="shortcut icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.webp') }}">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            height: 100%;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 4%, rgba(84, 142, 238, 0.14) 0%, rgba(84, 142, 238, 0) 42%),
                radial-gradient(circle at 88% 0%, rgba(57, 187, 174, 0.09) 0%, rgba(57, 187, 174, 0) 36%),
                linear-gradient(165deg, var(--bg-soft) 0%, var(--bg-main) 52%, #edf3fc 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        html[data-theme="dark"] body {
            background:
                radial-gradient(circle at 12% 0%, rgba(80, 121, 212, 0.2) 0%, rgba(80, 121, 212, 0) 40%),
                radial-gradient(circle at 88% 0%, rgba(35, 163, 150, 0.14) 0%, rgba(35, 163, 150, 0) 34%),
                linear-gradient(170deg, var(--bg-soft) 0%, var(--bg-main) 62%, #0f1a2f 100%);
        }

        :root {
            --bg-main: #eef4fb;
            --bg-soft: #f7faff;
            --sidebar: #f8fbff;
            --sidebar-2: #ecf3ff;
            --sidebar-border: #d7e3f3;
            --sidebar-text: #203754;
            --sidebar-title: #1a2f4b;
            --sidebar-subtitle: #5f7492;
            --sidebar-link: #2f4a6f;
            --sidebar-link-hover-bg: #dfebfd;
            --sidebar-link-hover-border: #c4d8f5;
            --sidebar-brand-bg: #fefefe;
            --sidebar-brand-border: #bfd3ef;
            --card: #ffffff;
            --line: #d5e0ef;
            --line-soft: #e7edf7;
            --text: #13243d;
            --muted: #60718d;
            --primary: #145fd1;
            --primary-soft: #ecf4ff;
            --danger: #c53a3a;
            --ok: #15834c;
            --shadow: 0 16px 34px rgba(10, 28, 56, 0.09);
            --topbar-bg: rgba(248, 251, 255, 0.88);
            --field-bg: #f8fbff;
            --field-border: #c9d8ec;
            --field-shadow: 0 1px 0 rgba(255, 255, 255, 0.8), inset 0 1px 1px rgba(255, 255, 255, 0.7);
        }

        html[data-theme="dark"] {
            --bg-main: #0c1426;
            --bg-soft: #111d34;
            --sidebar: #0c1528;
            --sidebar-2: #132341;
            --sidebar-border: rgba(167, 193, 232, 0.22);
            --sidebar-text: #dbe7ff;
            --sidebar-title: #f0f6ff;
            --sidebar-subtitle: #a8bad9;
            --sidebar-link: #deebff;
            --sidebar-link-hover-bg: rgba(255, 255, 255, 0.1);
            --sidebar-link-hover-border: rgba(255, 255, 255, 0.2);
            --sidebar-brand-bg: #0f1c34;
            --sidebar-brand-border: #2a4167;
            --card: #152642;
            --line: #2c4268;
            --line-soft: #243955;
            --text: #e9f1ff;
            --muted: #aabbd7;
            --primary: #78afff;
            --primary-soft: #203c63;
            --danger: #f17070;
            --ok: #4ac488;
            --shadow: 0 20px 38px rgba(0, 0, 0, 0.3);
            --topbar-bg: rgba(20, 36, 61, 0.84);
            --field-bg: #192c49;
            --field-border: #3a557c;
            --field-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
        }

        .app {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
        }

        .sidebar {
            background: linear-gradient(165deg, var(--sidebar), var(--sidebar-2));
            color: var(--sidebar-text);
            border-right: 1px solid var(--sidebar-border);
            padding: 0 12px 16px;
            display: grid;
            grid-template-rows: 1fr auto;
            gap: 12px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            margin-top: 0;
            width: 260px;
        }

        .sidebar-brand {
            display: grid;
            gap: 0;
            margin: 0 0 12px;
            padding: 14px;
            border: 1px solid color-mix(in srgb, var(--sidebar-brand-border) 82%, #ffffff 18%);
            border-radius: 22px;
            background:
                radial-gradient(circle at top right, rgba(255, 200, 120, 0.22), rgba(255, 200, 120, 0) 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(241, 247, 255, 0.96));
            box-shadow:
                0 18px 34px rgba(14, 44, 90, 0.11),
                inset 0 1px 0 rgba(255, 255, 255, 0.98);
        }

        .menu {
            display: grid;
            gap: 8px;
            align-content: start;
        }

        .menu a {
            text-decoration: none;
            color: var(--sidebar-link);
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 10px 11px;
            font-size: 13px;
            font-weight: 500;
            transition: 180ms ease;
        }

        .menu a:hover,
        .menu a.active {
            border-color: var(--sidebar-link-hover-border);
            background: var(--sidebar-link-hover-bg);
            transform: translateX(2px);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
        }

        .sidebar-bottom {
            border: 1px solid var(--sidebar-brand-border);
            background: var(--sidebar-brand-bg);
            border-radius: 12px;
            padding: 10px;
            display: grid;
            gap: 9px;
            box-shadow: 0 8px 16px rgba(18, 38, 72, 0.06);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-user-meta strong {
            display: block;
            font-size: 12px;
            line-height: 1.2;
            color: var(--sidebar-title);
        }

        .sidebar-user-meta span {
            font-size: 11px;
            color: var(--sidebar-subtitle);
            line-height: 1.2;
        }

        .sidebar-logout {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: var(--card);
            color: var(--danger);
            font-size: 12px;
            font-weight: 600;
            padding: 8px;
            cursor: pointer;
            text-align: center;
            transition: 150ms ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sidebar-logout:hover {
            background: rgba(197, 58, 58, 0.08);
            border-color: rgba(197, 58, 58, 0.4);
        }

        .icon-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
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
            border-color: #aac7f1;
            background: var(--primary-soft);
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

        .main {
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 100vh;
            margin-top: 0;
            min-width: 0;
        }

        @media (max-width: 980px) {
            .app {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: relative;
                height: auto;
                width: 100%;
                border-right: 0;
                border-bottom: 1px solid var(--sidebar-border);
            }
            .topbar {
                position: sticky;
            }
            .sidebar-brand {
                margin-bottom: 10px;
            }
            .brand-mark-shell {
                min-height: 90px;
                padding: 14px 16px;
            }
            .brand-mark {
                max-width: 200px;
            }
        }

        .topbar {
            width: 100%;
            border-bottom: 1px solid var(--line);
            background: var(--topbar-bg);
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 10px rgba(15, 38, 73, 0.04);
            padding: 0 16px;
            min-height: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .topbar-title {
            min-width: 0;
            display: grid;
            gap: 1px;
        }

        .topbar-title h2 {
            font-size: 16px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-title p {
            margin-top: 0;
            font-size: 12px;
            color: var(--muted);
        }

        .view-site-link {
            text-decoration: none;
            color: var(--muted);
            font-size: 14px;
            border-left: 1px solid var(--line);
            padding-left: 12px;
            white-space: nowrap;
        }

        .view-site-link:hover { color: var(--primary); }

        .brand-mark-shell {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 20px;
            background:
                radial-gradient(circle at left top, rgba(255, 194, 96, 0.2), rgba(255, 194, 96, 0) 30%),
                linear-gradient(180deg, #ffffff, #fbfdff);
            border: 1px solid rgba(210, 223, 242, 0.92);
            padding: 16px 18px;
            box-shadow:
                0 16px 28px rgba(15, 45, 89, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.98);
            min-height: 104px;
        }

        .brand-mark {
            display: block;
            width: 100%;
            height: auto;
            max-width: 220px;
            margin: 0 auto;
            object-fit: contain;
            filter: drop-shadow(0 10px 18px rgba(20, 51, 93, 0.08));
        }

        .profile-top {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .notification-shell {
            position: relative;
        }

        .notification-trigger {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #d93c3c;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            line-height: 18px;
            padding: 0 5px;
            box-shadow: 0 6px 14px rgba(217, 60, 60, 0.28);
        }

        .notification-trigger.has-unread {
            border-color: rgba(20, 95, 209, 0.32);
            background:
                radial-gradient(circle at top, rgba(20, 95, 209, 0.18), rgba(20, 95, 209, 0) 62%),
                var(--card);
            box-shadow: 0 10px 20px rgba(20, 95, 209, 0.12);
        }

        .profile-trigger {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--card);
            color: var(--text);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: 160ms ease;
        }

        .profile-trigger:hover {
            border-color: #abc6eb;
            background: var(--primary-soft);
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(140deg, #ddeaff, #bfd4f7);
            color: #1e4f98;
            display: grid;
            place-content: center;
            font-size: 12px;
            font-weight: 700;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .avatar.small {
            width: 30px;
            height: 30px;
            font-size: 11px;
        }

        html[data-theme="dark"] .avatar {
            background: linear-gradient(140deg, #1d3861, #285087);
            color: #d8e8ff;
        }

        .profile-meta { display: grid; gap: 2px; }
        .profile-meta strong { font-size: 12px; line-height: 1; }
        .profile-meta span { font-size: 11px; color: var(--muted); line-height: 1; }

        .profile-popup {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 280px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 0 0 12px 12px;
            box-shadow: 0 18px 32px rgba(7, 18, 36, 0.2);
            padding: 14px 14px 12px;
            display: none;
            z-index: 100;
        }

        .profile-popup.open {
            display: grid;
            gap: 12px;
        }

        .notification-popup {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: min(392px, 94vw);
            background:
                radial-gradient(circle at top right, rgba(20, 95, 209, 0.12), rgba(20, 95, 209, 0) 36%),
                linear-gradient(180deg, color-mix(in srgb, var(--card) 96%, #ffffff 4%), var(--card));
            border: 1px solid color-mix(in srgb, var(--line) 84%, #bfd3ef 16%);
            border-radius: 18px;
            box-shadow: 0 24px 42px rgba(7, 18, 36, 0.22);
            padding: 16px;
            display: none;
            z-index: 100;
        }

        .notification-popup.open {
            display: grid;
            gap: 12px;
        }

        .notification-popup-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line-soft);
        }

        .notification-popup-copy {
            display: grid;
            gap: 5px;
        }

        .notification-popup-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 9px;
            background: color-mix(in srgb, var(--primary-soft) 70%, #ffffff 30%);
            color: var(--primary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .notification-popup-head strong {
            font-size: 16px;
            line-height: 1.2;
        }

        .notification-popup-head span {
            color: var(--muted);
            font-size: 12px;
        }

        .notification-popup-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .notification-popup-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            background: color-mix(in srgb, var(--primary-soft) 72%, #ffffff 28%);
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
        }

        .notification-popup-pill.is-muted {
            background: color-mix(in srgb, var(--field-bg) 90%, #ffffff 10%);
            color: var(--muted);
        }

        .notification-popup-list {
            display: grid;
            gap: 14px;
            max-height: 320px;
            overflow-y: auto;
            padding-right: 2px;
        }

        .notification-popup-group {
            display: grid;
            gap: 10px;
        }

        .notification-popup-group-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
        }

        .notification-popup-group-head strong {
            font-size: 12px;
            color: var(--text);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .notification-popup-group-head span {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
        }

        .notification-popup-item {
            display: grid;
            gap: 7px;
            padding: 13px;
            border: 1px solid color-mix(in srgb, var(--line-soft) 88%, #d9e7fb 12%);
            border-radius: 15px;
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--card) 95%, #ffffff 5%), color-mix(in srgb, var(--card) 98%, var(--primary-soft) 2%));
            text-decoration: none;
            color: inherit;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .notification-popup-item.is-unread {
            border-color: #b8d0f5;
            background:
                radial-gradient(circle at top right, rgba(20, 95, 209, 0.14), rgba(20, 95, 209, 0) 36%),
                linear-gradient(180deg, color-mix(in srgb, var(--primary-soft) 84%, #ffffff 16%), var(--card));
        }

        .notification-popup-item-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 8px;
        }

        .notification-popup-item-body {
            display: flex;
            gap: 12px;
            align-items: start;
        }

        .notification-popup-avatar {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            background: linear-gradient(145deg, #e9f2ff 0%, #d7e8ff 100%);
            color: var(--primary);
            display: grid;
            place-content: center;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .notification-popup-copy {
            display: grid;
            gap: 7px;
            min-width: 0;
            flex: 1 1 auto;
        }

        .notification-popup-item strong {
            font-size: 13px;
            line-height: 1.35;
        }

        .notification-popup-item p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .notification-state-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(180deg, #2a79da 0%, #39bbae 100%);
            box-shadow: 0 0 0 4px rgba(42, 121, 218, 0.12);
            flex: 0 0 auto;
            margin-top: 4px;
        }

        .notification-popup-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
            color: var(--muted);
            font-size: 11px;
        }

        .notification-popup-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .notification-popup-tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 8px;
            background: color-mix(in srgb, var(--primary-soft) 72%, #ffffff 28%);
            color: var(--primary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .notification-popup-tag.is-soft {
            background: color-mix(in srgb, var(--field-bg) 90%, #ffffff 10%);
            color: var(--muted);
        }

        .notification-popup-actions {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
        }

        .notification-popup-inline-btn {
            border: 1px solid color-mix(in srgb, var(--line) 82%, #c0d4ef 18%);
            border-radius: 999px;
            background: color-mix(in srgb, var(--card) 92%, #ffffff 8%);
            color: var(--primary);
            font-size: 10px;
            font-weight: 800;
            padding: 5px 9px;
            cursor: pointer;
        }

        .notification-popup-inline-btn:hover {
            background: var(--primary-soft);
            border-color: #aac6ed;
        }

        .notification-popup-empty {
            border: 1px dashed color-mix(in srgb, var(--line) 76%, #b9ceeb 24%);
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            background: color-mix(in srgb, var(--field-bg) 72%, #ffffff 28%);
        }

        .notification-popup-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 4px;
            border-top: 1px solid var(--line-soft);
        }

        .notification-link {
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .notification-link:hover {
            text-decoration: underline;
        }

        .btn-text {
            border: 0;
            background: transparent;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            padding: 0;
        }

        .btn-text:hover {
            text-decoration: underline;
        }

        .profile-popup-head {
            display: flex;
            gap: 12px;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--line-soft);
        }
        .profile-popup-head-link {
            display: flex;
            gap: 12px;
            align-items: center;
            text-decoration: none;
            color: inherit;
            width: 100%;
        }

        .profile-popup-list { display: grid; gap: 6px; }

        .popup-item {
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            line-height: 1.2;
            font-weight: 600;
            padding: 10px 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: 150ms ease;
            text-align: left;
        }

        .popup-item svg {
            width: 18px;
            height: 18px;
            stroke: #7b879b;
            flex-shrink: 0;
        }

        .popup-item:hover { background: var(--primary-soft); color: var(--text); }
        .popup-item:hover svg { stroke: var(--primary); }

        .page {
            max-width: 1260px;
            width: 100%;
            margin: 0 auto;
            padding: 0 16px 0;
        }

        .content-stack {
            display: grid;
            gap: 14px;
            align-content: start;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
            box-shadow: var(--shadow);
            transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
        }

        .card:hover { transform: translateY(-2px); border-color: #bfd3ef; box-shadow: 0 24px 40px rgba(11, 34, 66, 0.14); }

        .grid { display: grid; gap: 14px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .kpi { font-size: 27px; font-weight: 700; }
        .muted { color: var(--muted); }

        .flash-ok, .flash-err {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid;
        }

        .flash-ok {
            background: rgba(18, 131, 76, 0.1);
            color: var(--ok);
            border-color: rgba(18, 131, 76, 0.3);
        }

        .flash-err {
            background: rgba(197, 58, 58, 0.1);
            color: var(--danger);
            border-color: rgba(197, 58, 58, 0.3);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px solid #cfd7e4;
            background: #f7f9fc;
            color: #1f2f48;
            text-decoration: none;
            border-radius: 10px;
            padding: 7px 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            line-height: 1;
            transition: 160ms ease;
        }

        .btn:hover {
            border-color: #bcc8d9;
            background: #eef3f9;
            transform: translateY(-1px);
        }
        .btn-danger {
            border-color: #cfd7e4;
            background: #f7f9fc;
            color: #1f2f48;
        }
        .btn-danger:hover {
            border-color: #d8b8b8;
            background: #f9f1f1;
            color: #b34747;
        }
        .btn-soft { background: #f7f9fc; color: #1f2f48; border: 1px solid #cfd7e4; }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }

        th, td {
            padding: 10px;
            border-bottom: 1px solid var(--line-soft);
            vertical-align: top;
            text-align: left;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--field-border);
            border-radius: 11px;
            padding: 10px 11px;
            font-size: 14px;
            line-height: 1.35;
            background: var(--field-bg);
            color: var(--text);
            box-shadow: var(--field-shadow);
            transition: border-color 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #8eb7ef;
            box-shadow:
                0 0 0 3px rgba(20, 95, 209, 0.16),
                0 8px 18px rgba(20, 95, 209, 0.09);
            background: var(--card);
        }

        input::placeholder, textarea::placeholder {
            color: color-mix(in srgb, var(--muted) 76%, transparent);
        }

        select {
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, var(--muted) 50%),
                linear-gradient(135deg, var(--muted) 50%, transparent 50%);
            background-position:
                calc(100% - 16px) calc(50% - 2px),
                calc(100% - 11px) calc(50% - 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            padding-right: 34px;
        }

        textarea {
            min-height: 96px;
            resize: vertical;
        }

        input[type="file"] {
            padding: 7px;
            line-height: 1.2;
        }

        input[type="file"]::file-selector-button {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--card);
            color: var(--text);
            padding: 6px 10px;
            margin-right: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 6px;
            display: block;
            color: color-mix(in srgb, var(--text) 88%, transparent);
        }
        .field { margin-bottom: 10px; }
        .field:last-child { margin-bottom: 0; }
        .mb-8 { margin-bottom: 8px; }
        .mt-8 { margin-top: 8px; }
        .m-0 { margin: 0; }
        .mt-0 { margin-top: 0; }
        .form-span-2 { grid-column: span 2; }
        .panel-soft {
            box-shadow: none;
            border-style: dashed;
            border-color: var(--line-soft);
        }
        .kpi-sm { font-size: 20px; margin-top: 6px; }
        .kpi-xs { font-size: 18px; margin-top: 6px; word-break: break-word; }
        .text-ok { color: var(--ok); }
        .text-danger { color: var(--danger); }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 12px;
            margin-bottom: 6px;
        }

        .page-head h1, .page-head h2 { line-height: 1.15; }
        .page-head p { margin-top: 4px; color: var(--muted); font-size: 13px; }

        .stack { display: grid; gap: 14px; }
        .form-grid { display: grid; gap: 12px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .form-grid-2 { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .form-premium {
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            padding: 14px;
            background:
                linear-gradient(145deg, rgba(20, 95, 209, 0.05), rgba(20, 95, 209, 0) 56%),
                var(--card);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
        }
        html[data-theme="dark"] .form-premium {
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        .table-wrap {
            border: 1px solid var(--line-soft);
            border-radius: 10px;
            overflow: auto;
            background: var(--card);
        }

        .table-wrap table { min-width: 720px; }

        .actions-row { display: flex; flex-wrap: wrap; gap: 9px; align-items: center; }

        .tag {
            display: inline-block;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            background: var(--primary-soft);
            color: var(--primary);
        }

        .tag.ok { background: rgba(18, 131, 76, 0.14); color: var(--ok); }
        .tag.no { background: rgba(197, 58, 58, 0.12); color: var(--danger); }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .filter-row select {
            max-width: 260px;
            background: var(--card);
        }
        .filter-row select:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .filter-wrap {
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            border-radius: 10px;
            padding: 8px 14px;
            background: #1a73e8;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 10px 18px rgba(26, 115, 232, 0.18);
        }
        .filter-btn svg { width: 16px; height: 16px; stroke: #fff; }
        .filter-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: min(360px, calc(100vw - 32px));
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 18px 34px rgba(10, 28, 56, 0.18);
            padding: 14px;
            display: none;
            z-index: 80;
            margin-top: 0;
        }
        .filter-panel.open { display: block; }
        .filter-field { display: grid; gap: 6px; margin-bottom: 10px; }
        .filter-field label { font-size: 12px; font-weight: 700; text-transform: none; }
        .filter-actions { display: flex; justify-content: space-between; gap: 8px; margin-top: 8px; }
        .filter-actions .btn { padding: 8px 12px; }

        @media (max-width: 640px) {
            .filter-panel {
                left: 0;
                right: auto;
                width: min(320px, calc(100vw - 32px));
            }
        }

        .footer {
            max-width: 1260px;
            width: 100%;
            margin: 16px auto 0;
            border-top: 1px solid var(--line);
            padding: 10px 16px;
            color: var(--muted);
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .pagination .page-item { display: inline-flex; }
        .pagination .page-link,
        .pagination .page-item > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .pagination .page-item.active > span {
            background: var(--primary-soft);
            border-color: #bcd3f7;
            color: var(--primary);
        }
        .pagination .page-item.disabled > span {
            color: var(--muted);
            background: #f3f6fb;
        }
        .pagination .page-link:hover { background: #eef3f9; }

        @media (max-width: 980px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; grid-template-rows: auto auto; }
            .menu { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .topbar { flex-direction: column; align-items: stretch; }
            .profile-top { justify-content: flex-end; }
            .view-site-link { border-left: 0; padding-left: 0; }
            .grid-3, .form-grid, .form-grid-2 { grid-template-columns: 1fr; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
@php
    $user = auth()->user();
    $roleLabels = \App\Models\User::roleOptions();
    $path = request()->path();
    $avatarUrl = $user->avatar_url;
    $brandLogo = asset('images/logo.webp');
    $notificationsAvailable = \Illuminate\Support\Facades\Schema::hasTable('notifications');
    $topbarNotifications = collect();
    $topbarUnreadCount = 0;
    $topbarUnreadNotifications = collect();
    $topbarSeenNotifications = collect();
    if ($notificationsAvailable) {
        $topbarNotifications = $user->notifications()->latest()->take(6)->get();
        $topbarUnreadCount = $user->unreadNotifications()->count();
        $topbarUnreadNotifications = $topbarNotifications->filter(fn ($notification) => is_null($notification->read_at))->values();
        $topbarSeenNotifications = $topbarNotifications->reject(fn ($notification) => is_null($notification->read_at))->values();
    }
    if ($user->role === \App\Models\User::ROLE_STUDENT) {
        $menu = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'show' => true],
            ['label' => 'My Courses', 'route' => 'student.courses', 'show' => true],
            ['label' => 'History', 'route' => 'student.history', 'show' => true],
            ['label' => 'Certificates', 'route' => 'student.certificates', 'show' => true],
            ['label' => 'My Profile', 'route' => 'profile.edit', 'show' => true],
        ];
    } else {
        if ($user->role === \App\Models\User::ROLE_DEMO) {
            $menu = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'show' => true],
            ];
        } elseif ($user->role === \App\Models\User::ROLE_TRAINER) {
            $menu = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'show' => true],
                ['label' => 'Review Queue', 'route' => 'trainer.submissions', 'show' => true],
                ['label' => 'All Courses', 'route' => 'trainer.courses', 'show' => true],
                ['label' => 'Assigned Students', 'route' => 'trainer.assigned-students', 'show' => true],
                ['label' => 'Trainer Tracking', 'route' => 'trainer.progress', 'show' => true],
                ['label' => 'My Profile', 'route' => 'profile.edit', 'show' => true],
            ];
        } else {
            $menu = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'show' => true],
                ['label' => 'HR Panel', 'route' => 'panel.manager_hr', 'show' => $user->role === \App\Models\User::ROLE_MANAGER_HR],
                ['label' => 'IT Panel', 'route' => 'panel.it', 'show' => $user->role === \App\Models\User::ROLE_IT],
                ['label' => 'Course Categories', 'route' => 'course-categories.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER_HR, \App\Models\User::ROLE_IT], true)],
                ['label' => 'Courses', 'route' => 'courses.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER_HR, \App\Models\User::ROLE_IT], true)],
                ['label' => 'Enrollments', 'route' => 'enrollments.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Submission Review', 'route' => 'submissions.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Activity Logs', 'route' => 'activity-logs.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'User Control', 'route' => 'users.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Broadcast Notifications', 'route' => 'broadcast-notifications.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Create Demo Task', 'route' => 'demo-tasks.create-page', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Assign Demo Task', 'route' => 'demo-tasks.assign-page', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Demo Feature Video', 'route' => 'demo-feature-video.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'Reviews', 'route' => 'demo-review-videos.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
                ['label' => 'My Profile', 'route' => 'profile.edit', 'show' => true],
            ];
        }
    }
    $initials = strtoupper(substr($user->name, 0, 1) . substr(strrchr(' ' . $user->name, ' '), 1, 1));
@endphp

<div class="app">
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="brand-mark-shell">
                    <img src="{{ $brandLogo }}" alt="Academic Mantra" class="brand-mark">
                </div>
            </div>
            <nav class="menu">
                @foreach ($menu as $item)
                    @if ($item['show'])
                        @php $url = route($item['route']); @endphp
                        <a href="{{ $url }}" class="{{ str_starts_with($path, trim(parse_url($url, PHP_URL_PATH), '/')) ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <div class="avatar small">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div class="sidebar-user-meta">
                    <strong>{{ $user->name }}</strong>
                    <span>{{ $roleLabels[$user->role] ?? $user->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-left">
                <div class="topbar-title">
                    <h2>Control Panel</h2>
                    <p>{{ $roleLabels[$user->role] ?? $user->role }} Panel</p>
                </div>
                <a class="view-site-link" href="{{ route('dashboard') }}">View site ↗</a>
            </div>

            <div class="profile-top">
                <div class="icon-actions">
                    <div class="notification-shell">
                        <button type="button" class="icon-btn notification-trigger {{ $topbarUnreadCount > 0 ? 'has-unread' : '' }}" id="notificationToggle" aria-label="Notifications" title="Notifications">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"></path>
                                <path d="M9 17a3 3 0 0 0 6 0"></path>
                            </svg>
                            @if ($topbarUnreadCount > 0)
                                <span class="notification-badge">{{ min($topbarUnreadCount, 99) }}</span>
                            @endif
                        </button>

                        <div class="notification-popup" id="notificationPopup">
                            <div class="notification-popup-head">
                                <div class="notification-popup-copy">
                                    <span class="notification-popup-kicker">Updates</span>
                                    <strong>Notifications</strong>
                                    <div class="notification-popup-summary">
                                        <span class="notification-popup-pill">{{ $topbarUnreadCount > 0 ? $topbarUnreadCount.' unread' : 'All read' }}</span>
                                        <span class="notification-popup-pill is-muted">{{ $topbarNotifications->count() }} shown</span>
                                    </div>
                                </div>
                                <a href="{{ route('dashboard') }}" class="notification-link">Open Dashboard</a>
                            </div>

                            @if ($topbarNotifications->isNotEmpty())
                                <div class="notification-popup-list">
                                    @if ($topbarUnreadNotifications->isNotEmpty())
                                        <section class="notification-popup-group">
                                            <div class="notification-popup-group-head">
                                                <strong>New</strong>
                                                <span>{{ $topbarUnreadNotifications->count() }} unread</span>
                                            </div>
                                            @foreach ($topbarUnreadNotifications as $notification)
                                                @php
                                                    $notificationAudience = $notification->data['audience'] ?? null;
                                                    $notificationSender = $notification->data['sender_name'] ?? 'System';
                                                    $notificationTitle = $notification->data['title'] ?? 'Update';
                                                    $notificationMessage = $notification->data['message'] ?? 'A new update is available.';
                                                    $notificationMonogram = \Illuminate\Support\Str::upper(
                                                        \Illuminate\Support\Str::substr(\Illuminate\Support\Str::replace(' ', '', $notificationTitle), 0, 2)
                                                    ) ?: 'UP';
                                                @endphp
                                                <article class="notification-popup-item is-unread">
                                                    <div class="notification-popup-item-body">
                                                        <div class="notification-popup-avatar">{{ $notificationMonogram }}</div>
                                                        <div class="notification-popup-copy">
                                                            <div class="notification-popup-item-head">
                                                                <strong>{{ $notificationTitle }}</strong>
                                                                <span class="notification-state-dot" aria-hidden="true"></span>
                                                            </div>
                                                            <p>{{ $notificationMessage }}</p>
                                                            <div class="notification-popup-meta">
                                                                <div class="notification-popup-tags">
                                                                    <span class="notification-popup-tag">{{ $notificationSender }}</span>
                                                                    @if ($notificationAudience)
                                                                        <span class="notification-popup-tag is-soft">{{ \Illuminate\Support\Str::headline((string) $notificationAudience) }}</span>
                                                                    @endif
                                                                </div>
                                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <div class="notification-popup-actions">
                                                                <span class="muted" style="font-size: 11px;">New and unread</span>
                                                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                                                    @csrf
                                                                    <button type="submit" class="notification-popup-inline-btn">Mark Read</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </section>
                                    @endif

                                    @if ($topbarSeenNotifications->isNotEmpty())
                                        <section class="notification-popup-group">
                                            <div class="notification-popup-group-head">
                                                <strong>Earlier</strong>
                                                <span>{{ $topbarSeenNotifications->count() }} seen</span>
                                            </div>
                                            @foreach ($topbarSeenNotifications as $notification)
                                                @php
                                                    $notificationAudience = $notification->data['audience'] ?? null;
                                                    $notificationSender = $notification->data['sender_name'] ?? 'System';
                                                    $notificationTitle = $notification->data['title'] ?? 'Update';
                                                    $notificationMessage = $notification->data['message'] ?? 'A new update is available.';
                                                    $notificationMonogram = \Illuminate\Support\Str::upper(
                                                        \Illuminate\Support\Str::substr(\Illuminate\Support\Str::replace(' ', '', $notificationTitle), 0, 2)
                                                    ) ?: 'UP';
                                                @endphp
                                                <article class="notification-popup-item">
                                                    <div class="notification-popup-item-body">
                                                        <div class="notification-popup-avatar">{{ $notificationMonogram }}</div>
                                                        <div class="notification-popup-copy">
                                                            <div class="notification-popup-item-head">
                                                                <strong>{{ $notificationTitle }}</strong>
                                                            </div>
                                                            <p>{{ $notificationMessage }}</p>
                                                            <div class="notification-popup-meta">
                                                                <div class="notification-popup-tags">
                                                                    <span class="notification-popup-tag">{{ $notificationSender }}</span>
                                                                    @if ($notificationAudience)
                                                                        <span class="notification-popup-tag is-soft">{{ \Illuminate\Support\Str::headline((string) $notificationAudience) }}</span>
                                                                    @endif
                                                                </div>
                                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </section>
                                    @endif
                                </div>
                            @else
                                <div class="notification-popup-empty">
                                    No notifications yet.
                                </div>
                            @endif

                            @if ($topbarUnreadCount > 0)
                                <div class="notification-popup-footer">
                                    <span class="muted" style="font-size: 12px;">Unread updates stay at the top until you mark them read.</span>
                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        <button type="submit" class="btn-text">Mark All As Read</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

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
                    <button type="button" class="profile-trigger" id="profileToggle" title="Profile" aria-label="Profile">
                        <div class="avatar">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                    </button>
                </div>

                <div class="profile-popup" id="profilePopup">
                    <div class="profile-popup-head">
                        <a href="{{ route('profile.edit') }}" class="profile-popup-head-link">
                            <div class="avatar">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}">
                                @else
                                    {{ $initials }}
                                @endif
                            </div>
                            <div class="profile-meta">
                                <strong>{{ $user->name }}</strong>
                                <span>{{ $roleLabels[$user->role] ?? $user->role }}</span>
                                <span>{{ $user->email }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="profile-popup-list">
                        <a href="{{ route('profile.edit') }}" class="popup-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21a8 8 0 1 0-16 0"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="popup-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="3" width="8" height="8" rx="1"></rect>
                                <rect x="13" y="3" width="8" height="5" rx="1"></rect>
                                <rect x="13" y="11" width="8" height="10" rx="1"></rect>
                                <rect x="3" y="14" width="8" height="7" rx="1"></rect>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="popup-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    <polyline points="10 17 15 12 10 7"></polyline>
                                    <line x1="15" y1="12" x2="3" y2="12"></line>
                                </svg>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="page content-stack">
            @if (session('success'))
                <div class="flash-ok">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash-err">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>

        <footer class="footer">
            <span>Academic Mantra</span>
            <span>{{ now()->format('Y') }} | {{ $roleLabels[$user->role] ?? $user->role }}</span>
        </footer>
    </main>
</div>

<script src="{{ asset('js/theme.js') }}" defer></script>
<script src="{{ asset('js/profile-popup.js') }}" defer></script>
</body>
</html>
