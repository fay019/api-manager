<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'favicon_source',
        'og_image',
        'site_name',
        'is_rounded',
    ];

    protected function casts(): array
    {
        return [
            'is_rounded' => 'boolean',
        ];
    }
}
