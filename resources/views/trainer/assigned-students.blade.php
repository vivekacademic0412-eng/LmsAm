@extends('layouts.app')

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Assigned Students</h1>
                    <p>Quick list of assigned learners with course and contact details.</p>
                </div>
                <div class="actions-row">
                    <a class="btn btn-soft" href="{{ route('trainer.submissions') }}">Review Queue</a>
                    <div class="filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="assignedStudentsFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="filter-panel" id="assignedStudentsFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('trainer.assigned-students') }}">
                                <div class="filter-field">
                                    <label>Student</label>
                                    <input type="text" name="student" value="{{ $activeStudentSearch }}" placeholder="Search name or email">
                                </div>
                                <div class="filter-field">
                                    <label>Category</label>
                                    <select name="category_id">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) $activeCategoryId === (string) $category->id)>{{ $category->name }}</option>
                                            @foreach ($category->children as $sub)
                                                <option value="{{ $sub->id }}" @selected((string) $activeCategoryId === (string) $sub->id)>
                                                    {{ $category->name }} / {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Course</label>
                                    <select name="course_id">
                                        <option value="">All Courses</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}" @selected((string) $activeCourseId === (string) $course->id)>{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-actions">
                                    <a class="btn btn-soft" href="{{ route('trainer.assigned-students') }}">Clear</a>
                                    <button class="btn" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <a class="btn btn-soft" href="{{ route('trainer.progress') }}">View Progress</a>
                </div>
            </div>
        </section>

        <section class="grid grid-3">
            <article class="card panel-soft">
                <p class="muted">Assigned Enrollments</p>
                <div class="kpi">{{ $enrollments->total() }}</div>
            </article>
            <article class="card panel-soft">
                <p class="muted">Unique Students</p>
                <div class="kpi">
                    {{ $enrollments->getCollection()->pluck('student_id')->unique()->count() }}
                </div>
            </article>
            <article class="card panel-soft">
                <p class="muted">Unique Courses</p>
                <div class="kpi">
                    {{ $enrollments->getCollection()->pluck('course_id')->unique()->count() }}
                </div>
            </article>
        </section>

        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Assigned By</th>
                        <th>Assigned On</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student?->name }}</td>
                            <td>{{ $enrollment->student?->email }}</td>
                            <td>{{ $enrollment->course?->title }}</td>
                            <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                            <td>{{ $enrollment->created_at?->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <a class="btn btn-soft" href="{{ route('trainer.courses.items', $enrollment->course_id) }}">Task & Quiz Items</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No students assigned to you.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $enrollments->links('pagination.custom') }}
            </div>
        </section>
    </div>
    <script src="{{ asset('js/filters.js') }}" defer></script>
@endsection
