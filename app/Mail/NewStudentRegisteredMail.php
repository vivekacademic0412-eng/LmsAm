<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewStudentRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $student)
    {
    }

    public function build()
    {
        return $this->subject('New Student Registration — ' . $this->student->name)
            ->view('emails.new-student-admin-notify');
    }
}