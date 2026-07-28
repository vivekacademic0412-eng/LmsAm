<?php

namespace App\Livewire\Student;

use App\Models\Payment;
use Livewire\Component;

class PaymentSuccess extends Component
{
    public Payment $link;

    public function mount(string $token): void
    {
        $this->link = Payment::with(['user', 'category', 'course', ])
            ->where('token', $token)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.student.payment-success');
    }
}