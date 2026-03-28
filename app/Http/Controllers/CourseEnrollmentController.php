<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Course;
use App\Models\CourseItemSubmission;
use App\Models\CourseSessionItem;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\User;
use App\Services\StudentCertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CourseEnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        $categoryId = $request->query('category_id');
        $subcategoryId = $request->query('subcategory_id');
        $trainerId = $request->query('trainer_id');
        $courseId = $request->query('course_id');

        $enrollmentsQuery = CourseEnrollment::with(['course.category', 'course.subcategory', 'student', 'trainer', 'assignedBy'])
            ->when($categoryId, fn ($q) => $q->whereHas('course', fn ($cq) => $cq->where('category_id', $categoryId)))
            ->when($subcategoryId, fn ($q) => $q->whereHas('course', fn ($cq) => $cq->where('subcategory_id', $subcategoryId)))
            ->when($trainerId, fn ($q) => $q->where('trainer_id', $trainerId))
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->latest();

        return view('enrollments.index', [
            'enrollments' => $enrollmentsQuery->paginate(8)->withQueryString(),
            'courses' => Course::orderBy('title')->get(),
            'students' => User::where('role', User::ROLE_STUDENT)->orderBy('name')->get(),
            'trainers' => User::where('role', User::ROLE_TRAINER)->orderBy('name')->get(),
            'categories' => \App\Models\CourseCategory::with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
            'activeCategoryId' => $categoryId,
            'activeSubcategoryId' => $subcategoryId,
            'activeTrainerId' => $trainerId,
            'activeCourseId' => $courseId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManager($request);

        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT)],
            'trainer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_TRAINER)],
        ]);

        $enrollment = CourseEnrollment::firstOrNew([
            'course_id' => $data['course_id'],
            'student_id' => $data['student_id'],
        ]);

        $enrollment->trainer_id = $data['trainer_id'] ?? null;
        $enrollment->assigned_by = $request->user()->id;
        $enrollment->save();

        $assignmentEmailSent = $this->sendCourseAssignmentEmail(
            $enrollment->fresh(['course.category', 'student', 'trainer']),
            $request->user(),
            false
        );
        $assignmentNotificationSent = $this->sendCourseAssignmentNotification(
            $enrollment->fresh(['course.category', 'student', 'trainer']),
            $request->user(),
            false
        );

        $response = back()->with(
            'success',
            $this->buildAssignmentSuccessMessage('Enrollment assigned.', $assignmentEmailSent, $assignmentNotificationSent)
        );

        if (! $assignmentEmailSent) {
            return $response->withErrors([
                'mail' => 'Course assignment email could not be sent. Check mail configuration to deliver the course details.',
            ]);
        }

        return $response;
    }

    public function update(Request $request, CourseEnrollment $enrollment): RedirectResponse
    {
        $this->authorizeManager($request);

        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT)],
            'trainer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_TRAINER)],
        ]);

        $request->validate([
            'course_id' => [
                Rule::unique('course_enrollments', 'course_id')
                    ->where(fn ($query) => $query->where('student_id', $data['student_id']))
                    ->ignore($enrollment->id),
            ],
        ]);

        $enrollment->update([
            'course_id' => $data['course_id'],
            'student_id' => $data['student_id'],
            'trainer_id' => $data['trainer_id'] ?? null,
            'assigned_by' => $request->user()->id,
        ]);

        $assignmentEmailSent = $this->sendCourseAssignmentEmail(
            $enrollment->fresh(['course.category', 'student', 'trainer']),
            $request->user(),
            true
        );
        $assignmentNotificationSent = $this->sendCourseAssignmentNotification(
            $enrollment->fresh(['course.category', 'student', 'trainer']),
            $request->user(),
            true
        );

        $response = back()->with(
            'success',
            $this->buildAssignmentSuccessMessage('Enrollment updated.', $assignmentEmailSent, $assignmentNotificationSent)
        );

        if (! $assignmentEmailSent) {
            return $response->withErrors([
                'mail' => 'Course assignment email could not be sent. Check mail configuration to deliver the course details.',
            ]);
        }

        return $response;
    }

    public function destroy(Request $request, CourseEnrollment $enrollment): RedirectResponse
    {
        $this->authorizeManager($request);
        $enrollment->delete();

        return back()->with('success', 'Enrollment removed.');
    }

    public function resendAssignmentEmail(Request $request, CourseEnrollment $enrollment): RedirectResponse
    {
        $this->authorizeManager($request);

        $mailSent = $this->sendCourseAssignmentEmail(
            $enrollment->fresh(['course.category', 'student', 'trainer']),
            null,
            false,
            true
        );
        $notificationSent = $this->sendCourseAssignmentNotification(
            $enrollment->fresh(['course.category', 'student', 'trainer']),
            null,
            false,
            true
        );

        $response = back()->with(
            'success',
            $mailSent
                ? ($notificationSent
                    ? 'Course access email resent successfully and dashboard notification sent.'
                    : 'Course access email resent successfully.')
                : ($notificationSent
                    ? 'Course access email could not be resent, but the dashboard notification was sent.'
                    : 'Course access email could not be resent.')
        );

        if (! $mailSent) {
            return $response->withErrors([
                'mail' => 'Course access email could not be resent. Check mail configuration and try again.',
            ]);
        }

        return $response;
    }

    public function myCourses(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        return view('student.courses', [
            'categories' => \App\Models\CourseCategory::with([
                'courses' => function ($query) {
                    $query->with(['category', 'subcategory'])->orderBy('title');
                },
                'children.courses' => function ($query) {
                    $query->with(['category', 'subcategory'])->orderBy('title');
                },
            ])->whereNull('parent_id')->orderBy('name')->get(),
            'enrolledCourseIds' => $user->enrollmentsAsStudent()->pluck('course_id')->all(),
        ]);
    }

    public function history(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        $enrollments = CourseEnrollment::with(['course.category', 'course.subcategory', 'trainer', 'assignedBy'])
            ->where('student_id', $user->id)
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        return view('student.history', [
            'enrollments' => $enrollments,
        ]);
    }

    public function certificates(Request $request, StudentCertificateService $certificateService): View
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        return view('student.certificates', [
            'certificates' => $certificateService->completedCertificatesForStudent($user),
        ]);
    }

    public function downloadCertificate(
        Request $request,
        CourseEnrollment $enrollment,
        StudentCertificateService $certificateService
    ): Response {
        $certificate = $this->resolveStudentCertificateOrAbort($request, $enrollment, $certificateService);
        $svg = $this->renderCertificateSvg($certificate);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$certificate['download_svg_filename'].'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function downloadCertificatePdf(
        Request $request,
        CourseEnrollment $enrollment,
        StudentCertificateService $certificateService
    ): Response {
        $certificate = $this->resolveStudentCertificateOrAbort($request, $enrollment, $certificateService);
        $pdfCertificate = $certificate;
        $pdfCertificate['brand_logo_data_uri'] = $certificateService->pdfBrandLogoDataUri();

        $pdf = Pdf::loadView('student.certificate-pdf', [
            'certificate' => $pdfCertificate,
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$certificate['download_pdf_filename'].'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveStudentCertificateOrAbort(
        Request $request,
        CourseEnrollment $enrollment,
        StudentCertificateService $certificateService
    ): array {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);
        abort_unless((int) $enrollment->student_id === (int) $user->id, 403, 'You can download only your own certificates.');

        $certificate = $certificateService->buildCompletedCertificate($enrollment);
        abort_if(! $certificate, 404, 'Certificate is available only after course completion.');

        return $certificate;
    }

    /**
     * @param  array<string, mixed>  $certificate
     */
    private function renderCertificateSvg(array $certificate): string
    {
        return view('student.certificate-svg', [
            'certificate' => $certificate,
        ])->render();
    }

    public function showEnrolledCourse(Request $request, Course $course): View
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        $enrollment = CourseEnrollment::with(['course.weeks.sessions.items', 'trainer', 'progressItems'])
            ->where('course_id', $course->id)
            ->where('student_id', $user->id)
            ->first();

        abort_unless($enrollment, 403, 'You can open only enrolled courses.');

        $itemIds = CourseSessionItem::whereHas('session.week', fn ($q) => $q->where('course_id', $course->id))->pluck('id');

        foreach ($itemIds as $itemId) {
            CourseProgress::firstOrCreate(
                [
                    'course_enrollment_id' => $enrollment->id,
                    'course_session_item_id' => $itemId,
                ],
                [
                    'completed_at' => null,
                ]
            );
        }

        $totalItems = $itemIds->count();
        $completedItems = $enrollment->progressItems()->whereNotNull('completed_at')->count();
        $enrollment = $enrollment->fresh(['course.weeks.sessions.items', 'trainer', 'progressItems']);

        $latestSubmissions = CourseItemSubmission::query()
            ->where('course_enrollment_id', $enrollment->id)
            ->latest('submitted_at')
            ->get()
            ->groupBy('course_session_item_id')
            ->map->first();

        $completedItemIds = $enrollment->progressItems
            ->whereNotNull('completed_at')
            ->pluck('course_session_item_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $nextPendingItemId = $enrollment->course->weeks
            ->flatMap->sessions
            ->flatMap->items
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->first(fn (int $itemId) => ! in_array($itemId, $completedItemIds, true));

        return view('student.course-show', [
            'enrollment' => $enrollment,
            'totalItems' => $totalItems,
            'completedItems' => $completedItems,
            'latestSubmissions' => $latestSubmissions,
            'completedItemIds' => $completedItemIds,
            'nextPendingItemId' => $nextPendingItemId,
        ]);
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    private function sendCourseAssignmentEmail(
        CourseEnrollment $enrollment,
        ?User $actor,
        bool $isUpdated,
        bool $isReminder = false
    ): bool
    {
        $student = $enrollment->student;
        $course = $enrollment->course;
        $trainerName = $enrollment->trainer?->name ?? 'Not assigned yet';
        $appName = $this->resolveMailBrandName();
        $loginUrl = route('login');
        $myCoursesUrl = route('student.courses');
        $subject = $isReminder
            ? 'Course access reminder'
            : ($isUpdated ? 'Your course assignment has been updated' : 'A course has been assigned to you');
        $assignedBy = $actor?->name ? 'Assigned by: '.$actor->name : null;

        if (! $student || ! $course) {
            return false;
        }

        $html = $this->renderCourseAssignmentEmailHtml(
            appName: $appName,
            userName: $student->name,
            courseTitle: $course->title,
            categoryLabel: $course->category?->name ?? 'General',
            trainerName: $trainerName,
            loginUrl: $loginUrl,
            myCoursesUrl: $myCoursesUrl,
            assignedBy: $assignedBy,
            isUpdated: $isUpdated,
            isReminder: $isReminder
        );

        try {
            Mail::html($html, function ($mail) use ($student, $appName, $subject): void {
                $mail->to($student->email, $student->name)
                    ->subject($subject.' - '.$appName);
            });

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function sendCourseAssignmentNotification(
        CourseEnrollment $enrollment,
        ?User $actor,
        bool $isUpdated,
        bool $isReminder = false
    ): bool {
        $student = $enrollment->student;

        if (! Schema::hasTable('notifications') || ! $student || ! $enrollment->course) {
            return false;
        }

        try {
            $student->notify(new class($enrollment, $actor, $isUpdated, $isReminder) extends BaseNotification {
                use Queueable;

                public function __construct(
                    private CourseEnrollment $enrollment,
                    private ?User $actor,
                    private bool $isUpdated,
                    private bool $isReminder
                ) {
                }

                public function via($notifiable): array
                {
                    return ['database'];
                }

                public function toArray($notifiable): array
                {
                    $course = $this->enrollment->course;
                    $trainerName = $this->enrollment->trainer?->name ?? 'Not assigned yet';
                    $senderName = $this->actor?->name ?? 'Academic Mantra';

                    $title = $this->isReminder
                        ? 'Course access reminder'
                        : ($this->isUpdated ? 'Course assignment updated' : 'New course assigned');

                    $message = $this->isReminder
                        ? sprintf(
                            'Your course %s is available in My Courses. Trainer: %s.',
                            $course?->title ?? 'course',
                            $trainerName
                        )
                        : ($this->isUpdated
                            ? sprintf(
                                'Your assigned course %s was updated. Trainer: %s.',
                                $course?->title ?? 'course',
                                $trainerName
                            )
                            : sprintf(
                                '%s has been assigned to your account. Trainer: %s.',
                                $course?->title ?? 'A course',
                                $trainerName
                            ));

                    return [
                        'title' => $title,
                        'message' => $message,
                        'sender_name' => $senderName,
                        'audience' => 'course_assignment',
                        'notification_kind' => 'course_assignment',
                        'course_id' => $course?->id,
                        'enrollment_id' => $this->enrollment->id,
                        'course_title' => $course?->title,
                        'trainer_name' => $trainerName,
                        'action_label' => 'Open My Courses',
                        'action_route' => route('student.courses'),
                        'assigned_at' => optional($this->enrollment->updated_at ?? $this->enrollment->created_at)->toDateTimeString(),
                        'is_updated' => $this->isUpdated,
                        'is_reminder' => $this->isReminder,
                    ];
                }
            });

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function buildAssignmentSuccessMessage(string $baseMessage, bool $emailSent, bool $notificationSent): string
    {
        if ($emailSent && $notificationSent) {
            return rtrim($baseMessage, '.').', course email sent, and dashboard notification delivered.';
        }

        if ($emailSent) {
            return rtrim($baseMessage, '.').' Course email sent.';
        }

        if ($notificationSent) {
            return rtrim($baseMessage, '.').' Dashboard notification sent.';
        }

        return $baseMessage;
    }

    private function renderCourseAssignmentEmailHtml(
        string $appName,
        string $userName,
        string $courseTitle,
        string $categoryLabel,
        string $trainerName,
        string $loginUrl,
        string $myCoursesUrl,
        ?string $assignedBy,
        bool $isUpdated,
        bool $isReminder
    ): string {
        $details = [
            ['label' => 'Course', 'value' => $courseTitle],
            ['label' => 'Category', 'value' => $categoryLabel],
            ['label' => 'Trainer', 'value' => $trainerName],
        ];

        return $this->renderMailShell(
            eyebrow: $isReminder ? 'Course Reminder' : ($isUpdated ? 'Course Updated' : 'New Course Assigned'),
            title: $isReminder ? 'Your course access details' : ($isUpdated ? 'Your course assignment changed' : 'A new course is ready for you'),
            subtitle: $isReminder
                ? 'We are resending your course access details so you can jump back in quickly.'
                : ($isUpdated
                ? 'Your assigned learning details were updated. Review the latest course information below.'
                : 'A course has been assigned to your account. You can open it from your dashboard now.'),
            greeting: 'Hello '.$userName.',',
            intro: $isReminder
                ? 'Here is a reminder of your course access inside '.$appName.'.'
                : ($isUpdated
                ? 'Your course assignment in '.$appName.' has been updated.'
                : 'A new course has been assigned to you in '.$appName.'.'),
            primaryBoxTitle: 'Course Details',
            primaryRows: $details,
            metaNote: $assignedBy,
            warningText: null,
            actionLabel: 'Login Now',
            actionUrl: $loginUrl,
            secondaryActionLabel: 'Open My Courses',
            secondaryActionUrl: $myCoursesUrl,
            closing: 'Log in and continue your learning from the dashboard.',
            footerText: 'This is an automated email from '.$appName.'.'
        );
    }

    private function renderMailShell(
        string $eyebrow,
        string $title,
        string $subtitle,
        string $greeting,
        string $intro,
        string $primaryBoxTitle,
        array $primaryRows,
        ?string $metaNote,
        ?string $warningText,
        ?string $actionLabel,
        ?string $actionUrl,
        ?string $secondaryActionLabel,
        ?string $secondaryActionUrl,
        string $closing,
        string $footerText
    ): string {
        $brandNameRaw = $this->resolveMailBrandName();
        $appName = e($brandNameRaw);
        $eyebrow = e($eyebrow);
        $title = e($title);
        $subtitle = e($subtitle);
        $greeting = e($greeting);
        $intro = e($intro);
        $primaryBoxTitle = e($primaryBoxTitle);
        $closing = e($closing);
        $footerText = e($footerText);
        $logoDataUri = $this->resolveMailLogoDataUri();

        $primaryHtml = collect($primaryRows)->map(function (array $row): string {
            return sprintf(
                '<div style="margin-bottom:10px; padding:11px 13px; border-radius:16px; background:linear-gradient(180deg, #ffffff, #f7faff); border:1px solid #dce5f2; font-size:13px; line-height:1.55; box-shadow:0 10px 18px rgba(15, 43, 79, 0.05);"><strong style="display:block; color:#6d7f98; font-size:10px; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:4px;">%s</strong><span style="color:#13243d; font-weight:700;">%s</span></div>',
                e((string) ($row['label'] ?? '')),
                e((string) ($row['value'] ?? ''))
            );
        })->implode('');

        $metaHtml = $metaNote
            ? '<div style="margin:0 0 14px; padding:11px 13px; border-radius:16px; background:linear-gradient(135deg, rgba(13, 93, 209, 0.08), rgba(122, 92, 255, 0.07)); border:1px solid #d8e1f4; color:#445a78; font-size:13px; line-height:1.65;">'.e($metaNote).'</div>'
            : '';
        $warningHtml = $warningText
            ? '<div style="margin:0 0 16px; padding:12px 14px; border-radius:16px; background:linear-gradient(135deg, #fff8ea, #ffe7b9); border:1px solid #f0d39a; color:#8b5a12; font-size:13px; line-height:1.65; box-shadow:0 10px 18px rgba(240, 179, 90, 0.14);">'.e($warningText).'</div>'
            : '';
        $primaryActionHtml = ($actionLabel && $actionUrl)
            ? '<div style="margin:0 0 10px;"><a href="'.e($actionUrl).'" style="display:inline-block; padding:11px 20px; border-radius:999px; background:linear-gradient(135deg, #0d5dd1, #7a5cff); color:#ffffff; text-decoration:none; font-size:13px; font-weight:700; letter-spacing:0.01em; box-shadow:0 14px 22px rgba(27, 75, 177, 0.22);">'.e($actionLabel).'</a></div>'
            : '';
        $secondaryActionHtml = ($secondaryActionLabel && $secondaryActionUrl)
            ? '<div style="margin:0 0 18px;"><a href="'.e($secondaryActionUrl).'" style="display:inline-block; padding:10px 16px; border-radius:999px; background:linear-gradient(135deg, #fff7e8, #fff2d5); color:#9a5b00; text-decoration:none; font-size:13px; font-weight:700; border:1px solid #f0ddb9; box-shadow:0 10px 16px rgba(240, 179, 90, 0.12);">'.e($secondaryActionLabel).'</a></div>'
            : '';
        $logoHtml = $logoDataUri !== ''
            ? '<div style="display:inline-flex; align-items:center; justify-content:center; padding:11px 15px; border-radius:20px; background:radial-gradient(circle at left top, rgba(240, 179, 90, 0.28), rgba(240, 179, 90, 0) 38%), rgba(255,255,255,0.98); box-shadow:0 14px 26px rgba(8, 18, 34, 0.16); border:1px solid rgba(255,255,255,0.55);"><img src="'.$logoDataUri.'" alt="'.$appName.'" style="display:block; width:170px; max-width:100%; height:auto;"></div>'
            : '<div style="display:inline-block; padding:8px 12px; border-radius:999px; background:rgba(255,255,255,0.16); color:#ffffff; font-size:11px; letter-spacing:0.12em; text-transform:uppercase; font-weight:700;">'.$appName.'</div>';
        $brandStripHtml = '<div style="margin-top:14px;"><span style="display:inline-block; margin-right:6px; margin-bottom:6px; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.15); color:#ffffff; font-size:10px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">Course Access</span><span style="display:inline-block; margin-right:6px; margin-bottom:6px; padding:6px 10px; border-radius:999px; background:rgba(240,179,90,0.2); color:#ffe3a7; font-size:10px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">Student Update</span><span style="display:inline-block; margin-bottom:6px; padding:6px 10px; border-radius:999px; background:rgba(122,92,255,0.18); color:#dfd6ff; font-size:10px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">Academic Mantra</span></div>';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="margin:0; padding:0; background:#eaf2ff; font-family:'Segoe UI', Arial, Helvetica, sans-serif; color:#16324f;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:radial-gradient(circle at 12% 8%, rgba(79, 140, 255, 0.18) 0%, rgba(79, 140, 255, 0) 42%), radial-gradient(circle at 88% 12%, rgba(122, 92, 255, 0.14) 0%, rgba(122, 92, 255, 0) 45%), linear-gradient(160deg, #eaf2ff, #f2f4ff, #ffffff); margin:0; padding:18px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:660px; background:#ffffff; border-radius:24px; overflow:hidden; border:1px solid rgba(255,255,255,0.42); box-shadow:0 24px 46px rgba(7, 18, 34, 0.14);">
                    <tr>
                        <td style="padding:22px 24px; background:linear-gradient(160deg, rgba(11, 32, 58, 0.96), rgba(54, 45, 120, 0.9), rgba(15, 45, 80, 0.92)); color:#ffffff;">
                            <div style="margin-bottom:14px;">{$logoHtml}</div>
                            <div style="font-size:11px; letter-spacing:1px; text-transform:uppercase; opacity:0.85; margin-bottom:8px;">{$eyebrow}</div>
                            <div style="font-size:24px; line-height:1.15; font-weight:700; max-width:15ch;">{$title}</div>
                            <div style="font-size:13px; line-height:1.6; opacity:0.92; margin-top:8px; max-width:52ch;">{$subtitle}</div>
                            {$brandStripHtml}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px 18px;">
                            <p style="margin:0 0 10px; font-size:14px; line-height:1.6; color:#1a2f4b;">{$greeting}</p>
                            <p style="margin:0 0 14px; font-size:13px; line-height:1.7; color:#52657f;">{$intro}</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 14px; border-collapse:separate; border-spacing:0;">
                                <tr>
                                    <td style="padding:16px; background:linear-gradient(180deg, #f7faff, #eef4ff); border:1px solid #d9e5f5; border-radius:18px;">
                                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#6b7c93; margin-bottom:10px; letter-spacing:0.08em;">{$primaryBoxTitle}</div>
                                        {$primaryHtml}
                                    </td>
                                </tr>
                            </table>
                            {$metaHtml}
                            {$warningHtml}
                            {$primaryActionHtml}
                            {$secondaryActionHtml}
                            <p style="margin:0; font-size:13px; line-height:1.7; color:#5d6d84;">{$closing}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px 18px; color:#617089; font-size:11px; line-height:1.65; border-top:1px solid #e4ebf5; background:linear-gradient(180deg, #fbfdff, #f6f9ff);">
                            <div style="font-weight:700; color:#1c3150; font-size:12px;">{$appName}</div>
                            <div style="margin-top:4px;">{$footerText}</div>
                            <div style="margin-top:4px; color:#8b9ab1;">Learning. Skills. Growth.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function resolveMailBrandName(): string
    {
        $brandName = trim((string) config('app.name', ''));

        if ($brandName === '' || $brandName === 'Laravel') {
            return 'Academic Mantra Services';
        }

        return $brandName;
    }

    private function resolveMailLogoDataUri(): string
    {
        $logoPath = public_path('images/logo.webp');

        if (! is_file($logoPath)) {
            return '';
        }

        $logoContents = file_get_contents($logoPath);

        if ($logoContents === false) {
            return '';
        }

        return 'data:image/webp;base64,'.base64_encode($logoContents);
    }

}
