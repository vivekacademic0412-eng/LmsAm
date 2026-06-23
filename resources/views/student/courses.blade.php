@extends('layouts.app')

@section('title', 'My Courses — Academic Mantra LMS')

@section('content')

<style>
/* ═══════════════════════════════════════════════
   PAGE LAYOUT
═══════════════════════════════════════════════ */
.courses-page { display: flex; flex-direction: column; gap: 24px; }

/* ═══════════════════════════════════════════════
   PAGE HERO HEADER
═══════════════════════════════════════════════ */
.courses-hero {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    padding: 28px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    box-shadow: var(--shadow-card);
}
.courses-hero-left { display: flex; align-items: center; gap: 18px; }
.courses-hero-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff; flex-shrink: 0;
    box-shadow: 0 6px 18px rgba(9,71,168,.25);
}
.courses-hero-title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
.courses-hero-sub   { font-size: 13px; color: var(--text-muted); }
.courses-hero-stats {
    display: flex; gap: 20px; flex-wrap: wrap;
}
.hero-stat {
    text-align: center;
    padding: 10px 18px;
    background: var(--bg2);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
}
.hero-stat-val { font-size: 20px; font-weight: 800; color: var(--brand-primary); line-height: 1; }
.hero-stat-label { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 3px; text-transform: uppercase; letter-spacing: .4px; }

/* ═══════════════════════════════════════════════
   SECTION CARD
═══════════════════════════════════════════════ */
.section-card {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-card);
}
.section-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1.5px solid var(--line);
    gap: 12px; flex-wrap: wrap;
}
.section-card-title {
    font-size: 15px; font-weight: 700; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.section-card-title i { color: var(--brand-primary); font-size: 17px; }
.section-card-body { padding: 24px; }
.section-card-count {
    font-size: 12px; font-weight: 600;
    color: var(--brand-primary);
    background: var(--primary-glow);
    padding: 4px 10px;
    border-radius: 20px;
}

/* ═══════════════════════════════════════════════
   SEARCH + FILTER BAR
═══════════════════════════════════════════════ */
.filter-bar {
    display: flex; align-items: center; gap: 12px;
    flex-wrap: wrap;
}
.filter-search {
    display: flex; align-items: center; gap: 8px;
    background: var(--input-bg);
    border: 1.5px solid var(--input-border);
    border-radius: var(--radius-xs);
    padding: 8px 14px;
    flex: 1; min-width: 220px;
    transition: border-color .2s;
}
.filter-search:focus-within {
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px var(--primary-glow);
}
.filter-search i { color: var(--text-muted); font-size: 15px; flex-shrink: 0; }
.filter-search input {
    background: none; border: none; outline: none;
    font-size: 13.5px; color: var(--text); font-family: inherit; width: 100%;
}
.filter-search input::placeholder { color: var(--text-muted); }

/* ═══════════════════════════════════════════════
   CATEGORY TABS (main)
═══════════════════════════════════════════════ */
.category-tabs-wrap {
    padding: 0 24px 0;
    border-bottom: 1.5px solid var(--line);
    display: flex; align-items: center; gap: 4px;
    overflow: scroll;
    scrollbar-width: 200px;
}
.category-tabs-wrap::-webkit-scrollbar { display: none; }

.cat-tab {
    display: flex; align-items: center; gap: 7px;
    padding: 14px 16px;
    font-size: 13px; font-weight: 600;
    color: var(--text-muted);
    background: none; border: none;
    border-bottom: 2.5px solid transparent;
    cursor: pointer; white-space: nowrap;
    transition: color .15s, border-color .15s;
    margin-bottom: -1.5px;
    font-family: inherit;
}
.cat-tab i { font-size: 15px; }
.cat-tab:hover { color: var(--brand-primary); }
.cat-tab.active {
    color: var(--brand-primary);
    border-bottom-color: var(--brand-primary);
    font-weight: 700;
}
.cat-tab .cat-count {
    font-size: 10px; font-weight: 700;
    background: var(--primary-glow);
    color: var(--brand-primary);
    padding: 2px 6px;
    border-radius: 20px;
}

/* ═══════════════════════════════════════════════
   SUBCATEGORY PILLS
═══════════════════════════════════════════════ */
.subcategory-row {
    display: flex; align-items: center; gap: 8px;
    flex-wrap: wrap;
    padding: 16px 24px 0;
}
.subcategory-label {
    font-size: 10px; font-weight: 700;
    color: var(--text-muted); text-transform: uppercase;
    letter-spacing: .6px; flex-shrink: 0;
}
.sub-pill {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    border: 1.5px solid var(--line);
    background: var(--bg2);
    color: var(--text-muted);
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    font-family: inherit;
}
.sub-pill:hover {
    border-color: var(--primary);
    color: var(--brand-primary);
    background: var(--primary-glow);
}
.sub-pill.active {
    border-color: var(--brand-primary);
    color: var(--brand-primary);
    background: var(--primary-glow);
    font-weight: 700;
}

/* ═══════════════════════════════════════════════
   TAB PANELS
═══════════════════════════════════════════════ */
.tab-panel          { display: none; }
.tab-panel.active   {
    display: block;
    animation: fadeUp 220ms ease;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════════════════════════════════════════
   COURSE GRID
═══════════════════════════════════════════════ */
.course-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    padding: 20px 24px 24px;
}
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 48px 20px;
    color: var(--text-muted);
}
.empty-state i { font-size: 40px; opacity: .35; margin-bottom: 12px; display: block; }
.empty-state p { font-size: 14px; }

