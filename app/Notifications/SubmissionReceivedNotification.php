<?php

namespace App\Notifications;

use App\Models\CourseItemSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(private CourseItemSubmission $submission)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $item = $this->submission->item;
        $course = optional(optional($item->session)->week)->course;

        return [
            'title' => 'New submission received',
            'message' => sprintf(
                '%s submitted %s for %s.',
                $this->submission->submitter?->name ?? 'A student',
                strtoupper($this->submission->submission_type),
                $course?->title ?? 'a course'
            ),
            'item_id' => $item?->id,
            'course_id' => $course?->id,
            'submitted_at' => optional($this->submission->submitted_at)->toDateTimeString(),
        ];
    }
}
