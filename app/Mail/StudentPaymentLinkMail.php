<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentPaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public Payment $payment,
        public string $password
    ) {
    }

    public function build()
    {
        return $this->subject('Complete Your Course Registration')
            ->view('emails.student-payment-link');
    }
}