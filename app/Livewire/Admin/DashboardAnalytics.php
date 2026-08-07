<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseItemSubmission;
use App\Models\CourseProgress;
use App\Models\CourseSessionItem;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardAnalytics extends Component
{
    public string $range = '30'; // days, for the payments/enrollment trend window

    /* ── Top KPI strip ── */
    #[Computed]
    public function kpis(): array
    {
        return [
            'users'        => User::count(),
            'students'     => User::where('role', User::ROLE_STUDENT)->count(),
            'trainers'     => User::where('role', User::ROLE_TRAINER)->count(),
            'demo_users'   => User::where('role', User::ROLE_DEMO)->count(),
            'courses'      => Course::count(),
            'categories'   => CourseCategory::count(),
            'enrollments'  => CourseEnrollment::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];
    }

    /* ── Payments / revenue ── */
    #[Computed]
    public function paymentStats(): array
    {
        $since = now()->subDays((int) $this->range);

        return [
            'total_revenue'   => Payment::where('status', 'success')->sum('paid_amount'),
            'revenue_period'  => Payment::where('status', 'success')->where('paid_at', '>=', $since)->sum('paid_amount'),
            'pending_count'   => Payment::where('status', 'pending')->count(),
            'pending_amount'  => Payment::where('status', 'pending')->sum('amount'),
            'success_count'   => Payment::where('status', 'success')->count(),
            'failed_count'    => Payment::where('status', 'failed')->count(),
        ];
    }

    #[Computed]
    public function recentPayments()
    {
        return Payment::with(['user', 'category', 'course'])
            ->latest('id')
            ->take(8)
            ->get();
    }

    /* ── Demo funnel ── */
    #[Computed]
    public function demoFunnel(): array
    {
        $assigned = DemoTaskAssignment::count();
        $submitted = DemoTaskSubmission::distinct('demo_task_assignment_id')->count('demo_task_assignment_id');

        return [
            'demo_users'      => User::where('role', User::ROLE_DEMO)->count(),
            'tasks_assigned'  => $assigned,
            'tasks_submitted' => $submitted,
            'pending_review'  => max(0, $assigned - $submitted),
        ];
    }

    #[Computed]
    public function latestDemoSubmissions()
    {
        return DemoTaskSubmission::with(['assignment.user', 'assignment.demoTask'])
            ->latest('submitted_at')
            ->take(6)
            ->get();
    }

    /* ── Enrollments / leads ── */
    #[Computed]
    public function recentEnrollments()
    {
        return CourseEnrollment::with(['student', 'course', 'trainer'])
            ->latest('id')
            ->take(8)
            ->get();
    }

    /* ── Student task submissions (course items, not demo) ── */
    #[Computed]
    public function latestStudentSubmissions()
    {
        return CourseItemSubmission::with(['enrollment.student', 'enrollment.course', 'sessionItem'])
            ->latest('submitted_at')
            ->take(8)
            ->get();
    }

    /* ── Attendance ── */
    #[Computed]
    public function attendanceToday(): array
    {
        $today = Carbon::today();

        $rows = Attendance::where('date', $today)->get();

        return [
            'present'  => $rows->where('status', Attendance::STATUS_PRESENT)->count(),
            'absent'   => $rows->where('status', Attendance::STATUS_ABSENT)->count(),
            'leave'    => $rows->where('status', Attendance::STATUS_LEAVE)->count(),
            'half_day' => $rows->where('status', Attendance::STATUS_HALF_DAY)->count(),
            'pending_approval' => Attendance::where('approval_status', Attendance::APPROVAL_PENDING)->count(),
            'marked_total' => $rows->count(),
        ];
    }

    #[Computed]
    public function recentAttendance()
    {
        return Attendance::with('user')
            ->latest('date')
            ->latest('id')
            ->take(8)
            ->get();
    }

    public function updatedRange(): void
    {
        unset($this->paymentStats, $this->paymentTrend);
    }

    /* ── Payment trend — daily revenue bars, capped to 14 points for readability ── */
    #[Computed]
    public function paymentTrend(): array
    {
        $days = min((int) $this->range, 14);
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Payment::where('status', 'success')
            ->where('paid_at', '>=', $start)
            ->get()
            ->groupBy(fn ($p) => optional($p->paid_at)->format('Y-m-d'));

        $result = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $result[] = [
                'label'  => $date->format('d M'),
                'amount' => (float) $rows->get($key, collect())->sum('paid_amount'),
            ];
        }

        return $result;
    }

    /* ── Attendance trend — present vs absent, last 7 days ── */
    #[Computed]
    public function attendanceTrend(): array
    {
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $rows = Attendance::whereDate('date', $date->toDateString())->get();

            $result[] = [
                'label'   => $date->format('D'),
                'present' => $rows->where('status', Attendance::STATUS_PRESENT)->count(),
                'absent'  => $rows->where('status', Attendance::STATUS_ABSENT)->count(),
                'leave'   => $rows->where('status', Attendance::STATUS_LEAVE)->count(),
            ];
        }

        return $result;
    }

    /**
     * Per-course average student progress — % of (enrollment × item) pairs
     * completed, ranked highest first. Powers the "Student Progress per
     * Course" bar list on the dashboard.
     */
    #[Computed]
    public function courseProgressStats()
    {
        return Course::withCount('enrollments')
            ->get()
            ->filter(fn (Course $course) => $course->enrollments_count > 0)
            ->map(function (Course $course) {
                $totalItems = CourseSessionItem::whereHas(
                    'session.week',
                    fn ($q) => $q->where('course_id', $course->id)
                )->count();

                $enrollmentIds = CourseEnrollment::where('course_id', $course->id)->pluck('id');

                $avgProgress = 0;
                if ($totalItems > 0 && $enrollmentIds->isNotEmpty()) {
                    $completedCount = CourseProgress::whereIn('course_enrollment_id', $enrollmentIds)
                        ->whereNotNull('completed_at')
                        ->count();

                    $possible = $totalItems * $enrollmentIds->count();
                    $avgProgress = $possible > 0 ? (int) round($completedCount / $possible * 100) : 0;
                }

                return [
                    'title'       => $course->title,
                    'enrollments' => $enrollmentIds->count(),
                    'avg_progress' => $avgProgress,
                ];
            })
            ->sortByDesc('avg_progress')
            ->take(8)
            ->values();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-analytics');
    }
}