<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;

class LogApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = (int) ((microtime(true) - $startTime) * 1000);

        // Log request asynchronously
        $this->logRequest($request, $response, $duration);

        return $response;
    }

    private function logRequest(Request $request, $response, int $durationMs): void
    {
        try {
            $apiClient = $request->attributes->get('api_client');
            $apiKey = $request->attributes->get('api_key');

            // Origin/Referer dépend du client, pas toujours présent. Fallback: Origin > Referer > x-site-domain
            $origin = $request->header('Origin');
            $referer = $request->header('Referer');
            $xSiteDomain = $request->header('X-Site-Domain');

            // Client custom headers
            $siteName = $request->header('X-Site-Name');
            $pagePath = $request->header('X-Site-Page');
            $fullUrl = $request->header('X-Site-Full-Url');
            $clientRequestTime = $request->header('X-Request-Time');
            $clientUserAgent = $request->header('X-User-Agent');

            $ip = $request->ip();
            $hostname = null;
            if ($ip) {
                $hostname = gethostbyaddr($ip);
                if ($hostname === $ip) {
                    $hostname = null;
                }
            }

            // Extract domain from Origin, Referer, or X-Site-Domain header
            $domain = $this->extractDomain($origin ?? $referer) ?? $xSiteDomain;

            ApiRequestLog::create([
                'api_client_id' => $apiClient?->id,
                'api_key_id' => $apiKey?->id,
                'method' => $request->method(),
                'path' => $request->path(),
                'status_code' => $response->status(),
                'ip' => $ip,
                'hostname' => $hostname,
                'domain' => $domain,
                'site_name' => $siteName,
                'page_path' => $pagePath,
                'full_url' => $fullUrl,
                'client_request_time' => $clientRequestTime ? \Carbon\Carbon::createFromTimestamp($clientRequestTime) : null,
                'client_user_agent' => $clientUserAgent,
                'user_agent' => $request->header('User-Agent'),
                'origin' => $origin ?? $referer,
                'referer' => $referer,
                'duration_ms' => $durationMs,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't let logging break the response
            \Log::error('Failed to log API request', ['exception' => $e]);
        }
    }

    private function extractDomain(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $parsed = parse_url($url);

        return $parsed['host'] ?? null;
    }
}
