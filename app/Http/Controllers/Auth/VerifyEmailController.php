<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Handles GET /email/verify/{id}/{hash}
     *
     * Laravel's built-in EmailVerificationRequest assumes the user is
     * already logged in (it checks auth()->id() === $id). Your register()
     * no longer calls Auth::login(), so the student clicking the link
     * from their inbox is a guest — this version verifies the signature
     * and hash directly instead of relying on auth state.
     */
    public function __invoke(Request $request, int $id, string $hash)
    {
        $user = Lead::findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Already verified
        if (!is_null($user->email_verified_at)) {

            if ($user->registration_source === 'lms') {
                return redirect()->route('login')
                    ->with('success', 'Your email is already verified.');
            }

            return redirect()->route('landing.thankyou', [
    'user' => $user,
])->with('success', 'Your email is already verified.');
        }

        // Actually verify email
        if (is_null($user->email_verified_at)) {

            $user->email_verified_at = now();
            $user->save();

            // Optional
            event(new Verified($user));
        }

        // Redirect based on source
        if ($user->registration_source === 'lms') {
            return redirect()->route('login')
                ->with('success', 'Email verified successfully! You can now log in.');
        }

      return redirect()->route('landing.thankyou', [
    'user' => $user,
])->with('success', 'Your email is already verified.');
    }
}
