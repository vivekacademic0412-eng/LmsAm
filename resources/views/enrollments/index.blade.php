@extends('layouts.app')

@section('content')
    <style>
        /* ═══════════════════════════════════════════════
           ENROLLMENTS — THEME-BASED STYLES
           (uses CSS vars from the global theme: --card, --line,
           --muted, --text, --primary, --radius, --shadow-card, ...)
        ═══════════════════════════════════════════════ */
        .row-actions { display: flex; flex-wrap: wrap; gap: 8px; }

        .btn-mini {
            border: 1px solid var(--line);
            border-radius: var(--radius-xs, 10px);
            background: var(--bg-card2, var(--card));
            color: var(--text);
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
        .btn-mini:hover { border-color: var(--primary); background: var(--primary-glow); transform: translateY(-1px); }
        .btn-mini.danger { color: var(--danger); }
        .btn-mini.danger:hover { border-color: var(--danger); background: rgba(220, 38, 38, .08); }

        /* Filters */
        .filter-wrap { position: relative; }
        .filter-btn {
            display: inline-flex; align-items: center; gap: 8px;
            border: 1px solid var(--line); background: var(--card);
            color: var(--text); border-radius: var(--radius-xs, 10px);
            padding: 9px 14px; font-weight: 600; font-size: 14px; cursor: pointer;
            transition: 160ms ease;
        }
        .filter-btn:hover { border-color: var(--primary); }
        .filter-btn svg { width: 16px; height: 16px; }
        .filter-panel {
            position: absolute; right: 0; top: calc(100% + 8px);
            width: min(560px, 90vw); background: var(--card);
            border: 1px solid var(--line); border-radius: var(--radius-sm, 12px);
            box-shadow: var(--shadow-card); padding: 16px; z-index: 40;
            display: none; opacity: 0; transform: translateY(-6px);
            transition: opacity 160ms ease, transform 160ms ease;
        }
        .filter-panel.open { display: block; }
        .filter-panel.show { opacity: 1; transform: translateY(0); }
        .filter-panel form { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .filter-field { display: flex; flex-direction: column; gap: 6px; }
        .filter-field label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
        .filter-field select {
            border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--card));
            color: var(--text); border-radius: var(--radius-xs, 8px); padding: 9px 10px; font-size: 14px;
        }
        .filter-field select:focus { outline: none; border-color: var(--input-focus, var(--primary)); }
        .filter-actions { grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 8px; margin-top: 4px; }

        /* Modal — smooth open */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(8, 15, 28, .56);
            backdrop-filter: blur(3px); display: none; align-items: center;
            justify-content: center; padding: 18px; z-index: 120;
            opacity: 0; transition: opacity 200ms ease;
        }
        .modal-overlay.open { display: flex; }
        .modal-overlay.show { opacity: 1; }
        .modal {
            width: min(860px, 100%); max-height: calc(100vh - 36px); overflow: auto;
            border-radius: var(--radius, 16px); border: 1px solid var(--line);
            background: var(--card); box-shadow: var(--shadow);
            transform: scale(.94) translateY(10px); opacity: 0;
            transition: transform 220ms cubic-bezier(.16,1,.3,1), opacity 200ms ease;
        }
        .modal-overlay.show .modal { transform: scale(1) translateY(0); opacity: 1; }
        .modal.modal-sm { width: min(460px, 100%); }
        .modal-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px; border-bottom: 1px solid var(--line);
        }
        .modal-head h3 { margin: 0; font-size: 22px; color: var(--text); }
        .modal-close { border: 0; background: transparent; color: var(--muted); font-size: 26px; line-height: 1; cursor: pointer; }
        .modal-close:hover { color: var(--danger); }
        .modal-body { padding: 14px 16px 16px; }
        .modal-body .form-premium { padding: 16px; border-radius: var(--radius-sm, 14px); }
        .modal-footer { display: flex; justify-content: flex-end; border-top: 1px solid var(--line); padding: 12px 16px; gap: 8px; }

        /* Form validation states */
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 13px; font-weight: 600; color: var(--muted); }
        .field label .req { color: var(--danger); }
        .field select, .field input {
            border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--card));
            color: var(--text); border-radius: var(--radius-xs, 8px); padding: 10px 12px; font-size: 14px;
            transition: border-color 140ms ease, box-shadow 140ms ease;
        }
        .field select:focus, .field input:focus {
            outline: none; border-color: var(--input-focus, var(--primary));
            box-shadow: 0 0 0 3px var(--primary-glow, rgba(13,93,209,.12));
        }
        .field.is-invalid select, .field.is-invalid input { border-color: var(--danger); }
        .field .error-text { display: none; font-size: 12px; color: var(--danger); }
        .field.is-invalid .error-text { display: block; }

        /* Table */
        .table-wrap table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-wrap thead th {
            text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .6px;
            color: var(--muted); background: var(--bg-card2, var(--bg2)); border-bottom: 1px solid var(--line);
            padding: 12px 14px; position: sticky; top: 0; z-index: 1;
        }
        .table-wrap tbody td { padding: 14px; border-bottom: 1px solid var(--line); color: var(--text); }
        .table-wrap tbody tr:nth-child(even) td { background: var(--bg-card2, var(--bg2)); }
        .table-wrap tbody tr:hover td { background: var(--primary-glow, rgba(13,93,209,.06)); }
        .table-wrap .id-pill {
            display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 28px;
            padding: 0 8px; border-radius: 999px; background: var(--bg-card2, var(--bg2)); color: var(--text);
            font-weight: 700; font-size: 12px; border: 1px solid var(--line);
        }
        .badge-empty { padding: 2px 8px; border-radius: 999px; background: var(--bg-card2, var(--bg2)); color: var(--muted); font-size: 12px; }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Enrollment Management</h1>
                    <p>Assign students to courses and optionally attach trainers.</p>
                </div>
                <div class="row">
                  
                        {{-- <button type="button" class="filter-btn" id="enrollmentFilterToggle" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button> --}}
                        {{-- <div class="filter-panel" id="enrollmentFilterPanel" aria-hidden="true"> --}}
                             
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
                        
                    
                    {{-- <button type="button" class="btn btn-soft" data-modal-open="modal-enrollment-create">+ Assign Enrollment</button> --}}
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
                            <td>
                                @if($enrollment->trainer)
                                    {{ $enrollment->trainer->name }}
                                @else
                                    <span class="badge-empty">Not Assigned</span>
                                @endif
                            </td>
                            <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                            <td>
                                <div class="row-actions">
                                    <form method="POST" action="{{ route('enrollments.resend-email', $enrollment) }}" class="resend-form">
                                        @csrf
                                        <button type="submit" class="btn-mini">Resend Email</button>
                                    </form>
                                    <button type="button" class="btn-mini" data-modal-open="modal-enrollment-edit-{{ $enrollment->id }}">Edit</button>
                                    <button type="button" class="btn-mini danger delete-trigger"
                                            data-delete-form="delete-form-{{ $enrollment->id }}"
                                            data-student="{{ $enrollment->student?->name }}"
                                            data-course="{{ $enrollment->course?->title }}">Remove</button>
                                    <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" id="delete-form-{{ $enrollment->id }}" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
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

    {{-- Create Modal --}}
    <div class="modal-overlay" id="modal-enrollment-create" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head">
                <h3>Assign Enrollment</h3>
                <button type="button" class="modal-close" data-modal-close="modal-enrollment-create" aria-label="Close">x</button>
            </div>
               <form method="POST" action="{{ route('enrollments.store') }}" class="stack form-premium needs-validation" novalidate>
            <div class="modal-body">
             
                    @csrf
                    <div class="form-grid">
                        <div class="field" data-field="course_id">
                            <label>Course <span class="req">*</span></label>
                            <select name="course_id" required>
                                <option value="">Select course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            <span class="error-text">Please select a course.</span>
                        </div>
                        <div class="field" data-field="student_id">
                            <label>Student <span class="req">*</span></label>
                            <select name="student_id" required>
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            <span class="error-text">Please select a student.</span>
                        </div>
                        <div class="field" data-field="trainer_id">
                            <label>Trainer</label>
                            <select name="trainer_id">
                                <option value="">No trainer</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
              
            </div>
            <div class="modal-footer">
                 <button class="bt btn" type="submit">Assign Enrollment</button>
                <button type="button" class="btn btn-soft" data-modal-close="modal-enrollment-create">Close</button>
            </div>
              </form>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach ($enrollments as $enrollment)
        <div class="modal-overlay" id="modal-enrollment-edit-{{ $enrollment->id }}" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Edit Enrollment</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-enrollment-edit-{{ $enrollment->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('enrollments.update', $enrollment) }}" class="stack form-premium needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="field" data-field="course_id">
                                <label>Course <span class="req">*</span></label>
                                <select name="course_id" required>
                                    <option value="">Select course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected($enrollment->course_id === $course->id)>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text">Please select a course.</span>
                            </div>
                            <div class="field" data-field="student_id">
                                <label>Student <span class="req">*</span></label>
                                <select name="student_id" required>
                                    <option value="">Select student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected($enrollment->student_id === $student->id)>{{ $student->name }} ({{ $student->email }})</option>
                                    @endforeach
                                </select>
                                <span class="error-text">Please select a student.</span>
                            </div>
                            <div class="field" data-field="trainer_id">
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
    @endforeach

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function () {
            /* ── Session flash → SweetAlert toast ─────────────────── */
            @if(session('success'))
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: @json(session('success')), showConfirmButton: false,
                    timer: 2800, timerProgressBar: true
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'error',
                    title: @json(session('error')), showConfirmButton: false,
                    timer: 3200, timerProgressBar: true
                });
            @endif
            @if($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Please check the form',
                    html: @json(implode('<br>', $errors->all())),
                    confirmButtonText: 'Got it'
                });
            @endif

            /* ── Modal open/close (smooth) ─────────────────────────── */
            function openModal(id) {
                var el = document.getElementById(id);
                if (!el) return;
                el.classList.add('open');
                el.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(function () { el.classList.add('show'); });
                document.body.style.overflow = 'hidden';
            }
            function closeModal(id) {
                var el = document.getElementById(id);
                if (!el) return;
                el.classList.remove('show');
                el.setAttribute('aria-hidden', 'true');
                setTimeout(function () {
                    el.classList.remove('open');
                    document.body.style.overflow = '';
                }, 200);
            }

            document.addEventListener('click', function (e) {
                var openTrigger = e.target.closest('[data-modal-open]');
                if (openTrigger) { openModal(openTrigger.getAttribute('data-modal-open')); return; }

                var closeTrigger = e.target.closest('[data-modal-close]');
                if (closeTrigger) { closeModal(closeTrigger.getAttribute('data-modal-close')); return; }

                if (e.target.classList.contains('modal-overlay')) {
                    closeModal(e.target.id);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                document.querySelectorAll('.modal-overlay.open').forEach(function (el) { closeModal(el.id); });
            });

            /* Re-open the relevant modal automatically if validation failed server-side */
            @if($errors->any() && old('course_id'))
                openModal('modal-enrollment-create');
            @endif

            /* ── Client-side validation ────────────────────────────── */
            document.querySelectorAll('form.needs-validation').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    var valid = true;
                    form.querySelectorAll('[required]').forEach(function (input) {
                        var field = input.closest('.field');
                        if (!input.value) {
                            valid = false;
                            field && field.classList.add('is-invalid');
                        } else {
                            field && field.classList.remove('is-invalid');
                        }
                    });
                    if (!valid) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing information',
                            text: 'Please fill in all required fields before submitting.',
                        });
                    }
                });
                form.querySelectorAll('[required]').forEach(function (input) {
                    input.addEventListener('change', function () {
                        var field = input.closest('.field');
                        if (input.value) field && field.classList.remove('is-invalid');
                    });
                });
            });

            /* ── Delete confirmation via SweetAlert2 ───────────────── */
            document.querySelectorAll('.delete-trigger').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var formId = btn.getAttribute('data-delete-form');
                    var student = btn.getAttribute('data-student') || 'this student';
                    var course = btn.getAttribute('data-course') || 'this course';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remove enrollment?',
                        html: 'Remove <b>' + student + '</b> from <b>' + course + '</b>? This cannot be undone.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        reverseButtons: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            var form = document.getElementById(formId);
                            if (form) form.submit();
                        }
                    });
                });
            });

            /* ── Resend email confirmation toast ───────────────────── */
            document.querySelectorAll('.resend-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var f = form;
                    Swal.fire({
                        icon: 'question',
                        title: 'Resend enrollment email?',
                        showCancelButton: true,
                        confirmButtonText: 'Send',
                        cancelButtonText: 'Cancel'
                    }).then(function (result) {
                        if (result.isConfirmed) f.submit();
                    });
                });
            });

            /* ── Filter panel toggle ────────────────────────────────── */
            var filterToggle = document.getElementById('enrollmentFilterToggle');
            var filterPanel = document.getElementById('enrollmentFilterPanel');
            if (filterToggle && filterPanel) {
                filterToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var isOpen = filterPanel.classList.contains('open');
                    if (isOpen) {
                        filterPanel.classList.remove('show');
                        setTimeout(function () { filterPanel.classList.remove('open'); }, 160);
                        filterToggle.setAttribute('aria-expanded', 'false');
                    } else {
                        filterPanel.classList.add('open');
                        requestAnimationFrame(function () { filterPanel.classList.add('show'); });
                        filterToggle.setAttribute('aria-expanded', 'true');
                    }
                });
                document.addEventListener('click', function (e) {
                    if (!filterPanel.contains(e.target) && e.target !== filterToggle) {
                        filterPanel.classList.remove('show');
                        setTimeout(function () { filterPanel.classList.remove('open'); }, 160);
                        filterToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            /* ── Cascading category → subcategory filter ───────────── */
            var categoryFilter = document.getElementById('enrollmentCategoryFilter');
            var subcategoryFilter = document.getElementById('enrollmentSubcategoryFilter');
            if (categoryFilter && subcategoryFilter) {
                var allSubOptions = Array.prototype.slice.call(subcategoryFilter.options);
                function filterSubcategories() {
                    var parentId = categoryFilter.value;
                    subcategoryFilter.innerHTML = '';
                    allSubOptions.forEach(function (opt) {
                        if (!opt.value || !parentId || opt.getAttribute('data-parent') === parentId) {
                            subcategoryFilter.appendChild(opt.cloneNode(true));
                        }
                    });
                }
                categoryFilter.addEventListener('change', filterSubcategories);
            }
        })();
    </script>

    <script src="{{ asset('js/course-modals.js') }}" defer></script>
    <script src="{{ asset('js/filters.js') }}" defer></script>
@endsection