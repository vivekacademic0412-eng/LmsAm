<div class="dta-wrap">

    <style>
        .dta-wrap {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .dta-panel {
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            padding: 20px 22px;
        }

        .dta-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
        }

        .dta-head h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .dta-head h2,
        .dta-head h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .dta-head p {
            color: var(--text-muted);
            font-size: 13.5px;
        }

        .dta-panel > h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 14px;
        }

        .dta-muted {
            color: var(--text-muted);
            font-size: 13.5px;
        }

        /* Buttons */
        .dta-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            border-radius: var(--radius-sm);
            padding: 10px 18px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
            text-decoration: none;
        }

        .dta-btn:hover { transform: translateY(-1px); }
        .dta-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        .dta-btn-primary {
            background: var(--brand-primary);
            color: #fff;
            box-shadow: 0 8px 18px -8px var(--primary-glow);
        }

        .dta-btn-soft {
            background: var(--bg-card2);
            color: var(--brand-primary);
            border: 1px solid var(--line);
        }

        .dta-btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .dta-btn-mini {
            border: 1px solid var(--input-border);
            border-radius: var(--radius-xs);
            background: var(--bg-card2);
            color: var(--text-main);
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: 150ms ease;
        }

        .dta-btn-mini:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
        }

        .dta-btn-mini--danger:hover {
            border-color: var(--danger);
            background: rgba(220, 38, 38, .06);
            color: var(--danger);
        }

        /* Form */
        .dta-form { display: flex; flex-direction: column; gap: 16px; }

        .dta-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .dta-field { display: flex; flex-direction: column; gap: 6px; }

        .dta-field label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .dta-field select,
        .dta-field input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: var(--radius-sm);
            padding: 10px 12px;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .dta-field select:focus,
        .dta-field input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .dta-field--error select,
        .dta-field--error input {
            border-color: var(--danger);
        }

        .dta-error {
            font-size: 12.5px;
            color: var(--danger);
        }

        .dta-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Table */
        .dta-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
        }

        .dta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .dta-table thead th {
            background: var(--bg2);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
        }

        .dta-table tbody td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--line);
            color: var(--text-main);
            vertical-align: top;
        }

        .dta-table tbody tr:last-child td { border-bottom: none; }
        .dta-table tbody tr:hover { background: var(--bg-card2); }

        .dta-pagination { margin-top: 14px; }

        /* Modal */
        .dta-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 15, 28, .56);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 120;
        }

        .dta-modal {
            width: min(640px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            background: var(--bg-card);
            box-shadow: var(--shadow);
        }

        .dta-modal--sm { width: min(420px, 100%); }

        .dta-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
        }

        .dta-modal-head h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .dta-modal-close {
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
        }

        .dta-modal-close:hover { color: var(--danger); }

        .dta-modal-body { padding: 18px; }

        .dta-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid var(--line);
            padding: 14px 18px;
        }

        @media (max-width: 640px) {
            .dta-grid-2 { grid-template-columns: 1fr; }
            .dta-head { flex-direction: column; }
        }
    </style>

    <section class="dta-panel">
        <div class="dta-head">
            <div>
                <h1>Assign Demo Task</h1>
                <p>Assign demo tasks to demo users and track submissions.</p>
            </div>
            <a class="dta-btn dta-btn-soft" href="{{ route('demo-tasks.submissions-page') }}">View All Submissions</a>
        </div>
    </section>

    <section class="dta-panel">
        <h3>Assign Demo Task</h3>
        @if ($tasks->isEmpty() || $demoUsers->isEmpty())
            <p class="dta-muted">Create a demo task and ensure demo users exist before assigning.</p>
        @else
            <form wire:submit.prevent="assign" class="dta-form">
                <div class="dta-grid-2">
                    <div class="dta-field @error('demo_task_id') dta-field--error @enderror">
                        <label>Task</label>
                        <select wire:model="demo_task_id">
                            <option value="">Select a task</option>
                            @foreach ($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->title }}</option>
                            @endforeach
                        </select>
                        @error('demo_task_id') <span class="dta-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="dta-field @error('user_id') dta-field--error @enderror">
                        <label>Demo User</label>
                        <select wire:model="user_id">
                            <option value="">Select a user</option>
                            @foreach ($demoUsers as $demoUser)
                                <option value="{{ $demoUser->id }}">{{ $demoUser->name }} ({{ $demoUser->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="dta-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="dta-actions">
                    <button class="dta-btn dta-btn-primary" type="submit" wire:loading.attr="disabled" wire:target="assign">
                        <span wire:loading.remove wire:target="assign">Assign Task</span>
                        <span wire:loading wire:target="assign">Assigning…</span>
                    </button>
                </div>
            </form>
        @endif
    </section>

    <section class="dta-panel">
        <div class="dta-head">
            <h2>Assignments & Submissions</h2>
        </div>
        <div class="dta-table-wrap">
            <table class="dta-table">
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
                    <tr wire:key="assignment-{{ $assignment->id }}">
                        <td>{{ $assignment->demoTask?->title ?? 'Demo Task' }}</td>
                        <td>{{ $assignment->user?->name ?? '-' }}</td>
                        <td>{{ $assignment->assigner?->name ?? 'System' }}</td>
                        <td>{{ $assignment->assigned_at?->format('M d, Y') ?? '-' }}</td>
                        <td>
                            @if ($submission)
                                <div class="dta-muted">Last update: {{ $submission->submitted_at?->diffForHumans() }}</div>
                            @else
                                <span class="dta-muted">No submission</span>
                            @endif
                            <div class="dta-actions" style="justify-content:flex-start; margin-top: 8px;">
                                <a class="dta-btn dta-btn-soft" href="{{ route('demo-tasks.submissions-page', ['user_id' => $assignment->user_id]) }}">View User Submissions</a>
                            </div>
                        </td>
                        <td>
                            <div class="dta-actions" style="justify-content:flex-start;">
                                <button type="button" class="dta-btn-mini" wire:click="openEdit({{ $assignment->id }})">Edit</button>
                                <button type="button" class="dta-btn-mini dta-btn-mini--danger" wire:click="confirmDelete({{ $assignment->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="dta-muted">No assignments yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="dta-pagination">
            {{ $assignments->links() }}
        </div>
    </section>

    {{-- Edit modal --}}
    @if ($editingAssignmentId)
        <div class="dta-modal-overlay" id="modal-demo-assignment-edit-{{ $editingAssignmentId }}" aria-hidden="false">
            <div class="dta-modal" role="dialog" aria-modal="true">
                <div class="dta-modal-head">
                    <h3>Edit Assignment</h3>
                    <button type="button" class="dta-modal-close" wire:click="$set('editingAssignmentId', null)" aria-label="Close">×</button>
                </div>
                <div class="dta-modal-body">
                    <form wire:submit.prevent="updateAssignment" class="dta-form">
                        <div class="dta-grid-2">
                            <div class="dta-field @error('edit_demo_task_id') dta-field--error @enderror">
                                <label>Task</label>
                                <select wire:model="edit_demo_task_id">
                                    @foreach ($tasks as $task)
                                        <option value="{{ $task->id }}">{{ $task->title }}</option>
                                    @endforeach
                                </select>
                                @error('edit_demo_task_id') <span class="dta-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="dta-field @error('edit_user_id') dta-field--error @enderror">
                                <label>Demo User</label>
                                <select wire:model="edit_user_id">
                                    @foreach ($demoUsers as $demoUser)
                                        <option value="{{ $demoUser->id }}">{{ $demoUser->name }} ({{ $demoUser->email }})</option>
                                    @endforeach
                                </select>
                                @error('edit_user_id') <span class="dta-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="dta-actions">
                            <button class="dta-btn dta-btn-primary" type="submit" wire:loading.attr="disabled" wire:target="updateAssignment">
                                <span wire:loading.remove wire:target="updateAssignment">Update Assignment</span>
                                <span wire:loading wire:target="updateAssignment">Updating…</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="dta-modal-footer">
                    <button type="button" class="dta-btn dta-btn-soft" wire:click="$set('editingAssignmentId', null)">Close</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete modal --}}
    @if ($deletingAssignmentId)
        @php $delAssignment = $assignments->firstWhere('id', $deletingAssignmentId); @endphp
        @if ($delAssignment)
            <div class="dta-modal-overlay" id="modal-demo-assignment-delete-{{ $deletingAssignmentId }}" aria-hidden="false">
                <div class="dta-modal dta-modal--sm" role="dialog" aria-modal="true">
                    <div class="dta-modal-head">
                        <h3>Delete Assignment</h3>
                        <button type="button" class="dta-modal-close" wire:click="$set('deletingAssignmentId', null)" aria-label="Close">×</button>
                    </div>
                    <div class="dta-modal-body">
                        <p class="dta-muted">
                            Are you sure you want to delete the assignment for
                            <strong style="color:var(--text-main);">{{ $delAssignment->user?->name ?? 'demo user' }}</strong>
                            on
                            <strong style="color:var(--text-main);">{{ $delAssignment->demoTask?->title ?? 'Demo Task' }}</strong>?
                        </p>
                        @if ($latestSubmissions->get($deletingAssignmentId))
                            <p class="dta-muted" style="margin-top:8px; margin-bottom: 0;">Deleting this assignment will also remove its submitted demo work.</p>
                        @endif
                    </div>
                    <div class="dta-modal-footer">
                        <button type="button" class="dta-btn dta-btn-soft" wire:click="$set('deletingAssignmentId', null)">Cancel</button>
                        <button type="button" class="dta-btn dta-btn-danger" wire:click="destroyAssignment({{ $deletingAssignmentId }})" wire:loading.attr="disabled" wire:target="destroyAssignment">
                            <span wire:loading.remove wire:target="destroyAssignment">Delete</span>
                            <span wire:loading wire:target="destroyAssignment">Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('swal', (event) => {
                    const data = Array.isArray(event) ? event[0] : event;
                    Swal.fire({
                        icon: data.icon,
                        title: data.title,
                        text: data.text ?? '',
                        confirmButtonColor: '#0947a8',
                    });
                });
            });
        </script>
    @endpush
</div>