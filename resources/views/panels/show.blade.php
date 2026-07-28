@extends('layouts.app')

@php
    $labels = \App\Models\User::roleOptions();
    $descriptions = [
        'superadmin' => 'Full control across users, enrollments, categories, and courses.',
        'admin' => 'Operational control for users, enrollments, and learning data.',
        'manager_hr' => 'Track learning coverage, assignments, and completion signals for workforce reporting.',
        'it' => 'Monitor platform readiness, access activity, and technical content delivery signals.',
        'trainer' => 'Track assigned students and monitor their progress.',
        'student' => 'Access enrolled courses and continue course progress.',
    ];
@endphp

@section('content')

    <style>
        /* ═══════════════════════════════════════════════
           PANEL LAYOUT — rebuilt on theme tokens (dark/light aware)
        ═══════════════════════════════════════════════ */
        .panel-wrap { display: grid; gap: 16px; margin-top: -12px; }

        .panel-hero {
            border-radius: var(--radius);
            padding: 24px;
            color: #fff;
            background: linear-gradient(120deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            box-shadow: var(--shadow);
        }
        .panel-hero h1 { margin: 0 0 6px; font-size: 28px; font-weight: 700; }
        .panel-hero p { margin: 0; opacity: .92; font-size: 14px; }

        .panel-grid, .summary-grid { display: grid; gap: 14px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .panel-wide-grid { display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .pipeline-grid, .signal-grid { display: grid; gap: 12px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .status-grid, .export-grid { display: grid; gap: 14px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .export-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        /* HR — Attendance & Progress focus row */
        .hr-focus-grid { display: grid; gap: 14px; grid-template-columns: 1.3fr 1fr; }
        .hr-side-stack { display: grid; gap: 14px; }

        /* Weekly attendance trend (mini bar chart) */
        .mini-bars { display: flex; align-items: flex-end; gap: 12px; height: 150px; padding: 14px 4px 0; }
        .mini-bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: 6px; }
        .mini-bar {
            width: 100%; max-width: 34px; border-radius: 8px 8px 3px 3px;
            background: linear-gradient(180deg, var(--brand-secondary), var(--brand-primary));
            box-shadow: 0 6px 14px -6px var(--primary-glow);
            transition: transform .15s;
        }
        .mini-bar-col:hover .mini-bar { transform: scaleY(1.02); }
        .mini-bar-col span { font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .mini-bar-col small { font-size: 12px; color: var(--text); font-weight: 800; }

        /* Per-student attendance rows */
        .attendance-list { display: grid; gap: 8px; }
        .attendance-row {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 10px 12px; border: 1px solid var(--line); border-radius: var(--radius-xs); background: var(--bg-card2);
        }
        .attendance-who { display: flex; flex-direction: column; gap: 2px; }
        .attendance-who strong { font-size: 13.5px; color: var(--text); }
        .attendance-who span { font-size: 11px; color: var(--text-muted); }
        .attendance-days { display: flex; gap: 4px; }
        .attendance-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--line); }
        .attendance-dot.present { background: var(--success); }
        .attendance-dot.late { background: var(--warning); }
        .attendance-dot.absent { background: var(--danger); }
        .attendance-rate { font-size: 13px; font-weight: 800; color: var(--brand-primary); min-width: 40px; text-align: right; }

        /* Per-student progress rows */
        .progress-list { display: grid; gap: 12px; }
        .progress-row-top { display: flex; justify-content: space-between; gap: 10px; font-size: 13px; }
        .progress-row-top strong { color: var(--text); font-size: 13.5px; }
        .progress-row-top span { color: var(--text-muted); font-size: 11px; }
        .progress-track { height: 8px; border-radius: 999px; background: var(--bg2); overflow: hidden; margin-top: 6px; border: 1px solid var(--line); }
        .progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary)); }
        .progress-pct { font-size: 12px; font-weight: 800; color: var(--brand-primary); }

        .panel-kpi, .panel-actions, .summary-card, .pipeline-card, .signal-card, .feed-card, .status-card {
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            box-shadow: var(--shadow-card);
            padding: 18px;
        }

        .panel-kpi p { margin: 0; color: var(--text-muted); font-size: 13px; }
        .panel-kpi b { display: block; margin-top: 6px; font-size: 30px; line-height: 1; color: var(--text); }

        .panel-actions h2, .summary-card > strong.section-title { margin: 0 0 12px; font-size: 20px; color: var(--text); }
        .action-grid { display: grid; gap: 10px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .action-link {
            display: block; text-decoration: none; color: var(--text);
            border: 1px solid var(--line); border-radius: var(--radius-xs);
            background: var(--bg-card2); padding: 12px; font-size: 14px; font-weight: 600;
            transition: all .15s;
        }
        .action-link:hover { border-color: var(--brand-primary); background: var(--primary-glow); color: var(--brand-primary); }

        .summary-card span, .signal-card span {
            display: block; color: var(--text-muted); font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .04em;
        }
        .summary-card strong, .signal-card strong {
            display: block; margin-top: 8px; color: var(--text); font-size: 28px; line-height: 1.05;
        }
        .summary-card p, .signal-card p, .feed-note, .empty-note {
            margin: 8px 0 0; color: var(--text-muted); font-size: 13px; line-height: 1.6;
        }

        .pipeline-card strong { display: block; margin-top: 6px; color: var(--text); font-size: 24px; line-height: 1.05; }
        .pipeline-card span { display: block; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .pipeline-card p { margin: 8px 0 0; color: var(--text-muted); font-size: 13px; line-height: 1.55; }

        .feed-list { display: grid; gap: 10px; }
        .feed-item { border: 1px solid var(--line); border-radius: var(--radius-xs); background: var(--bg-card2); padding: 12px 14px; display: grid; gap: 6px; }
        .feed-item-top { display: flex; justify-content: space-between; align-items: start; gap: 10px; }
        .feed-item strong { color: var(--text); font-size: 15px; line-height: 1.35; }
        .feed-meta { display: flex; flex-wrap: wrap; gap: 8px; color: var(--text-muted); font-size: 12px; }
        .feed-tags { display: flex; flex-wrap: wrap; gap: 8px; }

        .pill-tag {
            display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 10px;
            font-size: 11px; font-weight: 700; border: 1px solid var(--line); background: var(--bg2); color: var(--text-muted);
        }
        .pill-tag.warning { background: rgba(217,119,6,.12); border-color: rgba(217,119,6,.3); color: var(--warning); }
        .pill-tag.danger  { background: rgba(220,38,38,.12); border-color: rgba(220,38,38,.3); color: var(--danger); }
        .pill-tag.ok      { background: rgba(22,163,74,.12); border-color: rgba(22,163,74,.3); color: var(--success); }
        .pill-tag.muted   { background: var(--bg2); border-color: var(--line); color: var(--text-muted); }

        .status-card { display: grid; gap: 8px; }
        .status-card-head { display: flex; justify-content: space-between; gap: 10px; align-items: start; }
        .status-card strong { color: var(--text); font-size: 16px; line-height: 1.3; }
        .status-card p { margin: 0; color: var(--text-muted); font-size: 13px; line-height: 1.6; }

        .export-card { border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--bg-card2); padding: 16px; display: grid; gap: 10px; }
        .export-card strong { color: var(--text); font-size: 17px; line-height: 1.3; }
        .export-card p { margin: 0; color: var(--text-muted); font-size: 13px; line-height: 1.6; }
        .export-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .export-btn {
            display: inline-flex; align-items: center; justify-content: center; min-width: 108px;
            text-decoration: none; color: var(--text); border: 1px solid var(--line); border-radius: var(--radius-xs);
            background: var(--bg-card); padding: 10px 12px; font-size: 13px; font-weight: 700; transition: all .15s;
        }
        .export-btn:hover { border-color: var(--brand-primary); background: var(--primary-glow); color: var(--brand-primary); }

        @media (max-width: 960px) {
            .panel-grid, .action-grid, .summary-grid, .panel-wide-grid,
            .pipeline-grid, .signal-grid, .status-grid, .export-grid,
            .hr-focus-grid { grid-template-columns: 1fr; }
            .panel-hero h1 { font-size: 22px; }
        }
    </style>

    <div class="panel-wrap">
        <section class="panel-hero">
            <h1>{{ $labels[$panelRole] ?? strtoupper($panelRole) }} Panel</h1>
            <p>{{ $descriptions[$panelRole] ?? 'Role-restricted panel.' }}</p>
        </section>

        @if ($panelRole === 'trainer')
            <section class="panel-actions">
                <h2>Assigned Students & Progress</h2>
                <div class="cc-table-wrap">
                    <table class="cc-table">
                        <thead>
                        <tr>
                            <th>Student</th><th>Course</th><th>Completed</th><th>Total</th><th>Progress</th><th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($trainerRows as $row)
                            <tr>
                                <td data-label="Student">{{ $row['enrollment']->student?->name }}</td>
                                <td data-label="Course">{{ $row['enrollment']->course?->title }}</td>
                                <td data-label="Completed">{{ $row['completed_items'] }}</td>
                                <td data-label="Total">{{ $row['total_items'] }}</td>
                                <td data-label="Progress">{{ $row['progress_percent'] }}%</td>
                                <td data-label="Actions">
                                    <a class="cc-btn-link" href="{{ route('trainer.courses.items', $row['enrollment']->course_id) }}">Task & Quiz Items</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="cc-empty">No students assigned to you.</div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel-actions">
                <h2>Assigned Courses (Read-only)</h2>
                <div class="cc-table-wrap">
                    <table class="cc-table">
                        <thead><tr><th>Course</th><th>Actions</th></tr></thead>
                        <tbody>
                        @forelse ($trainerCourses as $course)
                            <tr>
                                <td data-label="Course">{{ $course->title }}</td>
                                <td data-label="Actions"><a class="cc-btn-link" href="{{ route('trainer.courses.items', $course) }}">Task & Quiz Items</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="2"><div class="cc-empty">No courses assigned to you.</div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @elseif ($panelRole === 'manager_hr')
            @include('panels.partials.manager-hr')
        @elseif ($panelRole === 'it')
            @include('panels.partials.it')
        @else
            <section class="panel-grid">
                <article class="panel-kpi"><p>Total Users</p><b>{{ $stats['users'] }}</b></article>
                <article class="panel-kpi"><p>Course Categories</p><b>{{ $stats['categories'] }}</b></article>
                <article class="panel-kpi"><p>Total Courses</p><b>{{ $stats['courses'] }}</b></article>
            </section>

            <section class="panel-actions">
                <h2>Available Actions</h2>
                <div class="action-grid">
                    @foreach ($quickActions as $action)
                        <a class="action-link" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection