@extends('layouts.app')

@section('content')
    <style>
        /* ═══════════════════════════════════════════════
           ENROLLMENTS — SCOPED THEME-BASED STYLES
           All page-specific classes are prefixed `enrollment-`
           so they never collide with other modules' styles.
           Uses CSS vars from the global theme: --card, --line,
           --muted, --text, --primary, --radius, --shadow-card, ...
        ═══════════════════════════════════════════════ */

        /* ── Page card wrapper (extends global .card) ─────────── */
        .enrollment-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius, 16px);
            box-shadow: var(--shadow-card);
            margin:5px 0px;
        }

        .enrollment-page-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--line);
        }
        .enrollment-page-head h1 { font-size: 22px; color: var(--text); margin-bottom: 4px; }
        .enrollment-page-head h2 { font-size: 17px; color: var(--text); font-weight: 700; }
        .enrollment-page-head p { color: var(--muted); font-size: 14px; }

        /* ── Stat cards row ─────────────────────────────────────── */
        .enrollment-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }
        @media (max-width: 960px) {
            .enrollment-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px) {
            .enrollment-stats-grid { grid-template-columns: 1fr; }
        }
        .enrollment-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-sm, 14px);
            box-shadow: var(--shadow-card);
        }
        .enrollment-stat-icon {
            flex: 0 0 auto;
            width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px;
            font-size: 19px;
        }
        .enrollment-stat-icon.total { background: var(--primary-glow, rgba(13, 93, 209, .12)); color: var(--primary); }
        .enrollment-stat-icon.students { background: rgba(16, 185, 129, .12); color: #10b981; }
        .enrollment-stat-icon.assigned { background: rgba(245, 158, 11, .14); color: #d97706; }
        .enrollment-stat-icon.unassigned { background: rgba(220, 38, 38, .1); color: var(--danger, #dc2626); }
        .enrollment-stat-body { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .enrollment-stat-value { font-size: 22px; font-weight: 800; color: var(--text); line-height: 1.1; }
        .enrollment-stat-label {
            font-size: 11.5px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .5px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* ── Table card header: title + single-row filters ────── */
        .enrollment-table-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            background: var(--bg-card2, var(--bg2));
        }
        .enrollment-table-head-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
        }
        .enrollment-table-head-top .enrollment-count-pill {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--muted);
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 4px 12px;
        }

        .enrollment-filter-bar {
            display: flex;
            flex-wrap: nowrap;
            align-items: flex-end;
            gap: 10px;
            width: 100%;
            margin-top: 14px;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .enrollment-filter-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1 1 170px;
            min-width: 150px;
        }
        .enrollment-filter-field label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .enrollment-filter-field select {
            border: 1px solid var(--input-border, var(--line));
            background: var(--input-bg, var(--card));
            color: var(--text);
            border-radius: var(--radius-xs, 8px);
            padding: 9px 10px;
            font-size: 14px;
            width: 100%;
        }
        .enrollment-filter-field select:focus {
            outline: none;
            border-color: var(--input-focus, var(--primary));
            box-shadow: 0 0 0 3px var(--primary-glow, rgba(13, 93, 209, .12));
        }
        .enrollment-filter-actions {
            display: flex;
            gap: 8px;
            flex: 0 0 auto;
        }
        .enrollment-filter-actions .btn,
        .enrollment-filter-actions .btn-soft { white-space: nowrap; }

        @media (max-width: 720px) {
            .enrollment-table-head-top { flex-direction: column; align-items: stretch; }
            .enrollment-filter-bar { flex-wrap: wrap; overflow-x: visible; }
            .enrollment-filter-actions { justify-content: flex-end; width: 100%; }
        }

        /* ── Row action buttons ────────────────────────────────── */
        .enrollment-row-actions { display: flex; flex-wrap: wrap; gap: 8px; }

        .enrollment-btn-mini {
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
        .enrollment-btn-mini:hover { border-color: var(--primary); background: var(--primary-glow); transform: translateY(-1px); }
        .enrollment-btn-mini.danger { color: var(--danger); }
        .enrollment-btn-mini.danger:hover { border-color: var(--danger); background: rgba(220, 38, 38, .08); }

        /* ── Modal — smooth open ───────────────────────────────── */
        .enrollment-modal-overlay {
            position: fixed; inset: 0; background: rgba(8, 15, 28, .56);
            backdrop-filter: blur(3px); display: none; align-items: center;
            justify-content: center; padding: 18px; z-index: 120;
            opacity: 0; transition: opacity 200ms ease;
        }
        .enrollment-modal-overlay.open { display: flex; }
        .enrollment-modal-overlay.show { opacity: 1; }
        .enrollment-modal {
            width: min(860px, 100%); max-height: calc(100vh - 36px); overflow: auto;
            border-radius: var(--radius, 16px); border: 1px solid var(--line);
            background: var(--card); box-shadow: var(--shadow);
            transform: scale(.94) translateY(10px); opacity: 0;
            transition: transform 220ms cubic-bezier(.16, 1, .3, 1), opacity 200ms ease;
        }
        .enrollment-modal-overlay.show .enrollment-modal { transform: scale(1) translateY(0); opacity: 1; }
        .enrollment-modal.enrollment-modal-sm { width: min(460px, 100%); }
        .enrollment-modal-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px; border-bottom: 1px solid var(--line);
        }
        .enrollment-modal-head h3 { margin: 0; font-size: 22px; color: var(--text); }
        .enrollment-modal-close { border: 0; background: transparent; color: var(--muted); font-size: 26px; line-height: 1; cursor: pointer; }
        .enrollment-modal-close:hover { color: var(--danger); }
        .enrollment-modal-body { padding: 14px 16px 16px; }
        .enrollment-modal-body .form-premium { padding: 16px; border-radius: var(--radius-sm, 14px); }
        .enrollment-modal-footer { display: flex; justify-content: flex-end; border-top: 1px solid var(--line); padding: 12px 16px; gap: 8px; }

        /* ── Form validation states ────────────────────────────── */
        .enrollment-field { display: flex; flex-direction: column; gap: 6px; }
        .enrollment-field label { font-size: 13px; font-weight: 600; color: var(--muted); }
        .enrollment-field label .req { color: var(--danger); }
        .enrollment-field select, .enrollment-field input {
            border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--card));
            color: var(--text); border-radius: var(--radius-xs, 8px); padding: 10px 12px; font-size: 14px;
            transition: border-color 140ms ease, box-shadow 140ms ease;
        }
        .enrollment-field select:focus, .enrollment-field input:focus {
            outline: none; border-color: var(--input-focus, var(--primary));
            box-shadow: 0 0 0 3px var(--primary-glow, rgba(13, 93, 209, .12));
        }
        .enrollment-field.is-invalid select, .enrollment-field.is-invalid input { border-color: var(--danger); }
        .enrollment-field .enrollment-error-text { display: none; font-size: 12px; color: var(--danger); }
        .enrollment-field.is-invalid .enrollment-error-text { display: block; }

        /* ── Table ──────────────────────────────────────────────── */
        .enrollment-table-wrap { overflow-x: auto; }
        .enrollment-table-wrap table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .enrollment-table-wrap thead th {
            text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .6px;
            color: var(--muted); background: var(--bg-card2, var(--bg2)); border-bottom: 1px solid var(--line);
            padding: 12px 14px; position: sticky; top: 0; z-index: 1;
        }
        .enrollment-table-wrap tbody td { padding: 14px; border-bottom: 1px solid var(--line); color: var(--text); }
        .enrollment-table-wrap tbody tr:nth-child(even) td { background: var(--bg-card2, var(--bg2)); }
        .enrollment-table-wrap tbody tr:hover td { background: var(--primary-glow, rgba(13, 93, 209, .06)); }
        .enrollment-id-pill {
            display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 28px;
            padding: 0 8px; border-radius: 999px; background: var(--bg-card2, var(--bg2)); color: var(--text);
            font-weight: 700; font-size: 12px; border: 1px solid var(--line);
        }
        .enrollment-badge-empty { padding: 2px 8px; border-radius: 999px; background: var(--bg-card2, var(--bg2)); color: var(--muted); font-size: 12px; }
        .enrollment-badge-trainer {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 3px 10px; border-radius: 999px; font-size: 12.5px; font-weight: 600;
            background: rgba(16, 185, 129, .12); color: #10b981;
        }
    </style>

    @php
        /*
         | Prefer passing a $stats array from the controller for accurate,
         | database-wide counts, e.g.:
         |
         |   $stats = [
         |       'total_enrollments'   => Enrollment::count(),
         |       'total_students'      => Enrollment::distinct('student_id')->count('student_id'),
         |       'trainer_assigned'    => Enrollment::whereNotNull('trainer_id')->count(),
         |       'trainer_unassigned'  => Enrollment::whereNull('trainer_id')->count(),
         |   ];
         |
         | Falls back to computing from the current (paginated) page so the
         | view still works without controller changes — update the
         | controller for totals that are correct across all pages.
        */
        $totalEnrollments  = $stats['total_enrollments']  ?? $enrollments->total();
        $totalStudents     = $stats['total_students']     ?? $enrollments->pluck('student_id')->unique()->count();
        $trainerAssigned   = $stats['trainer_assigned']   ?? $enrollments->whereNotNull('trainer_id')->count();
        $trainerUnassigned = $stats['trainer_unassigned'] ?? $enrollments->whereNull('trainer_id')->count();
    @endphp

    <div class="stack">
        {{-- Summary stat cards --}}
        <div class="enrollment-stats-grid">
            <div class="enrollment-stat">
                <div class="enrollment-stat-icon total">📋</div>
                <div class="enrollment-stat-body">
                    <span class="enrollment-stat-value">{{ $totalEnrollments }}</span>
                    <span class="enrollment-stat-label">Total Enrollments</span>
                </div>
            </div>
            <div class="enrollment-stat">
                <div class="enrollment-stat-icon students">🎓</div>
                <div class="enrollment-stat-body">
                    <span class="enrollment-stat-value">{{ $totalStudents }}</span>
                    <span class="enrollment-stat-label">Students Enrolled</span>
                </div>
            </div>
            <div class="enrollment-stat">
                <div class="enrollment-stat-icon assigned">👤</div>
                <div class="enrollment-stat-body">
                    <span class="enrollment-stat-value">{{ $trainerAssigned }}</span>
                    <span class="enrollment-stat-label">Trainer Assigned</span>
                </div>
            </div>
            <div class="enrollment-stat">
                <div class="enrollment-stat-icon unassigned">⚠️</div>
                <div class="enrollment-stat-body">
                    <span class="enrollment-stat-value">{{ $trainerUnassigned }}</span>
                    <span class="enrollment-stat-label">Trainer Unassigned</span>
                </div>
            </div>
        </div>

        

        <section class="enrollment-card">
            {{-- Table header: title/count + single-row filters --}}
            <div class="enrollment-table-head">
                <div class="enrollment-table-head-top">
                    <div class="">
                       <h2>Enrollment Management</h2>
                     <span>Assign students to courses and optionally attach trainers.</span>
                    </div>
                     <div class="">
                         <button type="button" class="btn btn-soft" data-modal-open="modal-enrollment-create">+ Assign Enrollment</button>
                    <span class="enrollment-count-pill">{{ $totalEnrollments }} total</span>
                    </div>

                     
                </div>

                <form method="GET" action="{{ route('enrollments.index') }}" id="enrollmentFilterForm" class="enrollment-filter-bar">
                    <div class="enrollment-filter-field">
                        <label>Category</label>
                        <select name="category_id" id="enrollmentCategoryFilter">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) $activeCategoryId === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="enrollment-filter-field">
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
                    <div class="enrollment-filter-field">
                        <label>Course</label>
                        <select name="course_id" id="enrollmentCourseFilter">
                            <option value="">All Courses</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) $activeCourseId === (string) $course->id)>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="enrollment-filter-field">
                        <label>Trainer</label>
                        <select name="trainer_id" id="enrollmentTrainerFilter">
                            <option value="">All Trainers</option>
                            @foreach ($trainers as $trainer)
                                <option value="{{ $trainer->id }}" @selected((string) $activeTrainerId === (string) $trainer->id)>{{ $trainer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="enrollment-filter-actions">
                        <a class="btn btn-soft" href="{{ route('enrollments.index') }}">Clear</a>
                        <button class="btn" type="submit">Apply</button>
                    </div>
                </form>
            </div>

            <div class="enrollment-table-wrap">
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
                            <td><span class="enrollment-id-pill">#{{ $enrollment->id }}</span></td>
                            <td>{{ $enrollment->course?->title }}</td>
                            <td>{{ $enrollment->student?->name }}</td>
                            <td>
                                @if($enrollment->trainer)
                                    <span class="enrollment-badge-trainer">{{ $enrollment->trainer->name }}</span>
                                @else
                                    <span class="enrollment-badge-empty">Not Assigned</span>
                                @endif
                            </td>
                            <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                            <td>
                                <div class="enrollment-row-actions">
                                    <form method="POST" action="{{ route('enrollments.resend-email', $enrollment) }}" class="resend-form">
                                        @csrf
                                        <button type="submit" class="enrollment-btn-mini">Resend Email</button>
                                    </form>
                                    <button type="button" class="enrollment-btn-mini" data-modal-open="modal-enrollment-edit-{{ $enrollment->id }}">Edit</button>
                                    <button type="button" class="enrollment-btn-mini danger delete-trigger"
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
    <div class="enrollment-modal-overlay" id="modal-enrollment-create" aria-hidden="true">
        <div class="enrollment-modal" role="dialog" aria-modal="true">
            <div class="enrollment-modal-head">
                <h3>Assign Enrollment</h3>
                <button type="button" class="enrollment-modal-close" data-modal-close="modal-enrollment-create" aria-label="Close">x</button>
            </div>
            <form method="POST" action="{{ route('enrollments.store') }}" class="stack form-premium needs-validation" novalidate>
                <div class="enrollment-modal-body">
                    @csrf
                    <div class="form-grid">
                        <div class="enrollment-field" data-field="course_id">
                            <label>Course <span class="req">*</span></label>
                            <select name="course_id" required>
                                <option value="">Select course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            <span class="enrollment-error-text">Please select a course.</span>
                        </div>
                        <div class="enrollment-field" data-field="student_id">
                            <label>Student <span class="req">*</span></label>
                            <select name="student_id" required>
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            <span class="enrollment-error-text">Please select a student.</span>
                        </div>
                        <div class="enrollment-field" data-field="trainer_id">
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
                <div class="enrollment-modal-footer">
                    <button class="bt btn" type="submit">Assign Enrollment</button>
                    <button type="button" class="btn btn-soft" data-modal-close="modal-enrollment-create">Close</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach ($enrollments as $enrollment)
        <div class="enrollment-modal-overlay" id="modal-enrollment-edit-{{ $enrollment->id }}" aria-hidden="true">
            <div class="enrollment-modal" role="dialog" aria-modal="true">
                <div class="enrollment-modal-head">
                    <h3>Edit Enrollment</h3>
                    <button type="button" class="enrollment-modal-close" data-modal-close="modal-enrollment-edit-{{ $enrollment->id }}" aria-label="Close">x</button>
                </div>
                <div class="enrollment-modal-body">
                    <form method="POST" action="{{ route('enrollments.update', $enrollment) }}" class="stack form-premium needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="enrollment-field" data-field="course_id">
                                <label>Course <span class="req">*</span></label>
                                <select name="course_id" required>
                                    <option value="">Select course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected($enrollment->course_id === $course->id)>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <span class="enrollment-error-text">Please select a course.</span>
                            </div>
                            <div class="enrollment-field" data-field="student_id">
                                <label>Student <span class="req">*</span></label>
                                <select name="student_id" required>
                                    <option value="">Select student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected($enrollment->student_id === $student->id)>{{ $student->name }} ({{ $student->email }})</option>
                                    @endforeach
                                </select>
                                <span class="enrollment-error-text">Please select a student.</span>
                            </div>
                            <div class="enrollment-field" data-field="trainer_id">
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
                <div class="enrollment-modal-footer">
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

                if (e.target.classList.contains('enrollment-modal-overlay')) {
                    closeModal(e.target.id);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                document.querySelectorAll('.enrollment-modal-overlay.open').forEach(function (el) { closeModal(el.id); });
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
                        var field = input.closest('.enrollment-field');
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
                        var field = input.closest('.enrollment-field');
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