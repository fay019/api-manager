<?php

namespace App\Services;

use App\Models\Promo;
use Illuminate\Support\Facades\Cache;

class PromoService
{
    public function getActivePromo(): ?Promo
    {
        return Cache::remember(
            'promo_active_banner',
            config('api.promo_cache_ttl', 60),
            fn() => Promo::active()->first()
        );
    }

    public function clearActivePromoCache(): void
    {
        Cache::forget('promo_active_banner');
    }

    public function getPromoForApi(): array
    {
        $promo = $this->getActivePromo();

        if (!$promo) {
            return [];
        }

        return [
            'id' => $promo->id,
            'title' => $promo->title,
            'content' => $promo->content,
            'image_url' => $promo->image_url,
            'cta_text' => $promo->cta_text,
            'cta_url' => $promo->cta_url,
            'priority' => $promo->priority,
        ];
    }
}
