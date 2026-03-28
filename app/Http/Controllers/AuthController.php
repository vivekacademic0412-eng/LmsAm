<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 300);

            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        $user = $request->user();

        if (! $user?->is_active) {
            ActivityLogger::log(
                actor: $user,
                module: 'Authentication',
                action: 'blocked_login',
                description: 'Blocked a login attempt for an inactive account.',
                context: [
                    'subject_type' => User::class,
                    'subject_id' => $user?->id,
                    'subject_label' => $user?->name ? $user->name.' | '.$user->email : $credentials['email'],
                    'route_name' => 'login.attempt',
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'properties' => [
                        'remember' => $request->boolean('remember'),
                        'status' => 'inactive',
                    ],
                ]
            );

            Auth::logout();

            return back()->withErrors([
                'email' => 'Your account is inactive. Contact Super Admin.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        ActivityLogger::log(
            actor: $user,
            module: 'Authentication',
            action: 'login',
            description: 'Signed in to the LMS.',
            context: [
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'subject_label' => $user->name.' | '.$user->email,
                'route_name' => 'login.attempt',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'properties' => [
                    'remember' => $request->boolean('remember'),
                ],
            ]
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            ActivityLogger::log(
                actor: $user,
                module: 'Authentication',
                action: 'logout',
                description: 'Signed out of the LMS.',
                context: [
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'subject_label' => $user->name.' | '.$user->email,
                    'route_name' => 'logout',
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                ]
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
