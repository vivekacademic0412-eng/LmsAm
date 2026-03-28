<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseItemSubmission;
use App\Models\CourseSessionItem;
use App\Models\DemoFeatureVideo;
use App\Models\DemoReviewVideo;
use App\Models\DemoTask;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\User;
use App\Services\ActivityLogger;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * @var list<string>
     */
    private const TRACKED_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * @var list<string>
     */
    private const EXCLUDED_ROUTES = [
        'logout',
        'notifications.read',
        'notifications.read-all',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->storeActivity($request, $response);

        return $response;
    }

    private function storeActivity(Request $request, Response $response): void
    {
        if (! $request->user()) {
            return;
        }

        if (! in_array($request->method(), self::TRACKED_METHODS, true)) {
            return;
        }

        if ($response->getStatusCode() >= 400) {
            return;
        }

        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, self::EXCLUDED_ROUTES, true)) {
            return;
        }

        [$subjectType, $subjectId, $subjectLabel] = $this->resolveSubjectDetails($request);

        ActivityLogger::log(
            actor: $request->user(),
            module: $this->resolveModule($request),
            action: $this->resolveAction($request),
            description: $this->resolveDescription($request),
            context: [
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'subject_label' => $subjectLabel,
                'route_name' => $routeName,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
                'properties' => $this->sanitizePayload($request),
            ]
        );
    }

    private function resolveModule(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        $routeMap = [
            'users.' => 'Users',
            'enrollments.' => 'Enrollments',
            'submissions.' => 'Submissions',
            'course-session-items.submit' => 'Submissions',
            'course-item-submissions.review' => 'Submissions',
            'trainer.items.quiz-live' => 'Live Quizzes',
            'courses.weeks.' => 'Course Builder',
            'course-weeks.' => 'Course Builder',
            'course-sessions.' => 'Course Builder',
            'course-session-items.' => 'Course Builder',
            'courses.' => 'Courses',
            'course-categories.' => 'Course Categories',
            'broadcast-notifications.' => 'Broadcast Notifications',
            'demo-tasks.' => 'Demo Tasks',
            'demo-assignments.' => 'Demo Tasks',
            'demo-feature-video.' => 'Demo Feature Videos',
            'demo-review-videos.' => 'Review Videos',
            'profile.' => 'Profiles',
        ];

        foreach ($routeMap as $prefix => $label) {
            if ($routeName === $prefix || Str::startsWith($routeName, $prefix)) {
                return $label;
            }
        }

        $firstSegment = Str::of($request->path())->before('/')->headline()->value();

        return $firstSegment !== '' ? $firstSegment : 'General';
    }

    private function resolveAction(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        $routeMap = [
            'users.store' => 'create',
            'users.update' => 'update',
            'users.destroy' => 'delete',
            'users.resend-email' => 'resend_email',
            'enrollments.store' => 'assign',
            'enrollments.update' => 'update',
            'enrollments.destroy' => 'delete',
            'enrollments.resend-email' => 'resend_email',
            'course-session-items.submit' => 'submit',
            'course-item-submissions.review' => 'review',
            'broadcast-notifications.store' => 'send',
            'courses.store' => 'create',
            'courses.update' => 'update',
            'courses.destroy' => 'delete',
            'course-categories.store' => 'create',
            'course-categories.update' => 'update',
            'course-categories.destroy' => 'delete',
            'courses.weeks.store' => 'create',
            'course-weeks.update' => 'update',
            'course-weeks.destroy' => 'delete',
            'course-weeks.sessions.store' => 'create',
            'course-sessions.update' => 'update',
            'course-sessions.destroy' => 'delete',
            'course-session-items.update' => 'update',
            'demo-tasks.store' => 'create',
            'demo-tasks.update' => 'update',
            'demo-tasks.destroy' => 'delete',
            'demo-tasks.assign' => 'assign',
            'demo-tasks.assignments.update' => 'update',
            'demo-tasks.assignments.destroy' => 'delete',
            'demo-assignments.submit' => 'submit',
            'demo-feature-video.store' => 'create',
            'demo-feature-video.update' => 'update',
            'demo-feature-video.destroy' => 'delete',
            'demo-review-videos.store' => 'create',
            'demo-review-videos.update' => 'update',
            'demo-review-videos.destroy' => 'delete',
            'trainer.items.quiz-live' => 'toggle_live',
            'profile.update' => 'update',
        ];

        if (isset($routeMap[$routeName])) {
            return $routeMap[$routeName];
        }

        return match ($request->method()) {
            'POST' => 'create',
            'DELETE' => 'delete',
            default => 'update',
        };
    }

    private function resolveDescription(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        $routeMap = [
            'users.store' => 'Created a user account.',
            'users.update' => 'Updated a user account.',
            'users.destroy' => 'Deleted a user account.',
            'users.resend-email' => 'Resent a user welcome email.',
            'enrollments.store' => 'Assigned a course enrollment.',
            'enrollments.update' => 'Updated a course enrollment.',
            'enrollments.destroy' => 'Removed a course enrollment.',
            'enrollments.resend-email' => 'Resent a course assignment email.',
            'course-session-items.submit' => 'Submitted course work.',
            'course-item-submissions.review' => 'Reviewed a student submission.',
            'broadcast-notifications.store' => 'Sent a broadcast notification.',
            'courses.store' => 'Created a course.',
            'courses.update' => 'Updated a course.',
            'courses.destroy' => 'Deleted a course.',
            'course-categories.store' => 'Created a course category.',
            'course-categories.update' => 'Updated a course category.',
            'course-categories.destroy' => 'Deleted a course category.',
            'courses.weeks.store' => 'Added a course week.',
            'course-weeks.update' => 'Updated a course week.',
            'course-weeks.destroy' => 'Deleted a course week.',
            'course-weeks.sessions.store' => 'Added a course session.',
            'course-sessions.update' => 'Updated a course session.',
            'course-sessions.destroy' => 'Deleted a course session.',
            'course-session-items.update' => 'Updated a course session item.',
            'demo-tasks.store' => 'Created a demo task.',
            'demo-tasks.update' => 'Updated a demo task.',
            'demo-tasks.destroy' => 'Deleted a demo task.',
            'demo-tasks.assign' => 'Assigned a demo task.',
            'demo-tasks.assignments.update' => 'Updated a demo task assignment.',
            'demo-tasks.assignments.destroy' => 'Deleted a demo task assignment.',
            'demo-assignments.submit' => 'Submitted a demo task response.',
            'demo-feature-video.store' => 'Created a demo feature video.',
            'demo-feature-video.update' => 'Updated a demo feature video.',
            'demo-feature-video.destroy' => 'Deleted a demo feature video.',
            'demo-review-videos.store' => 'Created a review video.',
            'demo-review-videos.update' => 'Updated a review video.',
            'demo-review-videos.destroy' => 'Deleted a review video.',
            'trainer.items.quiz-live' => 'Changed a quiz live status.',
            'profile.update' => 'Updated a profile.',
        ];

        if (isset($routeMap[$routeName])) {
            return $routeMap[$routeName];
        }

        return Str::headline($this->resolveAction($request)).' '.$this->resolveModule($request).'.';
    }

    /**
     * @return array{0:?string,1:?string,2:?string}
     */
    private function resolveSubjectDetails(Request $request): array
    {
        foreach ($request->route()?->parameters() ?? [] as $parameter) {
            if ($parameter instanceof Model) {
                return [
                    $parameter::class,
                    (string) $parameter->getKey(),
                    $this->describeModel($parameter),
                ];
            }
        }

        return [null, null, $this->resolveSubjectLabelFromPayload($request)];
    }

    private function resolveSubjectLabelFromPayload(Request $request): ?string
    {
        $routeName = (string) $request->route()?->getName();

        return match ($routeName) {
            'users.store' => $this->joinSubjectParts([
                $request->string('name')->trim()->value(),
                $request->string('email')->trim()->value(),
            ]),
            'courses.store' => $request->string('title')->trim()->value() ?: null,
            'course-categories.store' => $request->string('name')->trim()->value() ?: null,
            'broadcast-notifications.store' => $request->string('title')->trim()->value() ?: null,
            'profile.update' => $request->string('name')->trim()->value() ?: null,
            'enrollments.store' => $this->describeEnrollmentPayload($request),
            'demo-tasks.store' => $request->string('title')->trim()->value() ?: null,
            'demo-tasks.assign' => $this->describeDemoAssignmentPayload($request),
            default => $this->joinSubjectParts([
                $request->string('title')->trim()->value(),
                $request->string('name')->trim()->value(),
                $request->string('email')->trim()->value(),
            ]),
        };
    }

    private function describeEnrollmentPayload(Request $request): ?string
    {
        $courseTitle = Course::query()->whereKey($request->input('course_id'))->value('title');
        $studentName = User::query()->whereKey($request->input('student_id'))->value('name');

        return $this->joinSubjectParts([$studentName, $courseTitle], ' -> ');
    }

    private function describeDemoAssignmentPayload(Request $request): ?string
    {
        $taskTitle = DemoTask::query()->whereKey($request->input('demo_task_id'))->value('title');
        $userName = User::query()->whereKey($request->input('user_id'))->value('name');

        return $this->joinSubjectParts([$userName, $taskTitle], ' -> ');
    }

    private function joinSubjectParts(array $parts, string $separator = ' | '): ?string
    {
        $parts = array_values(array_filter($parts, fn ($value) => filled($value)));

        return $parts !== [] ? implode($separator, $parts) : null;
    }

    private function describeModel(Model $model): string
    {
        return match (true) {
            $model instanceof User => $this->joinSubjectParts([$model->name, $model->email]) ?? 'User #'.$model->getKey(),
            $model instanceof Course => $model->title,
            $model instanceof CourseCategory => $model->name,
            $model instanceof CourseSessionItem => $model->title,
            $model instanceof CourseEnrollment => $this->describeEnrollment($model),
            $model instanceof CourseItemSubmission => $this->describeSubmission($model),
            $model instanceof DemoTask => $model->title,
            $model instanceof DemoTaskAssignment => $this->describeDemoAssignment($model),
            $model instanceof DemoTaskSubmission => $this->describeDemoSubmission($model),
            $model instanceof DemoFeatureVideo => $model->title ?? 'Feature video #'.$model->getKey(),
            $model instanceof DemoReviewVideo => $model->title ?? 'Review video #'.$model->getKey(),
            default => class_basename($model).' #'.$model->getKey(),
        };
    }

    private function describeEnrollment(CourseEnrollment $enrollment): string
    {
        $enrollment->loadMissing(['course:id,title', 'student:id,name']);

        return $this->joinSubjectParts([
            $enrollment->student?->name,
            $enrollment->course?->title,
        ], ' -> ') ?? 'Enrollment #'.$enrollment->getKey();
    }

    private function describeSubmission(CourseItemSubmission $submission): string
    {
        $submission->loadMissing(['item:id,title', 'submitter:id,name']);

        return $this->joinSubjectParts([
            $submission->submitter?->name,
            $submission->item?->title,
        ], ' -> ') ?? 'Submission #'.$submission->getKey();
    }

    private function describeDemoAssignment(DemoTaskAssignment $assignment): string
    {
        $assignment->loadMissing(['user:id,name', 'demoTask:id,title']);

        return $this->joinSubjectParts([
            $assignment->user?->name,
            $assignment->demoTask?->title,
        ], ' -> ') ?? 'Demo assignment #'.$assignment->getKey();
    }

    private function describeDemoSubmission(DemoTaskSubmission $submission): string
    {
        $submission->loadMissing(['assignment.user:id,name', 'assignment.demoTask:id,title']);

        return $this->joinSubjectParts([
            $submission->assignment?->user?->name,
            $submission->assignment?->demoTask?->title,
        ], ' -> ') ?? 'Demo submission #'.$submission->getKey();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function sanitizePayload(Request $request): ?array
    {
        $payload = $request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'current_password',
        ]);

        if ($request->allFiles() !== []) {
            $payload = array_replace_recursive($payload, $request->allFiles());
        }

        $normalized = $this->normalizeValue($payload);

        return is_array($normalized) && $normalized !== [] ? $normalized : null;
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof UploadedFile) {
            return [
                'file_name' => $value->getClientOriginalName(),
                'mime' => $value->getClientMimeType(),
                'size' => $value->getSize(),
            ];
        }

        if (is_array($value)) {
            $normalized = [];

            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeValue($item);
            }

            return $normalized;
        }

        if (is_string($value)) {
            return Str::limit(trim($value), 500, '...');
        }

        return $value;
    }
}
