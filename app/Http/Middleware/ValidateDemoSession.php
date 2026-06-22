<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\DemoAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ValidateDemoSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle($request, Closure $next)
{
    $token = DemoAccessToken::where('user_id',auth()->id() )->latest()->first();

    if (!$token) {
        abort(403);
    }

    $fingerprint = md5(
        $request->ip().
        $request->userAgent()
    );

    if (
        $token->browser_fingerprint !== $fingerprint
    ) {
        abort(
            403,
            'Demo session already active in another browser.'
        );
    }

    return $next($request);
}
}
