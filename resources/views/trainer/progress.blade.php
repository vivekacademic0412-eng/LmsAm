@extends('layouts.app')

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Trainer Student Progress</h1>
                    <p>Clear overview of assigned learners and their course progress.</p>
                </div>
                <div class="actions-row">
                    <a class="btn btn-soft" href="{{ route('trainer.submissions') }}">Review Queue</a>
                    <div class="filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="trainerProgressFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="filter-panel" id="trainerProgressFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('trainer.progress') }}">
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
                                    <a class="btn btn-soft" href="{{ route('trainer.progress') }}">Clear</a>
                                    <button class="btn" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-3">
            <article class="card panel-soft">
                <p class="muted">Assigned Enrollments</p>
                <div class="kpi">{{ $rows->total() }}</div>
            </article>
            <article class="card panel-soft">
                <p class="muted">In Progress</p>
                <div class="kpi">
                    {{
                        $rows->getCollection()
                            ->filter(fn ($row) => $row['progress_percent'] > 0 && $row['progress_percent'] < 100)
                            ->count()
                    }}
                </div>
            </article>
            <article class="card panel-soft">
                <p class="muted">Completed</p>
                <div class="kpi">
                    {{ $rows->getCollection()->filter(fn ($row) => $row['progress_percent'] >= 100)->count() }}
                </div>
            </article>
        </section>

        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Completed Items</th>
                        <th>Total Items</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['enrollment']->student?->name }}</td>
                            <td>{{ $row['enrollment']->course?->title }}</td>
                            <td>{{ $row['completed_items'] }}</td>
                            <td>{{ $row['total_items'] }}</td>
                            <td>
                                <div class="stack" style="gap:6px;">
                                    <div style="font-weight:600;">{{ $row['progress_percent'] }}%</div>
                                    <div style="height:8px;background:var(--line-soft);border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $row['progress_percent'] }}%;background:linear-gradient(90deg,#2d7cf0,#4ac488);"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a class="btn btn-soft" href="{{ route('trainer.courses.items', $row['enrollment']->course_id) }}">Task & Quiz Items</a>
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
                {{ $rows->links('pagination.custom') }}
            </div>
        </section>
    </div>
    <script src="{{ asset('js/filters.js') }}" defer></script>
@endsection
