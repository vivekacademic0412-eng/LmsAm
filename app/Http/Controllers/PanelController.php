<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\CourseSessionItem;
use App\Models\DemoFeatureVideo;
use App\Models\DemoReviewVideo;
use App\Models\DemoTaskSubmission;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CloudinaryPrivateMediaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function show(Request $request, string $role): View
    {
        $user = $request->user();

        abort_unless($user && $user->role === $role, 403, 'You do not have permission to access this panel.');

        $quickActions = match ($role) {
            User::ROLE_SUPERADMIN, User::ROLE_ADMIN => [
                ['label' => 'User Control', 'route' => route('users.index')],
                ['label' => 'Enrollments', 'route' => route('enrollments.index')],
                ['label' => 'Submission Review', 'route' => route('submissions.index')],
                ['label' => 'Broadcast Notifications', 'route' => route('broadcast-notifications.index')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Demo Feature Video', 'route' => route('demo-feature-video.index')],
                ['label' => 'Reviews', 'route' => route('demo-review-videos.index')],
            ],
            User::ROLE_MANAGER_HR, User::ROLE_IT => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
            ],
            User::ROLE_STUDENT => [
                ['label' => 'My Courses', 'route' => route('student.courses')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Dashboard', 'route' => route('dashboard')],
            ],
            default => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
            ],
        };

        if ($role === User::ROLE_TRAINER) {
            $enrollments = CourseEnrollment::with(['course', 'student', 'progressItems'])
                ->where('trainer_id', $user->id)
                ->get();

            $rows = $enrollments->map(function (CourseEnrollment $enrollment) {
                $totalItems = CourseSessionItem::whereHas('session.week', fn ($q) => $q->where('course_id', $enrollment->course_id))->count();
                $completedItems = $enrollment->progressItems->whereNotNull('completed_at')->count();
                $progressPercent = $totalItems > 0 ? (int) floor(($completedItems / $totalItems) * 100) : 0;

                return [
                    'enrollment' => $enrollment,
                    'total_items' => $totalItems,
                    'completed_items' => $completedItems,
                    'progress_percent' => $progressPercent,
                ];
            });

            $assignedCourses = $enrollments
                ->pluck('course')
                ->filter()
                ->unique('id')
                ->values();

            return view('panels.show', [
                'panelRole' => $role,
                'trainerRows' => $rows,
                'trainerCourses' => $assignedCourses,
                'quickActions' => $quickActions,
            ]);
        }

        $sharedData = [
            'panelRole' => $role,
            'quickActions' => $quickActions,
        ];

        if ($role === User::ROLE_MANAGER_HR) {
            return view('panels.show', $sharedData + $this->buildManagerHrPanelData() + [
                'managerHrReportExports' => $this->managerHrReportExports(),
            ]);
        }

        if ($role === User::ROLE_IT) {
            return view('panels.show', $sharedData + $this->buildItPanelData());
        }

        return view('panels.show', $sharedData + [
            'stats' => [
                'users' => User::count(),
                'categories' => CourseCategory::count(),
                'courses' => Course::count(),
            ],
        ]);
    }

    public function exportManagerHrReport(Request $request, string $report, string $format): Response
    {
        $user = $request->user();

        abort_unless($user?->role === User::ROLE_MANAGER_HR, 403);

        $validReports = array_keys($this->managerHrReportDefinitions());
        $validFormats = ['pdf', 'xls'];

        abort_unless(in_array($report, $validReports, true), 404);
        abort_unless(in_array($format, $validFormats, true), 404);

        $payload = $this->buildManagerHrReportPayload($report);
        $filename = sprintf(
            'manager-hr-%s-%s.%s',
            $report,
            now()->format('Ymd-His'),
            $format
        );

        ActivityLogger::log(
            actor: $user,
            module: 'HR Reports',
            action: 'export',
            description: 'Exported a manager HR report.',
            context: [
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'subject_label' => $payload['title'],
                'route_name' => 'panel.manager_hr.export',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'properties' => [
                    'report' => $report,
                    'format' => $format,
                    'row_count' => count($payload['rows']),
                ],
            ]
        );

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('panels.reports.manager-hr-export', [
                'report' => $payload,
                'exportMode' => 'pdf',
            ])->setPaper('a4', 'landscape');

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()
            ->view('panels.reports.manager-hr-export', [
                'report' => $payload,
                'exportMode' => 'xls',
            ], 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
    }

    /**
     * @return array{
     *     managerHrMetrics: array<int, array<string, string|int>>,
     *     managerHrPipeline: array<int, array<string, string|int>>,
     *     managerHrRecentAssignments: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     managerHrAttentionRows: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     managerHrCategoryDemand: \Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    private function buildManagerHrPanelData(): array
    {
        $dataset = $this->buildManagerHrDataset();
        $enrollmentRows = $dataset['enrollment_rows'];
        $progressSummary = $dataset['progress_summary'];
        $attentionRows = $enrollmentRows
            ->filter(fn (array $row): bool => filled($row['follow_up_reason']))
            ->sortByDesc(fn (array $row): int => ($row['follow_up_priority'] * 10000000000) + $row['assigned_at_timestamp'])
            ->take(8)
            ->map(fn (array $row): array => [
                'learner_name' => $row['learner_name'],
                'course_title' => $row['course_title'],
                'trainer_name' => $row['trainer_name'],
                'assigned_at' => $row['assigned_at_human'],
                'progress_percent' => $row['progress_percent'],
                'reason' => $row['follow_up_reason'],
                'tone' => $row['follow_up_tone'],
            ])
            ->values();

        $recentAssignments = $enrollmentRows
            ->take(8)
            ->map(fn (array $row): array => [
                'learner_name' => $row['learner_name'],
                'learner_email' => $row['learner_email'],
                'course_title' => $row['course_title'],
                'category_name' => $row['category_name'],
                'trainer_name' => $row['trainer_name'],
                'assigned_by' => $row['assigned_by'],
                'assigned_at' => $row['assigned_at_human'],
                'progress_percent' => $row['progress_percent'],
                'progress_label' => $row['progress_label'],
            ])
            ->values();

        $totalLearners = User::where('role', User::ROLE_STUDENT)->count();
        $activeLearners = User::where('role', User::ROLE_STUDENT)->where('is_active', true)->count();
        $totalEnrollments = $enrollmentRows->count();
        $trainerCoverage = $totalEnrollments > 0
            ? (int) round(($enrollmentRows->where('trainer_assigned', true)->count() / $totalEnrollments) * 100)
            : 0;
        $completedCount = $enrollmentRows->where('progress_state', 'Completed')->count();
        $inProgressCount = $enrollmentRows->where('progress_state', 'In Progress')->count();
        $notStartedCount = $enrollmentRows->where('progress_state', 'Not Started')->count();

        return [
            'managerHrMetrics' => [
                ['label' => 'Learner Accounts', 'value' => $totalLearners, 'hint' => 'Student records available for training operations.'],
                ['label' => 'Active Learners', 'value' => $activeLearners, 'hint' => 'Learners who can access the LMS right now.'],
                ['label' => 'Total Enrollments', 'value' => $totalEnrollments, 'hint' => 'Assigned training slots across all courses.'],
                ['label' => 'Trainer Coverage', 'value' => $trainerCoverage, 'hint' => 'Enrollments with a trainer assigned.', 'suffix' => '%'],
                ['label' => 'Average Progress', 'value' => $progressSummary['average_progress'], 'hint' => 'Mean completion across active enrollments.', 'suffix' => '%'],
                ['label' => 'Certificates Ready', 'value' => $progressSummary['completed_certificates'], 'hint' => 'Enrollments that have reached full completion.'],
            ],
            'managerHrPipeline' => [
                ['label' => 'Not Started', 'value' => $notStartedCount, 'hint' => 'Assigned learners with no recorded progress yet.'],
                ['label' => 'In Progress', 'value' => $inProgressCount, 'hint' => 'Learners actively moving through course content.'],
                ['label' => 'Completed', 'value' => $completedCount, 'hint' => 'Enrollments that have reached 100% completion.'],
                ['label' => 'Needs Follow-up', 'value' => $attentionRows->count(), 'hint' => 'Learners that may need HR coordination or trainer assignment.'],
            ],
            'managerHrRecentAssignments' => $recentAssignments,
            'managerHrAttentionRows' => $attentionRows,
            'managerHrCategoryDemand' => $dataset['category_demand'],
        ];
    }

    /**
     * @return array{
     *     itMetrics: array<int, array<string, string|int>>,
     *     itServiceStatuses: array<int, array<string, string>>,
     *     itSecurityEvents: \Illuminate\Support\Collection<int, array<string, string>>,
     *     itChangeEvents: \Illuminate\Support\Collection<int, array<string, string>>,
     *     itContentFootprint: array<int, array<string, string|int>>
     * }
     */
    private function buildItPanelData(): array
    {
        $activityLogsReady = Schema::hasTable('activity_logs');
        $notificationsReady = Schema::hasTable('notifications');
        $cloudinaryReady = app(CloudinaryPrivateMediaService::class)->isConfigured();
        $weekSessionBuilderReady = Schema::hasTable('course_weeks')
            && Schema::hasTable('course_sessions')
            && Schema::hasTable('course_session_items');
        $legacyDayStructurePresent = Schema::hasTable('course_days') || Schema::hasTable('course_day_items');
        $secureMediaCount = Schema::hasTable('course_session_items') && Schema::hasColumn('course_session_items', 'cloudinary_public_id')
            ? CourseSessionItem::query()->whereNotNull('cloudinary_public_id')->count()
            : 0;
        $externalLessonLinks = Schema::hasTable('course_session_items') && Schema::hasColumn('course_session_items', 'resource_url')
            ? CourseSessionItem::query()->whereNotNull('resource_url')->count()
            : 0;
        $studentSubmissionFiles = Schema::hasTable('course_item_submissions')
            ? DB::table('course_item_submissions')->whereNotNull('file_path')->count()
            : 0;
        $demoSubmissionFiles = Schema::hasTable('demo_task_submissions')
            ? DemoTaskSubmission::query()->whereNotNull('file_path')->count()
            : 0;
        $demoFeatureVideos = Schema::hasTable('demo_feature_videos') ? DemoFeatureVideo::count() : 0;
        $demoReviewVideos = Schema::hasTable('demo_review_videos') ? DemoReviewVideo::count() : 0;
        $inactiveAccounts = User::where('is_active', false)->count();

        $authEventsToday = 0;
        $blockedLogins7d = 0;
        $platformChanges7d = 0;
        $securityEvents = collect();
        $changeEvents = collect();

        if ($activityLogsReady) {
            $today = now()->toDateString();
            $sevenDaysAgo = now()->subDays(7);

            $authEventsToday = ActivityLog::query()
                ->where('module', 'Authentication')
                ->whereDate('created_at', $today)
                ->count();

            $blockedLogins7d = ActivityLog::query()
                ->where('module', 'Authentication')
                ->where('action', 'blocked_login')
                ->where('created_at', '>=', $sevenDaysAgo)
                ->count();

            $platformChanges7d = ActivityLog::query()
                ->where('module', '!=', 'Authentication')
                ->where('created_at', '>=', $sevenDaysAgo)
                ->count();

            $securityEvents = ActivityLog::query()
                ->with('user:id,name,email,role')
                ->where('module', 'Authentication')
                ->latest('id')
                ->take(8)
                ->get()
                ->map(fn (ActivityLog $log): array => [
                    'actor' => $log->actorName(),
                    'role' => $log->actorRoleLabel() ?? 'Role unavailable',
                    'action' => $log->actionLabel(),
                    'tone' => $log->actionTone(),
                    'description' => $log->description,
                    'ip_address' => $log->ip_address ?: 'IP unavailable',
                    'when' => optional($log->created_at)->diffForHumans() ?? 'Recently',
                ]);

            $changeEvents = ActivityLog::query()
                ->with('user:id,name,email,role')
                ->where('module', '!=', 'Authentication')
                ->latest('id')
                ->take(8)
                ->get()
                ->map(fn (ActivityLog $log): array => [
                    'module' => $log->module,
                    'actor' => $log->actorName(),
                    'action' => $log->actionLabel(),
                    'tone' => $log->actionTone(),
                    'subject' => $log->subject_label ?: 'General update',
                    'description' => $log->description,
                    'when' => optional($log->created_at)->diffForHumans() ?? 'Recently',
                ]);
        }

        return [
            'itMetrics' => [
                ['label' => 'Auth Events Today', 'value' => $authEventsToday, 'hint' => 'Login, logout, and blocked sign-in activity recorded today.'],
                ['label' => 'Blocked Logins (7d)', 'value' => $blockedLogins7d, 'hint' => 'Inactive or throttled login attempts during the last week.'],
                ['label' => 'Platform Changes (7d)', 'value' => $platformChanges7d, 'hint' => 'Recorded create, update, delete, and send actions.'],
                ['label' => 'Secure Media Items', 'value' => $secureMediaCount, 'hint' => 'Course items backed by protected Cloudinary media.'],
                ['label' => 'External Lesson Links', 'value' => $externalLessonLinks, 'hint' => 'Course items that depend on external resource URLs.'],
                ['label' => 'Inactive Accounts', 'value' => $inactiveAccounts, 'hint' => 'Accounts currently blocked by active-status controls.'],
            ],
            'itServiceStatuses' => [
                [
                    'label' => 'Activity Logs',
                    'state' => $activityLogsReady ? 'Enabled' : 'Missing',
                    'tone' => $activityLogsReady ? 'ok' : 'warning',
                    'detail' => $activityLogsReady
                        ? 'Operational audit records are available for security and change review.'
                        : 'The activity_logs table is missing, so operational auditing is unavailable.',
                ],
                [
                    'label' => 'Notifications',
                    'state' => $notificationsReady ? 'Enabled' : 'Missing',
                    'tone' => $notificationsReady ? 'ok' : 'warning',
                    'detail' => $notificationsReady
                        ? 'Database notifications can be delivered across supported workflows.'
                        : 'The notifications table is missing, so in-app delivery is limited.',
                ],
                [
                    'label' => 'Cloudinary Media',
                    'state' => $cloudinaryReady ? 'Configured' : 'Not configured',
                    'tone' => $cloudinaryReady ? 'ok' : 'warning',
                    'detail' => $cloudinaryReady
                        ? 'Protected course files can be uploaded and streamed securely.'
                        : 'Cloudinary credentials are missing, so protected uploads will fail.',
                ],
                [
                    'label' => 'Week / Session Builder',
                    'state' => $weekSessionBuilderReady ? 'Ready' : 'Incomplete',
                    'tone' => $weekSessionBuilderReady ? 'ok' : 'warning',
                    'detail' => $weekSessionBuilderReady
                        ? 'Week, session, and item tables exist for the current course builder flow.'
                        : 'One or more course-builder tables are missing.',
                ],
                [
                    'label' => 'Legacy Day Structure',
                    'state' => $legacyDayStructurePresent ? 'Present' : 'Not detected',
                    'tone' => $legacyDayStructurePresent ? 'warning' : 'ok',
                    'detail' => $legacyDayStructurePresent
                        ? 'Legacy day-based tables still exist beside the newer week/session flow.'
                        : 'No legacy day-based course tables were detected.',
                ],
                [
                    'label' => 'Secure Content Coverage',
                    'state' => $secureMediaCount > 0 ? number_format($secureMediaCount).' item(s)' : 'No secure assets',
                    'tone' => $secureMediaCount > 0 ? 'ok' : 'muted',
                    'detail' => $secureMediaCount > 0
                        ? 'Protected course assets are already available in the catalog.'
                        : 'No course items are using protected media yet.',
                ],
            ],
            'itSecurityEvents' => $securityEvents,
            'itChangeEvents' => $changeEvents,
            'itContentFootprint' => [
                ['label' => 'Student Submission Files', 'value' => $studentSubmissionFiles, 'hint' => 'Uploads attached to learner task submissions.'],
                ['label' => 'Demo Submission Files', 'value' => $demoSubmissionFiles, 'hint' => 'Uploads attached to demo-task submissions.'],
                ['label' => 'Demo Feature Videos', 'value' => $demoFeatureVideos, 'hint' => 'Feature videos currently managed in the demo module.'],
                ['label' => 'Review Videos', 'value' => $demoReviewVideos, 'hint' => 'Review video assets currently stored in the platform.'],
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function managerHrReportExports(): array
    {
        return collect($this->managerHrReportDefinitions())
            ->map(function (array $definition, string $report): array {
                return [
                    'key' => $report,
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'excel_route' => route('panel.manager_hr.export', ['report' => $report, 'format' => 'xls']),
                    'pdf_route' => route('panel.manager_hr.export', ['report' => $report, 'format' => 'pdf']),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, array{label:string,description:string}>
     */
    private function managerHrReportDefinitions(): array
    {
        return [
            'learner-progress' => [
                'label' => 'Learner Progress',
                'description' => 'All enrollments with current learner, trainer, and progress signals.',
            ],
            'inactive-learners' => [
                'label' => 'Inactive Learners',
                'description' => 'Inactive learner accounts with assignment and completion context.',
            ],
            'completion' => [
                'label' => 'Completion Snapshot',
                'description' => 'Completion-state report covering not started, in progress, and completed enrollments.',
            ],
            'certificate-ready' => [
                'label' => 'Certificate Ready',
                'description' => 'Learners who have reached full completion and are ready for certificates.',
            ],
        ];
    }

    /**
     * @return array{
     *     title:string,
     *     subtitle:string,
     *     generated_at:string,
     *     summary: array<int, array{label:string,value:string|int}>,
     *     columns: array<int, array{key:string,label:string}>,
     *     rows: array<int, array<string, string|int>>
     * }
     */
    private function buildManagerHrReportPayload(string $report): array
    {
        $dataset = $this->buildManagerHrDataset();
        $rows = $dataset['enrollment_rows'];
        $inactiveLearners = $dataset['inactive_learners'];
        $progressSummary = $dataset['progress_summary'];

        return match ($report) {
            'inactive-learners' => [
                'title' => 'Inactive Learners Report',
                'subtitle' => 'Learner accounts currently blocked from the LMS, with training context for follow-up.',
                'generated_at' => now()->format('d M Y h:i A'),
                'summary' => [
                    ['label' => 'Inactive Learners', 'value' => $inactiveLearners->count()],
                    ['label' => 'With Enrollments', 'value' => $inactiveLearners->where('enrollments_count', '>', 0)->count()],
                    ['label' => 'Certificates Ready', 'value' => $inactiveLearners->sum('certificate_ready_count')],
                ],
                'columns' => [
                    ['key' => 'learner_name', 'label' => 'Learner'],
                    ['key' => 'learner_email', 'label' => 'Email'],
                    ['key' => 'enrollments_count', 'label' => 'Enrollments'],
                    ['key' => 'completed_count', 'label' => 'Completed'],
                    ['key' => 'certificate_ready_count', 'label' => 'Certificate Ready'],
                    ['key' => 'latest_course_title', 'label' => 'Latest Course'],
                    ['key' => 'latest_assigned_at', 'label' => 'Latest Assignment'],
                ],
                'rows' => $inactiveLearners->map(fn (array $row): array => [
                    'learner_name' => $row['learner_name'],
                    'learner_email' => $row['learner_email'],
                    'enrollments_count' => $row['enrollments_count'],
                    'completed_count' => $row['completed_count'],
                    'certificate_ready_count' => $row['certificate_ready_count'],
                    'latest_course_title' => $row['latest_course_title'],
                    'latest_assigned_at' => $row['latest_assigned_at'],
                ])->values()->all(),
            ],
            'completion' => [
                'title' => 'Completion Snapshot Report',
                'subtitle' => 'Completion-state view across all enrollments for HR monitoring and follow-up.',
                'generated_at' => now()->format('d M Y h:i A'),
                'summary' => [
                    ['label' => 'Not Started', 'value' => $rows->where('progress_state', 'Not Started')->count()],
                    ['label' => 'In Progress', 'value' => $rows->where('progress_state', 'In Progress')->count()],
                    ['label' => 'Completed', 'value' => $rows->where('progress_state', 'Completed')->count()],
                ],
                'columns' => [
                    ['key' => 'learner_name', 'label' => 'Learner'],
                    ['key' => 'course_title', 'label' => 'Course'],
                    ['key' => 'category_name', 'label' => 'Category'],
                    ['key' => 'trainer_name', 'label' => 'Trainer'],
                    ['key' => 'assigned_at', 'label' => 'Assigned At'],
                    ['key' => 'progress_percent', 'label' => 'Progress %'],
                    ['key' => 'progress_state', 'label' => 'Completion Status'],
                    ['key' => 'certificate_status', 'label' => 'Certificate'],
                ],
                'rows' => $rows->map(fn (array $row): array => [
                    'learner_name' => $row['learner_name'],
                    'course_title' => $row['course_title'],
                    'category_name' => $row['category_name'],
                    'trainer_name' => $row['trainer_name'],
                    'assigned_at' => $row['assigned_at'],
                    'progress_percent' => $row['progress_percent'],
                    'progress_state' => $row['progress_state'],
                    'certificate_status' => $row['certificate_ready'] ? 'Ready' : 'Not Ready',
                ])->values()->all(),
            ],
            'certificate-ready' => [
                'title' => 'Certificate Ready Learners Report',
                'subtitle' => 'Learners who have reached full completion and are eligible for certificate generation.',
                'generated_at' => now()->format('d M Y h:i A'),
                'summary' => [
                    ['label' => 'Certificate Ready', 'value' => $rows->where('certificate_ready', true)->count()],
                    ['label' => 'Average Progress', 'value' => $progressSummary['average_progress'].'%'],
                    ['label' => 'Total Completed Certificates', 'value' => $progressSummary['completed_certificates']],
                ],
                'columns' => [
                    ['key' => 'learner_name', 'label' => 'Learner'],
                    ['key' => 'learner_email', 'label' => 'Email'],
                    ['key' => 'course_title', 'label' => 'Course'],
                    ['key' => 'category_name', 'label' => 'Category'],
                    ['key' => 'trainer_name', 'label' => 'Trainer'],
                    ['key' => 'assigned_at', 'label' => 'Assigned At'],
                    ['key' => 'progress_percent', 'label' => 'Progress %'],
                    ['key' => 'certificate_status', 'label' => 'Certificate'],
                ],
                'rows' => $rows
                    ->where('certificate_ready', true)
                    ->map(fn (array $row): array => [
                        'learner_name' => $row['learner_name'],
                        'learner_email' => $row['learner_email'],
                        'course_title' => $row['course_title'],
                        'category_name' => $row['category_name'],
                        'trainer_name' => $row['trainer_name'],
                        'assigned_at' => $row['assigned_at'],
                        'progress_percent' => $row['progress_percent'],
                        'certificate_status' => 'Ready',
                    ])
                    ->values()
                    ->all(),
            ],
            default => [
                'title' => 'Learner Progress Report',
                'subtitle' => 'Current learner progress across all enrollments, trainers, and course categories.',
                'generated_at' => now()->format('d M Y h:i A'),
                'summary' => [
                    ['label' => 'Enrollments', 'value' => $rows->count()],
                    ['label' => 'Average Progress', 'value' => $progressSummary['average_progress'].'%'],
                    ['label' => 'Needs Follow-up', 'value' => $rows->filter(fn (array $row): bool => filled($row['follow_up_reason']))->count()],
                ],
                'columns' => [
                    ['key' => 'learner_name', 'label' => 'Learner'],
                    ['key' => 'learner_email', 'label' => 'Email'],
                    ['key' => 'account_status', 'label' => 'Account'],
                    ['key' => 'course_title', 'label' => 'Course'],
                    ['key' => 'category_name', 'label' => 'Category'],
                    ['key' => 'trainer_name', 'label' => 'Trainer'],
                    ['key' => 'progress_percent', 'label' => 'Progress %'],
                    ['key' => 'progress_label', 'label' => 'Items'],
                    ['key' => 'progress_state', 'label' => 'Status'],
                    ['key' => 'assigned_at', 'label' => 'Assigned At'],
                ],
                'rows' => $rows->map(fn (array $row): array => [
                    'learner_name' => $row['learner_name'],
                    'learner_email' => $row['learner_email'],
                    'account_status' => $row['account_status'],
                    'course_title' => $row['course_title'],
                    'category_name' => $row['category_name'],
                    'trainer_name' => $row['trainer_name'],
                    'progress_percent' => $row['progress_percent'],
                    'progress_label' => $row['progress_label'],
                    'progress_state' => $row['progress_state'],
                    'assigned_at' => $row['assigned_at'],
                ])->values()->all(),
            ],
        };
    }

    /**
     * @return array{
     *     enrollment_rows:\Illuminate\Support\Collection<int, array<string, mixed>>,
     *     inactive_learners:\Illuminate\Support\Collection<int, array<string, mixed>>,
     *     category_demand:\Illuminate\Support\Collection<int, array<string, mixed>>,
     *     progress_summary:array{
     *         rows: \Illuminate\Support\Collection<int, array{enrollment_id:int,total_items:int,completed_items:int,progress_percent:int}>,
     *         average_progress:int,
     *         completed_certificates:int
     *     }
     * }
     */
    private function buildManagerHrDataset(): array
    {
        $enrollments = CourseEnrollment::query()
            ->with([
                'course.category:id,name',
                'student:id,name,email,is_active',
                'trainer:id,name',
                'assignedBy:id,name',
            ])
            ->latest('id')
            ->get();

        $progressSummary = $this->summarizeEnrollmentProgress($enrollments);
        $progressRows = $progressSummary['rows'];
        $now = now();

        $enrollmentRows = $enrollments
            ->map(function (CourseEnrollment $enrollment) use ($progressRows, $now): array {
                $progress = $progressRows->get($enrollment->id, $this->emptyProgressRow($enrollment->id));
                $progressState = $progress['progress_percent'] >= 100
                    ? 'Completed'
                    : ($progress['progress_percent'] > 0 ? 'In Progress' : 'Not Started');

                $priority = 0;
                $tone = 'muted';
                $reason = null;

                if (! $enrollment->student?->is_active) {
                    $priority = 4;
                    $tone = 'danger';
                    $reason = 'Learner account is inactive and may be blocked from training access.';
                } elseif (! $enrollment->trainer_id) {
                    $priority = 3;
                    $tone = 'warning';
                    $reason = 'Trainer has not been assigned to this enrollment yet.';
                } elseif ($progress['progress_percent'] === 0 && optional($enrollment->created_at)->lte($now->copy()->subDays(7))) {
                    $priority = 2;
                    $tone = 'warning';
                    $reason = 'No learning progress has been recorded after assignment.';
                } elseif ($progress['progress_percent'] < 25) {
                    $priority = 1;
                    $tone = 'muted';
                    $reason = 'Completion progress is still below 25%.';
                }

                return [
                    'enrollment_id' => (int) $enrollment->id,
                    'learner_id' => (int) $enrollment->student_id,
                    'learner_name' => $enrollment->student?->name ?? 'Unknown learner',
                    'learner_email' => $enrollment->student?->email ?? 'No email',
                    'account_status' => $enrollment->student?->is_active ? 'Active' : 'Inactive',
                    'course_title' => $enrollment->course?->title ?? 'Untitled course',
                    'category_name' => $enrollment->course?->category?->name ?? 'General',
                    'trainer_name' => $enrollment->trainer?->name ?? 'Not assigned',
                    'assigned_by' => $enrollment->assignedBy?->name ?? 'System',
                    'assigned_at' => optional($enrollment->created_at)->format('Y-m-d H:i') ?? '',
                    'assigned_at_human' => optional($enrollment->created_at)->diffForHumans() ?? 'Recently',
                    'assigned_at_timestamp' => optional($enrollment->created_at)->timestamp ?? 0,
                    'progress_percent' => $progress['progress_percent'],
                    'completed_items' => $progress['completed_items'],
                    'total_items' => $progress['total_items'],
                    'progress_label' => $progress['total_items'] > 0
                        ? $progress['completed_items'].' / '.$progress['total_items'].' items'
                        : 'No lesson items yet',
                    'progress_state' => $progressState,
                    'follow_up_reason' => $reason,
                    'follow_up_tone' => $tone,
                    'follow_up_priority' => $priority,
                    'certificate_ready' => $progress['total_items'] > 0 && $progress['progress_percent'] >= 100,
                    'trainer_assigned' => $enrollment->trainer_id !== null,
                ];
            })
            ->values();

        $inactiveLearners = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->where('is_active', false)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function (User $learner) use ($enrollmentRows): array {
                $learnerEnrollments = $enrollmentRows
                    ->where('learner_id', $learner->id)
                    ->sortByDesc('assigned_at_timestamp')
                    ->values();
                $latestAssignment = $learnerEnrollments->first();

                return [
                    'learner_name' => $learner->name,
                    'learner_email' => $learner->email,
                    'enrollments_count' => $learnerEnrollments->count(),
                    'completed_count' => $learnerEnrollments->where('progress_state', 'Completed')->count(),
                    'certificate_ready_count' => $learnerEnrollments->where('certificate_ready', true)->count(),
                    'latest_course_title' => $latestAssignment['course_title'] ?? 'No course assigned',
                    'latest_assigned_at' => $latestAssignment['assigned_at'] ?? 'No assignment yet',
                ];
            })
            ->values();

        $categoryDemand = collect();
        if (Schema::hasTable('course_categories') && Schema::hasTable('courses') && Schema::hasTable('course_enrollments')) {
            $categoryDemand = DB::table('course_categories')
                ->leftJoin('courses', 'courses.category_id', '=', 'course_categories.id')
                ->leftJoin('course_enrollments', 'course_enrollments.course_id', '=', 'courses.id')
                ->whereNull('course_categories.parent_id')
                ->groupBy('course_categories.id', 'course_categories.name')
                ->select(
                    'course_categories.name',
                    DB::raw('COUNT(course_enrollments.id) as enrollments_count'),
                    DB::raw('COUNT(DISTINCT courses.id) as course_count')
                )
                ->orderByDesc('enrollments_count')
                ->orderBy('course_categories.name')
                ->limit(6)
                ->get()
                ->map(fn ($row): array => [
                    'name' => (string) $row->name,
                    'enrollments_count' => (int) $row->enrollments_count,
                    'course_count' => (int) $row->course_count,
                ]);
        }

        return [
            'enrollment_rows' => $enrollmentRows,
            'inactive_learners' => $inactiveLearners,
            'category_demand' => $categoryDemand,
            'progress_summary' => $progressSummary,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\CourseEnrollment>  $enrollments
     * @return array{
     *     rows: \Illuminate\Support\Collection<int, array{enrollment_id:int,total_items:int,completed_items:int,progress_percent:int}>,
     *     average_progress:int,
     *     completed_certificates:int
     * }
     */
    private function summarizeEnrollmentProgress(Collection $enrollments): array
    {
        if ($enrollments->isEmpty()) {
            return [
                'rows' => collect(),
                'average_progress' => 0,
                'completed_certificates' => 0,
            ];
        }

        $courseIds = $enrollments->pluck('course_id')->filter()->unique()->values();
        $enrollmentIds = $enrollments->pluck('id')->values();

        $totalItemsByCourse = $this->courseItemTotalsByCourse($courseIds);
        $completedByEnrollment = collect();

        if (
            Schema::hasTable('course_progress')
            && Schema::hasColumn('course_progress', 'course_session_item_id')
            && $enrollmentIds->isNotEmpty()
        ) {
            $completedByEnrollment = CourseProgress::query()
                ->whereIn('course_enrollment_id', $enrollmentIds)
                ->whereNotNull('completed_at')
                ->groupBy('course_enrollment_id')
                ->select('course_enrollment_id', DB::raw('count(*) as completed_items'))
                ->pluck('completed_items', 'course_enrollment_id');
        }

        $rows = $enrollments
            ->map(function (CourseEnrollment $enrollment) use ($totalItemsByCourse, $completedByEnrollment): array {
                $totalItems = (int) ($totalItemsByCourse[$enrollment->course_id] ?? 0);
                $completedItems = min($totalItems, (int) ($completedByEnrollment[$enrollment->id] ?? 0));
                $progressPercent = $totalItems > 0
                    ? (int) round(($completedItems / $totalItems) * 100)
                    : 0;

                return [
                    'enrollment_id' => (int) $enrollment->id,
                    'total_items' => $totalItems,
                    'completed_items' => $completedItems,
                    'progress_percent' => $progressPercent,
                ];
            })
            ->keyBy('enrollment_id');

        return [
            'rows' => $rows,
            'average_progress' => (int) round((float) $rows->avg('progress_percent')),
            'completed_certificates' => $rows
                ->filter(fn (array $row): bool => $row['total_items'] > 0 && $row['progress_percent'] >= 100)
                ->count(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int|string>  $courseIds
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function courseItemTotalsByCourse(Collection $courseIds): Collection
    {
        if (
            $courseIds->isEmpty()
            || ! Schema::hasTable('course_session_items')
            || ! Schema::hasTable('course_sessions')
            || ! Schema::hasTable('course_weeks')
        ) {
            return collect();
        }

        return DB::table('course_session_items')
            ->join('course_sessions', 'course_sessions.id', '=', 'course_session_items.course_session_id')
            ->join('course_weeks', 'course_weeks.id', '=', 'course_sessions.course_week_id')
            ->whereIn('course_weeks.course_id', $courseIds->all())
            ->groupBy('course_weeks.course_id')
            ->select('course_weeks.course_id', DB::raw('count(course_session_items.id) as total_items'))
            ->pluck('total_items', 'course_weeks.course_id');
    }

    /**
     * @return array{enrollment_id:int,total_items:int,completed_items:int,progress_percent:int}
     */
    private function emptyProgressRow(int $enrollmentId): array
    {
        return [
            'enrollment_id' => $enrollmentId,
            'total_items' => 0,
            'completed_items' => 0,
            'progress_percent' => 0,
        ];
    }
}
