<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\DemoUser;
use App\Models\Payment;

class EnrollmentService
{
    /**
     * Enroll the student for a paid course. Only call this when
     * $payment->type is a real course purchase (course_id is present).
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

    /**
     * Record a demo booking. There is no course_id here — the student's
     * interest is stored against demo_users.interest_area_id (category id).
     * Safe to call more than once (e.g. duplicate webhook) — updateOrCreate
     * won't duplicate rows per user.
     */
    public function enrollDemo(Payment $payment): DemoUser
    {
        return DemoUser::updateOrCreate(
            ['user_id' => $payment->user_id],
            [
                'full_name'          => $payment->name,
                'email'              => $payment->email,
                'phone'              => $payment->phone,
                'interest_area_id'   => $payment->category_id,
                'education_level_id'=>null,
                'preferred_course_id'=>null,
                'ip_address'         => request()->ip(),
                'progress_demo'=>0,
                'demo_feature_video_id'=>null,
            ]
        );
    }
}