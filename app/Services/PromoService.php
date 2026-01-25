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
            fn () => Promo::active()->first()
        );
    }

    public function clearActivePromoCache(): void
    {
        Cache::forget('promo_active_banner');
    }

    public function getPromoForApi(?Promo $promo = null): array
    {
        $promo = $promo ?? $this->getActivePromo();

        if (! $promo) {
            return [];
        }

        return [
            'id' => $promo->id,
            'title' => $promo->title,
            'content' => $promo->content,
            'image_url' => $promo->full_image_url,
            'cta_text' => $promo->cta_text,
            'cta_url' => $promo->cta_url,
            'priority' => $promo->priority,
            'max_impressions' => $promo->max_impressions,
            'cooldown_seconds' => $promo->cooldown_seconds,
            'display_mode' => $promo->display_mode,
            'start_date' => $promo->starts_at?->format('Y-m-d'),
            'end_date' => $promo->ends_at?->format('Y-m-d'),
        ];
    }

    public function getPromoBySlug(string $slug): ?Promo
    {
        return Promo::active()
            ->where('slug', $slug)
            ->first();
    }
}
