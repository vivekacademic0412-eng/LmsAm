<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyStudentSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $summary)
    {
    }

    public function build()
    {
        return $this
            ->subject('Daily Student Registration Summary - ' . now()->format('d M Y'))
            ->view('emails.daily-registration-summary')
            ->with(['summary' => $this->summary]);
    }
}