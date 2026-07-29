<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabSetupForm;
use App\Models\Lead;
use App\Models\TrafficSource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class LabSetupFormController extends Controller
{
    /**
     * POST /api/lab-setup-forms
     *
     * Validates the Discovery Form, finds-or-creates the submitting user
     * (sending a verification email for new/unverified accounts, same as
     * the general landing registration flow), then stores the lab data
     * against that user + the resolved traffic source.
     */
    public function store(Request $request): JsonResponse
    {
        // -----------------------------------------------------------
        // LOG EVERYTHING THAT HITS THIS ENDPOINT — raw input, before
        // any validation drops or rejects fields.
        // -----------------------------------------------------------
        Log::info('Lab setup form request received', [
            'ip'      => $request->ip(),
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'headers' => [
                'content-type' => $request->header('Content-Type'),
                'accept'       => $request->header('Accept'),
                'user-agent'   => $request->userAgent(),
                'referer'      => $request->header('referer'),
            ],
            'all_input' => $request->all(),
        ]);

        // -----------------------------------------------------------
        // VALIDATION
        // -----------------------------------------------------------
        try {
            $validated = $request->validate([
                'school_name'            => 'nullable|string|max:255',
                'board_affiliation'      => 'nullable|string|max:255',
                'address'                => 'nullable|string|max:500',
                'grades_offered'         => 'nullable|string|max:255',
                'student_strength'       => 'nullable|string|max:50',

                'existing_lab'           => 'nullable|in:dedicated_room,shared_partial,new_room',
                'room_size'              => 'nullable|string|max:100',
                'seating_capacity'       => 'nullable|string|max:100',
                'furniture'              => 'nullable|array',
                'furniture.*'            => 'string|in:work_tables,lockable_storage,display_board',

                'power_points'           => 'nullable|string|max:100',
                'backup_power'           => 'nullable|string|max:100',
                'internet_availability'  => 'nullable|in:wifi,lan,not_yet',
                'internet_speed'         => 'nullable|string|max:100',
                'school_devices'         => 'nullable|string|max:255',

                'enroll_grades'          => 'nullable|string|max:255',
                'expected_students'      => 'nullable|string|max:50',
                'session_frequency'      => 'nullable|in:weekly,twice_weekly,flexible',

                'start_date'             => 'nullable|string|max:100',
                'annual_budget'          => 'nullable|string|max:100',
                'procurement_process'    => 'nullable|in:principal,trust,tender',
                'lab_goals'              => 'nullable|string',

                'contact_name'           => ['required', 'string', 'max:255', 'regex:/^[\pL\s\.\'-]+$/u'],
                'designation'            => 'nullable|string|max:255',
                'phone'                  => ['required', 'regex:/^(?:\+91|91)?[6-9]\d{9}$/'],
                'email'                  => ['required', 'string', 'email:rfc,dns,spoof', 'max:255'],

                'signature'              => 'nullable|string|max:255',
                'sig_date'               => 'nullable|date',

                'source'                 => 'nullable|string|max:100',
                'website'                => ['prohibited'], // honeypot — must stay empty
            ], [
                'contact_name.regex' => 'Please enter a valid name.',
                'phone.regex'        => 'Enter a valid 10-digit mobile number.',
                'website.prohibited' => 'Invalid submission.',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Lab setup form validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);
            throw $e; // let Laravel return its normal 422 response
        }

        $email = Str::lower(trim($validated['email']));

        try {
            $result = DB::transaction(function () use ($request, $validated, $email) {

                // -----------------------------------------------------
                // Traffic source — same resolver used by the general
                // landing API, so lab-form leads land in the same table.
                // -----------------------------------------------------
                $attributes = TrafficSource::attributesFromRequestNew($request);
                Log::info('TrafficSource attributes resolved', $attributes);

                $trafficSource = TrafficSource::create($attributes);
                Log::info('TrafficSource created', ['id' => $trafficSource->id]);

                // -----------------------------------------------------
                // Find-or-create the user tied to this submission.
                // -----------------------------------------------------
                $user = Lead::whereEmail($email)->first();
                $registrationOutcome = 'existing';
                $generatedPassword = null;
                if (!$user) {

                   

                    try {

                        $user = Lead::create([
                            'lead_type'         => 'lab',
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
                Log::info('TrafficSource linked to user', [
                    'traffic_source_id' => $trafficSource->id,
                    'lead_id'           => $user->id,
                    'outcome'           => $registrationOutcome,
                ]);

                // -----------------------------------------------------
                // Persist the lab data itself — always, regardless of
                // whether the user was just created or already existed.
                // -----------------------------------------------------
                $labForm = LabSetupForm::create(array_merge(
                    $validated,
                    [
                        'lead_id'           => $user->id,
                        'traffic_source_id' => $trafficSource->id,
                        'synced_to_lms'     => true,
                        'lms_reference_id'  => 'LMS-' . strtoupper(uniqid()),
                    ]
                ));

                Log::info('LabSetupForm created and linked', [
                    'lab_setup_form_id' => $labForm->id,
                    'lead_id'           => $user->id,
                    'traffic_source_id' => $trafficSource->id,
                ]);

                return [
                    'outcome'  => $registrationOutcome,
                    'user'     => $user,
                    'password' => $generatedPassword,
                    'form'     => $labForm,
                ];
            });

            $user = $result['user'];
            $form = $result['form'];

            // New user → account created, verification email carries the
            // generated password, lab data is already saved.
            if ($result['outcome'] === 'created') {
                $this->sendVerificationEmail($user, $result['password']);

                return response()->json([
                    'success' => true,
                    'type'    => 'registered',
                    'user_id' => $user->id,
                    'message' => 'Form submitted. We\'ve also created your account — please check your email and verify it before logging in.',
                    'data'    => $form,
                ], 201);
            }

            // Existing but unverified → resend verification, lab data saved.
           if ($user && !$user->email_verified_at) {
                $this->sendVerificationEmail($user, null);

                return response()->json([
                    'success' => true,
                    'type'    => 'verification_resent',
                    'user_id' => $user->id,
                    'message' => 'Form submitted. An account with this email already exists but isn\'t verified yet — we\'ve resent the verification email.',
                    'data'    => $form,
                ], 201);
            }

            // Existing and verified → lab data still saved, no email sent.
            return response()->json([
                'success' => true,
                'type'    => 'already_registered',
                'user_id' => $user->id,
                'message' => 'Form submitted and stored successfully.',
                'data'    => $form,
            ], 201);
        } catch (Throwable $e) {
            Log::error('Lab setup form submission failed', [
                'email' => $email ?? null,
                'input' => $request->all(),
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'type'    => 'error',
                'message' => app()->environment('local')
                    ? $e->getMessage()
                    : 'Something went wrong while saving your submission. Please try again in a moment.',
            ], 500);
        }
    }

    /**
     * GET /api/lab-setup-forms
     */
    public function index(Request $request): JsonResponse
    {
        $forms = LabSetupForm::query()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $forms,
        ]);
    }

    /**
     * GET /api/lab-setup-forms/{labSetupForm}
     */
    public function show(LabSetupForm $labSetupForm): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $labSetupForm,
        ]);
    }

    /**
     * Sends the account-verification email.
     *
     * If your app already defines this elsewhere (e.g. a shared trait
     * used by the general landing/registration controller — the one
     * this flow was modeled on), delete this method and use that one
     * instead so both endpoints stay in sync.
     *
     * @param  User        $user
     * @param  string|null $plainPassword  Included only for brand-new accounts.
     */
    protected function sendVerificationEmail(User $user, ?string $plainPassword): void
    {
        // Laravel's built-in "verify your email" notification.
        // If you need the plaintext password included in the email body,
        // replace this with a custom Notification/Mailable that accepts
        // $plainPassword and renders it alongside the verification link.
        $user->sendEmailVerificationNotification();

        Log::info('Verification email dispatched', [
            'user_id'           => $user->id,
            'included_password' => $plainPassword !== null,
        ]);
    }
}
