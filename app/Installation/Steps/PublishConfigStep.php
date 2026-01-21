<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use Illuminate\Support\Facades\Artisan;

/**
 * Publication des fichiers de configuration.
 */
class PublishConfigStep extends BaseStep
{
    protected string $description = 'Publication des fichiers de configuration';

    /**
     * Exécute la publication des configs.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Publishing configuration files');

        try {
            // Publier les assets Filament
            Artisan::call('filament:install', ['--panels' => 'admin']);
            $this->log('Filament configuration published');

            // Publier les assets Laravel
            Artisan::call('vendor:publish', [
                '--tag' => 'laravel-assets',
                '--force' => true,
            ]);
            $this->log('Laravel assets published');

            return $this->success('Fichiers de configuration publiés avec succès');
        } catch (\Exception $e) {
            return $this->failed([$e->getMessage()]);
        }
    }
}
