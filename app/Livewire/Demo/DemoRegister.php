<?php

namespace App\Livewire\Demo;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class DemoRegister extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $gender = '';
    public bool $success = false;

    protected function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'min:2', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'gender'                => ['required', 'in:male,female,other'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'              => 'Your full name is required.',
            'name.min'                   => 'Name must be at least 2 characters.',
            'email.required'             => 'Email address is required.',
            'email.email'                => 'Please enter a valid email address.',
            'email.unique'               => 'This email is already registered.',
            'password.required'          => 'Password is required.',
            'password.min'               => 'Password must be at least 8 characters.',
            'password.confirmed'         => 'Passwords do not match.',
            'gender.required'            => 'Please select your gender.',
            'gender.in'                  => 'Please select a valid gender option.',
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => User::ROLE_STUDENT,
            // 'gender'    => $validated['gender'],
            'is_active' => true,
        ]);
 Auth::login($user);
        // Dispatch JS event so the blade can trigger SweetAlert
        $this->dispatch('registration-success', name: $user->name);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'gender']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.demo.demo-register');
    }
}