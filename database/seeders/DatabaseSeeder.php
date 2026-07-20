<?php

namespace Database\Seeders;
use App\Models\Country;
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
use Carbon\Carbon;
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
            // CourseLevelSeeder::class,
            ModuleSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            EducationLevelSeeder::class,
            HeroSectionSeeder::class,
             WeekelyLevelSeeder::class,
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
                   'email_verified_at' => Carbon::now(),
                ]
            );
            if (! $user->avatar) {
                $user->avatar = $avatarFiles[$index % count($avatarFiles)];
                $user->save();
            }
        }

        $bulkUsers = [
            User::ROLE_ADMIN => 5,
            User::ROLE_MANAGER_HR => 2,
            User::ROLE_IT => 1,
            User::ROLE_TRAINER => 4,
            // User::ROLE_STUDENT => 40,
            // User::ROLE_DEMO => 6,
        ];

    //     foreach ($bulkUsers as $role => $count) {
    // for ($i = 1; $i <= $count; $i++) {

    //     $user = User::updateOrCreate(
    //         ['email' => "{$role}{$i}@lms.test"],
    //         [
    //             'name'      => ucwords(str_replace('_', ' ', $role)) . " {$i}",
    //             'role'      => $role,
    //             'is_active' => $faker->boolean(92),
    //             'password'  => $password,
    //             'contact'   => $faker->numerify('98########'),
    //             'gender'    => $faker->randomElement(['male', 'female', 'other']),
    //         ]
    //     );

    //     if (! $user->avatar) {
    //         $user->avatar = $avatarFiles[($i + strlen($role)) % count($avatarFiles)];
    //         $user->save();
    //     }
//    }

      

        
        
        // }
    }
}
