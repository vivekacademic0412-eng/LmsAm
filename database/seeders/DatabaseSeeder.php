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
use App\Models\CourseType;
use App\Models\DemoFeatureVideo;
use App\Models\CourseLevel;
use App\Models\EducationLevel;
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
        $this->call([
            CourseLevelSeeder::class,
            CourseTypeSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            EducationLevelSeeder::class,
            HeroSectionSeeder::class,

        ]);
        $password = Hash::make('password');
        $faker = fake();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@lms.test',
                'role' => User::ROLE_SUPERADMIN,
                'contact' => '9388399939',
                'gender' => 'male',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@lms.test',
                'role' => User::ROLE_ADMIN,
                'contact' => '9388399940',
                'gender' => 'male',
            ],
            [
                'name' => 'Manager HR',
                'email' => 'manager.hr@lms.test',
                'role' => User::ROLE_MANAGER_HR,
                'contact' => '9388399941',
                'gender' => 'female',
            ],
            [
                'name' => 'IT',
                'email' => 'it@lms.test',
                'role' => User::ROLE_IT,
                'contact' => '9388399942',
                'gender' => 'male',
            ],
            [
                'name' => 'Trainer',
                'email' => 'trainer@lms.test',
                'role' => User::ROLE_TRAINER,
                'contact' => '9388399943',
                'gender' => 'male',
            ],
            [
                'name' => 'Student',
                'email' => 'student@lms.test',
                'role' => User::ROLE_STUDENT,
                'contact' => '9388399944',
                'gender' => 'other',
            ],
            [
                'name' => 'Demo User',
                'email' => 'demo@lms.test',
                'role' => User::ROLE_DEMO,
                'contact' => '9388399945',
                'gender' => 'other',
            ],
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
                    'contact'   => $data['contact'],
                    'gender'    => $data['gender'],
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
                'name'      => ucwords(str_replace('_', ' ', $role)) . " {$i}",
                'role'      => $role,
                'is_active' => $faker->boolean(92),
                'password'  => $password,
                'contact'   => $faker->numerify('98########'),
                'gender'    => $faker->randomElement(['male', 'female', 'other']),
            ]
        );

        if (! $user->avatar) {
            $user->avatar = $avatarFiles[($i + strlen($role)) % count($avatarFiles)];
            $user->save();
        }
    }
}
        // $categoryNames = [
        //     'HR Training',
        //     'IT Security',
        //     'Leadership',
        //     'Finance Basics',
        //     'Project Management',
        //     'Communication Skills',
        //     'Data Analysis',
        //     'Sales Excellence',
        //     'Customer Support',
        //     'Compliance and Policy',
        // ];
        // $categories = [];
        // $thumbnailFiles = [
        //     'images/course-1.svg',
        //     'images/course-2.svg',
        //     'images/course-3.svg',
        //     'images/course-4.svg',
        //     'images/course-5.svg',
        //     'images/course-6.svg',
        // ];

        // foreach ($categoryNames as $name) {
        //     $slug = Str::slug($name);
        //     $thumb = $thumbnailFiles[array_search($name, $categoryNames, true) % count($thumbnailFiles)];
        //     $categories[$slug] = CourseCategory::updateOrCreate(
        //         ['slug' => $slug],
        //         ['name' => $name, 'description' => $name.' related courses', 'thumbnail' => $thumb]
        //     );
        // }

        // $creator = User::where('role', User::ROLE_SUPERADMIN)->first()
        //     ?? User::where('role', User::ROLE_ADMIN)->first();

        // if ($creator) {
        //     $courseTitles = [
        //         'Foundations',
        //         'Beginner Track',
        //         'Intermediate Workshop',
        //         'Advanced Practice',
        //         'Case Study Program',
        //     ];

        //     foreach ($categories as $categorySlug => $category) {
        //         foreach ($courseTitles as $index => $courseTitle) {
        //             $title = "{$category->name} {$courseTitle}";
        //             $thumb = $thumbnailFiles[($index + strlen($categorySlug)) % count($thumbnailFiles)];
        //             $course = Course::updateOrCreate(
        //                 ['slug' => "{$categorySlug}-".($index + 1)],
        //                 [
        //                     'category_id' => $category->id,
        //                     'title' => $title,
        //                     'description' => "{$title} dummy content for LMS testing.",
        //                     'duration_hours' => rand(2, 16),
        //                     'thumbnail' => $thumb,
        //                     'created_by' => $creator->id,
        //                 ]
        //             );

        //             for ($weekNo = 1; $weekNo <= 2; $weekNo++) {
        //                 $week = CourseWeek::updateOrCreate(
        //                     [
        //                         'course_id' => $course->id,
        //                         'week_number' => $weekNo,
        //                     ],
        //                     [
        //                         'title' => "Week {$weekNo} Learning",
        //                     ]
        //                 );

        //                 for ($sessionNo = 1; $sessionNo <= 2; $sessionNo++) {
        //                     $session = CourseSession::updateOrCreate(
        //                         [
        //                             'course_week_id' => $week->id,
        //                             'session_number' => $sessionNo,
        //                         ],
        //                         [
        //                             'title' => "Session {$sessionNo}",
        //                         ]
        //                     );

        //                     $items = [
        //                         [CourseSessionItem::TYPE_INTRO, 'Introduction Material', 'video_or_ppt'],
        //                         [CourseSessionItem::TYPE_MAIN_VIDEO, 'Main Video', 'video'],
        //                         [CourseSessionItem::TYPE_TASK, 'Task', null],
        //                         [CourseSessionItem::TYPE_QUIZ, 'Quiz', null],
        //                     ];

        //                     foreach ($items as [$type, $titlePart, $resourceType]) {
        //                         CourseSessionItem::updateOrCreate(
        //                             [
        //                                 'course_session_id' => $session->id,
        //                                 'item_type' => $type,
        //                             ],
        //                             [
        //                                 'title' => "{$titlePart} - Week {$weekNo} Session {$sessionNo}",
        //                                 'resource_type' => $resourceType,
        //                                 'content' => "Dummy {$titlePart} content for {$course->title}.",
        //                                 'resource_url' => 'https://example.com/resource/'.Str::slug($course->title)."/{$weekNo}/{$sessionNo}/{$type}",
        //                             ]
        //                         );
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        $categoryNames = [
            'AI-Integrated Digital Marketing Training Course',
            'AI-Integrated SEO Training Course',
            'AI-Integrated Graphic Designing Training Course',
            'AI-Integrated Java Training Course',
            'AI-Integrated Java Training Course',
            'AI-Integrated Java Training Course',
            'AI-Integrated Java Training Course',
            'AI-Integrated Java Training Course',
            'AI-Integrated Data Analysis Training Course',
            'AI Integrated Social Media Training Courses',
            'French Training Class',
        ];

        $thumbnailFiles = [
            'images/course-1.png',
            'images/course-2.png',
            'images/course-3.png',
            'images/course-4.png',
            'images/course-5.png',
            'images/course-6.png',
            'images/course-6.png',
            'images/course-6.png',
            'images/course-6.png',
            'images/course-6.png',
            'images/course-6.png',
        ];

        $categories = [];

        foreach ($categoryNames as $index => $name) {
            $slug = Str::slug($name);

            $categories[$slug] = CourseCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => "{$name} professional training courses",
                    'thumbnail' => $thumbnailFiles[$index % count($thumbnailFiles)],
                ]
            );
        }

        CourseType::insert([['name' => 'Basic', 'status' => 1,], ['name' => 'Professional', 'status' => 1,],]);
        CourseLevel::insert([['name' => 'Beginner', 'status' => 1,], ['name' => 'Intermediate', 'status' => 1,], ['name' => 'Advanced', 'status' => 1,],]);
        $basicType = CourseType::where('name', 'Basic')->first();
        $professionalType = CourseType::where('name', 'Professional')->first();
        $levels = CourseLevel::pluck('id', 'name');
        $creator = User::where('role', User::ROLE_SUPERADMIN)->first()
            ?? User::where('role', User::ROLE_ADMIN)->first();


        if ($creator) {

            foreach ($categories as $categorySlug => $category) {

                $courses = [
                    [
                        'title' => "{$category->name} Fundamentals",
                        'type_id' => $basicType->id,
                        'level_id' => null,
                        'duration' => rand(5, 45),
                    ],
                    [
                        'title' => "{$category->name} Beginner Professional",
                        'type_id' => $professionalType->id,
                        'level_id' => $levels['Beginner'],
                        'duration' => rand(90, 120),
                    ],
                    [
                        'title' => "{$category->name} Intermediate Professional",
                        'type_id' => $professionalType->id,
                        'level_id' => $levels['Intermediate'],
                        'duration' => rand(100, 130),
                    ],
                    [
                        'title' => "{$category->name} Advanced Professional",
                        'type_id' => $professionalType->id,
                        'level_id' => $levels['Advanced'],
                        'duration' => rand(120, 150),
                    ],
                ];


                foreach ($courses as $index => $courseData) {


                    $course = Course::updateOrCreate(
                        [
                            'slug' => Str::slug($courseData['title']),
                        ],
                        [
                            'category_id'       => $category->id,
                            'subcategory_id'    => null,
                            'course_type_id'    => $courseData['type_id'],
                            'course_level_id'   => $courseData['level_id'],

                            'title'             => $courseData['title'],
                            'short_description' => "Learn {$category->name} from industry experts with practical projects and assessments.",

                            'description'       => "
            This course covers {$category->name} concepts from fundamentals to advanced implementation.
            Includes videos, assignments, quizzes, case studies and practical exercises.
        ",

                            'language'          => 'English',
                            'thumbnail'         => $thumbnailFiles[$index % count($thumbnailFiles)],
                            'duration_hours'    => $courseData['duration'],
                            'created_by'        => $creator->id,
                        ]
                    );







                    // Create Weeks
                    for ($weekNo = 1; $weekNo <= 4; $weekNo++) {

                        $week = CourseWeek::updateOrCreate(
                            [
                                'course_id' => $course->id,
                                'week_number' => $weekNo,
                            ],
                            [
                                'title' => "Week {$weekNo} - Learning Module",
                            ]
                        );


                        // Create Sessions
                        for ($sessionNo = 1; $sessionNo <= 3; $sessionNo++) {

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
                                [
                                    CourseSessionItem::TYPE_INTRO,
                                    'Introduction & Overview',
                                    'video_or_ppt'
                                ],
                                [
                                    CourseSessionItem::TYPE_MAIN_VIDEO,
                                    'Main Learning Video',
                                    'video'
                                ],
                                [
                                    CourseSessionItem::TYPE_TASK,
                                    'Practical Assignment',
                                    null
                                ],
                                [
                                    CourseSessionItem::TYPE_QUIZ,
                                    'Knowledge Quiz',
                                    null
                                ],
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
                                        'content' => "{$titlePart} content for {$course->title}",
                                        'resource_url' =>
                                        "https://example.com/resources/"
                                            . Str::slug($course->title)
                                            . "/week-{$weekNo}/session-{$sessionNo}",
                                    ]
                                );
                            }
                        }
                    }
                    $demoVideos = [
                        [
                            'title' => 'Course Introduction',
                            'description' => "Get an overview of {$course->title} and what you will learn.",
                            'file_name' => 'course-introduction.mp4',
                            'file_path' => 'demo-feature-videos/course-introduction.mp4',
                            'file_mime' => 'video/mp4',
                            'file_size' => 25400000,
                        ],
                        [
                            'title' => 'Course Demo Session',
                            'description' => "Watch a sample learning session from {$course->title}.",
                            'file_name' => 'demo-session.mp4',
                            'file_path' => 'demo-feature-videos/demo-session.mp4',
                            'file_mime' => 'video/mp4',
                            'file_size' => 45800000,
                        ],
                        [
                            'title' => 'Student Project Showcase',
                            'description' => "See practical projects and outcomes from {$course->title}.",
                            'file_name' => 'project-showcase.mp4',
                            'file_path' => 'demo-feature-videos/project-showcase.mp4',
                            'file_mime' => 'video/mp4',
                            'file_size' => 38600000,
                        ],
                    ];

                    foreach ($demoVideos as $position => $video) {

                        DemoFeatureVideo::updateOrCreate(
                            [
                                'course_id' => $course->id,
                                'position'  => $position + 1,
                            ],
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
                    ->flatMap(fn($week) => $week->sessions)
                    ->flatMap(fn($session) => $session->items)
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
                    ->flatMap(fn($week) => $week->sessions)
                    ->flatMap(fn($session) => $session->items)
                    ->firstWhere('item_type', CourseSessionItem::TYPE_TASK);
                $quizItem = $course->weeks
                    ->flatMap(fn($week) => $week->sessions)
                    ->flatMap(fn($session) => $session->items)
                    ->firstWhere('item_type', CourseSessionItem::TYPE_QUIZ);

                if ($taskItem) {
                    $taskItem->update(['is_live' => true]);
                    $submissionPath = 'task-submissions/' . $enrollment->id . '/' . $taskItem->id . '/seed-task.txt';
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
                        'resource_url' => 'https://example.com/demo/' . Str::slug($title),
                        'ai_video_url' => 'https://example.com/demo-ai/' . Str::slug($title),
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
                        $submissionPath = 'demo-task-submissions/' . $assignment->id . '/seed-demo.txt';
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
