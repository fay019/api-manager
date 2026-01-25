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

    public function getPromoForApi(?Promo $promo = null, ?string $locale = null): array
    {
        $promo = $promo ?? $this->getActivePromo();

        if (! $promo) {
            return [];
        }

        $locale = $locale ?? request()->query('lang') ?? app()->getLocale();

        // Récupérer le numéro de la version la plus récente
        $versionNumber = $promo->versions()->max('version') ?? 1;

        return [
            'id' => $promo->id,
            'version' => $versionNumber,
            'locale' => $locale,
            'title' => $promo->getTranslation('title', $locale),
            'content' => $promo->getTranslation('content', $locale),
            'image_url' => $promo->full_image_url,
            'cta_text' => $promo->getTranslation('cta_text', $locale),
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
