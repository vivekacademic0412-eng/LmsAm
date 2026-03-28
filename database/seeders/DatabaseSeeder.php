<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseWeek;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\CourseCategory;
use App\Models\CourseItemSubmission;
use App\Models\DemoTask;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            ['name' => 'Demo User', 'email' => 'demo@lms.test', 'role' => User::ROLE_DEMO],
        ];
        $avatarFiles = [
            'avatars/seed-1.svg',
            'avatars/seed-2.svg',
            'avatars/seed-3.svg',
            'avatars/seed-4.svg',
        ];

        foreach ($users as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'is_active' => true,
                    'password' => $password,
                ]
            );
            if (! $user->avatar) {
                $user->avatar = $avatarFiles[$index % count($avatarFiles)];
                $user->save();
            }
        }

        $bulkUsers = [
            User::ROLE_ADMIN => 5,
            User::ROLE_MANAGER_HR => 8,
            User::ROLE_IT => 8,
            User::ROLE_TRAINER => 12,
            User::ROLE_STUDENT => 40,
            User::ROLE_DEMO => 6,
        ];

        foreach ($bulkUsers as $role => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $user = User::updateOrCreate(
                    ['email' => "{$role}{$i}@lms.test"],
                    [
                        'name' => ucwords(str_replace('_', ' ', $role))." {$i}",
                        'role' => $role,
                        'is_active' => $faker->boolean(92),
                        'password' => $password,
                    ]
                );
                if (! $user->avatar) {
                    $user->avatar = $avatarFiles[($i + strlen($role)) % count($avatarFiles)];
                    $user->save();
                }
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
        $thumbnailFiles = [
            'images/course-1.svg',
            'images/course-2.svg',
            'images/course-3.svg',
            'images/course-4.svg',
            'images/course-5.svg',
            'images/course-6.svg',
        ];

        foreach ($categoryNames as $name) {
            $slug = Str::slug($name);
            $thumb = $thumbnailFiles[array_search($name, $categoryNames, true) % count($thumbnailFiles)];
            $categories[$slug] = CourseCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $name.' related courses', 'thumbnail' => $thumb]
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
                    $thumb = $thumbnailFiles[($index + strlen($categorySlug)) % count($thumbnailFiles)];
                    $course = Course::updateOrCreate(
                        ['slug' => "{$categorySlug}-".($index + 1)],
                        [
                            'category_id' => $category->id,
                            'title' => $title,
                            'description' => "{$title} dummy content for LMS testing.",
                            'duration_hours' => rand(2, 16),
                            'thumbnail' => $thumb,
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

                $taskItem = $course->weeks
                    ->flatMap(fn ($week) => $week->sessions)
                    ->flatMap(fn ($session) => $session->items)
                    ->firstWhere('item_type', CourseSessionItem::TYPE_TASK);
                $quizItem = $course->weeks
                    ->flatMap(fn ($week) => $week->sessions)
                    ->flatMap(fn ($session) => $session->items)
                    ->firstWhere('item_type', CourseSessionItem::TYPE_QUIZ);

                if ($taskItem) {
                    $taskItem->update(['is_live' => true]);
                    $submissionPath = 'task-submissions/'.$enrollment->id.'/'.$taskItem->id.'/seed-task.txt';
                    if (! Storage::disk('local')->exists($submissionPath)) {
                        Storage::disk('local')->put($submissionPath, "Seeded task submission for {$student->email}");
                    }

                    CourseItemSubmission::updateOrCreate(
                        [
                            'course_enrollment_id' => $enrollment->id,
                            'course_session_item_id' => $taskItem->id,
                            'submitted_by' => $student->id,
                        ],
                        [
                            'submission_type' => CourseSessionItem::TYPE_TASK,
                            'answer_text' => null,
                            'file_path' => $submissionPath,
                            'file_name' => 'seed-task.txt',
                            'file_mime' => 'text/plain',
                            'file_size' => Storage::disk('local')->size($submissionPath),
                            'submitted_at' => now()->subDays(rand(1, 6)),
                        ]
                    );
                }

                if ($quizItem) {
                    $quizItem->update(['is_live' => true, 'live_at' => now()->subDays(rand(1, 5))]);
                    CourseItemSubmission::updateOrCreate(
                        [
                            'course_enrollment_id' => $enrollment->id,
                            'course_session_item_id' => $quizItem->id,
                            'submitted_by' => $student->id,
                        ],
                        [
                            'submission_type' => CourseSessionItem::TYPE_QUIZ,
                            'answer_text' => "Seeded quiz answer from {$student->name}.",
                            'submitted_at' => now()->subDays(rand(1, 6)),
                        ]
                    );
                }
            }
        }

        $demoCreator = $adminAssigner ?? $creator;
        if ($demoCreator) {
            $demoTaskTitles = [
                'Upload Your Resume',
                'Write a Short Introduction',
                'Answer Product Quiz',
            ];

            $demoTasks = collect($demoTaskTitles)->map(function (string $title) use ($demoCreator) {
                return DemoTask::updateOrCreate(
                    ['title' => $title],
                    [
                        'description' => "{$title} demo task for onboarding.",
                        'resource_url' => 'https://example.com/demo/'.Str::slug($title),
                        'ai_video_url' => 'https://example.com/demo-ai/'.Str::slug($title),
                        'created_by' => $demoCreator->id,
                    ]
                );
            });

            $demoUsers = User::where('role', User::ROLE_DEMO)->get();

            foreach ($demoUsers as $demoUser) {
                foreach ($demoTasks as $demoTask) {
                    $assignment = DemoTaskAssignment::updateOrCreate(
                        [
                            'demo_task_id' => $demoTask->id,
                            'user_id' => $demoUser->id,
                        ],
                        [
                            'assigned_by' => $demoCreator->id,
                            'assigned_at' => now()->subDays(rand(1, 5)),
                        ]
                    );

                    if (rand(0, 1) === 1) {
                        $submissionPath = 'demo-task-submissions/'.$assignment->id.'/seed-demo.txt';
                        if (! Storage::disk('local')->exists($submissionPath)) {
                            Storage::disk('local')->put($submissionPath, "Seeded demo submission for {$demoUser->email}");
                        }

                        DemoTaskSubmission::updateOrCreate(
                            [
                                'demo_task_assignment_id' => $assignment->id,
                            ],
                            [
                                'answer_text' => "Seeded answer from {$demoUser->name}.",
                                'file_path' => $submissionPath,
                                'file_name' => 'seed-demo.txt',
                                'file_mime' => 'text/plain',
                                'file_size' => Storage::disk('local')->size($submissionPath),
                                'submitted_at' => now()->subDays(rand(1, 3)),
                            ]
                        );
                    }
                }
            }
        }
    }
}

