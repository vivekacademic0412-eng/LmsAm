<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user?->is_active) {
            abort(403, 'Your account is inactive.');
        }

        $stats = [
            'users' => User::count(),
            'categories' => CourseCategory::count(),
            'courses' => Course::count(),
            'enrollments' => CourseEnrollment::count(),
            'active_users' => User::where('is_active', true)->count(),
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'trainers' => User::where('role', User::ROLE_TRAINER)->count(),
        ];

        $learningItems = $this->resolveLearningItems($user);
        $heroCourse = $learningItems->sortByDesc('progress_percent')->first();
        $skillProgress = $learningItems
            ->groupBy('category')
            ->map(fn ($items, $category) => [
                'skill' => $category,
                'progress' => (int) round($items->avg('progress_percent')),
            ])
            ->values()
            ->take(5);

        $dashboardMode = $this->resolveDashboardMode($user);
        $overviewCards = $this->resolveOverviewCards($dashboardMode, $learningItems, $stats, $user);
        $quickActions = $this->resolveQuickActions($user);
        $panelDescription = $this->resolvePanelDescription($user);
        $panelStats = [
            'users' => $stats['users'],
            'categories' => $stats['categories'],
            'courses' => $stats['courses'],
        ];

        $recommendedCourses = Course::query()
            ->with(['category', 'creator'])
            ->latest('id')
            ->take(6)
            ->get()
            ->map(function (Course $course): array {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'category' => $course->category?->name ?? 'General',
                    'provider' => $course->creator?->name ?? 'LMS Academy',
                    'hours' => max(1, (int) $course->duration_hours),
                ];
            });

        $topics = CourseCategory::query()
            ->withCount('courses')
            ->orderByDesc('courses_count')
            ->take(8)
            ->get()
            ->map(fn (CourseCategory $category): array => [
                'name' => $category->name,
                'count' => (int) $category->courses_count,
            ]);

        return view('dashboard.index', compact(
            'user',
            'dashboardMode',
            'overviewCards',
            'quickActions',
            'panelDescription',
            'panelStats',
            'heroCourse',
            'learningItems',
            'skillProgress',
            'recommendedCourses',
            'topics'
        ));
    }

    private function resolveDashboardMode(User $user): string
    {
        if ($user->role === User::ROLE_STUDENT) {
            return 'student';
        }

        if ($user->role === User::ROLE_TRAINER) {
            return 'trainer';
        }

        if (in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true)) {
            return 'admin';
        }

        return 'viewer';
    }

    /**
     * @param \Illuminate\Support\Collection<int, array<string, mixed>> $learningItems
     * @param array<string, int> $stats
     * @return array<int, array<string, string|int|float>>
     */
    private function resolveOverviewCards(string $dashboardMode, $learningItems, array $stats, User $user): array
    {
        if ($dashboardMode === 'student') {
            return [
                ['code' => 'C', 'value' => $learningItems->count(), 'label' => 'Courses Enrolled'],
                ['code' => 'H', 'value' => round((float) $learningItems->sum('hours_done'), 1), 'label' => 'Total Learning Time', 'suffix' => 'h'],
                ['code' => 'T', 'value' => $learningItems->where('progress_percent', '>=', 100)->count(), 'label' => 'Certificates Earned'],
                ['code' => 'S', 'value' => max(2, min(14, 2 + $learningItems->where('progress_percent', '>=', 50)->count())), 'label' => 'Day Learning Streak'],
            ];
        }

        if ($dashboardMode === 'trainer') {
            $trainerEnrollments = CourseEnrollment::where('trainer_id', $user->id)->get();
            return [
                ['code' => 'ST', 'value' => $trainerEnrollments->pluck('student_id')->unique()->count(), 'label' => 'Assigned Students'],
                ['code' => 'CR', 'value' => $trainerEnrollments->pluck('course_id')->unique()->count(), 'label' => 'Assigned Courses'],
                ['code' => 'EN', 'value' => $trainerEnrollments->count(), 'label' => 'Active Enrollments'],
                ['code' => 'PR', 'value' => (int) round((float) $learningItems->avg('progress_percent')), 'label' => 'Avg Learner Progress', 'suffix' => '%'],
            ];
        }

        if ($dashboardMode === 'admin') {
            return [
                ['code' => 'U', 'value' => $stats['users'], 'label' => 'Total Users'],
                ['code' => 'A', 'value' => $stats['active_users'], 'label' => 'Active Users'],
                ['code' => 'EN', 'value' => $stats['enrollments'], 'label' => 'Enrollments'],
                ['code' => 'CR', 'value' => $stats['courses'], 'label' => 'Courses'],
            ];
        }

        return [
            ['code' => 'CA', 'value' => $stats['categories'], 'label' => 'Categories'],
            ['code' => 'CR', 'value' => $stats['courses'], 'label' => 'Courses'],
            ['code' => 'ST', 'value' => $stats['students'], 'label' => 'Students'],
            ['code' => 'TR', 'value' => $stats['trainers'], 'label' => 'Trainers'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function resolveQuickActions(User $user): array
    {
        if (in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true)) {
            return [
                ['label' => 'User Control', 'route' => route('users.index')],
                ['label' => 'Enrollments', 'route' => route('enrollments.index')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
            ];
        }

        if ($user->role === User::ROLE_TRAINER) {
            return [
                ['label' => 'Trainer Tracking', 'route' => route('trainer.progress')],
                ['label' => 'All Courses', 'route' => route('courses.index')],
            ];
        }

        if ($user->role === User::ROLE_STUDENT) {
            return [
                ['label' => 'My Courses', 'route' => route('student.courses')],
                ['label' => 'Course Catalog', 'route' => route('courses.index')],
            ];
        }

        return [
            ['label' => 'Categories', 'route' => route('course-categories.index')],
            ['label' => 'Courses', 'route' => route('courses.index')],
        ];
    }

    private function resolvePanelDescription(User $user): string
    {
        return match ($user->role) {
            User::ROLE_SUPERADMIN => 'Full control across users, enrollments, categories, and courses.',
            User::ROLE_ADMIN => 'Operational control for users, enrollments, and learning data.',
            User::ROLE_MANAGER_HR, User::ROLE_IT => 'View-only access for catalog and reporting workflows.',
            User::ROLE_TRAINER => 'Track assigned students and monitor their progress.',
            User::ROLE_STUDENT => 'Access enrolled courses and continue course progress.',
            default => 'Role-restricted workspace.',
        };
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function resolveLearningItems(User $user)
    {
        $palette = ['blue', 'green', 'violet', 'orange', 'red', 'teal'];
        $fallbackProgress = [72, 45, 90, 18, 63, 35];
        $learningItems = collect();

        if ($user->role === User::ROLE_STUDENT) {
            $enrollments = CourseEnrollment::query()
                ->where('student_id', $user->id)
                ->with('course.category')
                ->latest('id')
                ->get();

            $enrollmentIds = $enrollments->pluck('id');
            $courseIds = $enrollments->pluck('course_id')->unique();

            $totalItemsByCourse = DB::table('course_session_items')
                ->join('course_sessions', 'course_sessions.id', '=', 'course_session_items.course_session_id')
                ->join('course_weeks', 'course_weeks.id', '=', 'course_sessions.course_week_id')
                ->whereIn('course_weeks.course_id', $courseIds)
                ->groupBy('course_weeks.course_id')
                ->select('course_weeks.course_id', DB::raw('count(course_session_items.id) as total_items'))
                ->pluck('total_items', 'course_weeks.course_id');

            $completedByEnrollment = CourseProgress::query()
                ->whereIn('course_enrollment_id', $enrollmentIds)
                ->whereNotNull('completed_at')
                ->groupBy('course_enrollment_id')
                ->select('course_enrollment_id', DB::raw('count(*) as completed_items'))
                ->pluck('completed_items', 'course_enrollment_id');

            $learningItems = $enrollments->map(function (CourseEnrollment $enrollment, int $index) use ($palette, $totalItemsByCourse, $completedByEnrollment): array {
                $course = $enrollment->course;
                $totalItems = max(1, (int) ($totalItemsByCourse[$enrollment->course_id] ?? 1));
                $completedItems = min($totalItems, (int) ($completedByEnrollment[$enrollment->id] ?? 0));
                $progressPercent = (int) round(($completedItems / $totalItems) * 100);
                $hoursTotal = max(1, (int) ($course?->duration_hours ?? 1));
                $hoursDone = round(($hoursTotal * $progressPercent) / 100, 1);

                return [
                    'course_id' => $course?->id,
                    'title' => $course?->title ?? 'Untitled Course',
                    'category' => $course?->category?->name ?? 'General',
                    'provider' => 'LMS Academy',
                    'progress_percent' => $progressPercent,
                    'hours_done' => $hoursDone,
                    'hours_total' => $hoursTotal,
                    'accent' => $palette[$index % count($palette)],
                ];
            });
        }

        if ($learningItems->isNotEmpty()) {
            return $learningItems->values();
        }

        return Course::query()
            ->with('category')
            ->latest('id')
            ->take(4)
            ->get()
            ->map(function (Course $course, int $index) use ($palette, $fallbackProgress): array {
                $progressPercent = $fallbackProgress[$index % count($fallbackProgress)];
                $hoursTotal = max(1, (int) $course->duration_hours);

                return [
                    'course_id' => $course->id,
                    'title' => $course->title,
                    'category' => $course->category?->name ?? 'General',
                    'provider' => 'LMS Academy',
                    'progress_percent' => $progressPercent,
                    'hours_done' => round(($hoursTotal * $progressPercent) / 100, 1),
                    'hours_total' => $hoursTotal,
                    'accent' => $palette[$index % count($palette)],
                ];
            })
            ->values();
    }
}
