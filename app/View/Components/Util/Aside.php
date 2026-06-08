<?php

namespace App\View\Components\Util;

use App\Models\User;
use Illuminate\View\Component;

class Aside extends Component
{
    public User $user;
    public array $roleLabels;
    public string $initials;
    public ?string $avatarUrl;
    public array $menu;

    public function __construct()
    {
        $this->user = auth()->user();

        $this->roleLabels = User::roleOptions();

        $this->avatarUrl = $this->user->avatar_url;

        $nameParts = explode(' ', trim($this->user->name));

        $this->initials = strtoupper(
            substr($nameParts[0] ?? '', 0, 1) .
            substr(end($nameParts) ?: '', 0, 1)
        );
   
 $menus = [
            User::ROLE_STUDENT => [
                ['icon' => 'fa-solid fa-house', 'label' => 'Dashboard', 'route' => 'dashboard'],
                ['icon' => 'fa-solid fa-book-open', 'label' => 'My Courses', 'route' => 'student.courses'],
                ['icon' => 'fa-solid fa-clock-rotate-left', 'label' => 'History', 'route' => 'student.history'],
                ['icon' => 'fa-solid fa-certificate', 'label' => 'Certificates', 'route' => 'student.certificates'],
                ['icon' => 'fa-solid fa-user', 'label' => 'My Profile', 'route' => 'profile.edit'],
            ],

            User::ROLE_DEMO => [
                ['icon' => 'fa-solid fa-house', 'label' => 'Dashboard', 'route' => 'dashboard'],
            ],

            User::ROLE_TRAINER => [
                ['icon' => 'fa-solid fa-house', 'label' => 'Dashboard', 'route' => 'dashboard'],
                ['icon' => 'fa-solid fa-list-check', 'label' => 'Review Queue', 'route' => 'trainer.submissions'],
                ['icon' => 'fa-solid fa-book', 'label' => 'All Courses', 'route' => 'trainer.courses'],
                ['icon' => 'fa-solid fa-users', 'label' => 'Assigned Students', 'route' => 'trainer.assigned-students'],
                ['icon' => 'fa-solid fa-chart-line', 'label' => 'Trainer Tracking', 'route' => 'trainer.progress'],
                ['icon' => 'fa-solid fa-user', 'label' => 'My Profile', 'route' => 'profile.edit'],
            ],

            'admin' => [
                ['icon' => 'fa-solid fa-house', 'label' => 'Dashboard', 'route' => 'dashboard'],
                ['icon' => 'fa-solid fa-layer-group', 'label' => 'Course Categories', 'route' => 'course-categories.index'],
                ['icon' => 'fa-solid fa-book-open', 'label' => 'Courses', 'route' => 'courses.index'],
                ['icon' => 'fa-solid fa-user-graduate', 'label' => 'Enrollments', 'route' => 'enrollments.index'],
                ['icon' => 'fa-solid fa-file-circle-check', 'label' => 'Submission Review', 'route' => 'submissions.index'],
                ['icon' => 'fa-solid fa-chart-column', 'label' => 'Activity Logs', 'route' => 'activity-logs.index'],
                ['icon' => 'fa-solid fa-users-gear', 'label' => 'User Control', 'route' => 'users.index'],
                ['icon' => 'fa-solid fa-bullhorn', 'label' => 'Broadcast Notifications', 'route' => 'broadcast-notifications.index'],
                ['icon' => 'fa-solid fa-desktop', 'label' => 'Demo Tasks', 'route' => 'demo-tasks.create-page'],
                ['icon' => 'fa-solid fa-video', 'label' => 'Demo Videos', 'route' => 'demo-feature-video.index'],
                ['icon' => 'fa-solid fa-star', 'label' => 'Reviews', 'route' => 'demo-review-videos.index'],
                ['icon' => 'fa-solid fa-user', 'label' => 'My Profile', 'route' => 'profile.edit'],
            ]
        ];
 
        $this->menu = $menus[$this->user->role] ?? $menus['admin'];
        
    }

    public function render()
    {
        return view('components.util.aside');
    }
}