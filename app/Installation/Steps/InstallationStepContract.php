<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;

/**
 * Contrat pour toutes les étapes d'installation.
 */
interface InstallationStepContract
{
    /**
     * Exécute l'étape d'installation.
     *
     * @param ModuleRegistry $moduleRegistry Registre des modules
     *
     * @return array ['success' => bool, 'message' => string, 'errors' => array]
     */
    public function execute(ModuleRegistry $moduleRegistry): array;

    /**
     * Rollback de l'étape en cas d'erreur (optionnel).
     *
     * @param ModuleRegistry $moduleRegistry Registre des modules
     *
     * @return void
     */
    public function rollback(ModuleRegistry $moduleRegistry): void;

    /**
     * Obtient la description de l'étape.
     *
     * @return string
     */
    public function getDescription(): string;
}
