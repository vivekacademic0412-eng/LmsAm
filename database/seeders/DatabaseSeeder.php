<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseWeek;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');
        $faker = fake();

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@lms.test', 'role' => User::ROLE_SUPERADMIN],
            ['name' => 'Admin', 'email' => 'admin@lms.test', 'role' => User::ROLE_ADMIN],
            ['name' => 'Manager HR', 'email' => 'manager.hr@lms.test', 'role' => User::ROLE_MANAGER_HR],
            ['name' => 'IT', 'email' => 'it@lms.test', 'role' => User::ROLE_IT],
            ['name' => 'Trainer', 'email' => 'trainer@lms.test', 'role' => User::ROLE_TRAINER],
            ['name' => 'Student', 'email' => 'student@lms.test', 'role' => User::ROLE_STUDENT],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'is_active' => true,
                    'password' => $password,
                ]
            );
        }

        $bulkUsers = [
            User::ROLE_ADMIN => 5,
            User::ROLE_MANAGER_HR => 8,
            User::ROLE_IT => 8,
            User::ROLE_TRAINER => 12,
            User::ROLE_STUDENT => 40,
        ];

        foreach ($bulkUsers as $role => $count) {
            for ($i = 1; $i <= $count; $i++) {
                User::updateOrCreate(
                    ['email' => "{$role}{$i}@lms.test"],
                    [
                        'name' => ucwords(str_replace('_', ' ', $role))." {$i}",
                        'role' => $role,
                        'is_active' => $faker->boolean(92),
                        'password' => $password,
                    ]
                );
            }
        }

        $categoryNames = [
            'HR Training',
            'IT Security',
            'Leadership',
            'Finance Basics',
            'Project Management',
            'Communication Skills',
            'Data Analysis',
            'Sales Excellence',
            'Customer Support',
            'Compliance and Policy',
        ];
        $categories = [];

        foreach ($categoryNames as $name) {
            $slug = Str::slug($name);
            $categories[$slug] = CourseCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $name.' related courses']
            );
        }

        $creator = User::where('role', User::ROLE_SUPERADMIN)->first()
            ?? User::where('role', User::ROLE_ADMIN)->first();

        if ($creator) {
            $courseTitles = [
                'Foundations',
                'Beginner Track',
                'Intermediate Workshop',
                'Advanced Practice',
                'Case Study Program',
            ];

            foreach ($categories as $categorySlug => $category) {
                foreach ($courseTitles as $index => $courseTitle) {
                    $title = "{$category->name} {$courseTitle}";
                    $course = Course::updateOrCreate(
                        ['slug' => "{$categorySlug}-".($index + 1)],
                        [
                            'category_id' => $category->id,
                            'title' => $title,
                            'description' => "{$title} dummy content for LMS testing.",
                            'duration_hours' => rand(2, 16),
                            'created_by' => $creator->id,
                        ]
                    );

                    for ($weekNo = 1; $weekNo <= 2; $weekNo++) {
                        $week = CourseWeek::updateOrCreate(
                            [
                                'course_id' => $course->id,
                                'week_number' => $weekNo,
                            ],
                            [
                                'title' => "Week {$weekNo} Learning",
                            ]
                        );

                        for ($sessionNo = 1; $sessionNo <= 2; $sessionNo++) {
                            $session = CourseSession::updateOrCreate(
                                [
                                    'course_week_id' => $week->id,
                                    'session_number' => $sessionNo,
                                ],
                                [
                                    'title' => "Session {$sessionNo}",
                                ]
                            );

                            $items = [
                                [CourseSessionItem::TYPE_INTRO, 'Introduction Material', 'video_or_ppt'],
                                [CourseSessionItem::TYPE_MAIN_VIDEO, 'Main Video', 'video'],
                                [CourseSessionItem::TYPE_TASK, 'Task', null],
                                [CourseSessionItem::TYPE_QUIZ, 'Quiz', null],
                            ];

                            foreach ($items as [$type, $titlePart, $resourceType]) {
                                CourseSessionItem::updateOrCreate(
                                    [
                                        'course_session_id' => $session->id,
                                        'item_type' => $type,
                                    ],
                                    [
                                        'title' => "{$titlePart} - Week {$weekNo} Session {$sessionNo}",
                                        'resource_type' => $resourceType,
                                        'content' => "Dummy {$titlePart} content for {$course->title}.",
                                        'resource_url' => 'https://example.com/resource/'.Str::slug($course->title)."/{$weekNo}/{$sessionNo}/{$type}",
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }

        $students = User::where('role', User::ROLE_STUDENT)->limit(20)->get();
        $trainers = User::where('role', User::ROLE_TRAINER)->get();
        $courses = Course::with('weeks.sessions.items')->inRandomOrder()->limit(12)->get();
        $adminAssigner = User::where('role', User::ROLE_ADMIN)->first() ?? $creator;

        foreach ($students as $student) {
            foreach ($courses->random(min(3, $courses->count())) as $course) {
                $trainer = $trainers->isNotEmpty() ? $trainers->random() : null;

                $enrollment = CourseEnrollment::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'trainer_id' => $trainer?->id,
                        'assigned_by' => $adminAssigner?->id ?? $creator->id,
                    ]
                );

                $allItemIds = $course->weeks
                    ->flatMap(fn ($week) => $week->sessions)
                    ->flatMap(fn ($session) => $session->items)
                    ->pluck('id')
                    ->values();
                $completedSample = $allItemIds->isNotEmpty() ? $allItemIds->random(rand(1, $allItemIds->count())) : collect();
                $completedIds = collect($completedSample)->flatten()->values()->all();

                foreach ($allItemIds as $itemId) {
                    CourseProgress::updateOrCreate(
                        [
                            'course_enrollment_id' => $enrollment->id,
                            'course_session_item_id' => $itemId,
                        ],
                        [
                            'completed_at' => in_array($itemId, $completedIds, true) ? now() : null,
                        ]
                    );
                }
            }
        }
    }
}
