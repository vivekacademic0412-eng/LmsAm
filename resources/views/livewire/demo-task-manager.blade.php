<div class="dt-wrap" wire:loading.class="dt-busy">

    {{-- ============ HEADER ============ --}}
    <div class="dt-header">
        <div>
            <h2 class="dt-title">Demo Tasks</h2>
            <p class="dt-subtitle">Create, edit and manage demo tasks</p>
        </div>
        <button type="button" class="dt-btn dt-btn-primary" wire:click="openCreate">
            <i class="fa fa-plus-lg"></i> Add Task
        </button>
    </div>

    {{-- ============ FILTERS ============ --}}
    <div class="dt-card dt-filters">
        <div class="dt-filter-field">
            <label>Search</label>
            <input type="text" class="dt-input" placeholder="Search title or description..."
                   wire:model.live.debounce.400ms="search">
        </div>

        <div class="dt-filter-field">
            <label>From</label>
            <input type="date" class="dt-input" wire:model.live="dateFrom">
        </div>

        <div class="dt-filter-field">
            <label>To</label>
            <input type="date" class="dt-input" wire:model.live="dateTo">
        </div>

        <div class="dt-filter-field">
            <label>Per Page</label>
            <select class="dt-input" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="dt-filter-field dt-filter-actions">
            <label>&nbsp;</label>
            <button type="button" class="dt-btn dt-btn-outline" wire:click="resetFilters">
                <i class="fa fa-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    {{-- ============ TABLE ============ --}}
    <div class="dt-card dt-table-card">
        <div class="dt-table-responsive">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('title')" class="dt-sortable">
                            Title
                            @if($sortField === 'title')
                                <i class="fa fa-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-fill"></i>
                            @endif
                        </th>
                        <th>Description</th>
                        <th>Resource</th>
                        <th>Video</th>
                        <th>Created By</th>
                        <th wire:click="sortBy('created_at')" class="dt-sortable">
                            Created
                            @if($sortField === 'created_at')
                                <i class="fa fa-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-fill"></i>
                            @endif
                        </th>
                        <th class="dt-col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr wire:key="task-{{ $task->id }}">
                            <td class="dt-cell-title">{{ $task->title }}</td>
                            <td class="dt-cell-desc">
                                {{ \Illuminate\Support\Str::limit(strip_tags($task->description), 90) }}
                            </td>
                            <td>
                                @if($task->resource_file_path)
                                    <a href="{{ Storage::disk('public')->url($task->resource_file_path) }}"
                                       target="_blank" class="dt-chip dt-chip-info">
                                        <i class="fa fa-paperclip"></i> File
                                    </a>
                                @elseif($task->resource_url)
                                    <a href="{{ $task->resource_url }}" target="_blank" class="dt-chip dt-chip-info">
                                        <i class="fa fa-link-45deg"></i> Link
                                    </a>
                                @else
                                    <span class="dt-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($task->task_video_path)
                                    <span class="dt-chip dt-chip-success"><i class="fa fa-camera-video"></i> Video</span>
                                @elseif($task->ai_video_url)
                                    <a href="{{ $task->ai_video_url }}" target="_blank" class="dt-chip dt-chip-success">
                                        <i class="fa fa-robot"></i> AI Video
                                    </a>
                                @else
                                    <span class="dt-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $task->creator->name ?? '—' }}</td>
                            <td>{{ $task->created_at->format('d M Y') }}</td>
                            <td class="dt-col-actions">
                                <button type="button" class="dt-icon-btn" title="Edit"
                                        wire:click="openEdit({{ $task->id }})">
                                    <i class="fa fa-pencil-square"></i>
                                </button>
                                <button type="button" class="dt-icon-btn dt-icon-danger" title="Delete"
                                        wire:click="confirmDelete({{ $task->id }})">
                                    <i class="fa fa-trash3"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="dt-empty">
                                <i class="fa fa-inbox"></i>
                                <p>No demo tasks found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="dt-pagination">
            {{ $tasks->links() }}
        </div>
    </div>

    {{-- ============ CREATE / EDIT MODAL ============ --}}
    @if($showModal)
        <div class="dt-modal-backdrop" wire:click.self="closeModal">
            <div class="dt-modal">
                <div class="dt-modal-header">
                    <h3>{{ $isEdit ? 'Edit Task' : 'Add Task' }}</h3>
                    <button type="button" class="dt-modal-close" wire:click="closeModal">
                        <i class="fa fa-x-lg"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="dt-modal-body">

                    {{-- Title --}}
                    <div class="dt-form-group">
                        <label>Title <span class="dt-req">*</span></label>
                        <input type="text" class="dt-input @error('title') dt-input-error @enderror"
                               wire:model.blur="title" placeholder="Enter task title">
                        @error('title') <span class="dt-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description Editor --}}
                    <div class="dt-form-group">
                        <label>Description <span class="dt-req">*</span></label>

                        <div class="dt-editor @error('description') dt-input-error @enderror">
                            <div class="dt-editor-toolbar">
                                <button type="button" data-cmd="bold" title="Bold"><i class="fa fa-type-bold"></i></button>
                                <button type="button" data-cmd="italic" title="Italic"><i class="fa fa-type-italic"></i></button>
                                <button type="button" data-cmd="underline" title="Underline"><i class="fa fa-type-underline"></i></button>
                                <span class="dt-editor-sep"></span>
                                <button type="button" data-cmd="insertUnorderedList" title="Bullet list"><i class="fa fa-list-ul"></i></button>
                                <button type="button" data-cmd="insertOrderedList" title="Numbered list"><i class="fa fa-list-ol"></i></button>
                                <span class="dt-editor-sep"></span>
                                <button type="button" data-cmd="justifyLeft" title="Align left"><i class="fa fa-text-left"></i></button>
                                <button type="button" data-cmd="justifyCenter" title="Align center"><i class="fa fa-text-center"></i></button>
                                <span class="dt-editor-sep"></span>
                                <button type="button" data-cmd="undo" title="Undo"><i class="fa fa-arrow-counterclockwise"></i></button>
                                <button type="button" data-cmd="removeFormat" title="Clear formatting"><i class="fa fa-eraser"></i></button>
                            </div>

                            <div id="dtDescriptionEditor"
                                 class="dt-editor-area"
                                 contenteditable="true"
                                 x-data
                                 x-init="$el.innerHTML = @js($description)"
                                 x-on:input="$wire.set('description', $el.innerHTML, false)"
                                 placeholder="Write task description..."></div>
                        </div>
                        @error('description') <span class="dt-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="dt-form-row">
                        {{-- Resource URL --}}
                        <div class="dt-form-group">
                            <label>Resource URL</label>
                            <input type="url" class="dt-input @error('resource_url') dt-input-error @enderror"
                                   wire:model.blur="resource_url" placeholder="https://...">
                            @error('resource_url') <span class="dt-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- AI Video URL --}}
                        <div class="dt-form-group">
                            <label>AI Video URL</label>
                            <input type="url" class="dt-input @error('ai_video_url') dt-input-error @enderror"
                                   wire:model.blur="ai_video_url" placeholder="https://...">
                            @error('ai_video_url') <span class="dt-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dt-form-row">
                        {{-- Resource File --}}
                        <div class="dt-form-group">
                            <label>Resource File</label>
                            <input type="file" class="dt-input @error('resource_file') dt-input-error @enderror"
                                   wire:model="resource_file">

                            <div wire:loading wire:target="resource_file" class="dt-uploading">Uploading...</div>

                            @if($existing_resource_file_name && !$resource_file)
                                <div class="dt-existing-file">
                                    <i class="fa fa-file-earmark-check"></i>
                                    <span>{{ $existing_resource_file_name }}</span>
                                    <button type="button" wire:click="removeResourceFile" class="dt-remove-file">
                                        <i class="fa fa-x-circle"></i>
                                    </button>
                                </div>
                            @endif

                            @if($resource_file)
                                <div class="dt-existing-file">
                                    <i class="fa fa-file-earmark-arrow-up"></i>
                                    <span>{{ $resource_file->getClientOriginalName() }}</span>
                                </div>
                            @endif

                            @error('resource_file') <span class="dt-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Task Video --}}
                        <div class="dt-form-group">
                            <label>Task Video</label>
                            <input type="file" class="dt-input @error('task_video') dt-input-error @enderror"
                                   wire:model="task_video" accept="video/*">

                            <div wire:loading wire:target="task_video" class="dt-uploading">Uploading...</div>

                            @if($existing_task_video_name && !$task_video)
                                <div class="dt-existing-file">
                                    <i class="fa fa-camera-video"></i>
                                    <span>{{ $existing_task_video_name }}</span>
                                    <button type="button" wire:click="removeTaskVideo" class="dt-remove-file">
                                        <i class="fa fa-x-circle"></i>
                                    </button>
                                </div>
                            @endif

                            @if($task_video)
                                <div class="dt-existing-file">
                                    <i class="fa fa-camera-video"></i>
                                    <span>{{ $task_video->getClientOriginalName() }}</span>
                                </div>
                            @endif

                            @error('task_video') <span class="dt-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </form>

                <div class="dt-modal-footer">
                    <button type="button" class="dt-btn dt-btn-outline" wire:click="closeModal">Cancel</button>
                    <button type="button" class="dt-btn dt-btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            {{ $isEdit ? 'Update Task' : 'Save Task' }}
                        </span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ============ THEMED STYLES ============ --}}
