<?php

namespace App\Models;

use App\Enums\PromoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_url',
        'cta_text',
        'cta_url',
        'status',
        'starts_at',
        'ends_at',
        'priority',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => PromoStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'priority' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromoVersion::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PromoEvent::class);
    }

    public function scopeActive($query)
    {
        return $query
            ->where('status', PromoStatus::PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('priority')
            ->orderByDesc('created_at');
    }
}
