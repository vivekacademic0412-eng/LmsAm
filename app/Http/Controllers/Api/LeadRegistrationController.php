<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\StudentThankYouMail;
use App\Models\TrafficSource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;
 

class LeadRegistrationController extends Controller
{
     protected const TRACKS = [
        'AI-Integrated Digital Marketing',
        'SEO',
        'Content Writing',
        'Graphic Design',
        'HR Operations',
        'Java · Angular · Android',
    ];
 
    public function __invoke(Request $request)
    {
        // ---------------------------------------------------------------
        // VALIDATION
        // ---------------------------------------------------------------
        // - email:rfc,dns,spoof catches malformed addresses, non-existent
        //   domains, and homograph/spoofing tricks — the biggest source of
        //   junk leads. Drop ",dns" if your queue workers run somewhere
        //   without reliable outbound DNS, since it'll reject valid emails
        //   on a DNS hiccup.
        // - phone forced to exactly 10 digits, same rule as the Livewire
        //   form, so the API can't be used to bypass the UI's constraint.
        // - track locked to the known list instead of accepting any string.
        // - "website" is a honeypot: a real user never sees or fills this
        //   field (hide it with CSS in the form, don't just omit it), bots
        //   that auto-fill every input will populate it and get silently
        //   rejected below.
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s\.\'-]+$/u'],
            'email'   => ['required', 'string', 'email:rfc,dns,spoof', 'max:150'],
            'phone'   => ['required', 'digits:10'],
            'track'   => ['required', 'string', 'in:' . implode(',', self::TRACKS)],
            'source'  => ['nullable', 'string', 'max:100'],
            'website' => ['prohibited'], // honeypot — must stay empty
        ], [
            'name.regex'        => 'Please enter a valid name.',
            'phone.digits'      => 'Enter a valid 10-digit mobile number.',
            'website.prohibited' => 'Invalid submission.',
        ]);
 
        // Normalize so "Test@Gmail.com" and "test@gmail.com" are the same
        // account. Do this once, use the normalized value everywhere below.
        $email = Str::lower(trim($validated['email']));
 
        try {
            $result = DB::transaction(function () use ($request, $validated, $email) {
 
                // Traffic source is independent of the user outcome — always
                // record it, even for an "already registered" hit, so
                // marketing attribution stays complete.
                TrafficSource::create(
                    TrafficSource::attributesFromRequest($request)
                );
 
                $user = User::whereEmail($email)->first();
 
                if ($user) {
                    return ['outcome' => 'existing', 'user' => $user];
                }
 
                $password = Str::random(10);
 
                try {
                    $user = User::create([
                        'name'      => $validated['name'],
                        'email'     => $email,
                        'contact'   => $validated['phone'],
                        'password'  => Hash::make($password),
                        'role'      => User::ROLE_STUDENT,
                        'is_active' => true,
                    ]);
                } catch (QueryException $e) {
                    // Race condition guard: two requests for the same email
                    // arrived close enough together that both passed the
                    // whereEmail() check above. Requires a UNIQUE index on
                    // users.email (add one via migration if missing —
                    // without it this catch can't help you, and you'd get
                    // silent duplicate accounts instead).
                    if ((int) $e->getCode() === 23000) {
                        return ['outcome' => 'existing', 'user' => User::whereEmail($email)->first()];
                    }
                    throw $e;
                }
 
                return ['outcome' => 'created', 'user' => $user, 'password' => $password];
            });
 
            $user = $result['user'];
 
            // ---------------------------------------------------------------
            // OUTCOME HANDLING — mail is sent OUTSIDE the transaction that's
            // already committed by this point. If the mail server is down,
            // the account still exists; we just log the failure instead of
            // rolling back a successful registration.
            // ---------------------------------------------------------------
            if ($result['outcome'] === 'created') {
                $this->sendVerificationEmail($user, $result['password']);
 
                return response()->json([
                    'success' => true,
                    'type'    => 'registered',
                    'user_id' => $user->id,
                    'message' => 'Registration successful. Please check your email and verify your account before logging in.',
                ], 201);
            }
 
            // Existing account.
            if (!$user->hasVerifiedEmail()) {
                $this->sendVerificationEmail($user, null);
 
                return response()->json([
                    'success' => true,
                    'type'    => 'verification_resent',
                    'user_id' => $user->id,
                    'message' => 'An account with this email already exists but is not verified. We\'ve sent a new verification email — please check your inbox.',
                ], 200);
            }
 
            return response()->json([
                'success' => false,
                'type'    => 'already_registered',
                'message' => 'An account with this email already exists and is verified. Please log in, or use Forgot Password if you can\'t access it.',
            ], 409);
 
        } catch (Throwable $e) {
            // Log the real error for developers, but never leak file/line/
            // stack details to the client — that's information disclosure.
            Log::error('Landing registration failed', [
                'email' => $email ?? null,
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
 
            return response()->json([
                'success' => false,
                'type'    => 'error',
                'message' => app()->environment('local')
                    ? $e->getMessage()
                    : 'Something went wrong while processing your registration. Please try again in a moment.',
            ], 500);
        }
    }
 
    protected function sendVerificationEmail(User $user, ?string $password): void
    {
        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addDays(7),
                [
                    'id'   => $user->id,
                    'hash' => sha1($user->email),
                ]
            );
 
            Mail::to($user)->send(
                new StudentThankYouMail($user, $verificationUrl, $password)
            );
        } catch (Throwable $e) {
            // A failed send should never fail the registration response —
            // log it and let leads:sync-lms / a scheduled retry pick it up.
            Log::error('Verification email failed to send', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
