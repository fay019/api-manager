<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Str;

class ApiKeyService
{
    public function generateKey(): array
    {
        $rawKey = Str::random(32);
        $prefix = 'apk_' . Str::random(4);
        $hash = bcrypt($rawKey);

        return [
            'raw' => "{$prefix}{$rawKey}",
            'prefix' => $prefix,
            'hash' => $hash,
        ];
    }

    public function validateKey(string $rawKey): ?ApiKey
    {
        $prefix = substr($rawKey, 0, 8);

        $key = ApiKey::where('key_prefix', $prefix)
            ->where('is_active', true)
            ->with('apiClient')
            ->first();

        if (!$key) {
            return null;
        }

        if (!hash_equals($key->key_hash, crypt($rawKey, $key->key_hash))) {
            // Use a more reliable comparison
            if (!password_verify($rawKey, $key->key_hash)) {
                return null;
            }
        }

        if ($key->expires_at && $key->expires_at->isPast()) {
            return null;
        }

        if ($key->apiClient->status->value !== 'active') {
            return null;
        }

        return $key;
    }
}
