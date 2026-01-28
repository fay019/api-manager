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

        $allLangs = request()->query('all_langs') === 'true';
        $locale = $locale ?? request()->query('lang') ?? app()->getLocale();

        // Récupérer le numéro de la version la plus récente
        $versionNumber = $promo->versions()->max('version') ?? 1;

        $data = [
            'id' => $promo->id,
            'author_name' => $promo->author_name,
            'author_role' => $promo->author_role,
            'version' => $versionNumber,
        ];

        if ($allLangs) {
            $data['translations'] = [
                'title' => $promo->title,
                'content' => $promo->content,
                'cta_text' => $promo->cta_text,
            ];
        } else {
            $data['locale'] = $locale;
            $data['title'] = $promo->getTranslation('title', $locale);
            $data['content'] = $promo->getTranslation('content', $locale);
            $data['cta_text'] = $promo->getTranslation('cta_text', $locale);
        }

        $response = array_merge($data, [
            'image_url' => $promo->full_image_url,
            'cta_url' => $promo->cta_url,
            'priority' => $promo->priority,
            'max_impressions' => $promo->max_impressions,
            'cooldown_seconds' => $promo->cooldown_seconds,
            'display_mode' => $promo->display_mode,
            'start_date' => $promo->starts_at?->format('Y-m-d'),
            'end_date' => $promo->ends_at?->format('Y-m-d'),
        ]);

        // Add new optional display feature fields
        if ($promo->auto_close_timer !== null) {
            $response['auto_close_timer'] = $promo->auto_close_timer;
        }
        if ($promo->show_countdown !== null) {
            $response['show_countdown'] = $promo->show_countdown;
        }
        if ($promo->animation_style !== null) {
            $response['animation_style'] = $promo->animation_style;
        }
        if ($promo->message_display_mode !== null) {
            $response['message_display_mode'] = $promo->message_display_mode;
        }

        return $response;
    }

    public function getPromoBySlug(string $slug): ?Promo
    {
        return Promo::active()
            ->where('slug', $slug)
            ->first();
    }
}
