<?php

namespace App\Services;

use App\Models\DocumentationSetting;

class AppSettingService
{
    /**
     * Check if admin credentials should be shown
     * Returns true only if in local environment AND setting is enabled
     */
    public function shouldShowCredentials(): bool
    {
        return DocumentationSetting::shouldShowCredentials();
    }

    /**
     * Check if a specific documentation is visible
     */
    public function isDocumentationVisible(string $docName): bool
    {
        return DocumentationSetting::isDocVisible($docName);
    }

    /**
     * Get array of visible documentation names
     */
    public function getVisibleDocs(): array
    {
        return DocumentationSetting::visible();
    }

    /**
     * Check if the docs index should be visible
     * Index is visible if at least one documentation is visible
     */
    public function isDocsIndexVisible(): bool
    {
        return count($this->getVisibleDocs()) > 0;
    }

    /**
     * Get all documentation with their paths and visibility
     */
    public function getAllDocs(): array
    {
        return DocumentationSetting::getAllDocs();
    }

    /**
     * Get path for a documentation
     */
    public function getDocPath(string $docName): ?string
    {
        $doc = DocumentationSetting::getByName($docName);
        return $doc ? $doc->path : null;
    }
}
