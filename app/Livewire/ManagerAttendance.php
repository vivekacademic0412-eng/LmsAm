<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ManagerAttendance extends Component
{
    use WithPagination;

    #[Url(as: 'month', history: true)]
    public string $month = '';

    #[Url(as: 'q', history: true)]
    public string $search = '';

    public ?int $expandedUserId = null;

    public ?int $reviewingAttendanceId = null;
    public string $reviewNotes = '';

    public function mount(): void
    {
        $user = Auth::user();

        // Admins and managers both get the full roster view.
        abort_unless(in_array($user?->role, [User::ROLE_MANAGER_HR, User::ROLE_ADMIN], true), 403);

        $this->month = $this->month ?: now()->format('Y-m');
    }

    #[Computed]
    public function cursor(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
    }

    #[Computed]
    public function isCurrentMonth(): bool
    {
        return $this->cursor->isSameMonth(now());
    }

    #[Computed]
    public function students()
    {
        return User::query()
            ->where('role', User::ROLE_STUDENT)
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12);
    }

    #[Computed]
    public function attendanceByUser(): array
    {
        $userIds = $this->students->pluck('id')->all();

        if (empty($userIds)) {
            return [];
        }

        $rows = Attendance::whereIn('user_id', $userIds)
            ->forMonth($this->cursor->year, $this->cursor->month)
            ->get()
            ->groupBy('user_id');

        $summary = [];

        foreach ($userIds as $id) {
            $records = $rows->get($id, collect());

            $summary[$id] = [
                'present' => $records->where('status', Attendance::STATUS_PRESENT)->count(),
                'absent' => $records->where('status', Attendance::STATUS_ABSENT)->count(),
                'leave' => $records->where('status', Attendance::STATUS_LEAVE)->count(),
                'half_day' => $records->where('status', Attendance::STATUS_HALF_DAY)->count(),
                'pending' => $records->where('approval_status', Attendance::APPROVAL_PENDING)->count(),
                'records' => $records->keyBy(fn ($r) => $r->date->format('Y-m-d')),
            ];
        }

        return $summary;
    }

    #[Computed]
    public function calendarWeeksFor(int $userId): array
    {
        $data = $this->attendanceByUser[$userId] ?? null;
        $records = $data['records'] ?? collect();

        $start = $this->cursor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $end = $this->cursor->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $weeks = [];
        $week = [];
        $day = $start->copy();

        while ($day->lte($end)) {
            $record = $records->get($day->format('Y-m-d'));

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

    #[Computed]
    public function pendingApprovals()
    {
        return Attendance::with('user')
            ->pendingApproval()
            ->forMonth($this->cursor->year, $this->cursor->month)
            ->whereHas('user', fn ($q) => $q->where('role', User::ROLE_STUDENT))
            ->orderByDesc('date')
            ->limit(30)
            ->get();
    }

    public function toggleExpand(int $userId): void
    {
        $this->expandedUserId = $this->expandedUserId === $userId ? null : $userId;
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function approve(int $attendanceId): void
    {
        $record = Attendance::with('user')->findOrFail($attendanceId);

        $record->update([
            'approval_status' => Attendance::APPROVAL_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->resetComputed();
        $this->dispatch('attendance-saved', message: 'Approved '.($record->user?->name ?? 'student').'\'s attendance for '.$record->date->format('d M').'.');
    }

    public function startReject(int $attendanceId): void
    {
        $this->reviewingAttendanceId = $attendanceId;
        $this->reviewNotes = '';
    }

    public function cancelReject(): void
    {
        $this->reviewingAttendanceId = null;
        $this->reviewNotes = '';
    }

    public function reject(int $attendanceId): void
    {
        if (trim($this->reviewNotes) === '') {
            $this->dispatch('validation-failed', message: 'Please add a short note explaining the rejection.');

            return;
        }

        $record = Attendance::with('user')->findOrFail($attendanceId);

        $record->update([
            'approval_status' => Attendance::APPROVAL_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);

        $this->reviewingAttendanceId = null;
        $this->reviewNotes = '';
        $this->resetComputed();

        $this->dispatch('attendance-saved', message: 'Rejected '.($record->user?->name ?? 'student').'\'s attendance for '.$record->date->format('d M').'.');
    }

    /**
     * Manual override from the per-student calendar (e.g. marking a
     * forgotten day as Leave). Auto-approved since a manager/admin did it.
     */
    public function markManually(int $userId, string $date, string $status): void
    {
        if (! in_array($status, Attendance::STATUSES, true)) {
            $this->dispatch('validation-failed', message: 'Invalid attendance status selected.');

            return;
        }

        $student = User::where('id', $userId)->where('role', User::ROLE_STUDENT)->first();

        if (! $student) {
            $this->dispatch('validation-failed', message: 'Student not found.');

            return;
        }

        Attendance::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'status' => $status,
                'approval_status' => Attendance::APPROVAL_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'marked_by' => Auth::user()->role === User::ROLE_ADMIN ? Attendance::MARKED_ADMIN : Attendance::MARKED_MANAGER,
            ]
        );

        $this->resetComputed();
        $this->dispatch('attendance-saved', message: 'Updated '.$student->name."'s attendance for ".Carbon::parse($date)->format('d M').'.');
    }

    protected function resetComputed(): void
    {
        unset($this->attendanceByUser, $this->pendingApprovals);
    }

    public function render()
    {
        return view('livewire.manager-attendance');
    }
}