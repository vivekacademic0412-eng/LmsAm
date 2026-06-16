<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseType::insert([ [ 'name' => 'Basic', 'status' => 1, ], [ 'name' => 'Professional', 'status' => 1, ], ]);
    }
}
