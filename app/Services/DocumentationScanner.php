<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class DocumentationScanner
{
    /**
     * Scan for all documentation files and return with paths
     * Returns array of ['doc_name' => 'name', 'path' => '/path/to/file.md']
     */
    public static function scan(): array
    {
        $docs = [];

        // 1. Scan root directory for .md files
        $rootFiles = File::files(base_path());
        foreach ($rootFiles as $file) {
            if ($file->getExtension() === 'md') {
                $name = strtolower($file->getFilenameWithoutExtension());
                $docs[] = [
                    'doc_name' => $name,
                    'path' => '/' . $file->getFilename(),
                ];
            }
        }

        // 2. Scan docs directory - recursively
        $docsPath = base_path('docs');
        if (File::isDirectory($docsPath)) {
            $files = File::allFiles($docsPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'md') {
                    $name = strtolower($file->getFilenameWithoutExtension());

                    // Relative path from project root
                    $relativePath = str_replace(base_path(), '', $file->getRealPath());

                    // Avoid duplicates (if a file exists in both root and docs, which is unlikely but possible)
                    if (!in_array($name, array_column($docs, 'doc_name'))) {
                        $docs[] = [
                            'doc_name' => $name,
                            'path' => $relativePath,
                        ];
                    }
                }
            }
        }

        return $docs;
    }

    /**
     * Get metadata for a documentation
     * Reads icon from database, generates label and description
     */
    public static function getMetadata(string $docName): array
    {
        // Get the documentation record from database
        $doc = \App\Models\DocumentationSetting::where('doc_name', $docName)->first();

        // Use database icon if available, otherwise use default
        $icon = $doc?->icon ?? \App\Models\DocumentationSetting::getDefaultIcon($docName);

        // Predefined labels and descriptions (static, not in DB)
        $metadata = [
            'readme' => [
                'label' => 'README Documentation',
                'description' => 'Quick start guide with project overview',
            ],
            'installation' => [
                'label' => 'Installation Guide',
                'description' => 'Complete installation guide with Setup Wizard and CLI options',
            ],
            'setup_wizard' => [
                'label' => 'Setup Wizard',
                'description' => 'Interactive web-based installation for first-time setup',
            ],
            'module_creation' => [
                'label' => 'Module Creation Guide',
                'description' => 'Tutorial for creating custom modules and extending the application',
            ],
            'api' => [
                'label' => 'API Documentation',
                'description' => 'Complete endpoint reference',
            ],
            'database' => [
                'label' => 'Database Documentation',
                'description' => 'Database schema and relationships',
            ],
            'deployment' => [
                'label' => 'Deployment Documentation',
                'description' => 'Deployment guide for shared hosting',
            ],
            'clients' => [
                'label' => 'API Clients Management',
                'description' => 'Guide for managing API clients and generating API keys',
            ],
            'promos' => [
                'label' => 'Promotions System',
                'description' => 'Documentation for the promotional banners system',
            ],
        ];

        $base = $metadata[$docName] ?? [
            'label' => ucfirst(str_replace('_', ' ', $docName)),
            'description' => 'Documentation pour ' . $docName,
        ];

        return array_merge($base, ['icon' => $icon]);
    }

    /**
     * Sync documentation files with database
     * Creates/updates entries for all discovered docs
     * IMPORTANT: New documents are created with is_visible = false (user must enable manually)
     * Existing documents preserve their visibility and icon settings
     */
    public static function sync(): void
    {
        $scanned = self::scan();

        foreach ($scanned as $doc) {
            $existing = \App\Models\DocumentationSetting::where('doc_name', $doc['doc_name'])->first();

            if ($existing) {
                // Update only the path for existing records - preserve is_visible and icon settings
                $existing->update(['path' => $doc['path']]);
            } else {
                // Create new record with is_visible = false and default icon
                \App\Models\DocumentationSetting::create([
                    'doc_name' => $doc['doc_name'],
                    'path' => $doc['path'],
                    'is_visible' => false,
                    'icon' => \App\Models\DocumentationSetting::getDefaultIcon($doc['doc_name']),
                ]);
            }
        }

        // Optionally: remove docs that no longer exist
        $scannedNames = array_column($scanned, 'doc_name');
        \App\Models\DocumentationSetting::whereNotIn('doc_name', $scannedNames)
            ->where('doc_name', '!=', 'settings')  // Don't delete the settings entry
            ->delete();
    }
}
