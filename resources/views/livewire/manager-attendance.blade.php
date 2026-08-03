@php
    $weekLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
@endphp

<div class="att-scope" wire:key="manager-attendance">

    <style>
        .att-scope { font-family: var(--lw-body, system-ui, sans-serif); color: var(--text); }
        .att-scope * { box-sizing: border-box; }
        .att-page { display: grid; gap: 18px; }

        .att-card { border: 1px solid var(--line); border-radius: var(--radius, 14px); background: var(--card); box-shadow: var(--shadow-card); }
        .att-pad { padding: 18px 20px; }

        .att-topbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; }
        .att-topbar h2 { margin: 0; font-family: var(--lw-display, inherit); font-size: 19px; }
        .att-topbar p { margin: 2px 0 0; font-size: 12.5px; color: var(--muted); }

        .att-month-nav { display: flex; align-items: center; gap: 10px; }
        .att-month-nav button { border: 1px solid var(--line); background: var(--bg2); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; font-weight: 700; }
        .att-month-nav button:disabled { opacity: .4; cursor: not-allowed; }
        .att-month-label { font-weight: 700; font-size: 14px; min-width: 130px; text-align: center; }

        .att-search { border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--bg2)); border-radius: 9px; padding: 9px 12px; font-size: 13px; color: var(--text); min-width: 220px; }

        .att-btn { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 8px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 700; border: 1px solid transparent; cursor: pointer; }
        .att-btn-primary { background: var(--brand-primary); color: #fff; }
        .att-btn-danger { background: var(--danger, #d9534f); color: #fff; }
        .att-btn-soft { background: var(--bg2); color: var(--brand-primary); border-color: var(--line); }
        .att-btn:disabled { opacity: .5; cursor: not-allowed; }

        .att-badge { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; padding: 3px 8px; border-radius: 999px; border: 1px solid var(--line); background: var(--bg2); color: var(--muted); }
        .att-badge.b-pending { background: color-mix(in srgb, var(--warning, #d98a00) 16%, var(--bg2)); color: var(--warning, #d98a00); border-color: var(--warning, #d98a00); }
        .att-badge.b-present { background: color-mix(in srgb, var(--brand-green, #1a9e57) 16%, var(--bg2)); color: #0f6b32; border-color: var(--brand-green, #1a9e57); }
        .att-badge.b-absent { background: color-mix(in srgb, var(--danger, #d9534f) 12%, var(--bg2)); color: var(--danger, #d9534f); border-color: var(--danger, #d9534f); }

        /* Pending approvals */
        .att-pending-list { display: grid; gap: 10px; }
        .att-pending-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; border: 1px solid var(--line); border-radius: 10px; padding: 12px 14px; background: var(--bg-card2, var(--bg2)); }
        .att-pending-row strong { font-size: 13.5px; }
        .att-pending-row span.meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 2px; }
        .att-pending-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .att-reject-box { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; width: 100%; margin-top: 8px; }
        .att-reject-box textarea { flex: 1 1 220px; min-height: 40px; border: 1px solid var(--line); border-radius: 8px; padding: 8px 10px; font: inherit; font-size: 12.5px; }

        /* Roster table */
        .att-roster { display: grid; gap: 10px; }
        .att-roster-row { border: 1px solid var(--line); border-radius: 10px; background: var(--bg-card2, var(--bg2)); }
        .att-roster-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px; cursor: pointer; }
        .att-roster-top strong { font-size: 13.5px; }
        .att-roster-top span.email { display: block; font-size: 11.5px; color: var(--muted); }
        .att-roster-counts { display: flex; gap: 6px; flex-wrap: wrap; }

        .att-roster-cal { padding: 0 14px 14px; }
        .att-cal-head { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin: 6px 0; }
        .att-cal-head span { text-align: center; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
        .att-cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .att-cal-cell {
            aspect-ratio: 1; border-radius: 6px; border: 1px solid var(--line); background: var(--card);
            display: grid; place-items: center; font-size: 11px; font-weight: 600; color: var(--muted); cursor: pointer;
        }
        .att-cal-cell.out-month { opacity: .3; cursor: default; }
        .att-cal-cell.is-today { outline: 2px solid var(--brand-primary); outline-offset: -2px; }
        .att-cal-cell.st-present { background: color-mix(in srgb, var(--brand-green, #1a9e57) 18%, var(--card)); color: #0f6b32; }
        .att-cal-cell.st-absent { background: color-mix(in srgb, var(--danger, #d9534f) 14%, var(--card)); color: var(--danger, #d9534f); }
        .att-cal-cell.st-leave { background: color-mix(in srgb, var(--brand-secondary, #6c63ff) 16%, var(--card)); color: var(--brand-secondary, #6c63ff); }
        .att-cal-cell.st-half_day { background: color-mix(in srgb, var(--warning, #d98a00) 16%, var(--card)); color: var(--warning, #d98a00); }

        .att-empty { border: 1px dashed var(--line); border-radius: 10px; padding: 16px; text-align: center; color: var(--muted); font-size: 12.5px; }
    </style>

    <div class="att-page" x-data="{}">

        {{-- Header / filters --}}
        <section class="att-card att-pad">
            <div class="att-topbar" style="margin-bottom:10px;">
                <div>
                    <h2>Team Attendance</h2>
                    <p>All students · approve, review, and correct attendance.</p>
                </div>
                <div class="att-month-nav">
                    <button type="button" wire:click="previousMonth" wire:loading.attr="disabled" wire:target="previousMonth" aria-label="Previous month">&larr;</button>
                    <span class="att-month-label">{{ $this->cursor->format('F Y') }}</span>
                    <button type="button" wire:click="nextMonth" wire:loading.attr="disabled" wire:target="nextMonth" @disabled($this->isCurrentMonth) aria-label="Next month">&rarr;</button>
                </div>
            </div>
            <input type="search" class="att-search" placeholder="Search student by name or email…" wire:model.live.debounce.400ms="search">
        </section>

        {{-- Pending approvals --}}
        <section class="att-card att-pad">
            <div class="att-topbar" style="margin-bottom:12px;">
                <h2>Pending Approvals</h2>
                <span class="att-badge b-pending">{{ $this->pendingApprovals->count() }} waiting</span>
            </div>

            <div class="att-pending-list">
                @forelse ($this->pendingApprovals as $a)
                    <div class="att-pending-row" wire:key="pending-{{ $a->id }}">
                        <div>
                            <strong>{{ $a->user?->name ?? 'Unknown student' }}</strong>
                            <span class="meta">
                                {{ $a->date->format('d M Y') }} ·
                                {{ $a->statusLabel() }}
                                @if ($a->check_in_at) · In {{ $a->check_in_at->format('h:i A') }} @endif
                                @if ($a->check_out_at) · Out {{ $a->check_out_at->format('h:i A') }} @endif
                            </span>
                        </div>

                        <div class="att-pending-actions">
                            <button type="button" class="att-btn att-btn-primary"
                                x-on:click.prevent="
                                    Swal.fire({
                                        icon: 'question',
                                        title: 'Approve attendance?',
                                        text: 'Mark {{ $a->user?->name }}\'s {{ $a->date->format('d M') }} attendance as approved.',
                                        showCancelButton: true,
                                        confirmButtonText: 'Approve',
                                    }).then((r) => { if (r.isConfirmed) $wire.approve({{ $a->id }}) })
                                ">
                                Approve
                            </button>

                            @if ($this->reviewingAttendanceId === $a->id)
                                <button type="button" class="att-btn att-btn-soft" wire:click="cancelReject">Cancel</button>
                            @else
                                <button type="button" class="att-btn att-btn-soft" wire:click="startReject({{ $a->id }})">Reject</button>
                            @endif
                        </div>

                        @if ($this->reviewingAttendanceId === $a->id)
                            <div class="att-reject-box">
                                <textarea wire:model.live="reviewNotes" placeholder="Reason for rejection…"></textarea>
                                <button type="button" class="att-btn att-btn-danger"
                                    x-on:click.prevent="
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Reject this attendance?',
                                            text: 'This will be recorded with your note.',
                                            showCancelButton: true,
                                            confirmButtonText: 'Reject',
                                        }).then((r) => { if (r.isConfirmed) $wire.reject({{ $a->id }}) })
                                    ">
                                    Confirm Reject
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="att-empty">Nothing pending this month — all caught up.</div>
                @endforelse
            </div>
        </section>

        {{-- Roster --}}
        <section class="att-card att-pad">
            <h2 style="margin:0 0 12px;">Student Roster — {{ $this->cursor->format('F Y') }}</h2>

            <div class="att-roster">
                @forelse ($this->students as $student)
                    @php $summary = $this->attendanceByUser[$student->id] ?? null; @endphp
                    <div class="att-roster-row" wire:key="student-{{ $student->id }}">
                        <div class="att-roster-top" wire:click="toggleExpand({{ $student->id }})">
                            <div>
                                <strong>{{ $student->name }}</strong>
                                <span class="email">{{ $student->email }}</span>
                            </div>
                            <div class="att-roster-counts">
                                <span class="att-badge b-present">{{ $summary['present'] ?? 0 }} present</span>
                                <span class="att-badge b-absent">{{ $summary['absent'] ?? 0 }} absent</span>
                                <span class="att-badge">{{ $summary['leave'] ?? 0 }} leave</span>
                                @if (($summary['pending'] ?? 0) > 0)
                                    <span class="att-badge b-pending">{{ $summary['pending'] }} pending</span>
                                @endif
                            </div>
                        </div>

                        @if ($this->expandedUserId === $student->id)
                            <div class="att-roster-cal">
                                <div class="att-cal-head">
                                    @foreach ($weekLabels as $label)
                                        <span>{{ $label }}</span>
                                    @endforeach
                                </div>
                                <div style="display:grid; gap:4px;">
                                    @foreach ($this->calendarWeeksFor($student->id) as $week)
                                        <div class="att-cal-grid">
                                            @foreach ($week as $day)
                                                <div class="att-cal-cell
                                                    {{ $day['inMonth'] ? '' : 'out-month' }}
                                                    {{ $day['isToday'] ? 'is-today' : '' }}
                                                    {{ $day['status'] ? 'st-'.$day['status'] : '' }}"
                                                    title="{{ $day['date']->format('d M Y') }}{{ $day['status'] ? ' · '.ucfirst(str_replace('_',' ',$day['status'])) : '' }}"
                                                    @if ($day['inMonth'] && ! $day['isFuture'])
                                                        x-on:click.prevent="
                                                            Swal.fire({
                                                                title: 'Update attendance',
                                                                text: '{{ $day['date']->format('d M Y') }} — {{ $student->name }}',
                                                                input: 'select',
                                                                inputOptions: { present: 'Present', absent: 'Absent', leave: 'Leave', half_day: 'Half Day' },
                                                                inputValue: '{{ $day['status'] ?? 'absent' }}',
                                                                showCancelButton: true,
                                                                confirmButtonText: 'Save',
                                                            }).then((r) => {
                                                                if (r.isConfirmed && r.value) {
                                                                    $wire.call('markManually', {{ $student->id }}, '{{ $day['date']->format('Y-m-d') }}', r.value)
                                                                }
                                                            })
                                                        "
                                                    @endif
                                                >{{ $day['date']->day }}</div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="att-empty">No students match your search.</div>
                @endforelse
            </div>

            <div style="margin-top:14px;">
                {{ $this->students->links('pagination.custom') }}
            </div>
        </section>
    </div>

    {{-- SweetAlert2 wiring --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const toast = (icon, title, text) => {
                if (!window.Swal) return;
                Swal.fire({ icon, title, text, timer: 2200, showConfirmButton: false,  });
            };

            Livewire.on('attendance-saved', (p) => toast('success', 'Done', (Array.isArray(p) ? p[0]?.message : p?.message) ?? 'Attendance updated.'));
            Livewire.on('validation-failed', (p) => {
                if (!window.Swal) return;
                Swal.fire({ icon: 'warning', title: 'Check your input', text: (Array.isArray(p) ? p[0]?.message : p?.message) ?? 'Please review and try again.' });
            });
        });
    </script>
</div>