<?php

namespace App\Livewire;

use App\Models\SubmittedDemos;
use Livewire\Component;

class UserDemoDetail extends Component
{
    public ?SubmittedDemos $demo = null;
    
    public function mount()
    {
        $this->demo = SubmittedDemos::with(['course', 'demoUser', 'user'])
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->first();

        abort_if(! $this->demo, 404, 'No demo submission found for your account.');
    }

  public function getPersonProperty()
{
    return $this->demo->demoUser ?? $this->demo->user;
}
    public function render()
    {
        return view('livewire.user-demo-detail');
    }
}