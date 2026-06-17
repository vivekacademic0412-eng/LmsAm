@extends('demo.layout')

@section('title', $course->title)

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Page wrapper ── */
.cp-wrap {
    min-height: 100vh;
    background: var(--bg);
    padding-bottom: 4rem;
}

/* ── Hero banner ── */
.cp-hero {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    padding: 3rem 1.5rem 5rem;
    position: relative;
    overflow: hidden;
}
.cp-hero::after {
    content: '';
    position: absolute;
    bottom: -2px; left: 0; right: 0;
    height: 60px;
    background: var(--bg);
    clip-path: ellipse(55% 100% at 50% 100%);
}
.cp-hero-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 2.5rem;
    align-items: start;
}
.cp-breadcrumb {
    font-size: 12px;
    color: rgba(255,255,255,.65);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
.cp-breadcrumb a { color: rgba(255,255,255,.75); text-decoration: none; }
.cp-breadcrumb a:hover { color: #fff; }
.cp-category-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11.5px;
    font-weight: 600;
    color: #fff;
    margin-bottom: .75rem;
    backdrop-filter: blur(4px);
}
.cp-hero-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.25;
    margin-bottom: .75rem;
    letter-spacing: -.4px;
}
.cp-hero-desc {
    font-size: .9rem;
    color: rgba(255,255,255,.82);
    line-height: 1.65;
    margin-bottom: 1.25rem;
}
.cp-meta-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.cp-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 20px;
    padding: 5px 13px;
    font-size: 12px;
    font-weight: 500;
    color: #fff;
    backdrop-filter: blur(4px);
}
.cp-pill i { font-size: 13px; opacity: .85; }

/* ── Hero card (right side) ── */
.cp-hero-card {
    background: var(--bg-card);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.22);
    overflow: hidden;
    position: sticky;
    top: 1.5rem;
    z-index: 2;
}
.cp-thumb {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
    display: block;
}
.cp-thumb-placeholder {
    width: 100%;
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    display: flex;
    align-items: center;
    justify-content: center;
}
.cp-thumb-placeholder i { font-size: 3rem; color: rgba(255,255,255,.5); }
.cp-hero-card-body { padding: 1.25rem; }
.cp-price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}
.cp-price-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    font-weight: 600;
}
.cp-free-badge {
    background: color-mix(in srgb, var(--brand-green) 12%, transparent);
    color: var(--brand-green);
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 10px;
}
.btn-enroll {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 48px;
    background: var(--brand-secondary);
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    text-decoration: none;
    transition: all .18s;
    box-shadow: 0 6px 20px color-mix(in srgb, var(--brand-secondary) 38%, transparent);
    margin-bottom: .75rem;
}
.btn-enroll:hover {
    background: var(--brand-primary);
    transform: translateY(-1px);
    box-shadow: 0 8px 24px color-mix(in srgb, var(--brand-secondary) 45%, transparent);
}
.btn-demo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 42px;
    background: transparent;
    border: 1.5px solid var(--brand-secondary);
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--brand-secondary);
    cursor: pointer;
    text-decoration: none;
    transition: all .18s;
}
.btn-demo:hover {
    background: color-mix(in srgb, var(--brand-secondary) 8%, transparent);
}
.cp-card-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--line);
}
.cp-stat {
    text-align: center;
}
.cp-stat-val {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-main);
}
.cp-stat-key {
    font-size: 10.5px;
    color: var(--text-muted);
    margin-top: 2px;
}

/* ── Main content area ── */
.cp-body {
    max-width: 1100px;
    margin: -2rem auto 0;
    padding: 0 1.5rem;
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 2rem;
    position: relative;
    z-index: 1;
}
.cp-main { min-width: 0; }
.cp-sidebar-spacer { /* matches the sticky card above */ }

