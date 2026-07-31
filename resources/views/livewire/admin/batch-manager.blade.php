{{-- resources/views/livewire/admin/batch-manager.blade.php --}}
<div class="bm-wrap">

<style>
.bm-wrap { display: flex; flex-direction: column; gap: 20px; }

.bm-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
}
.bm-title { font-size: 16px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
.bm-title small { display: block; font-size: 12px; font-weight: 500; color: var(--text-muted); margin-top: 2px; }

/* ═══════════════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════════════ */
.bm-filters {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    padding: 14px 16px;
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-sm);
}
.bm-filters select, .bm-filters input {
    border: 1.5px solid var(--input-border);
    background: var(--input-bg);
    border-radius: var(--radius-xs);
    padding: 8px 12px;
    font-size: 13px;
    color: var(--text);
    font-family: inherit;
}
.bm-filters input[type="search"] { flex: 1; min-width: 160px; }
.bm-filters select:focus, .bm-filters input:focus {
    outline: none; border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--primary-glow);
}
.bm-filters-clear {
    font-size: 12px; font-weight: 700; color: var(--text-muted);
    background: none; border: none; cursor: pointer;
    display: flex; align-items: center; gap: 4px;
}
.bm-filters-clear:hover { color: var(--danger); }

.bm-table-wrap {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    overflow: hidden;
    box-shadow: var(--shadow-card);
}
.bm-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.bm-table th {
    text-align: left;
    font-size: 10.5px; text-transform: uppercase; letter-spacing: .4px;
    color: var(--text-muted); font-weight: 700;
    padding: 12px 16px;
    background: var(--bg2);
    border-bottom: 1.5px solid var(--line);
    white-space: nowrap;
}
.bm-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--line);
    color: var(--text);
    vertical-align: middle;
}
.bm-table tr:last-child td { border-bottom: none; }
.bm-table tr:hover td { background: var(--bg2); }

.bm-code { font-weight: 700; }
.bm-course-name { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }
.bm-mode-chip {
    font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px;
    padding: 2px 9px; border-radius: 20px;
    background: var(--primary-glow); color: var(--brand-primary);
}
.bm-status-chip {
    font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px;
    padding: 3px 10px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: 5px;
}
.bm-status-chip.upcoming  { background: rgba(9,71,168,.12);  color: var(--brand-primary); }
.bm-status-chip.active    { background: rgba(22,163,74,.12); color: var(--success); }
.bm-status-chip.completed { background: rgba(100,116,139,.14); color: var(--text-muted); }
.bm-status-chip.cancelled { background: rgba(220,38,38,.12); color: var(--danger); }

.bm-seats-cell { display: flex; align-items: center; gap: 8px; min-width: 130px; }
.bm-seats-track { flex: 1; height: 5px; border-radius: 3px; background: var(--line); overflow: hidden; }
.bm-seats-fill { height: 100%; background: var(--brand-primary); border-radius: 3px; }
.bm-seats-fill.is-low  { background: var(--warning); }
.bm-seats-fill.is-full { background: var(--danger); }
.bm-seats-label { font-size: 11px; font-weight: 700; color: var(--text-muted); white-space: nowrap; }

.bm-row-actions { display: flex; gap: 6px; justify-content: flex-end; }

.bm-empty { text-align: center; padding: 40px 20px; color: var(--text-muted); }
.bm-empty i { font-size: 34px; opacity: .35; display: block; margin-bottom: 10px; }

.bm-hint {
    font-size: 11px; color: var(--text-muted); margin-top: 4px;
    display: flex; align-items: center; gap: 5px;
}
.bm-hint i { color: var(--brand-primary); font-size: 13px; }

/* ═══════════════════════════════════════════════
   MODAL (form + delete confirm)
═══════════════════════════════════════════════ */
.bm-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(5,12,28,.55);
    backdrop-filter: blur(3px);
    z-index: 960;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.bm-modal {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    width: 100%; max-width: 560px;
    max-height: 90vh;
    display: flex; flex-direction: column;
}
.bm-modal-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1.5px solid var(--line);
}
.bm-modal-head h3 { font-size: 15px; font-weight: 800; color: var(--text); }
.bm-modal-body { padding: 20px 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; }
.bm-modal-foot {
    padding: 16px 22px;
    border-top: 1.5px solid var(--line);
    display: flex; justify-content: flex-end; gap: 10px;
    background: var(--bg2);
}

.bm-field { display: flex; flex-direction: column; gap: 6px; }
.bm-field label { font-size: 12px; font-weight: 700; color: var(--text-muted); }
.bm-field input, .bm-field select {
    border: 1.5px solid var(--input-border);
    background: var(--input-bg);
    border-radius: var(--radius-xs);
    padding: 9px 12px;
    font-size: 13.5px;
    color: var(--text);
    font-family: inherit;
}
.bm-field input:focus, .bm-field select:focus {
    outline: none;
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px var(--primary-glow);
}
.bm-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.bm-field-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
.bm-error { color: var(--danger); font-size: 11.5px; margin-top: 2px; }

