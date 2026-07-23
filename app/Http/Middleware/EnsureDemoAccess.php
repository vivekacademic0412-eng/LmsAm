<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoAccess
{
    /**
     * Register this on every demo route (lms.landing and anything under it),
     * e.g. in bootstrap/app.php:
     *   $middleware->alias(['ensure.demo.access' => \App\Http\Middleware\EnsureDemoAccess::class]);
     * and on the route group:
     *   Route::middleware(['auth', 'ensure.demo.access'])->group(function () { ... });
     */
    protected array $preFeedbackLockedRoutes = [
        'lms.step1',
        'lms.step1.store',
        'lms.step2',
        'lms.step2.store',
        'lms.step3',
        'lms.step3.store',
    ];

    /**
     * Once the whole funnel (demo + feedback) is complete, ONLY these
     * routes stay reachable. Everything else redirects to step6 instead
     * of logging the user out — they've earned the right to see their result.
     */
    protected array $finalRoutes = [
        'lms.step5',
        'lms.step6',
        'lms.certificate.download',
        'lms.feedback.store',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please use the demo link sent to your email to access this page.');
        }

        $user = Auth::user();

        // Only students who actually came in through the token flow may be here.
        // if ($user->role !== 'student' || !$request->session()->has('demo_login_token_id')) {
       if ($user->role !== 'student' ) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'You are not authorized to view this page.');
        }

        $demo = $user->demo;

        $hasSubmittedDemo     = $demo && $demo->submittedDemos()->exists();
        $hasSubmittedFeedback = $demo && $demo->feedback()->exists();
        $isCompleted          = $hasSubmittedDemo && $hasSubmittedFeedback;

        $routeName = $request->route()?->getName();

        // ── Fully completed: steer to the result page, never log out. ──
        if ($isCompleted && ! in_array($routeName, $this->finalRoutes, true)) {
            return redirect()->route('lms.step6')
                ->with('info', "You've already completed this demo — here's where you left off.");
        }

        // ── Demo submitted, feedback still pending: lock step1–3 only. ──
        if ($hasSubmittedDemo && ! $isCompleted && in_array($routeName, $this->preFeedbackLockedRoutes, true)) {
            return redirect()->route('lms.step4')
                ->with('info', 'Your demo has already been submitted — no need to redo it.');
        }

        return $next($request);
    }
}
