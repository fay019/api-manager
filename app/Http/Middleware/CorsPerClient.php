<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class CorsPerClient
{
    public function handle(Request $request, Closure $next)
    {
        $apiClient = $request->attributes->get('api_client');
        $origin = $request->header('Origin');

        // If there's an authenticated API client
        if ($apiClient) {
            $allowedOrigins = $apiClient->allowed_origins ?? [];

            // If Origin header is present, validate it
            if ($origin) {
                if (! in_array($origin, $allowedOrigins)) {
                    return ApiResponse::forbidden('Origin not allowed');
                }
            }

            // Set CORS headers for authenticated requests
            if ($origin && in_array($origin, $allowedOrigins)) {
                return $this->setCorsHeaders($next($request), $origin);
            }

            return $next($request);
        }

        // For public endpoints, allow if no Origin header (server-to-server)
        // or use default CORS config
        $response = $next($request);

        if ($origin) {
            // For public endpoints with Origin, use permissive CORS
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-KEY');
        }

        return $response;
    }

    private function setCorsHeaders($response, string $origin)
    {
        $response->header('Access-Control-Allow-Origin', $origin);
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-KEY');
        $response->header('Access-Control-Max-Age', '3600');

        return $response;
    }
}
