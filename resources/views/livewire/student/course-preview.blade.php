{{-- resources/views/livewire/student/course-preview.blade.php --}}
<div class="cp-page">

<style>
/* ═══════════════════════════════════════════════
   PAGE
═══════════════════════════════════════════════ */
.cp-page {  margin: 0 auto;  display: flex; flex-direction: column; gap: 20px; }

.cp-back {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600; color: var(--text-muted);
    text-decoration: none; width: fit-content;
    transition: color .15s;
}
.cp-back:hover { color: var(--brand-primary); }

/* ═══════════════════════════════════════════════
   HERO
═══════════════════════════════════════════════ */
.cp-hero {
    border-radius: var(--radius);
    overflow: hidden;
    border: 1.5px solid var(--line);
    box-shadow: var(--shadow-card);
    position: relative;
    min-height: 220px;
    background-size: cover;
    background-position: center;
    display: flex; align-items: flex-end;
}
.cp-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(5,15,35,.88) 0%, rgba(5,15,35,.35) 55%, rgba(5,15,35,.15) 100%);
}
.cp-hero-body {
    position: relative; z-index: 1;
    padding: 26px 28px;
    width: 100%;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
}
.cp-hero-crumb {
    font-size: 12px; color: rgba(255,255,255,.75); margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.cp-hero-title {
    font-size: 26px; font-weight: 800; color: #fff; line-height: 1.25;
    text-shadow: 0 2px 8px rgba(0,0,0,.35);
    max-width: 640px;
}
.cp-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 20px;
    font-size: 12.5px; font-weight: 700;
    backdrop-filter: blur(6px);
    flex-shrink: 0;
}
.cp-status-enrolled { background: rgba(22,163,74,.18); color: #7ee2a0; border: 1px solid rgba(22,163,74,.4); }
.cp-status-locked   { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.3); }
.cp-status-completed { background: rgba(240,179,90,.2); color: #ffd699; border: 1px solid rgba(240,179,90,.45); }

/* ═══════════════════════════════════════════════
   STAT STRIP
═══════════════════════════════════════════════ */
.cp-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
}
.cp-stat {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
    box-shadow: var(--shadow-sm);
}
.cp-stat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--primary-glow); color: var(--brand-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
}
.cp-stat-val { font-size: 15px; font-weight: 800; color: var(--text-main); line-height: 1.2; }
.cp-stat-label { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

/* ═══════════════════════════════════════════════
   CARD
═══════════════════════════════════════════════ */
.cp-card {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    overflow: hidden;
}
.cp-card-head {
    padding: 18px 22px;
    border-bottom: 1.5px solid var(--line);
    font-size: 15px; font-weight: 700; color: var(--text-main);
    display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;
}
.cp-card-head-left { display: flex; align-items: center; gap: 8px; }
.cp-card-head i { color: var(--brand-primary); font-size: 17px; }
.cp-card-body { padding: 22px; }

.cp-desc { color: var(--text-muted); line-height: 1.75; font-size: 13.5px; white-space: pre-line; }

/* ═══════════════════════════════════════════════
   CERTIFICATE CARD
═══════════════════════════════════════════════ */
.cp-cert-card {
    border-radius: var(--radius);
    overflow: hidden;
    border: 1.5px solid rgba(240,179,90,.4);
    background: linear-gradient(135deg, rgba(240,179,90,.14), rgba(9,71,168,.06));
    box-shadow: var(--shadow-card);
    padding: 22px 26px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 20px; flex-wrap: wrap;
}
.cp-cert-left { display: flex; align-items: center; gap: 16px; }
.cp-cert-icon {
    width: 54px; height: 54px; border-radius: 14px;
    background: linear-gradient(135deg, var(--brand-accent), #e8943a);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 24px; flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(240,179,90,.35);
}
.cp-cert-title { font-size: 16px; font-weight: 800; color: var(--text-main); }
.cp-cert-sub { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }

.cp-progress-card { padding: 20px 22px; }
.cp-progress-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
.cp-progress-label { color: var(--text-muted); font-weight: 600; }
.cp-progress-pct { color: var(--brand-primary); font-weight: 800; }
.cp-progress-track { height: 8px; background: var(--line); border-radius: 4px; overflow: hidden; }
.cp-progress-bar { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary)); transition: width .6s ease; }
.cp-progress-note { font-size: 11.5px; color: var(--text-muted); margin-top: 8px; }

