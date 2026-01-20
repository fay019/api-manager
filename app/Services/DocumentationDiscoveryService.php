<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class DocumentationDiscoveryService
{
    /**
     * Get all available documentation files
     *
     * @return array List of available doc names (without extension)
     */
    public static function getAvailableDocs(): array
    {
        $docs = [];

        // Check docs directory
        $docsPath = base_path('docs');
        if (File::isDirectory($docsPath)) {
            $files = File::files($docsPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'md') {
                    $docs[] = strtolower($file->getFilenameWithoutExtension());
                }
            }
        }

        // Check root markdown files that are treated as docs
        $rootDocs = [
            ['path' => 'README.md', 'name' => 'readme'],
            ['path' => 'DEPLOYMENT.md', 'name' => 'deployment'],
        ];

        foreach ($rootDocs as $doc) {
            if (File::exists(base_path($doc['path']))) {
                if (!in_array($doc['name'], $docs)) {
                    $docs[] = $doc['name'];
                }
            }
        }

        return array_values(array_unique(array_filter($docs)));
    }

    /**
     * Get documentation metadata with nice labels
     *
     * @return array
     */
    public static function getDocumentationMetadata(): array
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

        // Filter to only available docs
        $availableDocs = self::getAvailableDocs();
        $filtered = [];
        foreach ($availableDocs as $doc) {
            if (isset($metadata[$doc])) {
                $filtered[$doc] = $metadata[$doc];
            }
        }

        return $filtered;
    }
}
