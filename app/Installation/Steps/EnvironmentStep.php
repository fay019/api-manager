<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use Illuminate\Support\Facades\Artisan;

/**
 * Configuration de l'environnement.
 */
class EnvironmentStep extends BaseStep
{
    protected string $description = 'Configuration de l\'environnement';

    /**
     * Exécute la configuration de l'environnement.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Setting up environment');

        try {
            // Copier .env.example vers .env si nécessaire
            if (!file_exists(base_path('.env'))) {
                if (file_exists(base_path('.env.example'))) {
                    copy(base_path('.env.example'), base_path('.env'));
                    $this->log('.env créé depuis .env.example');
                } else {
                    return $this->failed(['.env.example non trouvé']);
                }
            }

            // Générer la clé APP_KEY si absent
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate');
                $this->log('APP_KEY générée');
            }

            // Créer le fichier sqlite si nécessaire
            $dbConnection = config('database.default');
            if ($dbConnection === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (!is_file($dbPath)) {
                    touch($dbPath);
                    chmod($dbPath, 0666);
                    $this->log('Base de données SQLite créée: ' . $dbPath);
                }
            }

            $this->log('Environment configuration completed');

            return $this->success('Environnement configuré avec succès');
        } catch (\Exception $e) {
            return $this->failed([$e->getMessage()]);
        }
    }
}
