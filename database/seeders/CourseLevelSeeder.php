<?php

namespace Database\Seeders;

use App\Models\CourseLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       CourseLevel::insert([ [ 'name' => 'Beginner', 'status' => 1, ], [ 'name' => 'Intermediate', 'status' => 1, ], [ 'name' => 'Advanced', 'status' => 1, ], ]);
    }
}
