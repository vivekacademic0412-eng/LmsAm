<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'LMS' }}</title>
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
            grid-template-columns: 250px 1fr;
        }

        .sidebar {
            background: linear-gradient(165deg, var(--sidebar), var(--sidebar-2));
            color: var(--sidebar-text);
            border-right: 1px solid var(--sidebar-border);
            padding: 12px 12px 16px;
            display: grid;
            grid-template-rows: 1fr auto;
            gap: 12px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            margin-top: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 12px;
            padding: 7px 9px;
            border: 1px solid var(--sidebar-brand-border);
            border-radius: 12px;
            background: var(--sidebar-brand-bg);
            box-shadow: 0 8px 18px rgba(14, 44, 90, 0.08);
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
        }

        .topbar {
            width: 100%;
            border-bottom: 1px solid var(--line);
            background: var(--topbar-bg);
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 10px rgba(15, 38, 73, 0.04);
            padding: 10px 16px;
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

        .topbar-title { min-width: 0; }

        .topbar-title h2 {
            font-size: 16px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-title p {
            margin-top: 2px;
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

        .brand-logo {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: linear-gradient(140deg, #0f4fb9, #1a73d9);
            color: #fff;
            display: grid;
            place-content: center;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 20px rgba(19, 80, 174, 0.35);
            flex-shrink: 0;
        }

        .brand-copy h1 {
            font-size: 14px;
            color: var(--sidebar-title);
            line-height: 1.15;
        }

        .brand-copy p {
            margin-top: 2px;
            color: var(--sidebar-subtitle);
            font-size: 11px;
        }

        .profile-top {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
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
            padding: 14px 16px 0;
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
    $menu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'show' => true],
        ['label' => 'Course Categories', 'route' => 'course-categories.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER_HR, \App\Models\User::ROLE_IT], true)],
        ['label' => 'Courses', 'route' => 'courses.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER_HR, \App\Models\User::ROLE_IT], true)],
        ['label' => 'Enrollments', 'route' => 'enrollments.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
        ['label' => 'User Control', 'route' => 'users.index', 'show' => in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true)],
        ['label' => 'Manage Profile', 'route' => 'profile.edit', 'show' => true],
        ['label' => 'My Panel', 'route' => 'panel.' . $user->role, 'show' => $user->role !== \App\Models\User::ROLE_SUPERADMIN],
        ['label' => 'Trainer Tracking', 'route' => 'trainer.progress', 'show' => $user->role === \App\Models\User::ROLE_TRAINER],
        ['label' => 'My Courses', 'route' => 'student.courses', 'show' => $user->role === \App\Models\User::ROLE_STUDENT],
    ];
    $initials = strtoupper(substr($user->name, 0, 1) . substr(strrchr(' ' . $user->name, ' '), 1, 1));
@endphp

<div class="app">
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="brand-logo">LMS</div>
                <div class="brand-copy">
                    <h1>LMS Control Center</h1>
                    <p>Premium Workspace</p>
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
                <div class="avatar small">{{ $initials }}</div>
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
                    <h2>Academy Learning Club</h2>
                    <p>{{ $roleLabels[$user->role] ?? $user->role }} Panel</p>
                </div>
                <a class="view-site-link" href="{{ route('dashboard') }}">View site ↗</a>
            </div>

            <div class="profile-top">
                <div class="icon-actions">
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
                        <div class="avatar">{{ $initials }}</div>
                    </button>
                </div>

                <div class="profile-popup" id="profilePopup">
                    <div class="profile-popup-head">
                        <a href="{{ route('profile.edit') }}" class="profile-popup-head-link">
                            <div class="avatar">{{ $initials }}</div>
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
            <span>LMS Control Center</span>
            <span>{{ now()->format('Y') }} | {{ $roleLabels[$user->role] ?? $user->role }}</span>
        </footer>
    </main>
</div>

<script src="{{ asset('js/theme.js') }}" defer></script>
<script src="{{ asset('js/profile-popup.js') }}" defer></script>
</body>
</html>
