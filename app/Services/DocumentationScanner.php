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

        // Define locations to scan
        $locations = [
            // Root files
            [
                'path' => 'README.md',
                'name' => 'readme',
            ],
            [
                'path' => 'DEPLOYMENT.md',
                'name' => 'deployment',
            ],
            // docs directory - scan all .md files
            [
                'dir' => 'docs',
                'extensions' => ['md'],
            ],
        ];

        // Scan specific root files
        foreach ($locations as $location) {
            if (isset($location['path']) && File::exists(base_path($location['path']))) {
                $docs[] = [
                    'doc_name' => $location['name'],
                    'path' => '/' . $location['path'],
                ];
            }
        }

        // Scan docs directory
        $docsPath = base_path('docs');
        if (File::isDirectory($docsPath)) {
            $files = File::files($docsPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'md') {
                    $name = strtolower($file->getFilenameWithoutExtension());

                    // Avoid duplicates
                    if (!in_array($name, array_column($docs, 'doc_name'))) {
                        $docs[] = [
                            'doc_name' => $name,
                            'path' => '/docs/' . $file->getFilename(),
                        ];
                    }
                }
            }
        }

        return $docs;
    }

    /**
     * Get metadata for all available docs
     */
    public static function getMetadata(string $docName): array
    {
        $metadata = [
            'readme' => [
                'label' => 'README Documentation',
                'icon' => '📖',
                'description' => 'Quick start guide with project overview',
            ],
            'api' => [
                'label' => 'API Documentation',
                'icon' => '📡',
                'description' => 'Complete endpoint reference',
            ],
            'database' => [
                'label' => 'Database Documentation',
                'icon' => '🗄️',
                'description' => 'Database schema and relationships',
            ],
            'deployment' => [
                'label' => 'Deployment Documentation',
                'icon' => '🚀',
                'description' => 'Deployment guide for shared hosting',
            ],
        ];

        return $metadata[$docName] ?? [
            'label' => ucfirst(str_replace('_', ' ', $docName)) . ' Documentation',
            'icon' => '📄',
            'description' => ucfirst($docName) . ' documentation',
        ];
    }

    /**
     * Sync documentation files with database
     * Creates/updates entries for all discovered docs
     * IMPORTANT: Only sets is_visible to true for NEW records, preserves existing visibility settings
     */
    public static function sync(): void
    {
        $scanned = self::scan();

        foreach ($scanned as $doc) {
            $existing = \App\Models\DocumentationSetting::where('doc_name', $doc['doc_name'])->first();

            if ($existing) {
                // Update only the path for existing records - preserve is_visible setting
                $existing->update(['path' => $doc['path']]);
            } else {
                // Create new record with is_visible = true
                \App\Models\DocumentationSetting::create([
                    'doc_name' => $doc['doc_name'],
                    'path' => $doc['path'],
                    'is_visible' => true,
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
