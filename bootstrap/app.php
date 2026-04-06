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
        // Tell Laravel's built-in auth middleware where the login page is
        $middleware->redirectGuestsTo(fn () => route('auth.login'));

        // Register custom named middleware aliases
        $middleware->alias([
            'admin'        => \App\Http\Middleware\AdminMiddleware::class,
            'staff'        => \App\Http\Middleware\StaffMiddleware::class,
            'website.live' => \App\Http\Middleware\CheckWebsiteLive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
