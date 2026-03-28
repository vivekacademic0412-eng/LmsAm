@extends('layouts.app')

@section('content')
    <style>
        .history-grid { display: grid; gap: 14px; }
        .history-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--card);
            padding: 14px;
            box-shadow: var(--shadow);
        }
        .history-kpi { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
        .history-kpi div { border: 1px solid var(--line-soft); border-radius: 10px; padding: 10px; background: #f8fbff; }
        .history-kpi b { display: block; font-size: 18px; }
        .history-row { display: flex; gap: 10px; align-items: center; }
        .course-pill {
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            background: #eef4ff;
            color: #1f4fa3;
            border: 1px solid #c6d8ff;
        }
        @media (max-width: 900px) {
            .history-kpi { grid-template-columns: 1fr; }
        }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Enrollment History</h1>
                    <p>All your enrolled courses and assignment details.</p>
                </div>
            </div>
        </section>

        <section class="history-card">
            <div class="history-kpi">
                <div>
                    <span class="muted">Total Enrollments</span>
                    <b>{{ $enrollments->total() }}</b>
                </div>
                <div>
                    <span class="muted">Active Courses</span>
                    <b>{{ $enrollments->getCollection()->pluck('course_id')->unique()->count() }}</b>
                </div>
                <div>
                    <span class="muted">Assigned Trainers</span>
                    <b>{{ $enrollments->getCollection()->pluck('trainer_id')->filter()->unique()->count() }}</b>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Enrollments</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Course</th>
                        <th>Category</th>
                        <th>Trainer</th>
                        <th>Assigned By</th>
                        <th>Enrolled On</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td>
                                <div class="history-row">
                                    <strong>{{ $enrollment->course?->title ?? 'Course' }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="course-pill">{{ $enrollment->course?->category?->name ?? 'General' }}</span>
                                @if ($enrollment->course?->subcategory)
                                    <div class="muted" style="font-size:12px;">{{ $enrollment->course->subcategory->name }}</div>
                                @endif
                            </td>
                            <td>{{ $enrollment->trainer?->name ?? 'Not Assigned' }}</td>
                            <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                            <td>{{ $enrollment->created_at?->format('M d, Y') ?? '-' }}</td>
                            <td><span class="tag ok">Active</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No enrollments found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $enrollments->links('pagination.custom') }}
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Transactions</h2>
            </div>
            <p class="muted">No transaction data available in this system.</p>
        </section>
    </div>
@endsection
