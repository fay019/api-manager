<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

class ThrottleApiClient
{
    public function __construct(protected RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next)
    {
        $apiClient = $request->attributes->get('api_client');
        $apiKey = $request->attributes->get('api_key');

        if ($apiClient && $apiKey) {
            // Authenticated request - use client's rate limit
            $limit = $apiClient->rate_limit_per_minute;
            $key = "api_client:{$apiClient->id}";
        } else {
            // Unauthenticated request - use strict limit per IP
            $limit = config('api.unauthenticated_rate_limit', 10);
            $key = 'api_ip:'.$request->ip();
        }

        if ($this->limiter->tooManyAttempts($key, $limit)) {
            $retryAfter = $this->limiter->availableIn($key);

            return ApiResponse::tooManyRequests('Rate limit exceeded')
                ->header('Retry-After', $retryAfter);
        }

        $this->limiter->hit($key, 60); // 60 second window

        $response = $next($request);

        return $response->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', $limit - $this->limiter->attempts($key));
    }
}
