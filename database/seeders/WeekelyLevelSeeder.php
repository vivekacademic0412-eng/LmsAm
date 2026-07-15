<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseType;
use App\Models\CourseWeek;
use App\Models\DemoFeatureVideo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
class WeekelyLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Schema::disableForeignKeyConstraints();

    DB::table('demo_feature_videos')->truncate();
    DB::table('course_session_items')->truncate();
    DB::table('course_sessions')->truncate();
    DB::table('course_weeks')->truncate();
    DB::table('courses')->truncate();
    DB::table('course_levels')->truncate();
    DB::table('course_types')->truncate();
    DB::table('course_categories')->truncate();

    Schema::enableForeignKeyConstraints();
        $categoryData = [
    [
        'name' => 'AI-Integrated Digital Marketing Training Course',
        'thumbnail' => 'images/course-1.png',
        'skills' => ['SEO, GEO & AEO', 'Meta Campaigns', 'AI Agent Workflows & GMB Optimization', 'Video Creation & Editing', 'Social Media Engagement', 'ROI Optimization & Audience Insights'],
    ],
    [
        'name' => 'AI-Integrated SEO Training Course',
        'thumbnail' => 'images/course-2.png',
        'skills' => ['Keyword Research', 'On-Page & Off-Page SEO', 'Technical SEO', 'Link Building', 'Semrush & Ahrefs', 'Google Search Console & Analytics'],
    ],
    [
        'name' => 'AI-Integrated Graphic Designing Training Course',
        'thumbnail' => 'images/course-3.png',
        'skills' => ['Figma', 'Canva', 'Photoshop', 'Illustrator', 'Brand Identity Design', 'AI-Assisted Design Workflows'],
    ],
    [
        'name' => 'AI-Integrated HR Training Course',
        'thumbnail' => 'images/course-4.png',
        'skills' => ['Recruitment & Sourcing', 'Onboarding', 'HR Operations', 'Payroll Basics', 'Employee Engagement', 'HR Analytics'],
    ],
    [
        'name' => 'AI-Integrated Content Writing Training Course',
        'thumbnail' => 'images/course-5.png',
        'skills' => ['SEO Writing', 'Blog & Article Writing', 'Copywriting', 'Technical Writing', 'AI-Assisted Content Tools', 'Editing & Proofreading'],
    ],
    [
        'name' => 'AI-Integrated Data Analysis Training Course',
        'thumbnail' => 'images/course-6.png',
        'skills' => ['Excel & Google Sheets', 'SQL', 'Power BI / Tableau', 'Python for Data Analysis', 'Data Visualization', 'Statistical Analysis'],
    ],
    [
        'name' => 'AI-Integrated Videography Training Course',
        'thumbnail' => 'images/course-6.png',
        'skills' => ['Camera & Shot Composition', 'Premiere Pro Editing', 'Color Grading', 'Motion Graphics', 'Sound Design', 'Short-Form Content for Social'],
    ],
    [
        'name' => 'IT Training Program',
        'thumbnail' => 'images/course-6.png',
        'skills' => ['Laravel', 'PHP & MySQL', 'React', 'Git & GitHub', 'REST APIs', 'AI-Assisted Development (ChatGPT/Claude)'],
    ],
    
];

$categories = [];

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
}

CourseType::insert([
    ['name' => 'Basic', 'status' => 1],
    ['name' => 'Professional', 'status' => 1],
    ['name' => 'Crash Course', 'status' => 1],
    ['name' => 'Diploma', 'status' => 1],
]);

CourseLevel::insert([
    ['name' => 'Beginner', 'status' => 1],
    ['name' => 'Intermediate', 'status' => 1],
    ['name' => 'Advanced', 'status' => 1],
]);

$courseTypes = CourseType::pluck('id', 'name');
$levels      = CourseLevel::pluck('id', 'name');
$creator     = User::where('role', User::ROLE_SUPERADMIN)->first()
    ?? User::where('role', User::ROLE_ADMIN)->first();