/* ═══════════════════════════════════════════════
   COURSE TILE
═══════════════════════════════════════════════ */
.course-tile {
    border-radius: var(--radius-sm);
    overflow: hidden;
    border: 1.5px solid var(--line);
    background: var(--bg-card);
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    color: inherit;
    display: flex; flex-direction: column;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    position: relative;
}
.course-tile:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
    border-color: var(--border);
}
.course-tile.is-locked {
    opacity: .82;
}
.course-tile.is-locked:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-card);
}

/* Thumbnail */
.course-thumb {
    min-height: 148px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex; align-items: flex-end;
    padding: 14px;
}
.course-thumb-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(5,15,35,.72) 0%, rgba(5,15,35,.08) 60%, transparent 100%);
}
.course-thumb h3 {
    position: relative; z-index: 1;
    margin: 0;
    font-size: 15px; font-weight: 700;
    color: #fff;
    line-height: 1.3;
    text-shadow: 0 1px 4px rgba(0,0,0,.4);
}

/* Enrolled ribbon */
.course-enrolled-badge {
    position: absolute;
    top: 10px; left: 10px;
    display: flex; align-items: center; gap: 5px;
    font-size: 10.5px; font-weight: 700;
    color: var(--success);
    background: rgba(22,163,74,.12);
    border: 1px solid rgba(22,163,74,.3);
    backdrop-filter: blur(6px);
    padding: 3px 10px;
    border-radius: 20px;
    z-index: 2;
}

/* New badge */
.course-new-badge {
    position: absolute;
    top: 10px; right: 10px;
    font-size: 10px; font-weight: 800;
    color: #fff;
    background: var(--brand-accent);
    padding: 3px 9px;
    border-radius: 20px;
    z-index: 2;
    text-transform: uppercase;
    letter-spacing: .4px;
}

/* Lock overlay */
.course-lock-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(8,17,31,.45);
    backdrop-filter: blur(2px);
    z-index: 3;
}
.course-lock-icon {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 2px solid rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff;
}

/* Body */
.course-body {
    padding: 14px 16px;
    display: flex; flex-direction: column;
    gap: 10px; flex: 1;
}
.course-meta {
    display: flex; align-items: center; gap: 6px;
    font-size: 11.5px; color: var(--text-muted);
    flex-wrap: wrap;
}
.course-meta-dot {
    width: 3px; height: 3px; border-radius: 50%;
    background: var(--text-muted); opacity: .4;
}

