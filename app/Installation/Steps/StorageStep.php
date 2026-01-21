<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use Illuminate\Support\Facades\File;

/**
 * Configuration des répertoires de stockage.
 */
class StorageStep extends BaseStep
{
    protected string $description = 'Configuration des répertoires de stockage';

    /**
     * Exécute la configuration du stockage.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Setting up storage directories');

        try {
            $errors = [];

            // Répertoires à créer/vérifier
            $directories = [
                'storage/app',
                'storage/app/public',
                'storage/framework',
                'storage/framework/cache',
                'storage/framework/views',
                'storage/framework/sessions',
                'storage/logs',
                'bootstrap/cache',
                'database',
                'public/storage',
            ];

            foreach ($directories as $directory) {
                $path = base_path($directory);

                if (!is_dir($path)) {
                    if (!mkdir($path, 0755, true)) {
                        $errors[] = "Cannot create directory: {$directory}";
                        continue;
                    }
                    $this->log("Created directory: {$directory}");
                }

                // S'assurer que c'est accessible en écriture
                if (!is_writable($path)) {
                    if (!chmod($path, 0755)) {
                        $errors[] = "Cannot make directory writable: {$directory}";
                    }
                }
            }

            // Créer le lien symbolique public/storage -> storage/app/public
            $link = public_path('storage');
            $target = storage_path('app/public');

            if (!is_link($link)) {
                if (is_dir($link)) {
                    File::deleteDirectory($link);
                }

                // Créer le lien symbolique
                symlink($target, $link);
                $this->log('Symbolic link created: public/storage -> storage/app/public');
            }

            if (!empty($errors)) {
                return $this->failed($errors);
            }

            $this->log('Storage directories configured successfully');

            return $this->success('Répertoires de stockage configurés avec succès', [
                'directories_created' => count($directories),
            ]);
        } catch (\Exception $e) {
            return $this->failed([$e->getMessage()]);
        }
    }
}
