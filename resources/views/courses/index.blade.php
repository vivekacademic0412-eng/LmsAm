@extends('layouts.app')

@section('content')
    <style>
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
        .modal-overlay.open { display: flex; }
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
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>All Courses ({{ $courses->count() }})</h1>
                    <p>Create and manage courses with category and subcategory.</p>
                </div>
                @if ($canManage)
                    <button type="button" class="btn category-create-btn" data-modal-open="modal-course-create">+ Add Course</button>
                @endif
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Course List</h2>
            </div>
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
    <script>
        (function () {
            function bindDependentSubcategory(scope) {
                var mainSelect = scope.querySelector('.js-main-category');
                var subSelect = scope.querySelector('.js-subcategory');
                if (!mainSelect || !subSelect) {
                    return;
                }

                var optionNodes = Array.from(subSelect.querySelectorAll('option[data-parent]'));

                function sync() {
                    var parentId = mainSelect.value;

                    optionNodes.forEach(function (option) {
                        var show = parentId !== '' && option.getAttribute('data-parent') === parentId;
                        option.hidden = !show;
                    });

                    if (subSelect.value) {
                        var selected = subSelect.options[subSelect.selectedIndex];
                        if (selected && selected.getAttribute('data-parent') !== parentId) {
                            subSelect.value = '';
                        }
                    }
                }

                mainSelect.addEventListener('change', sync);
                sync();
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form.js-course-form').forEach(bindDependentSubcategory);
            });
        })();
    </script>
@endsection
