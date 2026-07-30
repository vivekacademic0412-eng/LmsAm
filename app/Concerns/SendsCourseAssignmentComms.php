<?php

namespace App\Concerns;

use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

trait SendsCourseAssignmentComms
{
    protected function sendCourseAssignmentEmail(
        CourseEnrollment $enrollment,
        ?User $actor,
        bool $isUpdated = false,
        bool $isReminder = false
    ): bool {
        $student = $enrollment->student;
        $course = $enrollment->course;

        if (! $student || ! $course) {
            return false;
        }

        $trainerName = $enrollment->trainer?->name ?? 'Not assigned yet';
        $appName = $this->resolveMailBrandName();

        $subject = $isReminder
            ? 'Course access reminder'
            : ($isUpdated
                ? 'Your course assignment has been updated'
                : 'A course has been assigned to you');

        $html = $this->renderCourseAssignmentEmailHtml(
            appName: $appName,
            userName: $student->name,
            courseTitle: $course->title,
            categoryLabel: $course->category?->name ?? 'General',
            trainerName: $trainerName,
            loginUrl: route('login'),
            myCoursesUrl: route('student.courses'),
            assignedBy: $actor?->name ? 'Assigned by: '.$actor->name : null,
            isUpdated: $isUpdated,
            isReminder: $isReminder
        );

        try {
            Mail::html($html, function ($mail) use ($student, $subject, $appName) {
                $mail->to($student->email, $student->name)
                    ->subject($subject.' - '.$appName);
            });

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    protected function sendCourseAssignmentNotification(
        CourseEnrollment $enrollment,
        ?User $actor,
        bool $isUpdated = false,
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
                    $trainer = $this->enrollment->trainer?->name ?? 'Not assigned yet';

                    return [
                        'title' => $this->isReminder
                            ? 'Course access reminder'
                            : ($this->isUpdated ? 'Course assignment updated' : 'New course assigned'),

                        'message' => $this->isReminder
                            ? "Your course {$course->title} is available."
                            : ($this->isUpdated
                                ? "Your course {$course->title} has been updated."
                                : "{$course->title} has been assigned to you."),

                        'sender_name' => $this->actor?->name ?? config('app.name'),

                        'course_id' => $course->id,
                        'course_title' => $course->title,
                        'trainer_name' => $trainer,
                        'enrollment_id' => $this->enrollment->id,
                        'action_route' => route('student.courses'),
                        'action_label' => 'Open My Courses',
                        'is_updated' => $this->isUpdated,
                        'is_reminder' => $this->isReminder,
                    ];
                }
            });

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    protected function buildAssignmentSuccessMessage(
        string $base,
        bool $emailSent,
        bool $notificationSent
    ): string {
        if ($emailSent && $notificationSent) {
            return rtrim($base, '.').', course email sent, and dashboard notification delivered.';
        }

        if ($emailSent) {
            return rtrim($base, '.').' Course email sent.';
        }

        if ($notificationSent) {
            return rtrim($base, '.').' Dashboard notification sent.';
        }

        return $base;
    }
}