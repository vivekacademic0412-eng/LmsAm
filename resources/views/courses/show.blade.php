@extends('layouts.app')

@section('content')
    @php
        $itemLabels = [
            'intro' => 'Intro PPT / Video',
            'main_video' => 'Main Video',
            'task' => 'Task',
            'quiz' => 'Quiz',
        ];
    @endphp
    <style>
        .day-card {
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(20, 95, 209, 0.03), rgba(20, 95, 209, 0));
            padding: 14px;
        }
        .item-type-chip {
            display: inline-block;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid var(--line);
            white-space: nowrap;
        }
        .secure-link {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        .secure-link:hover { text-decoration: underline; }
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
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>{{ $course->title }}</h1>
                    <p>
                        Category: {{ $course->category?->name }}
                        @if ($course->subcategory)
                            | Subcategory: {{ $course->subcategory->name }}
                        @endif
                        | Duration: {{ $course->duration_hours }}h
                        | Language: {{ $course->language ?: 'N/A' }}
                    </p>
                    <p>{{ $course->short_description ?: ($course->description ?: '-') }}</p>
                </div>
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" style="width:140px;height:90px;border-radius:12px;object-fit:cover;border:1px solid var(--line-soft);">
                @endif
            </div>
            @if ($canManage)
                <div class="row-actions mt-8">
                    <button type="button" class="btn btn-soft" data-modal-open="modal-week-create">+ Add Week</button>
                </div>
            @endif
        </section>

    <section class="card">
        <div class="page-head">
            <h2>Course Weeks</h2>
        </div>
        @forelse ($course->weeks as $week)
            <article class="day-card mb-8">
                <div class="page-head">
                    <h3 class="mt-0">Week {{ $week->week_number }}: {{ $week->title }}</h3>
                    @if ($canManage)
                        <div class="row-actions">
                            <button type="button" class="btn btn-soft" data-modal-open="modal-session-create-{{ $week->id }}">+ Add Session</button>
                            <button type="button" class="btn-mini" data-modal-open="modal-week-edit-{{ $week->id }}">Edit</button>
                            <button type="button" class="btn-mini danger" data-modal-open="modal-week-delete-{{ $week->id }}">Delete</button>
                        </div>
                    @endif
                </div>
                @if ($canManage)
                    <div class="muted mt-6">Add sessions inside this week to create Intro/Main Video/Task/Quiz items.</div>
                @endif

                @forelse ($week->sessions as $session)
                    <div class="mt-10">
                        <div class="page-head">
                            <h4 class="mt-0">Session {{ $session->session_number }}: {{ $session->title }}</h4>
                            @if ($canManage)
                                <div class="row-actions">
                                    <button type="button" class="btn-mini" data-modal-open="modal-session-edit-{{ $session->id }}">Edit</button>
                                    <button type="button" class="btn-mini danger" data-modal-open="modal-session-delete-{{ $session->id }}">Delete</button>
                                </div>
                            @endif
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Resource Type</th>
                                    <th>Content/Task/Quiz</th>
                                    <th>URL</th>
                                    @if ($canManage)<th>Update</th>@endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($session->items as $item)
                                    <tr>
                                        <td><span class="item-type-chip">{{ $itemLabels[$item->item_type] ?? strtoupper(str_replace('_', ' ', $item->item_type)) }}</span></td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->resource_type ?: '-' }}</td>
                                        <td>{{ $item->content ?: '-' }}</td>
                                    <td>
                                        @if ($item->hasPrivateCloudinaryAsset())
                                            <a href="{{ route('course-session-items.media.view', $item) }}" class="secure-link">Open Secure Viewer</a>
                                        @elseif ($item->resource_url && !in_array($item->resource_type, ['video', 'ppt', 'video_or_ppt'], true))
                                                <a href="{{ $item->resource_url }}" class="secure-link" target="_blank" rel="noopener">Open Link</a>
                                            @else
                                                -
                                        @endif
                                    </td>
                                    @if ($canManage)
                                        <td>
                                            <button type="button" class="btn-mini" data-modal-open="modal-item-edit-{{ $item->id }}">Edit</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="muted">No sessions yet for this week.</p>
            @endforelse
        </article>
    @empty
        <p class="muted">No weeks yet for this course.</p>
    @endforelse