.bm-confirm-text { font-size: 13.5px; color: var(--text); line-height: 1.6; }

@media (max-width: 560px) {
    .bm-field-row, .bm-field-row-3 { grid-template-columns: 1fr; }
}
</style>

<div class="bm-head">
    <div class="bm-title">
        Batches
        <small>{{ $batches->count() }} batch{{ $batches->count() !== 1 ? 'es' : '' }} shown · pick a course when adding one — every batch belongs to exactly one course</small>
    </div>
    <button class="btn btn-unlock btn-sm" type="button" wire:click="create">
        <i class="ti ti-plus" aria-hidden="true"></i> Add Batch
    </button>
</div>

{{-- ══════════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════════ --}}
<div class="bm-filters">
    {{-- <select wire:model.live="filterCourseId" aria-label="Filter by course">
        <option value="">All courses</option>
        @foreach ($courses as $c)
            <option value="{{ $c->id }}">{{ $c->title }}</option>
        @endforeach
    </select> --}}
    <select wire:model.live="filterStatus" aria-label="Filter by status">
        <option value="">All statuses</option>
        <option value="upcoming">Upcoming</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
    <select wire:model.live="filterMode" aria-label="Filter by mode">
        <option value="">All modes</option>
        <option value="online">Online</option>
        <option value="offline">Offline</option>
        <option value="hybrid">Hybrid</option>
    </select>
    <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search batch code…" aria-label="Search batch code">
    @if ($filterCourseId || $filterStatus || $filterMode || $search)
        <button class="bm-filters-clear" type="button" wire:click="clearFilters">
            <i class="ti ti-x"></i> Clear filters
        </button>
    @endif
</div>

@error('delete') <small class="error-text">{{ $message }}</small> @enderror

<div class="bm-table-wrap">
    @if ($batches->isEmpty())
        <div class="bm-empty">
            <i class="ti ti-calendar-off" aria-hidden="true"></i>
            <p>No batches match these filters.</p>
        </div>
    @else
        <table class="bm-table">
            <thead>
                <tr>
                    <th>Batch</th>
                    {{-- <th>Course</th> --}}
                    <th>Mode</th>
                    {{-- <th>Trainer</th> --}}
                    <th>Starts</th>
                    <th>Weeks</th>
                    <th>Seats</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($batches as $batch)
                    @php
                        $taken = $batch->active_students_count ?? 0;
                        $cap   = $batch->max_seats ?? 0;
                        $left  = max(0, $cap - $taken);
                        $pct   = $cap > 0 ? min(100, round(($taken / $cap) * 100)) : 0;
                        $state = $left <= 0 ? 'is-full' : ($left <= 3 ? 'is-low' : '');
                    @endphp
                    <tr wire:key="batch-row-{{ $batch->id }}">
                        <td class="bm-code">{{ $batch->batch_code }}</td>
                        {{-- <td>{{ $batch->course->title ?? '—' }}</td> --}}
                        <td><span class="bm-mode-chip">{{ $batch->mode }}</span></td>
                        {{-- <td>{{ $batch->trainer->name ?? '—' }}</td> --}}
                        <td>
                            {{ $batch->start_date?->format('d M Y') ?? '—' }}
                            @if ($batch->start_time)
                                <div class="bm-course-name"><i class="ti ti-clock" style="font-size:10px"></i> {{ substr($batch->start_time, 0, 5) }}</div>
                            @endif
                        </td>
                        <td>{{ $batch->max_weeks }} wk</td>
                        <td>
                            <div class="bm-seats-cell">
                                <div class="bm-seats-track">
                                    <div class="bm-seats-fill {{ $state }}" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="bm-seats-label">{{ $taken }}/{{ $cap }}</span>
                            </div>
                        </td>
                        <td><span class="bm-status-chip {{ $batch->status }}">{{ $batch->status }}</span></td>
                        <td>
                            <div class="bm-row-actions">
                                <button class="btn btn-outline btn-sm btn-icon" type="button" wire:click="edit({{ $batch->id }})" aria-label="Edit {{ $batch->batch_code }}">
                                    <i class="ti ti-pencil"></i>
                                </button>
                                <button class="btn btn-outline btn-sm btn-icon" type="button" wire:click="confirmDelete({{ $batch->id }})" aria-label="Delete {{ $batch->batch_code }}" style="color:var(--danger);">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ══════════════════════════════════════════════
     CREATE / EDIT MODAL
