<?php

namespace App\Livewire\Demo;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DemoLogin extends Component
{
    public string $login = '';       // email OR 10-digit contact number
    public string $password = '';
    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'login.required'    => 'Please enter your email or mobile number.',
            'password.required' => 'Please enter your password.',
        ];
    }

    public function authenticate()
{
    $this->validate();

    $field = filter_var($this->login, FILTER_VALIDATE_EMAIL)
        ? 'email'
        : 'contact';

    if (Auth::attempt([
        $field => $this->login,
        'password' => $this->password,
    ], $this->remember)) {

        request()->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {

            Auth::logout();

            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Email Verification Required',
                'text'  => 'Please verify your email address before logging in.',
            ]);

            return;
        }

        Log::info('User logged in', [
            'user_id' => $user->id,
        ]);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Login Successful',
            'text'  => 'Welcome back!',
        ]);

        $this->redirect('/dashboard', navigate: true);

        return;
    }

    $this->dispatch('swal', [
        'icon'  => 'error',
        'title' => 'Login Failed',
        'text'  => 'Invalid email/mobile number or password.',
    ]);
}
    public function render()
    {
        return view('livewire.demo.demo-login');
    }
}
