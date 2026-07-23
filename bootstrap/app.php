<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
         api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => \App\Http\Middleware\ActiveUserMiddleware::class,
            'activity.log' => \App\Http\Middleware\LogActivity::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'secure.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'onbording' => \App\Http\Middleware\EnsureOnboardingCompleted::class,
             'demo_access' => \App\Http\Middleware\EnsureDemoAccess::class,
            
        ]);

        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
