@extends('layouts.app')

@section('content')

    {{-- ═══════════════════════════════════════════════════
     SWEET ALERT — flash messages from controller
════════════════════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Done!',
                    text: @json(session('success')),
                    confirmButtonColor: '#0947a8',
                    confirmButtonText: 'Great!',
                    timer: 3500,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: @json(session('error')),
                    confirmButtonColor: '#0947a8',
                });
            });
        </script>
    @endif

    <style>
        /* ═══════════════════════════════════════════
           PAGE LAYOUT — mapped to the real theme vars:
           --card, --line, --border, --text, --muted,
           --primary, --primary-dark, --primary-glow,
           --accent, --danger, --radius, --radius-sm,
           --radius-xs, --shadow, --shadow-card, --shadow-sm,
           --input-bg, --input-border, --input-focus, --bg-card2
        ═══════════════════════════════════════════ */
        .cat-page { display: flex; flex-direction: column; gap: 20px; }

        /* ── Header ─────────────────────────────────── */
        .cat-header {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius, 20px);
            padding: 24px 28px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; flex-wrap: wrap;
            position: relative; overflow: hidden;
            box-shadow: var(--shadow-card);
        }
        .cat-header::before {
            content: ''; position: absolute; top: -40px; left: -40px;
            width: 200px; height: 200px; border-radius: 50%;
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            pointer-events: none;
        }
        .cat-header-text h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.02em; margin: 0 0 4px; color: var(--text); }
        .cat-header-text p { font-size: 13px; color: var(--muted); margin: 0; }
        .cat-count-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--primary-glow); border: 1px solid var(--primary);
            color: var(--primary-dark); border-radius: 100px; padding: 4px 12px;
            font-size: 12px; font-weight: 700; margin-left: 10px;
        }

        /* ── Buttons ────────────────────────────────── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--primary); color: #fff; border: none;
            border-radius: var(--radius-sm, 12px); padding: 10px 18px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            transition: all 200ms ease; box-shadow: 0 4px 14px var(--primary-glow);
            text-decoration: none; white-space: nowrap;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 22px var(--primary-glow); filter: brightness(1.06); }

        .btn-ghost {
            display: inline-flex; align-items: center; gap: 6px;
            background: transparent; color: var(--text); border: 1px solid var(--line);
            border-radius: var(--radius-sm, 12px); padding: 9px 16px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all 180ms ease; text-decoration: none; white-space: nowrap;
        }
        .btn-ghost:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-glow); }

        .btn-danger-ghost {
            display: inline-flex; align-items: center; gap: 6px;
            background: transparent; color: var(--muted); border: 1px solid var(--line);
            border-radius: var(--radius-sm, 12px); padding: 9px 16px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all 180ms ease; white-space: nowrap;
        }
        .btn-danger-ghost:hover { border-color: var(--danger); color: var(--danger); background: rgba(220, 38, 38, .08); }

        /* ── Category grid ──────────────────────────── */
        .cat-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        @media (max-width: 1140px) { .cat-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 720px)  { .cat-grid { grid-template-columns: 1fr; } }

        /* ── Category card ──────────────────────────── */
        .cat-card {
            background: var(--card); border: 1px solid var(--line);
            border-radius: var(--radius, 20px); overflow: hidden;
            display: grid; grid-template-rows: auto auto 1fr auto;
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
            box-shadow: var(--shadow-card);
        }
        .cat-card:hover {
            transform: translateY(-4px); border-color: var(--primary);
            box-shadow: var(--shadow), 0 0 0 1px var(--primary-glow);
        }

        .cat-thumb-wrap { position: relative; overflow: hidden; height: 160px; }
        .cat-thumb { width: 100%; height: 100%; object-fit: cover; transition: transform 350ms ease; display: block; }
        .cat-card:hover .cat-thumb { transform: scale(1.06); }
        .cat-thumb-placeholder {
            width: 100%; height: 100%; background: var(--bg-card2, var(--bg2));
            display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px;
            color: var(--muted); font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
        }
        .cat-thumb-placeholder svg { opacity: .35; }
        .cat-thumb-wrap::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 60px;
            background: linear-gradient(to top, rgba(14, 31, 54, .35), transparent); pointer-events: none;
        }

        .cat-card-head {
            padding: 16px 16px 12px; border-bottom: 1px solid var(--line);
            display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;
        }
        .cat-card-head-text h3 { font-size: 17px; font-weight: 800; color: var(--text); margin: 0 0 4px; letter-spacing: -0.01em; line-height: 1.2; }
        .cat-card-head-text p {
            font-size: 12px; color: var(--muted); margin: 0; line-height: 1.5;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .sub-count-badge {
            flex-shrink: 0; background: var(--primary-glow); border: 1px solid var(--primary);
            color: var(--primary-dark); border-radius: 100px; padding: 4px 10px; font-size: 11px; font-weight: 700; white-space: nowrap;
        }

        .sub-list { background: var(--bg-card2, var(--bg2)); }
        .sub-item {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 10px 16px; border-bottom: 1px solid var(--line); transition: background 160ms ease;
        }
        .sub-item:last-child { border-bottom: none; }
        .sub-item:hover { background: var(--primary-glow); }
        .sub-name { font-size: 13px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 7px; }
        .sub-name::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: var(--primary); flex-shrink: 0; opacity: .7; }
        .sub-actions { display: flex; gap: 5px; }

        .icon-btn {
            width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--line);
            background: var(--card); color: var(--muted); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 170ms ease; flex-shrink: 0;
        }
        .icon-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-glow); transform: translateY(-1px); }
        .icon-btn.del:hover { border-color: var(--danger); color: var(--danger); background: rgba(220, 38, 38, .1); }

        .sub-empty { padding: 14px 16px; font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 7px; }

        .cat-card-tools {
            padding: 12px 14px; border-top: 1px solid var(--line);
            display: flex; gap: 8px; flex-wrap: wrap; background: var(--bg-card2, var(--bg2));
        }
        .cat-card-tools form { display: contents; }

        /* ── Empty state ─────────────────────────────── */
        .cat-empty {
            grid-column: 1/-1; background: var(--card); border: 1px dashed var(--line);
            border-radius: var(--radius, 20px); padding: 60px 20px; text-align: center; color: var(--muted);
        }
        .cat-empty svg { opacity: .35; margin-bottom: 14px; }
        .cat-empty p { font-size: 14px; margin: 0; }

        /* ── Pagination ──────────────────────────────── */
        .cat-pagination { display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap; margin-top: 4px; }
        .page-btn {
            min-width: 36px; height: 36px; border-radius: var(--radius-sm, 12px);
            border: 1px solid var(--line); background: var(--card); color: var(--text);
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
            padding: 0 12px; font-size: 13px; font-weight: 600; transition: all 160ms ease;
        }
        .page-btn:hover:not(.active):not(.disabled) { border-color: var(--primary); color: var(--primary); background: var(--primary-glow); }
        .page-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 4px 12px var(--primary-glow); }
        .page-btn.disabled { opacity: .35; pointer-events: none; }

        /* ── Modal ───────────────────────────────────── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(8, 15, 28, .56); backdrop-filter: blur(4px);
            display: none; align-items: center; justify-content: center; padding: 20px; z-index: 1000;
            opacity: 0; transition: opacity 200ms ease;
        }
        .modal-overlay.open { display: flex; opacity: 1; }
        .modal {
            width: min(580px, 100%); max-height: calc(100vh - 40px); overflow-y: auto;
            background: var(--card); border: 1px solid var(--line); border-radius: var(--radius, 20px);
            box-shadow: var(--shadow); animation: modalSlideIn 220ms cubic-bezier(.175, .885, .32, 1.275);
            display: flex; flex-direction: column;
        }
        @keyframes modalSlideIn { from { opacity: 0; transform: translateY(20px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); flex-shrink: 0; }
        .modal-head h3 { font-size: 18px; font-weight: 800; margin: 0; color: var(--text); letter-spacing: -0.01em; }
        .modal-close-btn {
            width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line);
            background: transparent; color: var(--muted); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 160ms ease; flex-shrink: 0;
        }
        .modal-close-btn:hover { background: rgba(220, 38, 38, .1); border-color: var(--danger); color: var(--danger); }
        .modal-body { padding: 22px; flex: 1; }
        .modal-footer { padding: 14px 22px; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0; }

        /* ── Form fields inside modal ────────────────── */
        .form-stack { display: flex; flex-direction: column; gap: 16px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width:520px) { .form-row { grid-template-columns: 1fr; } }

        .field label { display: block; font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 7px; }
        .field input[type="text"], .field input[type="email"], .field select, .field textarea {
            width: 100%; background: var(--input-bg, var(--card)); border: 1px solid var(--input-border, var(--line));
            border-radius: var(--radius-xs, 8px); color: var(--text); font-size: 14px; padding: 10px 14px;
            outline: none; transition: border-color 160ms ease, box-shadow 160ms ease; appearance: none;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--input-focus, var(--primary)); box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .field input::placeholder, .field textarea::placeholder { color: var(--muted); }
        .field select option { background: var(--card); color: var(--text); }

        .field input[type="file"] {
            background: var(--input-bg, var(--card)); border: 1px dashed var(--input-border, var(--line));
            border-radius: var(--radius-xs, 8px); color: var(--muted); font-size: 13px; padding: 10px 14px;
            cursor: pointer; width: 100%; transition: border-color 160ms ease;
        }
        .field input[type="file"]:hover { border-color: var(--primary); }

        .thumb-preview-wrap { margin-bottom: 6px; }
        .thumb-preview { width: 100%; max-height: 120px; object-fit: cover; border-radius: var(--radius-xs, 8px); border: 1px solid var(--line); display: block; }
    </style>

    {{-- ═══ PAGE ═══ --}}
    <div class="cat-page">

        {{-- ── Header ── --}}
        <div class="cat-header">
            <div class="cat-header-text">
                <h1>
                    All Categories
                    <span class="cat-count-chip">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        {{ $allCategories->count() }} total
                    </span>
                </h1>
                <p>Manage main categories and subcategories from one place.</p>
            </div>
            @if ($canManage)
                <button type="button" class="btn-primary" data-open="modal-create-main">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Category
                </button>
            @endif
        </div>

        {{-- ── Grid ── --}}
        <div class="cat-grid">
            @forelse($categories as $cat)
                <article class="cat-card">

                    <div class="cat-thumb-wrap">
                        @if ($cat->thumbnail)
                            <img class="cat-thumb" src="{{ $cat->thumbnail_url }}" alt="{{ $cat->name }}" loading="lazy">
                        @else
                            <div class="cat-thumb-placeholder">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="3" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <path d="M21 15l-5-5L5 21" />
                                </svg>
                                No Image
                            </div>
                        @endif
                    </div>

                    <div class="cat-card-head">
                        <div class="cat-card-head-text">
                            <h3>{{ $cat->name }}</h3>
                            <p>{{ $cat->description ?: 'No description provided.' }}</p>
                        </div>
                        <span class="sub-count-badge">{{ $cat->children_count }} sub</span>
                    </div>

                    <div class="sub-list">
                        @forelse($cat->children as $child)
                            <div class="sub-item">
                                <span class="sub-name">{{ $child->name }}</span>
                                @if ($canManage)
                                    <div class="sub-actions">
                                        <button type="button" class="icon-btn" title="Edit {{ $child->name }}" data-open="modal-edit-sub-{{ $child->id }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('course-categories.destroy', $child) }}" class="del-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="icon-btn del" title="Delete {{ $child->name }}"
                                                data-confirm="Delete subcategory <strong>{{ $child->name }}</strong>? This cannot be undone."
                                                onclick="confirmDelete(this)">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="sub-empty">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                No subcategories yet.
                            </div>
                        @endforelse
                    </div>

                    @if ($canManage)
                        <div class="cat-card-tools">
                            <button type="button" class="btn-ghost" style="font-size:12px; padding:7px 12px" data-open="modal-create-sub-{{ $cat->id }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Add Sub
                            </button>
                            <button type="button" class="btn-ghost" style="font-size:12px; padding:7px 12px" data-open="modal-edit-cat-{{ $cat->id }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                                Edit
                            </button>
                            <form method="POST" action="{{ route('course-categories.destroy', $cat) }}" class="del-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-danger-ghost" style="font-size:12px; padding:7px 12px"
                                    data-confirm="Delete category <strong>{{ $cat->name }}</strong> and all its subcategories? This cannot be undone."
                                    onclick="confirmDelete(this)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6l-1 14H6L5 6" />
                                        <path d="M10 11v6" />
                                        <path d="M14 11v6" />
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif

                </article>
            @empty
                <div class="cat-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    <p>No categories found. Create your first one!</p>
                </div>
            @endforelse
        </div>

        {{-- ── Pagination ── --}}
        @if ($categories->hasPages())
            <div class="cat-pagination">
                <a class="page-btn {{ $categories->onFirstPage() ? 'disabled' : '' }}" href="{{ $categories->previousPageUrl() ?: '#' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                </a>

                @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                    <a class="page-btn {{ $page == $categories->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                @endforeach

                <a class="page-btn {{ $categories->hasMorePages() ? '' : 'disabled' }}" href="{{ $categories->nextPageUrl() ?: '#' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </a>
            </div>
        @endif
    </div>

    @if ($canManage)
        {{-- ── Create Main Category Modal ── --}}
        <div class="modal-overlay" id="modal-create-main">
            <div class="modal">
                <div class="modal-head">
                    <h3>Add Category</h3>
                    <button type="button" class="modal-close-btn" data-close="modal-create-main">✕</button>
                </div>
                <form method="POST" action="{{ route('course-categories.store') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-stack">
                            @csrf
                            <div class="field">
                                <label>Name</label>
                                <input type="text" name="name" placeholder="e.g. Web Development" required>
                            </div>
                            <div class="field">
                                <label>Description</label>
                                <textarea name="description" rows="3" placeholder="Short description of this category"></textarea>
                            </div>
                            <div class="field">
                                <label>Thumbnail</label>
                                <input type="file" name="thumbnail" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-ghost" data-close="modal-create-main">Cancel</button>
                        <button type="submit" class="btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($categories as $cat)
            {{-- ── Edit Category Modal ── --}}
            <div class="modal-overlay" id="modal-edit-cat-{{ $cat->id }}">
                <div class="modal">
                    <div class="modal-head">
                        <h3>Edit Category</h3>
                        <button type="button" class="modal-close-btn" data-close="modal-edit-cat-{{ $cat->id }}">✕</button>
                    </div>
                    <form method="POST" action="{{ route('course-categories.update', $cat) }}" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-stack">
                                @csrf @method('PUT')
                                @if ($cat->thumbnail)
                                    <div class="thumb-preview-wrap">
                                        <img class="thumb-preview" src="{{ $cat->thumbnail_url }}" alt="{{ $cat->name }}">
                                    </div>
                                @endif
                                <div class="field">
                                    <label>Name</label>
                                    <input type="text" name="name" value="{{ $cat->name }}" required>
                                </div>
                                <div class="field">
                                    <label>Description</label>
                                    <textarea name="description" rows="3">{{ $cat->description }}</textarea>
                                </div>
                                <div class="field">
                                    <label>Replace Thumbnail</label>
                                    <input type="file" name="thumbnail" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-ghost" data-close="modal-edit-cat-{{ $cat->id }}">Cancel</button>
                            <button type="submit" class="btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Create Subcategory Modal ── --}}
            <div class="modal-overlay" id="modal-create-sub-{{ $cat->id }}">
                <div class="modal">
                    <div class="modal-head">
                        <h3>Add Subcategory to {{ $cat->name }}</h3>
                        <button type="button" class="modal-close-btn" data-close="modal-create-sub-{{ $cat->id }}">✕</button>
                    </div>
                    <form method="POST" action="{{ route('course-categories.store') }}">
                        <div class="modal-body">
                            <div class="form-stack">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $cat->id }}">
                                <div class="field">
                                    <label>Name</label>
                                    <input type="text" name="name" placeholder="e.g. Frontend" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-ghost" data-close="modal-create-sub-{{ $cat->id }}">Cancel</button>
                            <button type="submit" class="btn-primary">Add Subcategory</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Edit Subcategory Modals ── --}}
            @foreach ($cat->children as $child)
                <div class="modal-overlay" id="modal-edit-sub-{{ $child->id }}">
                    <div class="modal">
                        <div class="modal-head">
                            <h3>Edit Subcategory</h3>
                            <button type="button" class="modal-close-btn" data-close="modal-edit-sub-{{ $child->id }}">✕</button>
                        </div>
                        <form method="POST" action="{{ route('course-categories.update', $child) }}">
                            <div class="modal-body">
                                <div class="form-stack">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="parent_id" value="{{ $cat->id }}">
                                    <div class="field">
                                        <label>Name</label>
                                        <input type="text" name="name" value="{{ $child->name }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-ghost" data-close="modal-edit-sub-{{ $child->id }}">Cancel</button>
                                <button type="submit" class="btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        @endforeach
    @endif

    <script>
        (function () {
            function openModal(id) {
                var el = document.getElementById(id);
                if (!el) return;
                el.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
            function closeModal(id) {
                var el = document.getElementById(id);
                if (!el) return;
                el.classList.remove('open');
                document.body.style.overflow = '';
            }

            document.addEventListener('click', function (e) {
                var openTrigger = e.target.closest('[data-open]');
                if (openTrigger) { openModal(openTrigger.getAttribute('data-open')); return; }

                var closeTrigger = e.target.closest('[data-close]');
                if (closeTrigger) { closeModal(closeTrigger.getAttribute('data-close')); return; }

                if (e.target.classList.contains('modal-overlay')) {
                    closeModal(e.target.id);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                document.querySelectorAll('.modal-overlay.open').forEach(function (el) { closeModal(el.id); });
            });

            window.confirmDelete = function (btn) {
                var form = btn.closest('.del-form');
                var message = btn.getAttribute('data-confirm') || 'Are you sure you want to delete this?';
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    html: message,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed && form) form.submit();
                });
            };
        })();
    </script>

@endsection