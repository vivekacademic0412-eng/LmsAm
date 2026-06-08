<?php

namespace App\View\Components\Util;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Schema;

class Main extends Component
{
    public $user;
    public $roleLabels;
    public $avatarUrl;
    public $initials;

    public $topbarNotifications;
    public $topbarUnreadCount;
    public $topbarUnreadNotifications;
    public $topbarSeenNotifications;

    public function __construct()
    {
        $this->user = auth()->user();

        $this->roleLabels = \App\Models\User::roleOptions();

        $this->avatarUrl = $this->user->avatar_url;

        $this->initials = strtoupper(
            substr($this->user->name, 0, 1) .
            substr(strrchr(' '.$this->user->name, ' '), 1, 1)
        );

        $this->topbarNotifications = collect();
        $this->topbarUnreadCount = 0;
        $this->topbarUnreadNotifications = collect();
        $this->topbarSeenNotifications = collect();

        if (Schema::hasTable('notifications')) {

            $this->topbarNotifications = $this->user
                ->notifications()
                ->latest()
                ->take(6)
                ->get();

            $this->topbarUnreadCount = $this->user
                ->unreadNotifications()
                ->count();

            $this->topbarUnreadNotifications = $this->topbarNotifications
                ->filter(fn ($notification) => is_null($notification->read_at))
                ->values();

            $this->topbarSeenNotifications = $this->topbarNotifications
                ->reject(fn ($notification) => is_null($notification->read_at))
                ->values();
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.util.main');
    }
}