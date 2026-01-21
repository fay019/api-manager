<?php

namespace App\Console\Commands;

use App\Installation\InstallationManager;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'install
                            {--env= : Environnement (development, production)}
                            {--force : Force l\'installation même si déjà installée}
                            {--step= : Exécuter une étape spécifique}
                            {--skip-seeds : Passer les seeders}';

    protected $description = 'Installation complète de l\'application';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $this->info('🚀 Démarrage de l\'installation de l\'application');
        $this->newLine();

        // Afficher les informations
        $this->info('Application: ' . config('app.name'));
        $this->info('Environment: ' . app()->environment());
        $this->info('PHP Version: ' . PHP_VERSION);
        $this->newLine();

        $manager = new InstallationManager();

        // Si une étape spécifique est demandée
        if ($step = $this->option('step')) {
            return $this->executeSpecificStep($manager, $step);
        }

        // Installation complète
        $this->info('Exécution de l\'installation complète...');
        $this->newLine();

        $progressBar = $this->output->createProgressBar($manager->getSteps()->count());
        $progressBar->start();

        $results = $manager->install();

        $progressBar->finish();
        $this->newLine(2);

        // Afficher les résultats
        return $this->displayResults($results);
    }

    /**
     * Exécute une étape spécifique.
     */
    protected function executeSpecificStep(InstallationManager $manager, string $stepKey): int
    {
        $this->info("Exécution de l'étape: {$stepKey}");

        $result = $manager->executeSpecificStep($stepKey);

        if ($result['success']) {
            $this->info("✅ Étape réussie: {$stepKey}");
            if (isset($result['result']['message'])) {
                $this->line('   ' . $result['result']['message']);
            }
            return 0;
        } else {
            $this->error("❌ Étape échouée: {$stepKey}");
            if (isset($result['result']['errors'])) {
                foreach ($result['result']['errors'] as $error) {
                    $this->line('   ❌ ' . $error);
                }
            }
            return 1;
        }
    }

    /**
     * Affiche les résultats de l'installation.
     */
    protected function displayResults(array $results): int
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('RÉSUMÉ DE L\'INSTALLATION');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        $success = $results['success'];
        $status = $results['status'];

        $statusEmoji = $success ? '✅' : '❌';
        $statusText = $success ? 'RÉUSSIE' : 'ÉCHOUÉE';

        $this->line($statusEmoji . ' Statut: ' . $statusText);
        $this->line('⏱️  Durée: ' . $results['duration_seconds'] . 's');
        $this->line('📊 Étapes: ' . $results['steps_completed'] . '/' . $results['steps_total']);
        $this->newLine();

        // Afficher les détails des étapes
        if (!empty($results['results'])) {
            $this->info('Détails des étapes:');
            $this->newLine();

            foreach ($results['results'] as $step => $result) {
                $stepSuccess = $result['success'] ?? false;
                $emoji = $stepSuccess ? '✅' : '❌';

                $this->line("{$emoji} {$step}");

                if ($stepSuccess && isset($result['message'])) {
                    $this->line("   └─ {$result['message']}");
                } elseif (!$stepSuccess && isset($result['errors'])) {
                    foreach ($result['errors'] as $error) {
                        $this->line("   └─ ❌ {$error}");
                    }
                }
            }
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════');

        if ($success) {
            $this->newLine();
            $this->info('✨ Application prête! Vous pouvez démarrer le serveur avec:');
            $this->line('   php artisan serve');
            $this->newLine();
        }

        return $success ? 0 : 1;
    }
}
