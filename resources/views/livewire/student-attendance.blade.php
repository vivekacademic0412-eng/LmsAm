@php
    $today = $this->todayRecord;
    $weekLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
@endphp

<div class="att-scope" wire:key="student-attendance">

    <style>
        .att-scope { font-family: var(--lw-body, system-ui, sans-serif); color: var(--text); }
        .att-scope * { box-sizing: border-box; }
        .att-page { display: grid; gap: 18px; }

        .att-card { border: 1px solid var(--line); border-radius: var(--radius, 14px); background: var(--card); box-shadow: var(--shadow-card); }
        .att-pad { padding: 18px 20px; }

        .att-topbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; }
        .att-topbar h2 { margin: 0; font-family: var(--lw-display, inherit); font-size: 19px; }
        .att-topbar p { margin: 2px 0 0; font-size: 12.5px; color: var(--muted); }

        .att-checkin-box { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .att-status-pill {
            font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            padding: 6px 12px; border-radius: 999px; border: 1px solid var(--line); color: var(--muted); background: var(--bg2);
        }
        .att-status-pill.st-present { background: color-mix(in srgb, var(--brand-green, #1a9e57) 16%, var(--bg2)); color: #0f6b32; border-color: var(--brand-green, #1a9e57); }
        .att-status-pill.st-absent { background: color-mix(in srgb, var(--danger, #d9534f) 12%, var(--bg2)); color: var(--danger, #d9534f); border-color: var(--danger, #d9534f); }
        .att-status-pill.st-leave { background: color-mix(in srgb, var(--brand-secondary, #6c63ff) 14%, var(--bg2)); color: var(--brand-secondary, #6c63ff); border-color: var(--brand-secondary, #6c63ff); }
        .att-status-pill.st-half_day { background: color-mix(in srgb, var(--warning, #d98a00) 14%, var(--bg2)); color: var(--warning, #d98a00); border-color: var(--warning, #d98a00); }
        .att-status-pill.st-none { }

        .att-btn {
            display: inline-flex; align-items: center; gap: 6px; min-height: 40px; padding: 9px 16px;
            border-radius: 9px; font-size: 13px; font-weight: 700; border: 1px solid transparent; cursor: pointer;
        }
        .att-btn-primary { background: var(--brand-primary); color: #fff; }
        .att-btn-soft { background: var(--bg2); color: var(--brand-primary); border-color: var(--line); }
        .att-btn:disabled { opacity: .5; cursor: not-allowed; }

        .att-stats { display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 10px; }
        .att-stat { border: 1px solid var(--line); border-radius: 10px; background: var(--bg-card2, var(--bg2)); padding: 12px 14px; }
        .att-stat span { display: block; font-size: 10.5px; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); font-weight: 700; }
        .att-stat strong { display: block; font-size: 19px; font-family: var(--lw-display, inherit); margin-top: 2px; }

        .att-month-nav { display: flex; align-items: center; gap: 10px; }
        .att-month-nav button { border: 1px solid var(--line); background: var(--bg2); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; font-weight: 700; }
        .att-month-nav button:disabled { opacity: .4; cursor: not-allowed; }
        .att-month-label { font-weight: 700; font-size: 14px; min-width: 130px; text-align: center; }

        .att-cal-head { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; margin-bottom: 6px; }
        .att-cal-head span { text-align: center; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
        .att-cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
        .att-cal-cell {
            aspect-ratio: 1; border-radius: 8px; border: 1px solid var(--line); background: var(--bg2);
            display: grid; place-items: center; font-size: 12.5px; font-weight: 600; color: var(--muted);
        }
        .att-cal-cell.out-month { opacity: .35; }
        .att-cal-cell.is-today { outline: 2px solid var(--brand-primary); outline-offset: -2px; }
        .att-cal-cell.st-present { background: color-mix(in srgb, var(--brand-green, #1a9e57) 18%, var(--bg2)); color: #0f6b32; }
        .att-cal-cell.st-absent { background: color-mix(in srgb, var(--danger, #d9534f) 14%, var(--bg2)); color: var(--danger, #d9534f); }
        .att-cal-cell.st-leave { background: color-mix(in srgb, var(--brand-secondary, #6c63ff) 16%, var(--bg2)); color: var(--brand-secondary, #6c63ff); }
        .att-cal-cell.st-half_day { background: color-mix(in srgb, var(--warning, #d98a00) 16%, var(--bg2)); color: var(--warning, #d98a00); }

        .att-legend { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 12px; font-size: 11.5px; color: var(--muted); }
        .att-legend i { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px; vertical-align: -1px; }

        @media (max-width: 640px) {
            .att-stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
        }
    </style>

    <div class="att-page" x-data="{}">

        {{-- Check-in / check-out --}}
        <section class="att-card att-pad">
            <div class="att-topbar">
                <div>
                    <h2>Today's Attendance</h2>
                    <p>{{ now()->format('l, d M Y') }}</p>
                </div>

                <div class="att-checkin-box">
                    <span class="att-status-pill st-{{ $today?->status ?? 'none' }}">
                        {{ $today ? $today->statusLabel() : 'Not marked yet' }}
                    </span>

                    @if (! $this->hasCheckedInToday)
                        <button type="button" class="att-btn att-btn-primary" wire:click="checkIn" wire:loading.attr="disabled" wire:target="checkIn">
                            Check In
                        </button>
                    @elseif (! $this->hasCheckedOutToday)
                        <button type="button" class="att-btn att-btn-soft" wire:click="checkOut" wire:loading.attr="disabled" wire:target="checkOut">
                            Check Out
                        </button>
                    @else
                        <span style="font-size:12px;color:var(--muted);">
                            In {{ $today->check_in_at->format('h:i A') }} · Out {{ $today->check_out_at->format('h:i A') }}
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Monthly stats --}}
        <section class="att-card att-pad">
            <div class="att-topbar" style="margin-bottom:14px;">
                <h2>This Month</h2>
                <div class="att-month-nav">
                    <button type="button" wire:click="previousMonth" wire:loading.attr="disabled" wire:target="previousMonth" aria-label="Previous month">&larr;</button>
                    <span class="att-month-label">{{ $this->cursor->format('F Y') }}</span>
                    <button type="button" wire:click="nextMonth" wire:loading.attr="disabled" wire:target="nextMonth" @disabled($this->isCurrentMonth) aria-label="Next month">&rarr;</button>
                </div>
            </div>

            <div class="att-stats">
                <div class="att-stat"><span>Present</span><strong>{{ $this->stats['present'] }}</strong></div>
                <div class="att-stat"><span>Absent</span><strong>{{ $this->stats['absent'] }}</strong></div>
                <div class="att-stat"><span>Leave</span><strong>{{ $this->stats['leave'] }}</strong></div>
                <div class="att-stat"><span>Half Day</span><strong>{{ $this->stats['half_day'] }}</strong></div>
                <div class="att-stat"><span>Pending Review</span><strong>{{ $this->stats['pending'] }}</strong></div>
            </div>
        </section>

        {{-- Calendar --}}
        <section class="att-card att-pad">
            <h2 style="margin:0 0 12px;">Calendar</h2>

            <div class="att-cal-head">
                @foreach ($weekLabels as $label)
                    <span>{{ $label }}</span>
                @endforeach
            </div>

            <div style="display:grid; gap:6px;">
                @foreach ($this->calendarWeeks as $week)
                    <div class="att-cal-grid">
                        @foreach ($week as $day)
                            <div class="att-cal-cell
                                {{ $day['inMonth'] ? '' : 'out-month' }}
                                {{ $day['isToday'] ? 'is-today' : '' }}
                                {{ $day['status'] ? 'st-'.$day['status'] : '' }}"
                                title="{{ $day['date']->format('d M Y') }}{{ $day['status'] ? ' · '.ucfirst(str_replace('_',' ',$day['status'])) : '' }}"
                            >
                                {{ $day['date']->day }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="att-legend">
                <span><i style="background: color-mix(in srgb, var(--brand-green, #1a9e57) 18%, var(--bg2));"></i>Present</span>
                <span><i style="background: color-mix(in srgb, var(--danger, #d9534f) 14%, var(--bg2));"></i>Absent</span>
                <span><i style="background: color-mix(in srgb, var(--brand-secondary, #6c63ff) 16%, var(--bg2));"></i>Leave</span>
                <span><i style="background: color-mix(in srgb, var(--warning, #d98a00) 16%, var(--bg2));"></i>Half Day</span>
            </div>
        </section>
    </div>

    {{-- SweetAlert2 wiring --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const toast = (icon, title, text) => {
                if (!window.Swal) return;
                Swal.fire({ icon, title, text, timer: 2200, showConfirmButton: false,});
            };

            Livewire.on('attendance-saved', (p) => toast('success', 'Saved', (Array.isArray(p) ? p[0]?.message : p?.message) ?? 'Attendance updated.'));
            Livewire.on('attendance-info', (p) => toast('info', 'Heads up', (Array.isArray(p) ? p[0]?.message : p?.message) ?? ''));
            Livewire.on('attendance-warning', (p) => toast('warning', 'Wait', (Array.isArray(p) ? p[0]?.message : p?.message) ?? ''));
            Livewire.on('validation-failed', (p) => {
                if (!window.Swal) return;
                Swal.fire({ icon: 'warning', title: 'Check your input', text: (Array.isArray(p) ? p[0]?.message : p?.message) ?? 'Please review and try again.' });
            });

            // The "you'll be marked absent" nudge — shown once on page load if
            // the student hasn't checked in yet past the reminder cutoff.
            Livewire.on('attendance-checkin-reminder', (p) => {
                if (!window.Swal) return;
                const message = (Array.isArray(p) ? p[0]?.message : p?.message) ?? "You haven't checked in yet today.";
                Swal.fire({
                    icon: 'warning',
                    title: "You haven't checked in today",
                    text: message,
                    showCancelButton: true,
                    confirmButtonText: 'Check in now',
                    cancelButtonText: 'Later',
                }).then((r) => {
                    if (r.isConfirmed) {
                        $wire.checkIn();
                    }
                });
            });
        });
    </script>
</div>