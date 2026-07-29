<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\StudentThankYouMail;
use App\Models\Lead;
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
        // LOG EVERYTHING THAT HITS THIS ENDPOINT — raw input, before any
        // validation drops or rejects fields. This is the first thing
        // that runs so even a validation failure still gets logged.
        // ---------------------------------------------------------------
        Log::info('Landing API request received', [
            'ip'      => $request->ip(),
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'headers' => [
                'content-type' => $request->header('Content-Type'),
                'accept'       => $request->header('Accept'),
                'user-agent'   => $request->userAgent(),
                'referer'      => $request->header('referer'),
            ],
            'all_input' => $request->all(), // everything: query + body + json
        ]);

        // ---------------------------------------------------------------
        // VALIDATION
        // ---------------------------------------------------------------
        try {
            $validated = $request->validate([
                'name'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s\.\'-]+$/u'],
                'email'   => ['required', 'string', 'email:rfc,dns,spoof', 'max:150'],
                'phone'   => ['required', 'digits:10'],
                'track'   => ['required', 'string', 'in:' . implode(',', self::TRACKS)],
                'source'  => ['nullable', 'string', 'max:100'],
                'website' => ['prohibited'], // honeypot — must stay empty
            ], [
                'name.regex'          => 'Please enter a valid name.',
                'phone.digits'        => 'Enter a valid 10-digit mobile number.',
                'website.prohibited'  => 'Invalid submission.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation failures explicitly — this is a very common
            // reason "nothing got saved": the request never made it past
            // this point, and previously you'd have no record of why.
            Log::warning('Landing API validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);
            throw $e; // let Laravel return its normal 422 response
        }

        $email = Str::lower(trim($validated['email']));

        try {
            $result = DB::transaction(function () use ($request, $validated, $email) {

                $attributes = TrafficSource::attributesFromRequestNew($request);

                // Log exactly what will be inserted into traffic_sources —
                // if this array is missing a field or has an unexpected
                // null, you'll see it here before the DB call even runs.
                Log::info('TrafficSource attributes resolved', $attributes);
                $trafficSource = TrafficSource::create($attributes);
                Log::info('TrafficSource created', ['id' => $trafficSource->id]);
                $user = Lead::whereEmail($email)->first();
                $registrationOutcome = 'existing';
                $generatedPassword = null;
                if (!$user) {
                    try {

                        $user = Lead::create([
                            'lead_type'         => 'campanion',
                            'name'              => $validated['contact_name'],
                            'email'             => $email,
                            'phone'             => $validated['phone'],
                            'designation'       => $validated['designation'] ?? null,
                            'traffic_source_id' => $trafficSource->id,
                            'status'            => 'New',
                            'email_verified_at' => null,
                        ]);

                        $registrationOutcome = 'created';
                    } catch (QueryException $e) {

                        if ((int)$e->getCode() === 23000) {

                            $user = Lead::whereEmail($email)->first();

                            $registrationOutcome = 'existing';
                        } else {
                            throw $e;
                        }
                    }
                }

                $trafficSource->update([
                    'lead_id'      => $user->id,
                    'lead_type'    => 'lab',
                ]);

                Log::info('New user + TrafficSource created and linked', [
                    'user_id'           => $user->id,
                    'traffic_source_id' => $trafficSource->id,
                ]);

                return ['outcome' => 'created', 'user' => $user, ];
            });

            $user = $result['user'];

            if ($result['outcome'] === 'created') {
                $this->sendVerificationEmail($user, null);

                return response()->json([
                    'success' => true,
                    'type'    => 'registered',
                    'user_id' => $user->id,
                    'message' => 'Registration successful. Please check your email and verify your account before logging in.',
                ], 201);
            }

           if ($user && !$user->email_verified_at) {
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
            // Full detail in the log, sanitized message to the client.
            Log::error('Landing registration failed', [
                'email'   => $email ?? null,
                'input'   => $request->all(),
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
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
                    'type' => 'landing', // or lms
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
