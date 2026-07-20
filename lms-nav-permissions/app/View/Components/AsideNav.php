<?php

namespace App\View\Components;

use App\Models\NavItem;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class AsideNav extends Component
{
    public $items;

    public function __construct()
    {
        $role = Auth::check() ? Auth::user()->role : null;

        $this->items = $role ? NavItem::forRole($role) : collect();
    }

    public function render()
    {
        return view('components.aside-nav');
    }
}
