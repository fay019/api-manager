<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\AiSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IaTokenAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $tokenHeader = $request->header('X-IA-TOKEN');

        if (! $tokenHeader) {
            return ApiResponse::unauthorized('Invalid or missing IA token');
        }

        $settings = AiSetting::getInstance();

        if (! $settings->verifyToken($tokenHeader)) {
            return ApiResponse::unauthorized('Invalid or missing IA token');
        }

        return $next($request);
    }
}