/* Progress bar */
.course-progress-wrap { display: flex; flex-direction: column; gap: 4px; }
.course-progress-row  { display: flex; justify-content: space-between; font-size: 11.5px; }
.course-progress-label { color: var(--text-muted); }
.course-progress-pct   { color: var(--brand-primary); font-weight: 600; }
.course-progress-track {
    height: 5px;
    background: var(--line);
    border-radius: 3px;
    overflow: hidden;
}
.course-progress-bar {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
    transition: width .6s ease;
}

/* Footer */
.course-foot {
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: auto;
}

/* ═══════════════════════════════════════════════
   BUTTONS
═══════════════════════════════════════════════ */
.btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px;
    border-radius: var(--radius-xs);
    font-size: 13px; font-weight: 600;
    border: 1.5px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .15s;
    text-decoration: none;
    white-space: nowrap;
}
.btn-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: #fff;
    box-shadow: 0 3px 10px rgba(9,71,168,.25);
}
.btn-primary:hover { opacity: .88; box-shadow: 0 5px 16px rgba(9,71,168,.32); transform: translateY(-1px); }
.btn-success {
    background: rgba(22,163,74,.1);
    color: var(--success);
    border-color: rgba(22,163,74,.3);
}
.btn-success:hover { background: rgba(22,163,74,.18); }
.btn-outline {
    background: transparent;
    color: var(--text-muted);
    border-color: var(--line);
}
.btn-outline:hover { border-color: var(--primary); color: var(--brand-primary); background: var(--primary-glow); }
.btn-unlock {
    background: linear-gradient(135deg, var(--brand-accent), #e8943a);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 3px 10px rgba(240,179,90,.3);
    flex: 1;
}
.btn-unlock:hover { opacity: .88; transform: translateY(-1px); box-shadow: 0 5px 16px rgba(240,179,90,.4); }
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══════════════════════════════════════════════
   PRICE TAG
═══════════════════════════════════════════════ */
.price-tag {
    display: flex; align-items: baseline; gap: 4px;
    font-size: 14px; font-weight: 800;
    color: var(--text);
}
.price-tag .price-currency { font-size: 11px; font-weight: 600; color: var(--text-muted); }
.price-tag .price-free     { color: var(--success); font-size: 13px; }
.price-tag .price-old {
    font-size: 11px; font-weight: 500;
    color: var(--text-muted);
    text-decoration: line-through;
}

/* Badge locked pill */
.badge-locked {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    color: var(--text-muted);
    background: var(--bg2);
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 4px 10px;
}

/* ═══════════════════════════════════════════════
   MODAL — BUY / UNLOCK
═══════════════════════════════════════════════ */
.modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(5,12,28,.6);
    backdrop-filter: blur(4px);
    z-index: 900;
    display: none;
    align-items: center; justify-content: center;
    padding: 20px;
}
.modal-backdrop.open { display: flex; animation: backdropIn .2s ease; }
@keyframes backdropIn { from { opacity: 0; } to { opacity: 1; } }

