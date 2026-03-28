<?php

namespace App\Notifications;

use App\Models\CourseSessionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuizLiveNotification extends Notification
{
    use Queueable;

    public function __construct(private CourseSessionItem $item, private User $trainer)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $course = optional(optional($this->item->session)->week)->course;

        return [
            'title' => 'Quiz is live',
            'message' => sprintf(
                '%s started a quiz for %s.',
                $this->trainer->name,
                $course?->title ?? 'your course'
            ),
            'item_id' => $this->item->id,
            'course_id' => $course?->id,
            'live_at' => optional($this->item->live_at)->toDateTimeString(),
        ];
    }
}
