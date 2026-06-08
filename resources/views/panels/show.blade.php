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
        .panel-wrap { display: grid; gap: 14px; margin-top: -12px; }
        .panel-hero {
            border-radius: 14px;
            padding: 20px;
            color: #fff;
            background: linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%);
        }
        .panel-hero h1 { margin: 0 0 6px; font-size: 30px; }
        .panel-hero p { margin: 0; opacity: 0.92; font-size: 14px; }
        .panel-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .panel-kpi {
            border: 1px solid #d7deea;
            border-radius: 12px;
            background: #fff;
            padding: 16px;
        }
        .panel-kpi p { margin: 0; color: #63708a; font-size: 13px; }
        .panel-kpi b { display: block; margin-top: 6px; font-size: 30px; line-height: 1; }
        .panel-actions {
            border: 1px solid #d7deea;
            border-radius: 12px;
            background: #fff;
            padding: 16px;
        }
        .panel-actions h2 { margin: 0 0 10px; font-size: 24px; }
        .action-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .action-link {
            display: block;
            text-decoration: none;
            color: #17325a;
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #f8fbff;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .action-link:hover { border-color: #abc6ef; background: #f1f7ff; }
        .summary-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .summary-card,
        .pipeline-card,
        .signal-card,
        .feed-card {
            border: 1px solid #d7deea;
            border-radius: 12px;
            background: #fff;
            padding: 16px;
        }
        .summary-card span,
        .signal-card span {
            display: block;
            color: #63708a;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .summary-card strong,
        .signal-card strong {
            display: block;
            margin-top: 8px;
            color: #102849;
            font-size: 28px;
            line-height: 1.05;
        }
        .summary-card p,
        .signal-card p,
        .feed-note,
        .empty-note {
            margin: 8px 0 0;
            color: #5c6b84;
            font-size: 13px;
            line-height: 1.6;
        }
        .panel-wide-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .pipeline-grid,
        .signal-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .pipeline-card strong {
            display: block;
            margin-top: 6px;
            color: #102849;
            font-size: 24px;
            line-height: 1.05;
        }
        .pipeline-card span {
            display: block;
            color: #63708a;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .pipeline-card p {
            margin: 8px 0 0;
            color: #5c6b84;
            font-size: 13px;
            line-height: 1.55;
        }
        .feed-list {
            display: grid;
            gap: 10px;
        }
        .feed-item {
            border: 1px solid #dce5f2;
            border-radius: 12px;
            background: #f8fbff;
            padding: 12px 14px;
            display: grid;
            gap: 6px;
        }
        .feed-item-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 10px;
        }
        .feed-item strong {
            color: #102849;
            font-size: 15px;
            line-height: 1.35;
        }
        .feed-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            color: #5c6b84;
            font-size: 12px;
        }
        .feed-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .pill-tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #d7e4f5;
            background: #eef5ff;
            color: #2d5b95;
        }
        .pill-tag.warning {
            background: #fff6e8;
            border-color: #f0d8b3;
            color: #a9620f;
        }
        .pill-tag.danger {
            background: #fff0f0;
            border-color: #efc7c7;
            color: #a03a3a;
        }
        .pill-tag.ok {
            background: #eef9f1;
            border-color: #cfe7d6;
            color: #28744b;
        }
        .pill-tag.muted {
            background: #f4f7fb;
            border-color: #dce5f2;
            color: #63708a;
        }
        .status-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .status-card {
            border: 1px solid #dce5f2;
            border-radius: 12px;
            background: #fff;
            padding: 14px;
            display: grid;
            gap: 8px;
        }
        .status-card-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: start;
        }
        .status-card strong {
            color: #102849;
            font-size: 16px;
            line-height: 1.3;
        }
        .status-card p {
            margin: 0;
            color: #5c6b84;
            font-size: 13px;
            line-height: 1.6;
        }
        .export-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .export-card {
            border: 1px solid #dce5f2;
            border-radius: 12px;
            background: #f8fbff;
            padding: 14px;
            display: grid;
            gap: 10px;
        }
        .export-card strong {
            color: #102849;
            font-size: 17px;
            line-height: 1.3;
        }
        .export-card p {
            margin: 0;
            color: #5c6b84;
            font-size: 13px;
            line-height: 1.6;
        }
        .export-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .export-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 108px;
            text-decoration: none;
            color: #17325a;
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #fff;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
        }
        .export-btn:hover {
            border-color: #abc6ef;
            background: #eef5ff;
        }
        @media (max-width: 960px) {
            .panel-grid,
            .action-grid,
            .summary-grid,
            .panel-wide-grid,
            .pipeline-grid,
            .signal-grid,
            .status-grid,
            .export-grid {
                grid-template-columns: 1fr;
            }
            .panel-hero h1 { font-size: 24px; }
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
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Completed Items</th>
                            <th>Total Items</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($trainerRows as $row)
                            <tr>
                                <td>{{ $row['enrollment']->student?->name }}</td>
                                <td>{{ $row['enrollment']->course?->title }}</td>
                                <td>{{ $row['completed_items'] }}</td>
                                <td>{{ $row['total_items'] }}</td>
                                <td>{{ $row['progress_percent'] }}%</td>
                                <td>
                                    <a class="btn btn-soft" href="{{ route('trainer.courses.items', $row['enrollment']->course_id) }}">Task & Quiz Items</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No students assigned to you.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel-actions">
                <h2>Assigned Courses (Read-only)</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($trainerCourses as $course)
                            <tr>
                                <td>{{ $course->title }}</td>
                                <td>
                                    <a class="btn btn-soft" href="{{ route('trainer.courses.items', $course) }}">Task & Quiz Items</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No courses assigned to you.</td>
                            </tr>
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
                <article class="panel-kpi">
                    <p>Total Users</p>
                    <b>{{ $stats['users'] }}</b>
                </article>
                <article class="panel-kpi">
                    <p>Course Categories</p>
                    <b>{{ $stats['categories'] }}</b>
                </article>
                <article class="panel-kpi">
                    <p>Total Courses</p>
                    <b>{{ $stats['courses'] }}</b>
                </article>
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



 



