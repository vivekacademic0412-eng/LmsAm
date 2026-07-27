<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseSessionSetting;
use App\Models\CourseSettings;
use App\Models\CourseType;
use App\Models\CourseWeek;
use App\Models\CrashCourseLink;
use App\Models\DemoFeatureVideo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WeekelyLevelSeeder extends Seeder
{
    /**
     * NOTE: This seeder assumes the following NEW tables/models already exist
     * via migrations (see ARCHITECTURE.md):
     *   course_settings, course_session_settings, crash_course_links,
     *   certificates, user_courses, user_course_progress, user_attendance
     * If those migrations aren't run yet, comment out the relevant blocks below.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('demo_feature_videos')->truncate();
        DB::table('course_session_items')->truncate();
        if (Schema::hasTable('course_session_settings')) {
            DB::table('course_session_settings')->truncate();
        }
        DB::table('course_sessions')->truncate();
        DB::table('course_weeks')->truncate();
        if (Schema::hasTable('course_settings')) {
            DB::table('course_settings')->truncate();
        }
        if (Schema::hasTable('crash_course_links')) {
            DB::table('crash_course_links')->truncate();
        }
        DB::table('courses')->truncate();
        DB::table('course_levels')->truncate();
        DB::table('course_types')->truncate();
        DB::table('course_categories')->truncate();

        Schema::enableForeignKeyConstraints();

        // ── Subjects / Categories ─────────────────────────────
        $categoryData = [
            ['name' => 'AI-Integrated Digital Marketing Training Course', 'thumbnail' => 'images/course-1.png', 'skills' => ['SEO, GEO & AEO', 'Meta Campaigns', 'AI Agent Workflows & GMB Optimization', 'Video Creation & Editing', 'Social Media Engagement', 'ROI Optimization & Audience Insights']],
            ['name' => 'AI-Integrated SEO Training Course', 'thumbnail' => 'images/course-2.png', 'skills' => ['Keyword Research', 'On-Page & Off-Page SEO', 'Technical SEO', 'Link Building', 'Semrush & Ahrefs', 'Google Search Console & Analytics']],
            ['name' => 'AI-Integrated Graphic Designing Training Course', 'thumbnail' => 'images/course-3.png', 'skills' => ['Figma', 'Canva', 'Photoshop', 'Illustrator', 'Brand Identity Design', 'AI-Assisted Design Workflows']],
            ['name' => 'AI-Integrated HR Training Course', 'thumbnail' => 'images/course-4.png', 'skills' => ['Recruitment & Sourcing', 'Onboarding', 'HR Operations', 'Payroll Basics', 'Employee Engagement', 'HR Analytics']],
            ['name' => 'AI-Integrated Content Writing Training Course', 'thumbnail' => 'images/course-5.png', 'skills' => ['SEO Writing', 'Blog & Article Writing', 'Copywriting', 'Technical Writing', 'AI-Assisted Content Tools', 'Editing & Proofreading']],
            ['name' => 'AI-Integrated Data Analysis Training Course', 'thumbnail' => 'images/course-6.png', 'skills' => ['Excel & Google Sheets', 'SQL', 'Power BI / Tableau', 'Python for Data Analysis', 'Data Visualization', 'Statistical Analysis']],
            ['name' => 'AI-Integrated Videography Training Course', 'thumbnail' => 'images/course-6.png', 'skills' => ['Camera & Shot Composition', 'Premiere Pro Editing', 'Color Grading', 'Motion Graphics', 'Sound Design', 'Short-Form Content for Social']],
            ['name' => 'IT Training Program', 'thumbnail' => 'images/course-6.png', 'skills' => ['Laravel', 'PHP & MySQL', 'React', 'Git & GitHub', 'REST APIs', 'AI-Assisted Development (ChatGPT/Claude)']],
        ];

        $categories = [];
        $creator = User::where('role', User::ROLE_SUPERADMIN)->first()
            ?? User::where('role', User::ROLE_ADMIN)->first();

        foreach ($categoryData as $data) {
            $slug = Str::slug($data['name']);
            $categories[$slug] = CourseCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $data['name'],
                    'description' => "{$data['name']} — live, AI-integrated skills training with real client projects, expert mentors and placement assistance.",
                    'thumbnail'   => $data['thumbnail'],
                ]
            );

            $category = $categories[$slug];
            DemoFeatureVideo::updateOrCreate(
                ['category_id' => $category->id, 'position' => 1],
                [
                    'title'       => 'Subject Introduction',
                    'description' => "Get an overview of {$data['name']} and what you will learn.",
                    'file_path'   => 'demo-feature-videos/course-introduction.mp4',
                    'file_name'   => 'course-introduction.mp4',
                    'file_mime'   => 'video/mp4',
                    'file_size'   => 25400000,
                    'status'      => 1,
                    'uploaded_by' => $creator?->id,
                ]
            );
        }

        // ── Course Types ───────────────────────────────────────
        CourseType::insert([
            ['name' => 'Basic', 'status' => 1],
            ['name' => 'Professional', 'status' => 1],
            ['name' => 'Diploma', 'status' => 1],
            ['name' => 'Crash Course', 'status' => 1],
        ]);

        // ── Course Levels (expanded per real flow) ─────────────
        CourseLevel::insert([
            ['name' => 'Basic', 'status' => 1],                // Basic Industrial Training
            ['name' => 'Beginner', 'status' => 1],              // Professional
            ['name' => 'Beginner +', 'status' => 1],
            ['name' => 'Intermediate', 'status' => 1],
            ['name' => 'Intermediate +', 'status' => 1],
            ['name' => 'Advanced', 'status' => 1],              // seats always "full"
            ['name' => '1 Year Diploma', 'status' => 1],
            ['name' => '1.5 Year Diploma', 'status' => 1],
            ['name' => '2 Year Diploma', 'status' => 1],
            ['name' => 'Crash Basic (5-45 hrs)', 'status' => 1],   // mirrors Basic
            ['name' => 'Crash 90 Hours', 'status' => 1],            // mirrors Professional Beginner
        ]);

        $courseTypes = CourseType::pluck('id', 'name');
        $levels      = CourseLevel::pluck('id', 'name');

        if (!$creator) {
            return;
        }

        foreach ($categoryData as $data) {
            $category   = $categories[Str::slug($data['name'])];
            $skillsList = implode(', ', $data['skills']);

            // title, type, level, duration, prices, weeks count, unlock mode, cert mode, is_crash_source flag
            $courses = [
                [
                    'title' => "{$category->name} - Basic Skill-based Industrial Training",
                    'type' => 'Basic', 'level' => 'Basic',
                    'duration_label' => '1.5 Months', 'weeks' => 6,
                    'original_price' => 19999, 'price' => 14999,
                    'unlock_mode' => 'sequential_all_weeks', 'cert_mode' => 'both',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - Beginner",
                    'type' => 'Professional', 'level' => 'Beginner',
                    'duration_label' => '3 Months', 'weeks' => 12,
                    'original_price' => 49999, 'price' => 34999,
                    'unlock_mode' => 'week1_gate_only', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - Beginner +",
                    'type' => 'Professional', 'level' => 'Beginner +',
                    'duration_label' => '6 Months', 'weeks' => 24,
                    'original_price' => 99998, 'price' => 69998,
                    'unlock_mode' => 'week1_gate_only', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - Intermediate",
                    'type' => 'Professional', 'level' => 'Intermediate',
                    'duration_label' => '9 Months', 'weeks' => 36,
                    'original_price' => 149000, 'price' => 105000,
                    'unlock_mode' => 'week1_gate_only', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - Intermediate +",
                    'type' => 'Professional', 'level' => 'Intermediate +',
                    'duration_label' => '12 Months', 'weeks' => 48,
                    'original_price' => 199996, 'price' => 139996,
                    'unlock_mode' => 'week1_gate_only', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - Advanced",
                    'type' => 'Professional', 'level' => 'Advanced',
                    'duration_label' => '18 Months', 'weeks' => 72,
                    'original_price' => 249999, 'price' => 199999,
                    'unlock_mode' => 'week1_gate_only', 'cert_mode' => 'per_level',
                    'show_seats_full' => true, 'countdown_days' => 0, // seats ALWAYS shown full
                ],
                [
                    'title' => "{$category->name} - 1 Year Diploma",
                    'type' => 'Diploma', 'level' => '1 Year Diploma',
                    'duration_label' => '12 Months', 'weeks' => 48,
                    'original_price' => 199996, 'price' => 139996,
                    'unlock_mode' => 'sequential_all_weeks', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - 1.5 Year Diploma",
                    'type' => 'Diploma', 'level' => '1.5 Year Diploma',
                    'duration_label' => '18 Months', 'weeks' => 72,
                    'original_price' => 249999, 'price' => 199999,
                    'unlock_mode' => 'sequential_all_weeks', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - 2 Year Diploma",
                    'type' => 'Diploma', 'level' => '2 Year Diploma',
                    'duration_label' => '24 Months', 'weeks' => 96,
                    'original_price' => 399999, 'price' => 279999,
                    'unlock_mode' => 'sequential_all_weeks', 'cert_mode' => 'per_level',
                    'show_seats_full' => false, 'countdown_days' => 0,
                ],
                [
                    'title' => "{$category->name} - Crash Course (5-45 Hours)",
                    'type' => 'Crash Course', 'level' => 'Crash Basic (5-45 hrs)',
                    'duration_label' => '5-45 Hours (Flexible)', 'weeks' => 0, // no own weeks — mirrors Basic
                    'original_price' => 19999, 'price' => 14999,
                    'unlock_mode' => 'free_no_lock', 'cert_mode' => 'end_of_course',
                    'show_seats_full' => false, 'countdown_days' => 0,
                    'mirrors_level' => 'Basic',
                ],
                [
                    'title' => "{$category->name} - Crash Course (90 Hours)",
                    'type' => 'Crash Course', 'level' => 'Crash 90 Hours',
                    'duration_label' => '90 Hours', 'weeks' => 0, // no own weeks — mirrors Prof. Beginner
                    'original_price' => 49999, 'price' => 34999,
                    'unlock_mode' => 'free_no_lock', 'cert_mode' => 'end_of_course',
                    'show_seats_full' => false, 'countdown_days' => 0,
                    'mirrors_level' => 'Beginner',
                ],
            ];

            $createdBySlug = []; // title-slug => Course, so crash courses can link to source course_id

            foreach ($courses as $courseData) {
                $isCrash = $courseData['type'] === 'Crash Course';

                $course = Course::updateOrCreate(
                    ['slug' => Str::slug($courseData['title'])],
                    [
                        'category_id'        => $category->id,
                        'subcategory_id'      => null,
                        'course_type_id'      => $courseTypes[$courseData['type']],
                        'course_level_id'     => $levels[$courseData['level']],
                        'title'               => $courseData['title'],
                        'short_description'   => "Learn {$skillsList} through live, AI-integrated projects with real mentors — {$courseData['duration_label']}.",
                        'description'         => "This program covers {$skillsList} through hands-on, live-project-based training. Duration: {$courseData['duration_label']}. Includes expert mentorship, real client-style work, and industry-recognised certification on completion.",
                        'original_price'      => $courseData['original_price'],
                        'price'               => $courseData['price'],
                        'gst'                 => '18',
                        'language'            => 'English',
                        'thumbnail'           => $data['thumbnail'],
                        'duration_hours'      => 0,
                        'created_by'          => $creator->id,
                    ]
                );

                $createdBySlug[$courseData['level']] = $course;

                // ── Course Settings (new) ──
                if (Schema::hasTable('course_settings')) {
                    CourseSettings::updateOrCreate(
                        ['course_id' => $course->id],
                        [
                            'min_completion_percent'     => 80,
                            'weekly_unlock_mode'         => $courseData['unlock_mode'],
                            'certificate_mode'           => $courseData['cert_mode'],
                            'show_seats_as_full'         => $courseData['show_seats_full'],
                            'zero_day_countdown_enabled' => true,
                            'countdown_days'             => $courseData['countdown_days'],
                        ]
                    );
                }

                // ── Crash Course: DO NOT create own weeks — link to source instead ──
                if ($isCrash) {
                    if (Schema::hasTable('crash_course_links') && isset($createdBySlug[$courseData['mirrors_level']])) {
                        CrashCourseLink::updateOrCreate(
                            ['crash_course_id' => $course->id],
                            [
                                'source_course_id' => $createdBySlug[$courseData['mirrors_level']]->id,
                                'crash_level'       => $courseData['level'] === 'Crash Basic (5-45 hrs)' ? 1 : 2,
                            ]
                        );
                    }
                    continue; // skip week/session generation for crash courses entirely
                }

                // ── Weeks / Sessions / Items for real (non-crash) courses ──
                for ($weekNo = 1; $weekNo <= $courseData['weeks']; $weekNo++) {
                    $week = CourseWeek::updateOrCreate(
                        ['course_id' => $course->id, 'week_number' => $weekNo],
                        ['title' => "Week {$weekNo} - Learning Module"]
                    );

                    for ($sessionNo = 1; $sessionNo <= 3; $sessionNo++) {
                        $session = CourseSession::updateOrCreate(
                            ['course_week_id' => $week->id, 'session_number' => $sessionNo],
                            ['title' => "Session {$sessionNo}"]
                        );

                        // First session of each week marked "required" by default (admin can change)
                        if (Schema::hasTable('course_session_settings')) {
                            CourseSessionSetting::updateOrCreate(
                                ['course_session_id' => $session->id],
                                [
                                    'is_required_for_certificate' => $sessionNo === 1,
                                    'meet_link'                   => null,
                                    'meet_datetime'                => null,
                                    'is_visible'                   => true,
                                ]
                            );
                        }

                        $items = [
                            [CourseSessionItem::TYPE_INTRO, 'Introduction & Overview', 'video_or_ppt'],
                            [CourseSessionItem::TYPE_MAIN_VIDEO, 'Main Learning Video', 'video'],
                            [CourseSessionItem::TYPE_TASK, 'Practical Assignment', null],
                            [CourseSessionItem::TYPE_QUIZ, 'Knowledge Quiz', null],
                        ];

                        foreach ($items as [$type, $titlePart, $resourceType]) {
                            CourseSessionItem::updateOrCreate(
                                ['course_session_id' => $session->id, 'item_type' => $type],
                                [
                                    'title'         => "{$titlePart} - Week {$weekNo} Session {$sessionNo}",
                                    'resource_type' => $resourceType,
                                    'content'       => "{$titlePart} content for {$course->title}",
                                    'resource_url'  => "https://example.com/resources/" . Str::slug($course->title) . "/week-{$weekNo}/session-{$sessionNo}",
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}