<style>
    .dt-wrap { color: var(--text-main); }
    .dt-busy { opacity: .6; pointer-events: none; }

    .dt-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    }
    .dt-title { font-size: 22px; font-weight: 700; color: var(--text-main); margin: 0; }
    .dt-subtitle { color: var(--text-muted); margin: 4px 0 0; font-size: 14px; }

    .dt-card {
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-card);
        padding: 18px;
    }

    .dt-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .dt-filter-field label {
        display: block; font-size: 12px; font-weight: 600;
        color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .03em;
    }
    .dt-filter-actions { display: flex; align-items: flex-end; }

    .dt-input {
        width: 100%;
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        border-radius: var(--radius-xs);
        padding: 10px 12px;
        color: var(--text-main);
        font-size: 14px;
        transition: border-color .15s, box-shadow .15s;
    }
    .dt-input:focus {
        outline: none;
        border-color: var(--input-focus);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .dt-input-error { border-color: var(--danger) !important; }
    .dt-error { color: var(--danger); font-size: 12px; margin-top: 4px; display: block; }
    .dt-req { color: var(--danger); }

    .dt-btn {
        display: inline-flex; align-items: center; gap: 8px;
        border: none; border-radius: var(--radius-xs);
        padding: 10px 18px; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: transform .1s, box-shadow .15s, background .15s;
    }
    .dt-btn:active { transform: scale(.97); }
    .dt-btn-primary {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        color: #fff; box-shadow: var(--shadow-sm);
    }
    .dt-btn-primary:hover { box-shadow: var(--shadow); }
    .dt-btn-outline {
        background: transparent; color: var(--text-main);
        border: 1px solid var(--line);
    }
    .dt-btn-outline:hover { background: var(--bg2); }

    .dt-table-card { padding: 0; overflow: hidden; }
    .dt-table-responsive { overflow-x: auto; }
    .dt-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .dt-table thead th {
        text-align: left; padding: 14px 16px;
        background: var(--bg2); color: var(--text-muted);
        font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .03em;
        border-bottom: 1px solid var(--line); white-space: nowrap;
    }
    .dt-sortable { cursor: pointer; user-select: none; }
    .dt-sortable:hover { color: var(--brand-primary); }
    .dt-table tbody td {
        padding: 14px 16px; border-bottom: 1px solid var(--line); vertical-align: middle;
    }
    .dt-table tbody tr:hover { background: var(--bg2); }
    .dt-cell-title { font-weight: 600; color: var(--text-main); }
    .dt-cell-desc { color: var(--text-muted); max-width: 320px; }
    .dt-muted { color: var(--text-muted); }

    .dt-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;
        text-decoration: none;
    }
    .dt-chip-info { background: rgba(2, 132, 199, .12); color: var(--info); }
    .dt-chip-success { background: rgba(22, 163, 74, .12); color: var(--success); }

    .dt-col-actions { text-align: right; white-space: nowrap; }
    .dt-icon-btn {
        background: transparent; border: 1px solid var(--line);
        color: var(--text-muted); width: 34px; height: 34px;
        border-radius: var(--radius-xs); cursor: pointer; margin-left: 6px;
        transition: all .15s;
    }
    .dt-icon-btn:hover { background: var(--primary-glow); color: var(--brand-primary); border-color: var(--brand-primary); }
    .dt-icon-danger:hover { background: rgba(220, 38, 38, .1); color: var(--danger); border-color: var(--danger); }

    .dt-empty { text-align: center; padding: 50px 20px; color: var(--text-muted); }
    .dt-empty i { font-size: 32px; display: block; margin-bottom: 8px; }

    .dt-pagination { padding: 16px; }

    /* Modal */
    .dt-modal-backdrop {
        position: fixed; inset: 0; background: rgba(8, 17, 31, .55);
        display: flex; align-items: center; justify-content: center;
        z-index: 1000; padding: 16px; backdrop-filter: blur(2px);
    }
    .dt-modal {
        background: var(--bg-card); border-radius: var(--radius);
        width: 100%; max-width: 720px; max-height: 90vh;
        display: flex; flex-direction: column; box-shadow: var(--shadow);
        border: 1px solid var(--line);
    }
    .dt-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px; border-bottom: 1px solid var(--line);
    }
    .dt-modal-header h3 { margin: 0; font-size: 18px; color: var(--text-main); }
    .dt-modal-close {
        background: transparent; border: none; color: var(--text-muted);
        cursor: pointer; font-size: 16px;
    }
    .dt-modal-body { padding: 22px; overflow-y: auto; }
    .dt-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 16px 22px; border-top: 1px solid var(--line);
    }

    .dt-form-group { margin-bottom: 18px; }
    .dt-form-group label {
        display: block; font-size: 13px; font-weight: 600;
        color: var(--text-main); margin-bottom: 6px;
    }
    .dt-form-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    }

    .dt-editor {
        border: 1px solid var(--input-border); border-radius: var(--radius-xs);
        overflow: hidden; background: var(--input-bg);
    }
    .dt-editor-toolbar {
        display: flex; align-items: center; gap: 4px; padding: 8px;
        background: var(--bg2); border-bottom: 1px solid var(--input-border); flex-wrap: wrap;
    }
    .dt-editor-toolbar button {
        background: transparent; border: none; color: var(--text-muted);
        width: 30px; height: 30px; border-radius: 6px; cursor: pointer;
    }
    .dt-editor-toolbar button:hover { background: var(--primary-glow); color: var(--brand-primary); }
    .dt-editor-sep { width: 1px; height: 20px; background: var(--line); margin: 0 4px; }
    .dt-editor-area {
        min-height: 140px; padding: 12px; color: var(--text-main);
        font-size: 14px; line-height: 1.6;
    }
    .dt-editor-area:empty:before {
        content: attr(placeholder); color: var(--text-muted);
    }
    .dt-editor-area:focus { outline: none; }

    .dt-existing-file {
        display: flex; align-items: center; gap: 8px; margin-top: 8px;
        font-size: 13px; color: var(--text-muted);
        background: var(--bg2); padding: 6px 10px; border-radius: var(--radius-xs);
    }
    .dt-remove-file {
        background: none; border: none; color: var(--danger); cursor: pointer; margin-left: auto;
    }
    .dt-uploading { font-size: 12px; color: var(--brand-primary); margin-top: 6px; }

    @media (max-width: 640px) {
        .dt-form-row { grid-template-columns: 1fr; }
    }
