<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your route middleware aliases here
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class, // <-- Add this line
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            // Add other aliases like 'guest', 'auth', etc. if needed,
            // though Breeze might handle some automatically.
        ]);

        // You can also configure global middleware, groups, etc. here
        // $middleware->use([...]);
        // $middleware->appendToGroup('web', ...);
        // $middleware->prependToGroup('web', ...);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception handling configuration
    })->create();