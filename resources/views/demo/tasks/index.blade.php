@extends('layouts.app')

@section('content')
    <style>
        .demo-grid { display: grid; gap: 14px; }
        .demo-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--card);
            padding: 14px;
            box-shadow: var(--shadow);
        }
        .demo-card h3 { margin: 0 0 8px; }
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
            width: min(640px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.25);
        }
        .modal.modal-sm { width: min(420px, 100%); }
        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line-soft);
        }
        .modal-head h3 { margin: 0; font-size: 20px; }
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
            border-top: 1px solid var(--line-soft);
            padding: 12px 16px;
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
            color: #b34747;
        }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Demo Tasks</h1>
                    <p>Create demo tasks and assign them to demo users.</p>
                </div>
            </div>
        </section>

        <section class="demo-card">
            <h3>Create Demo Task</h3>
            <form method="POST" action="{{ route('demo-tasks.store') }}" class="stack form-premium">
                @csrf
                <div class="form-grid-2">
                    <div class="field">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="field">
                        <label>Resource URL (optional)</label>
                        <input type="url" name="resource_url" placeholder="https://...">
                    </div>
                </div>
                <div class="field">
                    <label>AI Video Tool URL (optional)</label>
                    <input type="url" name="ai_video_url" placeholder="https://...">
                </div>
                <div class="field">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="actions-row">
                    <button class="btn btn-soft" type="submit">Create Task</button>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Manage Demo Tasks</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Resource</th>
                        <th>AI Video</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td>
                                <strong>{{ $task->title }}</strong>
                                @if ($task->description)
                                    <div class="muted">{{ $task->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($task->resource_url)
                                    <a class="btn btn-soft" href="{{ $task->resource_url }}" target="_blank" rel="noopener">Open</a>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($task->ai_video_url)
                                    <a class="btn btn-soft" href="{{ $task->ai_video_url }}" target="_blank" rel="noopener">Open</a>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>{{ $task->created_at?->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <div class="actions-row">
                                    <button type="button" class="btn-mini" data-modal-open="modal-demo-task-edit-{{ $task->id }}">Edit</button>
                                    <button type="button" class="btn-mini danger" data-modal-open="modal-demo-task-delete-{{ $task->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No demo tasks yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="demo-card">
            <h3>Assign Demo Task</h3>
            @if ($tasks->isEmpty() || $demoUsers->isEmpty())
                <p class="muted">Create a demo task and ensure demo users exist before assigning.</p>
            @else
            <form method="POST" action="{{ route('demo-tasks.assign', $tasks->first() ?? 0) }}" class="stack" id="demoAssignForm">
                @csrf
                <div class="form-grid-2">
                    <div class="field">
                        <label>Task</label>
                        <select name="demo_task_id" id="demoTaskSelect">
                            @foreach ($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Demo User</label>
                        <select name="user_id">
                            @foreach ($demoUsers as $demoUser)
                                <option value="{{ $demoUser->id }}">{{ $demoUser->name }} ({{ $demoUser->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="actions-row">
                    <button class="btn btn-soft" type="submit">Assign Task</button>
                </div>
            </form>
            @endif
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Assignments & Submissions</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Task</th>
                        <th>Demo User</th>
                        <th>Assigned By</th>
                        <th>Assigned On</th>
                        <th>Submission</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($assignments as $assignment)
                        @php $submission = $latestSubmissions->get($assignment->id); @endphp
                        <tr>
                            <td>{{ $assignment->demoTask?->title ?? 'Demo Task' }}</td>
                            <td>{{ $assignment->user?->name ?? '-' }}</td>
                            <td>{{ $assignment->assigner?->name ?? 'System' }}</td>
                            <td>{{ $assignment->assigned_at?->format('M d, Y') ?? '-' }}</td>
                            <td>
                                @if ($submission)
                                    <div class="muted">{{ $submission->submitted_at?->diffForHumans() }}</div>
                                    @if ($submission->answer_text)
                                        <div style="white-space: pre-wrap; max-width: 420px;">{{ $submission->answer_text }}</div>
                                    @endif
                                    @if ($submission->file_path)
                                        <a class="btn btn-soft" href="{{ route('demo-tasks.submissions.download', $submission) }}">Download File</a>
                                    @endif
                                @else
                                    <span class="muted">No submission</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No assignments yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @foreach ($tasks as $task)
        <div class="modal-overlay" id="modal-demo-task-edit-{{ $task->id }}" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Edit Demo Task</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-demo-task-edit-{{ $task->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('demo-tasks.update', $task) }}" class="stack form-premium">
                        @csrf
                        @method('PUT')
                        <div class="form-grid-2">
                            <div class="field">
                                <label>Title</label>
                                <input type="text" name="title" value="{{ $task->title }}" required>
                            </div>
                            <div class="field">
                                <label>Resource URL (optional)</label>
                                <input type="url" name="resource_url" value="{{ $task->resource_url }}" placeholder="https://...">
                            </div>
                        </div>
                        <div class="field">
                            <label>AI Video Tool URL (optional)</label>
                            <input type="url" name="ai_video_url" value="{{ $task->ai_video_url }}" placeholder="https://...">
                        </div>
                        <div class="field">
                            <label>Description</label>
                            <textarea name="description" rows="3">{{ $task->description }}</textarea>
                        </div>
                        <div class="actions-row">
                            <button class="btn" type="submit">Update Task</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-demo-task-edit-{{ $task->id }}">Close</button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="modal-demo-task-delete-{{ $task->id }}" aria-hidden="true">
            <div class="modal modal-sm" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Delete Demo Task</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-demo-task-delete-{{ $task->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <p class="muted">Are you sure you want to delete <strong>{{ $task->title }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-demo-task-delete-{{ $task->id }}">Cancel</button>
                    <form method="POST" action="{{ route('demo-tasks.destroy', $task) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach\n    <script src="{{ asset('js/course-modals.js') }}" defer></script>
@endsection







