<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseItemSubmission;
use App\Models\CourseProgress;
use App\Models\CourseSessionItem;
use App\Models\DemoFeatureVideo;
use App\Models\DemoReviewVideo;
use App\Models\DemoTask;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\User;
use App\Services\StudentCertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Schema;
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
            'inactive_users' => User::where('is_active', false)->count(),
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'trainers' => User::where('role', User::ROLE_TRAINER)->count(),
            'live_quizzes' => Schema::hasTable('course_session_items') && Schema::hasColumn('course_session_items', 'is_live')
                ? CourseSessionItem::where('item_type', CourseSessionItem::TYPE_QUIZ)
                    ->where('is_live', true)
                    ->count()
                : 0,
            'pending_reviews' => $this->countLatestPendingReviews(),
            'completed_certificates' => $this->countCompletedCertificates(),
        ];

        $studentResumeItem = null;
        $studentPendingActionItems = collect();
        $studentPendingActionSummary = [
            'tasks' => 0,
            'live_quizzes' => 0,
            'total' => 0,
        ];
        $studentRecentSubmissions = collect();
        $studentCertificates = collect();

        if ($user->role === User::ROLE_STUDENT) {
            $studentDashboard = $this->resolveStudentDashboardData($user);
            $learningItems = $studentDashboard['learningItems'];
            $studentResumeItem = $studentDashboard['resumeItem'];
            $studentPendingActionItems = $studentDashboard['pendingActionItems'];
            $studentPendingActionSummary = $studentDashboard['pendingActionSummary'];
            $studentRecentSubmissions = $studentDashboard['recentSubmissions'];
            $studentCertificates = $studentDashboard['certificates'];
        } else {
            $learningItems = $this->resolveLearningItems($user);
        }

        $heroCourse = $learningItems->sortByDesc('progress_percent')->first();
        if ($user->role === User::ROLE_STUDENT && $studentResumeItem) {
            $heroCourse = $learningItems->firstWhere('course_id', $studentResumeItem['course_id']) ?: $heroCourse;
        }
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

        $notifications = collect();
        if (Schema::hasTable('notifications')) {
            $notifications = $user->notifications()
                ->latest()
                ->take(5)
                ->get();
        }

        $assignedCourseIds = [];
        if ($user->role === User::ROLE_TRAINER) {
            $assignedCourseIds = CourseEnrollment::where('trainer_id', $user->id)
                ->pluck('course_id')
                ->all();
        }

        $demoAssignments = collect();
        $demoCategories = collect();
        $demoFeatureVideos = collect();
        $demoReviewVideos = collect();
        $demoTasks = collect();
        if ($dashboardMode === 'demo') {
            $userAssignments = DemoTaskAssignment::query()
                ->with(['demoTask.creator:id,role'])
                ->where('user_id', $user->id)
                ->latest('assigned_at')
                ->latest('id')
                ->get();

            $latestDemoSubmissions = DemoTaskSubmission::query()
                ->whereIn('demo_task_assignment_id', $userAssignments->pluck('id'))
                ->latest('submitted_at')
                ->get()
                ->groupBy('demo_task_assignment_id')
                ->map->first();

            $demoAssignments = $userAssignments
                ->filter(fn (DemoTaskAssignment $assignment): bool => (bool) $assignment->demoTask)
                ->values()
                ->map(function (DemoTaskAssignment $assignment) use ($latestDemoSubmissions) {
                    $task = $assignment->demoTask;

                    return [
                        'assignment' => $assignment,
                        'task' => $task,
                        'submission' => $latestDemoSubmissions->get($assignment->id),
                    ];
                });

            $demoTasks = $demoAssignments->pluck('task');

            $demoCategories = CourseCategory::with([
                'courses' => fn ($q) => $q->with(['category', 'subcategory'])->orderBy('title'),
                'children.courses' => fn ($q) => $q->with(['category', 'subcategory'])->orderBy('title'),
            ])->whereNull('parent_id')->orderBy('name')->get();

            $demoFeatureVideosQuery = DemoFeatureVideo::query();

            if (Schema::hasColumn('demo_feature_videos', 'position')) {
                $demoFeatureVideosQuery
                    ->orderByRaw('CASE WHEN position IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('position')
                    ->orderByDesc('id');
            } else {
                $demoFeatureVideosQuery->latest('id');
            }

            $demoFeatureVideos = $demoFeatureVideosQuery->get();

            if (Schema::hasTable('demo_review_videos')) {
                $demoReviewVideos = DemoReviewVideo::query()
                    ->orderByRaw('CASE WHEN position IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('position')
                    ->orderByDesc('id')
                    ->get();
            }
        }

        if (in_array($dashboardMode, ['admin', 'viewer'], true)) {
            $demoTasks = DemoTask::withCount('assignments')->latest('id')->take(8)->get();
        }

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
            'topics',
            'notifications',
            'assignedCourseIds',
            'studentResumeItem',
            'studentPendingActionItems',
            'studentPendingActionSummary',
            'studentRecentSubmissions',
            'studentCertificates',
            'demoAssignments',
            'demoCategories',
            'demoFeatureVideos',
            'demoReviewVideos',
            'demoTasks'
        ));
    }

    public function markAllNotificationsRead(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && Schema::hasTable('notifications')) {
            $user->unreadNotifications()->update([
                'read_at' => now(),
            ]);
        }

        return back()->with('success', 'Notifications marked as read.');
    }

    public function markNotificationRead(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user
            && $notification->notifiable_type === $user::class
            && (string) $notification->notifiable_id === (string) $user->getKey(),
            403
        );

        if (Schema::hasTable('notifications') && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    private function resolveDashboardMode(User $user): string
    {
        if ($user->role === User::ROLE_STUDENT) {
            return 'student';
        }

        if ($user->role === User::ROLE_TRAINER) {
            return 'trainer';
        }

        if ($user->role === User::ROLE_DEMO) {
            return 'demo';
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
            $trainerStats = $this->resolveTrainerOverviewStats($user);

            return [
                ['code' => 'ST', 'value' => $trainerStats['assigned_students'], 'label' => 'Assigned Students'],
                ['code' => 'CR', 'value' => $trainerStats['assigned_courses'], 'label' => 'Assigned Courses'],
                ['code' => 'EN', 'value' => $trainerStats['active_enrollments'], 'label' => 'Active Enrollments'],
                ['code' => 'PQ', 'value' => $trainerStats['pending_reviews'], 'label' => 'Pending Review'],
                ['code' => 'RQ', 'value' => $trainerStats['revision_requested'], 'label' => 'Revision Requested'],
                ['code' => 'PR', 'value' => $trainerStats['avg_progress'], 'label' => 'Avg Learner Progress', 'suffix' => '%'],
            ];
        }

        if ($dashboardMode === 'admin') {
            return [
                ['code' => 'U', 'value' => $stats['users'], 'label' => 'Total Users'],
                ['code' => 'A', 'value' => $stats['active_users'], 'label' => 'Active Users'],
                ['code' => 'IU', 'value' => $stats['inactive_users'], 'label' => 'Inactive Users'],
                ['code' => 'EN', 'value' => $stats['enrollments'], 'label' => 'Enrollments'],
                ['code' => 'CR', 'value' => $stats['courses'], 'label' => 'Courses'],
                ['code' => 'PR', 'value' => $stats['pending_reviews'], 'label' => 'Pending Review'],
                ['code' => 'LQ', 'value' => $stats['live_quizzes'], 'label' => 'Live Quizzes'],
                ['code' => 'CC', 'value' => $stats['completed_certificates'], 'label' => 'Certificates'],
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
                ['label' => 'Submission Review', 'route' => route('submissions.index')],
                ['label' => 'Broadcast Notifications', 'route' => route('broadcast-notifications.index')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Demo Feature Video', 'route' => route('demo-feature-video.index')],
                ['label' => 'Reviews', 'route' => route('demo-review-videos.index')],
            ];
        }

        if ($user->role === User::ROLE_TRAINER) {
            return [
                ['label' => 'Review Queue', 'route' => route('trainer.submissions')],
                ['label' => 'Assigned Students', 'route' => route('trainer.assigned-students')],
                ['label' => 'Trainer Tracking', 'route' => route('trainer.progress')],
                ['label' => 'My Courses', 'route' => route('trainer.courses')],
            ];
        }

        if ($user->role === User::ROLE_DEMO) {
            return [];
        }

        if ($user->role === User::ROLE_STUDENT) {
            return [
                ['label' => 'My Courses', 'route' => route('student.courses')],
                ['label' => 'My History', 'route' => route('student.history')],
                ['label' => 'My Certificates', 'route' => route('student.certificates')],
                ['label' => 'Course Catalog', 'route' => route('courses.index')],
            ];
        }

        if ($user->role === User::ROLE_MANAGER_HR) {
            return [
                ['label' => 'HR Panel', 'route' => route('panel.manager_hr')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
            ];
        }

        if ($user->role === User::ROLE_IT) {
            return [
                ['label' => 'IT Panel', 'route' => route('panel.it')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
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
            User::ROLE_DEMO => 'Demo account for task previews and sample catalog.',
            default => 'Role-restricted workspace.',
        };
    }

    /**
     * @return array{
     *     assigned_students:int,
     *     assigned_courses:int,
     *     active_enrollments:int,
     *     avg_progress:int,
     *     pending_reviews:int,
     *     revision_requested:int
     * }
     */
    private function resolveTrainerOverviewStats(User $trainer): array
    {
        $trainerEnrollments = CourseEnrollment::query()
            ->with(['progressItems:id,course_enrollment_id,completed_at'])
            ->where('trainer_id', $trainer->id)
            ->get(['id', 'course_id', 'student_id']);

        $courseIds = $trainerEnrollments->pluck('course_id')->unique()->filter()->values();
        $totalItemsByCourse = collect();

        if ($courseIds->isNotEmpty()) {
            $totalItemsByCourse = DB::table('course_session_items')
                ->join('course_sessions', 'course_sessions.id', '=', 'course_session_items.course_session_id')
                ->join('course_weeks', 'course_weeks.id', '=', 'course_sessions.course_week_id')
                ->whereIn('course_weeks.course_id', $courseIds)
                ->groupBy('course_weeks.course_id')
                ->select('course_weeks.course_id', DB::raw('count(course_session_items.id) as total_items'))
                ->pluck('total_items', 'course_weeks.course_id');
        }

        $avgProgress = (int) round((float) $trainerEnrollments
            ->map(function (CourseEnrollment $enrollment) use ($totalItemsByCourse): int {
                $totalItems = max(1, (int) ($totalItemsByCourse[$enrollment->course_id] ?? 1));
                $completedItems = min(
                    $totalItems,
                    $enrollment->progressItems->whereNotNull('completed_at')->count()
                );

                return (int) round(($completedItems / $totalItems) * 100);
            })
            ->avg());

        $reviewCounts = $this->resolveTrainerReviewCounts($trainer);

        return [
            'assigned_students' => $trainerEnrollments->pluck('student_id')->unique()->count(),
            'assigned_courses' => $courseIds->count(),
            'active_enrollments' => $trainerEnrollments->count(),
            'avg_progress' => $avgProgress,
            'pending_reviews' => $reviewCounts['pending_reviews'],
            'revision_requested' => $reviewCounts['revision_requested'],
        ];
    }

    /**
     * @return array{pending_reviews:int, revision_requested:int}
     */
    private function resolveTrainerReviewCounts(User $trainer): array
    {
        if (! Schema::hasTable('course_item_submissions') || ! Schema::hasColumn('course_item_submissions', 'review_status')) {
            return [
                'pending_reviews' => 0,
                'revision_requested' => 0,
            ];
        }

        $trainerEnrollmentIds = CourseEnrollment::query()
            ->where('trainer_id', $trainer->id)
            ->pluck('id');

        if ($trainerEnrollmentIds->isEmpty()) {
            return [
                'pending_reviews' => 0,
                'revision_requested' => 0,
            ];
        }

        $latestSubmissionIds = CourseItemSubmission::query()
            ->whereIn('course_enrollment_id', $trainerEnrollmentIds)
            ->selectRaw('MAX(id) as id')
            ->groupBy('course_enrollment_id', 'course_session_item_id')
            ->pluck('id');

        if ($latestSubmissionIds->isEmpty()) {
            return [
                'pending_reviews' => 0,
                'revision_requested' => 0,
            ];
        }

        return [
            'pending_reviews' => CourseItemSubmission::query()
                ->whereIn('id', $latestSubmissionIds)
                ->where('review_status', CourseItemSubmission::STATUS_PENDING_REVIEW)
                ->count(),
            'revision_requested' => CourseItemSubmission::query()
                ->whereIn('id', $latestSubmissionIds)
                ->where('review_status', CourseItemSubmission::STATUS_REVISION_REQUESTED)
                ->count(),
        ];
    }

    private function countLatestPendingReviews(): int
    {
        if (! Schema::hasTable('course_item_submissions') || ! Schema::hasColumn('course_item_submissions', 'review_status')) {
            return 0;
        }

        $latestSubmissionIds = CourseItemSubmission::query()
            ->selectRaw('MAX(id) as id')
            ->groupBy('course_enrollment_id', 'course_session_item_id')
            ->pluck('id');

        if ($latestSubmissionIds->isEmpty()) {
            return 0;
        }

        return CourseItemSubmission::query()
            ->whereIn('id', $latestSubmissionIds)
            ->where('review_status', CourseItemSubmission::STATUS_PENDING_REVIEW)
            ->count();
    }

    private function countCompletedCertificates(): int
    {
        $requiredTables = [
            'course_session_items',
            'course_sessions',
            'course_weeks',
            'course_progress',
            'course_enrollments',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                return 0;
            }
        }

        $totalItemsByCourse = DB::table('course_session_items')
            ->join('course_sessions', 'course_sessions.id', '=', 'course_session_items.course_session_id')
            ->join('course_weeks', 'course_weeks.id', '=', 'course_sessions.course_week_id')
            ->groupBy('course_weeks.course_id')
            ->select('course_weeks.course_id', DB::raw('count(course_session_items.id) as total_items'))
            ->pluck('total_items', 'course_weeks.course_id');

        if ($totalItemsByCourse->isEmpty()) {
            return 0;
        }

        $completedByEnrollment = CourseProgress::query()
            ->whereNotNull('completed_at')
            ->groupBy('course_enrollment_id')
            ->select('course_enrollment_id', DB::raw('count(*) as completed_items'))
            ->pluck('completed_items', 'course_enrollment_id');

        return CourseEnrollment::query()
            ->get(['id', 'course_id'])
            ->filter(function (CourseEnrollment $enrollment) use ($totalItemsByCourse, $completedByEnrollment): bool {
                $totalItems = (int) ($totalItemsByCourse[$enrollment->course_id] ?? 0);

                if ($totalItems <= 0) {
                    return false;
                }

                $completedItems = min($totalItems, (int) ($completedByEnrollment[$enrollment->id] ?? 0));

                return $completedItems >= $totalItems;
            })
            ->count();
    }

    /**
     * @return array{
     *     learningItems: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     resumeItem: array<string, mixed>|null,
     *     pendingActionItems: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     pendingActionSummary: array{tasks:int, live_quizzes:int, total:int},
     *     recentSubmissions: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     certificates: \Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    private function resolveStudentDashboardData(User $user): array
    {
        $palette = ['blue', 'green', 'violet', 'orange', 'red', 'teal'];
        $certificateService = app(StudentCertificateService::class);
        $enrollments = CourseEnrollment::query()
            ->where('student_id', $user->id)
            ->with(['course.category', 'course.weeks.sessions.items', 'progressItems'])
            ->latest('id')
            ->get();

        $learningItems = collect();
        $pendingActionItems = collect();
        $certificates = collect();

        foreach ($enrollments as $index => $enrollment) {
            $course = $enrollment->course;

            if (! $course) {
                continue;
            }

            $completedItemIds = $enrollment->progressItems
                ->whereNotNull('completed_at')
                ->pluck('course_session_item_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $courseItems = collect();
            foreach ($course->weeks as $week) {
                foreach ($week->sessions as $session) {
                    foreach ($session->items as $item) {
                        $courseItems->push([
                            'item_id' => (int) $item->id,
                            'title' => $item->title ?: 'Untitled Item',
                            'item_type' => $item->item_type,
                            'is_live' => (bool) $item->is_live,
                            'week_id' => (int) $week->id,
                            'week_number' => (int) ($week->week_number ?? 0),
                            'session_id' => (int) $session->id,
                            'session_number' => (int) ($session->session_number ?? 0),
                            'session_title' => $session->title ?: 'Session',
                            'route' => $this->buildStudentCourseItemRoute(
                                (int) $course->id,
                                (int) $week->id,
                                (int) $session->id,
                                (int) $item->id
                            ),
                        ]);
                    }
                }
            }

            $totalItems = max(1, $courseItems->count());
            $completedItems = min(
                $totalItems,
                $courseItems->whereIn('item_id', $completedItemIds)->count()
            );
            $progressPercent = (int) round(($completedItems / $totalItems) * 100);
            $hoursTotal = max(1, (int) ($course->duration_hours ?? 1));
            $hoursDone = round(($hoursTotal * $progressPercent) / 100, 1);

            $nextPendingItem = $courseItems->first(
                fn (array $courseItem) => ! in_array($courseItem['item_id'], $completedItemIds, true)
            );

            $pendingTaskCount = $courseItems->filter(
                fn (array $courseItem) => $courseItem['item_type'] === CourseSessionItem::TYPE_TASK
                    && ! in_array($courseItem['item_id'], $completedItemIds, true)
            )->count();

            $liveQuizCount = $courseItems->filter(
                fn (array $courseItem) => $courseItem['item_type'] === CourseSessionItem::TYPE_QUIZ
                    && $courseItem['is_live']
                    && ! in_array($courseItem['item_id'], $completedItemIds, true)
            )->count();

            $pendingActionItems = $pendingActionItems->merge(
                $courseItems
                    ->filter(
                        fn (array $courseItem) => ! in_array($courseItem['item_id'], $completedItemIds, true)
                            && (
                                $courseItem['item_type'] === CourseSessionItem::TYPE_TASK
                                || ($courseItem['item_type'] === CourseSessionItem::TYPE_QUIZ && $courseItem['is_live'])
                            )
                    )
                    ->map(function (array $courseItem) use ($course) {
                        $isQuiz = $courseItem['item_type'] === CourseSessionItem::TYPE_QUIZ;

                        return [
                            'course_id' => (int) $course->id,
                            'course_title' => $course->title ?: 'Untitled Course',
                            'item_title' => $courseItem['title'],
                            'item_type' => $courseItem['item_type'],
                            'item_type_label' => $isQuiz ? 'Live Quiz' : 'Task',
                            'status_label' => $isQuiz ? 'Live Now' : 'Pending Submission',
                            'route' => $courseItem['route'],
                            'week_number' => $courseItem['week_number'],
                            'session_number' => $courseItem['session_number'],
                            'session_title' => $courseItem['session_title'],
                        ];
                    })
            );

            $learningItems->push([
                'course_id' => (int) $course->id,
                'title' => $course->title ?: 'Untitled Course',
                'category' => $course->category?->name ?? 'General',
                'provider' => 'LMS Academy',
                'thumbnail_url' => $course->thumbnail_url,
                'progress_percent' => $progressPercent,
                'hours_done' => $hoursDone,
                'hours_total' => $hoursTotal,
                'accent' => $palette[$index % count($palette)],
                'resume_route' => $nextPendingItem['route'] ?? route('student.courses.show', $course),
                'resume_item_title' => $nextPendingItem['title'] ?? 'Open course workspace',
                'resume_item_type' => $nextPendingItem
                    ? ucwords(str_replace('_', ' ', (string) $nextPendingItem['item_type']))
                    : 'Course',
                'pending_tasks_count' => $pendingTaskCount,
                'live_quizzes_count' => $liveQuizCount,
                'has_pending_resume' => $nextPendingItem !== null,
            ]);

            if ($progressPercent >= 100) {
                $certificate = $certificateService->buildCompletedCertificate($enrollment);

                if ($certificate) {
                    $certificates->push($certificate);
                }
            }
        }

        $recentSubmissions = CourseItemSubmission::query()
            ->with(['item.session.week.course', 'reviewer'])
            ->whereHas('enrollment', fn ($query) => $query->where('student_id', $user->id))
            ->latest('submitted_at')
            ->take(5)
            ->get()
            ->map(function (CourseItemSubmission $submission): array {
                $item = $submission->item;
                $session = $item?->session;
                $week = $session?->week;
                $course = $week?->course;

                return [
                    'title' => $item?->title ?? 'Submission',
                    'course_title' => $course?->title ?? 'Course',
                    'submission_type' => strtoupper((string) $submission->submission_type),
                    'status_label' => $submission->reviewStatusLabel(),
                    'status_tone' => $submission->reviewStatusTone(),
                    'submitted_at_human' => optional($submission->submitted_at)->diffForHumans(),
                    'answer_text' => (string) $submission->answer_text,
                    'review_notes' => (string) $submission->review_notes,
                    'file_name' => $submission->file_name,
                    'download_route' => $submission->file_path
                        ? route('course-item-submissions.download', $submission)
                        : null,
                    'open_route' => ($course && $week && $session && $item)
                        ? $this->buildStudentCourseItemRoute((int) $course->id, (int) $week->id, (int) $session->id, (int) $item->id)
                        : route('student.courses'),
                ];
            });

        $resumeCourse = $learningItems->first(fn (array $item) => (bool) $item['has_pending_resume'])
            ?: $learningItems->first();

        $resumeItem = $resumeCourse
            ? [
                'course_id' => $resumeCourse['course_id'],
                'course_title' => $resumeCourse['title'],
                'route' => $resumeCourse['resume_route'],
                'item_title' => $resumeCourse['resume_item_title'],
                'item_type' => $resumeCourse['resume_item_type'],
                'progress_percent' => $resumeCourse['progress_percent'],
                'hours_done' => $resumeCourse['hours_done'],
                'hours_total' => $resumeCourse['hours_total'],
                'pending_tasks_count' => $resumeCourse['pending_tasks_count'],
                'live_quizzes_count' => $resumeCourse['live_quizzes_count'],
            ]
            : null;

        $pendingActionItems = $pendingActionItems
            ->sortByDesc(fn (array $item) => $item['item_type'] === CourseSessionItem::TYPE_QUIZ)
            ->values();

        return [
            'learningItems' => $learningItems->values(),
            'resumeItem' => $resumeItem,
            'pendingActionItems' => $pendingActionItems->take(6)->values(),
            'pendingActionSummary' => [
                'tasks' => $pendingActionItems->where('item_type', CourseSessionItem::TYPE_TASK)->count(),
                'live_quizzes' => $pendingActionItems->where('item_type', CourseSessionItem::TYPE_QUIZ)->count(),
                'total' => $pendingActionItems->count(),
            ],
            'recentSubmissions' => $recentSubmissions,
            'certificates' => $certificates->sortByDesc('issued_at_timestamp')->take(4)->values(),
        ];
    }

    private function buildStudentCourseItemRoute(int $courseId, int $weekId, int $sessionId, int $itemId): string
    {
        return route('student.courses.show', [
            'course' => $courseId,
            'week' => $weekId,
            'session' => $sessionId,
            'item' => $itemId,
        ]).'#learning-workspace';
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
                    'thumbnail_url' => $course?->thumbnail_url,
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
                    'thumbnail_url' => $course->thumbnail_url,
                    'progress_percent' => $progressPercent,
                    'hours_done' => round(($hoursTotal * $progressPercent) / 100, 1),
                    'hours_total' => $hoursTotal,
                    'accent' => $palette[$index % count($palette)],
                ];
            })
            ->values();
    }
}
