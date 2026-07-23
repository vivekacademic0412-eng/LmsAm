<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        // The 'signed' route middleware already rejects a tampered or
        // expired URL before this method runs — this is a second,
        // explicit check on the hash itself for defense in depth.
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('success', 'Your email is already verified. Please log in.');
        }
        // LMS Registration
        if ($user->registration_source === 'lms') {
            return redirect()->route('login')
                ->with('success', 'Email verified successfully! You can now log in.');
        }
        // Landing Page Registration

        // This is the line that actually sets email_verified_at = now()
        // and saves the model.
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

       return redirect()->route('landing.thankyou', $user)
            ->with('success', 'Thank you! Our team will contact you shortly.');
    }
}
