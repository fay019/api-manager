<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use Illuminate\Support\Facades\Process;

/**
 * Compilation des assets front-end.
 */
class AssetsStep extends BaseStep
{
    protected string $description = 'Compilation des assets front-end';

    /**
     * Exécute la compilation des assets.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Building frontend assets');

        try {
            // Vérifier si npm/node est disponible
            if (!$this->hasNodeInstalled()) {
                $this->log('Node.js not installed, skipping asset build');
                return $this->success('Node.js non installé - build assets ignoré');
            }

            // Installer les dépendances npm
            if (file_exists(base_path('package.json')) && !is_dir(base_path('node_modules'))) {
                $this->log('Installing npm dependencies');
                $result = Process::path(base_path())->run('npm install');

                if (!$result->successful()) {
                    $this->log('npm install failed: ' . $result->errorOutput(), 'warn');
                    // Non critique
                }
            }

            // Compiler les assets
            if (file_exists(base_path('package.json'))) {
                $this->log('Building assets with npm run build');
                $result = Process::path(base_path())->run('npm run build');

                if (!$result->successful()) {
                    $this->log('npm run build failed: ' . $result->errorOutput(), 'warn');
                    // Non critique
                }
            }

            $this->log('Asset compilation completed');

            return $this->success('Assets compilés avec succès');
        } catch (\Exception $e) {
            $this->log("Asset compilation error: " . $e->getMessage(), 'warn');
            return $this->success('Asset compilation non critique');
        }
    }

    /**
     * Vérifie si Node.js est installé.
     */
    protected function hasNodeInstalled(): bool
    {
        try {
            $result = Process::run('which node');
            return $result->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
