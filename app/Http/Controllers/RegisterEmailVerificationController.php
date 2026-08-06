<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class RegisterEmailVerificationController extends Controller
{
    public function __invoke(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Already verified
        if (!is_null($user->email_verified_at)) {

            // if ($user->registration_source === 'lms') {
            return redirect()->route('login')
                ->with('success', 'Your email is already verified.');
        }


        // }

        // Actually verify email
        if (is_null($user->email_verified_at)) {

            $user->email_verified_at = now();
            $user->save();

            // Optional
            event(new Verified($user));
        }

        // Redirect based on source
        // if ($user->registration_source === 'lms') {
        // return redirect()->route('login')
        //     ->with('success', 'Email verified successfully! You can now log in.');
        // }

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now log in.');
    }
}
