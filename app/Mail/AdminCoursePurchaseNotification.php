<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AdminCoursePurchaseNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public Payment $payment;
    public Collection $courses;

    /**
     * @param  \App\Models\User  $student
     * @param  \App\Models\Payment  $payment
     * @param  \Illuminate\Support\Collection<int, \App\Models\Course>  $courses
     */
    public function __construct($student, Payment $payment, Collection $courses)
    {
        $this->student = $student;
        $this->payment = $payment;
        $this->courses = $courses;
    }

    public function build()
    {
        return $this->subject('New Enrollment — ' . $this->payment->invoice_no)
            ->markdown('emails.admin-course-purchase-notification');
    }
}