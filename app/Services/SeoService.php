<?php

namespace App\Services;

use App\Models\SeoMeta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeoService
{
    public function getMeta(string $url, string $locale = 'fr'): ?SeoMeta
    {
        $key = "seo:{$locale}:{$url}";
        $ttl = config('seo.cache_ttl', 86400);

        return Cache::remember($key, $ttl, fn () => $this->findOrCreate($url, $locale));
    }

    private function findOrCreate(string $url, string $locale): ?SeoMeta
    {
        $existing = SeoMeta::byUrl($url, $locale)->first();

        if ($existing?->is_ignored) {
            return null;
        }

        return $existing ?? $this->createAuto($url, $locale);
    }

    private function createAuto(string $url, string $locale): SeoMeta
    {
        $title = Str::title(str_replace(['-', '_', '/'], ' ', trim($url, '/')));

        return SeoMeta::create([
            'url' => $url,
            'locale' => $locale,
            'title' => $title,
            'description' => "Découvrez $title sur notre plateforme",
            'is_auto_generated' => true,
        ]);
    }
}
