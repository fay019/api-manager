<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = ['route_name', 'url', 'locale', 'title', 'description', 'keywords', 'og_title', 'og_description', 'og_image', 'canonical_url', 'robots', 'is_auto_generated', 'is_ignored'];

    public function scopeActive($query)
    {
        return $query->where('is_ignored', false);
    }

    public function scopeByRoute($query, $route, $locale = 'fr')
    {
        return $query->where('route_name', $route)->where('locale', $locale);
    }

    public function scopeByUrl($query, $url, $locale = 'fr')
    {
        return $query->where('url', $url)->where('locale', $locale);
    }
}
