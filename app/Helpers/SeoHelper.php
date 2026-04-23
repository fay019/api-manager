<?php

use App\Models\SeoMeta;
use App\Services\SeoService;

if (! function_exists('seo')) {
    function seo(?string $url = null, ?string $locale = null): ?SeoMeta
    {
        $url = $url ?? request()->path();
        $locale = $locale ?? app()->getLocale();

        return app(SeoService::class)->getMeta($url, $locale);
    }
}
