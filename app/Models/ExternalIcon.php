<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalIcon extends Model
{
    protected $fillable = ['name', 'slug', 'provider', 'type', 'color', 'source', 'tags', 'is_active'];

    protected $casts = ['tags' => 'array'];

    public function getSvg(): ?string
    {
        return $this->type === 'svg' ? $this->source : null;
    }

    public function getUrl(): ?string
    {
        return $this->type === 'cdn' ? $this->source : null;
    }
}
