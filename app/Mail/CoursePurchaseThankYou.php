<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CoursePurchaseThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment, public Collection $courses) {}

    public function build()
    {
        return $this->subject('Thank you for choosing Academic Mantra! 🎉')
            ->markdown('emails.courses.thank-you', [
                'payment' => $this->payment,
                'courses' => $this->courses,
            ]);
    }
}