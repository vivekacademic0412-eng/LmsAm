<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\CourseSettings;
use App\Models\CourseType;
use App\Models\CrashCourseLink;
use Illuminate\Support\Str;

/**
 * Keeps Crash Course records in sync with their "parent" course — per category (subject):
 *
 *  Parent course                                  -> Auto-synced Crash Course
 *  ─────────────────────────────────────────────────────────────────────────
 *  Basic Skill-based Industrial Training (1.5mo)   -> Level 1, 5–45 Hours (Flexible)
 *  Professional Industry-ready Course - Beginner    -> Level 2, 90 Hours
 *
 * The crash course never has its own weeks/sessions — it mirrors the parent's
 * content via `crash_course_links.source_course_id` (see WeekelyLevelSeeder).
 * Admins never edit crash courses directly; this service is the only writer.
 */
class CrashCourseSyncService
{
    /**
     * Call this right after a course is created/updated. Does nothing if the
     * course doesn't match one of the two auto-sync rules above.
     */
    public function syncFromParent(Course $parent): ?Course
    {
        $crashLevel = $this->resolveCrashLevel($parent);

        if (!$crashLevel) {
            return null; // this course doesn't trigger a crash-course sync
        }

        $crashCourseType = CourseType::where('name', 'Crash Course')->first();
        $crashLevelName = $crashLevel === 1 ? 'Crash Basic (5-45 hrs)' : 'Crash 90 Hours';
        $crashCourseLevel = CourseLevel::where('name', $crashLevelName)->first();

        if (!$crashCourseType || !$crashCourseLevel) {
            return null; // seed data missing — nothing to sync into
        }

        $title = $crashLevel === 1
            ? "{$parent->category->name} - Crash Course (5-45 Hours)"
            : "{$parent->category->name} - Crash Course (90 Hours)";

        $crashData = [
            'category_id'        => $parent->category_id,
            'subcategory_id'     => $parent->subcategory_id,
            'course_type_id'     => $crashCourseType->id,
            'course_level_id'    => $crashCourseLevel->id,
            'title'              => $title,
            'slug'               => Str::slug($title),
            'short_description'  => "Fast-track version of {$parent->title}.",
            'description'        => $parent->description,
            'thumbnail'          => $parent->thumbnail,
            'language'           => $parent->language,
            'gst'                => $parent->gst,
            'duration_hours'     => 0,
            'created_by'         => $parent->created_by,
        ];

        // Prices stay fixed per the crash-course pricing table (not copied from parent)
        $crashData['original_price'] = $crashLevel === 1 ? 19999 : 49999;
        $crashData['price']          = $crashLevel === 1 ? 14999 : 34999;

        // IMPORTANT: keyed on (category, crash type, crash level) — NOT on slug —
        // so this is idempotent even if a course with this slug already exists
        // from the seeder. This never throws a duplicate-slug error and never
        // creates a second crash course for the same subject.
        $crashCourse = Course::updateOrCreate(
            [
                'category_id'     => $parent->category_id,
                'course_type_id'  => $crashCourseType->id,
                'course_level_id' => $crashCourseLevel->id,
            ],
            $crashData
        );

        // Point (or re-point) the link at this parent — updateOrCreate prevents duplicates
        CrashCourseLink::updateOrCreate(
            ['crash_course_id' => $crashCourse->id],
            ['source_course_id' => $parent->id, 'crash_level' => $crashLevel]
        );

        // Mirror the unlock/certificate rule appropriate for crash courses
        CourseSettings::updateOrCreate(
            ['course_id' => $crashCourse->id],
            [
                'min_completion_percent'     => optional($parent->settings)->min_completion_percent ?? 80,
                'weekly_unlock_mode'         => 'free_no_lock', // crash courses are never locked/sequential
                'certificate_mode'           => 'end_of_course',
                'show_seats_as_full'         => false,
                'zero_day_countdown_enabled' => false,
                'countdown_days'             => 0,
            ]
        );

        return $crashCourse;
    }

    /**
     * Returns 1, 2, or null depending on whether $course matches an auto-sync rule.
     * Matched purely on course_type + course_level (each parent role maps to exactly
     * one course per category per the seeder), not on a hardcoded duration string.
     */
    protected function resolveCrashLevel(Course $course): ?int
    {
        $type = $course->courseType?->name;
        $level = $course->courseLevel?->name;

        return match (true) {
            $type === 'Basic'                                  => 1,
            $type === 'Professional' && $level === 'Beginner'  => 2,
            default                                             => null,
        };
    }
}