</section>

        <section class="card">
            <div class="page-head">
                <h2>Enrollments</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Trainer</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($course->enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student?->name }}</td>
                            <td>{{ $enrollment->trainer?->name ?? 'Not Assigned' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No students enrolled.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @if ($canManage)
        <div class="modal-overlay" id="modal-week-create" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Add Week</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-week-create" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('courses.weeks.store', $course) }}" class="stack form-premium">
                        @csrf
                        <div class="form-grid">
                            <div class="field">
                                <label>Week Number</label>
                                <input type="number" min="1" name="week_number" placeholder="1" required>
                            </div>
                            <div class="field form-span-2">
                                <label>Week Title</label>
                                <input type="text" name="title" placeholder="Week 1 Basics" required>
                            </div>
                        </div>
                        <div class="actions-row">
                            <button class="btn" type="submit">Create Week</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-week-create">Close</button>
                </div>
            </div>
        </div>

        @foreach ($course->weeks as $week)
            <div class="modal-overlay" id="modal-session-create-{{ $week->id }}" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Add Session (Week {{ $week->week_number }})</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-session-create-{{ $week->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('course-weeks.sessions.store', $week) }}" class="stack form-premium">
                            @csrf
                            <div class="form-grid">
                                <div class="field">
                                    <label>Session Number</label>
                                    <input type="number" min="1" name="session_number" placeholder="1" required>
                                </div>
                                <div class="field form-span-2">
                                    <label>Session Title</label>
                                    <input type="text" name="title" placeholder="Session 1 Introduction" required>
                                </div>
                            </div>
                            <div class="actions-row">
                                <button class="btn btn-soft" type="submit">Add Session + Intro/Main Video/Task/Quiz</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-session-create-{{ $week->id }}">Close</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="modal-week-edit-{{ $week->id }}" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Edit Week {{ $week->week_number }}</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-week-edit-{{ $week->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('course-weeks.update', $week) }}" class="stack form-premium">
                            @csrf
                            @method('PUT')
                            <div class="form-grid">
                                <div class="field">
                                    <label>Week Number</label>
                                    <input type="number" min="1" name="week_number" value="{{ $week->week_number }}" required>
                                </div>
                                <div class="field form-span-2">
                                    <label>Week Title</label>
                                    <input type="text" name="title" value="{{ $week->title }}" required>
                                </div>
                            </div>
                            <div class="actions-row">
                                <button class="btn" type="submit">Update Week</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-week-edit-{{ $week->id }}">Close</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="modal-week-delete-{{ $week->id }}" aria-hidden="true">
                <div class="modal modal-sm" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Delete Week</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-week-delete-{{ $week->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <p class="muted">Delete Week {{ $week->week_number }} and all its sessions?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-week-delete-{{ $week->id }}">Cancel</button>
                        <form method="POST" action="{{ route('course-weeks.destroy', $week) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($course->weeks as $week)
            @foreach ($week->sessions as $session)
                @foreach ($session->items as $item)
                    <div class="modal-overlay" id="modal-item-edit-{{ $item->id }}" aria-hidden="true">
                        <div class="modal" role="dialog" aria-modal="true">
                            <div class="modal-head">
                                <h3>Edit {{ $itemLabels[$item->item_type] ?? strtoupper(str_replace('_', ' ', $item->item_type)) }}</h3>
                                <button type="button" class="modal-close" data-modal-close="modal-item-edit-{{ $item->id }}" aria-label="Close">x</button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('course-session-items.update', $item) }}" enctype="multipart/form-data" class="stack form-premium">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-grid-2">
                                        <input type="text" name="title" value="{{ $item->title }}" placeholder="Item title (e.g., Intro PPT)" required>
                                        <select name="resource_type">
                                            <option value="">None</option>
                                            <option value="video_or_ppt" @selected($item->resource_type === 'video_or_ppt')>Video or PPT</option>
                                            <option value="video" @selected($item->resource_type === 'video')>Video</option>
                                            <option value="ppt" @selected($item->resource_type === 'ppt')>PPT</option>
                                        </select>
                                    </div>
                                    <textarea name="content" rows="2" placeholder="Content / instructions for this session item">{{ $item->content }}</textarea>
                                    <input type="url" name="resource_url" value="{{ $item->resource_url }}" placeholder="External link (optional, for non-video/PPT items)">
                                    <div class="field">
                                        <label>Secure File ({{ $item->item_type === 'task' ? 'Any Document/Archive' : 'Video/PPT/PDF' }})</label>
                                        <input
                                            type="file"
                                            name="resource_file"
                                            @if ($item->item_type === 'task')
                                                accept=".mp4,.mov,.avi,.mkv,.pdf,.ppt,.pptx,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.txt,.csv,.rtf,video/*,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.ms-excel,application/zip,application/x-7z-compressed,application/x-rar-compressed,text/plain,text/csv,application/rtf"
                                            @else
                                                accept=".mp4,.mov,.avi,.mkv,.pdf,.ppt,.pptx,video/*,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation"
                                            @endif
                                        >
                                    </div>
                                    <div class="actions-row">
                                        <button class="btn btn-soft" type="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-soft" data-modal-close="modal-item-edit-{{ $item->id }}">Close</button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="modal-overlay" id="modal-session-edit-{{ $session->id }}" aria-hidden="true">
                    <div class="modal" role="dialog" aria-modal="true">
                        <div class="modal-head">
                            <h3>Edit Session {{ $session->session_number }}</h3>
                            <button type="button" class="modal-close" data-modal-close="modal-session-edit-{{ $session->id }}" aria-label="Close">x</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('course-sessions.update', $session) }}" class="stack form-premium">
                                @csrf
                                @method('PUT')
                                <div class="form-grid">
                                    <div class="field">
                                        <label>Session Number</label>
                                        <input type="number" min="1" name="session_number" value="{{ $session->session_number }}" required>
                                    </div>
                                    <div class="field form-span-2">
                                        <label>Session Title</label>
                                        <input type="text" name="title" value="{{ $session->title }}" required>
                                    </div>
                                </div>
                                <div class="actions-row">
                                    <button class="btn btn-soft" type="submit">Update Session</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-soft" data-modal-close="modal-session-edit-{{ $session->id }}">Close</button>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay" id="modal-session-delete-{{ $session->id }}" aria-hidden="true">
                    <div class="modal modal-sm" role="dialog" aria-modal="true">
                        <div class="modal-head">
                            <h3>Delete Session</h3>
                            <button type="button" class="modal-close" data-modal-close="modal-session-delete-{{ $session->id }}" aria-label="Close">x</button>
                        </div>
                        <div class="modal-body">
                            <p class="muted">Delete Session {{ $session->session_number }} and all its items?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-soft" data-modal-close="modal-session-delete-{{ $session->id }}">Cancel</button>
                            <form method="POST" action="{{ route('course-sessions.destroy', $session) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    @endif

    <script src="{{ asset('js/course-modals.js') }}" defer></script>
@endsection
