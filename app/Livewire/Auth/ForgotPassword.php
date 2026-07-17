<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        /* ── Rate limit: 3 requests per 10 minutes ── */
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many requests. Please try again in {$seconds} seconds.");

            return;
        }

        RateLimiter::hit($throttleKey, 600);

        // Deliberately ignore the returned status string here — showing a
        // different message for "email not found" vs "link sent" lets an
        // attacker enumerate registered accounts. Always show success.
        Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
