<?php

use App\Http\Middleware\ApiKeyAuthentication;
use App\Http\Middleware\CorsPerClient;
use App\Http\Middleware\EnsureDatabaseExists;
use App\Http\Middleware\LogApiRequest;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ThrottleApiClient;
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
            'locale',
        ]);

        // Trust proxies for correct HTTPS detection
        $middleware->trustProxies(at: '*');

        $middleware->web(prepend: [
            EnsureDatabaseExists::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'api.key' => ApiKeyAuthentication::class,
            'cors.client' => CorsPerClient::class,
            'throttle.api.client' => ThrottleApiClient::class,
            'log.api' => LogApiRequest::class,
        ]);

        $middleware->api(prepend: [
            ApiKeyAuthentication::class,
            CorsPerClient::class,
            ThrottleApiClient::class,
            LogApiRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
