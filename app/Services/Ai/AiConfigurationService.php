<?php

namespace App\Services\Ai;

use App\Models\AiSetting;

class AiConfigurationService
{
    public function getBaseUrl(): string
    {
        return AiSetting::getInstance()->base_url;
    }

    public function getDefaultModel(): string
    {
        return AiSetting::getInstance()->default_model;
    }

    public function getAllowedModels(): array
    {
        return AiSetting::getInstance()->getAllowedModelsArray();
    }

    public function getTimeout(): int
    {
        // Durchsetze ein Minimum von 60 Sekunden
        return max(60, AiSetting::getInstance()->timeout);
    }

    public function isActive(): bool
    {
        return AiSetting::getInstance()->is_active;
    }

    public function getCacheKey(): string
    {
        return AiSetting::buildCacheKey();
    }
}
