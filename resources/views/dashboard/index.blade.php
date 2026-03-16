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
    if (!empty($heroCourse['course_id'])) {
        $heroResumeRoute = $user->role === \App\Models\User::ROLE_STUDENT
            ? route('student.courses.show', $heroCourse['course_id'])
            : route('courses.show', $heroCourse['course_id']);
    }
@endphp

@section('content')
    <style>
        .dash-grid { display: grid; gap: 18px; }
        .dash-hero {
            background: linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%);
            color: #fff;
            border-radius: 14px;
            padding: 24px 26px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .dash-hero::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.09);
            right: -40px;
            top: -60px;
        }
        .hero-kicker { margin: 0; text-transform: uppercase; letter-spacing: 0.7px; font-size: 11px; opacity: 0.88; }
        .hero-title { margin: 5px 0; font-size: 32px; line-height: 1.1; }
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
        .section-head h2 { margin: 0; font-size: 28px; }
        .section-head p { margin: 4px 0 0; font-size: 13px; color: #617089; }
        .section-link { text-decoration: none; color: #0d55cf; font-size: 13px; font-weight: 600; }
        .learning-grid { display: grid; gap: 12px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
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
            min-height: 76px;
        }
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
        .course-body { padding: 12px; display: grid; gap: 7px; }
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
        .panel-box { background: #fff; border: 1px solid #d7deea; border-radius: 12px; padding: 15px; }
        .panel-box h3 { margin: 0 0 12px; font-size: 22px; }
        .skill-row { margin-bottom: 10px; }
        .skill-row:last-child { margin-bottom: 0; }
        .skill-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
        .topic-grid { display: grid; gap: 10px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .topic { border: 1px solid #d7deea; border-radius: 10px; padding: 11px; display: flex; gap: 9px; align-items: center; }
        .topic-bullet { width: 30px; height: 30px; border-radius: 8px; background: #eef3fb; color: #2a61b8; display: grid; place-content: center; font-size: 11px; font-weight: 700; }
        .topic p { margin: 0; color: #66758e; font-size: 12px; }
        .recommend-grid { display: grid; gap: 12px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .recommend-card { border: 1px solid #d7deea; border-radius: 12px; overflow: hidden; background: #fff; }
        .recommend-top { color: #fff; padding: 12px; min-height: 74px; display: flex; align-items: center; }
        .recommend-body { padding: 12px; }
        .recommend-body h4 { margin: 6px 0 2px; font-size: 18px; }
        .recommend-meta { margin: 0 0 8px; color: #6e7b93; font-size: 12px; }
        .recommend-foot { display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #65748d; }
        .mini-btn { text-decoration: none; background: #0f59c7; color: #fff; border-radius: 7px; padding: 7px 10px; font-size: 12px; font-weight: 700; }
        .panel-inline {
            border: 1px solid #d7deea;
            border-radius: 14px;
            background: #fff;
            padding: 16px;
            display: grid;
            gap: 12px;
        }
        .panel-inline-hero {
            border-radius: 12px;
            padding: 16px;
            color: #fff;
            background: linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%);
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
        }
        @media (max-width: 900px) {
            .dash-hero { grid-template-columns: 1fr; }
            .split-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .learning-grid, .recommend-grid, .stats-grid, .topic-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 24px; }
        }
    </style>

    <div class="dash-grid">
        {{-- <section class="dash-hero">
            <div style="z-index: 1;">
                @if ($heroKicker !== '')
                    <p class="hero-kicker">{{ $heroKicker }}</p>
                @endif
                <h1 class="hero-title">{{ $heroCourse['title'] ?? 'Learning Dashboard' }}</h1>
                <p class="hero-meta">{{ $heroCourse['provider'] ?? 'LMS Academy' }} &middot; Role: {{ $roleLabels[$user->role] ?? $user->role }}</p>
                <a class="hero-btn" href="{{ $heroResumeRoute }}">{{ $isStudent ? 'Resume Course' : 'Open Course' }}</a>
                <p class="hero-sub">{{ $heroCourse['progress_percent'] ?? 0 }}% complete &middot; {{ $heroCourse['hours_done'] ?? 0 }}h of {{ $heroCourse['hours_total'] ?? 0 }}h</p>
            </div>
            <div class="hero-ring">
                <b>{{ $heroCourse['progress_percent'] ?? 0 }}%</b>
                <span>Done</span>
            </div>
        </section> --}}

        @if ($user->role === \App\Models\User::ROLE_SUPERADMIN)
            <section class="panel-inline">
                <div class="panel-inline-hero">
                    <h3>Super Admin Panel</h3>
                    <p>{{ $panelDescription }}</p>
                </div>
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

        <section>
            <div class="section-head">
                <div>
                    <h2>{{ $learningTitle }}</h2>
                    <p>{{ $learningSubtitle }}</p>
                </div>
                <a class="section-link" href="{{ $allCoursesRoute }}">{{ $learningActionLabel }}</a>
            </div>
            <div class="learning-grid" style="margin-top: 10px;">
                @forelse ($learningItems as $index => $item)
                    @php
                        $itemRoute = !empty($item['course_id'])
                            ? ($isStudent ? route('student.courses.show', $item['course_id']) : route('courses.show', $item['course_id']))
                            : $allCoursesRoute;
                    @endphp
                    <a href="{{ $itemRoute }}" class="course-card" style="text-decoration: none; color: inherit;">
                        <div class="course-top {{ $accentClass[$item['accent']] ?? 'accent-blue' }}">
                            <div class="icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                            <span class="badge">{{ min(100, (int) $item['progress_percent']) }}%</span>
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
                        </div>
                    </a>
                @empty
                    <article class="course-card"><div class="course-body"><h3>No learning data yet</h3><p class="course-meta">No assigned or enrolled courses found.</p></div></article>
                @endforelse
            </div>
        </section>
        <section class="card">
            <h2 style="margin: 0 0 10px;">Quick Actions</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                @foreach ($quickActions as $action)
                    <a class="btn btn-soft" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
                @endforeach
            </div>
        </section>

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
    </div>
@endsection
