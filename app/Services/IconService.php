<?php

namespace App\Services;

use App\Models\ExternalIcon;
use Illuminate\Support\Facades\Cache;

class IconService
{
    public function getIcon(string $name): ?string
    {
        return Cache::remember("icon:{$name}", 86400, fn () => $this->resolve($name));
    }

    private function resolve(string $name): ?string
    {
        $icon = ExternalIcon::where('slug', $name)->where('is_active', true)->first();

        return $icon?->getSvg() ?? $icon?->getUrl();
    }

    public function importFromUrl(string $url, string $name, ?string $provider = null): ExternalIcon
    {
        $svg = file_get_contents($url);
        $svg = $this->normalizeSvg($svg);

        return ExternalIcon::create([
            'name' => $name,
            'slug' => str()->slug($name),
            'provider' => $provider,
            'type' => 'svg',
            'source' => $svg,
        ]);
    }

    public function normalizeSvg(string $svg): string
    {
        return strip_tags($svg, '<svg><path><circle><rect><line><polyline><polygon><g><text><tspan><defs><style>');
    }
}
