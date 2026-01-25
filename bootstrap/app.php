<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: [
            'api_manager_setup_token',
        ]);

        // Trust proxies for correct HTTPS detection
        $middleware->trustProxies(at: '*');

        $middleware->web(prepend: [
            \App\Http\Middleware\EnsureDatabaseExists::class,
        ]);

        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyAuthentication::class,
            'cors.client' => \App\Http\Middleware\CorsPerClient::class,
            'throttle.api.client' => \App\Http\Middleware\ThrottleApiClient::class,
            'log.api' => \App\Http\Middleware\LogApiRequest::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\ApiKeyAuthentication::class,
            \App\Http\Middleware\CorsPerClient::class,
            \App\Http\Middleware\ThrottleApiClient::class,
            \App\Http\Middleware\LogApiRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
