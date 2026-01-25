<?php

namespace App\Models;

use App\Enums\PromoStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Promo extends Model
{
    protected $fillable = [
        'slug',
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

    protected function fullImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->getAttributes()['image_url'] ?? null;
                if (! $value) {
                    return null;
                }
                if (str_starts_with($value, 'http')) {
                    return $value;
                }

                return Storage::disk('public')->url($value);
            }
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromoVersion::class);
    }

    public function scopeActive($query)
    {
        return $query
            ->whereIn('status', [PromoStatus::PUBLISHED, PromoStatus::SCHEDULED])
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
