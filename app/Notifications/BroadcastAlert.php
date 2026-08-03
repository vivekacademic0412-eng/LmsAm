<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BroadcastAlert extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $senderName,
        public string $audience,
        public ?int $courseId = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'sender_name' => $this->senderName,
            'audience' => $this->audience,
            'course_id' => $this->courseId,
            'broadcasted_at' => now()->toDateTimeString(),
        ];
    }
}