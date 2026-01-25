<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ApiKeyService
{
    public function generateKey(): array
    {
        $rawKeyPart = Str::random(32);
        $prefix = 'apk_'.Str::random(4);
        $fullRawKey = "{$prefix}{$rawKeyPart}";
        $encrypted = Crypt::encryptString($fullRawKey);

        return [
            'raw' => $fullRawKey,
            'prefix' => $prefix,
            'encrypted' => $encrypted,
        ];
    }

    public function validateKey(string $rawKey): ?ApiKey
    {
        $prefix = substr($rawKey, 0, 8);

        $keys = ApiKey::where('key_prefix', $prefix)
            ->where('is_active', true)
            ->with('apiClient')
            ->get();

        foreach ($keys as $key) {
            try {
                $decrypted = Crypt::decryptString($key->key_encrypted);
                if ($decrypted === $rawKey) {
                    // Validation des dates et du client
                    if ($key->starts_at && $key->starts_at->isFuture()) {
                        continue;
                    }

                    if ($key->expires_at && $key->expires_at->isPast()) {
                        continue;
                    }

                    if (! $key->apiClient->is_active) {
                        continue;
                    }

                    return $key;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
