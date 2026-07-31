<div class="cba {{ $courseId ? 'cba--section' : 'cba--page' }}" data-theme-scope>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap');

        /* ===================================================
           TOKENS — inherits app palette, adds component-scoped
           extras (prefixed cba-*) so nothing collides globally.
        =================================================== */
        .cba {
            --cba-font-display: 'Sora', 'Inter', system-ui, sans-serif;
            --cba-font-mono: 'IBM Plex Mono', 'SFMono-Regular', Menlo, monospace;

            --cba-chip-bg: var(--bg-card2, #f8fbff);
            --cba-chip-border: var(--line, #d6e4f5);
            --cba-link-color: var(--brand-secondary, #7a5cff);

            color: var(--text, #0e1f36);
            font-family: 'Inter', system-ui, sans-serif;
        }

        .cba *, .cba *::before, .cba *::after { box-sizing: border-box; }

        .cba :focus-visible {
            outline: 2px solid var(--brand-primary, #0947a8);
            outline-offset: 2px;
            border-radius: 4px;
        }

        @media (prefers-reduced-motion: reduce) {
            .cba * { animation-duration: .001ms !important; transition-duration: .001ms !important; }
        }

        /* ===================================================
           SHARED PRIMITIVES
        =================================================== */
        .cba-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--cba-font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--brand-secondary, #7a5cff);
        }

        .cba-eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--brand-accent, #f0b35a);
            flex-shrink: 0;
        }

        .cba-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: var(--radius-sm, 12px);
            font-weight: 600;
            font-size: 14px;
            padding: 11px 20px;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .2s ease;
            white-space: nowrap;
        }

        .cba-btn--primary {
            background: linear-gradient(135deg, var(--brand-primary, #0947a8), var(--brand-secondary, #7a5cff));
            color: #fff;
            box-shadow: 0 10px 24px -8px rgba(9, 71, 168, .45);
        }
        .cba-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 14px 28px -8px rgba(9, 71, 168, .55); }
        .cba-btn--primary:active { transform: translateY(0); }

        .cba-btn--ghost {
            background: transparent;
            color: var(--text-muted, #5a718a);
            border: 1px solid var(--line, #d6e4f5);
        }
        .cba-btn--ghost:hover { background: var(--bg-card2, #f8fbff); color: var(--text, #0e1f36); }

        .cba-icon-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid var(--line, #d6e4f5);
            background: var(--bg-card, #fff);
            color: var(--text-muted, #5a718a);
            cursor: pointer;
            transition: .15s ease;
        }
        .cba-icon-btn:hover { transform: translateY(-1px); }
        .cba-icon-btn--edit:hover { color: var(--info, #0284c7); border-color: var(--info, #0284c7); }
        .cba-icon-btn--danger:hover { color: var(--danger, #dc2626); border-color: var(--danger, #dc2626); }

        .cba-field { display: flex; flex-direction: column; gap: 6px; }
        .cba-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text, #0e1f36);
        }
        .cba-hint { font-size: 12px; color: var(--danger, #dc2626); }

        .cba-control-wrap { position: relative; }

        .cba-input,
        .cba-select {
            width: 100%;
            appearance: none;
            background: var(--input-bg, #f4f8ff);
            border: 1px solid var(--input-border, #c8daf0);
            color: var(--text, #0e1f36);
            border-radius: var(--radius-sm, 12px);
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            min-height: 42px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .cba-select { padding-right: 36px; }
        .cba-input:focus, .cba-select:focus {
            border-color: var(--brand-primary, #0947a8);
            box-shadow: 0 0 0 3px rgba(9, 71, 168, .12);
            outline: none;
        }
        .cba-control-wrap .cba-chevron {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--text-muted, #5a718a);
            font-size: 12px;
        }

        .cba-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 600;
        }
        .cba-status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .cba-status-pill--active { background: #dcfce7; color: #166534; }
        .cba-status-pill--active::before { background: #16a34a; }
        .cba-status-pill--inactive { background: #fee2e2; color: #991b1b; }
        .cba-status-pill--inactive::before { background: #dc2626; }
        [data-theme="dark"] .cba-status-pill--active { background: rgba(22,163,74,.15); color: #4ade80; }
        [data-theme="dark"] .cba-status-pill--inactive { background: rgba(220,38,38,.15); color: #f87171; }

        .cba-alert {
            border-radius: var(--radius-sm, 12px);
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            background: #dcfce7;
            color: #166534;
            border: 1px solid rgba(22,163,74,.25);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        [data-theme="dark"] .cba-alert { background: rgba(22,163,74,.12); color: #4ade80; }

        /* ===================================================
           SIGNATURE ELEMENT — the "chain":
           Course · Batch · Trainer rendered as linked nodes,
           since an assignment literally IS that link.
        =================================================== */
        .cba-chain {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0;
        }
        .cba-chain__node {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--cba-chip-bg);
            border: 1px solid var(--cba-chip-border);
            border-radius: 10px;
            padding: 6px 11px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text, #0e1f36);
        }
        .cba-chain__node--course { border-color: rgba(9,71,168,.35); }
        .cba-chain__node--course .cba-chain__dot { background: var(--brand-primary, #0947a8); }
        .cba-chain__node--batch { border-color: rgba(122,92,255,.35); font-family: var(--cba-font-mono); }
        .cba-chain__node--batch .cba-chain__dot { background: var(--brand-secondary, #7a5cff); }
        .cba-chain__node--trainer { border-color: rgba(240,179,90,.5); }
        .cba-chain__node--trainer .cba-chain__dot { background: var(--brand-accent, #f0b35a); }
        .cba-chain__dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .cba-chain__type {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--text-muted, #5a718a);
            font-family: 'Inter', sans-serif;
        }
        .cba-chain__link {
            width: 18px;
            height: 1px;
            background: repeating-linear-gradient(90deg, var(--line, #d6e4f5) 0 4px, transparent 4px 7px);
            flex-shrink: 0;
        }

        /* ===================================================
           PAGE MODE — standalone admin index
        =================================================== */
        .cba--page .cba-hero {
            background: var(--bg-card, #fff);
            border: 1px solid var(--line, #d6e4f5);
            border-radius: var(--radius, 20px);
            box-shadow: var(--shadow, 0 20px 50px rgba(13,93,209,.08));
            padding: 32px 32px 28px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .cba--page .cba-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(9,71,168,.06), rgba(122,92,255,.05) 60%, transparent);
            pointer-events: none;
        }
        .cba-hero__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
        }
        .cba-hero__heading { display: flex; flex-direction: column; gap: 10px; }
        .cba-hero h1 {
            font-family: var(--cba-font-display);
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -.01em;
        }
        .cba-hero__sub {
            color: var(--text-muted, #5a718a);
            font-size: 14.5px;
            max-width: 46ch;
        }

        .cba-stats {
            display: flex;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap;
            position: relative;
        }
        .cba-stat {
            display: flex;
            align-items: baseline;
            gap: 8px;
            background: var(--bg-card2, #f8fbff);
            border: 1px solid var(--line, #d6e4f5);
            border-radius: var(--radius-sm, 12px);
            padding: 10px 16px;
        }
        .cba-stat strong {
            font-family: var(--cba-font-display);
            font-size: 20px;
            font-weight: 700;
        }
        .cba-stat span {
            font-size: 12.5px;
            color: var(--text-muted, #5a718a);
            font-weight: 600;
        }
        .cba-stat--active strong { color: var(--success, #16a34a); }
        .cba-stat--inactive strong { color: var(--danger, #dc2626); }

        .cba-toolbar {
            display: grid;
            grid-template-columns: minmax(200px, 320px) minmax(200px, 320px);
            gap: 16px;
            background: var(--bg-card, #fff);
            border: 1px solid var(--line, #d6e4f5);
            border-radius: var(--radius, 20px);
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm, 0 2px 8px rgba(14,31,54,.06));
        }

        /* ===================================================
           SECTION MODE — compact card embedded in Course page
        =================================================== */
        .cba--section .cba-panel-card {
            background: var(--bg-card, #fff);
            border: 1px solid var(--line, #d6e4f5);
            border-radius: var(--radius, 20px);
            box-shadow: var(--shadow-sm, 0 2px 8px rgba(14,31,54,.06));
            overflow: hidden;
        }
        .cba--section .cba-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 20px 22px;
            border-bottom: 1px solid var(--line, #d6e4f5);
            background: var(--bg-card2, #f8fbff);
        }
        .cba--section .cba-panel-head h2 {
            font-family: var(--cba-font-display);
            font-size: 18px;
            font-weight: 700;
            margin-top: 4px;
        }
        .cba--section .cba-panel-body { padding: 18px 22px 22px; }

        /* ===================================================
           TABLE — shared, styling differs slightly by density
        =================================================== */
        .cba-table-wrap {
            overflow-x: auto;
            border-radius: var(--radius, 20px);
            border: 1px solid var(--line, #d6e4f5);
            background: var(--bg-card, #fff);
        }
        .cba--section .cba-table-wrap { border-radius: var(--radius-sm, 12px); }

        .cba-table { width: 100%; border-collapse: collapse; min-width: 640px; }
        .cba-table thead th {
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted, #5a718a);
            background: var(--bg-card2, #f8fbff);
            padding: 13px 18px;
            border-bottom: 1px solid var(--line, #d6e4f5);
        }
        .cba-table tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid var(--line, #d6e4f5);
            font-size: 14px;
            vertical-align: middle;
        }
        .cba-table tbody tr:last-child td { border-bottom: none; }
        .cba-table tbody tr { transition: background .15s ease; }
        .cba-table tbody tr:hover { background: rgba(9, 71, 168, .035); }
        .cba-table .cba-col-index { color: var(--text-muted, #5a718a); font-weight: 600; width: 44px; }
        .cba-table .cba-col-actions { width: 110px; }
        .cba-row-actions { display: flex; gap: 8px; }

        /* ===================================================
           EMPTY STATE
        =================================================== */
        .cba-empty {
            padding: 64px 24px;
            text-align: center;
        }
        .cba-empty .cba-empty-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 16px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-card2, #f8fbff);
            border: 1px solid var(--line, #d6e4f5);
            color: var(--brand-primary, #0947a8);
            font-size: 20px;
        }
        .cba-empty h3 {
            font-family: var(--cba-font-display);
            font-size: 16px;
            margin-bottom: 4px;
        }
        .cba-empty p { color: var(--text-muted, #5a718a); font-size: 13.5px; }

        /* ===================================================
           MODAL — custom dialog, no bootstrap
        =================================================== */
        .cba-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 17, 31, .5);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1050;
            animation: cba-fade .18s ease;
        }
        @keyframes cba-fade { from { opacity: 0 } to { opacity: 1 } }
        @keyframes cba-rise { from { opacity: 0; transform: translateY(10px) scale(.98) } to { opacity: 1; transform: translateY(0) scale(1) } }

        .cba-dialog {
            width: 100%;
            max-width: 460px;
            background: var(--bg-card, #fff);
            border: 1px solid var(--line, #d6e4f5);
            border-radius: var(--radius, 20px);
            box-shadow: var(--shadow, 0 20px 50px rgba(13,93,209,.08));
            animation: cba-rise .2s cubic-bezier(.2,.7,.3,1);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        .cba-dialog__head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--line, #d6e4f5);
        }
        .cba-dialog__head h3 {
            font-family: var(--cba-font-display);
            font-size: 17px;
            font-weight: 700;
        }
        .cba-dialog__close {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--text-muted, #5a718a);
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .cba-dialog__close:hover { background: var(--bg-card2, #f8fbff); color: var(--text, #0e1f36); }
        .cba-dialog__body {
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
        }
        .cba-dialog__foot {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 18px 24px;
            border-top: 1px solid var(--line, #d6e4f5);
        }

        /* ===================================================
           RESPONSIVE
        =================================================== */
        @media (max-width: 768px) {
            .cba--page .cba-hero { padding: 24px 20px; }
            .cba-hero__top { flex-direction: column; }
            .cba-hero .cba-btn--primary { width: 100%; }
            .cba-toolbar { grid-template-columns: 1fr; padding: 16px; }
            .cba-stats { width: 100%; }
            .cba-stat { flex: 1; justify-content: center; }
        }
        @media (max-width: 560px) {
            .cba-dialog { max-width: 100%; }
        }
    </style>

    {{-- =========================================================
         PAGE MODE — no course context: full standalone admin page
    ========================================================== --}}
    @if (!$courseId)

        <div class="cba-hero mt-4">
            <div class="cba-hero__top">
                <div class="cba-hero__heading">
                    <span class="cba-eyebrow">Training Operations</span>
                    <h1>Course Batch Assignments</h1>
                    <p class="cba-hero__sub">Link every batch to its course and assign the trainer running it.</p>
                </div>

                <button type="button" class="cba-btn cba-btn--primary" wire:click="create">
                    <i class="ti ti-plus"></i>
                    Assign Batch
                </button>
            </div>

            <div class="cba-stats">
                <div class="cba-stat">
                    <strong>{{ $assignments->count() }}</strong>
                    <span>Total</span>
                </div>
                <div class="cba-stat cba-stat--active">
                    <strong>{{ $assignments->where('status', 'active')->count() }}</strong>
                    <span>Active</span>
                </div>
                <div class="cba-stat cba-stat--inactive">
                    <strong>{{ $assignments->where('status', 'inactive')->count() }}</strong>
                    <span>Inactive</span>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="cba-alert"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
        @endif

        <div class="cba-toolbar">
            <div class="cba-field">
                <label class="cba-label" for="cba-filter-course">Course</label>
                <div class="cba-control-wrap">
                    <select id="cba-filter-course" class="cba-select" wire:model.live="course_id">
                        <option value="">All courses</option>
                        @foreach ($courses as $courseOption)
                            <option value="{{ $courseOption->id }}">{{ $courseOption->title }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down cba-chevron"></i>
                </div>
                @error('course_id') <span class="cba-hint">{{ $message }}</span> @enderror
            </div>

            <div class="cba-field">
                <label class="cba-label" for="cba-filter-search">Search batch</label>
                <input id="cba-filter-search" type="text" class="cba-input" placeholder="Batch code…" wire:model.live="search">
            </div>
        </div>

        <div class="cba-table-wrap">
            <table class="cba-table">
                <thead>
                    <tr>
                        <th class="cba-col-index">#</th>
                        <th>Assignment</th>
                        <th>Status</th>
                        <th class="cba-col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr>
                            <td class="cba-col-index">{{ $loop->iteration }}</td>
                            <td>
                                <div class="cba-chain">
                                    <span class="cba-chain__node cba-chain__node--course">
                                        <span class="cba-chain__dot"></span>
                                        {{ $assignment->course?->title }}
                                    </span>
                                    <span class="cba-chain__link"></span>
                                    <span class="cba-chain__node cba-chain__node--batch">
                                        <span class="cba-chain__dot"></span>
                                        {{ $assignment->batch?->batch_code }}
                                        <span class="cba-chain__type">{{ ucfirst($assignment->batch?->batch_type) }}</span>
                                    </span>
                                    <span class="cba-chain__link"></span>
                                    <span class="cba-chain__node cba-chain__node--trainer">
                                        <span class="cba-chain__dot"></span>
                                        {{ $assignment->trainer?->name }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if ($assignment->status == 'active')
                                    <span class="cba-status-pill cba-status-pill--active">Active</span>
                                @else
                                    <span class="cba-status-pill cba-status-pill--inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="cba-row-actions">
                                    <button type="button" class="cba-icon-btn cba-icon-btn--edit" title="Edit" wire:click="edit({{ $assignment->id }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" class="cba-icon-btn cba-icon-btn--danger" title="Delete" wire:click="delete({{ $assignment->id }})">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="cba-empty">
                                    <div class="cba-empty-icon"><i class="ti ti-link-off"></i></div>
                                    <h3>No batches assigned yet</h3>
                                    <p>Assign a batch and trainer to a course to see it here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else

    {{-- =========================================================
         SECTION MODE — embedded inside the Course Details page
    ========================================================== --}}

        <div class="cba-panel-card">
            <div class="cba-panel-head">
                <div>
                    <span class="cba-eyebrow">Linked batches</span>
                    <h2>{{ $course?->title }}</h2>
                </div>
                <button type="button" class="cba-btn cba-btn--primary" wire:click="create">
                    <i class="ti ti-plus"></i>
                    Assign Batch
                </button>
            </div>

            <div class="cba-panel-body">
                @if (session()->has('success'))
                    <div class="cba-alert"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
                @endif

                <div class="cba-table-wrap">
                    <table class="cba-table">
                        <thead>
                            <tr>
                                <th class="cba-col-index">#</th>
                                <th>Assignment</th>
                                <th>Status</th>
                                <th class="cba-col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr>
                                    <td class="cba-col-index">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="cba-chain">
                                            <span class="cba-chain__node cba-chain__node--batch">
                                                <span class="cba-chain__dot"></span>
                                                {{ $assignment->batch?->batch_code }}
                                                <span class="cba-chain__type">{{ ucfirst($assignment->batch?->batch_type) }}</span>
                                            </span>
                                            <span class="cba-chain__link"></span>
                                            <span class="cba-chain__node cba-chain__node--trainer">
                                                <span class="cba-chain__dot"></span>
                                                {{ $assignment->trainer?->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($assignment->status == 'active')
                                            <span class="cba-status-pill cba-status-pill--active">Active</span>
                                        @else
                                            <span class="cba-status-pill cba-status-pill--inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="cba-row-actions">
                                            <button type="button" class="cba-icon-btn cba-icon-btn--edit" title="Edit" wire:click="edit({{ $assignment->id }})">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button type="button" class="cba-icon-btn cba-icon-btn--danger" title="Delete" wire:click="delete({{ $assignment->id }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="cba-empty">
                                            <div class="cba-empty-icon"><i class="ti ti-link-off"></i></div>
                                            <h3>No batches assigned</h3>
                                            <p>Assign a batch and trainer to this course.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endif

    {{-- =========================================================
         ASSIGN / EDIT DIALOG — shared by both modes
    ========================================================== --}}
    @if ($showModal)
        <div class="cba-overlay" wire:click.self="closeModal">
            <div class="cba-dialog" role="dialog" aria-modal="true">
                <div class="cba-dialog__head">
                    <h3>{{ $editingId ? 'Update batch assignment' : 'Assign batch' }}</h3>
                    <button type="button" class="cba-dialog__close" wire:click="closeModal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <div class="cba-dialog__body">
                    @if (!$courseId)
                        <div class="cba-field">
                            <label class="cba-label" for="cba-form-course">Course</label>
                            <div class="cba-control-wrap">
                                <select id="cba-form-course" class="cba-select" wire:model="course_id">
                                    <option value="">Select course</option>
                                    @foreach ($courses as $courseOption)
                                        <option value="{{ $courseOption->id }}">{{ $courseOption->title }}</option>
                                    @endforeach
                                </select>
                                <i class="ti ti-chevron-down cba-chevron"></i>
                            </div>
                            @error('course_id') <span class="cba-hint">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="cba-field">
                        <label class="cba-label" for="cba-form-batch">Batch</label>
                        <div class="cba-control-wrap">
                            <select id="cba-form-batch" class="cba-select" wire:model="batch_id">
                                <option value="">Select batch</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->batch_code }} — {{ ucfirst($batch->batch_type) }}</option>
                                @endforeach
                            </select>
                            <i class="ti ti-chevron-down cba-chevron"></i>
                        </div>
                        @error('batch_id') <span class="cba-hint">{{ $message }}</span> @enderror
                    </div>

                    <div class="cba-field">
                        <label class="cba-label" for="cba-form-trainer">Trainer</label>
                        <div class="cba-control-wrap">
                            <select id="cba-form-trainer" class="cba-select" wire:model="trainer_id">
                                <option value="">Select trainer</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                            <i class="ti ti-chevron-down cba-chevron"></i>
                        </div>
                        @error('trainer_id') <span class="cba-hint">{{ $message }}</span> @enderror
                    </div>

                    <div class="cba-field">
                        <label class="cba-label" for="cba-form-status">Status</label>
                        <div class="cba-control-wrap">
                            <select id="cba-form-status" class="cba-select" wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <i class="ti ti-chevron-down cba-chevron"></i>
                        </div>
                    </div>
                </div>

                <div class="cba-dialog__foot">
                    <button type="button" class="cba-btn cba-btn--ghost" wire:click="closeModal">Cancel</button>
                    <button type="button" class="cba-btn cba-btn--primary" wire:click="save">
                        {{ $editingId ? 'Update' : 'Assign' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
@push('scripts')

<script>

document.addEventListener('livewire:init', () => {


    /*
    |--------------------------------------------------------------------------
    | SweetAlert Success Toast
    |--------------------------------------------------------------------------
    */

    Livewire.on('swal', (event) => {


        Swal.fire({

            toast: true,

            position: 'top-end',

            icon: event.icon ?? 'success',

            title: event.title ?? 'Success',

            text: event.text ?? '',

            showConfirmButton: false,

            timer: 2500,

            timerProgressBar: true

        });


    });




    /*
    |--------------------------------------------------------------------------
    | Delete Confirmation
    |--------------------------------------------------------------------------
    */

    window.confirmDeleteAssignment = function(id){


        Swal.fire({

            title: "Delete Assignment?",

            text: "This batch assignment will be removed.",

            icon: "warning",

            showCancelButton: true,

            confirmButtonText: "Yes, Delete",

            cancelButtonText: "Cancel",

            confirmButtonColor: "#dc2626",

            cancelButtonColor: "#6b7280"


        }).then((result)=>{


            if(result.isConfirmed){


                Livewire.dispatch(
                    'deleteConfirmed',
                    {
                        id:id
                    }
                );


            }


        });


    }




});

</script>




@endpush
</div>