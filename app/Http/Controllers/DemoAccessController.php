<?php

namespace App\Http\Controllers;

use App\Models\DemoAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DemoAccessController extends Controller
{
    public function access(Request $request, string $token)
    {
        $accessToken = DemoAccessToken::where('token', $token)->first();

        if (!$accessToken) {
            abort(404, 'This demo link is invalid.');
        }

        if ($accessToken->used && $accessToken->completed) {
            abort(403, 'This demo link has already been used.');
        }

        if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
            abort(403, 'This demo link has expired.');
        }

        $user = User::find($accessToken->user_id);

        if (!$user || $user->role !== 'student') {
            abort(403, 'This demo link is not valid for this account.');
        }

        $demo = $user->demo;

        // $hasReachedLastStep = $demo && ($demo->current_step ?? 0) >= 5;
        $hasSubmittedDemo   = $demo && $demo->submittedDemos()->exists();
        $hasSubmittedFeedback = $demo && $demo->feedback()->exists();

        $isCompleted = $hasSubmittedDemo
            && $hasSubmittedFeedback;

        if ($isCompleted) {
            abort(403, 'This demo has already been completed. The link is no longer active.');
        }

        // Atomically claim the token — same column that was checked above,
        // so a second request against the same token can never sneak through.
        $claimed = DemoAccessToken::where('id', $accessToken->id)
            ->where('used', false)
            ->update(['used' => true, 'used_at' => now()]);

        if (!$claimed) {
            // Someone else (or another tab / a race) claimed it a moment earlier.
            abort(403, 'This demo link has already been used.');
        }

        // Rotate the session id so nothing from a prior session on this
        // browser carries over into the newly authenticated one.
        $request->session()->regenerate();

        Auth::login($user);

        // Tag this login so EnsureDemoAccess can confirm, on every later
        // request, that the *current* session is the one that claimed
        // this token — not a session copied/replayed some other way.
        $request->session()->put('demo_login_token_id', $accessToken->id);

        return redirect()->route('lms.landing')
            ->with('info', 'Welcome! Your demo session has started.');
    }
}