if ($creator) {

    foreach ($categoryData as $data) {

        $category = $categories[Str::slug($data['name'])];
        $skillsList = implode(', ', $data['skills']);

        // Real course-type structure confirmed from live site pages
        $courses = [
            [
                'title'          => "{$category->name} — Basic",
                'type_id'        => $courseTypes['Basic'],
                'level_id'       => $levels['Beginner'],
                'duration_hours' => rand(5, 45) * 8, // 5–45 days, ~8 hrs/day
                'duration_label' => '5 to 45 Days',
            ],
            [
                'title'          => "{$category->name} — Professional",
                'type_id'        => $courseTypes['Professional'],
                'level_id'       => $levels['Intermediate'],
                'duration_hours' => rand(3, 18) * 30 * 3, // 3–18 months, ~3 hrs/day avg
                'duration_label' => '3 to 18 Months',
            ],
            [
                'title'          => "{$category->name} — Crash Course",
                'type_id'        => $courseTypes['Crash Course'],
                'level_id'       => $levels['Beginner'],
                'duration_hours' => collect([45, 90])->random() * 3,
                'duration_label' => '45 or 90 Days',
            ],
            [
                'title'          => "{$category->name} — Diploma",
                'type_id'        => $courseTypes['Diploma'],
                'level_id'       => $levels['Advanced'],
                'duration_hours' => collect([12, 18, 24])->random() * 30 * 3, // 1 / 1.5 / 2 years
                'duration_label' => '1, 1.5 or 2 Years',
            ],
        ];

        foreach ($courses as $index => $courseData) {

            $course = Course::updateOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'category_id'       => $category->id,
                    'subcategory_id'    => null,
                    'course_type_id'    => $courseData['type_id'],
                    'course_level_id'   => $courseData['level_id'],

                    'title'             => $courseData['title'],
                    'short_description' => "Learn {$skillsList} through live, AI-integrated projects with real mentors — {$courseData['duration_label']}.",

                    'description'       => "
This program covers {$skillsList} through hands-on, live-project-based training — zero classroom-only teaching.
Duration: {$courseData['duration_label']}. Includes expert mentorship, real client-style work, and industry-recognised
certification on completion. Placement assistance and interview preparation included where applicable.
",

                    'language'          => 'English',
                    'thumbnail'         => $data['thumbnail'],
                    'duration_hours'    => $courseData['duration_hours'],
                    'created_by'        => $creator->id,
                ]
            );

            // ── Weeks, Sessions, Session Items, Demo Videos ──
            // (unchanged from your original seeder logic — kept identical below)

            for ($weekNo = 1; $weekNo <= 4; $weekNo++) {

                $week = CourseWeek::updateOrCreate(
                    ['course_id' => $course->id, 'week_number' => $weekNo],
                    ['title' => "Week {$weekNo} - Learning Module"]
                );

                for ($sessionNo = 1; $sessionNo <= 3; $sessionNo++) {

                    $session = CourseSession::updateOrCreate(
                        ['course_week_id' => $week->id, 'session_number' => $sessionNo],
                        ['title' => "Session {$sessionNo}"]
                    );

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

            $demoVideos = [
                ['title' => 'Course Introduction', 'description' => "Get an overview of {$course->title} and what you will learn.", 'file_name' => 'course-introduction.mp4', 'file_path' => 'demo-feature-videos/course-introduction.mp4', 'file_mime' => 'video/mp4', 'file_size' => 25400000],
                ['title' => 'Course Demo Session', 'description' => "Watch a sample learning session from {$course->title}.", 'file_name' => 'demo-session.mp4', 'file_path' => 'demo-feature-videos/demo-session.mp4', 'file_mime' => 'video/mp4', 'file_size' => 45800000],
                ['title' => 'Student Project Showcase', 'description' => "See practical projects and outcomes from {$course->title}.", 'file_name' => 'project-showcase.mp4', 'file_path' => 'demo-feature-videos/project-showcase.mp4', 'file_mime' => 'video/mp4', 'file_size' => 38600000],
            ];

            foreach ($demoVideos as $position => $video) {
                DemoFeatureVideo::updateOrCreate(
                    ['course_id' => $course->id, 'position' => $position + 1],
                    [
                        'title'       => $video['title'],
                        'description' => $video['description'],
                        'file_path'   => $video['file_path'],
                        'file_name'   => $video['file_name'],
                        'file_mime'   => $video['file_mime'],
                        'file_size'   => $video['file_size'],
                        'status'      => 1,
                        'uploaded_by' => $creator->id,
                    ]
                );
            }
        }
    }
}
    }
}
