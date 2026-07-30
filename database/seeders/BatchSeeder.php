<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class BatchSeeder extends Seeder
{
    public function run(): void
    {
        // Batch::truncate();

        $trainers = User::where('role', User::ROLE_TRAINER)->get();

        $admin = User::where('role', User::ROLE_SUPERADMIN)->first()
            ?? User::where('role', User::ROLE_ADMIN)->first();

        if ($trainers->isEmpty()) {
            $this->command->warn('No trainer found.');
            return;
        }

        foreach (Course::all() as $course) {

            // Create 3 batches for every course
            for ($i = 1; $i <= 3; $i++) {

                $trainer = $trainers->random();
                $batchCode = strtoupper(
                    substr(Str::slug($course->title, ''), 0, 4)
                );


                Batch::create([
                    'course_id'      => $course->id,
                    'trainer_id'     => $trainer->id,
                    'batch_code' => $batchCode
                        . '-'
                        . $course->id
                        . '-'
                        . now()->format('ym')
                        . '-'
                        . sprintf('%02d', $i),
                    'mode'           => collect([
                        'Online',
                        'Offline',
                        'Hybrid'
                    ])->random(),

                    'start_date'     => Carbon::today()->addDays($i * 7),

                    'zero_day_date'  => Carbon::today()->addDays(($i * 7) - 1),

                    'max_weeks'       => match ($course->courseType?->name) {
                        'Basic'        => 6,
                        'Professional' => 72,
                        'Diploma'      => 96,
                        'Crash Course' => 2,
                        default        => 6,
                    },

                    'status'         => 'Upcoming',

                    'created_by'     => $admin?->id,
                ]);
            }
        }
    }
}
