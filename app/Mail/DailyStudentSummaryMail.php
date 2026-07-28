<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyStudentSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $summary;

    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    public function build()
    {
        return $this->subject('Daily Student Registration Summary Report')
            ->view('emails.daily-student-summary');
    }
}