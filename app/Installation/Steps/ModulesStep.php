<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;

/**
 * Initialisation et découverte des modules.
 */
class ModulesStep extends BaseStep
{
    protected string $description = 'Initialisation et découverte des modules';

    /**
     * Exécute la découverte et initialisation des modules.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Discovering modules');

        try {
            // Invalider le cache existant
            $moduleRegistry->invalidateCache();

            // Redécouvrir les modules
            $moduleRegistry->discoverModules();

            $modules = $moduleRegistry->all();
            $this->log("Found {$modules->count()} modules");

            // Valider les dépendances
            $validation = $moduleRegistry->validateDependencies();
            if (!$validation['valid']) {
                return $this->failed($validation['errors']);
            }

            // Appeler le hook onInstall pour chaque module
            $modulesInfo = [];
            foreach ($modules as $name => $module) {
                try {
                    $instance = app($module['class']);
                    $instance->onInstall();
                    $modulesInfo[$name] = $instance->getInfo();
                    $this->log("Module {$name} initialized");
                } catch (\Exception $e) {
                    $this->log("Error initializing module {$name}: " . $e->getMessage(), 'warn');
                }
            }

            return $this->success(
                "Modules découverts et initialisés avec succès ({$modules->count()} modules)",
                [
                    'modules_count' => $modules->count(),
                    'modules' => $modulesInfo,
                ]
            );
        } catch (\Exception $e) {
            return $this->failed([$e->getMessage()]);
        }
    }
}
