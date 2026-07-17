<?php

namespace App\Livewire\Demo;

use App\Mail\NewStudentRegisteredMail;
use App\Mail\StudentThankYouMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

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
            'contact'    => ['required', 'digits:10', 'regex:/^[6-9]\d{9}$/', 'unique:users,contact'],
            'email'      => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'gender'     => ['required', 'in:male,female,other'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your first name.',
            'first_name.min'      => 'First name must be at least 2 characters.',
            'first_name.max'      => 'First name may not be greater than 50 characters.',

            'last_name.required'  => 'Please enter your last name.',
            'last_name.min'       => 'Last name must be at least 2 characters.',
            'last_name.max'       => 'Last name may not be greater than 50 characters.',

            'contact.required'    => 'Please enter your contact number.',
            'contact.digits'      => 'Contact number must be exactly 10 digits.',
            'contact.regex'       => 'Please enter a valid mobile number.',
            'contact.unique'      => 'This contact number is already registered.',

            'email.required'      => 'Please enter your email address.',
            'email.email'         => 'Please enter a valid email address.',
            'email.unique'        => 'This email address is already registered.',

            'gender.required'     => 'Please select your gender.',
            'gender.in'           => 'Please select a valid gender option.',

            'password.required'   => 'Please create a password.',
            'password.min'        => 'Password must be at least 8 characters long.',
            'password.confirmed'  => 'Password confirmation does not match.',
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        $user = null;

        /* ── Step 1: create the account. DB only — no mail here.
           If anything below throws, the user row must NOT vanish
           just because an email template had a bug. ── */
        try {
            DB::beginTransaction();

            Log::info('Registration Started', [
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
                'contact'    => $this->contact,
            ]);

            $user = User::create([
                'name'      => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'contact'   => $validated['contact'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role'      => User::ROLE_STUDENT,
                'gender'    => $validated['gender'],
                'is_active' => true,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Registration Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Registration failed. Please try again.');

            return;
        }

        /* ── Step 2: emails. The account already exists at this point,
           so each send gets its own try/catch — one bad template or a
           down mail server logs an error but never undoes the signup. ── */
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Verification email failed to send', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($user->email)->send(new StudentThankYouMail($user));
        } catch (\Throwable $e) {
            Log::error('Thank-you email failed to send', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $admins = User::where('role', User::ROLE_SUPERADMIN)->get();

            if ($admins->isEmpty()) {
                $admins = User::where('role', User::ROLE_ADMIN)->get();
            }

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewStudentRegisteredMail($user));
            }
        } catch (\Throwable $e) {
            Log::error('Admin notification email failed to send', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        session()->flash(
            'success',
            'Registration successful! A verification email has been sent to '
            . $user->email .
            '. Please verify your email before logging in.'
        );

        $this->reset([
            'first_name',
            'last_name',
            'contact',
            'email',
            'password',
            'password_confirmation',
            'gender',
        ]);

        $this->success = true;
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