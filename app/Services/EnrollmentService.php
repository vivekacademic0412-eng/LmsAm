<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\Payment;

class EnrollmentService
{
    /**
     * Enroll the student for the course tied to this payment.
     * Safe to call more than once (e.g. duplicate webhook) — won't duplicate rows.
     */
    public function enroll(Payment $payment): CourseEnrollment
    {
        return CourseEnrollment::firstOrCreate(
            [
                'course_id'  => $payment->course_id,
                'student_id' => $payment->user_id,
            ],
            [
                'trainer_id'              => $payment->course->trainer_id ?? null,
                'assigned_by'             => null,
                'status'                  => 'active',
                'enrolled_at'             => now(),

                'order_reference'         => $payment->razorpay_order_id,
                'amount_paid'             => $payment->paid_amount,
                'registered_at'           => now(),
                'zero_day_start_at'       => now(),
                'progress_percent'        => 0,
                'certificate_unlocked_at' => null,
                'batch_id'                => $payment->batch_id ?? null,
                'course_level_id'         => $payment->course_level_id ?? null,
            ]
        );
    }
}