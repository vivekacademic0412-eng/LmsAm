<?php

namespace App\Livewire\Student;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\CourseSessionItem;
use App\Models\CourseWeekProgress;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StudentDashboard extends Component
{
    /** Which enrolled course's weekly bar chart is shown (defaults to the first). */
    public ?int $activeCourseId = null;

    public function mount(): void
    {
        $first = $this->enrollments->first();
        $this->activeCourseId = $first?->course_id;
    }

    public function selectCourse(int $courseId): void
    {
        $this->activeCourseId = $courseId;
    }

    /* -----------------------------------------------------------------
     |  Enrollment / mode detection
     |----------------------------------------------------------------- */

    #[Computed]
    public function enrollments()
    {
        return CourseEnrollment::with(['course.category', 'course.settings', 'trainer'])
            ->where('student_id', Auth::id())
            ->get();
    }

    #[Computed]
    public function hasCourseEnrollment(): bool
    {
        return $this->enrollments->isNotEmpty();
    }

    /* -----------------------------------------------------------------
     |  Demo-only section
     |----------------------------------------------------------------- */

    #[Computed]
    public function demoAssignments()
    {
        return DemoTaskAssignment::with(['demoTask'])
            ->where('user_id', Auth::id())
            ->latest('assigned_at')
            ->get();
    }

    #[Computed]
    public function demoSubmissions()
    {
        return DemoTaskSubmission::whereIn('demo_task_assignment_id', $this->demoAssignments->pluck('id'))
            ->get()
            ->groupBy('demo_task_assignment_id')
            ->map->first();
    }

    #[Computed]
    public function demoCertificate(): ?Certificate
    {
        return Certificate::where('user_id', Auth::id())
            ->where('type', Certificate::TYPE_DEMO)
            ->latest()
            ->first();
    }

    /* -----------------------------------------------------------------
     |  Course progress (pie + bar chart data)
     |----------------------------------------------------------------- */

    #[Computed]
    public function courseProgressCards()
    {
        return $this->enrollments->map(function (CourseEnrollment $enr) {
            $totalItems = CourseSessionItem::whereHas(
                'session.week',
                fn ($q) => $q->where('course_id', $enr->course_id)
            )->count();

            $completed = CourseProgress::where('course_enrollment_id', $enr->id)
                ->whereNotNull('completed_at')
                ->count();

            $percent = $totalItems > 0 ? (int) round($completed / $totalItems * 100) : 0;

            return [
                'enrollment_id' => $enr->id,
                'course_id'     => $enr->course_id,
                'title'         => $enr->course?->title,
                'category'      => $enr->course?->category?->name,
                'trainer'       => $enr->trainer?->name,
                'total_items'   => $totalItems,
                'completed'     => $completed,
                'percent'       => $percent,
            ];
        });
    }

    /** Weekly bar-chart data for the currently selected course. */
    #[Computed]
    public function activeCourseWeeklyChart()
    {
        if (! $this->activeCourseId) return collect();

        $enrollment = $this->enrollments->firstWhere('course_id', $this->activeCourseId);
        if (! $enrollment) return collect();

        return CourseWeekProgress::where('course_enrollment_id', $enrollment->id)
            ->with('week')
            ->get()
            ->sortBy(fn ($row) => $row->week?->week_number)
            ->map(fn ($row) => [
                'week_number' => $row->week?->week_number,
                'percent'     => $row->percent,
            ])
            ->values();
    }

    #[Computed]
    public function certificates()
    {
        return Certificate::where('user_id', Auth::id())->get();
    }

    /* -----------------------------------------------------------------
     |  Transaction history
     |----------------------------------------------------------------- */

    #[Computed]
    public function paymentHistory()
    {
        return Payment::where('user_id', Auth::id())->latest('id')->get();
    }

    #[Computed]
    public function paymentSummary(): array
    {
        return [
            'total_paid' => $this->paymentHistory->where('status', 'success')->sum('paid_amount'),
            'pending'    => $this->paymentHistory->where('status', 'pending')->count(),
        ];
    }

    /* -----------------------------------------------------------------
     |  Attendance
     |----------------------------------------------------------------- */

    #[Computed]
    public function attendanceRows()
    {
        return Attendance::where('user_id', Auth::id())->latest('date')->take(10)->get();
    }

    #[Computed]
    public function attendanceSummary(): array
    {
        $all = Attendance::where('user_id', Auth::id())->get();

        return [
            'present'  => $all->where('status', Attendance::STATUS_PRESENT)->count(),
            'absent'   => $all->where('status', Attendance::STATUS_ABSENT)->count(),
            'leave'    => $all->where('status', Attendance::STATUS_LEAVE)->count(),
            'half_day' => $all->where('status', Attendance::STATUS_HALF_DAY)->count(),
            'total'    => $all->count(),
        ];
    }

    public function render()
    {
        return view('livewire.student.student-dashboard');
    }
}