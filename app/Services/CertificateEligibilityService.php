<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\CourseWeek;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * ASSUMPTION: a `user_course_progress` table exists (see ARCHITECTURE.md) with columns:
 * user_id, course_id, course_week_id, course_session_id, status ('locked'|'in_progress'|'completed')
 * Adjust table/column names below if your progress-tracking table differs.
 */
class CertificateEligibilityService
{
    /** Overall % of sessions completed by the user for a course */
    public function courseCompletionPercent(User $user, Course $course): float
    {
        $totalSessions = CourseSession::whereHas('week', fn ($q) => $q->where('course_id', $course->id))->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $completedSessions = DB::table('user_course_progress')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->whereNotNull('course_session_id')
            ->count();

        return round(($completedSessions / $totalSessions) * 100, 2);
    }

    /** % completion for a single week */
    public function weekCompletionPercent(User $user, CourseWeek $week): float
    {
        $totalSessions = CourseSession::where('course_week_id', $week->id)->count();
        if ($totalSessions === 0) return 0;

        $completed = DB::table('user_course_progress')
            ->where('user_id', $user->id)
            ->where('course_week_id', $week->id)
            ->where('status', 'completed')
            ->whereNotNull('course_session_id')
            ->count();

        return round(($completed / $totalSessions) * 100, 2);
    }

    /** All sessions admin marked "required" must be completed */
    public function requiredSessionsCompleted(User $user, Course $course): bool
    {
        $requiredSessionIds = CourseSession::whereHas('week', fn ($q) => $q->where('course_id', $course->id))
            ->whereHas('settings', fn ($q) => $q->where('is_required_for_certificate', true))
            ->pluck('id');

        if ($requiredSessionIds->isEmpty()) {
            return true;
        }

        $completedCount = DB::table('user_course_progress')
            ->where('user_id', $user->id)
            ->whereIn('course_session_id', $requiredSessionIds)
            ->where('status', 'completed')
            ->count();

        return $completedCount >= $requiredSessionIds->count();
    }

    /**
     * Checks course-cert eligibility and unlocks it if the rules are met.
     * Call this after any session is marked "completed" for the user.
     */
    public function evaluateCourseCertificate(User $user, Course $course): ?Certificate
    {
        $settings = $course->settings;
        $minPercent = $settings->min_completion_percent ?? 80;

        $percent = $this->courseCompletionPercent($user, $course);
        $requiredDone = $this->requiredSessionsCompleted($user, $course);

        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id, 'type' => Certificate::TYPE_COURSE],
            ['status' => Certificate::STATUS_LOCKED]
        );

        if ($percent >= $minPercent && $requiredDone && !$certificate->isUnlocked()) {
            $certificate->update(['completion_percent' => $percent]);
            $certificate->unlock();
        } else {
            $certificate->update(['completion_percent' => $percent]);
        }

        return $certificate->fresh();
    }

    /** Evaluate a per-week certificate (only if admin enabled it for that week) */
    public function evaluateWeekCertificate(User $user, CourseWeek $week): ?Certificate
    {
        $weekSettings = $week->settings;
        if (!$weekSettings || !$weekSettings->certificate_enabled) {
            return null; // admin hasn't enabled a certificate for this week
        }

        $minPercent = $weekSettings->min_completion_percent
            ?? $week->course->settings->min_completion_percent
            ?? 80;

        $percent = $this->weekCompletionPercent($user, $week);

        $certificate = Certificate::firstOrCreate(
            [
                'user_id'        => $user->id,
                'course_id'      => $week->course_id,
                'course_week_id' => $week->id,
                'type'           => Certificate::TYPE_WEEK,
            ],
            ['status' => Certificate::STATUS_LOCKED]
        );

        $certificate->update(['completion_percent' => $percent]);

        if ($percent >= $minPercent && !$certificate->isUnlocked()) {
            $certificate->unlock();
        }

        return $certificate->fresh();
    }
      /** Overall % of sessions completed by the user for a course */
    public function submitDemoForReview(User $user, CourseCategory $category, int $demoSubmissionId): Certificate
{
    return Certificate::updateOrCreate(
        [
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => Certificate::TYPE_DEMO,
        ],
        [
            'demo_submission_id' => $demoSubmissionId,
            'status'             => Certificate::STATUS_PENDING,
        ]
    );
}
 
/**
 * Admin creates/opens a level certificate for review (e.g. once a
 * student finishes all courses in a level). Also lands as PENDING.
 */
public function submitLevelForReview(User $user, Course $course): Certificate
{
    return Certificate::updateOrCreate(
        [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'type'      => Certificate::TYPE_LEVEL,
        ],
        [
            'status' => Certificate::STATUS_PENDING,
        ]
    );
}
 
/** Admin action: approve + unlock a pending certificate. */
public function approveCertificate(Certificate $certificate, User $admin): Certificate
{
    $certificate->unlock($admin->id);
    return $certificate->fresh();
}
 
/** Admin action: reject — sends it back to locked, clears the review flag. */
public function rejectCertificate(Certificate $certificate): Certificate
{
    $certificate->update([
        'status'      => Certificate::STATUS_LOCKED,
        'approved_by' => null,
        'approved_at' => null,
    ]);
    return $certificate->fresh();
}
 
/**
 * Admin manual issue — for one-off certificates outside the normal
 * flow (e.g. a make-good, a partner-org waiver). Issues + unlocks
 * immediately, attributed to the issuing admin.
 */
public function issueManualCertificate(User $user, Course $course, User $admin, string $type = Certificate::TYPE_COURSE): Certificate
{
    $certificate = Certificate::firstOrCreate(
        ['user_id' => $user->id, 'course_id' => $course->id, 'type' => $type],
        ['status' => Certificate::STATUS_LOCKED]
    );
 
    $certificate->update(['completion_percent' => 100]);
    $certificate->unlock($admin->id);
 
    return $certificate->fresh();
}
}