@extends('layouts.app')

@php
    $roleLabels = \App\Models\User::roleOptions();
    $accentClass = [
        'blue' => 'accent-blue',
        'green' => 'accent-green',
        'violet' => 'accent-violet',
        'orange' => 'accent-orange',
        'red' => 'accent-red',
        'teal' => 'accent-teal',
    ];
    $courseIcons = ['DS', 'UX', 'FN', 'WB', 'CL', 'AI'];
    $allCoursesRoute = $user->role === \App\Models\User::ROLE_STUDENT ? route('student.courses') : route('courses.index');
    $isStudent = $dashboardMode === 'student';
    $isTrainer = $dashboardMode === 'trainer';
    $learningTitle = $isStudent ? 'My Learning' : ($isTrainer ? 'Assigned Learning' : 'Course Snapshot');
    $learningSubtitle = $isStudent
        ? 'Track your progress and continue your training path.'
        : ($isTrainer ? 'Track assigned learners and completion.' : 'Monitor catalog activity with role-safe access.');
    $learningActionLabel = $isStudent ? 'View all courses ->' : 'Open catalog ->';
    $heroKicker = $isStudent ? 'Continue learning' : ($user->role === \App\Models\User::ROLE_SUPERADMIN ? '' : 'Dashboard overview');
    $heroResumeRoute = route('panel.' . $user->role);
    if ($isStudent && !empty($studentResumeItem) && !empty($studentResumeItem['route'])) {
        $heroResumeRoute = $studentResumeItem['route'];
    } elseif (!empty($heroCourse) && !empty($heroCourse['resume_route'])) {
        $heroResumeRoute = $heroCourse['resume_route'];
    } elseif (!empty($heroCourse['course_id'])) {
        $heroResumeRoute = $user->role === \App\Models\User::ROLE_STUDENT
            ? route('student.courses.show', $heroCourse['course_id'])
            : route('courses.show', $heroCourse['course_id']);
    }
@endphp

