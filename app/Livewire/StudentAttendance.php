<?php

namespace App\Livewire;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StudentAttendance extends Component
{
    // Hour of day after which we nudge the student that they still haven't
    // checked in. Purely a client-side reminder — the actual absent marking
    // happens end-of-day via the attendance:mark-absent scheduled command.
    protected const REMINDER_HOUR = 11;

    #[Url(as: 'month', history: true)]
    public string $month = '';

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->month = $this->month ?: now()->format('Y-m');

        if (! $this->hasCheckedInToday && now()->hour >= self::REMINDER_HOUR) {
            $this->dispatch(
                'attendance-checkin-reminder',
                message: "You haven't checked in yet today. If you don't check in, today will be marked Absent."
            );
        }
    }

    #[Computed]
    public function cursor(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
    }

    #[Computed]
    public function records()
    {
        return Attendance::forUser(Auth::id())
            ->forMonth($this->cursor->year, $this->cursor->month)
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));
    }

    #[Computed]
    public function todayRecord(): ?Attendance
    {
        return Attendance::forUser(Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();
    }

    #[Computed]
    public function hasCheckedInToday(): bool
    {
        return (bool) $this->todayRecord?->check_in_at;
    }

    #[Computed]
    public function hasCheckedOutToday(): bool
    {
        return (bool) $this->todayRecord?->check_out_at;
    }

    #[Computed]
    public function stats(): array
    {
        $records = $this->records;

        return [
            'present' => $records->where('status', Attendance::STATUS_PRESENT)->count(),
            'absent' => $records->where('status', Attendance::STATUS_ABSENT)->count(),
            'leave' => $records->where('status', Attendance::STATUS_LEAVE)->count(),
            'half_day' => $records->where('status', Attendance::STATUS_HALF_DAY)->count(),
            'pending' => $records->where('approval_status', Attendance::APPROVAL_PENDING)->count(),
            'marked' => $records->count(),
        ];
    }

    #[Computed]
    public function isCurrentMonth(): bool
    {
        return $this->cursor->isSameMonth(now());
    }

    #[Computed]
    public function calendarWeeks(): array
    {
        $start = $this->cursor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $end = $this->cursor->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $records = $this->records;
        $weeks = [];
        $week = [];
        $day = $start->copy();

        while ($day->lte($end)) {
            $key = $day->format('Y-m-d');
            $record = $records->get($key);

            $week[] = [
                'date' => $day->copy(),
                'inMonth' => $day->month === $this->cursor->month,
                'isToday' => $day->isToday(),
                'isFuture' => $day->isFuture(),
                'status' => $record?->status,
                'approval' => $record?->approval_status,
            ];

            if ((int) $day->dayOfWeek === Carbon::SATURDAY) {
                $weeks[] = $week;
                $week = [];
            }

            $day->addDay();
        }

        if (! empty($week)) {
            $weeks[] = $week;
        }

        return $weeks;
    }

    public function previousMonth(): void
    {
        $this->month = $this->cursor->copy()->subMonth()->format('Y-m');
        $this->resetComputed();
    }

    public function nextMonth(): void
    {
        $next = $this->cursor->copy()->addMonth();

        if ($next->gt(now()->startOfMonth())) {
            return;
        }

        $this->month = $next->format('Y-m');
        $this->resetComputed();
    }

    public function checkIn(): void
    {
        if ($this->hasCheckedInToday) {
            $this->dispatch('attendance-info', message: 'You already checked in today.');

            return;
        }

        Attendance::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => now()->toDateString()],
            [
                'check_in_at' => now(),
                'status' => Attendance::STATUS_PRESENT,
                'approval_status' => Attendance::APPROVAL_PENDING,
                'marked_by' => Attendance::MARKED_SELF,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]
        );

        $this->resetComputed(true);
        $this->dispatch('attendance-saved', message: 'Checked in! Marked Present — awaiting manager approval.');
    }

    public function checkOut(): void
    {
        $record = $this->todayRecord;

        if (! $record || ! $record->check_in_at) {
            $this->dispatch('attendance-warning', message: 'Please check in before checking out.');

            return;
        }

        if ($record->check_out_at) {
            $this->dispatch('attendance-info', message: 'You already checked out today.');

            return;
        }

        $record->update(['check_out_at' => now()]);

        $this->resetComputed(true);
        $this->dispatch('attendance-saved', message: 'Checked out. Have a good day!');
    }

    protected function resetComputed(bool $includeToday = false): void
    {
        unset($this->records, $this->calendarWeeks, $this->stats);

        if ($includeToday) {
            unset($this->todayRecord);
        }
    }

    public function render()
    {
        return view('livewire.student-attendance');
    }
}