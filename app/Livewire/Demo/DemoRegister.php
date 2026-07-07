<?php

namespace App\Livewire\Demo;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
 use Illuminate\Support\Facades\Log;

class DemoRegister extends Component
{
   public string $first_name = '';
public string $last_name = '';
public string $contact = '';
public string $email = '';
public string $password = '';
public string $password_confirmation = '';
public string $gender = '';
public bool $success = false;

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],

            'last_name'  => ['required', 'string', 'min:2', 'max:50'],

            'contact'    => [
                'required',
                'digits:10',
                'regex:/^[6-9]\d{9}$/',
                'unique:users,contact'
            ],

            'email'      => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:users,email'
            ],

            'gender'     => ['required', 'in:male,female,other'],

            'password'   => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
        ];
    }

   protected function messages(): array
{
    return [
        // First Name
        'first_name.required' => 'Please enter your first name.',
        'first_name.min'      => 'First name must be at least 2 characters.',
        'first_name.max'      => 'First name may not be greater than 50 characters.',

        // Last Name
        'last_name.required'  => 'Please enter your last name.',
        'last_name.min'       => 'Last name must be at least 2 characters.',
        'last_name.max'       => 'Last name may not be greater than 50 characters.',

        // Contact Number
        'contact.required'    => 'Please enter your contact number.',
        'contact.digits'      => 'Contact number must be exactly 10 digits.',
        'contact.regex'       => 'Please enter a valid mobile number.',
        'contact.unique'      => 'This contact number is already registered.',

        // Email
        'email.required'      => 'Please enter your email address.',
        'email.email'         => 'Please enter a valid email address.',
        'email.unique'        => 'This email address is already registered.',

        // Gender
        'gender.required'     => 'Please select your gender.',
        'gender.in'           => 'Please select a valid gender option.',

        // Password
        'password.required'   => 'Please create a password.',
        'password.min'        => 'Password must be at least 8 characters long.',
        'password.confirmed'  => 'Password confirmation does not match.',
    ];
}


public function register()
{
    try {

        Log::info('Registration Started', [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'contact' => $this->contact,
        ]);

        $validated = $this->validate();

        Log::info('Validation Passed', $validated);

        $user = User::create([
            'name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'contact'    => $validated['contact'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => User::ROLE_STUDENT,
            'gender'     => $validated['gender'],
            'is_active'  => true,
        ]);

        Log::info('User Created Successfully', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        Auth::login($user);

        $this->dispatch(
            'registration-success',
            name: $user->first_name . ' ' . $user->last_name
        );

        $this->reset([
            'first_name',
            'last_name',
            'contact',
            'email',
            'password',
            'password_confirmation',
            'gender'
        ]);

        $this->success = true;
    } catch (\Exception $e) {

        Log::error('Registration Failed', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        session()->flash('error', $e->getMessage());

        // Temporary debug
        // dd($e->getMessage());
    }
}
public function updated($property)
{
    $this->validateOnly($property);
}
    public function render()
    {
        return view('livewire.demo.demo-register');
    }
}