/* ── Section card ── */
.sec-card {
    background: var(--bg-card);
    border-radius: 16px;
    box-shadow: var(--shadow-card);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.sec-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sec-title i { color: var(--brand-secondary); font-size: 16px; }

/* ── Demo videos ── */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.video-card {
    border: 1.5px solid var(--line);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all .18s;
    background: var(--bg-card2);
}
.video-card:hover {
    border-color: var(--brand-secondary);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px color-mix(in srgb, var(--brand-secondary) 15%, transparent);
}
.video-thumb {
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.video-thumb i { font-size: 2rem; color: rgba(255,255,255,.85); }
.video-play-btn {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.25);
    transition: background .15s;
}
.video-card:hover .video-play-btn { background: rgba(0,0,0,.4); }
.play-circle {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,.9);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(0,0,0,.25);
}
.play-circle i { font-size: 16px; color: var(--brand-primary); margin-left: 3px; }
.video-info { padding: 10px 12px; }
.video-title {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-main);
    margin-bottom: 3px;
    line-height: 1.35;
}
.video-desc {
    font-size: 11px;
    color: var(--text-muted);
    line-height: 1.4;
}
.video-size {
    font-size: 10.5px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* ── Curriculum accordion ── */
.week-block { margin-bottom: 10px; }
.week-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: var(--bg2);
    border: 1.5px solid var(--line);
    border-radius: 12px;
    cursor: pointer;
    user-select: none;
    transition: all .15s;
}
.week-header:hover { border-color: var(--brand-secondary); background: color-mix(in srgb, var(--brand-secondary) 5%, var(--bg2)); }
.week-header.open {
    border-radius: 12px 12px 0 0;
    border-color: var(--brand-secondary);
    background: color-mix(in srgb, var(--brand-secondary) 8%, var(--bg2));
}
.week-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.week-num {
    width: 28px; height: 28px;
    border-radius: 8px;
    background: var(--brand-secondary);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.week-title-text {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-main);
}
.week-meta {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 1px;
}
.week-chevron {
    color: var(--text-muted);
    font-size: 13px;
    transition: transform .25s;
}
.week-header.open .week-chevron { transform: rotate(180deg); }
.week-body {
    border: 1.5px solid var(--brand-secondary);
    border-top: none;
    border-radius: 0 0 12px 12px;
    overflow: hidden;
    display: none;
}
.week-body.open { display: block; }

/* Sessions */
.session-block { border-bottom: 1px solid var(--line); }
.session-block:last-child { border-bottom: none; }
.session-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    background: var(--bg-card);
    cursor: pointer;
    transition: background .12s;
}
.session-header:hover { background: var(--bg2); }
.session-num {
    width: 22px; height: 22px;
    border-radius: 6px;
    background: color-mix(in srgb, var(--brand-secondary) 15%, transparent);
    color: var(--brand-secondary);
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.session-title-text {
    flex: 1;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text);
}
.session-chevron {
    font-size: 11px;
    color: var(--text-muted);
    transition: transform .2s;
}
.session-header.open .session-chevron { transform: rotate(180deg); }
.session-items { display: none; background: var(--bg-card2); }
.session-items.open { display: block; }

/* Items */
.item-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 20px 9px 28px;
    border-bottom: 1px solid var(--line);
    font-size: 12px;
}
.item-row:last-child { border-bottom: none; }
.item-icon {
    width: 26px; height: 26px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}
.item-icon.intro    { background: color-mix(in srgb, var(--brand-primary) 12%, transparent); color: var(--brand-primary); }
.item-icon.video    { background: color-mix(in srgb, #ef4444 12%, transparent); color: #ef4444; }
.item-icon.task     { background: color-mix(in srgb, var(--brand-accent) 15%, transparent); color: var(--brand-accent); }
.item-icon.quiz     { background: color-mix(in srgb, var(--brand-green) 12%, transparent); color: var(--brand-green); }
.item-title { flex: 1; color: var(--text); line-height: 1.35; }
.item-type-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: .04em;
    flex-shrink: 0;
}
.badge-intro  { background: color-mix(in srgb, var(--brand-primary) 10%, transparent); color: var(--brand-primary); }
.badge-video  { background: color-mix(in srgb, #ef4444 10%, transparent); color: #ef4444; }
.badge-task   { background: color-mix(in srgb, var(--brand-accent) 15%, transparent); color: var(--brand-accent); }
.badge-quiz   { background: color-mix(in srgb, var(--brand-green) 10%, transparent); color: var(--brand-green); }

/* ── What you'll learn ── */
.learn-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.learn-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 12.5px;
    color: var(--text);
    line-height: 1.45;
}
.learn-item i { color: var(--brand-green); font-size: 13px; margin-top: 1px; flex-shrink: 0; }

/* ── Video modal ── */
.vid-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.72);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
.vid-modal-overlay.open { display: flex; }
.vid-modal {
    background: var(--bg-card);
    border-radius: 20px;
    width: 100%;
    max-width: 800px;
    box-shadow: 0 30px 80px rgba(0,0,0,.5);
    overflow: hidden;
}
.vid-modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--line);
}
.vid-modal-title { font-size: 14px; font-weight: 700; color: var(--text-main); }
.vid-modal-close {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1.5px solid var(--line);
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 14px;
    transition: all .15s;
}
.vid-modal-close:hover { background: var(--line); color: var(--text-main); }
.vid-modal-body { padding: 1rem 1.25rem 1.25rem; }
.vid-placeholder {
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .75rem;
    color: rgba(255,255,255,.8);
    font-size: 13px;
}
.vid-placeholder i { font-size: 3rem; color: rgba(255,255,255,.6); }
.vid-modal-desc { font-size: 13px; color: var(--text-muted); margin-top: .75rem; line-height: 1.55; }

