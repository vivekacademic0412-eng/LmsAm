<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email — Academic Mantra LMS')
            ->view('emails.verify-email', [
                // Keys must match what the blade view reads: $notifiable and $url.
                // Your previous version passed 'user' and 'verificationUrl',
                // but the view expects $notifiable->name and $url — that
                // mismatch is exactly what threw "Undefined variable $notifiable".
                'notifiable' => $notifiable,
                'url'        => $url,
            ]);
    }
}