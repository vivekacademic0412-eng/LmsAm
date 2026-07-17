<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewStudentRegisteredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function build()
    {
        return $this
            ->subject('New Student Registration — ' . $this->user->name . ' ' . $this->user->last_name)
            // FIX: your build() called 'emails.new-student-admin-notify', but the
            // blade file on disk is resources/views/emails/new-student-registered.blade.php.
            // A missing view throws immediately, which — because the mail send was
            // inside your DB transaction — rolled back the whole registration.
            ->view('emails.new-student-registered')
            ->with(['user' => $this->user]);
    }
}