/* ── Responsive ── */
@media (max-width: 860px) {
    .cp-hero-inner { grid-template-columns: 1fr; }
    .cp-hero-card  { position: static; max-width: 420px; }
    .cp-body       { grid-template-columns: 1fr; }
    .cp-sidebar-spacer { display: none; }
    .learn-grid    { grid-template-columns: 1fr; }
    .video-grid    { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 480px) {
    .cp-hero-title { font-size: 1.4rem; }
    .video-grid    { grid-template-columns: 1fr; }
}
</style>

@section('content')

@php
    // Helper maps for item types
    $itemMeta = [
        'intro'      => ['icon' => 'fas fa-play-circle',   'cls' => 'intro', 'badge' => 'badge-intro',  'label' => 'Intro'],
        'main_video' => ['icon' => 'fas fa-video',          'cls' => 'video', 'badge' => 'badge-video',  'label' => 'Video'],
        'task'       => ['icon' => 'fas fa-tasks',          'cls' => 'task',  'badge' => 'badge-task',   'label' => 'Task'],
        'quiz'       => ['icon' => 'fas fa-question-circle','cls' => 'quiz',  'badge' => 'badge-quiz',   'label' => 'Quiz'],
    ];
    $totalWeeks    = $course->weeks->count();
    $totalSessions = $course->weeks->sum(fn($w) => $w->sessions->count());
    $totalItems    = $course->weeks->sum(fn($w) => $w->sessions->sum(fn($s) => $s->items->count()));
@endphp

<div class="cp-wrap">

    {{-- ═══════════════════════ HERO ═══════════════════════ --}}
    <div class="cp-hero">
        <div class="cp-hero-inner">

            {{-- Left: info --}}
            <div>
                <div class="cp-breadcrumb">
                    <a href="{{ url('/') }}">Home</a>
                    <i class="fas fa-chevron-right" style="font-size:9px"></i>
                    <a href="#">Courses</a>
                    <i class="fas fa-chevron-right" style="font-size:9px"></i>
                    <span>{{ $course->category?->name }}</span>
                </div>

                <div class="cp-category-tag">
                    <i class="fas fa-tag"></i>
                    {{ $course->category?->name ?? 'Course' }}
                </div>

                <h1 class="cp-hero-title">{{ $course->title }}</h1>
                <p class="cp-hero-desc">{{ $course->short_description }}</p>

                <div class="cp-meta-pills">
                    @if($course->courseType)
                    <span class="cp-pill">
                        <i class="fas fa-certificate"></i>
                        {{ $course->courseType->name }}
                    </span>
                    @endif

                    @if($course->courseLevel)
                    <span class="cp-pill">
                        <i class="fas fa-chart-bar"></i>
                        {{ $course->courseLevel->name }}
                    </span>
                    @else
                    <span class="cp-pill">
                        <i class="fas fa-seedling"></i>
                        All Levels
                    </span>
                    @endif

                    <span class="cp-pill">
                        <i class="fas fa-clock"></i>
                        {{ $course->duration_hours }} hrs
                    </span>

                    <span class="cp-pill">
                        <i class="fas fa-language"></i>
                        {{ $course->language ?? 'English' }}
                    </span>

                    <span class="cp-pill">
                        <i class="fas fa-calendar-week"></i>
                        {{ $totalWeeks }} Weeks
                    </span>
                </div>
            </div>

            {{-- Right: sticky card --}}
            <div class="cp-hero-card">
                @if($course->thumbnail)
                    <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}" class="cp-thumb">
                @else
                    <div class="cp-thumb-placeholder">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                @endif

                <div class="cp-hero-card-body">
                    <div class="cp-price-row">
                        <span class="cp-price-label">Demo Access</span>
                        <span class="cp-free-badge">FREE</span>
                    </div>

                    @if(session('demo_user_id'))
                        <a href="{{ route('lms.step2') }}" class="btn-enroll">
                            <i class="fas fa-rocket"></i> Continue My Demo
                        </a>
                    @else
                        <a href="{{ route('lms.step1') }}" class="btn-enroll">
                            <i class="fas fa-rocket"></i> Start Free Demo
                        </a>
                    @endif

                    <a href="#demo-videos" class="btn-demo">
                        <i class="fas fa-play"></i> Watch Preview Videos
                    </a>

                    <div class="cp-card-stats">
                        <div class="cp-stat">
                            <div class="cp-stat-val">{{ $totalWeeks }}</div>
                            <div class="cp-stat-key">Weeks</div>
                        </div>
                        <div class="cp-stat">
                            <div class="cp-stat-val">{{ $totalSessions }}</div>
                            <div class="cp-stat-key">Sessions</div>
                        </div>
                        <div class="cp-stat">
                            <div class="cp-stat-val">{{ $totalItems }}</div>
                            <div class="cp-stat-key">Lessons</div>
                        </div>
                        <div class="cp-stat">
                            <div class="cp-stat-val">{{ $course->duration_hours }}h</div>
                            <div class="cp-stat-key">Duration</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════ BODY ═══════════════════════ --}}
    <div class="cp-body">
        <div class="cp-main">

            {{-- ── What you'll learn ── --}}
            <div class="sec-card">
                <div class="sec-title">
                    <i class="fas fa-lightbulb"></i> What You'll Learn
                </div>
                <div class="learn-grid">
                    @foreach([
                        'Industry-relevant practical skills',
                        'Real-world project experience',
                        'Expert-guided video sessions',
                        'Weekly assignments & assessments',
                        'AI-integrated tools & workflows',
                        'Certificate upon completion',
                        'Live doubt-clearing sessions',
                        'Portfolio-ready projects',
                    ] as $point)
                    <div class="learn-item">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $point }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Demo Videos ── --}}
            @if($course->demoFeatureVideos->isNotEmpty())
            <div class="sec-card" id="demo-videos">
                <div class="sec-title">
                    <i class="fas fa-film"></i> Preview Videos
                </div>
                <div class="video-grid">
                    @foreach($course->demoFeatureVideos as $video)
                    <div class="video-card" onclick="openVideoModal({{ $video->id }}, '{{ addslashes($video->title) }}', '{{ addslashes($video->description) }}', '{{ $video->file_path }}')">
                        <div class="video-thumb">
                            <div class="video-play-btn">
                                <div class="play-circle">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                        </div>
                        <div class="video-info">
                            <div class="video-title">{{ $video->title }}</div>
                            <div class="video-desc">{{ Str::limit($video->description, 60) }}</div>
                            <div class="video-size">
                                <i class="fas fa-file-video" style="font-size:10px"></i>
                                {{ number_format($video->file_size / 1048576, 1) }} MB
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Course Curriculum ── --}}
            <div class="sec-card">
                <div class="sec-title">
                    <i class="fas fa-list-alt"></i> Course Curriculum
                    <span style="margin-left:auto;font-size:12px;font-weight:500;color:var(--text-muted)">
                        {{ $totalWeeks }} weeks · {{ $totalSessions }} sessions · {{ $totalItems }} lessons
                    </span>
                </div>

                @forelse($course->weeks->sortBy('week_number') as $week)
                <div class="week-block">
                    <div class="week-header" onclick="toggleWeek(this)">
                        <div class="week-left">
                            <div class="week-num">{{ $week->week_number }}</div>
                            <div>
                                <div class="week-title-text">{{ $week->title }}</div>
                                <div class="week-meta">
                                    {{ $week->sessions->count() }} sessions ·
                                    {{ $week->sessions->sum(fn($s) => $s->items->count()) }} lessons
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down week-chevron"></i>
                    </div>

                    <div class="week-body">
                        @foreach($week->sessions->sortBy('session_number') as $session)
                        <div class="session-block">
                            <div class="session-header" onclick="toggleSession(this)">
                                <div class="session-num">{{ $session->session_number }}</div>
                                <div class="session-title-text">{{ $session->title }}</div>
                                <span style="font-size:11px;color:var(--text-muted);margin-right:8px">
                                    {{ $session->items->count() }} items
                                </span>
                                <i class="fas fa-chevron-down session-chevron"></i>
                            </div>

                            <div class="session-items">
                                @foreach($session->items as $item)
                                @php
                                    $meta = $itemMeta[$item->item_type] ?? $itemMeta['intro'];
                                @endphp
                                <div class="item-row">
                                    <div class="item-icon {{ $meta['cls'] }}">
                                        <i class="{{ $meta['icon'] }}"></i>
                                    </div>
                                    <div class="item-title">{{ $item->title }}</div>
                                    <span class="item-type-badge {{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                    <p style="color:var(--text-muted);font-size:13px;text-align:center;padding:1rem">
                        Curriculum coming soon.
                    </p>
                @endforelse
            </div>

            {{-- ── About the course ── --}}
            <div class="sec-card">
                <div class="sec-title"><i class="fas fa-info-circle"></i> About This Course</div>
                <p style="font-size:13.5px;color:var(--text);line-height:1.75">
                    {{ $course->description }}
                </p>
            </div>

        </div>

        {{-- Sidebar spacer (the card is sticky in the hero) --}}
        <div class="cp-sidebar-spacer"></div>
    </div>

