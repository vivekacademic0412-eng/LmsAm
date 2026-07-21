<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }
          // Check onboarding only for Students & Trainers
        if (
            in_array($user->role, [User::ROLE_STUDENT, User::ROLE_TRAINER]) &&
            $user->onboarding_status !== 'completed' &&
            ! $request->routeIs('onboarding.*')
        ) {
            return redirect()->route('onboarding.wizard');
        }

        return $next($request);
    }
}
