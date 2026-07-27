<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * AUTO-CREATE ENGINE for course/week certificates.
 *
 * Certificates for type=course and type=week are NEVER created by hand —
 * they are created (as 'locked') and then unlocked automatically the
 * moment a student's progress crosses the threshold your admin set.
 *
 * The trigger point is: wherever in your app a session flips to
 * 'completed' for a student. Call markSessionCompleted() from THAT place
 * (e.g. your existing "mark lesson done" controller/action, a video
 * completion webhook, a quiz-passed event, etc). This method:
 *   1. Upserts the row in user_course_progress to status=completed
 *   2. Re-evaluates the WEEK certificate for that session's week
 *   3. Re-evaluates the COURSE certificate for that session's course
 *
 * evaluateCourseCertificate()/evaluateWeekCertificate() already do the
 * firstOrCreate + unlock logic (see CertificateEligibilityService) — this
 * class's only job is to make sure that logic actually gets CALLED.
 */
class CourseProgressService
{
    public function __construct(
        protected CertificateEligibilityService $eligibility
    ) {}

    /**
     * Call this the moment a session is completed by a user.
     * Returns the (possibly newly unlocked) course + week certificates.
     */
    public function markSessionCompleted(User $user, CourseSession $session): array
    {
        $week = $session->week;
        $course = $week->course;

        DB::table('user_course_progress')->updateOrInsert(
            [
                'user_id'           => $user->id,
                'course_session_id' => $session->id,
            ],
            [
                'course_id'      => $course->id,
                'course_week_id' => $week->id,
                'status'         => 'completed',
                'updated_at'     => now(),
                'created_at'     => now(),
            ]
        );

        // Auto-create/unlock — no admin involved for these two types.
        $weekCertificate   = $this->eligibility->evaluateWeekCertificate($user, $week);
        $courseCertificate = $this->eligibility->evaluateCourseCertificate($user, $course);

        return [
            'week'   => $weekCertificate,
            'course' => $courseCertificate,
        ];
    }

    /**
     * Optional: call this to re-check every enrolled student against a
     * course (e.g. after an admin lowers min_completion_percent). Useful
     * for a "Recalculate all" button in the admin panel.
     */
    public function reevaluateAllForCourse(Course $course): void
    {
        $userIds = DB::table('user_course_progress')
            ->where('course_id', $course->id)
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            $this->eligibility->evaluateCourseCertificate($user, $course);

            foreach ($course->weeks as $week) {
                $this->eligibility->evaluateWeekCertificate($user, $week);
            }
        }
    }
}