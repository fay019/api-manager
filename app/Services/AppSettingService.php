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

    /**
     * Resets the application to PRE-INSTALL state.
     * WARNING: This is destructive.
     */
    public function resetApplication(): bool
    {
        try {
            // 1. Remove lock
            $lockFile = storage_path('app/installed.lock');
            if (file_exists($lockFile)) {
                @unlink($lockFile);
            }

            // 2. Clear setup progress
            $setupDir = storage_path('app/setup');
            if (is_dir($setupDir)) {
                $files = glob($setupDir.'/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }

            // 3. Reset DB (SQLite)
            if (config('database.default') === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (file_exists($dbPath)) {
                    @unlink($dbPath);
                    @unlink($dbPath.'-wal');
                    @unlink($dbPath.'-shm');
                }
            }

            // 4. Purge logs
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }

            $installLog = storage_path('logs/installation.log');
            if (file_exists($installLog)) {
                file_put_contents($installLog, '');
            }

            // 5. Purge sessions (driver file)
            $sessionDir = storage_path('framework/sessions');
            if (is_dir($sessionDir)) {
                $files = glob($sessionDir.'/*');
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== '.gitignore') {
                        @unlink($file);
                    }
                }
            }

            // 6. Clear caches
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Application Reset Failed: '.$e->getMessage());

            return false;
        }
    }
}
