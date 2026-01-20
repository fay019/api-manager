<?php

namespace App\Modules\Promo\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\PromoEventService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoEventController
{
    public function store(Request $request, PromoEventService $promoEventService)
    {
        $validated = $request->validate([
            'promo_id' => ['required', 'integer', 'exists:promos,id'],
            'event_type' => ['required', 'string', Rule::in(['impression', 'click', 'dismiss'])],
            'session_id' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url'],
        ]);

        $success = $promoEventService->trackEvent(
            promoId: $validated['promo_id'],
            eventType: $validated['event_type'],
            sessionId: $validated['session_id'] ?? null,
            url: $validated['url'] ?? null,
            request: $request,
        );

        if (!$success) {
            return ApiResponse::notFound('Promo not found');
        }

        return ApiResponse::success(
            ['message' => 'Event tracked successfully'],
            status: 201
        );
    }
}
