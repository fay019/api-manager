<?php

namespace App\Installation\Steps;

use Illuminate\Support\Facades\Log;

/**
 * Classe de base pour les étapes d'installation.
 */
abstract class BaseStep implements InstallationStepContract
{
    /**
     * Description de l'étape.
     *
     * @var string
     */
    protected string $description = '';

    /**
     * Obtient la description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Rollback par défaut (peut être surchargé).
     */
    public function rollback(\App\Installation\Modules\ModuleRegistry $moduleRegistry): void
    {
        // À implémenter par les étapes si nécessaire
    }

    /**
     * Enregistre un message de log.
     */
    protected function log(string $message, string $level = 'info'): void
    {
        // Convertir 'warn' en 'warning' pour Monolog
        if ($level === 'warn') {
            $level = 'warning';
        }

        Log::{$level}("[Installation] " . static::class . ": {$message}");
    }

    /**
     * Retourne un succès.
     */
    protected function success(string $message = '', array $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Retourne une erreur.
     */
    protected function failed(array $errors = []): array
    {
        return [
            'success' => false,
            'errors' => $errors,
        ];
    }
}
