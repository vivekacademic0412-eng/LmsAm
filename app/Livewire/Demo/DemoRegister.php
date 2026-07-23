<?php

namespace App\Livewire\Demo;

use App\Mail\StudentThankYouMail;
use App\Models\TrafficSource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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

    // Success-screen state
    public bool $success = false;
    public ?int $registeredUserId = null;
    public string $registeredName = '';
    public string $registeredEmail = '';

    // Resend-verification UI state
    public bool $resendSent = false;
    public string $resendMessage = '';
    public int $resendCooldown = 0;

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

    /**
     * Builds the same signed URL Laravel's default VerifyEmail notification
     * would generate, so the existing `verification.verify` route keeps working.
     */
    private function buildVerificationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    public function register()
    {
        $validated = $this->validate();

        $user = null;

        try {

            DB::beginTransaction();

            Log::info('Registration Started', [
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
                'contact'    => $this->contact,
            ]);
            $request = request();

            try {

                $attributes = TrafficSource::attributesFromRequest($request);

                $traffic = TrafficSource::create($attributes);

                session()->put('traffic_source_id', $traffic->id);

                Log::info('Traffic source captured', [
                    'traffic_source_id' => $traffic->id,
                    'source' => $traffic->source,
                ]);
            } catch (\Throwable $e) {

                Log::error('Traffic tracking failed', [
                    'message' => $e->getMessage(),
                ]);
            }

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
if ($traffic) {
    $traffic->update([
        'demo_user_id' => $user->id,
    ]);
}
            DB::commit();

            Log::info('User Registered Successfully', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Registration Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Registration Failed',
                'text'  => app()->isLocal()
                    ? $e->getMessage()
                    : 'Unable to create your account. Please try again.',
            ]);

            return;
        }

        // Send verification email
        try {

            $verificationUrl = $this->buildVerificationUrl($user);

            Mail::to($user->email)->send(
                new StudentThankYouMail($user, $verificationUrl)
            );

            Log::info('Verification email sent.', [
                'user_id' => $user->id,
            ]);
        } catch (\Throwable $e) {

            Log::error('Verification Email Failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Email Not Sent',
                'text'  => 'Account created successfully, but verification email could not be sent.',
            ]);
        }

        // Success state
        $this->registeredUserId = $user->id;
        $this->registeredName   = $user->name;
        $this->registeredEmail  = $user->email;
        $this->success          = true;

        // Success SweetAlert
        $this->dispatch('registration-success', [
            'name' => $user->name,
        ]);

        $this->reset([
            'first_name',
            'last_name',
            'contact',
            'email',
            'password',
            'password_confirmation',
            'gender',
        ]);
    }

    /**
     * Resends the same combined welcome/verification email.
     * Rate-limited to 1 send per 60 seconds per user to prevent abuse.
     */
    // public function resendVerification()
    // {
    //     if (! $this->registeredUserId) {
    //         return;
    //     }

    //     $key = 'resend-verification:' . $this->registeredUserId;

    //     if (RateLimiter::tooManyAttempts($key, 1)) {
    //         $this->resendCooldown = RateLimiter::availableIn($key);
    //         $this->resendMessage  = 'Please wait before requesting another email.';
    //         $this->resendSent     = false;
    //         return;
    //     }

    //     $user = User::find($this->registeredUserId);

    //     if (! $user) {
    //         $this->resendMessage = 'We could not find your account. Please refresh and try again.';
    //         return;
    //     }

    //     if ($user->hasVerifiedEmail()) {
    //         $this->resendMessage = 'Your email is already verified — you can log in now.';
    //         $this->resendSent    = true;
    //         return;
    //     }

    //     try {
    //         $verificationUrl = $this->buildVerificationUrl($user);

    //         Mail::to($user->email)->send(
    //             new StudentWelcomeVerifyMail($user, $verificationUrl)
    //         );

    //         RateLimiter::hit($key, 60);

    //         $this->resendSent     = true;
    //         $this->resendCooldown = 60;
    //         $this->resendMessage  = 'Verification email resent! Please check your inbox.';
    //     } catch (\Throwable $e) {
    //         Log::error('Resend verification email failed', [
    //             'user_id' => $user->id,
    //             'message' => $e->getMessage(),
    //         ]);

    //         $this->resendMessage = 'Something went wrong sending the email. Please try again shortly.';
    //     }
    // }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function render()
    {
        return view('livewire.demo.demo-register');
    }
}