</div>

{{-- ═══════════════════════ VIDEO MODAL ═══════════════════════ --}}
<div class="vid-modal-overlay" id="videoModal" onclick="closeModalOnBackdrop(event)">
    <div class="vid-modal">
        <div class="vid-modal-head">
            <span class="vid-modal-title" id="modalTitle">Video Preview</span>
            <button class="vid-modal-close" onclick="closeVideoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="vid-modal-body">
            <div id="modalVideoWrap">
                {{-- Video element injected by JS --}}
            </div>
            <p class="vid-modal-desc" id="modalDesc"></p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ── Accordion: weeks ── */
function toggleWeek(header) {
    const body = header.nextElementSibling;
    const isOpen = header.classList.contains('open');
    // Close all weeks first
    document.querySelectorAll('.week-header').forEach(h => {
        h.classList.remove('open');
        h.nextElementSibling.classList.remove('open');
    });
    if (!isOpen) {
        header.classList.add('open');
        body.classList.add('open');
    }
}

/* ── Accordion: sessions ── */
function toggleSession(header) {
    const items = header.nextElementSibling;
    header.classList.toggle('open');
    items.classList.toggle('open');
}

/* ── Open first week by default ── */
document.addEventListener('DOMContentLoaded', () => {
    const firstWeek = document.querySelector('.week-header');
    if (firstWeek) toggleWeek(firstWeek);
});

