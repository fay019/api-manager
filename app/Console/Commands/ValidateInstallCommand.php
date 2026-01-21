<?php

namespace App\Console\Commands;

use App\Installation\Validators\DatabaseValidator;
use App\Installation\Validators\EnvironmentValidator;
use Illuminate\Console\Command;

class ValidateInstallCommand extends Command
{
    protected $signature = 'validate:install
                            {--full : Validation complète incluant la base de données}';

    protected $description = 'Valide que l\'installation est correcte';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $this->info('🔍 Validation de l\'installation');
        $this->newLine();

        $allValid = true;

        // Validation de l'environnement
        $this->info('📋 Validation de l\'environnement système...');
        $envValidation = EnvironmentValidator::validateComplete();
        $allValid = $allValid && $envValidation['valid'];
        $this->displayValidationResults('Environnement', $envValidation);

        // Validation de la base de données
        $this->info('📋 Validation de la base de données...');
        $dbValidation = DatabaseValidator::validateComplete();
        $allValid = $allValid && $dbValidation['valid'];
        $this->displayDbValidationResults('Base de données', $dbValidation);

        $this->newLine();

        if ($allValid) {
            $this->info('✅ Installation valide!');
            return 0;
        } else {
            $this->error('❌ Des problèmes ont été détectés lors de la validation');
            return 1;
        }
    }

    /**
     * Affiche les résultats de validation.
     */
    protected function displayValidationResults(string $title, array $validation): void
    {
        $status = $validation['valid'] ? '✅' : '❌';
        $this->line("{$status} {$title}");

        if (!$validation['valid']) {
            foreach ($validation['results'] as $check => $result) {
                if (!empty($result['errors'])) {
                    foreach ($result['errors'] as $error) {
                        $this->line("   ❌ {$error}");
                    }
                }
            }
        } else {
            foreach ($validation['results'] as $check => $result) {
                if ($result['valid'] ?? true) {
                    $this->line("   ✅ {$check}");
                }
            }
        }

        $this->newLine();
    }

    /**
     * Affiche les résultats de validation de la BD.
     */
    protected function displayDbValidationResults(string $title, array $validation): void
    {
        $status = $validation['valid'] ? '✅' : '❌';
        $this->line("{$status} {$title}");

        foreach ($validation['results'] as $check => $result) {
            if ($result['valid'] ?? true) {
                $this->line("   ✅ {$check}");

                // Afficher les détails pertinents
                if ($check === 'connection' && isset($result['host'])) {
                    $this->line("      └─ Host: {$result['host']}");
                }

                if ($check === 'migrations' && isset($result['migration_count'])) {
                    $this->line("      └─ Migrations exécutées: {$result['migration_count']}");
                }

                if ($check === 'essential_tables' && isset($result['found_tables'])) {
                    $this->line("      └─ Tables: {$result['found_tables']}/{$result['required_tables']}");
                }

                if ($check === 'performance' && isset($result['response_time_ms'])) {
                    $this->line("      └─ Temps de réponse: {$result['response_time_ms']}ms");
                }
            } else {
                foreach ($result['errors'] ?? [] as $error) {
                    $this->line("   ❌ {$error}");
                }
            }
        }

        $this->newLine();
    }
}
