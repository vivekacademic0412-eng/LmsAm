@extends('layouts.app')

@section('content')
    <style>
        .demo-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--card);
            padding: 14px;
            box-shadow: var(--shadow);
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
                    <h1>Assign Demo Task</h1>
                    <p>Assign demo tasks to demo users and track submissions.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('demo-tasks.submissions-page') }}">View All Submissions</a>
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
                        <th>Actions</th>
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
                                    <div class="muted">Last update: {{ $submission->submitted_at?->diffForHumans() }}</div>
                                    <div class="actions-row" style="margin-top: 8px;">
                                        <a class="btn btn-soft" href="{{ route('demo-tasks.submissions-page', ['user_id' => $assignment->user_id]) }}">View User Submissions</a>
                                    </div>
                                @else
                                    <span class="muted">No submission</span>
                                    <div class="actions-row" style="margin-top: 8px;">
                                        <a class="btn btn-soft" href="{{ route('demo-tasks.submissions-page', ['user_id' => $assignment->user_id]) }}">View User Submissions</a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="actions-row">
                                    <button type="button" class="btn-mini" data-modal-open="modal-demo-assignment-edit-{{ $assignment->id }}">Edit</button>
                                    <button type="button" class="btn-mini danger" data-modal-open="modal-demo-assignment-delete-{{ $assignment->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No assignments yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $assignments->links('pagination.custom') }}
            </div>
        </section>
    </div>

    @foreach ($assignments as $assignment)
        @php $submission = $latestSubmissions->get($assignment->id); @endphp
        <div class="modal-overlay" id="modal-demo-assignment-edit-{{ $assignment->id }}" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Edit Assignment</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-demo-assignment-edit-{{ $assignment->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('demo-tasks.assignments.update', $assignment) }}" class="stack form-premium">
                        @csrf
                        @method('PUT')
                        <div class="form-grid-2">
                            <div class="field">
                                <label>Task</label>
                                <select name="demo_task_id">
                                    @foreach ($tasks as $task)
                                        <option value="{{ $task->id }}" @selected((int) $task->id === (int) $assignment->demo_task_id)>{{ $task->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Demo User</label>
                                <select name="user_id">
                                    @foreach ($demoUsers as $demoUser)
                                        <option value="{{ $demoUser->id }}" @selected((int) $demoUser->id === (int) $assignment->user_id)>{{ $demoUser->name }} ({{ $demoUser->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if ($submission)
                            <div class="muted" style="font-size: 12px;">
                                This assignment already has a submission. If you edit it, the existing submission will stay attached to this assignment record.
                            </div>
                        @endif
                        <div class="actions-row">
                            <button class="btn" type="submit">Update Assignment</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-demo-assignment-edit-{{ $assignment->id }}">Close</button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="modal-demo-assignment-delete-{{ $assignment->id }}" aria-hidden="true">
            <div class="modal modal-sm" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Delete Assignment</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-demo-assignment-delete-{{ $assignment->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <p class="muted">
                        Are you sure you want to delete the assignment for
                        <strong>{{ $assignment->user?->name ?? 'demo user' }}</strong>
                        on
                        <strong>{{ $assignment->demoTask?->title ?? 'Demo Task' }}</strong>?
                    </p>
                    @if ($submission)
                        <p class="muted" style="margin-bottom: 0;">Deleting this assignment will also remove its submitted demo work.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-demo-assignment-delete-{{ $assignment->id }}">Cancel</button>
                    <form method="POST" action="{{ route('demo-tasks.assignments.destroy', $assignment) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="{{ asset('js/course-modals.js') }}" defer></script>
@endsection
