<?php

namespace App\Installation;

use App\Installation\Modules\ModuleRegistry;
use App\Installation\Steps\InstallationStepContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrateur principal du processus d'installation.
 *
 * Cette classe gère le flux d'installation complet avec:
 * - Exécution séquentielle des étapes
 * - Validation et gestion des erreurs
 * - Logging détaillé
 * - Rollback en cas d'erreur critique
 */
class InstallationManager
{
    /**
     * Collection des étapes d'installation.
     *
     * @var Collection
     */
    protected Collection $steps;

    /**
     * Résultats de l'installation.
     *
     * @var Collection
     */
    protected Collection $results;

    /**
     * Registre des modules.
     *
     * @var ModuleRegistry
     */
    protected ModuleRegistry $moduleRegistry;

    /**
     * Horodatage du début de l'installation.
     *
     * @var \DateTime
     */
    protected \DateTime $startTime;

    /**
     * État de l'installation.
     *
     * @var string
     */
    protected string $status = 'idle';

    /**
     * Étapes complétées avec succès.
     *
     * @var Collection
     */
    protected Collection $completed;

    /**
     * Constructeur.
     */
    public function __construct()
    {
        $this->steps = new Collection();
        $this->results = new Collection();
        $this->completed = new Collection();
        $this->moduleRegistry = new ModuleRegistry();
        $this->loadSteps();
    }

    /**
     * Charge les étapes d'installation depuis la configuration.
     */
    protected function loadSteps(): void
    {
        $stepsConfig = config('installation.steps', []);

        foreach ($stepsConfig as $key => $config) {
            if (!isset($config['class'])) {
                continue;
            }

            $this->steps->put($key, [
                'key' => $key,
                'class' => $config['class'],
                'description' => $config['description'] ?? '',
                'critical' => $config['critical'] ?? false,
            ]);
        }
    }

    /**
     * Exécute l'installation complète.
     *
     * @return array ['success' => bool, 'results' => array]
     */
    public function install(): array
    {
        $this->startTime = now();
        $this->status = 'running';

        $this->log('Installation starting', 'info');

        try {
            foreach ($this->steps as $key => $stepConfig) {
                if (!$this->executeStep($key, $stepConfig)) {
                    if ($stepConfig['critical']) {
                        $this->status = 'failed';
                        $this->log("Installation failed at critical step: {$key}", 'error');
                        return $this->getResults();
                    }
                    // Continuer si l'étape n'est pas critique
                }
            }

            $this->status = 'completed';
            $this->log('Installation completed successfully', 'info');

            return $this->getResults();
        } catch (\Exception $e) {
            $this->status = 'failed';
            $this->log("Installation failed with exception: " . $e->getMessage(), 'error');
            $this->handleRollback();

            return $this->getResults();
        }
    }

    /**
     * Exécute une étape spécifique.
     *
     * @param string $key Clé de l'étape
     * @param array  $stepConfig Configuration de l'étape
     *
     * @return bool true si succès
     */
    protected function executeStep(string $key, array $stepConfig): bool
    {
        $this->log("Starting step: {$key}", 'info');

        try {
            $step = app($stepConfig['class']);

            if (!($step instanceof InstallationStepContract)) {
                throw new \Exception("Step {$key} doit implémenter InstallationStepContract");
            }

            // Exécuter l'étape
            $result = $step->execute($this->moduleRegistry);

            if (!$result['success']) {
                $this->log("Step {$key} failed: " . implode(', ', $result['errors'] ?? []), 'error');
                $this->results->put($key, $result);
                return false;
            }

            $this->completed->push($key);
            $this->results->put($key, $result);
            $this->log("Step {$key} completed successfully", 'info');

            return true;
        } catch (\Exception $e) {
            $this->log("Step {$key} threw exception: " . $e->getMessage(), 'error');
            $this->results->put($key, [
                'success' => false,
                'errors' => [$e->getMessage()],
            ]);

            return false;
        }
    }

    /**
     * Exécute une étape spécifique par sa clé.
     *
     * @param string $stepKey Clé de l'étape à exécuter
     *
     * @return array
     */
    public function executeSpecificStep(string $stepKey): array
    {
        if (!$this->steps->has($stepKey)) {
            return [
                'success' => false,
                'error' => "Étape '{$stepKey}' non trouvée",
            ];
        }

        $stepConfig = $this->steps->get($stepKey);
        $success = $this->executeStep($stepKey, $stepConfig);

        return [
            'success' => $success,
            'step' => $stepKey,
            'result' => $this->results->get($stepKey),
        ];
    }

    /**
     * Gère le rollback en cas d'erreur.
     */
    protected function handleRollback(): void
    {
        if (!config('installation.rollback.enabled', true)) {
            return;
        }

        $this->log('Starting rollback', 'warn');

        // Rollback dans l'ordre inverse
        $completed = $this->completed->reverse();

        foreach ($completed as $key) {
            try {
                $stepConfig = $this->steps->get($key);
                $step = app($stepConfig['class']);

                if (method_exists($step, 'rollback')) {
                    $step->rollback($this->moduleRegistry);
                    $this->log("Rolled back step: {$key}", 'info');
                }
            } catch (\Exception $e) {
                $this->log("Error rolling back {$key}: " . $e->getMessage(), 'error');
            }
        }
    }

    /**
     * Obtient les résultats de l'installation.
     *
     * @return array
     */
    public function getResults(): array
    {
        $duration = $this->startTime ? now()->diffInSeconds($this->startTime) : 0;

        return [
            'success' => $this->status === 'completed',
            'status' => $this->status,
            'timestamp' => now()->toIso8601String(),
            'duration_seconds' => $duration,
            'steps_completed' => $this->completed->count(),
            'steps_total' => $this->steps->count(),
            'results' => $this->results->toArray(),
        ];
    }

    /**
     * Obtient le statut actuel.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Obtient le registre des modules.
     *
     * @return ModuleRegistry
     */
    public function getModuleRegistry(): ModuleRegistry
    {
        return $this->moduleRegistry;
    }

    /**
     * Obtient les étapes d'installation.
     *
     * @return Collection
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    /**
     * Enregistre un message dans les logs.
     *
     * @param string $message Message à enregistrer
     * @param string $level   Niveau de log
     */
    protected function log(string $message, string $level = 'info'): void
    {
        $prefix = '[Installation] ';

        Log::{$level}($prefix . $message);

        // Log aussi dans le fichier d'installation spécifique
        if (config('installation.logging.enabled', true)) {
            $logFile = config('installation.logging.file', 'storage/logs/installation.log');
            $timestamp = now()->format('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] [{$level}] {$prefix}{$message}\n";

            if (!is_dir(dirname(base_path($logFile)))) {
                mkdir(dirname(base_path($logFile)), 0755, true);
            }

            file_put_contents(base_path($logFile), $logMessage, FILE_APPEND);
        }
    }

    /**
     * Invalide le cache des modules.
     */
    public function invalidateModuleCache(): void
    {
        $this->moduleRegistry->invalidateCache();
    }
}