══════════════════════════════════════════════ --}}
@if ($showForm)
    <div class="bm-modal-backdrop" wire:click.self="closeForm">
        <div class="bm-modal">
            <div class="bm-modal-head">
                <h3>{{ $editingId ? 'Edit Batch' : 'Add Batch' }}</h3>
                <button class="modal-close" type="button" wire:click="closeForm" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div class="bm-modal-body">
                {{-- <div class="bm-field">
                    <label for="bm_course">Course</label>
                    <select id="bm_course" wire:model="course_id">
                        <option value="">Select a course…</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                    <span class="bm-hint"><i class="ti ti-info-circle"></i> A course can have many batches — this just says which course this one belongs to.</span>
                    @error('course_id') <span class="bm-error">{{ $message }}</span> @enderror
                </div> --}}

                <div class="bm-field-row">
                    <div class="bm-field">
                        <label for="bm_batch_code">Batch code</label>
                        <input id="bm_batch_code" type="text" wire:model="batch_code" placeholder="e.g. JULY-2026-BATCH-1">
                        @error('batch_code') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="bm-field">
                        <label for="bm_mode">Mode</label>
                        <select id="bm_mode" wire:model="mode">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                        @error('mode') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- <div class="bm-field">
                    <label for="bm_trainer">Trainer</label>
                    <select id="bm_trainer" wire:model="trainer_id">
                        <option value="">Select a trainer…</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                        @endforeach
                    </select>
                    @error('trainer_id') <span class="bm-error">{{ $message }}</span> @enderror
                </div> --}}

                <div class="bm-field-row-3">
                    <div class="bm-field">
                        <label for="bm_start_date">Start date</label>
                        <input id="bm_start_date" type="date" wire:model="start_date">
                        @error('start_date') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="bm-field">
                        <label for="bm_start_time">Start time</label>
                        <input id="bm_start_time" type="time" wire:model="start_time">
                        @error('start_time') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="bm-field">
                        <label for="bm_zero_day">Zero day</label>
                        <input id="bm_zero_day" type="date" wire:model="zero_day_date">
                        @error('zero_day_date') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bm-field-row">
                    <div class="bm-field">
                        <label for="bm_max_weeks">Duration (weeks)</label>
                        <input id="bm_max_weeks" type="number" min="1" max="104" wire:model="max_weeks">
                        @error('max_weeks') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="bm-field">
                        <label for="bm_max_seats">Seat capacity</label>
                        <input id="bm_max_seats" type="number" min="1" max="1000" wire:model="max_seats">
                        @if (! $editingId)
                            <span class="bm-hint"><i class="ti ti-sparkles"></i> Defaults to 50 — change it if this batch is different</span>
                        @endif
                        @error('max_seats') <span class="bm-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bm-field">
                    <label for="bm_status">Status</label>
                    <select id="bm_status" wire:model="status">
                        <option value="upcoming">Upcoming</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <span class="bm-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bm-modal-foot">
                <button class="btn btn-outline btn-sm" type="button" wire:click="closeForm">Cancel</button>
                <button class="btn btn-primary btn-sm" type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Create Batch' }}</span>
                    <span wire:loading wire:target="save"><i class="ti ti-loader-2"></i> Saving…</span>
                </button>
            </div>
        </div>
    </div>
@endif

{{-- ══════════════════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════════════════ --}}
@if ($confirmingDeleteId)
    <div class="bm-modal-backdrop" wire:click.self="cancelDelete">
        <div class="bm-modal" style="max-width:420px;">
            <div class="bm-modal-head">
                <h3>Delete batch?</h3>
                <button class="modal-close" type="button" wire:click="cancelDelete" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="bm-modal-body">
                <p class="bm-confirm-text">
                    This removes the batch and its student roster. Batches with active students can't be deleted —
                    move or cancel those students first.
                </p>
                @error('delete') <span class="bm-error">{{ $message }}</span> @enderror
            </div>
            <div class="bm-modal-foot">
                <button class="btn btn-outline btn-sm" type="button" wire:click="cancelDelete">Cancel</button>
                <button class="btn btn-sm" type="button" wire:click="delete" style="background:var(--danger);color:#fff;">
                    <i class="ti ti-trash"></i> Delete Batch
                </button>
            </div>
        </div>
    </div>
@endif

{{-- ══════════════════════════════════════════════
     SWEETALERT TOASTS (same Swal already used on checkout)
══════════════════════════════════════════════ --}}
@script
<script>
    $wire.on('batch-saved', (e) => {
        const data = Array.isArray(e) ? e[0] : e;
        Swal.fire({
            icon: 'success',
            title: 'Saved',
            text: data?.message ?? 'Batch saved.',
            timer: 2200,
            showConfirmButton: false,
            // toast: true,
            // position: 'top-end',
        });
    });

    $wire.on('batch-deleted', (e) => {
        const data = Array.isArray(e) ? e[0] : e;
        Swal.fire({
            icon: 'success',
            title: 'Deleted',
            text: data?.message ?? 'Batch deleted.',
            timer: 2200,
            showConfirmButton: false,
            // toast: true,
            // position: 'top-end',
        });
    });
</script>
@endscript

</div>