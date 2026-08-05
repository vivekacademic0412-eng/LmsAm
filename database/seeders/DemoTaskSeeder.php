<?php

namespace Database\Seeders;

use App\Models\DemoTask;
use App\Models\DemoTaskAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Get Admin (Task Creator)
        $admin = User::first();

        // Create Demo Task
        $task = DemoTask::create([
            'title' => 'Task 1: HTML Basics - Student Registration Form',
            'description' => <<<TEXT
Objective:
Create a simple Student Registration Form using only HTML.

----------------------------------------
Requirements
----------------------------------------

Create a file named:
index.html

Design a Student Registration Form with the following fields:

• Student Name
• Father's Name
• Email Address
• Mobile Number
• Gender (Male / Female)
• Date of Birth
• Course (Dropdown)
• Address (Textarea)
• Submit Button
• Reset Button

----------------------------------------
Instructions
----------------------------------------

1. Use only HTML.
2. Use proper labels for every field.
3. Use appropriate input types.
4. Use a dropdown for Course.
5. Use radio buttons for Gender.
6. Use a textarea for Address.
7. All fields should be required.

----------------------------------------
Learning Outcomes
----------------------------------------

• HTML Forms
• Input Types
• Labels
• Select
• Textarea
• Buttons

----------------------------------------
Submission

Upload:

1. index.html
2. Screenshot of your form

Best of Luck!
TEXT,
            'created_by' => $admin?->id,
        ]);

        // Assign Task to All Students
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            DemoTaskAssignment::create([
                'demo_task_id' => $task->id,
                'user_id' => $student->id,
                'assigned_by' => $admin?->id,
                'assigned_at' => Carbon::now(),
            ]);
        }
    }
}