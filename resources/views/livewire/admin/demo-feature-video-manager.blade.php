<div class="reel-admin">
    <style>
        /* =====================================================
           DEMO VIDEO MANAGEMENT — "REEL" ADMIN
           Self-contained layout. No Bootstrap grid/utility
           classes anywhere — everything below is custom Grid
           and Flexbox, built on the app's existing theme tokens
           (--bg, --bg-card, --brand-primary, --text, etc.)
        ===================================================== */

        .reel-admin {
            position: relative;
            z-index: 1;
            color: var(--text);
        }

        .reel-admin * {
            box-sizing: border-box;
        }

        .reel-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .reel-mono {
            font-family: 'JetBrains Mono', 'SFMono-Regular', ui-monospace, Menlo, Consolas, monospace;
            letter-spacing: .02em;
        }

        /* ── Sprocket / perforation strip — the signature motif ── */
        .sprockets {
            height: 14px;
            background-image: radial-gradient(circle at 10px 7px, var(--bg) 3.5px, transparent 4px);
            background-size: 20px 14px;
            background-repeat: repeat-x;
            background-color: var(--bg2);
            flex-shrink: 0;
        }

        [data-theme="dark"] .sprockets {
            background-image: radial-gradient(circle at 10px 7px, rgba(0,0,0,.55) 3.5px, transparent 4px);
        }

        /* ── Hero ── */
        .reel-hero {
            position: relative;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-left: 4px solid var(--brand-primary);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .reel-hero-inner {
            padding: 26px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .reel-hero-copy {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 560px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 16px;
            border-radius: 999px;
            background: var(--primary-glow);
            color: var(--brand-primary);
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid var(--border);
        }

        .reel-hero-title {
            color: var(--text);
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        .reel-hero-meta {
            color: var(--text-muted);
            margin: 0;
            font-size: .92rem;
            line-height: 1.5;
        }

        .theme-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: var(--radius-sm);
            border: none;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            color: #fff;
            font-weight: 600;
            font-size: .9rem;
            box-shadow: var(--shadow-sm);
            transition: transform .2s ease, box-shadow .2s ease;
            white-space: nowrap;
            cursor: pointer;
        }

        .theme-add-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            color: #fff;
        }

        /* ── Stats ── */
        .reel-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }

        .reel-stat {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform .25s ease;
            box-shadow: var(--shadow-card);
        }

        .reel-stat:hover {
            transform: translateY(-4px);
        }

        .reel-stat-value {
            color: var(--text);
            font-size: 1.7rem;
            font-weight: 700;
            margin: 0 0 2px;
        }

        .reel-stat-label {
            color: var(--text-muted);
            font-size: .85rem;
        }

        .reel-stat-icon {
            width: 54px;
            height: 54px;
            flex-shrink: 0;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
        }

        .reel-stat-icon.g { background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); }
        .reel-stat-icon.success { background: var(--success); }
        .reel-stat-icon.warning { background: var(--warning); }

        /* ── Filters ── */
        .filter-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 24px;
            box-shadow: var(--shadow-card);
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
        }

        @media (max-width: 640px) {
            .filter-card {
                grid-template-columns: 1fr;
            }
        }

        /* ── Form elements ── */
        .form-label {
            color: var(--text);
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 8px;
            display: block;
        }

        .form-control,
        .form-select {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text);
            border-radius: var(--radius-sm);
            padding: 11px 14px;
            font-size: .9rem;
            transition: border-color .2s ease, box-shadow .2s ease;
            font-family: inherit;
        }

        .form-select option:disabled { color: var(--text-muted); }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            background: var(--input-bg);
            color: var(--text);
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-control::placeholder { color: var(--text-muted); }

        .field-note {
            color: var(--text-muted);
            font-size: .78rem;
            margin-top: 6px;
        }

        .field-error {
            color: var(--danger);
            font-size: .8rem;
            display: block;
            margin-top: 6px;
        }

        /* ── Gallery card (the reel) ── */
        .reel-section {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow-card);
        }

        .reel-section-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .reel-section-title {
            color: var(--text);
            font-size: 1.15rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .reel-section-sub {
            color: var(--text-muted);
            font-size: .85rem;
            margin: 4px 0 0;
        }

        .video-count {
            background: var(--primary-glow);
            color: var(--brand-primary);
            font-weight: 600;
            font-size: .8rem;
            padding: 8px 14px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .reel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .frame {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .frame:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            border-color: var(--brand-primary);
        }

        .frame-media {
            position: relative;
            background: #000;
        }

        .frame-media video {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .frame-id {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(8, 17, 31, .72);
            color: #fff;
            font-size: .7rem;
            padding: 4px 8px;
            border-radius: var(--radius-xs);
            pointer-events: none;
        }

        .frame-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .frame-tags {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .timecode {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--brand-primary);
            color: #fff;
            font-weight: 700;
            font-size: .74rem;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .subject-tag {
            color: var(--text-muted);
            font-size: .78rem;
            border: 1px solid var(--border);
            padding: 4px 10px;
            border-radius: 999px;
        }

        .frame-title {
            color: var(--text);
            font-size: .98rem;
            font-weight: 600;
            margin: 0;
        }

        .frame-desc {
            color: var(--text-muted);
            font-size: .82rem;
            line-height: 1.5;
            margin: 0;
        }

        .frame-meta {
            color: var(--text-muted);
            font-size: .76rem;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
        }

        .frame-actions {
            display: flex;
            gap: 8px;
            padding: 0 16px 16px;
        }

        .icon-btn {
            flex: 1;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .8rem;
            padding: 9px 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: transform .15s ease, opacity .15s ease;
            cursor: pointer;
        }

        .icon-btn.edit { background: var(--warning); color: #fff; }
        .icon-btn.delete { background: var(--danger); color: #fff; }
        .icon-btn:hover { transform: translateY(-1px); opacity: .92; color: #fff; }

        /* ── Empty state ── */
        .empty-frame {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 60px 20px;
            border: 1px dashed var(--border);
            border-radius: var(--radius-sm);
            text-align: center;
        }

        .empty-frame i {
            font-size: 28px;
            color: var(--text-muted);
        }

        .empty-frame p {
            color: var(--text-muted);
            margin: 0;
            font-size: .9rem;
        }

        /* ── Pagination ── */
        .reel-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
        }

        .pg-info { color: var(--text-muted); font-size: .82rem; }

        .pg-controls {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pg-btn {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: var(--radius-xs);
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text);
            font-size: .82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
            cursor: pointer;
        }

        .pg-btn:hover:not(:disabled):not(.active) {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
        }

        .pg-btn.active { background: var(--brand-primary); border-color: var(--brand-primary); color: #fff; }
        .pg-btn:disabled { opacity: .4; cursor: not-allowed; }
        .pg-dots { color: var(--text-muted); padding: 0 4px; }

        /* ── Modal ── */
        .reel-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(8, 17, 31, .6);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 16px;
            overflow-y: auto;
            z-index: 1050;
            animation: reelFadeIn .15s ease;
        }

        .reel-modal {
            width: 100%;
            max-width: 860px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            animation: reelSlideUp .2s ease;
        }

        .reel-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .reel-modal-header h4 {
            color: var(--text);
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .reel-modal-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: var(--bg2);
            color: var(--text-muted);
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s ease, color .2s ease;
            cursor: pointer;
        }

        .reel-modal-close:hover { background: var(--danger); color: #fff; }

        .reel-modal-body {
            padding: 24px;
            max-height: 72vh;
            overflow-y: auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-grid .span-2 { grid-column: 1 / -1; }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        .reel-modal-footer {
            display: flex;
            gap: 10px;
            padding: 18px 24px;
            border-top: 1px solid var(--border);
        }

        /* ── Upload dropzone ── */
        .upload-box {
            display: block;
            cursor: pointer;
            border: 2px dashed var(--input-border);
            border-radius: var(--radius-sm);
            background: var(--input-bg);
            padding: 28px 20px;
            text-align: center;
            transition: border-color .2s ease, background .2s ease;
        }

        .upload-box:hover { border-color: var(--brand-primary); background: var(--primary-glow); }

        .upload-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            overflow: hidden;
        }

        .upload-icon { font-size: 30px; color: var(--brand-primary); margin-bottom: 10px; }
        .upload-content h5 { color: var(--text); font-size: .95rem; font-weight: 600; margin: 0 0 4px; }
        .upload-content p { color: var(--text-muted); font-size: .82rem; margin: 0 0 6px; }

        .upload-note {
            display: inline-block;
            color: var(--text-muted);
            font-size: .72rem;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .selected-file {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            text-align: left;
        }

        .selected-file i { color: var(--success); font-size: 18px; flex-shrink: 0; }
        .selected-file strong { display: block; color: var(--text); font-size: .82rem; word-break: break-all; }
        .selected-file small { color: var(--text-muted); font-size: .74rem; }

        .upload-progress { margin-top: 12px; }

        .progress-track {
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--bg2);
        }

        .progress-fill {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
        }

        /* ── Submit buttons ── */
        .theme-submit-btn,
        .btn-success,
        .btn-secondary {
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .85rem;
            padding: 11px 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .15s ease, opacity .15s ease;
            cursor: pointer;
            flex: 1;
        }

        .theme-submit-btn { background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-secondary { background: var(--bg2); color: var(--text); border: 1px solid var(--border); }

        .theme-submit-btn:hover, .btn-success:hover { transform: translateY(-1px); opacity: .92; color: #fff; }
        .btn-secondary:hover { transform: translateY(-1px); color: var(--text); }

        .theme-submit-btn:disabled, .btn-success:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        @keyframes reelFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes reelSlideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .reel-hero-inner { padding: 20px; }
            .theme-add-btn { justify-content: center; width: 100%; }
            .reel-hero-title { font-size: 1.4rem; }
            .reel-stat { padding: 16px; }
            .filter-card, .reel-section { padding: 16px; }
            .reel-pagination { justify-content: center; text-align: center; }

            .reel-modal-backdrop { padding: 0; align-items: flex-end; }
            .reel-modal {
                max-width: 100%;
                border-radius: var(--radius) var(--radius) 0 0;
                max-height: 92vh;
                display: flex;
                flex-direction: column;
            }
            .reel-modal-body { max-height: none; flex: 1; }
        }

        @media (max-width: 480px) {
            .reel-modal-footer { flex-direction: column; }
        }

        @media (prefers-reduced-motion: reduce) {
            .frame, .reel-stat, .theme-add-btn, .pg-btn, .icon-btn, .theme-submit-btn, .btn-success, .btn-secondary {
                transition: none;
            }
            .reel-modal-backdrop, .reel-modal { animation: none; }
        }

        /* Visible keyboard focus */
        .theme-add-btn:focus-visible,
        .icon-btn:focus-visible,
        .pg-btn:focus-visible,
        .theme-submit-btn:focus-visible,
        .btn-success:focus-visible,
        .btn-secondary:focus-visible,
        .reel-modal-close:focus-visible,
        .form-control:focus-visible,
        .form-select:focus-visible {
            outline: 2px solid var(--brand-primary);
            outline-offset: 2px;
        }
    </style>

    <div class="reel-wrap">

        {{-- Hero --}}
        <div class="reel-hero">
            <div class="sprockets"></div>
            <div class="reel-hero-inner">
                <div class="reel-hero-copy">
                    <span class="hero-badge">
                        <i class="fa-solid fa-film"></i>
                        Demo Feature Videos
                    </span>
                    <h2 class="reel-hero-title">Feature Video Management</h2>
                    <p class="reel-hero-meta">
                        Upload, manage and order the dashboard videos shown to demo users.
                        Each subject can have one feature video.
                    </p>
                </div>

                <button type="button" wire:click="openCreateModal" class="theme-add-btn">
                    <i class="fa-solid fa-plus"></i>
                    Add new video
                </button>
            </div>
            <div class="sprockets"></div>
        </div>

        {{-- Stats --}}
        <div class="reel-stats">
            <div class="reel-stat">
                <div class="reel-stat-icon g"><i class="fa-solid fa-video"></i></div>
                <div>
                    <p class="reel-stat-value">{{ $totalVideos }}</p>
                    <span class="reel-stat-label">Total videos</span>
                </div>
            </div>

            <div class="reel-stat">
                <div class="reel-stat-icon success"><i class="fa-solid fa-play"></i></div>
                <div>
                    <p class="reel-stat-value">{{ $featured?->position ?? '—' }}</p>
                    <span class="reel-stat-label">Featured position</span>
                </div>
            </div>

            <div class="reel-stat">
                <div class="reel-stat-icon warning"><i class="fa-solid fa-arrow-up"></i></div>
                <div>
                    <p class="reel-stat-value">{{ $nextPosition }}</p>
                    <span class="reel-stat-label">Next position</span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-card">
            <div>
                <input type="text" wire:model.live.debounce.500ms="search"
                    placeholder="Search title or description..." class="form-control">
            </div>
            <div>
                <input type="number" wire:model.live="filterPosition" placeholder="Filter by position"
                    class="form-control">
            </div>
        </div>

        {{-- Video Gallery --}}
        <div class="reel-section">
            <div class="reel-section-header">
                <div>
                    <h4 class="reel-section-title"><i class="fa-solid fa-clapperboard"></i> Uploaded videos</h4>
                    <p class="reel-section-sub">Every frame below plays in position order.</p>
                </div>
                <div class="video-count">{{ $totalVideos }} videos</div>
            </div>

            <div class="reel-grid">
                @forelse($videos as $video)
                    <div class="frame" wire:key="video-{{ $video->id }}">
                        <div class="frame-media">
                            <span class="frame-id reel-mono">#{{ $video->id }}</span>
                            <video controls preload="metadata">
                                <source src="{{ Storage::url($video->file_path) }}" type="{{ $video->file_mime }}">
                            </video>
                        </div>

                        <div class="frame-body">
                            <div class="frame-tags">
                                <span class="timecode reel-mono">POS·{{ str_pad($video->position, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="subject-tag">{{ $video->category?->name ?? 'No subject' }}</span>
                            </div>

                            <p class="frame-title">{{ $video->title ?: 'Untitled' }}</p>
                            <p class="frame-desc">{{ \Illuminate\Support\Str::limit($video->description, 90) }}</p>

                            <div class="frame-meta">
                                @if ($video->file_size)
                                    <span>{{ round($video->file_size / 1024 / 1024, 2) }} MB</span>
                                    <span>·</span>
                                @endif
                                <span>{{ $video->created_at?->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="frame-actions">
                            <button wire:click="edit({{ $video->id }})" class="icon-btn edit">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button onclick="confirmDelete({{ $video->id }})" class="icon-btn delete">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-frame">
                        <i class="fa-solid fa-film"></i>
                        <p>No videos yet — add one to start the reel.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($videos->hasPages())
                <div class="reel-pagination">
                    <div class="pg-info">
                        Showing {{ $videos->firstItem() }}–{{ $videos->lastItem() }} of {{ $videos->total() }}
                    </div>

                    <div class="pg-controls">
                        <button type="button" class="pg-btn" wire:click="previousPage" wire:loading.attr="disabled"
                            @if ($videos->onFirstPage()) disabled @endif>
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        @php
                            $current = $videos->currentPage();
                            $last    = $videos->lastPage();
                            $window  = 1;
                        @endphp

                        @for ($page = 1; $page <= $last; $page++)
                            @if ($page === 1 || $page === $last || ($page >= $current - $window && $page <= $current + $window))
                                <button type="button"
                                    class="pg-btn {{ $page === $current ? 'active' : '' }}"
                                    wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            @elseif ($page === $current - $window - 1 || $page === $current + $window + 1)
                                <span class="pg-dots">…</span>
                            @endif
                        @endfor

                        <button type="button" class="pg-btn" wire:click="nextPage" wire:loading.attr="disabled"
                            @if (! $videos->hasMorePages()) disabled @endif>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Upload / Edit Modal --}}
        @if ($showModal)
            <div class="reel-modal-backdrop" wire:click.self="closeModal">
                <div class="reel-modal">

                    <div class="reel-modal-header">
                        <h4>
                            @if ($isEdit)
                                <i class="fa-solid fa-pen"></i> Edit video
                            @else
                                <i class="fa-solid fa-upload"></i> Upload video
                            @endif
                        </h4>
                        <button type="button" wire:click="closeModal" class="reel-modal-close" aria-label="Close">&times;</button>
                    </div>

                    <form wire:submit="{{ $isEdit ? 'update' : 'save' }}">
                        <div class="reel-modal-body">
                            <div class="form-grid">

                                <div>
                                    <label class="form-label">Subject / course</label>
                                    <select wire:model="category_id" class="form-select">
                                        <option value="">Select subject</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if (in_array($category->id, $usedCategoryIds)) disabled @endif>
                                                {{ $category->name }}
                                                @if (in_array($category->id, $usedCategoryIds)) (already has a video) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="field-note">Only one feature video is allowed per subject.</div>
                                    @error('category_id') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="form-label">Position</label>
                                    <input type="number" wire:model.live="position" class="form-control" placeholder="e.g. {{ $nextPosition }}">
                                    <div class="field-note">Must be unique — video 1 shows first, video 2 shows second.</div>
                                    @error('position') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="span-2">
                                    <label class="form-label">Video file @unless($isEdit) <span style="color:var(--danger)">*</span> @endunless</label>

                                    <label class="upload-box">
                                        <input type="file" wire:model="video_file" accept="video/*" class="upload-input">
                                        <div class="upload-content">
                                            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                            <h5>{{ $isEdit ? 'Replace video (optional)' : 'Upload video' }}</h5>
                                            <p>Click to browse or drag & drop</p>
                                            <span class="upload-note">MP4 · MOV · AVI · MKV · WEBM</span>

                                            @if ($video_file)
                                                <div class="selected-file">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    <div>
                                                        <strong>{{ $video_file->getClientOriginalName() }}</strong>
                                                        <small>{{ round($video_file->getSize() / 1024 / 1024, 2) }} MB</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </label>

                                    @error('video_file') <span class="field-error">{{ $message }}</span> @enderror

                                    <div wire:loading wire:target="video_file" class="upload-progress">
                                        <div class="progress-track">
                                            <div class="progress-fill"></div>
                                        </div>
                                        <div class="field-note">Uploading…</div>
                                    </div>
                                </div>

                                <div class="span-2">
                                    <label class="form-label">Title</label>
                                    <input type="text" wire:model.live="title" class="form-control" placeholder="Short, descriptive title">
                                    @error('title') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="span-2">
                                    <label class="form-label">Description</label>
                                    <textarea rows="4" wire:model.live="description" class="form-control" placeholder="What does this video show?"></textarea>
                                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>

                        <div class="reel-modal-footer">
                            <button type="submit" class="{{ $isEdit ? 'btn-success' : 'theme-submit-btn' }}" wire:loading.attr="disabled" wire:target="save,update">
                                <span wire:loading.remove wire:target="save,update">
                                    <i class="fa-solid {{ $isEdit ? 'fa-save' : 'fa-cloud-upload' }}"></i>
                                    {{ $isEdit ? 'Update video' : 'Save video' }}
                                </span>
                                <span wire:loading wire:target="save,update">Saving…</span>
                            </button>

                            <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                        </div>
                    </form>

                </div>
            </div>
        @endif

    </div>

    {{-- Sweet Alert --}}
    @script
        <script>
            window.confirmDelete = function (id) {
                Swal.fire({
                    title: 'Delete video?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Delete',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.delete(id);
                    }
                });
            };

            Livewire.on('video-created', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Video uploaded successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });

            Livewire.on('video-updated', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Video updated successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });

            Livewire.on('video-deleted', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Video deleted successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });

            Livewire.on('video-error', (event) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: event.message ?? 'Please try again.',
                    confirmButtonColor: '#dc2626',
                });
            });
        </script>
    @endscript

</div>