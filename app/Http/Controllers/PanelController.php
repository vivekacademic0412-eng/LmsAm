<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function show(Request $request, string $role): View
    {
        $user = $request->user();

        abort_unless($user && $user->role === $role, 403, 'You do not have permission to access this panel.');

        $quickActions = match ($role) {
            User::ROLE_SUPERADMIN, User::ROLE_ADMIN => [
                ['label' => 'User Control', 'route' => route('users.index')],
                ['label' => 'Enrollments', 'route' => route('enrollments.index')],
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
            ],
            User::ROLE_MANAGER_HR, User::ROLE_IT => [
                ['label' => 'Categories', 'route' => route('course-categories.index')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Dashboard', 'route' => route('dashboard')],
            ],
            User::ROLE_TRAINER => [
                ['label' => 'Trainer Tracking', 'route' => route('trainer.progress')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Dashboard', 'route' => route('dashboard')],
            ],
            User::ROLE_STUDENT => [
                ['label' => 'My Courses', 'route' => route('student.courses')],
                ['label' => 'Courses', 'route' => route('courses.index')],
                ['label' => 'Dashboard', 'route' => route('dashboard')],
            ],
            default => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
            ],
        };

        return view('panels.show', [
            'panelRole' => $role,
            'stats' => [
                'users' => User::count(),
                'categories' => CourseCategory::count(),
                'courses' => Course::count(),
            ],
            'quickActions' => $quickActions,
        ]);
    }
}
