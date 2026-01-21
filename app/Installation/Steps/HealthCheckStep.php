<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use App\Installation\Validators\DatabaseValidator;
use App\Installation\Validators\EnvironmentValidator;

/**
 * Vérification finale de la santé de l'application.
 */
class HealthCheckStep extends BaseStep
{
    protected string $description = 'Vérification de la santé de l\'application';

    /**
     * Exécute la vérification de santé.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Running health checks');

        try {
            $healthChecks = [];
            $allHealthy = true;

            // Vérifier l'environnement
            $envCheck = EnvironmentValidator::validateComplete();
            $healthChecks['environment'] = $envCheck['valid'];
            if (!$envCheck['valid']) {
                $allHealthy = false;
            }

            // Vérifier la base de données
            $dbCheck = DatabaseValidator::validateComplete();
            $healthChecks['database'] = $dbCheck['valid'];
            if (!$dbCheck['valid']) {
                $allHealthy = false;
            }

            // Vérifier les modules
            $moduleValidation = $moduleRegistry->validateDependencies();
            $healthChecks['modules'] = $moduleValidation['valid'];
            if (!$moduleValidation['valid']) {
                $allHealthy = false;
            }

            // Vérifier les fichiers de cache
            $cachePathExists = is_dir(base_path('bootstrap/cache'));
            $healthChecks['cache_directory'] = $cachePathExists;

            // Vérifier l'accès au stockage
            $storageAccessible = is_dir(storage_path());
            $healthChecks['storage_directory'] = $storageAccessible;

            if (!$allHealthy) {
                $errors = [];
                foreach ($healthChecks as $check => $result) {
                    if (!$result) {
                        $errors[] = "Health check failed: {$check}";
                    }
                }

                return $this->failed($errors);
            }

            $this->log('All health checks passed');

            return $this->success(
                'Application en bonne santé - Installation réussie!',
                [
                    'health_checks' => $healthChecks,
                    'status' => 'healthy',
                ]
            );
        } catch (\Exception $e) {
            return $this->failed([$e->getMessage()]);
        }
    }
}