.modal-box {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    width: 100%; max-width: 460px;
    box-shadow: var(--shadow);
    overflow: hidden;
    animation: modalIn .22s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(16px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1.5px solid var(--line);
}
.modal-head-title { font-size: 16px; font-weight: 700; color: var(--text); }
.modal-close {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: var(--bg2);
    border: 1.5px solid var(--line);
    color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 15px;
    transition: all .15s;
}
.modal-close:hover { background: var(--primary-glow); color: var(--brand-primary); border-color: var(--primary); }

.modal-body { padding: 24px; }

.modal-course-preview {
    display: flex; align-items: center; gap: 14px;
    background: var(--bg2);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    margin-bottom: 20px;
}
.modal-course-thumb {
    width: 52px; height: 52px;
    border-radius: 10px;
    background-size: cover;
    background-position: center;
    flex-shrink: 0;
}
.modal-course-name { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
.modal-course-cat  { font-size: 12px; color: var(--text-muted); }

.modal-price-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 0;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    margin-bottom: 20px;
}
.modal-price-label { font-size: 13px; color: var(--text-muted); }
.modal-price-val   { font-size: 22px; font-weight: 800; color: var(--brand-primary); }

.modal-features { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
.modal-feature {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; color: var(--text-muted);
}
.modal-feature i { font-size: 16px; color: var(--success); flex-shrink: 0; }

.modal-foot {
    display: flex; gap: 10px;
    padding: 16px 24px;
    border-top: 1.5px solid var(--line);
    background: var(--bg2);
}
.modal-foot .btn { flex: 1; justify-content: center; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 1024px) {
    .course-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 640px) {
    .course-grid { grid-template-columns: 1fr; padding: 14px; }
    .courses-hero { padding: 20px; }
    .courses-hero-stats { display: none; }
    .section-card-head { padding: 14px 16px; }
    .section-card-body  { padding: 14px; }
    .category-tabs-wrap { padding: 0 14px; }
    .subcategory-row    { padding: 12px 14px 0; }
}
</style>

@php
    $enrolledCourses = $categories
        ->flatMap(fn ($cat) => $cat->courses)
        ->filter(fn ($course) => in_array($course->id, $enrolledCourseIds, true))
        ->unique('id')
        ->values();

    $totalCourses = $categories->flatMap(fn($c) => $c->courses->concat($c->children->flatMap->courses))->unique('id')->count();
@endphp

{{-- ══════════════════════════════════════════════════════════
     BUY / UNLOCK MODAL
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="buyModal" role="dialog" aria-modal="true" aria-label="Buy course">
    <div class="modal-box">
        <div class="modal-head">
            <span class="modal-head-title">
                <i class="ti ti-lock-open" style="color:var(--brand-accent)"></i>
                Unlock Course
            </span>
            <button class="modal-close" onclick="closeBuyModal()" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-course-preview">
                <div class="modal-course-thumb" id="modalThumb"></div>
                <div>
                    <div class="modal-course-name" id="modalCourseName">Course Name</div>
                    <div class="modal-course-cat"  id="modalCourseCategory">Category</div>
                </div>
            </div>

            <div class="modal-price-row">
                <span class="modal-price-label">One-time access fee</span>
                <span class="modal-price-val" id="modalPrice">₹999</span>
            </div>

            <div class="modal-features">
                <div class="modal-feature">
                    <i class="ti ti-infinity"></i>
                    Lifetime access to all course content
                </div>
                <div class="modal-feature">
                    <i class="ti ti-certificate"></i>
                    Verified completion certificate
                </div>
                <div class="modal-feature">
                    <i class="ti ti-headset"></i>
                    Dedicated trainer support
                </div>
                <div class="modal-feature">
                    <i class="ti ti-refresh"></i>
                    Free content updates included
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-outline" onclick="closeBuyModal()">
                <i class="ti ti-x"></i> Cancel
            </button>
            <form method="POST" id="buyForm" action="" style="flex:1;display:flex">
                @csrf
                <button type="submit" class="btn btn-unlock" style="flex:1;justify-content:center">
                    <i class="ti ti-credit-card"></i>
                    Buy & Unlock Now
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     PAGE
══════════════════════════════════════════════════════════ --}}
<div class="courses-page">

    {{-- ── Hero header ─────────────────────────────────── --}}
    <div class="courses-hero">
        <div class="courses-hero-left">
            <div class="courses-hero-icon" aria-hidden="true">
                <i class="ti ti-books"></i>
            </div>
            <div>
                <div class="courses-hero-title">My Courses</div>
                <div class="courses-hero-sub">
                    Browse enrolled courses and unlock new ones to keep learning.
                </div>
            </div>
        </div>
        <div class="courses-hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $enrolledCourses->count() }}</div>
                <div class="hero-stat-label">Enrolled</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $totalCourses }}</div>
                <div class="hero-stat-label">Available</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $categories->count() }}</div>
                <div class="hero-stat-label">Categories</div>
            </div>
        </div>
    </div>

    {{-- ── Enrolled courses ─────────────────────────────── --}}
    <div class="section-card">
        <div class="section-card-head">
            <div class="section-card-title">
                <i class="ti ti-star" aria-hidden="true"></i>
                My Enrolled Courses
            </div>
            <span class="section-card-count">{{ $enrolledCourses->count() }} course{{ $enrolledCourses->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="course-grid">
            @forelse ($enrolledCourses as $course)
                @php
                    $thumb = $course->thumbnail_url ?: '';
                    $bg    = $thumb ? "url('{$thumb}')" : 'linear-gradient(135deg, #0947a8 0%, #7a5cff 100%)';
                    $progress = $course->progress ?? 0;
                @endphp

                <a class="course-tile"
                   href="{{ route('student.courses.show', $course) }}"
                   aria-label="Open {{ $course->title }}">

                    <div class="course-thumb" style="background-image: {{ $bg }};">
                        <div class="course-thumb-overlay" aria-hidden="true"></div>
                        <span class="course-enrolled-badge" aria-label="Enrolled">
                            <i class="ti ti-check" aria-hidden="true"></i> Enrolled
                        </span>
                        @if ($course->is_new ?? false)
                            <span class="course-new-badge">New</span>
                        @endif
                        <h3>{{ $course->title }}</h3>
                    </div>

                    <div class="course-body">
                        <div class="course-meta">
                            <i class="ti ti-folder" aria-hidden="true" style="font-size:13px"></i>
                            {{ $course->category?->name ?? 'General' }}
                            <span class="course-meta-dot" aria-hidden="true"></span>
                            <i class="ti ti-clock" aria-hidden="true" style="font-size:13px"></i>
                            {{ $course->duration ?? '—' }}
                        </div>

                        @if ($progress > 0)
                            <div class="course-progress-wrap">
                                <div class="course-progress-row">
                                    <span class="course-progress-label">Progress</span>
                                    <span class="course-progress-pct">{{ $progress }}%</span>
                                </div>
                                <div class="course-progress-track">
                                    <div class="course-progress-bar"
                                         style="width: {{ $progress }}%"
                                         role="progressbar"
                                         aria-valuenow="{{ $progress }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endif

                        <div class="course-foot">
                            <span class="btn btn-success btn-sm">
                                <i class="ti ti-player-play" aria-hidden="true"></i>
                                {{ $progress > 0 ? 'Continue' : 'Start Course' }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <i class="ti ti-books" aria-hidden="true"></i>
                    <p>You haven't enrolled in any courses yet.<br>Browse below to find your first one!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── Browse by Category ───────────────────────────── --}}
    <div class="section-card">
        <div class="section-card-head">
            <div class="section-card-title">
                <i class="ti ti-layout-grid" aria-hidden="true"></i>
                Browse All Courses
            </div>
            <div class="filter-bar">
                <div class="filter-search">
                    <i class="ti ti-search" aria-hidden="true"></i>
                    <input type="search"
                           id="courseSearch"
                           placeholder="Search courses…"
                           autocomplete="off"
                           aria-label="Search courses">
                </div>
            </div>
        </div>

        {{-- Category tab bar --}}
        <div class="category-tabs-wrap" id="categoryTabs" role="tablist" aria-label="Course categories">
            @foreach ($categories as $index => $category)
                @php
                    $catCount = $category->courses
                        ->concat($category->children->flatMap->courses)
                        ->unique('id')->count();
                @endphp
                <button class="cat-tab {{ $index === 0 ? 'active' : '' }}"
                        type="button"
                        role="tab"
                        data-tab="{{ $category->id }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="panel-{{ $category->id }}">
                    {{ $category->name }}
                    <span class="cat-count">{{ $catCount }}</span>
                </button>
            @endforeach
        </div>

        {{-- Tab panels --}}
        @foreach ($categories as $index => $category)
            @php
                $tabCourses = $category->courses
                    ->concat($category->children->flatMap->courses)
                    ->unique('id')
                    ->values();
            @endphp

            <div class="tab-panel {{ $index === 0 ? 'active' : '' }}"
                 id="panel-{{ $category->id }}"
                 role="tabpanel"
                 data-tab-panel="{{ $category->id }}">

                {{-- Sub-category pills --}}
                @if ($category->children->isNotEmpty())
                    <div class="subcategory-row">
                        <span class="subcategory-label" aria-hidden="true">Filter:</span>
                        <button class="sub-pill active" type="button" data-subtab="all">All</button>
                        @foreach ($category->children as $child)
                            <button class="sub-pill" type="button" data-subtab="{{ $child->id }}">
                                {{ $child->name }}
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Courses --}}
                <div class="course-grid">
                    @forelse ($tabCourses as $course)
                        @php
                            $thumb      = $course->thumbnail_url ?: '';
                            $bg         = $thumb ? "url('{$thumb}')" : 'linear-gradient(135deg, #0947a8 0%, #7a5cff 100%)';
                            $enrolled   = in_array($course->id, $enrolledCourseIds, true);
                            $catLabel   = $course->subcategory?->name ?? $course->category?->name ?? $category->name;
                            $subCatId   = $course->subcategory?->id ? (string) $course->subcategory->id : 'none';
                            $price      = $course->price ?? 0;
                            $priceLabel = $price > 0 ? '₹' . number_format($price) : 'Free';
                            $buyUrl     = route('student.courses.buy', $course);
                        @endphp

                        @if ($enrolled)
                            {{-- ENROLLED TILE --}}
                            <a class="course-tile"
                               href="{{ route('student.courses.show', $course) }}"
                               data-subcat="{{ $subCatId }}"
                               data-title="{{ strtolower($course->title) }}"
                               aria-label="Open {{ $course->title }}">

                                <div class="course-thumb" style="background-image: {{ $bg }};">
                                    <div class="course-thumb-overlay" aria-hidden="true"></div>
                                    <span class="course-enrolled-badge">
                                        <i class="ti ti-check" aria-hidden="true"></i> Enrolled
                                    </span>
                                    <h3>{{ $course->title }}</h3>
                                </div>

                                <div class="course-body">
                                    <div class="course-meta">
                                        <i class="ti ti-folder" style="font-size:13px" aria-hidden="true"></i>
                                        {{ $catLabel }}
                                    </div>
                                    <div class="course-foot">
                                        <span class="btn btn-success btn-sm">
                                            <i class="ti ti-player-play" aria-hidden="true"></i>
                                            Open Course
                                        </span>
                                    </div>
                                </div>
                            </a>

                        @else
                            {{-- LOCKED TILE --}}
                            <div class="course-tile is-locked"
                                 data-subcat="{{ $subCatId }}"
                                 data-title="{{ strtolower($course->title) }}"
                                 tabindex="0"
                                 role="article"
                                 aria-label="{{ $course->title }} — locked, price {{ $priceLabel }}">

                                <div class="course-thumb" style="background-image: {{ $bg }};">
                                    <div class="course-thumb-overlay" aria-hidden="true"></div>
                                    <div class="course-lock-overlay" aria-hidden="true">
                                        <div class="course-lock-icon">
                                            <i class="ti ti-lock"></i>
                                        </div>
                                    </div>
                                    <h3>{{ $course->title }}</h3>
                                </div>

                                <div class="course-body">
                                    <div class="course-meta">
                                        <i class="ti ti-folder" style="font-size:13px" aria-hidden="true"></i>
                                        {{ $catLabel }}
                                        @if ($course->duration)
                                            <span class="course-meta-dot" aria-hidden="true"></span>
                                            <i class="ti ti-clock" style="font-size:13px" aria-hidden="true"></i>
                                            {{ $course->duration }}
                                        @endif
                                    </div>

                                    <div class="course-foot">
                                        <div class="price-tag" aria-label="Price: {{ $priceLabel }}">
                                            @if ($price > 0)
                                                <span class="price-currency">₹</span>
                                                <span>{{ number_format($price) }}</span>
                                                @if ($course->original_price && $course->original_price > $price)
                                                    <span class="price-old">₹{{ number_format($course->original_price) }}</span>
                                                @endif
                                            @else
                                                <span class="price-free">Free</span>
                                            @endif
                                        </div>

                                        <button class="btn btn-unlock btn-sm"
                                                type="button"
                                                onclick="openBuyModal(
                                                    '{{ addslashes($course->title) }}',
                                                    '{{ addslashes($catLabel) }}',
                                                    '{{ $priceLabel }}',
                                                    '{{ $bg }}',
                                                    '{{ $buyUrl }}'
                                                )"
                                                aria-label="Unlock {{ $course->title }} for {{ $priceLabel }}">
                                            <i class="ti ti-lock-open" aria-hidden="true"></i>
                                            {{ $price > 0 ? 'Buy' : 'Enroll' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @empty
                        <div class="empty-state">
                            <i class="ti ti-mood-empty" aria-hidden="true"></i>
                            <p>No courses available in this category yet.</p>
                        </div>
                    @endforelse
                </div>

            </div>{{-- /.tab-panel --}}
        @endforeach

    </div>{{-- /.section-card --}}

</div>{{-- /.courses-page --}}

{{-- ══════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════ --}}
<script>
/* ── Category tabs ──────────────────────────────────── */
document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.tab;

        /* Tabs */
        document.querySelectorAll('.cat-tab').forEach(b => {
            b.classList.toggle('active', b === btn);
            b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
        });

        /* Panels */
        document.querySelectorAll('.tab-panel').forEach(p => {
            p.classList.toggle('active', p.dataset.tabPanel === id);
        });

        /* Reset sub-pills of the newly-active panel */
        const activePanel = document.querySelector(`.tab-panel[data-tab-panel="${id}"]`);
        if (activePanel) {
            activePanel.querySelectorAll('.sub-pill').forEach((p, i) => p.classList.toggle('active', i === 0));
            filterBySubtab(activePanel, 'all');
        }
    });
});

