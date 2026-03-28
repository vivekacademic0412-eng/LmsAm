@extends('layouts.app')

@php
    $statusOptions = \App\Models\CourseItemSubmission::reviewStatusOptions();
@endphp

@section('content')
    <style>
        .row-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .review-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .review-tag--pending {
            background: #eef2fb;
            color: #5d6c85;
        }
        .review-tag--done {
            background: #edf8f1;
            color: #19744b;
        }
        .review-tag--revision {
            background: #fff3e6;
            color: #a45a0f;
        }
        .submission-copy {
            max-width: 360px;
            white-space: pre-wrap;
        }
        .submission-note {
            max-width: 320px;
            white-space: pre-wrap;
            color: var(--muted);
        }
        .meta-stack {
            display: grid;
            gap: 4px;
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
            width: min(780px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.25);
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
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 12px 16px;
            border-top: 1px solid var(--line-soft);
        }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Trainer Review Queue</h1>
                    <p>See the latest submissions from all students assigned to you, with faster review filters.</p>
                </div>
                <div class="actions-row">
                    <a class="btn btn-soft" href="{{ route('trainer.progress') }}">View Progress</a>
                    <div class="filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="trainerSubmissionFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="filter-panel" id="trainerSubmissionFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('trainer.submissions') }}">
                                <div class="filter-field">
                                    <label>Student</label>
                                    <input type="text" name="student" value="{{ $activeStudentSearch }}" placeholder="Search name or email">
                                </div>
                                <div class="filter-field">
                                    <label>Course</label>
                                    <select name="course_id">
                                        <option value="">All Courses</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}" @selected((int) $activeCourseId === (int) $course->id)>{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Review Status</label>
                                    <select name="status">
                                        <option value="">All Statuses</option>
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-actions">
                                    <a class="btn btn-soft" href="{{ route('trainer.submissions') }}">Clear</a>
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
                <p class="muted">Pending Review</p>
                <div class="kpi">{{ $summary['pending_review'] }}</div>
            </article>
            <article class="card panel-soft">
                <p class="muted">Revision Requested</p>
                <div class="kpi">{{ $summary['revision_requested'] }}</div>
            </article>
            <article class="card panel-soft">
                <p class="muted">Reviewed</p>
                <div class="kpi">{{ $summary['reviewed'] }}</div>
            </article>
        </section>

        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Course / Item</th>
                        <th>Student</th>
                        <th>Review Status</th>
                        <th>Submitted</th>
                        <th>Answer / File</th>
                        <th>Review Notes</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td>
                                <div class="meta-stack">
                                    <strong>{{ $submission->enrollment?->course?->title ?? 'Course' }}</strong>
                                    <span class="muted">{{ $submission->item?->title ?? 'Submission Item' }}</span>
                                    <span class="muted">
                                        Week {{ $submission->item?->session?->week?->week_number ?? '-' }}
                                        / Session {{ $submission->item?->session?->session_number ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="meta-stack">
                                    <strong>{{ $submission->enrollment?->student?->name ?? '-' }}</strong>
                                    <span class="muted">{{ $submission->enrollment?->student?->email ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="review-tag review-tag--{{ $submission->reviewStatusTone() }}">{{ $submission->reviewStatusLabel() }}</span>
                            </td>
                            <td>{{ $submission->submitted_at?->diffForHumans() ?? '-' }}</td>
                            <td>
                                @if ($submission->answer_text)
                                    <div class="submission-copy">{{ $submission->answer_text }}</div>
                                @endif
                                @if ($submission->file_path)
                                    <a class="btn btn-soft mt-8" href="{{ route('course-item-submissions.download', $submission) }}">Download File</a>
                                @endif
                                @if (! $submission->answer_text && ! $submission->file_path)
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($submission->review_notes)
                                    <div class="submission-note">{{ $submission->review_notes }}</div>
                                @else
                                    <span class="muted">No review notes yet.</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="btn btn-soft" data-modal-open="modal-trainer-submission-review-{{ $submission->id }}">Review</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No submissions found for the selected filters.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-10">
                {{ $submissions->links('pagination.custom') }}
            </div>
        </section>
    </div>

    @foreach ($submissions as $submission)
        <div class="modal-overlay" id="modal-trainer-submission-review-{{ $submission->id }}" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Review Submission</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-trainer-submission-review-{{ $submission->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('course-item-submissions.review', $submission) }}" class="stack form-premium">
                        @csrf
                        @method('PUT')
                        <div class="form-grid-2">
                            <div class="field">
                                <label>Student</label>
                                <input type="text" value="{{ $submission->enrollment?->student?->name }}" disabled>
                            </div>
                            <div class="field">
                                <label>Status</label>
                                <select name="review_status" required>
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected($submission->review_status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>Review Notes</label>
                            <textarea name="review_notes" rows="5" placeholder="Share feedback, corrections, or next steps.">{{ old('review_notes', $submission->review_notes) }}</textarea>
                        </div>
                        <div class="actions-row">
                            <button class="btn" type="submit">Save Review</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-trainer-submission-review-{{ $submission->id }}">Close</button>
                </div>
            </div>
        </div>
    @endforeach

    <script src="{{ asset('js/course-modals.js') }}" defer></script>
    <script src="{{ asset('js/filters.js') }}" defer></script>
@endsection
