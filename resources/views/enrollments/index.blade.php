@extends('layouts.app')

@section('content')
    <style>
        .row-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
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
        .btn-mini.danger:hover {
            border-color: #d8b8b8;
            background: #f9f1f1;
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
        .table-wrap table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-wrap thead th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #5b6a82;
            background: #f5f7fb;
            border-bottom: 1px solid #dbe2ee;
            padding: 12px 14px;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .table-wrap tbody td {
            padding: 14px;
            border-bottom: 1px solid #e7edf6;
            color: #1f2f48;
        }
        .table-wrap tbody tr:nth-child(even) td {
            background: #fbfcfe;
        }
        .table-wrap tbody tr:hover td {
            background: #f1f6ff;
        }
        .table-wrap .id-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 28px;
            padding: 0 8px;
            border-radius: 999px;
            background: #eef2fb;
            color: #2a3b5f;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid #d6deee;
        }
    </style>
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Enrollment Management</h1>
                    <p>Assign students to courses and optionally attach trainers.</p>
                </div>
                <div class="actions-row">
                    <div class="filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="enrollmentFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="filter-panel" id="enrollmentFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('enrollments.index') }}" id="enrollmentFilterForm">
                                <div class="filter-field">
                                    <label>Category</label>
                                    <select name="category_id" id="enrollmentCategoryFilter">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) $activeCategoryId === (string) $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" id="enrollmentSubcategoryFilter">
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
                                <div class="filter-field">
                                    <label>Course</label>
                                    <select name="course_id" id="enrollmentCourseFilter">
                                        <option value="">All Courses</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}" @selected((string) $activeCourseId === (string) $course->id)>{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Trainer</label>
                                    <select name="trainer_id" id="enrollmentTrainerFilter">
                                        <option value="">All Trainers</option>
                                        @foreach ($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" @selected((string) $activeTrainerId === (string) $trainer->id)>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-actions">
                                    <a class="btn btn-soft" href="{{ route('enrollments.index') }}">Clear</a>
                                    <button class="btn" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <button type="button" class="btn btn-soft" data-modal-open="modal-enrollment-create">+ Assign Enrollment</button>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Assigned Enrollments</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Student</th>
                        <th>Trainer</th>
                        <th>Assigned By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td><span class="id-pill">#{{ $enrollment->id }}</span></td>
                            <td>{{ $enrollment->course?->title }}</td>
                            <td>{{ $enrollment->student?->name }}</td>
                            <td>{{ $enrollment->trainer?->name ?? 'Not Assigned' }}</td>
                            <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                            <td>
                                <div class="row-actions">
                                    <form method="POST" action="{{ route('enrollments.resend-email', $enrollment) }}">
                                        @csrf
                                        <button type="submit" class="btn-mini">Resend Email</button>
                                    </form>
                                    <button type="button" class="btn-mini" data-modal-open="modal-enrollment-edit-{{ $enrollment->id }}">Edit</button>
                                    <button type="button" class="btn-mini danger" data-modal-open="modal-enrollment-delete-{{ $enrollment->id }}">Remove</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No enrollments yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-10">
                {{ $enrollments->links('pagination.custom') }}
            </div>
        </section>
    </div>

    <div class="modal-overlay" id="modal-enrollment-create" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head">
                <h3>Assign Enrollment</h3>
                <button type="button" class="modal-close" data-modal-close="modal-enrollment-create" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('enrollments.store') }}" class="stack form-premium">
                    @csrf
                    <div class="form-grid">
                        <div class="field">
                            <label>Course</label>
                            <select name="course_id" required>
                                <option value="">Select course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Student</label>
                            <select name="student_id" required>
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Trainer</label>
                            <select name="trainer_id">
                                <option value="">No trainer</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="actions-row">
                        <button class="btn" type="submit">Assign Enrollment</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft" data-modal-close="modal-enrollment-create">Close</button>
            </div>
        </div>
    </div>

    @foreach ($enrollments as $enrollment)
        <div class="modal-overlay" id="modal-enrollment-edit-{{ $enrollment->id }}" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Edit Enrollment</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-enrollment-edit-{{ $enrollment->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('enrollments.update', $enrollment) }}" class="stack form-premium">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="field">
                                <label>Course</label>
                                <select name="course_id" required>
                                    <option value="">Select course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected($enrollment->course_id === $course->id)>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Student</label>
                                <select name="student_id" required>
                                    <option value="">Select student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected($enrollment->student_id === $student->id)>{{ $student->name }} ({{ $student->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Trainer</label>
                                <select name="trainer_id">
                                    <option value="">No trainer</option>
                                    @foreach ($trainers as $trainer)
                                        <option value="{{ $trainer->id }}" @selected($enrollment->trainer_id === $trainer->id)>{{ $trainer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="actions-row">
                            <button class="btn btn-soft" type="submit">Update</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-enrollment-edit-{{ $enrollment->id }}">Close</button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="modal-enrollment-delete-{{ $enrollment->id }}" aria-hidden="true">
            <div class="modal modal-sm" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Remove Enrollment</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-enrollment-delete-{{ $enrollment->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <p class="muted">Remove <strong>{{ $enrollment->student?->name }}</strong> from <strong>{{ $enrollment->course?->title }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-enrollment-delete-{{ $enrollment->id }}">Cancel</button>
                    <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="{{ asset('js/course-modals.js') }}" defer></script>
    <script src="{{ asset('js/filters.js') }}" defer></script>
@endsection
