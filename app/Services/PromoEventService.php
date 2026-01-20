<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\PromoEvent;
use Illuminate\Http\Request;

class PromoEventService
{
    public function trackEvent(
        int $promoId,
        string $eventType,
        ?string $sessionId = null,
        ?string $url = null,
        ?Request $request = null,
    ): bool {
        $promo = Promo::find($promoId);

        if (!$promo) {
            return false;
        }

        PromoEvent::create([
            'promo_id' => $promoId,
            'event_type' => $eventType,
            'session_hash' => $sessionId ? hash('sha256', $sessionId) : null,
            'ip_hash' => $request ? hash('sha256', $request->ip()) : null,
            'user_agent_hash' => $request ? hash('sha256', $request->header('User-Agent') ?? '') : null,
            'referer' => $request?->header('Referer'),
            'origin' => $request?->header('Origin'),
            'created_at' => now(),
        ]);

        return true;
    }
}