/* ── Video modal ── */
function openVideoModal(id, title, desc, filePath) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDesc').textContent  = desc;

    const wrap = document.getElementById('modalVideoWrap');
    const src  = '/storage/' + filePath;

    // Try native <video>; if path is a placeholder show a styled placeholder
    wrap.innerHTML = `
        <video controls style="width:100%;border-radius:12px;background:#000;max-height:420px"
               onerror="this.outerHTML=videoPlaceholder()">
            <source src="${src}" type="video/mp4">
            Your browser does not support video playback.
        </video>`;

    document.getElementById('videoModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function videoPlaceholder() {
    return `<div style="aspect-ratio:16/9;background:linear-gradient(135deg,var(--brand-primary),var(--brand-secondary));
            border-radius:12px;display:flex;flex-direction:column;align-items:center;
            justify-content:center;gap:.75rem;color:rgba(255,255,255,.8);font-size:13px">
                <i class="fas fa-video" style="font-size:3rem;color:rgba(255,255,255,.6)"></i>
                <span>Video preview not available in demo</span>
            </div>`;
}

function closeVideoModal() {
    document.getElementById('videoModal').classList.remove('open');
    document.body.style.overflow = '';
    // Pause video if playing
    const v = document.querySelector('#modalVideoWrap video');
    if (v) v.pause();
}

function closeModalOnBackdrop(e) {
    if (e.target.id === 'videoModal') closeVideoModal();
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeVideoModal();
});
</script>
@endsection