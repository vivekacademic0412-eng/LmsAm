@extends('layouts.app')

@section('content')
    <style>
        .course-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .course-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--card);
            box-shadow: var(--shadow);
            overflow: hidden;
            display: grid;
        }
        .course-hero {
            position: relative;
            background: #0f1c34;
        }
        .course-hero img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            display: block;
        }
        .course-hero .course-pill {
            position: absolute;
            left: 12px;
            top: 12px;
            background: rgba(10, 20, 38, 0.7);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 999px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .course-hero .course-lock {
            position: absolute;
            right: 12px;
            bottom: 12px;
            background: rgba(255, 255, 255, 0.9);
            color: #1f2f48;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 999px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .course-body {
            padding: 14px;
            display: grid;
            gap: 8px;
        }
        .course-body h3 {
            margin: 0;
            font-size: 16px;
            line-height: 1.3;
        }
        .course-body .course-meta {
            font-size: 12px;
            color: var(--muted);
        }
        .course-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
        .tab-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .tab-row.centered { justify-content: center; }
        .tab-row .tab-btn { transition: 180ms ease; }
        .tab-row .tab-btn:hover { transform: translateY(-1px); border-color: #b6c7e8; }
        .tab-btn {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            background: var(--card);
            color: var(--text);
            cursor: pointer;
        }
        .tab-btn.active {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: #bcd3f7;
            box-shadow: 0 10px 20px rgba(28, 95, 202, 0.18);
        }
        .tab-btn.main-tab {
            padding: 8px 16px;
            font-size: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            border: 1px solid #c6d4ee;
            background: #fff;
        }
        .tab-btn.main-tab.active {
            background: linear-gradient(135deg, rgba(28, 95, 202, 0.12), rgba(73, 142, 255, 0.08));
            border-color: #9cbcf4;
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeUp 240ms ease; }
        .subtab-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; justify-content: center; }
        .subtab-btn {
            border: 1px dashed #c7d4ea;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            background: #fff;
            color: var(--text);
            cursor: pointer;
            transition: 160ms ease;
        }
        .subtab-btn:hover { transform: translateY(-1px); }
        .subtab-btn.active {
            border-style: solid;
            border-color: #bcd3f7;
            background: #eef4ff;
            color: #1f4fa3;
        }
        .subtab-label {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #7a8aa6;
            margin-top: 6px;
        }
        .course-tile {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: var(--shadow);
            text-decoration: none;
            color: inherit;
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }
        .course-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(20, 60, 120, 0.16);
            border-color: #c9d9f2;
        }
        .course-tile.disabled {
            opacity: 0.6;
            filter: grayscale(0.4);
            pointer-events: none;
        }
        .course-tile-top {
            min-height: 150px;
            padding: 14px;
            color: #fff;
            display: flex;
            align-items: end;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .course-tile-top::after { display: none; }
        .course-tile-top h3 {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            z-index: 1;
        }
        .course-tile-body { padding: 14px 16px; display: grid; gap: 8px; }
        .course-tile-meta { color: var(--muted); font-size: 12px; }
        .badge-lock {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #7a879b;
            background: #f1f4f9;
            border-radius: 999px;
            padding: 4px 8px;
        }
        .category-divider {
            height: 1px;
            background: linear-gradient(90deg, rgba(170, 188, 216, 0) 0%, rgba(170, 188, 216, 0.75) 50%, rgba(170, 188, 216, 0) 100%);
            margin: 18px 0 12px;
        }
        .category-head {
            text-align: center;
            display: grid;
            gap: 10px;
        }
        .category-head h2 { margin: 0; font-size: 24px; }
        .category-head p { margin: 0; color: var(--muted); font-size: 13px; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .course-thumb {
            width: 86px;
            height: 56px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--line-soft);
            background: var(--primary-soft);
        }
        .course-thumb-placeholder {
            width: 86px;
            height: 56px;
            border-radius: 10px;
            display: grid;
            place-content: center;
            border: 1px dashed var(--line);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
        }
        .course-title {
            display: grid;
            gap: 4px;
        }
        .course-title .name {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }
        .course-title .meta {
            font-size: 12px;
            color: var(--muted);
        }
        .row-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .category-create-btn {
            border: 1px solid #cfd7e4;
            background: #f7f9fc;
            color: #1f2f48;
            box-shadow: none;
            border-radius: 10px;
            padding: 7px 12px;
            font-weight: 600;
            letter-spacing: 0;
            transition: 160ms ease;
        }
        .category-create-btn:hover {
            border-color: #bcc8d9;
            background: #eef3f9;
            transform: translateY(-2px);
            box-shadow: none;
        }
        .btn-mini {
            border: 1px solid #cfd7e4;
            border-radius: 10px;
            background: #f7f9fc;
            color: #1f2f48;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1;
            transition: 160ms ease;
        }
        .btn-mini:hover {
            border-color: #bcc8d9;
            background: #eef3f9;
            transform: translateY(-1px);
        }
        .btn-mini svg {
            width: 13px;
            height: 13px;
            stroke: #53657f;
            stroke-width: 2;
        }
        .btn-mini.danger:hover {
            border-color: #d8b8b8;
            background: #f9f1f1;
        }
        .btn-mini.danger:hover svg,
        .btn-mini.danger:hover span {
            stroke: #b34747;
            color: #b34747;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 15, 28, 0.56);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 120;
        }
       .modal-overlay.open {
            display: flex;
            opacity: 1;
        }
        .modal {
            width: min(860px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.25);
        }
        .modal.modal-sm {
            width: min(460px, 100%);
        }
        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line-soft);
        }
        .modal-head h3 { margin: 0; font-size: 22px; }
        .modal-close {
            border: 0;
            background: transparent;
            color: var(--muted);
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }
        .modal-body { padding: 14px 16px 16px; }
        .modal-body .form-premium {
            padding: 16px;
            border-radius: 14px;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid var(--line-soft);
            padding: 12px 16px;
            gap: 8px;
        }
        .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .pagination .page-item {
            display: inline-flex;
        }
        .pagination .page-link,
        .pagination .page-item > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .pagination .page-item.active > span {
            background: var(--primary-soft);
            border-color: #bcd3f7;
            color: var(--primary);
        }
        .pagination .page-item.disabled > span {
            color: var(--muted);
            background: #f3f6fb;
        }
        .pagination .page-link:hover {
            background: #eef3f9;
        }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>All Courses ({{ $courses->total() }})</h1>
                    <p>Create and manage courses with category and subcategory.</p>
                </div>
                <div class="actions-row">
                    <div class="filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="courseFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="filter-panel" id="courseFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('courses.index') }}" id="courseFilterForm">
                                <div class="filter-field">
                                    <label>Main Category</label>
                                    <select name="category_id" id="courseCategoryFilter" data-active="{{ (string) $activeCategoryId }}">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) $activeCategoryId === (string) $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" id="courseSubcategoryFilter" data-active="{{ (string) $activeSubcategoryId }}">
                                        <option value="">All Subcategories</option>
                                        @foreach ($categories as $category)
                                            @foreach ($category->children as $sub)
                                                <option value="{{ $sub->id }}" data-parent="{{ $category->id }}" @selected((string) $activeSubcategoryId === (string) $sub->id)>
                                                    {{ $category->name }} / {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-actions">
                                    <a class="btn btn-soft" href="{{ route('courses.index') }}">Clear</a>
                                    <button class="btn" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if ($canManage)
                        <button type="button" class="btn category-create-btn" data-modal-open="modal-course-create">+ Add Course</button>
                    @endif
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Course List</h2>
            </div>

            @if ($isTrainer)
                <div class="category-head">
                    <h2>Browse by Category</h2>
                    <p>Select a main category, then choose a subcategory to filter courses.</p>
                </div>
                <div class="tab-row centered" id="categoryTabs" style="margin-top: 12px;">
                    @foreach ($categories as $index => $category)
                        <button class="tab-btn main-tab {{ $index === 0 ? 'active' : '' }}" type="button" data-tab="{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
                @foreach ($categories as $index => $category)
                    @php
                        $tabCourses = $category->courses
                            ->concat($category->children->flatMap->courses)
                            ->unique('id')
                            ->values();
                    @endphp
                    <div class="tab-panel {{ $index === 0 ? 'active' : '' }}" data-tab-panel="{{ $category->id }}">
                        <div class="subtab-label">Subcategories</div>
                        <div class="subtab-row" data-subtabs>
                            <button class="subtab-btn active" type="button" data-subtab="all">All</button>
                            @foreach ($category->children as $child)
                                <button class="subtab-btn" type="button" data-subtab="{{ $child->id }}">{{ $child->name }}</button>
                            @endforeach
                        </div>
                        <div class="category-divider"></div>
                        <div class="course-grid">
                            @forelse ($tabCourses as $course)
                                @php
                                    $thumb = $course->thumbnail_url ?: '';
                                    $bg = $thumb
                                        ? "url('{$thumb}')"
                                        : 'linear-gradient(120deg, #1c5fca, #3aa77a)';
                                    $assigned = in_array($course->id, $assignedCourseIds, true);
                                    $courseCategory = $course->subcategory?->name ?? $course->category?->name ?? $category->name;
                                    $subCategoryId = $course->subcategory?->id ? (string) $course->subcategory->id : 'none';
                                @endphp
                                @if ($assigned)
                                    <a class="course-tile" href="{{ route('courses.show', $course) }}" data-subcat="{{ $subCategoryId }}">
                                        <div class="course-tile-top" style="background-image: {{ $bg }};">
                                            <h3>{{ $course->title }}</h3>
                                        </div>
                                        <div class="course-tile-body">
                                            <div class="course-tile-meta">Category: {{ $courseCategory }}</div>
                                            <div class="btn btn-soft" style="width: fit-content;">Open Course</div>
                                        </div>
                                    </a>
                                @else
                                    <div class="course-tile disabled" data-subcat="{{ $subCategoryId }}">
                                        <div class="course-tile-top" style="background-image: {{ $bg }};">
                                            <h3>{{ $course->title }}</h3>
                                        </div>
                                        <div class="course-tile-body">
                                            <div class="course-tile-meta">Category: {{ $courseCategory }}</div>
                                            <span class="badge-lock">Locked</span>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="muted">No courses in this category.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>Course</th>
                            <th>Language</th>
                            <th>Duration</th>
                            <th>Created By</th>
                            @if ($canManage)<th>Actions</th>@endif
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td>
                                    <div><strong>{{ $course->category?->name ?? '-' }}</strong></div>
                                    <div class="muted">{{ $course->subcategory?->name ? 'Sub: '.$course->subcategory->name : 'No subcategory' }}</div>
                                </td>
                                <td>
                                    <div class="actions-row">
                                        @if ($course->thumbnail_url)
                                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="course-thumb" loading="lazy">
                                        @else
                                            <div class="course-thumb-placeholder">NO IMAGE</div>
                                        @endif
                                        <div class="course-title">
                                            <span class="name">{{ $course->title }}</span>
                                            <span class="meta">{{ $course->short_description ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-8">
                                        <a class="btn btn-soft" href="{{ route('courses.show', $course) }}">Open Course</a>
                                    </div>
                                </td>
                                <td>{{ $course->language ?: '-' }}</td>
                                <td>{{ $course->duration_hours }}h</td>
                                <td>{{ $course->creator?->name ?? 'N/A' }}</td>
                                @if ($canManage)
                                    <td>
                                        <div class="row-actions">
                                            <button type="button" class="btn-mini" data-modal-open="modal-course-edit-{{ $course->id }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                                </svg>
                                                <span>Edit</span>
                                            </button>
                                            <button type="button" class="btn-mini danger" data-modal-open="modal-course-delete-{{ $course->id }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManage ? 6 : 5 }}">No courses found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-10">
                {{ $courses->links('pagination.custom') }}
            </div>
        </section>
    </div>

    @if ($canManage)
        <div class="modal-overlay" id="modal-course-create" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Add Course</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-course-create" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data" class="stack form-premium js-course-form">
                        @csrf
                        <div class="form-grid">
                            <div class="field">
                                <label>Main Category</label>
                                <select name="category_id" class="js-main-category" required>
                                    <option value="">Select main category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Subcategory</label>
                                <select name="subcategory_id" class="js-subcategory">
                                    <option value="">Select subcategory (optional)</option>
                                    @foreach ($categories as $category)
                                        @foreach ($category->children as $sub)
                                            <option value="{{ $sub->id }}" data-parent="{{ $category->id }}">{{ $category->name }} / {{ $sub->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Course Title</label>
                                <input type="text" name="title" required>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="field">
                                <label>Course Language</label>
                                <input type="text" name="language" placeholder="English / Hindi / Gujarati">
                            </div>
                            <div class="field">
                                <label>Thumbnail Image</label>
                                <input type="file" name="thumbnail" accept="image/*">
                            </div>
                            <div class="field">
                                <label>Duration (hours)</label>
                                <input type="number" min="1" name="duration_hours" required>
                            </div>
                        </div>
                        <div class="field">
                            <label>Short Description</label>
                            <textarea name="short_description" rows="2"></textarea>
                        </div>
                        <div class="field">
                            <label>Description</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        <div class="actions-row">
                            <button class="btn" type="submit">Create Course</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-course-create">Close</button>
                </div>
            </div>
        </div>

        @foreach ($courses as $course)
            <div class="modal-overlay" id="modal-course-edit-{{ $course->id }}" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Edit Course</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-course-edit-{{ $course->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data" class="stack form-premium js-course-form">
                            @csrf
                            @method('PUT')
                            <div class="form-grid">
                                <div class="field">
                                    <label>Main Category</label>
                                    <select name="category_id" class="js-main-category" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected($course->category_id === $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" class="js-subcategory">
                                        <option value="">Select subcategory (optional)</option>
                                        @foreach ($categories as $category)
                                            @foreach ($category->children as $sub)
                                                <option value="{{ $sub->id }}" data-parent="{{ $category->id }}" @selected($course->subcategory_id === $sub->id)>
                                                    {{ $category->name }} / {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Course Title</label>
                                    <input type="text" name="title" value="{{ $course->title }}" required>
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="field">
                                    <label>Course Language</label>
                                    <input type="text" name="language" value="{{ $course->language }}" placeholder="Course language">
                                </div>
                                <div class="field">
                                    <label>Thumbnail Image</label>
                                    <input type="file" name="thumbnail" accept="image/*">
                                </div>
                                <div class="field">
                                    <label>Duration (hours)</label>
                                    <input type="number" min="1" name="duration_hours" value="{{ $course->duration_hours }}" required>
                                </div>
                            </div>
                            <div class="field">
                                <label>Short Description</label>
                                <textarea name="short_description" rows="2">{{ $course->short_description }}</textarea>
                            </div>
                            <div class="field">
                                <label>Description</label>
                                <textarea name="description" rows="2">{{ $course->description }}</textarea>
                            </div>
                            <div class="actions-row">
                                <button class="btn" type="submit">Update Course</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-course-edit-{{ $course->id }}">Close</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="modal-course-delete-{{ $course->id }}" aria-hidden="true">
                <div class="modal modal-sm" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Delete Course</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-course-delete-{{ $course->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <p class="muted">Are you sure you want to delete <strong>{{ $course->title }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-course-delete-{{ $course->id }}">Cancel</button>
                        <form method="POST" action="{{ route('courses.destroy', $course) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <script src="{{ asset('js/category.js') }}" defer></script>
    <script src="{{ asset('js/filters.js') }}" defer></script>
    @if ($isTrainer)
        <script src="{{ asset('js/student-courses.js') }}" defer></script>
    @endif
@endsection