/* ── Sub-category pills ─────────────────────────────── */
document.querySelectorAll('.subtab-row').forEach(row => {
    row.querySelectorAll('.sub-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            row.querySelectorAll('.sub-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');

            const panel = pill.closest('.tab-panel');
            filterBySubtab(panel, pill.dataset.subtab);
        });
    });
});

function filterBySubtab(panel, subtabId) {
    panel.querySelectorAll('.course-tile').forEach(tile => {
        const match = subtabId === 'all' || tile.dataset.subcat === subtabId;
        tile.style.display = match ? '' : 'none';
    });
    checkEmpty(panel);
}

/* ── Search ─────────────────────────────────────────── */
document.getElementById('courseSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();

    /* Activate "All" sub-pill across every panel when searching */
    document.querySelectorAll('.sub-pill[data-subtab="all"]').forEach(p => {
        p.click();
    });

    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.querySelectorAll('.course-tile').forEach(tile => {
            const title = tile.dataset.title ?? '';
            tile.style.display = (!q || title.includes(q)) ? '' : 'none';
        });
        checkEmpty(panel);
    });
});

function checkEmpty(panel) {
    const visible = [...panel.querySelectorAll('.course-tile')].filter(t => t.style.display !== 'none');
    let empty = panel.querySelector('.course-grid .empty-state-dynamic');
    if (visible.length === 0) {
        if (!empty) {
            empty = document.createElement('div');
            empty.className = 'empty-state empty-state-dynamic';
            empty.innerHTML = '<i class="ti ti-mood-empty"></i><p>No courses match your search.</p>';
            panel.querySelector('.course-grid').appendChild(empty);
        }
    } else {
        empty?.remove();
    }
}

/* ── Buy / Unlock Modal ─────────────────────────────── */
function openBuyModal(name, category, price, bg, actionUrl) {
    document.getElementById('modalCourseName').textContent     = name;
    document.getElementById('modalCourseCategory').textContent = category;
    document.getElementById('modalPrice').textContent          = price;
    document.getElementById('modalThumb').style.backgroundImage = bg;
    document.getElementById('buyForm').action                  = actionUrl;
    document.getElementById('buyModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeBuyModal() {
    document.getElementById('buyModal').classList.remove('open');
    document.body.style.overflow = '';
}

/* Close on backdrop click */
document.getElementById('buyModal').addEventListener('click', function(e) {
    if (e.target === this) closeBuyModal();
});

/* Close on Escape */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeBuyModal();
});
</script>

@endsection