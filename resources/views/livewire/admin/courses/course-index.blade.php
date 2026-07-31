<div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ═══════════════════════════════════════════════
           COURSE INDEX — INLINE THEME-BASED STYLES
           Covers: hero, filter bar, grid view (trainer),
           table view (admin), modals, forms.
           Uses global theme vars: --card, --line, --text,
           --muted, --primary, --radius, --shadow-card, ...
        ═══════════════════════════════════════════════ */

        /* ── Hero ───────────────────────────────────────────────── */
        .cc-hero {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 14px; padding: 18px 20px;
            background: var(--card); border: 1px solid var(--line);
            border-radius: var(--radius, 16px); box-shadow: var(--shadow-card);
            margin-bottom: 16px;
        }
        .cc-hero-title { font-size: 20px; font-weight: 700; color: var(--text); }
        .cc-hero-meta { font-size: 13px; color: var(--muted); margin-top: 2px; }

        /* ── Flash alert (fallback if JS disabled) ─────────────── */
        .cc-alert {
            padding: 12px 16px; border-radius: var(--radius-xs, 10px);
            font-size: 14px; font-weight: 600; margin-bottom: 16px;
        }
        .cc-alert-success { background: rgba(22, 163, 74, .1); color: var(--success); border: 1px solid rgba(22, 163, 74, .25); }

        /* ── Filter bar — single row ───────────────────────────── */
        .cc-filter-bar {
            background: var(--card); border: 1px solid var(--line);
            border-radius: var(--radius, 16px); box-shadow: var(--shadow-card);
            padding: 16px 20px; margin-bottom: 16px;
        }
        .cc-filter-bar-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; }
        .cc-filter-bar-row .cc-field { flex: 1 1 200px; min-width: 160px; margin-bottom: 0; }
        .cc-filter-bar-row .cc-actions { flex: 0 0 auto; display: flex; gap: 8px; }
        @media (max-width: 720px) {
            .cc-filter-bar-row { flex-direction: column; align-items: stretch; }
        }

        .cc-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .cc-field-full { grid-column: 1 / -1; }
        .cc-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
        .cc-select, .cc-input, .cc-textarea {
            border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--card));
            color: var(--text); border-radius: var(--radius-xs, 8px); padding: 9px 11px; font-size: 14px;
            width: 100%; font-family: inherit;
        }
        .cc-select:focus, .cc-input:focus, .cc-textarea:focus {
            outline: none; border-color: var(--input-focus, var(--primary));
            box-shadow: 0 0 0 3px var(--primary-glow, rgba(13, 93, 209, .12));
        }
        .cc-textarea { resize: vertical; }
        .cc-error { display: block; font-size: 12px; color: var(--danger); margin-top: 2px; }
        .req { color: var(--danger); }

        /* ── Buttons ────────────────────────────────────────────── */
        .cc-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            border-radius: var(--radius-xs, 10px); padding: 9px 16px; font-size: 14px; font-weight: 600;
            border: 1px solid transparent; cursor: pointer; transition: 160ms ease; line-height: 1;
        }
        .cc-btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .cc-btn-primary:hover { background: var(--primary-dark, var(--primary)); }
        .cc-btn-outline { background: var(--card); color: var(--text); border-color: var(--line); }
        .cc-btn-outline:hover { border-color: var(--primary); background: var(--primary-glow); }
        .cc-btn-link { background: none; border: none; color: var(--primary); font-weight: 600; font-size: 13px; cursor: pointer; padding: 4px 6px; }
        .cc-btn-link:hover { text-decoration: underline; }
        .cc-btn-link-danger { background: none; border: none; color: var(--danger); font-weight: 600; font-size: 13px; cursor: pointer; padding: 4px 6px; }
        .cc-btn-link-danger:hover { text-decoration: underline; }
        .cc-table-actions { display: flex; gap: 10px; }

        /* ── Content wrap ───────────────────────────────────────── */
        .cc-wrap {
            background: var(--card); border: 1px solid var(--line);
            border-radius: var(--radius, 16px); box-shadow: var(--shadow-card);
            padding: 20px;
        }
        .cc-empty { text-align: center; padding: 40px 20px; color: var(--muted); font-size: 14px; }

        /* ── Trainer tabs / subtabs ─────────────────────────────── */
        .cc-tabs { display: flex; flex-wrap: wrap; gap: 8px; }
        .cc-tab-btn {
            border: 1px solid var(--line); background: var(--bg-card2, var(--bg2)); color: var(--text);
            border-radius: 999px; padding: 8px 18px; font-size: 13.5px; font-weight: 600; cursor: pointer;
            transition: 160ms ease;
        }
        .cc-tab-btn.active, .cc-tab-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        .cc-subtab-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
        .cc-subtabs { display: flex; flex-wrap: wrap; gap: 8px; }
        .cc-subtab-btn {
            border: 1px solid var(--line); background: var(--card); color: var(--muted);
            border-radius: 999px; padding: 6px 14px; font-size: 12.5px; font-weight: 600; cursor: pointer;
            transition: 160ms ease;
        }
        .cc-subtab-btn.active, .cc-subtab-btn:hover { background: var(--primary-glow); color: var(--primary); border-color: var(--primary); }

        .cc-divider { height: 1px; background: var(--line); margin: 14px 0 18px; }

        /* ── Course GRID (trainer browse view) ─────────────────── */
        .cc-course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 18px;
        }
        .cc-course-tile {
            display: block; border: 1px solid var(--line); border-radius: var(--radius-sm, 14px);
            background: var(--card); overflow: hidden; box-shadow: var(--shadow-sm);
            transition: 180ms ease;
        }
        a.cc-course-tile:hover { transform: translateY(-3px); box-shadow: var(--shadow-card); border-color: var(--primary); }
        .cc-course-tile.disabled { opacity: .65; }
        .cc-tile-top {
            height: 130px; background: var(--bg-card2, var(--bg2)) center/cover no-repeat;
            display: flex; align-items: flex-end; padding: 10px 12px;
            background-blend-mode: multiply;
        }
        .cc-tile-top h3 {
            color: #fff; font-size: 15px; font-weight: 700;
            text-shadow: 0 2px 6px rgba(0, 0, 0, .55);
        }
        .cc-tile-body { padding: 12px 14px 14px; display: flex; flex-direction: column; gap: 10px; }
        .cc-tile-meta { font-size: 12px; color: var(--muted); font-weight: 600; }
        .cc-badge {
            display: inline-flex; align-items: center; gap: 4px; width: fit-content;
            padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;
        }
        .cc-badge-muted { background: var(--bg-card2, var(--bg2)); color: var(--muted); }

        /* ── Course TABLE (admin view) ──────────────────────────── */
        .cc-table-wrap { overflow-x: auto; }
        .cc-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .cc-table thead th {
            text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .6px;
            color: var(--muted); background: var(--bg-card2, var(--bg2)); border-bottom: 1px solid var(--line);
            padding: 12px 14px; white-space: nowrap;
        }
        .cc-table tbody td { padding: 14px; border-bottom: 1px solid var(--line); color: var(--text); vertical-align: top; }
        .cc-table tbody tr:nth-child(even) td { background: var(--bg-card2, var(--bg2)); }
        .cc-table tbody tr:hover td { background: var(--primary-glow, rgba(13, 93, 209, .06)); }

        .cc-course-title-cell { display: flex; align-items: center; gap: 12px; }
        .cc-course-title-cell .name { display: block; font-weight: 700; color: var(--text); font-size: 14px; }
        .cc-course-title-cell .meta { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; max-width: 320px; }
        .cc-course-thumb {
            width: 56px; height: 56px; object-fit: cover; border-radius: var(--radius-xs, 8px);
            border: 1px solid var(--line); flex-shrink: 0;
        }
        .cc-course-thumb-placeholder {
            width: 56px; height: 56px; border-radius: var(--radius-xs, 8px);
            border: 1px dashed var(--line); background: var(--bg-card2, var(--bg2));
            color: var(--muted); font-size: 9px; font-weight: 700; text-align: center;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* ── Modal ──────────────────────────────────────────────── */
        .cc-modal-overlay {
            position: fixed; inset: 0; background: rgba(8, 15, 28, .56);
            backdrop-filter: blur(3px); display: flex; align-items: center;
            justify-content: center; padding: 18px; z-index: 120;
        }
        .cc-modal {
            width: min(860px, 100%); max-height: calc(100vh - 36px); overflow: auto;
            border-radius: var(--radius, 16px); border: 1px solid var(--line);
            background: var(--card); box-shadow: var(--shadow);
        }
        .cc-modal-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 18px; border-bottom: 1px solid var(--line);
        }
        .cc-modal-head h3 { margin: 0; font-size: 20px; color: var(--text); }
        .cc-modal-close { border: 0; background: transparent; color: var(--muted); font-size: 20px; line-height: 1; cursor: pointer; }
        .cc-modal-close:hover { color: var(--danger); }
        .cc-modal-body { padding: 18px; }
        .cc-modal-footer { display: flex; justify-content: flex-end; border-top: 1px solid var(--line); padding: 14px 18px; gap: 10px; }

        /* ── Course form grid (used in partial) ────────────────── */
        .cc-form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        @media (max-width: 640px) { .cc-form-grid { grid-template-columns: 1fr; } }

        .cc-thumb-row { display: flex; align-items: center; gap: 16px; }
        .cc-thumb-preview {
            width: 90px; height: 90px; border-radius: var(--radius-xs, 10px);
            object-fit: cover; border: 1px solid var(--line); flex-shrink: 0;
        }
        .cc-thumb-empty {
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); font-size: 11px; font-weight: 600;
            background: var(--bg-card2, var(--bg2)); border-style: dashed;
        }
        .cc-file-input { font-size: 13px; color: var(--text); }
        .cc-uploading { font-size: 12px; color: var(--primary); margin-top: 4px; }
        .cc-hint { font-size: 12px; color: var(--muted); margin-top: 6px; }
    </style>

    {{-- ================= HERO ================= --}}
    <div class="cc-hero">
        <div>
            <div class="cc-hero-title">All Courses ({{ $courses->total() }})</div>
            <div class="cc-hero-meta">Create and manage courses with category and subcategory.</div>
        </div>
        <button type="button" wire:click="openCreateModal" class="cc-btn cc-btn-primary">+ Add Course</button>
    </div>

    {{-- ================= FILTER BAR - single row, always visible ================= --}}
    <div class="cc-filter-bar cc-filter-bar-row">
        <div class="cc-field">
            <label class="cc-label">Main Category</label>
            <select wire:model.live="category_id" class="cc-select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="cc-field">
            <label class="cc-label">Subcategory</label>
            <select wire:model.live="subcategory_id" class="cc-select">
                <option value="">All Subcategories</option>
                @foreach ($categories as $category)
                    @foreach ($category->children as $sub)
                        <option value="{{ $sub->id }}">{{ $category->name }} / {{ $sub->name }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="cc-actions">
            <button wire:click="clearFilters" class="cc-btn cc-btn-outline">Clear</button>
        </div>
    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="cc-wrap" style="max-width:none;">

        @if ($isTrainer)
            {{-- ================= TRAINER TAB-BROWSE / GRID VIEW ================= --}}
            <div style="text-align:center; margin-bottom:18px;">
                <h2 style="font-size:18px; color:var(--text); margin-bottom:4px;">Browse by Category</h2>
                <p style="font-size:13px; color:var(--text-muted);">Select a category and subcategory to filter courses</p>
            </div>

            <div class="cc-tabs" x-data="{ active: {{ $categories->first()?->id ?? 'null' }} }">
                @foreach ($categories as $index => $category)
                    <button type="button" class="cc-tab-btn" :class="active === {{ $category->id }} ? 'active' : ''"
                            @click="active = {{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach

                @foreach ($categories as $index => $category)
                    @php
                        $tabCourses = $category->courses
                            ->concat($category->children->flatMap->courses ?? collect())
                            ->unique('id')
                            ->values();
                    @endphp

                    <div x-show="active === {{ $category->id }}" x-data="{ subtab: 'all' }" style="width:100%; margin-top:16px;">

                        <div class="cc-subtab-label">Subcategories</div>
                        <div class="cc-subtabs">
                            <button type="button" class="cc-subtab-btn" :class="subtab === 'all' ? 'active' : ''" @click="subtab = 'all'">All</button>
                            @foreach ($category->children as $child)
                                <button type="button" class="cc-subtab-btn" :class="subtab === {{ $child->id }} ? 'active' : ''" @click="subtab = {{ $child->id }}">
                                    {{ $child->name }}
                                </button>
                            @endforeach
                        </div>

                        <div class="cc-divider"></div>

                        <div class="cc-course-grid">
                            @forelse ($tabCourses as $course)
                                @php
                                    $thumb = $course->thumbnail_url ?? null;
                                    $bg = $thumb ? "background-image:url('{$thumb}');" : '';
                                    $assigned = in_array($course->id, $this->assignedCourseIds, true);
                                    $courseCategory = $course->subcategory?->name ?? $course->category?->name ?? $category->name;
                                    $subCatId = $course->subcategory_id ?: 'none';
                                @endphp

                                <div x-show="subtab === 'all' || subtab === '{{ $subCatId }}'">
                                    @if ($assigned)
                                        <a href="{{ route('courses.show', $course) }}" class="cc-course-tile">
                                            <div class="cc-tile-top" style="{{ $bg }}"><h3>{{ $course->title }}</h3></div>
                                            <div class="cc-tile-body">
                                                <div class="cc-tile-meta">{{ $courseCategory }}</div>
                                                <span class="cc-btn cc-btn-outline" style="padding:6px 14px; font-size:12.5px;">Open Course</span>
                                            </div>
                                        </a>
                                    @else
                                        <div class="cc-course-tile disabled">
                                            <div class="cc-tile-top" style="{{ $bg }}"><h3>{{ $course->title }}</h3></div>
                                            <div class="cc-tile-body">
                                                <div class="cc-tile-meta">{{ $courseCategory }}</div>
                                                <span class="cc-badge cc-badge-muted">🔒 Locked</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="cc-empty">No courses found in this category</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            {{-- ================= ADMIN TABLE VIEW ================= --}}
            <div class="cc-table-wrap">
                <table class="cc-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Course</th>
                            <th>Language</th>
                            <th>Duration</th>
                            <th>Created By</th>
                            @if ($canManage) <th>Actions</th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td data-label="Category">
                                    <strong>{{ $course->category?->name ?? '-' }}</strong>
                                    <div style="font-size:12px; color:var(--text-muted);">
                                        {{ $course->subcategory?->name ? 'Sub: '.$course->subcategory->name : 'No subcategory' }}
                                    </div>
                                </td>
                                <td data-label="Course">
                                    <div class="cc-course-title-cell">
                                        @if ($course->thumbnail_url)
                                            <img src="{{ $course->thumbnail_url }}" class="cc-course-thumb">
                                        @else
                                            <div class="cc-course-thumb-placeholder">NO IMAGE</div>
                                        @endif
                                        <div>
                                            <span class="name">{{ $course->title }}</span>
                                            <span class="meta">{{ $course->short_description }}</span>
                                        </div>
                                    </div>
                                    <a class="cc-btn cc-btn-outline" style="margin-top:8px; padding:6px 14px; font-size:12.5px;" href="{{ route('courses.show', $course) }}">
                                        Open Course
                                    </a>
                                </td>
                                <td data-label="Language">{{ $course->language ?: '-' }}</td>
                                <td data-label="Duration">{{ $course->duration_hours }}h</td>
                                <td data-label="Created By">{{ $course->creator?->name ?? 'N/A' }}</td>
                                @if ($canManage)
                                    <td data-label="Actions">
                                        <div class="cc-table-actions">
                                            <button wire:click="openEditModal({{ $course->id }})" class="cc-btn-link">Edit</button>
                                            <button wire:click="confirmDelete({{ $course->id }})" class="cc-btn-link-danger">Delete</button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="cc-empty">No courses found</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div style="margin-top:18px;">{{ $courses->links('pagination.custom') }}</div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: @json(session('success')), showConfirmButton: false,
                timer: 2800, timerProgressBar: true
            });
        </script>
    @endif

    {{-- ================= CREATE MODAL ================= --}}
    @if ($canManage && $showCreateModal)
        <div class="cc-modal-overlay">
            <div class="cc-modal">
                <div class="cc-modal-head">
                    <h3>Add Course</h3>
                    <button wire:click="closeCreateModal" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    @include('livewire.admin.courses.partials.course-form', ['submitMethod' => 'saveCourse'])
                </div>
                <div class="cc-modal-footer">
                    <button wire:click="closeCreateModal" class="cc-btn cc-btn-outline">Cancel</button>
                    <button wire:click="saveCourse" class="cc-btn cc-btn-primary">Create Course</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ================= EDIT MODAL ================= --}}
    @if ($canManage && $editingCourseId)
        <div class="cc-modal-overlay">
            <div class="cc-modal">
                <div class="cc-modal-head">
                    <h3>Edit Course</h3>
                    <button wire:click="closeEditModal" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    @include('livewire.admin.courses.partials.course-form', ['submitMethod' => 'updateCourse'])
                </div>
                <div class="cc-modal-footer">
                    <button wire:click="closeEditModal" class="cc-btn cc-btn-outline">Cancel</button>
                    <button wire:click="updateCourse" class="cc-btn cc-btn-primary">Update Course</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ================= DELETE CONFIRM ================= --}}
    @if ($canManage && $deletingCourseId)
        <div class="cc-modal-overlay">
            <div class="cc-modal" style="max-width:420px;">
                <div class="cc-modal-head">
                    <h3>Delete Course</h3>
                    <button wire:click="cancelDelete" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    <p style="color:var(--text-muted); font-size:13.5px;">Are you sure you want to delete this course? This cannot be undone.</p>
                </div>
                <div class="cc-modal-footer">
                    <button wire:click="cancelDelete" class="cc-btn cc-btn-outline">Cancel</button>
                    <button wire:click="deleteCourse" class="cc-btn" style="background:var(--danger); color:#fff;">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>