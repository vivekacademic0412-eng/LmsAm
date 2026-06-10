<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EducationLevel;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        EducationLevel::insert([
            ['name' => '10th Pass', 'slug' => '10th'],
            ['name' => '12th Pass', 'slug' => '12th'],
            ['name' => 'Graduate', 'slug' => 'graduate'],
            ['name' => 'Post Graduate', 'slug' => 'postgraduate'],
            ['name' => 'Other', 'slug' => 'other'],
        ]);
    }
}