</style>

{{-- ============ SCRIPTS: EDITOR TOOLBAR + SWEETALERT ============ --}}
@script
<script>
    // Rich text toolbar commands
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.dt-editor-toolbar button');
        if (!btn) return;
        e.preventDefault();
        const cmd = btn.getAttribute('data-cmd');
        document.getElementById('dtDescriptionEditor')?.focus();
        document.execCommand(cmd, false, null);
        document.getElementById('dtDescriptionEditor')?.dispatchEvent(new Event('input'));
    });

    // Success toast
    $wire.on('swal:success', (event) => {
        Swal.fire({
            icon: 'success',
            title: event.message ?? 'Success',
            toast: true,
            position: 'top-end',
            timer: 2500,
            showConfirmButton: false,
        });
    });

    // Error toast
    $wire.on('swal:error', (event) => {
        Swal.fire({
            icon: 'error',
            title: event.message ?? 'Something went wrong',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false,
        });
    });

    // Delete confirmation
    $wire.on('swal:confirm-delete', (event) => {
        Swal.fire({
            title: 'Delete this task?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#5a718a',
            background: getComputedStyle(document.body).getPropertyValue('--bg-card') || '#fff',
            color: getComputedStyle(document.body).getPropertyValue('--text-main') || '#0e1f36',
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.delete(event.id);
            }
        });
    });
</script>
@endscript