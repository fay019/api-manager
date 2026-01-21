<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use Illuminate\Support\Facades\Artisan;

/**
 * Remplissage de la base de données avec les données initiales.
 */
class SeedersStep extends BaseStep
{
    protected string $description = 'Remplissage de la base de données avec les données initiales';

    /**
     * Exécute les seeders.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Running seeders');

        try {
            // Vérifier si le seeding est activé
            if (!config('installation.seeding.enabled', true)) {
                $this->log('Seeding disabled, skipping');
                return $this->success('Seeding désactivé');
            }

            // Déterminer l'environnement
            $env = app()->environment();
            $seedingConfig = config('installation.seeding');

            // Vérifier si on doit skipper en production
            if ($env === 'production' && $seedingConfig['skip_in_production']) {
                $this->log('Skipping seeding in production environment');
                return $this->success('Seeding désactivé en production');
            }

            // Exécuter les seeders
            Artisan::call('db:seed', [
                '--force' => true,
            ]);

            $this->log('Seeders executed successfully');

            return $this->success('Base de données initialisée avec les données de départ');
        } catch (\Exception $e) {
            // Le seeding n'étant pas critique, on continue même en cas d'erreur
            $this->log("Seeding error: " . $e->getMessage(), 'warn');
            return $this->success('Seeding partiellement exécuté (non critique)');
        }
    }
}
