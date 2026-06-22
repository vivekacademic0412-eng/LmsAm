<?php

namespace App\Http\Controllers;

use App\Models\DemoAccessToken;
use Illuminate\Http\Request;

class DemoAccessController extends Controller
{
    public function access($token, Request $request)
{
    $demoToken = DemoAccessToken::where('token', $token)
        ->firstOrFail();

    // if ($demoToken->used_at) {
    //     abort(403, 'This demo link has already been used.');
    // }

    // if ($demoToken->is_completed) {
    //     abort(403, 'Demo already completed.');
    // }

    // if ($demoToken->expires_at < now()) {
    //     abort(403, 'Demo link expired.');
    // }

    $fingerprint = md5(
        $request->ip().
        $request->userAgent()
    );

    $demoToken->update([
        'session_id' => session()->getId(),
        'browser_fingerprint' => $fingerprint,
        'used_at' => now()
    ]);

    auth()->loginUsingId(
        $demoToken->user_id
    );

    return redirect()->route('lms.landing');
}
}