@section('content')
    <style>
        .page { max-width: 1440px; padding: 0 18px 0; }
        .dash-grid { display: grid; gap: 18px; margin-top: -12px; }
        .student-mode .dash-hero {
            background: radial-gradient(circle at 10% 0%, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0) 45%),
                        linear-gradient(120deg, #0e4aa8 0%, #2a79da 100%);
            box-shadow: 0 18px 36px rgba(15, 55, 120, 0.2);
        }
        .student-mode .hero-btn {
            border-radius: 999px;
            padding: 10px 16px;
            box-shadow: 0 10px 22px rgba(255, 255, 255, 0.25);
        }
        .student-mode .learning-grid { grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
        .student-mode .recommend-grid { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .student-mode .course-card {
            border-color: #cfd9ec;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.08);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }
        .student-mode .course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 30px rgba(18, 42, 86, 0.14);
            border-color: #b7c9e8;
        }
        .student-mode .course-top::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(12, 32, 64, 0.18), rgba(12, 32, 64, 0.55));
            z-index: 0;
        }
        .student-mode .course-top > * { z-index: 1; }
        .student-mode .course-body h3 { font-size: 19px; }
        .student-focus-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            align-items: start;
        }
        .resume-panel {
            border: 1px solid #d7deea;
            border-radius: 14px;
            background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 56%, #eef4ff 100%);
            padding: 16px;
            display: grid;
            gap: 12px;
            align-content: start;
            grid-auto-rows: max-content;
            box-shadow: 0 12px 24px rgba(18, 42, 86, 0.07);
        }
        .resume-panel-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
        }
        .resume-copy {
            display: grid;
            gap: 8px;
        }
        .resume-copy h2 {
            margin: 0;
            font-size: 24px;
            line-height: 1.12;
            color: #102849;
        }
        .resume-note {
            margin: 0;
            color: #5c6b84;
            font-size: 13px;
            line-height: 1.6;
        }
        .resume-route-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .resume-route-meta .pill,
        .resume-route-meta .focus-pill {
            flex: 0 0 auto;
        }
        .resume-stat-grid {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .resume-stat {
            border: 1px solid #d7e4f5;
            border-radius: 12px;
            background: #fff;
            padding: 10px 12px;
            display: grid;
            gap: 4px;
        }
        .resume-stat span {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #62728b;
        }
        .resume-stat strong {
            color: #102849;
            font-size: 20px;
            line-height: 1.1;
        }
        .focus-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: fit-content;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #d5e3f6;
            background: #f6f9ff;
            color: #31588f;
        }
        .focus-pill--task {
            background: #fff6ea;
            color: #a86112;
            border-color: #f0d7b7;
        }
        .focus-pill--quiz {
            background: #edf4ff;
            color: #1c56b5;
            border-color: #c8d8f5;
        }
        .action-queue {
            border: 1px solid #d7deea;
            border-radius: 14px;
            background: #fff;
            padding: 16px;
            display: grid;
            gap: 12px;
            align-content: start;
            box-shadow: 0 12px 24px rgba(18, 42, 86, 0.05);
        }
        .action-queue-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: end;
        }
        .action-queue-head h3 {
            margin: 0;
            font-size: 20px;
        }
        .action-queue-head p {
            margin: 4px 0 0;
            color: #617089;
            font-size: 13px;
        }
        .queue-summary {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .queue-summary-box {
            border: 1px solid #dce4f1;
            border-radius: 12px;
            background: #f8fbff;
            padding: 10px 12px;
            display: grid;
            gap: 4px;
        }
        .queue-summary-box span {
            color: #617089;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .queue-summary-box strong {
            color: #102849;
            font-size: 20px;
            line-height: 1.1;
        }
        .queue-list {
            display: grid;
            gap: 8px;
            max-height: 320px;
            overflow: auto;
            padding-right: 4px;
            align-content: start;
            scrollbar-width: thin;
        }
        .queue-list::-webkit-scrollbar {
            width: 8px;
        }
        .queue-list::-webkit-scrollbar-thumb {
            background: #c8d6ea;
            border-radius: 999px;
        }
        .queue-item {
            text-decoration: none;
            color: inherit;
            border: 1px solid #dde6f3;
            border-radius: 12px;
            background: #f9fbff;
            padding: 10px 12px;
            display: grid;
            gap: 4px;
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }
        .queue-item:hover {
            transform: translateY(-2px);
            border-color: #bfd1ef;
            box-shadow: 0 14px 24px rgba(18, 42, 86, 0.08);
        }
        .queue-item-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: start;
        }
        .queue-item strong {
            color: #102849;
            font-size: 15px;
            line-height: 1.3;
        }
        .queue-item p {
            margin: 0;
            color: #5a6b84;
            font-size: 13px;
        }
        .queue-meta {
            color: #6a7890;
            font-size: 12px;
        }
        .queue-tag {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .queue-tag--task {
            background: #fff4e7;
            color: #a65e0e;
        }
        .queue-tag--quiz {
            background: #edf4ff;
            color: #1d56b3;
        }
        .submission-card--done {
            border-color: #cfe5d9;
            background: linear-gradient(180deg, #f7fffb 0%, #ffffff 100%);
        }
        .submission-card--pending {
            border-color: #d7deea;
        }
        .submission-card--revision {
            border-color: #f0d7b8;
            background: linear-gradient(180deg, #fffaf3 0%, #ffffff 100%);
        }
        .submission-empty {
            border: 1px dashed #ccd8ea;
            border-radius: 12px;
            background: #f9fbff;
            padding: 18px;
            color: #5c6b84;
            font-size: 13px;
        }
        .certificate-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }
        .certificate-card {
            border: 1px solid #d7e4f8;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            padding: 16px;
            display: grid;
            gap: 10px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.05);
        }
        .certificate-card-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 10px;
        }
        .certificate-card h4 {
            margin: 0;
            color: #102849;
            font-size: 19px;
            line-height: 1.25;
        }
        .certificate-meta {
            margin: 0;
            color: #5a6b84;
            font-size: 13px;
            line-height: 1.65;
        }
        .certificate-code {
            color: #6d7d95;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .student-dashboard-columns {
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.85fr);
            align-items: start;
        }
        .student-dashboard-columns > * {
            min-width: 0;
        }
        .student-dashboard-main,
        .student-dashboard-side {
            display: grid;
            gap: 16px;
            align-content: start;
        }
        .student-column-group {
            display: grid;
            gap: 8px;
            align-content: start;
        }
        .student-column-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 10px;
            border: 1px solid #d6e1f3;
            background: #edf3ff;
            color: #335689;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .dashboard-section {
            border: 1px solid #d7deea;
            border-radius: 14px;
            background: #fff;
            padding: 16px;
            display: grid;
            gap: 12px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.05);
        }
        .dashboard-section--side {
            padding: 14px;
        }
        .dashboard-section .section-head {
            align-items: center;
        }
        .dashboard-section .section-head h2 {
            font-size: 22px;
        }
        .dashboard-section .section-head p {
            max-width: 56ch;
        }
        .dashboard-section-body {
            display: grid;
            gap: 10px;
        }
        .student-dashboard-side .topic-grid,
        .student-dashboard-side .quick-actions-grid {
            grid-template-columns: 1fr;
        }
        .notification-feed {
            display: grid;
            gap: 12px;
        }
        .notification-summary {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            border: 1px solid #dbe6f5;
            border-radius: 18px;
            padding: 14px 16px;
            background:
                radial-gradient(circle at top right, rgba(42, 121, 218, 0.12), rgba(42, 121, 218, 0) 34%),
                linear-gradient(180deg, #ffffff 0%, #f6faff 100%);
            box-shadow: 0 12px 22px rgba(18, 42, 86, 0.05);
        }
        .notification-summary-copy {
            display: grid;
            gap: 4px;
        }
        .notification-summary-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 9px;
            background: #edf4ff;
            color: #2f5c96;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .notification-summary strong {
            color: #102849;
            font-size: 18px;
            line-height: 1.2;
        }
        .notification-summary p {
            margin: 0;
            color: #61758f;
            font-size: 13px;
            line-height: 1.6;
        }
        .notification-summary-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }
        .notification-summary-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            background: #eff5ff;
            color: #2f5c96;
            font-size: 11px;
            font-weight: 800;
        }
        .notification-summary-btn,
        .notification-inline-btn {
            border: 1px solid #c7d8f1;
            border-radius: 999px;
            background: #ffffff;
            color: #145fd1;
            font-size: 11px;
            font-weight: 800;
            padding: 6px 10px;
            cursor: pointer;
            transition: 160ms ease;
        }
        .notification-summary-btn:hover,
        .notification-inline-btn:hover {
            border-color: #a9c4ec;
            background: #eef5ff;
            transform: translateY(-1px);
        }
        .notification-list {
            display: grid;
            gap: 12px;
        }
        .notification-card {
            position: relative;
            border: 1px solid #dbe5f4;
            border-radius: 16px;
            background:
                radial-gradient(circle at top right, rgba(65, 124, 217, 0.08), rgba(65, 124, 217, 0) 36%),
                linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            padding: 14px;
            display: grid;
            gap: 8px;
            box-shadow: 0 12px 24px rgba(18, 42, 86, 0.06);
        }
        .notification-card::before {
            content: '';
            position: absolute;
            inset: 14px auto 14px 0;
            width: 4px;
            border-radius: 999px;
            background: rgba(42, 121, 218, 0.16);
        }
        .notification-card--quiz::before {
            background: linear-gradient(180deg, #2a79da 0%, #5ea5ff 100%);
        }
        .notification-card--submission::before {
            background: linear-gradient(180deg, #c8851a 0%, #f0b257 100%);
        }
        .notification-card--broadcast::before {
            background: linear-gradient(180deg, #2a79da 0%, #39bbae 100%);
        }
        .notification-card--system::before {
            background: linear-gradient(180deg, #66758d 0%, #93a6c0 100%);
        }
        .notification-card--unread::before {
            background: linear-gradient(180deg, #2a79da 0%, #39bbae 100%);
            box-shadow: 0 6px 12px rgba(42, 121, 218, 0.24);
        }
        .notification-card-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
        }
        .notification-card-lead {
            display: flex;
            gap: 12px;
            align-items: start;
            min-width: 0;
            flex: 1 1 auto;
        }
        .notification-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-content: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.05em;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        }
        .notification-avatar--broadcast {
            background: linear-gradient(145deg, #edf4ff 0%, #ddebff 100%);
            color: #245ebc;
        }
        .notification-avatar--quiz {
            background: linear-gradient(145deg, #edf4ff 0%, #d7e9ff 100%);
            color: #245ebc;
        }
        .notification-avatar--submission {
            background: linear-gradient(145deg, #fff5e8 0%, #ffe4bc 100%);
            color: #a96409;
        }
        .notification-avatar--system {
            background: linear-gradient(145deg, #f1f5fb 0%, #dde6f3 100%);
            color: #536a8d;
        }
        .notification-card-copy {
            display: grid;
            gap: 4px;
            min-width: 0;
            flex: 1 1 auto;
        }
        .notification-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 9px;
            background: #edf4ff;
            color: #2e5c99;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .notification-time {
            color: #6880a4;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .notification-card-side {
            display: grid;
            justify-items: end;
            gap: 8px;
            flex: 0 0 auto;
        }
        .notification-card strong {
            color: #102849;
            font-size: 15px;
            line-height: 1.35;
        }
        .notification-card .muted {
            margin: 0;
            font-size: 13px;
            line-height: 1.65;
        }
        .notification-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .notification-tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            background: #eff5ff;
            color: #315b92;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .notification-tag--soft {
            background: #f4f7fb;
            color: #6d7d95;
        }
        @media (max-width: 720px) {
            .notification-summary,
            .notification-card-top {
                grid-template-columns: 1fr;
            }
            .notification-summary {
                display: grid;
            }
            .notification-summary-actions {
                justify-content: flex-start;
            }
            .notification-card-top {
                display: grid;
            }
            .notification-card-side {
                justify-items: start;
            }
        }
        .quick-actions-card h2 {
            margin: 0;
            font-size: 22px;
            line-height: 1.15;
        }
        .quick-actions-card p {
            margin: 4px 0 0;
            color: #617089;
            font-size: 13px;
        }
        .quick-actions-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .quick-action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid #d7deea;
            background: #f8fbff;
            color: #15335c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }
        .quick-action-link:hover {
            transform: translateY(-1px);
            border-color: #bcd0ef;
            box-shadow: 0 14px 24px rgba(18, 42, 86, 0.08);
        }
        .student-side-card h3 {
            margin: 0 0 10px;
            font-size: 22px;
        }
        .student-side-card .stack {
            display: grid;
            gap: 10px;
        }
        .mini-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #0f59c7;
            background: #edf3ff;
            border-radius: 999px;
            padding: 5px 10px;
            width: fit-content;
        }
        .dash-hero {
            background: linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%);
            color: #fff;
            border-radius: 14px;
            padding: 20px 22px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .dash-hero.with-image {
            background-size: cover;
            background-position: center;
        }
        .dash-hero::before { content: none; }
        .dash-hero::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.09);
            right: -40px;
            top: -60px;
            z-index: 0;
        }
        .hero-kicker { margin: 0; text-transform: uppercase; letter-spacing: 0.7px; font-size: 11px; opacity: 0.88; }
        .hero-title { margin: 5px 0; font-size: 30px; line-height: 1.08; }
        .hero-meta { margin: 0; font-size: 13px; opacity: 0.92; }
        .hero-sub { margin: 7px 0 0; font-size: 13px; opacity: 0.86; }
        .hero-btn {
            display: inline-block;
            text-decoration: none;
            color: #0f4dbf;
            background: #fff;
            font-weight: 700;
            border-radius: 8px;
            padding: 9px 14px;
            margin-top: 10px;
        }
        .hero-ring {
            width: 98px;
            height: 98px;
            border-radius: 50%;
            border: 7px solid rgba(255, 255, 255, 0.85);
            display: grid;
            place-content: center;
            text-align: center;
            z-index: 1;
        }
        .hero-ring b { font-size: 28px; line-height: 1; }
        .hero-ring span { font-size: 10px; text-transform: uppercase; opacity: 0.78; }
        .section-head { display: flex; justify-content: space-between; align-items: end; gap: 10px; }
        .section-head h2 { margin: 0; font-size: 24px; }
        .section-head p { margin: 4px 0 0; font-size: 13px; color: #617089; }
        .section-link { text-decoration: none; color: #0d55cf; font-size: 12px; font-weight: 700; }
        .learning-grid { display: grid; gap: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .course-card {
            border: 1px solid #d7deea;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(17, 36, 66, 0.07);
        }
        .course-top {
            color: #fff;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 68px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .course-lock {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background: rgba(255, 255, 255, 0.9);
            color: #1f2f48;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            text-transform: uppercase;
        }
        .course-card.disabled {
            opacity: 0.7;
            filter: grayscale(0.3);
            pointer-events: none;
        }
        .course-top::after {
            content: none;
        }
        .course-top > * { position: relative; z-index: 1; }
        .icon-box {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.25);
            display: grid;
            place-content: center;
            font-size: 11px;
            font-weight: 700;
        }
        .badge { background: rgba(255, 255, 255, 0.28); border-radius: 999px; padding: 3px 8px; font-size: 10px; font-weight: 700; }
        .course-body { padding: 11px; display: grid; gap: 6px; }
        .pill { display: inline-flex; width: fit-content; background: #eef3fb; color: #30496d; border-radius: 999px; padding: 3px 7px; font-size: 10px; font-weight: 700; }
        .course-body h3 { margin: 0; font-size: 18px; line-height: 1.2; }
        .course-meta { margin: 0; color: #69758e; font-size: 12px; }
        .bar-track { height: 6px; border-radius: 999px; background: #edf1f6; overflow: hidden; }
        .bar-val { height: 100%; border-radius: inherit; }
        .course-foot { display: flex; justify-content: space-between; font-size: 12px; color: #65748d; }
        .stats-grid { display: grid; gap: 12px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .stat-box { background: #fff; border: 1px solid #d7deea; border-radius: 12px; padding: 14px; display: flex; gap: 10px; align-items: center; }
        .stat-icon { width: 34px; height: 34px; border-radius: 9px; background: #edf3ff; color: #1d67d2; display: grid; place-content: center; font-size: 11px; font-weight: 800; }
        .stat-box b { display: block; font-size: 24px; line-height: 1; }
        .stat-box span { color: #5f6c82; font-size: 12px; }
        .split-grid { display: grid; gap: 12px; grid-template-columns: 1fr 1fr; }
        .panel-box { background: #fff; border: 1px solid #d7deea; border-radius: 12px; padding: 14px; }
        .panel-box h3 { margin: 0 0 10px; font-size: 20px; }
        .skill-row { margin-bottom: 10px; }
        .skill-row:last-child { margin-bottom: 0; }
        .skill-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
        .topic-grid { display: grid; gap: 8px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .topic { border: 1px solid #d7deea; border-radius: 10px; padding: 11px; display: flex; gap: 9px; align-items: center; }
        .topic-bullet { width: 30px; height: 30px; border-radius: 8px; background: #eef3fb; color: #2a61b8; display: grid; place-content: center; font-size: 11px; font-weight: 700; }
        .topic p { margin: 0; color: #66758e; font-size: 12px; }
        .recommend-grid { display: grid; gap: 10px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .recommend-card { border: 1px solid #d7deea; border-radius: 12px; overflow: hidden; background: #fff; }
        .recommend-top { color: #fff; padding: 12px; min-height: 66px; display: flex; align-items: center; }
        .recommend-body { padding: 12px; }
        .recommend-body h4 { margin: 6px 0 2px; font-size: 18px; }
        .recommend-meta { margin: 0 0 8px; color: #6e7b93; font-size: 12px; }
        .recommend-foot { display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #65748d; }
        .mini-btn { text-decoration: none; background: #0f59c7; color: #fff; border-radius: 7px; padding: 7px 10px; font-size: 12px; font-weight: 700; }
        .panel-inline-hero {
            border: 1px solid #d7deea;
            border-radius: 14px;
            padding: 18px 20px;
            color: #fff;
            background: linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%);
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.08);
        }
        .panel-inline-hero h3 { margin: 0 0 4px; font-size: 24px; }
        .panel-inline-hero p { margin: 0; font-size: 13px; opacity: 0.92; }
        .panel-inline-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .panel-inline-kpi {
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #f8fbff;
            padding: 12px;
        }
        .panel-inline-kpi p { margin: 0; color: #63708a; font-size: 12px; }
        .panel-inline-kpi b { display: block; margin-top: 5px; font-size: 24px; line-height: 1; }
        .accent-blue { background: linear-gradient(115deg, #1f5fcc, #5b92df); }
        .accent-green { background: linear-gradient(115deg, #21a86b, #67c796); }
        .accent-violet { background: linear-gradient(115deg, #7047af, #9c7acb); }
        .accent-orange { background: linear-gradient(115deg, #e17a0c, #f3ac63); }
        .accent-red { background: linear-gradient(115deg, #c94f43, #dd8e87); }
        .accent-teal { background: linear-gradient(115deg, #0c95a7, #4cc0ca); }
        @media (max-width: 1180px) {
            .learning-grid, .recommend-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .panel-inline-grid { grid-template-columns: 1fr; }
            .student-focus-grid { grid-template-columns: 1fr; }
            .student-dashboard-columns { grid-template-columns: 1fr; }
        }
        @media (max-width: 900px) {
            .dash-hero { grid-template-columns: 1fr; }
            .split-grid { grid-template-columns: 1fr; }
            .resume-panel-head, .action-queue-head { display: grid; }
            .resume-stat-grid, .queue-summary { grid-template-columns: 1fr; }
            .quick-actions-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .learning-grid, .recommend-grid, .stats-grid, .topic-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 24px; }
            .resume-copy h2 { font-size: 24px; }
        }
        .demo-grid { display: grid; gap: 12px; }
        .demo-video-slider {
            display: grid;
            gap: 14px;
            margin-top: 8px;
        }
        .demo-video-viewport {
            overflow: hidden;
            border-radius: 30px;
        }
        .demo-video-track {
            display: flex;
            width: 100%;
            transition: transform 380ms ease;
            will-change: transform;
        }
        .demo-video-slide {
            flex: 0 0 100%;
            min-width: 100%;
        }
        .demo-video {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--card);
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(300px, 0.85fr) minmax(380px, 1.35fr);
            gap: 0;
            box-shadow: 0 28px 56px rgba(18, 42, 86, 0.14);
            position: relative;
            width: 100%;
        }
        .demo-video:hover {
            box-shadow: 0 34px 64px rgba(18, 42, 86, 0.18);
        }
        .demo-video::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 77, 191, 0.08), rgba(15, 77, 191, 0) 45%);
            pointer-events: none;
        }
        .demo-video-cover {
            min-height: 520px;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0) 36%),
                linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 55%, #0d3f8f 100%);
            color: #fff;
            padding: 52px;
            display: grid;
            align-content: center;
            gap: 14px;
            position: relative;
            overflow: hidden;
        }
        .demo-video-cover::after {
            content: '';
            position: absolute;
            inset: auto -42px -48px auto;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.10);
        }
        .demo-video-cover::before {
            content: '';
            position: absolute;
            inset: auto auto -18px -18px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0));
            animation: demoFloat 8s ease-in-out infinite;
        }
        .demo-video-cover > * { position: relative; z-index: 1; }
        .demo-video-cover h3 { margin: 0; font-size: 58px; line-height: 1.01; max-width: 10ch; }
        .demo-video-cover p { margin: 0; line-height: 1.85; font-size: 17px; max-width: 56ch; }
        .demo-video-cover .hero-note {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 6px 0 2px;
        }
        .demo-video-cover .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.95);
        }
        .demo-video-cover .btn,
        .demo-video-cover .btn-soft {
            width: fit-content;
            border-color: rgba(255, 255, 255, 0.65);
            background: rgba(255, 255, 255, 0.96);
            color: #0f4dbf;
        }
        .demo-video-cover .hero-play {
            padding: 14px 22px;
            border-radius: 999px;
            box-shadow: 0 12px 26px rgba(4, 20, 56, 0.18);
        }
        .demo-video-cover .btn:hover,
        .demo-video-cover .btn-soft:hover {
            background: #fff;
        }
        .demo-video-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.92);
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
        }
        .demo-video-thumb {
            background:
                radial-gradient(circle at 50% 18%, rgba(15, 77, 191, 0.08), rgba(15, 77, 191, 0) 44%),
                #f3f6fb;
            position: relative;
            display: grid;
            align-items: stretch;
            justify-items: stretch;
            min-height: 520px;
            overflow: hidden;
        }
        .demo-video-thumb video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            background: #07162f;
            transition: transform 360ms ease, filter 360ms ease;
        }
        .demo-video-thumb iframe {
            width: 100%;
            height: 100%;
            display: block;
            border: 0;
            background: #07162f;
        }
        .demo-video-thumb::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 18, 40, 0.10), rgba(5, 18, 40, 0.20)),
                radial-gradient(circle at center, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0) 40%);
            pointer-events: none;
        }
        .demo-video-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .demo-video-nav-group {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .demo-video-arrow {
            border: 1px solid #c7d7ef;
            background: #fff;
            color: #173a73;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            display: inline-grid;
            place-content: center;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(18, 42, 86, 0.08);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }
        .demo-video-arrow:hover {
            transform: translateY(-1px);
            border-color: #9ebae7;
            box-shadow: 0 14px 24px rgba(18, 42, 86, 0.14);
        }
        .demo-video-dots {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .demo-video-dot {
            border: 0;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #c9d6e8;
            padding: 0;
            cursor: pointer;
            transition: width 180ms ease, background 180ms ease, transform 180ms ease;
        }
        .demo-video-dot.active {
            width: 28px;
            background: #0f4dbf;
            transform: scaleY(1.05);
        }
        .demo-video-counter {
            color: #55657d;
            font-size: 13px;
            font-weight: 700;
        }
        .demo-video-empty {
            min-height: 420px;
            display: grid;
            place-content: center;
            text-align: center;
            color: #506179;
            font-size: 14px;
            letter-spacing: 0.03em;
        }
        .demo-review-slider .demo-video-cover {
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0) 36%),
                linear-gradient(120deg, #0b3b7d 0%, #155cc7 52%, #0a8d7f 100%);
        }
        .demo-review-slider .demo-video {
            grid-template-columns: 1fr;
        }
        .demo-review-slider .demo-video-badge {
            padding: 3px 8px;
            font-size: 10px;
        }
        .demo-review-slider .demo-video-cover h3 {
            max-width: none;
            font-size: clamp(20px, 1.8vw, 26px);
            line-height: 1.16;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .demo-review-slider .demo-video-cover p {
            font-size: 12px;
            line-height: 1.45;
            max-width: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .demo-review-slider .demo-video-cover {
            order: 2;
            min-height: auto;
            padding: 14px 18px 18px;
            gap: 8px;
        }
        .demo-review-slider .demo-video-cover .hero-note {
            gap: 5px;
            margin: 0;
        }
        .demo-review-slider .demo-review-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .demo-review-slider .demo-review-actions .hero-note {
            flex: 1 1 240px;
        }
        .demo-review-slider .demo-video-cover .hero-chip {
            padding: 3px 7px;
            font-size: 9px;
        }
        .demo-review-slider .demo-video-cover .hero-play {
            padding: 9px 15px;
            font-size: 12px;
            margin-left: auto;
        }
        .demo-review-slider .demo-video-thumb {
            order: 1;
            min-height: clamp(360px, 46vw, 640px);
        }
        .demo-task-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background:
                linear-gradient(180deg, rgba(15, 77, 191, 0.03), rgba(15, 77, 191, 0)),
                var(--card);
            padding: 16px;
            display: grid;
            gap: 10px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.07);
        }
        .demo-task-video {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #d7deea;
            background: #08152c;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.03);
        }
        .demo-task-video video {
            width: 100%;
            display: block;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            background: #08152c;
        }
        .demo-task-video-note {
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #244a86;
            background: #eef4ff;
            border-top: 1px solid #d7e2f5;
        }
        .demo-task-card strong { font-size: 18px; }
        .demo-task-card:hover { transform: translateY(-2px); }
        .demo-task-meta { color: var(--muted); font-size: 13px; line-height: 1.55; }
        .demo-task-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .demo-task-actions .btn-soft {
            border-color: #cbdaf3;
            background: #f4f8ff;
            color: #1f4fa3;
        }
        .demo-task-actions .btn-soft:hover {
            background: #eef4ff;
        }
        .demo-submit-panel {
            border: 1px solid #d7deea;
            border-radius: 14px;
            background: #fff;
            padding: 14px;
            display: grid;
            gap: 12px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.06);
        }
        .demo-submit-block {
            border: 1px solid #dce4f1;
            border-radius: 12px;
            background: #f8fbff;
            padding: 12px;
            display: grid;
            gap: 8px;
        }
        .demo-submit-block h4 {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6c7c94;
        }
        .demo-submit-block textarea,
        .demo-submit-block input[type="file"] {
            width: 100%;
        }
        .demo-submit-preview {
            border-radius: 12px;
            background: #eef4ff;
            border: 1px solid #cbdaf3;
            padding: 12px;
            display: grid;
            gap: 6px;
        }
        .demo-submit-preview strong {
            font-size: 13px;
            color: #244a86;
        }
        .demo-submit-preview p {
            margin: 0;
            color: #47566b;
            font-size: 13px;
            line-height: 1.55;
            white-space: pre-wrap;
        }
        .demo-submit-file {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
            border-top: 1px dashed #c9d7ee;
            padding-top: 10px;
            margin-top: 2px;
        }
        .demo-submit-file span {
            font-size: 12px;
            color: #5b6a82;
        }
        .demo-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: fit-content;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #1f4fa3;
            background: #eef4ff;
            border: 1px solid #c8d8f5;
        }
        .submission-grid {
            display: grid;
            gap: 10px;
        }
        .submission-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--card);
            padding: 12px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.06);
            display: grid;
            gap: 8px;
        }
        .submission-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: start;
            flex-wrap: wrap;
        }
        .submission-head-left {
            display: grid;
            gap: 4px;
        }
        .submission-head strong {
            display: block;
            font-size: 15px;
        }
        .submission-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .submission-answer {
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.55;
        }
        .submission-answer-box {
            border: 1px solid #dce4f1;
            border-radius: 12px;
            background: #f8fbff;
            padding: 10px;
        }
        .submission-answer-box h4,
        .submission-doc-box h4 {
            margin: 0 0 6px;
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6c7c94;
        }
        .submission-doc-box {
            border: 1px solid #dce4f1;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
            align-items: center;
        }
        .submission-doc-box .doc-name {
            color: #425066;
            font-size: 13px;
            word-break: break-word;
        }
        .submission-meta {
            color: var(--muted);
            font-size: 12px;
        }
        .submission-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .tab-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .tab-row.centered { justify-content: center; }
        .tab-row .tab-btn { transition: 180ms ease; }
        .tab-row .tab-btn:hover { transform: translateY(-1px); border-color: #b6c7e8; }
        .tab-btn {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            background: var(--card);
            color: var(--text);
            cursor: pointer;
        }
        .tab-btn.active {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: #bcd3f7;
            box-shadow: 0 10px 20px rgba(28, 95, 202, 0.18);
        }
        .tab-btn.main-tab {
            padding: 8px 16px;
            font-size: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            border: 1px solid #c6d4ee;
            background: #fff;
        }
        .tab-btn.main-tab.active {
            background: linear-gradient(135deg, rgba(28, 95, 202, 0.12), rgba(73, 142, 255, 0.08));
            border-color: #9cbcf4;
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeUp 240ms ease; }
        .subtab-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; margin-bottom: 10px; justify-content: center; }
        .subtab-btn {
            border: 1px dashed #c7d4ea;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            background: #fff;
            color: var(--text);
            cursor: pointer;
            transition: 160ms ease;
        }
        .subtab-btn:hover { transform: translateY(-1px); }
        .subtab-btn.active {
            border-style: solid;
            border-color: #bcd3f7;
            background: #eef4ff;
            color: #1f4fa3;
        }
        .subtab-label {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #7a8aa6;
            margin-top: 6px;
        }
        .demo-course-grid { display: grid; gap: 18px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .demo-course-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 360px));
            justify-content: center;
            justify-items: center;
            max-width: 1480px;
            margin: 0 auto;
        }
        .demo-course-tile {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: var(--shadow);
            color: inherit;
            width: 100%;
            max-width: 360px;
        }
        .tab-panel {
            padding-top: 12px;
        }
        .demo-course-top {
            min-height: 220px;
            padding: 18px;
            color: #fff;
            display: flex;
            align-items: end;
            background-size: cover;
            background-position: center;
        }
        .demo-course-body { padding: 16px 18px 18px; display: grid; gap: 10px; }
        .badge-lock {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #7a879b;
            background: #f1f4f9;
            border-radius: 999px;
            padding: 4px 8px;
        }
        .demo-empty {
            border: 1px dashed var(--line);
            border-radius: 14px;
            padding: 16px;
            color: var(--muted);
            background: linear-gradient(180deg, rgba(15, 77, 191, 0.03), rgba(15, 77, 191, 0));
        }
        .demo-section-title {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 10px;
            margin-bottom: 14px;
        }
        .demo-section-title h2 {
            margin: 0;
            font-size: 24px;
        }
        .demo-section-title p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }
        @media (max-width: 980px) {
            .demo-video { grid-template-columns: 1fr; }
            .demo-video-cover,
            .demo-video-thumb { min-height: 320px; }
            .demo-video-cover h3 { font-size: 38px; max-width: none; }
            .demo-video-nav {
                justify-content: center;
            }
            .demo-course-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                max-width: 100%;
            }
            .demo-course-tile { max-width: 100%; }
        }
        @keyframes demoFloat {
            0%, 100% { transform: translate3d(0, 0, 0); opacity: 0.65; }
            50% { transform: translate3d(16px, -12px, 0); opacity: 1; }
        }
        .demo-video:hover .demo-video-thumb video {
            transform: scale(1.02);
            filter: saturate(1.05);
        }
    </style>

    <div class="dash-grid {{ $isStudent ? 'student-mode' : '' }}">
        @if ($isStudent && !empty($heroCourse))
            @php
                $heroThumb = $heroCourse['thumbnail_url'] ?? '';
                $heroBg = $heroThumb
                    ? "url('{$heroThumb}')"
                    : 'linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%)';
            @endphp
            <section class="dash-hero with-image" style="background-image: {{ $heroBg }};">
                <div style="z-index: 1;">
                    @if ($heroKicker !== '')
                        <p class="hero-kicker">{{ $heroKicker }}</p>
                    @endif
                    <h1 class="hero-title">{{ $heroCourse['title'] ?? 'Learning Dashboard' }}</h1>
                    <p class="hero-meta">{{ $heroCourse['provider'] ?? 'LMS Academy' }}</p>
                    <a class="hero-btn" href="{{ $heroResumeRoute }}">Continue</a>
                    <p class="hero-sub">{{ $heroCourse['progress_percent'] ?? 0 }}% complete &middot; {{ $heroCourse['hours_done'] ?? 0 }}h of {{ $heroCourse['hours_total'] ?? 0 }}h</p>
                </div>
                <div class="hero-ring">
                    <b>{{ $heroCourse['progress_percent'] ?? 0 }}%</b>
                    <span>Done</span>
                </div>
            </section>
        @endif

        @if ($dashboardMode === 'demo')
            @php
                $demoVideoCount = $demoFeatureVideos->count();
            @endphp
            <section class="card">
                <div class="page-head">
                    <div>
                        <h2>Welcome Demo User</h2>
                        <p>Explore the demo experience, browse every feature video, and complete your tasks.</p>
                    </div>
                </div>
            </section>

            @if ($notifications->isNotEmpty())
                <section class="card">
                    <div class="page-head">
                        <div>
                            <h2>Notifications</h2>
                            <p>Announcements and updates sent to your demo account appear here.</p>
                        </div>
                    </div>
                    @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
                </section>
            @endif

            <section class="demo-video-slider" data-demo-video-slider>
                <div class="demo-video-viewport">
                    <div class="demo-video-track" data-demo-video-track>
                        @forelse ($demoFeatureVideos as $index => $video)
                            <article class="demo-video-slide {{ $index === 0 ? 'active' : '' }}" data-demo-video-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                                <div class="demo-video">
                                    <div class="demo-video-cover">
                                        <span class="demo-video-badge">Feature Video {{ str_pad((string) ($video->position ?? ($index + 1)), 2, '0', STR_PAD_LEFT) }}</span>
                                        <h3>{{ $video->title ?: 'Feature Video' }}</h3>
                                        <p>{{ $video->description ?: 'See how our learning platform works in minutes.' }}</p>
                                        <div class="hero-note">
                                            <span class="hero-chip">Position {{ $video->position ?? ($index + 1) }}</span>
                                            <span class="hero-chip">{{ $demoVideoCount }} video{{ $demoVideoCount === 1 ? '' : 's' }} live</span>
                                        </div>
                                        <a class="btn btn-soft hero-play" href="{{ route('demo-feature-video.show', $video) }}" target="_blank" rel="noopener">Open Full Video</a>
                                    </div>
                                    <div class="demo-video-thumb">
                                        <video controls preload="metadata" controlslist="nodownload" playsinline>
                                            <source src="{{ route('demo-feature-video.show', $video) }}" type="{{ $video->file_mime ?: 'video/mp4' }}">
                                        </video>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="demo-video-slide active" data-demo-video-slide aria-hidden="false">
                                <div class="demo-video demo-video-empty">
                                    <div class="demo-video-cover">
                                        <span class="demo-video-badge">Feature Video</span>
                                        <h3>Videos Coming Soon</h3>
                                        <p>Feature videos added here will appear automatically for demo users.</p>
                                        <div class="hero-note">
                                            <span class="hero-chip">0 videos live</span>
                                            <span class="hero-chip">Dynamic slider ready</span>
                                        </div>
                                        <div class="btn btn-soft hero-play">No video yet</div>
                                    </div>
                                    <div class="demo-video-thumb">
                                        <div class="demo-empty" style="height: 100%; display: grid; place-content: center; text-align: center;">
                                            FEATURE VIDEO
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($demoVideoCount > 1)
                    <div class="demo-video-nav">
                        <div class="demo-video-nav-group">
                            <button class="demo-video-arrow" type="button" data-demo-video-prev aria-label="Show previous feature video">&#8249;</button>
                            <button class="demo-video-arrow" type="button" data-demo-video-next aria-label="Show next feature video">&#8250;</button>
                        </div>
                        <div class="demo-video-dots" aria-label="Demo video slider navigation">
                            @foreach ($demoFeatureVideos as $index => $video)
                                <button
                                    class="demo-video-dot {{ $index === 0 ? 'active' : '' }}"
                                    type="button"
                                    data-demo-video-dot="{{ $index }}"
                                    aria-label="Show feature video {{ $index + 1 }}: {{ $video->title ?: 'Feature Video' }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                ></button>
                            @endforeach
                        </div>
                        <div class="demo-video-counter" data-demo-video-counter>1 / {{ $demoVideoCount }}</div>
                    </div>
                @endif
            </section>

            <section class="card">
                <div class="demo-section-title">
                    <div>
                        <h2>Demo Tasks</h2>
                        <p>Download resources, review submissions, and upload your answer.</p>
                    </div>
                </div>
                <div class="demo-grid">
                    @forelse ($demoAssignments as $row)
                        @php
                            $task = $row['task'];
                            $submission = $row['submission'];
                            $assignment = $row['assignment'];
                            $canDownloadResource = !empty($task?->resource_file_path);
                            $hasToolsLink = !empty($task?->ai_video_url);
                            $hasTaskActions = $canDownloadResource || $hasToolsLink;
                        @endphp
                        <div class="demo-task-card">
                            @if (!empty($task?->task_video_path))
                                <div class="demo-task-video">
                                    <div class="demo-task-video-note">Watch this task video first, then complete the task below.</div>
                                    <video controls preload="metadata" controlslist="nodownload" playsinline>
                                        <source src="{{ route('demo-tasks.video', $task) }}" type="{{ $task->task_video_mime ?: 'video/mp4' }}">
                                    </video>
                                </div>
                            @endif
                            <strong>{{ $task?->title ?? 'Demo Task' }}</strong>
                            <div class="demo-task-meta">{{ $task?->description ?? 'Complete this task to see how submissions work.' }}</div>
                            {{-- <div class="actions-row">
                                @if ($assignment)
                                    <span class="demo-status">Assigned</span>
                                @else
                                    <span class="demo-status">Available</span>
                                @endif
                                @if (!empty($task?->resource_file_path))
                                    <span class="demo-status">Download Ready</span>
                                @endif
                                @if ($submission)
                                    <span class="demo-status">Submitted</span>
                                @endif
                            </div> --}}
                            @if ($hasTaskActions)
                                <div class="demo-task-actions">
                                    @if ($canDownloadResource)
                                        <a class="btn btn-soft" href="{{ route('demo-tasks.download', $task) }}">Download Resource</a>
                                    @endif
                                    @if ($hasToolsLink)
                                        <a class="btn btn-soft" href="{{ $task->ai_video_url }}" target="_blank" rel="noopener">Tools</a>
                                    @endif
                                </div>
                            @endif
                            @if ($assignment)
                                <form method="POST" action="{{ route('demo-assignments.submit', $assignment) }}" enctype="multipart/form-data" class="demo-submit-panel">
                                    @csrf
                                    <div class="demo-submit-block">
                                        <h4>Your Answer</h4>
                                        <textarea name="answer_text" rows="4" placeholder="Type your answer here..."></textarea>
                                        <div class="muted" style="font-size: 12px;">Write a short response, then upload a supporting document if needed.</div>
                                    </div>
                                    <div class="demo-submit-block">
                                        <h4>Upload Document</h4>
                                        <input type="file" name="submission_file" accept="*/*">
                                        <div class="muted" style="font-size: 12px;">Upload any document or media file (PDF, DOCX, PPT, ZIP, video, image).</div>
                                    </div>
                                    <div class="demo-task-actions">
                                        <button class="btn btn-soft" type="submit">Submit Demo Task</button>
                                        @if ($submission && $submission->file_path)
                                            <a class="btn btn-soft" href="{{ route('demo-tasks.submissions.download', $submission) }}">Download Your File</a>
                                        @endif
                                    </div>
                                    @if ($submission)
                                        <div class="demo-submit-preview">
                                            <strong>Last Submission</strong>
                                            @if ($submission->answer_text)
                                                <p>{{ $submission->answer_text }}</p>
                                            @else
                                                <p>No text answer submitted.</p>
                                            @endif
                                            <div class="demo-submit-file">
                                                <span>{{ $submission->file_name ?: 'No file uploaded' }}</span>
                                                @if ($submission->file_path)
                                                    <a class="btn btn-soft" href="{{ route('demo-tasks.submissions.download', $submission) }}">Download Uploaded File</a>
                                                @endif
                                            </div>
                                            <span class="muted">Last submitted {{ optional($submission->submitted_at)->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                </form>
                            @else
                                <div class="upload-empty">
                                    This task was uploaded by admin.
                                    @if ($canDownloadResource)
                                        You can access the resource above, but submission is disabled until it is assigned to you.
                                    @else
                                        Submission is disabled until it is assigned to you.
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="muted">No demo tasks available yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <div class="page-head">
                    <h2>Browse Courses</h2>
                </div>
                <div class="category-head">
                    <p>Select a main category, then choose a subcategory to filter courses.</p>
                </div>
                <div class="tab-row centered" id="categoryTabs" style="margin-top: 12px;">
                    @foreach ($demoCategories as $index => $category)
                        <button class="tab-btn main-tab {{ $index === 0 ? 'active' : '' }}" type="button" data-tab="{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
                @foreach ($demoCategories as $index => $category)
                    @php
                        $tabCourses = $category->courses
                            ->concat($category->children->flatMap->courses)
                            ->unique('id')
                            ->values();
                    @endphp
                    <div class="tab-panel {{ $index === 0 ? 'active' : '' }}" data-tab-panel="{{ $category->id }}">
                        <div class="subtab-label">Subcategories</div>
                        <div class="subtab-row" data-subtabs>
                            <button class="subtab-btn active" type="button" data-subtab="all">All</button>
                            @foreach ($category->children as $child)
                                <button class="subtab-btn" type="button" data-subtab="{{ $child->id }}">{{ $child->name }}</button>
                            @endforeach
                        </div>
                        <div class="category-divider"></div>
                        <div class="demo-course-grid">
                            @forelse ($tabCourses as $course)
                                @php
                                    $thumb = $course->thumbnail_url ?: '';
                                    $bg = $thumb
                                        ? "url('{$thumb}')"
                                        : 'linear-gradient(120deg, #1c5fca, #3aa77a)';
                                    $courseCategory = $course->subcategory?->name ?? $course->category?->name ?? $category->name;
                                    $subCategoryId = $course->subcategory?->id ? (string) $course->subcategory->id : 'none';
                                @endphp
                                <div class="demo-course-tile" data-subcat="{{ $subCategoryId }}">
                                    <div class="demo-course-top" style="background-image: {{ $bg }};">
                                        <strong>{{ $course->title }}</strong>
                                    </div>
                                    <div class="demo-course-body">
                                        <div class="muted">Category: {{ $courseCategory }}</div>
                                        <span class="badge-lock">Locked</span>
                                    </div>
                                </div>
                            @empty
                                <p class="muted">No courses in this category.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </section>

            @php
                $demoReviewCount = $demoReviewVideos->count();
            @endphp
            <section class="card">
                <div class="page-head">
                    <div>
                        <h2>Reviews</h2>
                    </div>
                </div>

                <div class="demo-video-slider demo-review-slider" data-demo-video-slider>
                    <div class="demo-video-viewport">
                        <div class="demo-video-track" data-demo-video-track>
                            @forelse ($demoReviewVideos as $index => $video)
                                <article class="demo-video-slide {{ $index === 0 ? 'active' : '' }}" data-demo-video-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                                    <div class="demo-video">
                                        <div class="demo-video-cover">
                                            <span class="demo-video-badge">Review {{ str_pad((string) ($video->position ?? ($index + 1)), 2, '0', STR_PAD_LEFT) }}</span>
                                            <h3>{{ $video->title ?: 'Learner Review' }}</h3>
                                            <p>{{ $video->description ?: 'YouTube review videos added in the admin panel will appear here automatically for demo users.' }}</p>
                                            <div class="demo-review-actions">
                                                <div class="hero-note">
                                                    <span class="hero-chip">Position {{ $video->position ?? ($index + 1) }}</span>
                                                    <span class="hero-chip">YouTube review</span>
                                                    <span class="hero-chip">{{ $demoReviewCount }} review{{ $demoReviewCount === 1 ? '' : 's' }} live</span>
                                                </div>
                                                <a class="btn btn-soft hero-play" href="{{ $video->watch_url }}" target="_blank" rel="noopener">Watch on YouTube</a>
                                            </div>
                                        </div>
                                        <div class="demo-video-thumb">
                                            <iframe
                                                src="{{ $video->embed_url }}&enablejsapi=1"
                                                title="{{ $video->title ?: 'Demo Review Video' }}"
                                                loading="lazy"
                                                referrerpolicy="strict-origin-when-cross-origin"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen
                                                data-demo-youtube-embed
                                            ></iframe>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="demo-video-slide active" data-demo-video-slide aria-hidden="false">
                                    <div class="demo-video demo-video-empty">
                                        <div class="demo-video-cover">
                                            <span class="demo-video-badge">Reviews</span>
                                            <h3>Review Videos Coming Soon</h3>
                                            <p>Once admin or superadmin adds YouTube review videos with positions, they will appear here in this slider.</p>
                                            <div class="hero-note">
                                                <span class="hero-chip">0 reviews live</span>
                                                <span class="hero-chip">YouTube slider ready</span>
                                            </div>
                                            <div class="btn btn-soft hero-play">No review video yet</div>
                                        </div>
                                        <div class="demo-video-thumb">
                                            <div class="demo-empty" style="height: 100%; display: grid; place-content: center; text-align: center;">
                                                DEMO REVIEWS
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($demoReviewCount > 1)
                        <div class="demo-video-nav">
                            <div class="demo-video-nav-group">
                                <button class="demo-video-arrow" type="button" data-demo-video-prev aria-label="Show previous review video">&#8249;</button>
                                <button class="demo-video-arrow" type="button" data-demo-video-next aria-label="Show next review video">&#8250;</button>
                            </div>
                            <div class="demo-video-dots" aria-label="Demo review slider navigation">
                                @foreach ($demoReviewVideos as $index => $video)
                                    <button
                                        class="demo-video-dot {{ $index === 0 ? 'active' : '' }}"
                                        type="button"
                                        data-demo-video-dot="{{ $index }}"
                                        aria-label="Show review video {{ $index + 1 }}: {{ $video->title ?: 'Demo Review Video' }}"
                                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                    ></button>
                                @endforeach
                            </div>
                            <div class="demo-video-counter" data-demo-video-counter>1 / {{ $demoReviewCount }}</div>
                        </div>
                    @endif
                </div>
            </section>
        @elseif (in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true))
            <section class="panel-inline-hero">
                <h3>Admin Panel</h3>
                <p>{{ $panelDescription }}</p>
            </section>
            <section class="stats-grid">
                @foreach ($overviewCards as $card)
                    <div class="stat-box">
                        <div class="stat-icon">{{ $card['code'] }}</div>
                        <div>
                            <b>{{ $card['value'] }}{{ $card['suffix'] ?? '' }}</b>
                            <span>{{ $card['label'] }}</span>
                        </div>
                    </div>
                @endforeach
            </section>

        @endif

        @if ($dashboardMode !== 'demo')
            @if ($isStudent)
                <div class="student-dashboard-columns">
                    <div class="student-dashboard-main">
                        <article class="resume-panel">
                            @if (!empty($studentResumeItem))
                                <div class="resume-panel-head">
                                    <div class="resume-copy">
                                        <span class="mini-cta">Resume Last Lesson</span>
                                        <h2>{{ $studentResumeItem['item_title'] }}</h2>
                                        <p class="resume-note">
                                            {{ $studentResumeItem['item_type'] }} in {{ $studentResumeItem['course_title'] }}.
                                            Jump back in exactly where you stopped.
                                        </p>
                                    </div>
                                    <a class="hero-btn" href="{{ $studentResumeItem['route'] }}">Resume Now</a>
                                </div>

                                <div class="resume-route-meta">
                                    <span class="pill">{{ $studentResumeItem['course_title'] }}</span>
                                    <span class="pill">{{ $studentResumeItem['item_type'] }}</span>
                                    @if (($studentResumeItem['pending_tasks_count'] ?? 0) > 0)
                                        <span class="focus-pill focus-pill--task">{{ $studentResumeItem['pending_tasks_count'] }} task{{ $studentResumeItem['pending_tasks_count'] === 1 ? '' : 's' }} pending</span>
                                    @endif
                                    @if (($studentResumeItem['live_quizzes_count'] ?? 0) > 0)
                                        <span class="focus-pill focus-pill--quiz">{{ $studentResumeItem['live_quizzes_count'] }} live quiz{{ $studentResumeItem['live_quizzes_count'] === 1 ? '' : 'zes' }}</span>
                                    @endif
                                </div>

                                <div class="resume-stat-grid">
                                    <div class="resume-stat">
                                        <span>Course Progress</span>
                                        <strong>{{ $studentResumeItem['progress_percent'] ?? 0 }}%</strong>
                                    </div>
                                    <div class="resume-stat">
                                        <span>Hours Done</span>
                                        <strong>{{ $studentResumeItem['hours_done'] ?? 0 }}h</strong>
                                    </div>
                                    <div class="resume-stat">
                                        <span>Hours Total</span>
                                        <strong>{{ $studentResumeItem['hours_total'] ?? 0 }}h</strong>
                                    </div>
                                </div>
                            @else
                                <div class="resume-copy">
                                    <span class="mini-cta">Resume Last Lesson</span>
                                    <h2>Your learning space is ready</h2>
                                    <p class="resume-note">Once you enroll in courses and open lessons, your exact resume link will appear here automatically.</p>
                                    <div>
                                        <a class="hero-btn" href="{{ route('student.courses') }}">Open My Courses</a>
                                    </div>
                                </div>
                            @endif
                        </article>

                        <div class="student-column-group">
                            <span class="student-column-label">Learning Hub</span>
            @endif

            <section @class(['dashboard-section' => $isStudent])>
                <div class="section-head">
                    <div>
                        <h2>{{ $learningTitle }}</h2>
                        <p>{{ $learningSubtitle }}</p>
                    </div>
                    <a class="section-link" href="{{ $allCoursesRoute }}">{{ $learningActionLabel }}</a>
                </div>
                <div class="learning-grid" @unless($isStudent) style="margin-top: 10px;" @endunless>
                    @forelse ($learningItems as $index => $item)
                        @php
                            $itemRoute = !empty($item['course_id'])
                                ? ($isStudent
                                    ? ($item['resume_route'] ?? route('student.courses.show', $item['course_id']))
                                    : route('courses.show', $item['course_id']))
                                : $allCoursesRoute;
                            $isAssigned = ! $isTrainer || in_array($item['course_id'] ?? 0, $assignedCourseIds ?? [], true);
                        @endphp
                        @if ($isTrainer && ! $isAssigned)
                            <article class="course-card disabled">
                        @else
                            <a href="{{ $itemRoute }}" class="course-card" style="text-decoration: none; color: inherit;">
                        @endif
                            @php
                                $thumb = $item['thumbnail_url'] ?? '';
                                $topStyle = $thumb
                                    ? "background-image: url('{$thumb}')"
                                    : '';
                                $topClass = $thumb ? '' : ($accentClass[$item['accent']] ?? 'accent-blue');
                            @endphp
                            <div class="course-top {{ $topClass }}" @if ($thumb) style="{{ $topStyle }}" @endif>
                                <div class="icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                                <span class="badge">{{ min(100, (int) $item['progress_percent']) }}%</span>
                                @if ($isTrainer && ! $isAssigned)
                                    <span class="course-lock">Locked</span>
                                @endif
                            </div>
                            <div class="course-body">
                                <span class="pill">{{ $item['category'] }}</span>
                                <h3>{{ $item['title'] }}</h3>
                                <p class="course-meta">{{ $item['provider'] }}</p>
                                <div class="bar-track">
                                    <div class="bar-val {{ $accentClass[$item['accent']] ?? 'accent-blue' }}" style="width: {{ min(100, (int) $item['progress_percent']) }}%"></div>
                                </div>
                                <div class="course-foot">
                                    <span>{{ $item['hours_done'] }}h / {{ $item['hours_total'] }}h</span>
                                    <span>{{ $item['progress_percent'] }}%</span>
                                </div>
                                @if ($isStudent)
                                    <span class="mini-cta">Continue</span>
                                @endif
                            </div>
                        @if ($isTrainer && ! $isAssigned)
                            </article>
                        @else
                            </a>
                        @endif
                    @empty
                        <article class="course-card"><div class="course-body"><h3>No learning data yet</h3><p class="course-meta">No assigned or enrolled courses found.</p></div></article>
                    @endforelse
                </div>
            </section>

            @if ($isStudent)
                <section class="dashboard-section">
                    <div class="section-head">
                        <div>
                            <h2>My Submissions</h2>
                            <p>Your latest task and quiz submissions, with quick access back to the lesson.</p>
                        </div>
                        <a class="section-link" href="{{ route('student.history') }}">Open history -&gt;</a>
                    </div>

                    <div class="submission-grid">
                        @forelse ($studentRecentSubmissions as $submission)
                            <article class="submission-card submission-card--{{ $submission['status_tone'] }}">
                                <div class="submission-head">
                                    <div class="submission-head-left">
                                        <strong>{{ $submission['title'] }}</strong>
                                        <div class="submission-meta">{{ $submission['course_title'] }} &middot; {{ $submission['submitted_at_human'] ?: 'Recently submitted' }}</div>
                                    </div>
                                    <div class="submission-badges">
                                        <span class="demo-status">{{ $submission['submission_type'] }}</span>
                                        <span class="demo-status">{{ $submission['status_label'] }}</span>
                                    </div>
                                </div>

                                @if (!empty($submission['answer_text']))
                                    <div class="submission-answer-box">
                                        <h4>Answer</h4>
                                        <div class="submission-answer">{{ \Illuminate\Support\Str::limit($submission['answer_text'], 180) }}</div>
                                    </div>
                                @endif

                                @if (!empty($submission['file_name']))
                                    <div class="submission-doc-box">
                                        <div class="doc-name">{{ $submission['file_name'] }}</div>
                                        @if (!empty($submission['download_route']))
                                            <a class="btn btn-soft" href="{{ $submission['download_route'] }}">Download File</a>
                                        @endif
                                    </div>
                                @endif

                                @if (!empty($submission['review_notes']))
                                    <div class="submission-answer-box">
                                        <h4>Review Notes</h4>
                                        <div class="submission-answer">{{ \Illuminate\Support\Str::limit($submission['review_notes'], 180) }}</div>
                                    </div>
                                @endif

                                <div class="submission-actions">
                                    <a class="btn btn-soft" href="{{ $submission['open_route'] }}">Open Lesson</a>
                                </div>
                            </article>
                        @empty
                            <div class="submission-empty">No submissions yet. When you submit a task or quiz, it will appear here.</div>
                        @endforelse
                    </div>
                </section>

                <section class="dashboard-section">
                        <div class="section-head">
                            <div>
                                <h2>Certificates</h2>
                                <p>Download PDF or SVG certificates for the courses you have fully completed.</p>
                            </div>
                            <a class="section-link" href="{{ route('student.certificates') }}">View all -&gt;</a>
                        </div>

                    <div class="certificate-grid">
                        @forelse ($studentCertificates as $certificate)
                            <article class="certificate-card">
                                <div class="certificate-card-top">
                                    <span class="pill">{{ $certificate['category'] }}</span>
                                    <span class="certificate-code">{{ $certificate['certificate_code'] }}</span>
                                </div>
                                <div>
                                    <h4>{{ $certificate['course_title'] }}</h4>
                                    <p class="certificate-meta">
                                        Issued {{ $certificate['issued_at_human'] }}
                                        &middot; {{ $certificate['hours_total'] }}h
                                        &middot; Trainer: {{ $certificate['trainer_name'] }}
                                    </p>
                                </div>
                                <div class="submission-actions">
                                    <a class="btn btn-soft" href="{{ $certificate['course_route'] }}">Open Course</a>
                                    <a class="btn" href="{{ $certificate['download_pdf_route'] }}">PDF</a>
                                    <a class="btn btn-soft" href="{{ $certificate['download_svg_route'] }}">SVG</a>
                                </div>
                            </article>
                        @empty
                            <div class="submission-empty">Complete a course to unlock your first certificate downloads.</div>
                        @endforelse
                    </div>
                </section>

                        </div>

                        <div class="student-column-group">
                            <span class="student-column-label">Discover More</span>
                            <section class="dashboard-section">
                                <div class="section-head">
                                    <div>
                                        <h2>Recommended Courses</h2>
                                        <p>Available courses from your existing LMS catalog.</p>
                                    </div>
                                    <a class="section-link" href="{{ route('courses.index') }}">Browse all -&gt;</a>
                                </div>
                                <div class="dashboard-section-body">
                                    <div class="recommend-grid">
                                        @forelse ($recommendedCourses as $index => $course)
                                            @php $tone = array_keys($accentClass)[$index % count($accentClass)]; @endphp
                                            <article class="recommend-card">
                                                <div class="recommend-top {{ $accentClass[$tone] }}">
                                                    <div class="icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                                                </div>
                                                <div class="recommend-body">
                                                    <span class="pill">{{ $course['category'] }}</span>
                                                    <h4>{{ $course['title'] }}</h4>
                                                    <p class="recommend-meta">By {{ $course['provider'] }}</p>
                                                    <div class="recommend-foot">
                                                        <span>{{ $course['hours'] }}h total</span>
                                                        <a class="mini-btn" href="{{ route('courses.show', $course['id']) }}">View Course</a>
                                                    </div>
                                                </div>
                                            </article>
                                        @empty
                                            <article class="recommend-card"><div class="recommend-body"><h4>No courses available</h4></div></article>
                                        @endforelse
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <aside class="student-dashboard-side">
                        <article class="action-queue">
                            <div class="action-queue-head">
                                <div>
                                    <h3>Pending Tasks &amp; Live Quizzes</h3>
                                    <p>Open the most important actions directly from your dashboard.</p>
                                </div>
                                <a class="section-link" href="{{ route('student.courses') }}">Open courses -&gt;</a>
                            </div>

                            <div class="queue-summary">
                                <div class="queue-summary-box">
                                    <span>Pending Tasks</span>
                                    <strong>{{ $studentPendingActionSummary['tasks'] ?? 0 }}</strong>
                                </div>
                                <div class="queue-summary-box">
                                    <span>Live Quizzes</span>
                                    <strong>{{ $studentPendingActionSummary['live_quizzes'] ?? 0 }}</strong>
                                </div>
                                <div class="queue-summary-box">
                                    <span>Total Actions</span>
                                    <strong>{{ $studentPendingActionSummary['total'] ?? 0 }}</strong>
                                </div>
                            </div>

                            <div class="queue-list">
                                @forelse ($studentPendingActionItems as $actionItem)
                                    <a href="{{ $actionItem['route'] }}" class="queue-item">
                                        <div class="queue-item-top">
                                            <strong>{{ $actionItem['item_title'] }}</strong>
                                            <span class="queue-tag {{ $actionItem['item_type'] === \App\Models\CourseSessionItem::TYPE_QUIZ ? 'queue-tag--quiz' : 'queue-tag--task' }}">
                                                {{ $actionItem['item_type_label'] }}
                                            </span>
                                        </div>
                                        <p>{{ $actionItem['course_title'] }}</p>
                                        <div class="queue-meta">
                                            Week {{ $actionItem['week_number'] ?: '-' }} / Session {{ $actionItem['session_number'] ?: '-' }}
                                            &middot; {{ $actionItem['status_label'] }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="submission-empty">No pending task or live quiz right now. You are nicely caught up.</div>
                                @endforelse
                            </div>
                        </article>

                        <div class="student-column-group">
                            <span class="student-column-label">Action Center</span>
                            <section class="dashboard-section dashboard-section--side quick-actions-card">
                                <div>
                                    <h2>Quick Actions</h2>
                                    <p>Open the most-used student shortcuts from one place.</p>
                                </div>
                                <div class="quick-actions-grid">
                                    @foreach ($quickActions as $action)
                                        <a class="quick-action-link" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
                                    @endforeach
                                </div>
                            </section>

                            @if ($notifications->isNotEmpty())
                                <section class="dashboard-section dashboard-section--side student-side-card">
                                    <h3>Notifications</h3>
                                    @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
                                </section>
                            @endif

                            <article class="dashboard-section dashboard-section--side student-side-card">
                                <h3>Skill Progress</h3>
                                @forelse ($skillProgress as $index => $skill)
                                    <div class="skill-row">
                                        <div class="skill-label">
                                            <span>{{ $skill['skill'] }}</span>
                                            <span>{{ $skill['progress'] }}%</span>
                                        </div>
                                        <div class="bar-track">
                                            <div class="bar-val {{ $accentClass[array_keys($accentClass)[$index % count($accentClass)]] }}" style="width: {{ $skill['progress'] }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="muted" style="margin: 0;">No skill progress available.</p>
                                @endforelse
                            </article>

                            <article class="dashboard-section dashboard-section--side student-side-card">
                                <h3>Browse by Topic</h3>
                                <div class="topic-grid">
                                    @forelse ($topics as $topic)
                                        <a href="{{ route('courses.index') }}" class="topic" style="text-decoration: none; color: inherit;">
                                            <div class="topic-bullet">{{ strtoupper(substr($topic['name'], 0, 2)) }}</div>
                                            <div>
                                                <strong>{{ $topic['name'] }}</strong>
                                                <p>{{ number_format($topic['count']) }} courses</p>
                                            </div>
                                        </a>
                                    @empty
                                        <p class="muted" style="margin: 0;">No topics found.</p>
                                    @endforelse
                                </div>
                            </article>
                        </div>
                    </aside>
                </div>
            @else
                <section class="card">
                    <h2 style="margin: 0 0 10px;">Quick Actions</h2>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach ($quickActions as $action)
                            <a class="btn btn-soft" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
                        @endforeach
                    </div>
                </section>

                @if ($notifications->isNotEmpty())
                    <section class="card">
                        <div class="page-head">
                            <div>
                                <h2>Notifications</h2>
                                <p>Latest LMS updates for your account.</p>
                            </div>
                        </div>
                        @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
                    </section>
                @endif

                <section class="split-grid">
                    <article class="panel-box">
                        <h3>Skill Progress</h3>
                        @forelse ($skillProgress as $index => $skill)
                            <div class="skill-row">
                                <div class="skill-label">
                                    <span>{{ $skill['skill'] }}</span>
                                    <span>{{ $skill['progress'] }}%</span>
                                </div>
                                <div class="bar-track">
                                    <div class="bar-val {{ $accentClass[array_keys($accentClass)[$index % count($accentClass)]] }}" style="width: {{ $skill['progress'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="muted" style="margin: 0;">No skill progress available.</p>
                        @endforelse
                    </article>

                    <article class="panel-box">
                        <h3>Browse by Topic</h3>
                        <div class="topic-grid">
                            @forelse ($topics as $topic)
                                <a href="{{ route('courses.index') }}" class="topic" style="text-decoration: none; color: inherit;">
                                    <div class="topic-bullet">{{ strtoupper(substr($topic['name'], 0, 2)) }}</div>
                                    <div>
                                        <strong>{{ $topic['name'] }}</strong>
                                        <p>{{ number_format($topic['count']) }} courses</p>
                                    </div>
                                </a>
                            @empty
                                <p class="muted" style="margin: 0;">No topics found.</p>
                            @endforelse
                        </div>
                    </article>
                </section>

                <section>
                    <div class="section-head">
                        <div>
                            <h2>Recommended Courses</h2>
                            <p>Available courses from your existing LMS catalog.</p>
                        </div>
                        <a class="section-link" href="{{ route('courses.index') }}">Browse all -></a>
                    </div>
                    <div class="recommend-grid" style="margin-top: 10px;">
                        @forelse ($recommendedCourses as $index => $course)
                            @php $tone = array_keys($accentClass)[$index % count($accentClass)]; @endphp
                            <article class="recommend-card">
                                <div class="recommend-top {{ $accentClass[$tone] }}">
                                    <div class="icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                                </div>
                                <div class="recommend-body">
                                    <span class="pill">{{ $course['category'] }}</span>
                                    <h4>{{ $course['title'] }}</h4>
                                    <p class="recommend-meta">By {{ $course['provider'] }}</p>
                                    <div class="recommend-foot">
                                        <span>{{ $course['hours'] }}h total</span>
                                        <a class="mini-btn" href="{{ route('courses.show', $course['id']) }}">View Course</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <article class="recommend-card"><div class="recommend-body"><h4>No courses available</h4></div></article>
                        @endforelse
                    </div>
                </section>
            @endif
        @endif
    </div>
    @if ($dashboardMode === 'demo')
        <script src="{{ asset('js/student-courses.js') }}" defer></script>
    @endif
@endsection
