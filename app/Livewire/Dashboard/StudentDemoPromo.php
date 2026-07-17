<?php

namespace App\Livewire\Dashboard;

use App\Models\SubmittedDemos;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDemoPromo extends Component
{
    /**
     * Fixed demo enrollment fee. Move this to config/services.php
     * (e.g. config('academy.demo_fee')) once you wire up payments.
     */
    public const DEMO_FEE = 999;

    public bool $hasDemo = false;

    public ?SubmittedDemos $demo = null;

    public function mount(): void
    {
        $this->demo = SubmittedDemos::query()
            ->with('course')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->first();

        $this->hasDemo = (bool) $this->demo;
    }

    public function render()
    {
        return view('livewire.dashboard.student-demo-promo', [
            'demoFee' => self::DEMO_FEE,
        ]);
    }
}