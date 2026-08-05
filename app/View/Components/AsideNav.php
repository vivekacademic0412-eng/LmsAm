<?php

namespace App\View\Components;

use App\Models\Module;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class AsideNav extends Component
{
    public $categories;
    public $user;
    public $avatarUrl;
    public $initials;
    public $roleLabels;

    public function __construct()
    {
        $this->user = Auth::user();

        $this->categories = collect();

        if ($this->user) {

            $categories = Module::treeForRole($this->user->role);

            // Check if student has any enrolled course
            $isEnrolled = $this->user->role === 'student'
                ? $this->user->enrollments()->exists()
                : false;

            $this->categories = $categories->map(function ($category) use ($isEnrolled) {

                $modules = $category->modules->map(function ($module) use ($isEnrolled) {

                    $children = $module->children->filter(function ($module) use ($isEnrolled) {

                      

                        return match ($module->condition ?? 'always') {
                            'always'       => true,
                            'enrolled'     => $isEnrolled,
                            'not_enrolled' => !$isEnrolled,
                            default        => true,
                        };
                    });

                    $module->setRelation('children', $children);

                    return $module;
                })->filter(function ($module) use ($isEnrolled) {

                  

                    return match ($module->condition ?? 'always') {
                        'always'       => true,
                        'enrolled'     => $isEnrolled,
                        'not_enrolled' => !$isEnrolled,
                        default        => true,
                    };
                });

                $category->setRelation('modules', $modules);

                return $category;
            })->filter(fn($category) => $category->modules->isNotEmpty());
        }

        $this->avatarUrl = $this->user?->avatar
            ? asset('storage/' . $this->user->avatar)
            : null;

        $this->initials = $this->user
            ? collect(preg_split('/\s+/', trim($this->user->name)))
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->implode('')
            : '';

        $this->roleLabels = [
            'superadmin' => 'Super Admin',
            'admin'      => 'Admin',
            'manager_hr' => 'HR Manager',
            'it'         => 'IT',
            'trainer'    => 'Trainer',
            'student'    => 'Student',
        ];
    }
    public function render()
    {
        return view('components.aside-nav');
    }
}
