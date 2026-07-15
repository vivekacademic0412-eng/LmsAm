@php
    use App\Models\CourseSessionItem;

    $course = $this->course;
    $week = $this->selectedWeek;
    $session = $this->selectedSession;
    $item = $this->selectedItem;
    $sessionItems = $this->selectedSessionItems;
    $enrollment = $this->enrollment;
@endphp

<div class="lw-scope" wire:key="course-{{ $course->id }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        .lw-scope {
            --lw-display: 'Sora', system-ui, sans-serif;
            --lw-body: 'Inter', system-ui, sans-serif;
            --lw-mono: 'JetBrains Mono', ui-monospace, monospace;
            font-family: var(--lw-body);
            color: var(--text);
        }

        .lw-page { margin: 0 auto; padding: 18px 20px 60px; display: grid; gap: 22px; }

        .lw-scope a { color: inherit; }
        .lw-scope * { box-sizing: border-box; }

        /* ---------- Alert ---------- */
        .lw-alert {
            border: 1px solid #f0b7b7;
            border-radius: var(--radius-sm);
            background: color-mix(in srgb, var(--danger) 8%, var(--bg-card));
            color: var(--danger);
            padding: 14px 16px;
            box-shadow: var(--shadow-sm);
        }
        .lw-alert strong { display: block; margin-bottom: 4px; font-family: var(--lw-display); }

        /* ---------- Hero ---------- */
        .lw-hero {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            padding: 34px clamp(20px, 4vw, 44px);
            background:
                radial-gradient(700px 320px at 100% -10%, color-mix(in srgb, var(--brand-secondary) 22%, transparent), transparent),
                linear-gradient(160deg, var(--brand-primary) 0%, #123b8f 55%, #0a1f4d 100%);
            color: #fff;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(220px, 0.65fr);
            gap: 28px;
            align-items: center;
        }
        .lw-hero-eyebrow {
            font-family: var(--lw-mono);
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: color-mix(in srgb, #fff 70%, var(--brand-accent));
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .lw-hero-eyebrow::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--brand-accent); }
        .lw-hero h1 {
            font-family: var(--lw-display);
            font-weight: 800;
            font-size: clamp(26px, 3.2vw, 40px);
            line-height: 1.08;
            margin: 10px 0 8px;
            letter-spacing: -0.01em;
        }
        .lw-hero-summary { margin: 0 0 18px; color: rgba(255,255,255,.78); font-size: 14.5px; line-height: 1.7; max-width: 62ch; }
        .lw-hero-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
        .lw-hero-meta span {
            font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 999px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.16);
        }
        .lw-hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }

        .lw-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            min-height: 42px; padding: 10px 18px; border-radius: var(--radius-xs);
            font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer;
            border: 1px solid transparent; transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
            font-family: var(--lw-body);
        }
        .lw-btn:hover { transform: translateY(-1px); }
        .lw-btn:active { transform: translateY(0); }
        .lw-btn-primary { background: var(--brand-accent); color: #241705; box-shadow: 0 10px 24px rgba(240,179,90,.35); }
        .lw-btn-primary:hover { filter: brightness(1.04); }
        .lw-btn-ghost { background: rgba(255,255,255,.08); color: #fff; border-color: rgba(255,255,255,.28); }
        .lw-btn-ghost:hover { background: rgba(255,255,255,.16); }
        .lw-btn-soft { background: var(--bg2); color: var(--brand-primary); border-color: var(--line); }
        .lw-btn-soft:hover { background: var(--bg); }
        .lw-btn-solid { background: var(--brand-primary); color: #fff; }
        .lw-btn-solid:hover { filter: brightness(1.06); }
        .lw-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
        .lw-btn-block { width: 100%; }

        /* ---------- Progress dial (hero) ---------- */
        .lw-dial-wrap { display: grid; justify-items: center; gap: 14px; }
        .lw-dial {
            --pct: 0;
            width: 168px; height: 168px; border-radius: 50%;
            background: conic-gradient(var(--brand-accent) calc(var(--pct) * 1%), rgba(255,255,255,.14) 0);
            display: grid; place-items: center; position: relative;
        }
        .lw-dial::after {
            content: ''; position: absolute; inset: 12px; border-radius: 50%;
            background: rgba(10, 20, 45, .55); backdrop-filter: blur(2px);
        }
        .lw-dial-inner { position: relative; z-index: 1; text-align: center; }
        .lw-dial-inner strong { display: block; font-family: var(--lw-display); font-size: 30px; font-weight: 800; }
        .lw-dial-inner span { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.7); margin-top: 2px; }
        .lw-dial-stats { display: flex; gap: 18px; }
        .lw-dial-stats div { text-align: center; }
        .lw-dial-stats strong { display: block; font-family: var(--lw-display); font-size: 18px; }
        .lw-dial-stats span { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: rgba(255,255,255,.65); }

        /* ---------- Journey rail (signature element) ---------- */
        .lw-rail-card {
            border: 1px solid var(--line); border-radius: var(--radius); background: var(--card);
            box-shadow: var(--shadow-card); padding: 20px clamp(14px, 3vw, 26px);
        }
        .lw-rail-head { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .lw-rail-head h2 { font-family: var(--lw-display); font-size: 17px; margin: 0; color: var(--text); }
        .lw-rail-head p { margin: 0; font-size: 12.5px; color: var(--muted); }
        .lw-rail {
            display: flex; gap: 0; overflow-x: auto; padding-bottom: 6px; scrollbar-width: thin;
        }
        .lw-rail-stop {
            position: relative; display: flex; flex-direction: column; align-items: center; gap: 10px;
            padding: 0 22px; flex: 0 0 auto; min-width: 128px; cursor: pointer; background: none; border: none;
        }
        .lw-rail-stop::before {
            content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 3px; background: var(--line); z-index: 0;
        }
        .lw-rail-stop:first-child::before { left: 50%; }
        .lw-rail-stop:last-child::before { right: 50%; }
        .lw-rail-stop.is-done::before { background: var(--brand-green); }
        .lw-rail-node {
            position: relative; z-index: 1; width: 32px; height: 32px; border-radius: 50%;
            display: grid; place-items: center; font-family: var(--lw-mono); font-size: 12px; font-weight: 700;
            border: 3px solid var(--line); background: var(--card); color: var(--muted);
            transition: all .18s ease;
        }
        .lw-rail-stop.is-done .lw-rail-node { border-color: var(--brand-green); background: var(--brand-green); color: #fff; }
        .lw-rail-stop.is-current .lw-rail-node { border-color: var(--brand-primary); background: var(--brand-primary); color: #fff; box-shadow: 0 0 0 5px color-mix(in srgb, var(--brand-primary) 18%, transparent); }
        .lw-rail-stop.is-next .lw-rail-node { border-color: var(--brand-accent); color: var(--brand-accent); }
        .lw-rail-label { text-align: center; }
        .lw-rail-label strong { display: block; font-size: 12.5px; color: var(--text); font-weight: 700; }
        .lw-rail-label span { display: block; font-size: 10.5px; color: var(--muted); margin-top: 2px; }

        /* ---------- Main grid ---------- */
        .lw-grid { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: 20px; align-items: start; }
        .lw-stack { display: grid; gap: 14px; position: sticky; top: 16px; }

        .lw-panel { border: 1px solid var(--line); border-radius: var(--radius); background: var(--card); box-shadow: var(--shadow-card); }
        .lw-panel-pad { padding: 16px; }
        .lw-panel-head { display: grid; gap: 3px; margin-bottom: 12px; }
        .lw-panel-head .lw-eyebrow { font-family: var(--lw-mono); font-size: 10.5px; letter-spacing: .08em; text-transform: uppercase; color: var(--brand-secondary); }
        .lw-panel-head h3 { margin: 0; font-family: var(--lw-display); font-size: 15.5px; color: var(--text); }
        .lw-panel-head p { margin: 0; font-size: 12px; color: var(--muted); line-height: 1.5; }

        .lw-list { display: grid; gap: 8px; }
        .lw-row {
            display: block; width: 100%; text-align: left; border: 1px solid var(--line); border-radius: var(--radius-xs);
            background: var(--bg-card2); padding: 11px 12px; cursor: pointer; transition: border-color .15s ease, background .15s ease, transform .15s ease;
        }
        .lw-row:hover { border-color: var(--primary); transform: translateX(2px); }
        .lw-row.is-active { border-color: var(--brand-primary); background: color-mix(in srgb, var(--brand-primary) 8%, var(--bg-card)); box-shadow: 0 0 0 1px var(--brand-primary) inset; }
        .lw-row.is-done { background: color-mix(in srgb, var(--brand-green) 6%, var(--bg-card2)); }
        .lw-row-top { display: flex; justify-content: space-between; align-items: start; gap: 8px; }
        .lw-row-top strong { font-size: 13px; color: var(--text); line-height: 1.3; }
        .lw-row-sub { font-size: 11.5px; color: var(--muted); margin-top: 4px; }
        .lw-badge {
            font-family: var(--lw-mono); font-size: 9.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
            padding: 3px 8px; border-radius: 999px; white-space: nowrap; border: 1px solid var(--line); color: var(--muted); background: var(--bg2);
        }
        .lw-badge.b-active { background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }
        .lw-badge.b-next { background: color-mix(in srgb, var(--brand-accent) 20%, var(--bg2)); color: #8a5a0d; border-color: var(--brand-accent); }
        .lw-badge.b-done { background: color-mix(in srgb, var(--brand-green) 16%, var(--bg2)); color: #0f6b32; border-color: var(--brand-green); }
        .lw-badge.b-live { background: color-mix(in srgb, var(--warning) 18%, var(--bg2)); color: var(--warning); border-color: var(--warning); }

        .lw-tiny-progress { height: 5px; border-radius: 999px; background: var(--line); overflow: hidden; margin-top: 8px; }
        .lw-tiny-progress i { display: block; height: 100%; background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary)); }

        .lw-empty {
            border: 1px dashed var(--line); border-radius: var(--radius-xs); padding: 18px; text-align: center;
            color: var(--muted); font-size: 12.5px; background: var(--bg2);
        }

        /* ---------- Viewer ---------- */
        .lw-viewer { display: grid; gap: 16px; padding: clamp(16px, 2.4vw, 26px); }
        .lw-viewer-head { display: flex; justify-content: space-between; gap: 16px; align-items: start; flex-wrap: wrap; }
        .lw-viewer-head h2 { font-family: var(--lw-display); font-size: clamp(20px, 2.4vw, 26px); margin: 6px 0 0; color: var(--text); line-height: 1.15; }
        .lw-crumb { display: flex; flex-wrap: wrap; gap: 6px; font-size: 12px; color: var(--muted); }
        .lw-crumb b { color: var(--brand-primary); }

        .lw-stat-row { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 10px; }
        .lw-stat {
            border: 1px solid var(--line); border-radius: var(--radius-xs); background: var(--bg-card2); padding: 12px 14px; display: grid; gap: 4px;
        }
        .lw-stat span { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); font-weight: 700; }
        .lw-stat strong { font-family: var(--lw-display); font-size: 15px; color: var(--text); }
        .lw-stat p { margin: 0; font-size: 11.5px; color: var(--muted); }

        .lw-frame {
            border: 1px solid var(--line); border-radius: var(--radius-sm); overflow: hidden; background: #060c18; box-shadow: var(--shadow-card);
        }
        .lw-frame iframe, .lw-frame video { width: 100%; display: block; border: 0; }
        .lw-frame iframe { min-height: 680px; background: #fff; }
        .lw-frame video { aspect-ratio: 16/9; background: #000; }

        .lw-docx, .lw-pptx {
            width: 100%; min-height: 640px; border: 1px solid var(--line); border-radius: var(--radius-sm);
            background: var(--bg2); color: var(--text); overflow: auto; box-shadow: var(--shadow-card);
        }
        .lw-docx-status, .lw-pptx-status { min-height: 600px; display: grid; place-items: center; color: var(--muted); font-size: 13.5px; padding: 20px; text-align: center; }

        .lw-hint {
            border: 1px dashed var(--line); border-radius: var(--radius-sm); background: var(--bg2); padding: 20px; text-align: center; color: var(--muted);
        }
        .lw-hint strong { display: block; color: var(--text); font-family: var(--lw-display); margin-bottom: 4px; }

        .lw-notes { border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--bg-card2); padding: 16px 18px; display: grid; gap: 10px; }
        .lw-notes-head { display: flex; justify-content: space-between; gap: 10px; align-items: baseline; flex-wrap: wrap; }
        .lw-notes-body { white-space: pre-wrap; line-height: 1.75; font-size: 13.5px; color: var(--text); }

        .lw-tools { border: 1px solid var(--line); border-radius: var(--radius-sm); background: linear-gradient(180deg, var(--bg2), var(--card)); padding: 14px 16px; display: grid; gap: 10px; }
        .lw-tools-head { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; align-items: baseline; }
        .lw-tools-head strong { font-family: var(--lw-display); font-size: 13.5px; }
        .lw-tools-head span { font-size: 11.5px; color: var(--muted); }
        .lw-tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px; }
        .lw-field { display: grid; gap: 5px; }
        .lw-field span { font-size: 11px; font-weight: 700; color: var(--muted); }
        .lw-field select, .lw-field textarea, .lw-field input[type="file"] {
            width: 100%; border: 1px solid var(--input-border); border-radius: var(--radius-xs); padding: 9px 11px;
            background: var(--input-bg); color: var(--text); font: inherit; font-size: 13px;
        }
        .lw-field textarea { min-height: 110px; resize: vertical; }
        .lw-field select:focus, .lw-field textarea:focus, .lw-field input:focus { outline: 2px solid var(--input-focus); outline-offset: 1px; }

        .lw-submit-box { border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--bg-card2); padding: 16px 18px; display: grid; gap: 12px; }
        .lw-submit-box strong { font-family: var(--lw-display); font-size: 14px; }
        .lw-submit-meta { display: grid; gap: 4px; font-size: 12px; color: var(--muted); }
        .lw-submit-meta a { color: var(--brand-primary); font-weight: 700; text-decoration: none; }
        .lw-submit-meta a:hover { text-decoration: underline; }
        .lw-answer-echo { border: 1px solid var(--line); border-radius: var(--radius-xs); background: var(--bg2); padding: 10px 12px; font-size: 12.5px; color: var(--text); }
        .lw-error { color: var(--danger); font-size: 12px; font-weight: 600; }

        .lw-actions-row { display: flex; flex-wrap: wrap; gap: 10px; }

        .lw-support-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
        .lw-support-card { border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--card); box-shadow: var(--shadow-sm); padding: 14px 16px; display: grid; gap: 5px; }
        .lw-support-card.accent { border-color: var(--brand-primary); background: color-mix(in srgb, var(--brand-primary) 5%, var(--card)); }
        .lw-support-card span { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; font-weight: 700; color: var(--muted); }
        .lw-support-card strong { font-family: var(--lw-display); font-size: 14.5px; color: var(--text); }
        .lw-support-card p { margin: 0; font-size: 11.5px; color: var(--muted); line-height: 1.55; }

        [wire\:loading] { }
        .lw-busy { opacity: .55; pointer-events: none; transition: opacity .15s ease; }

        @media (max-width: 1080px) {
            .lw-hero { grid-template-columns: 1fr; text-align: left; }
            .lw-dial-wrap { justify-self: start; }
            .lw-grid { grid-template-columns: 1fr; }
            .lw-stack { position: static; grid-auto-flow: column; overflow-x: auto; }
            .lw-support-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
        }
        @media (max-width: 720px) {
            .lw-page { padding: 12px 14px 40px; }
            .lw-hero, .lw-rail-card, .lw-panel-pad, .lw-viewer { padding: 18px; }
            .lw-stat-row, .lw-tools-grid, .lw-support-grid { grid-template-columns: 1fr; }
            .lw-stack { grid-auto-flow: row; overflow-x: visible; }
            .lw-dial { width: 132px; height: 132px; }
        }
    </style>

    <div class="lw-page" x-data="{}">

        @if ($errors->any())
            <div class="lw-alert" role="alert">
                <strong>We could not save that just now.</strong>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- ============ HERO ============ --}}
        <section class="lw-hero">
            <div>
                <a href="{{ route('student.courses') }}" class="lw-hero-eyebrow">Back to My Courses</a>
                <h1>{{ $course->title }}</h1>
                @php
                    $summary = $course->short_description
                        ?: (($stripped = trim(preg_replace('/\s+/', ' ', strip_tags((string) $course->description)))) !== ''
                            ? \Illuminate\Support\Str::limit($stripped, 200)
                            : null);
                @endphp
                @if ($summary)
                    <p class="lw-hero-summary">{{ $summary }}</p>
                @endif

                <div class="lw-hero-meta">
                    <span>{{ $course->category?->name ?? 'Uncategorized' }}</span>
                    @if ($course->subcategory)
                        <span>{{ $course->subcategory->name }}</span>
                    @endif
                    <span>Trainer · {{ $enrollment->trainer?->name ?? 'Not assigned' }}</span>
                    <span>{{ $course->language ?: 'Language N/A' }}</span>
                </div>

                <div class="lw-hero-actions">
                    @if ($this->nextPendingItemId)
                        <button type="button" class="lw-btn lw-btn-primary" wire:click="continueNextPending">
                            Continue next item
                        </button>
                    @endif
                    <a href="#learning-workspace" class="lw-btn lw-btn-ghost">Jump to workspace</a>
                </div>
            </div>

            <div class="lw-dial-wrap">
                <div class="lw-dial" style="--pct: {{ $this->progressPercent }}">
                    <div class="lw-dial-inner">
                        <strong>{{ $this->progressPercent }}%</strong>
                        <span>Complete</span>
                    </div>
                </div>
                <div class="lw-dial-stats">
                    <div><strong>{{ $course->weeks->count() }}</strong><span>Weeks</span></div>
                    <div><strong>{{ $this->allSessions->count() }}</strong><span>Sessions</span></div>
                    <div><strong>{{ $this->pendingItems }}</strong><span>Pending</span></div>
                </div>
            </div>
        </section>

        {{-- ============ JOURNEY RAIL (signature element) ============ --}}
        <section class="lw-rail-card" id="learning-workspace">
            <div class="lw-rail-head">
                <div>
                    <h2>Your course journey</h2>
                    <p>Tap a week to jump straight to it — the line fills in as you complete every item inside it.</p>
                </div>
                <span class="lw-badge {{ $this->progressPercent >= 100 ? 'b-done' : 'b-active' }}">
                    {{ $this->completedWeeksCount }} / {{ $course->weeks->count() }} weeks done
                </span>
            </div>

            <div class="lw-rail">
                @foreach ($course->weeks as $w)
                    @php
                        $wItems = $w->sessions->flatMap->items;
                        $wDone = $wItems->isNotEmpty() && $wItems->every(fn ($i) => isset($this->completedMap[(int) $i->id]));
                        $isCurrentWeek = $week && (int) $week->id === (int) $w->id;
                    @endphp
                    <button
                        type="button"
                        wire:click="selectWeek({{ $w->id }})"
                        class="lw-rail-stop {{ $wDone ? 'is-done' : '' }} {{ $isCurrentWeek ? 'is-current' : '' }}"
                    >
                        <span class="lw-rail-node">{{ $wDone ? '✓' : $w->week_number }}</span>
                        <span class="lw-rail-label">
                            <strong>Week {{ $w->week_number }}</strong>
                            <span>{{ Str::limit($w->title, 18) }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </section>

        {{-- ============ MAIN WORKSPACE ============ --}}
        <div class="lw-grid">

            {{-- ---- Sidebar: sessions + items for the selected week ---- --}}
            <aside class="lw-stack">
                <div class="lw-panel lw-panel-pad">
                    <div class="lw-panel-head">
                        <span class="lw-eyebrow">Step 1 · Sessions</span>
                        <h3>{{ $week ? 'Week '.$week->week_number.' sessions' : 'Choose a week' }}</h3>
                        <p>{{ $week ? $week->title : 'Pick a week on the journey rail above.' }}</p>
                    </div>

                    <div class="lw-list">
                        @forelse (($week?->sessions ?? collect()) as $s)
                            @php
                                $sItems = $s->items;
                                $sDone = $sItems->filter(fn ($i) => isset($this->completedMap[(int) $i->id]))->count();
                                $isCurrentSession = $session && (int) $session->id === (int) $s->id;
                                $pct = $sItems->count() ? (int) round($sDone / $sItems->count() * 100) : 0;
                            @endphp
                            <button type="button" wire:click="selectSession({{ $s->id }})"
                                class="lw-row {{ $isCurrentSession ? 'is-active' : '' }}">
                                <div class="lw-row-top">
                                    <strong>Session {{ $s->session_number }}: {{ $s->title }}</strong>
                                    @if ($isCurrentSession)
                                        <span class="lw-badge b-active">Open</span>
                                    @endif
                                </div>
                                <div class="lw-row-sub">{{ $sItems->count() }} item{{ $sItems->count() === 1 ? '' : 's' }} · {{ $sDone }} done</div>
                                <div class="lw-tiny-progress"><i style="width: {{ $pct }}%"></i></div>
                            </button>
                        @empty
                            <div class="lw-empty">No sessions yet for this week.</div>
                        @endforelse
                    </div>
                </div>

                <div class="lw-panel lw-panel-pad">
                    <div class="lw-panel-head">
                        <span class="lw-eyebrow">Step 2 · Lesson items</span>
                        <h3>{{ $session ? 'Session '.$session->session_number.' items' : 'Choose a session' }}</h3>
                        <p>{{ $session ? 'Intro, video, task, or quiz — pick one to open on the right.' : 'Pick a session above first.' }}</p>
                    </div>

                    <div class="lw-list">
                        @forelse ($sessionItems as $i)
                            @php
                                $isDone = isset($this->completedMap[(int) $i->id]);
                                $isNextItem = $this->nextPendingItemId !== null && (int) $i->id === (int) $this->nextPendingItemId;
                                $isCurrentItem = $item && (int) $item->id === (int) $i->id;
                                $isQuiz = $i->item_type === CourseSessionItem::TYPE_QUIZ;
                            @endphp
                            <button type="button" wire:click="selectItem({{ $i->id }})"
                                class="lw-row {{ $isCurrentItem ? 'is-active' : '' }} {{ $isDone ? 'is-done' : '' }}">
                                <div class="lw-row-top">
                                    <strong>{{ $loop->iteration }}. {{ $i->title }}</strong>
                                    @if ($isCurrentItem)
                                        <span class="lw-badge b-active">Viewing</span>
                                    @elseif ($isDone)
                                        <span class="lw-badge b-done">Done</span>
                                    @elseif ($isNextItem)
                                        <span class="lw-badge b-next">Next</span>
                                    @elseif ($isQuiz && $i->is_live)
                                        <span class="lw-badge b-live">Live</span>
                                    @endif
                                </div>
                                <div class="lw-row-sub">{{ ucwords(str_replace('_', ' ', $i->item_type)) }}</div>
                            </button>
                        @empty
                            <div class="lw-empty">No lesson items yet for this session.</div>
                        @endforelse
                    </div>
                </div>
            </aside>

            {{-- ---- Main viewer ---- --}}
            <div wire:loading.class="lw-busy" wire:target="selectWeek,selectSession,selectItem,goToNextItem,goToPreviousItem,continueNextPending">
                @if ($item && $session && $week)
                    @php
                        $tone = 'ready';
                        $label = 'Ready to open';
                        if ($this->selectedIsCompleted) {
                            $tone = 'done';
                            $label = $this->selectedIsTask ? 'Task submitted' : ($this->selectedIsQuiz ? 'Quiz answered' : 'Completed');
                        } elseif ($this->selectedIsNext) {
                            $tone = 'next'; $label = 'Up next';
                        } elseif ($this->selectedIsQuiz && $item->is_live) {
                            $tone = 'live'; $label = 'Live quiz';
                        }
                        $submission = $this->selectedSubmission;
                    @endphp

                    <section class="lw-panel lw-viewer">
                        <div class="lw-viewer-head">
                            <div>
                                <div class="lw-crumb">
                                    <span>Week {{ $week->week_number }}</span> · <span>Session {{ $session->session_number }}</span> · <b>{{ $this->selectedTypeLabel }}</b>
                                </div>
                                <h2>{{ $item->title }}</h2>
                            </div>
                            <div class="lw-actions-row">
                                @if ($this->selectedPreviousItem)
                                    <button type="button" class="lw-btn lw-btn-soft" wire:click="goToPreviousItem">&larr; Previous</button>
                                @endif
                                @if ($this->selectedNextItem)
                                    <button type="button" class="lw-btn lw-btn-solid" wire:click="goToNextItem">Next &rarr;</button>
                                @elseif ($this->nextPendingItemId && ! $this->selectedIsCompleted)
                                    <button type="button" class="lw-btn lw-btn-solid" wire:click="continueNextPending">Go to next pending</button>
                                @endif
                            </div>
                        </div>

                        <div class="lw-stat-row">
                            <div class="lw-stat">
                                <span>Preview mode</span>
                                <strong>{{ $this->selectedPreviewLabel }}</strong>
                                <p>{{ $this->selectedResourceLabel }} source</p>
                            </div>
                            <div class="lw-stat">
                                <span>Access</span>
                                <strong>{{ $this->selectedAccessLabel }}</strong>
                                <p>
                                    @if ($this->selectedHasPrivateAsset) Protected, view-only
                                    @elseif ($item->resource_url) Opens externally
                                    @else No extra step needed @endif
                                </p>
                            </div>
                            <div class="lw-stat">
                                <span>Session progress</span>
                                <strong>{{ $this->selectedSessionProgress }}%</strong>
                                <p>{{ $this->selectedSessionCompleted }}/{{ $sessionItems->count() }} items · {{ $this->selectedSessionRemaining }} left</p>
                            </div>
                        </div>

                        @if ($this->selectedCanUseReadAloud)
                            <div class="lw-tools" data-read-aloud
                                data-read-aloud-selectors="{{ implode(', ', $this->selectedReadAloudSelectors) }}"
                                data-read-aloud-ready="Use your browser voice to listen to the visible lesson text."
                                data-read-aloud-empty="Content is still loading or has no readable text yet.">
                                <div class="lw-tools-head">
                                    <strong>Read aloud</strong>
                                    <span data-read-aloud-status aria-live="polite">Listen to this lesson instead of reading it.</span>
                                </div>
                                <div class="lw-tools-grid">
                                    <label class="lw-field"><span>Voice</span><select data-read-aloud-voice></select></label>
                                    <label class="lw-field"><span>Speed</span>
                                        <select data-read-aloud-rate>
                                            <option value="0.85">Slow</option>
                                            <option value="1" selected>Normal</option>
                                            <option value="1.15">Fast</option>
                                            <option value="1.3">Faster</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lw-actions-row">
                                    <button type="button" class="lw-btn lw-btn-soft" data-read-aloud-play>Speak</button>
                                    <button type="button" class="lw-btn lw-btn-soft" data-read-aloud-pause disabled>Pause</button>
                                    <button type="button" class="lw-btn lw-btn-soft" data-read-aloud-resume disabled>Resume</button>
                                    <button type="button" class="lw-btn lw-btn-soft" data-read-aloud-stop disabled>Stop</button>
                                </div>
                            </div>
                        @endif

                        @if ($this->selectedCanPreviewVideo)
                            <div class="lw-frame">
                                <video controls controlsList="nodownload noplaybackrate" preload="metadata">
                                    <source src="{{ $this->selectedStreamUrl }}">
                                    Your browser does not support secure video playback.
                                </video>
                            </div>
                        @elseif ($this->selectedCanPreviewPdf)
                            <div class="lw-frame">
                                <iframe src="{{ $this->selectedStreamUrl }}#toolbar=0&navpanes=0&scrollbar=1" title="{{ $item->title }}"></iframe>
                            </div>
                        @elseif ($this->selectedCanPreviewDocx)
                            <div class="lw-docx" id="lesson-read-aloud-docx" data-docx-stream="{{ $this->selectedStreamUrl }}">
                                <div class="lw-docx-status" data-docx-status>Loading DOCX preview…</div>
                            </div>
                        @elseif ($this->selectedCanPreviewPptx)
                            <div class="lw-pptx" id="lesson-read-aloud-pptx" data-pptx-stream="{{ $this->selectedStreamUrl }}">
                                <div class="lw-pptx-status" data-pptx-status>Loading PPTX slides…</div>
                            </div>
                        @elseif ($this->selectedCanPreviewOffice)
                            <div class="lw-frame">
                                <iframe src="{{ $this->selectedEmbeddedViewerUrl }}" title="{{ $item->title }}" loading="lazy"></iframe>
                            </div>
                        @elseif ($item->resource_url)
                            <div class="lw-hint">
                                <strong>External resource</strong>
                                <p>This lesson opens from an outside link. Use the button below to continue.</p>
                            </div>
                        @elseif ($this->selectedHasPrivateAsset)
                            <div class="lw-hint">
                                <strong>Secure file attached</strong>
                                <p>This file is protected. Open it safely with the button below.</p>
                            </div>
                        @elseif (! $this->selectedIsTask && ! $this->selectedIsQuiz)
                            <div class="lw-hint">
                                <strong>No preview attached</strong>
                                <p>This lesson item does not have a video or document yet.</p>
                            </div>
                        @endif

                        @if ($item->content)
                            <div class="lw-notes">
                                <div class="lw-notes-head">
                                    <div>
                                        <span class="lw-eyebrow">{{ $this->selectedIsTask ? 'Task brief' : ($this->selectedIsQuiz ? 'Quiz notes' : 'Lesson notes') }}</span>
                                    </div>
                                    <span class="lw-badge">{{ $this->selectedResourceLabel }}</span>
                                </div>
                                <div class="lw-notes-body" id="lesson-read-aloud-notes">{{ $item->content }}</div>
                            </div>
                        @endif

                        <div class="lw-actions-row">
                            @if ($this->selectedHasPrivateAsset)
                                <a href="{{ $this->selectedViewerUrl }}" class="lw-btn lw-btn-solid">Open secure viewer</a>
                            @endif
                            @if ($item->resource_url)
                                <a href="{{ $item->resource_url }}" target="_blank" rel="noopener" class="lw-btn lw-btn-soft">Open external link</a>
                            @endif
                            @if ($this->selectedDownloadUrl)
                                <a href="{{ $this->selectedDownloadUrl }}" class="lw-btn lw-btn-soft">Download brief</a>
                            @endif
                        </div>

                        {{-- Task submission --}}
                        @if ($this->selectedIsTask)
                            <div class="lw-submit-box">
                                <strong>Submit your task file</strong>
                                <form wire:submit="submitTask">
                                    <div class="lw-field">
                                        <input type="file" wire:model="submissionFile">
                                        @error('submissionFile') <span class="lw-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div wire:loading wire:target="submissionFile" style="font-size:12px;color:var(--muted);margin-top:6px;">Uploading…</div>
                                    <button type="submit" class="lw-btn lw-btn-soft" style="margin-top:10px;" wire:loading.attr="disabled" wire:target="submitTask">
                                        Submit task
                                    </button>
                                </form>
                                @if ($submission)
                                    <div class="lw-submit-meta">
                                        <span>Last submitted {{ optional($submission->submitted_at)->diffForHumans() }}</span>
                                        <span>Review status: {{ $submission->reviewStatusLabel() }}</span>
                                        @if ($submission->file_name)
                                            <span>Latest file: {{ $submission->file_name }}</span>
                                        @endif
                                        @if ($submission->file_path)
                                            <a href="{{ route('course-item-submissions.download', $submission) }}">Download your submission</a>
                                        @endif
                                        @if ($submission->review_notes)
                                            <div class="lw-answer-echo">{{ \Illuminate\Support\Str::limit($submission->review_notes, 220) }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Quiz submission --}}
                        @if ($this->selectedIsQuiz)
                            <div class="lw-submit-box">
                                <strong>Submit your quiz answer</strong>
                                @if (! $item->is_live)
                                    <p style="margin:0;font-size:12.5px;color:var(--muted);">This quiz is not live yet — your trainer must open it first.</p>
                                @else
                                    <form wire:submit="submitQuiz">
                                        <div class="lw-field">
                                            <textarea wire:model="answerText" placeholder="Type your answer here…"></textarea>
                                            @error('answerText') <span class="lw-error">{{ $message }}</span> @enderror
                                        </div>
                                        <button type="submit" class="lw-btn lw-btn-soft" style="margin-top:10px;" wire:loading.attr="disabled" wire:target="submitQuiz">
                                            Submit quiz
                                        </button>
                                    </form>
                                @endif

                                @if ($submission)
                                    <div class="lw-submit-meta">
                                        <span>Last answer submitted {{ optional($submission->submitted_at)->diffForHumans() }}</span>
                                        <span>Review status: {{ $submission->reviewStatusLabel() }}</span>
                                        @if ($submission->answer_text)
                                            <div class="lw-answer-echo">{{ \Illuminate\Support\Str::limit($submission->answer_text, 220) }}</div>
                                        @endif
                                        @if ($submission->review_notes)
                                            <div class="lw-answer-echo">{{ \Illuminate\Support\Str::limit($submission->review_notes, 220) }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </section>
                @else
                    <div class="lw-panel lw-panel-pad lw-hint" style="border:1px solid var(--line);">
                        <strong>Start from the journey rail</strong>
                        <p>Choose a week, then a session, then one lesson item — it opens right here.</p>
                    </div>
                @endif

                {{-- Support strip --}}
                <div class="lw-support-grid" style="margin-top: 16px;">
                    <div class="lw-support-card">
                        <span>Current focus</span>
                        <strong>{{ $item?->title ?? ($session?->title ?? ($week?->title ?? 'Choose a step')) }}</strong>
                        <p>@if ($item) Session {{ $session->session_number }} of Week {{ $week->week_number }} @else Pick a week to begin @endif</p>
                    </div>
                    <div class="lw-support-card">
                        <span>Session progress</span>
                        <strong>{{ $this->selectedSessionProgress }}%</strong>
                        <p>{{ $this->selectedSessionCompleted }}/{{ $sessionItems->count() }} completed</p>
                    </div>
                    <div class="lw-support-card accent">
                        <span>Next step</span>
                        <strong>{{ $this->selectedNextItem?->title ?? 'Keep the momentum' }}</strong>
                        <p>@if ($this->selectedNextItem) Continue right after this lesson @else Use the rail or sidebar to pick your next move @endif</p>
                    </div>
                    <div class="lw-support-card">
                        <span>Trainer support</span>
                        <strong>{{ $enrollment->trainer?->name ?? 'Not assigned' }}</strong>
                        <p>{{ $this->completedSessionsCount }}/{{ $this->allSessions->count() }} sessions fully done</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($this->selectedCanPreviewPptx)
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script type="module" src="{{ asset('js/secure-pptx-viewer.js') }}"></script>
    @endif
    @if ($this->selectedCanPreviewDocx)
        <script src="{{ asset('js/jszip.min.js') }}" defer></script>
        <script src="{{ asset('js/docx-preview.min.js') }}" defer></script>
        <script src="{{ asset('js/mammoth.browser.min.js') }}" defer></script>
        <script src="{{ asset('js/secure-docx-viewer.js') }}" defer></script>
    @endif
    @if ($this->selectedCanUseReadAloud)
        <script src="{{ asset('js/secure-read-aloud.js') }}" defer></script>
    @endif
</div>