/* ═══════════════════════════════════════════════
   BATCHES / SEATS
═══════════════════════════════════════════════ */
.cp-batch-list { display: flex; flex-direction: column; gap: 10px; }
.cp-batch-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 14px; flex-wrap: wrap;
    padding: 14px 16px;
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    background: var(--bg2);
    cursor: pointer;
    transition: all .15s;
}
.cp-batch-row:hover { border-color: var(--primary); }
.cp-batch-row.active { border-color: var(--brand-primary); background: var(--primary-glow); }
.cp-batch-row.is-full { opacity: .55; cursor: not-allowed; }
.cp-batch-left { display: flex; align-items: center; gap: 12px; }
.cp-batch-code { font-size: 13.5px; font-weight: 700; color: var(--text-main); }
.cp-batch-date { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
.cp-batch-mode {
    font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px;
    padding: 3px 10px; border-radius: 20px;
    background: var(--primary-glow); color: var(--brand-primary);
}
.cp-batch-seats { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; min-width: 90px; }
.cp-batch-seats-label { font-size: 11.5px; font-weight: 700; color: var(--text-muted); white-space: nowrap; }
.cp-batch-seats-label.is-low { color: var(--warning); }
.cp-batch-seats-label.is-full { color: var(--danger); }
.cp-batch-seats-track { width: 70px; height: 5px; border-radius: 3px; background: var(--line); overflow: hidden; }
.cp-batch-seats-fill { height: 100%; background: var(--brand-primary); }
.cp-batch-seats-fill.is-low { background: var(--warning); }
.cp-batch-seats-fill.is-full { background: var(--danger); }
.cp-your-batch {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    padding: 12px 16px;
    border: 1px dashed var(--line);
    border-radius: var(--radius-sm);
    background: var(--bg2);
    font-size: 12.5px; color: var(--text-muted);
}
.cp-your-batch strong { color: var(--text-main); }

/* ═══════════════════════════════════════════════
   RELATED COURSES
═══════════════════════════════════════════════ */
.cp-related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
}
.cp-related-tile {
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    overflow: hidden;
    text-decoration: none; color: inherit;
    background: var(--bg2);
    transition: transform .15s, box-shadow .15s, border-color .15s;
    display: flex; flex-direction: column;
}
.cp-related-tile:hover { transform: translateY(-3px); box-shadow: var(--shadow-sm); border-color: var(--brand-primary); }
.cp-related-thumb {
    height: 90px;
    background-size: cover; background-position: center;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
}
.cp-related-body { padding: 10px 12px; }
.cp-related-title { font-size: 12.5px; font-weight: 700; color: var(--text-main); line-height: 1.3; margin-bottom: 4px; }
.cp-related-price { font-size: 12px; font-weight: 800; color: var(--brand-primary); }
.cp-related-price.free { color: var(--success); }

