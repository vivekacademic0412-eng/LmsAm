@extends('layouts.app')

@section('content')
<div class="d-root">

    {{-- ================= HEADER ================= --}}
      <div class="d-admin-hero mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 d-hero-inner">

                <div>


                    <h2 class="mt-3 mb-2 d-hero-title">
                       All Courses ({{ $courses->total() }})
                    </h2>

                    <p class=" mb-0 d-hero-meta">
                       Create and manage courses with category and subcategory.
                    </p>

                </div>

            </div>

        </div>
    <section class="um-filter-bar">
        
            <div class="filter-group">

                {{-- FILTER --}}
                <div class="filter-wrap">
                    <button type="button"
                            class="btn btn-soft"
                            data-filter-toggle="courseFilterPanel">
                        ⚲ Filter
                    </button>

                    <div class="filter-panel" id="courseFilterPanel">

                        <form method="GET" action="{{ route('courses.index') }}" id="courseFilterForm">

                            <div class="filter-field">
                                <label>Main Category</label>
                                <select name="category_id" id="courseCategoryFilter">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @selected((string)$activeCategoryId === (string)$category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field">
                                <label>Subcategory</label>
                                <select name="subcategory_id" id="courseSubcategoryFilter">
                                    <option value="">All Subcategories</option>

                                    @foreach ($categories as $category)
                                        @foreach ($category->children as $sub)
                                            <option value="{{ $sub->id }}"
                                                    data-parent="{{ $category->id }}"
                                                @selected((string)$activeSubcategoryId === (string)$sub->id)>
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

                {{-- ADD COURSE --}}
                @if ($canManage)
                    <button type="button"
                            class="btn"
                            data-modal-open="modal-course-create">
                        + Add Course
                    </button>
                @endif

            </div>
      
    </section>

    {{-- ================= CONTENT ================= --}}
   {{-- ================= CONTENT ================= --}}
<section class="um-table-card">

    {{-- ================= HEADER ================= --}}
    <div class="um-table-head">
        <h2>
            Course List
            <span class="um-count-badge">{{ $courses->total() }}</span>
        </h2>
    </div>

    {{-- ================= TRAINER VIEW ================= --}}
    @if ($isTrainer)

        <div class="category-head">
            <h2>Browse by Category</h2>
            <p>Select a category and subcategory to filter courses</p>
        </div>

        {{-- MAIN TABS --}}
        <div class="tab-row centered" id="categoryTabs">
            @foreach ($categories as $index => $category)
                <button class="tab-btn main-tab {{ $index === 0 ? 'active' : '' }}"
                        type="button"
                        data-tab="{{ $category->id }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- CATEGORY PANELS --}}
        @foreach ($categories as $index => $category)

            @php
                $tabCourses = $category->courses
                    ->concat($category->children->flatMap->courses)
                    ->unique('id')
                    ->values();
            @endphp

            <div class="tab-panel {{ $index === 0 ? 'active' : '' }}"
                 data-tab-panel="{{ $category->id }}">

                {{-- SUB TABS --}}
                <div class="subtab-label">Subcategories</div>

                <div class="subtab-row" data-subtabs>
                    <button class="subtab-btn active" data-subtab="all">All</button>

                    @foreach ($category->children as $child)
                        <button class="subtab-btn"
                                data-subtab="{{ $child->id }}">
                            {{ $child->name }}
                        </button>
                    @endforeach
                </div>

                <div class="category-divider"></div>

                {{-- COURSE GRID INSIDE TABLE STYLE WRAP --}}
                <div class="um-table-wrap">

                    <div class="course-grid">

                        @forelse ($tabCourses as $course)

                            @php
                                $thumb = $course->thumbnail_url ?: '';
                                $bg = $thumb
                                    ? "url('{$thumb}')"
                                    : 'linear-gradient(135deg, var(--accent), var(--accent-dark))';

                                $assigned = in_array($course->id, $assignedCourseIds, true);

                                $courseCategory = $course->subcategory?->name
                                    ?? $course->category?->name
                                    ?? $category->name;

                                $subCategoryId = $course->subcategory?->id
                                    ? (string)$course->subcategory->id
                                    : 'none';
                            @endphp

                            @if ($assigned)
                                <a class="course-tile"
                                   href="{{ route('courses.show', $course) }}"
                                   data-subcat="{{ $subCategoryId }}">

                                    <div class="course-tile-top"
                                         style="background-image: {{ $bg }};">
                                        <h3>{{ $course->title }}</h3>
                                    </div>

                                    <div class="course-tile-body">
                                        <div class="course-tile-meta">
                                            {{ $courseCategory }}
                                        </div>

                                        <span class="btn btn-soft">Open Course</span>
                                    </div>
                                </a>
                            @else
                                <div class="course-tile disabled"
                                     data-subcat="{{ $subCategoryId }}">

                                    <div class="course-tile-top"
                                         style="background-image: {{ $bg }};">
                                        <h3>{{ $course->title }}</h3>
                                    </div>

                                    <div class="course-tile-body">
                                        <div class="course-tile-meta">
                                            {{ $courseCategory }}
                                        </div>

                                        <span class="badge-lock">Locked</span>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <div class="um-empty">
                                <p>No courses found in this category</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        @endforeach

    {{-- ================= ADMIN TABLE VIEW ================= --}}
    @else

        <div class="um-table-wrap">

            <table>

                <thead>
                <tr>
                    <th>Category</th>
                    <th>Course</th>
                    <th>Language</th>
                    <th>Duration</th>
                    <th>Created By</th>
                    @if ($canManage)
                        <th>Actions</th>
                    @endif
                </tr>
                </thead>

                <tbody>
                @forelse ($courses as $course)

                    <tr>

                        <td>
                            <strong>{{ $course->category?->name ?? '-' }}</strong>
                            <div class="muted">
                                {{ $course->subcategory?->name ? 'Sub: '.$course->subcategory->name : 'No subcategory' }}
                            </div>
                        </td>

                        <td>
                            <div class="actions-row">

                                @if ($course->thumbnail_url)
                                    <img src="{{ $course->thumbnail_url }}"
                                         class="course-thumb">
                                @else
                                    <div class="course-thumb-placeholder">NO IMAGE</div>
                                @endif

                                <div class="course-title">
                                    <span class="name">{{ $course->title }}</span>
                                    <span class="meta">{{ $course->short_description }}</span>
                                </div>

                            </div>

                            <a class="btn btn-soft mt-8"
                               href="{{ route('courses.show', $course) }}">
                                Open Course
                            </a>
                        </td>

                        <td>{{ $course->language ?: '-' }}</td>
                        <td>{{ $course->duration_hours }}h</td>
                        <td>{{ $course->creator?->name ?? 'N/A' }}</td>

                        @if ($canManage)
                            <td>
                                <div class="row-actions">
                                    <button class="btn-mini"
                                            data-modal-open="modal-course-edit-{{ $course->id }}">
                                        Edit
                                    </button>

                                    <button class="btn-mini danger"
                                            data-modal-open="modal-course-delete-{{ $course->id }}">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        @endif

                    </tr>

                @empty
                    <tr>
                        <td colspan="6">
                            <div class="um-empty">
                                No courses found
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>

    @endif

    {{-- ================= PAGINATION ================= --}}
    <div class="um-pagination">
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



