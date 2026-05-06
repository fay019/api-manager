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
        // Vérifier que le service IA est configuré avec un token interne
        $settings = AiSetting::getInstance();

        if (! $settings->ia_token_hash) {
            return ApiResponse::error('AI_NOT_CONFIGURED', 'AI service is not properly configured', [], 503);
        }

        return $next($request);
    }
}
