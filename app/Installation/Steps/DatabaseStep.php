<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use App\Installation\Validators\DatabaseValidator;
use Illuminate\Support\Facades\Artisan;

/**
 * Exécution des migrations de base de données.
 */
class DatabaseStep extends BaseStep
{
    protected string $description = 'Exécution des migrations de base de données';

    /**
     * Exécute les migrations.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Running database migrations');

        try {
            // Valider la connexion avant de procéder
            $validation = DatabaseValidator::validateConnection();
            if (!$validation['valid']) {
                return $this->failed($validation['errors']);
            }

            $this->log('Database connection validated');

            // Exécuter les migrations
            Artisan::call('migrate', [
                '--force' => true,
                '--step' => true, // Exécute les migrations étape par étape pour le débogage
            ]);

            $this->log('Database migrations completed');

            // Valider que les migrations ont été exécutées
            $migrationCheck = DatabaseValidator::validateMigrations();
            if (!$migrationCheck['migrated']) {
                return $this->failed(['Aucune migration n\'a été exécutée']);
            }

            // Valider les tables essentielles
            $tableCheck = DatabaseValidator::validateEssentialTables();
            if (!$tableCheck['valid']) {
                return $this->failed($tableCheck['errors']);
            }

            $this->log('Essential tables verified');

            return $this->success(
                'Migrations exécutées et base de données initialisée',
                [
                    'migrations_count' => $migrationCheck['migration_count'],
                    'tables_created' => $tableCheck['found_tables'],
                ]
            );
        } catch (\Exception $e) {
            return $this->failed([$e->getMessage()]);
        }
    }

    /**
     * Rollback des migrations.
     */
    public function rollback(ModuleRegistry $moduleRegistry): void
    {
        $this->log('Rolling back migrations');

        try {
            Artisan::call('migrate:rollback', ['--force' => true]);
            $this->log('Migrations rolled back');
        } catch (\Exception $e) {
            $this->log("Error rolling back migrations: " . $e->getMessage(), 'error');
        }
    }
}
