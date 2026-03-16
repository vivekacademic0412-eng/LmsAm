@extends('layouts.app')

@php
    $labels = \App\Models\User::roleOptions();
    $descriptions = [
        'superadmin' => 'Full control across users, enrollments, categories, and courses.',
        'admin' => 'Operational control for users, enrollments, and learning data.',
        'manager_hr' => 'View-only access for catalog and reporting workflows.',
        'it' => 'View-only access for catalog and reporting workflows.',
        'trainer' => 'Track assigned students and monitor their progress.',
        'student' => 'Access enrolled courses and continue course progress.',
    ];
@endphp

@section('content')
    <style>
        .panel-wrap { display: grid; gap: 14px; }
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
        @media (max-width: 960px) {
            .panel-grid, .action-grid { grid-template-columns: 1fr; }
            .panel-hero h1 { font-size: 24px; }
        }
    </style>

    <div class="panel-wrap">
        <section class="panel-hero">
            <h1>{{ $labels[$panelRole] ?? strtoupper($panelRole) }} Panel</h1>
            <p>{{ $descriptions[$panelRole] ?? 'Role-restricted panel.' }}</p>
        </section>

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
    </div>
@endsection
