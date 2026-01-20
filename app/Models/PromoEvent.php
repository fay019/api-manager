<?php

namespace App\Models;

use App\Enums\PromoEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'promo_id',
        'event_type',
        'session_hash',
        'ip_hash',
        'user_agent_hash',
        'referer',
        'origin',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => PromoEventType::class,
            'created_at' => 'datetime',
        ];
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }
}
