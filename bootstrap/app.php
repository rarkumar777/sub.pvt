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
    ->withMiddleware(function (Middleware $middleware): void {
        // Remove ValidatePostSize to allow large file/video uploads
        $middleware->remove(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAuth::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'website.offline' => \App\Http\Middleware\CheckWebsiteOffline::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '*/payment/paytabs/callback',
            '*/payment/paytabs/return',
            'payment/return',
            'payment/return*',
            'payment/callback',
            'payment/callback*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

