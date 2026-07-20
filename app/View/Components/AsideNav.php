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

        // Already filtered to only what this role can_view — nothing further to check in the view.
        $this->categories = $this->user
            ? Module::treeForRole($this->user->role)
            : collect();

        $this->avatarUrl = $this->user?->avatar
            ? asset('storage/' . $this->user->avatar)
            : null;

        $this->initials = $this->user
            ? collect(preg_split('/\s+/', trim($this->user->name)))
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
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
            'demo'       => 'Demo User',
        ];
    }

    public function render()
    {
        return view('components.aside-nav');
    }
}