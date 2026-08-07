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
        $accessToken = DemoAccessToken::where('token', $token)->firstOrFail();

        $user = User::find($accessToken->user_id);
        if (!$user || $user->role !== 'student') {
            abort(403, 'This demo link is not valid for this account');
        }

        if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
            abort(403, 'This demo link has expired.');
        }

        $demo = $user->demo;
        $isCompleted = $demo
            && $demo->submittedDemos()->exists()
            && $demo->feedback()->exists();

        if ($isCompleted) {
            abort(403, 'This demo has already been completed. The link is no longer active.');
        }

        // All checks passed — now log in
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('demo_login_token_id', $accessToken->id);

        if (!$accessToken->used) {
            $accessToken->update(['used' => true, 'used_at' => now()]);
        }

        return redirect()->route('lms.landing')
            ->with('info', 'Welcome! Your demo session has started.');
    }
}