/* ═══════════════════════════════════════════════
   DEMO VIDEOS
═══════════════════════════════════════════════ */
.cp-video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
}
.cp-video-tile {
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    overflow: hidden;
    background: var(--bg2);
    transition: transform .15s, box-shadow .15s;
}
.cp-video-tile:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); }
.cp-video-thumb {
    height: 110px;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.cp-video-play {
    width: 42px; height: 42px; border-radius: 50%;
    background: rgba(255,255,255,.9);
    color: var(--brand-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
}
.cp-video-info { padding: 10px 12px; }
.cp-video-name { font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 2px; }
.cp-video-desc { font-size: 11.5px; color: var(--text-muted); line-height: 1.4; }

/* ═══════════════════════════════════════════════
   CURRICULUM ACCORDION
═══════════════════════════════════════════════ */
.cp-week {
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    margin-bottom: 12px;
    overflow: hidden;
}
.cp-week:last-child { margin-bottom: 0; }
.cp-week-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px;
    background: var(--bg2);
    cursor: pointer;
    user-select: none;
}
.cp-week-title { font-size: 13.5px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
.cp-week-count { font-size: 11px; color: var(--text-muted); font-weight: 600; }
.cp-week-chevron { color: var(--text-muted); transition: transform .2s; font-size: 15px; }
.cp-week.open .cp-week-chevron { transform: rotate(180deg); }
.cp-week-body { display: none; padding: 4px 16px 14px; }
.cp-week.open .cp-week-body { display: block; }

.cp-session { margin-top: 10px; }
.cp-session-title {
    font-size: 12.5px; font-weight: 700; color: var(--text-main);
    display: flex; align-items: center; gap: 7px; margin-bottom: 6px;
}
.cp-session-title i { color: var(--brand-primary); font-size: 13px; }
.cp-session-item-count {
    margin-left: auto;
    font-size: 10.5px; font-weight: 700; color: var(--text-muted);
    background: var(--bg2); padding: 2px 8px; border-radius: 20px;
}
.cp-item {
    display: flex; align-items: center; gap: 10px;
    font-size: 12.5px; color: var(--text-muted);
    padding: 7px 0 7px 20px;
    border-left: 2px solid var(--line);
    margin-left: 4px;
}
.cp-item.unlocked { color: var(--text-main); }
.cp-item i { font-size: 14px; flex-shrink: 0; width: 16px; text-align: center; }
.cp-item.unlocked i { color: var(--success); }
.cp-item.locked-item i { color: var(--text-muted); opacity: .6; }
.cp-item-type {
    margin-left: auto;
    font-size: 10px; text-transform: uppercase; letter-spacing: .3px;
    color: var(--text-muted); background: var(--bg2);
    padding: 2px 8px; border-radius: 20px; flex-shrink: 0;
}

.cp-empty { text-align: center; padding: 30px; color: var(--text-muted); font-size: 13px; }
.cp-empty i { font-size: 28px; opacity: .4; display: block; margin-bottom: 8px; }

.error-text { color: var(--danger); font-size: 12px; display: block; margin-top: 6px; }

/* ═══════════════════════════════════════════════
   STICKY ACTION BAR
═══════════════════════════════════════════════ */
.cp-action-bar {
    position: sticky; bottom: 16px;
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 16px 22px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
    z-index: 50;
}
.cp-action-price { display: flex; flex-direction: column; }
.cp-action-price-val { font-size: 22px; font-weight: 800; color: var(--text-main); }
.cp-action-price-old { font-size: 12px; color: var(--text-muted); text-decoration: line-through; }
.cp-action-price-free { font-size: 20px; font-weight: 800; color: var(--success); }
.cp-action-buttons { display: flex; gap: 10px; flex: 1; min-width: 260px; justify-content: flex-end; }

.btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px;
    border-radius: var(--radius-xs);
    font-size: 13.5px; font-weight: 700;
    border: 1.5px solid transparent;
    cursor: pointer; font-family: inherit;
    transition: all .15s; text-decoration: none; white-space: nowrap;
}
.btn-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: #fff; box-shadow: 0 3px 10px rgba(9,71,168,.25);
}
.btn-primary:hover { opacity: .9; transform: translateY(-1px); }
.btn-unlock {
    background: linear-gradient(135deg, var(--brand-accent), #e8943a);
    color: #fff; box-shadow: 0 3px 10px rgba(240,179,90,.3);
}
.btn-unlock:hover { opacity: .9; transform: translateY(-1px); }
.btn-outline { background: transparent; color: var(--text-muted); border-color: var(--line); }
.btn-outline:hover { border-color: var(--primary); color: var(--brand-primary); background: var(--primary-glow); }
.cp-cart-msg { color: var(--success); font-size: 12.5px; margin-top: 4px; width: 100%; text-align: right; }

@media (max-width: 640px) {
    .cp-hero-title { font-size: 20px; }
    .cp-action-bar { flex-direction: column; align-items: stretch; }
    .cp-action-buttons { justify-content: stretch; }
    .cp-action-buttons .btn { flex: 1; justify-content: center; }
    .cp-cert-card { flex-direction: column; align-items: stretch; text-align: center; }
    .cp-cert-left { flex-direction: column; }
}
</style>

@php
    $thumb = $course->thumbnail_url ?: '';
    $heroBg = $thumb ? "url('{$thumb}')" : 'linear-gradient(135deg, #0947a8 0%, #7a5cff 100%)';
    $price = $course->price ?? 0;
    $weekCount = $course->weeks->count();
    $sessionCount = $course->weeks->flatMap->sessions->count();
    $itemCount = $course->weeks->flatMap->sessions->flatMap->items->count();

    // ASSUMPTION: Course::courseType() belongsTo CourseType{name}. Falls back to "Self-paced".
    $courseTypeLabel = $course->courseType->name ?? 'Self-paced';

    $openBatches = $course->batches->filter(fn ($b) => in_array($b->status ?? 'upcoming', ['upcoming', 'active']));

    // ASSUMPTION: `certificate_unlocked_at` / `progress_percent` / `status` on the enrollment
    // row come from your Enrollment model. If User::enrollmentsAsStudent() points at a
    // different model without these columns, they'll just read as null/0 below.
    $isCompleted = $isEnrolled && ($enrollment->status === 'completed' || $enrollment->certificate_unlocked_at);
    $progressPct = $isEnrolled ? ($enrollment->progress_percent ?? 0) : 0;
@endphp

{{-- ── Back link ── --}}
<a href="{{ route('student.courses') }}" class="cp-back">
    <i class="ti ti-arrow-left"></i> Back to Courses
</a>

{{-- ── Hero ── --}}
<div class="cp-hero" style="background-image: {{ $heroBg }};">
    <div class="cp-hero-overlay"></div>
    <div class="cp-hero-body">
        <div>
            <div class="cp-hero-crumb">
                <i class="ti ti-folder"></i>
                {{ $course->category?->name ?? 'General' }}
                @if ($course->subcategory)
                    <i class="ti ti-chevron-right" style="font-size:11px;"></i> {{ $course->subcategory->name }}
                @endif
                <i class="ti ti-point-filled" style="font-size:8px;opacity:.6;"></i>
                {{ $courseTypeLabel }}
            </div>
            <h1 class="cp-hero-title">{{ $course->title }}</h1>
        </div>

        @if ($isCompleted)
            <span class="cp-status-badge cp-status-completed"><i class="ti ti-certificate"></i> Completed</span>
        @elseif ($isEnrolled)
            <span class="cp-status-badge cp-status-enrolled"><i class="ti ti-check"></i> Enrolled</span>
        @else
            <span class="cp-status-badge cp-status-locked"><i class="ti ti-lock"></i> Locked</span>
        @endif
    </div>
</div>

{{-- ── Stat strip ── --}}
<div class="cp-stats">
    <div class="cp-stat">
        <div class="cp-stat-icon"><i class="ti ti-clock"></i></div>
        <div><div class="cp-stat-val">{{ $course->duration ?? '—' }}</div><div class="cp-stat-label">Duration</div></div>
    </div>
    <div class="cp-stat">
        <div class="cp-stat-icon"><i class="ti ti-world"></i></div>
        <div><div class="cp-stat-val">{{ $course->language ?? 'English' }}</div><div class="cp-stat-label">Language</div></div>
    </div>
    <div class="cp-stat">
        <div class="cp-stat-icon"><i class="ti ti-chart-bar"></i></div>
        <div><div class="cp-stat-val">{{ $course->level?->name ?? $course->level ?? '—' }}</div><div class="cp-stat-label">Level</div></div>
    </div>
    <div class="cp-stat">
        <div class="cp-stat-icon"><i class="ti ti-calendar-week"></i></div>
        <div><div class="cp-stat-val">{{ $weekCount }} weeks</div><div class="cp-stat-label">{{ $sessionCount }} sessions</div></div>
    </div>
    <div class="cp-stat">
        <div class="cp-stat-icon"><i class="ti ti-list-check"></i></div>
        <div><div class="cp-stat-val">{{ $itemCount }}</div><div class="cp-stat-label">Total lessons</div></div>
    </div>
    @if ($openBatches->isNotEmpty())
        <div class="cp-stat">
            <div class="cp-stat-icon"><i class="ti ti-users"></i></div>
            <div><div class="cp-stat-val">{{ $openBatches->count() }} batch{{ $openBatches->count() !== 1 ? 'es' : '' }}</div><div class="cp-stat-label">Open now</div></div>
        </div>
    @endif
</div>

{{-- ── Certificate / progress ── --}}
@if ($isCompleted)
    <div class="cp-cert-card">
        <div class="cp-cert-left">
            <div class="cp-cert-icon"><i class="ti ti-certificate"></i></div>
            <div>
                <div class="cp-cert-title">You've completed this course 🎉</div>
                <div class="cp-cert-sub">Your certificate is ready to download.</div>
            </div>
        </div>
        {{-- ASSUMPTION: route name for certificate download — adjust to match yours --}}
        <a href="{{ route('student.certificate.download', $enrollment->id) }}" class="btn btn-unlock">
            <i class="ti ti-download"></i> Download Certificate
        </a>
    </div>
@elseif ($isEnrolled)
    <div class="cp-card cp-progress-card">
        <div class="cp-progress-row">
            <span class="cp-progress-label">Your progress</span>
            <span class="cp-progress-pct">{{ $progressPct }}%</span>
        </div>
        <div class="cp-progress-track">
            <div class="cp-progress-bar" style="width:{{ $progressPct }}%" role="progressbar" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="cp-progress-note">Finish the remaining lessons to unlock your certificate.</div>
    </div>
@endif

{{-- ── Your batch (enrolled) ── --}}
@if ($isEnrolled && $enrollment->batch)
    <div class="cp-your-batch">
        <i class="ti ti-users" style="color:var(--brand-primary);font-size:16px;"></i>
        You're in batch <strong>{{ $enrollment->batch->batch_code }}</strong>
        · {{ ucfirst($enrollment->batch->mode ?? 'Online') }}
        · started {{ $enrollment->batch->start_date?->format('d M Y') ?? '—' }}
    </div>
@endif

{{-- ── About ── --}}
<div class="cp-card">
    <div class="cp-card-head"><div class="cp-card-head-left"><i class="ti ti-info-circle"></i> About this course</div></div>
    <div class="cp-card-body">
        <p class="cp-desc">{{ $course->description ?? $course->short_description }}</p>
    </div>
</div>

{{-- ── Batches & seats (only relevant before enrolling) ── --}}
@if (! $isEnrolled && $openBatches->isNotEmpty())
    <div class="cp-card">
        <div class="cp-card-head">
            <div class="cp-card-head-left"><i class="ti ti-calendar-event"></i> Batches &amp; Seats</div>
            <span class="cp-week-count">Pick one before adding to cart</span>
        </div>
        <div class="cp-card-body">
            <div class="cp-batch-list">
                @foreach ($openBatches as $batch)
                    @php
                        $left  = $this->seatsLeftFor($batch);
                        $cap   = $batch->max_seats ?? 0;
                        $pct   = $cap > 0 ? min(100, round((($cap - $left) / $cap) * 100)) : 0;
                        $state = $left <= 0 ? 'is-full' : ($left <= 3 ? 'is-low' : '');
                    @endphp
                    <div class="cp-batch-row {{ (int) $selectedBatchId === $batch->id ? 'active' : '' }} {{ $left <= 0 ? 'is-full' : '' }}"
                         wire:click="{{ $left > 0 ? 'pickBatch(' . $batch->id . ')' : '' }}"
                         role="button"
                         tabindex="0"
                         aria-label="Batch {{ $batch->batch_code }}, {{ $left }} seats left">
                        <div class="cp-batch-left">
                            <div>
                                <div class="cp-batch-code">{{ $batch->batch_code }}</div>
                                <div class="cp-batch-date"><i class="ti ti-calendar" style="font-size:11px"></i> Starts {{ $batch->start_date?->format('d M Y') ?? 'TBA' }} · {{ $batch->max_weeks }} weeks</div>
                            </div>
                            <span class="cp-batch-mode">{{ $batch->mode ?? 'Online' }}</span>
                        </div>
                        <div class="cp-batch-seats">
                            <span class="cp-batch-seats-label {{ $state }}">{{ $left <= 0 ? 'Full' : $left . ' seat' . ($left !== 1 ? 's' : '') . ' left' }}</span>
                            <div class="cp-batch-seats-track">
                                <div class="cp-batch-seats-fill {{ $state }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('batch') <small class="error-text">{{ $message }}</small> @enderror
        </div>
    </div>
@endif

{{-- ── Demo videos ── --}}
{{-- @if ($course->demoFeatureVideos->isNotEmpty())
    <div class="cp-card">
        <div class="cp-card-head"><div class="cp-card-head-left"><i class="ti ti-video"></i> Preview Videos</div></div>
        <div class="cp-card-body">
            <div class="cp-video-grid">
                @foreach ($course->demoFeatureVideos as $video)
                    <div class="cp-video-tile">
                        <div class="cp-video-thumb">
                            <div class="cp-video-play"><i class="ti ti-player-play-filled"></i></div>
                        </div>
                        <div class="cp-video-info">
                            <div class="cp-video-name">{{ $video->title }}</div>
                            <div class="cp-video-desc">{{ $video->description }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif --}}

{{-- ── Curriculum ── --}}
<div class="cp-card">
    <div class="cp-card-head"><div class="cp-card-head-left"><i class="ti ti-list-details"></i> Curriculum</div></div>
    <div class="cp-card-body">
        @forelse ($course->weeks as $wIndex => $week)
            <div class="cp-week {{ $wIndex === 0 ? 'open' : '' }}" data-week>
                <div class="cp-week-head" data-week-toggle>
                    <div class="cp-week-title">
                        <i class="ti ti-calendar-week" style="color:var(--brand-primary);"></i>
                        {{ $week->title }}
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="cp-week-count">{{ $week->sessions->count() }} sessions · {{ $week->sessions->flatMap->items->count() }} lessons</span>
                        <i class="ti ti-chevron-down cp-week-chevron"></i>
                    </div>
                </div>
                <div class="cp-week-body">
                    @foreach ($week->sessions as $session)
                        <div class="cp-session">
                            <div class="cp-session-title">
                                <i class="ti ti-folder-open"></i> {{ $session->title }}
                                <span class="cp-session-item-count">{{ $session->items->count() }} lesson{{ $session->items->count() !== 1 ? 's' : '' }}</span>
                            </div>
                            @foreach ($session->items as $item)
                                <div class="cp-item {{ $isEnrolled ? 'unlocked' : 'locked-item' }}">
                                    <i class="ti {{ $isEnrolled ? 'ti-player-play' : 'ti-lock' }}"></i>
                                    {{ $item->title }}
                                    @if ($item->item_type)
                                        <span class="cp-item-type">{{ str_replace('_', ' ', $item->item_type) }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="cp-empty">
                <i class="ti ti-mood-empty"></i>
                Curriculum details will be added soon.
            </div>
        @endforelse
    </div>
</div>

{{-- ── Related courses ── --}}
@if ($relatedCourses->isNotEmpty())
    <div class="cp-card">
        <div class="cp-card-head"><div class="cp-card-head-left"><i class="ti ti-apps"></i> You might also like</div></div>
        <div class="cp-card-body">
            <div class="cp-related-grid">
                @foreach ($relatedCourses as $related)
                    @php
                        $rThumb = $related->thumbnail_url ?: '';
                        $rBg = $rThumb ? "url('{$rThumb}')" : '';
                        $rPrice = $related->price ?? 0;
                    @endphp
                    <a href="{{ route('student.courses.preview', $related) }}" class="cp-related-tile">
                        <div class="cp-related-thumb" style="{{ $rBg ? 'background-image:' . $rBg . ';' : '' }}"></div>
                        <div class="cp-related-body">
                            <div class="cp-related-title">{{ $related->title }}</div>
                            <div class="cp-related-price {{ $rPrice > 0 ? '' : 'free' }}">{{ $rPrice > 0 ? '₹' . number_format($rPrice) : 'Free' }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- ── Sticky action bar ── --}}
<div class="cp-action-bar">
    @if ($isEnrolled)
        <div class="cp-action-price">
            <span class="cp-action-price-free"><i class="ti ti-check"></i> You own this course</span>
        </div>
        <div class="cp-action-buttons">
            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary">
                <i class="ti ti-player-play"></i> Continue Course
            </a>
        </div>
    @else
        <div class="cp-action-price">
            @if ($price > 0)
                <span class="cp-action-price-val">₹{{ number_format($price) }}</span>
                @if ($course->original_price && $course->original_price > $price)
                    <span class="cp-action-price-old">₹{{ number_format($course->original_price) }}</span>
                @endif
            @else
                <span class="cp-action-price-free">Free</span>
            @endif
        </div>
        <div class="cp-action-buttons">
            <a href="{{ route('student.courses') }}" class="btn btn-outline">
                <i class="ti ti-arrow-left"></i> Back
            </a>
            <button class="btn btn-unlock" wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart">
                <span wire:loading.remove wire:target="addToCart">
                    <i class="ti ti-shopping-cart-plus"></i> Add to Cart
                </span>
                <span wire:loading wire:target="addToCart">
                    <i class="ti ti-loader-2"></i> Adding...
                </span>
            </button>
        </div>
        @if (session('cart_message'))
            <div class="cp-cart-msg"><i class="ti ti-circle-check"></i> {{ session('cart_message') }}</div>
        @endif
    @endif
</div>

@script
<script>
    function cpInitAccordion() {
        document.querySelectorAll('[data-week-toggle]').forEach(head => {
            head.addEventListener('click', () => {
                head.closest('[data-week]').classList.toggle('open');
            });
        });
    }
    cpInitAccordion();
</script>
@endscript

</div>