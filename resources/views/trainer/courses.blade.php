@extends('layouts.app')

@section('content')
    <style>
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
        .course-grid { display: grid; gap: 18px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
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
        @media (max-width: 980px) {
            .course-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .course-grid { grid-template-columns: 1fr; }
        }
    </style>
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Teacher Courses</h1>
                    <p>Your assigned courses appear first. Browse all courses by category below.</p>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Assigned Courses</h2>
            </div>
            <div class="course-grid">
                @forelse ($assignedCourses as $course)
                    @php
                        $thumb = $course->thumbnail_url ?: '';
                        $bg = $thumb
                            ? "url('{$thumb}')"
                            : 'linear-gradient(120deg, #1c5fca, #3aa77a)';
                    @endphp
                    <a class="course-tile" href="{{ route('courses.show', $course) }}">
                        <div class="course-tile-top" style="background-image: {{ $bg }};">
                            <h3>{{ $course->title }}</h3>
                        </div>
                        <div class="course-tile-body">
                            <div class="course-tile-meta">Open assigned course</div>
                            <div class="btn btn-soft" style="width: fit-content;">Open Course</div>
                        </div>
                    </a>
                @empty
                    <p class="muted">No courses assigned yet.</p>
                @endforelse
            </div>
        </section>

        <section class="card">
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
                                <div class="course-tile disabled" aria-disabled="true" data-subcat="{{ $subCategoryId }}">
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
        </section>
    </div>

    <script src="{{ asset('js/student-courses.js') }}" defer></script>
@endsection
