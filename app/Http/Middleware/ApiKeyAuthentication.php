<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiKeyAuthentication
{
    public function handle(Request $request, Closure $next, bool $required = false)
    {
        $apiKey = null;
        $apiClient = null;

        $keyHeader = $request->header('X-API-KEY');

        if ($keyHeader) {
            $keyPrefix = substr($keyHeader, 0, 8);
            $key = ApiKey::where('key_prefix', $keyPrefix)
                ->where('is_active', true)
                ->with('apiClient')
                ->first();

            if ($key && Hash::check($keyHeader, $key->key_encrypted)) {
                if ($key->expires_at && $key->expires_at->isPast()) {
                    return ApiResponse::unauthorized('API key has expired');
                }

                if (! $key->apiClient->is_active) {
                    return ApiResponse::unauthorized('API client is not active');
                }

                $apiKey = $key;
                $apiClient = $key->apiClient;

                // Update last_used_at (async to avoid latency)
                $key->update(['last_used_at' => now()]);
            } else {
                return ApiResponse::unauthorized('Invalid API key');
            }
        } elseif ($required) {
            return ApiResponse::unauthorized('API key is required');
        }

        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('api_client', $apiClient);

        return $next($request);
    }
}
