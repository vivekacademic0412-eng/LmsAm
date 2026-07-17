<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $done = false;

    public function mount(string $token, ?string $email = null): void
    {
        $this->token = $token;
        $this->email = $email ?? request()->query('email', '');
    }

    protected function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required'     => 'Your email address is missing from the reset link.',
            'email.email'        => 'Please enter a valid email address.',
            'password.required'  => 'Please create a new password.',
            'password.min'       => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordResetEvent($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->done = true;
            $this->reset(['password', 'password_confirmation']);

            return;
        }

        // Covers: expired token, invalid token, unknown email — all
        // surfaced as one field error rather than leaking which case it was.
        $this->addError('email', match ($status) {
            Password::INVALID_TOKEN => 'This reset link is invalid or has expired. Please request a new one.',
            Password::INVALID_USER  => 'We could not find an account for that email address.',
            default                 => 'Something went wrong. Please request a new reset link.',
        });
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
