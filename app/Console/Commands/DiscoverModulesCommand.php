<?php

namespace App\Console\Commands;

use App\Installation\Modules\ModuleRegistry;
use Illuminate\Console\Command;

class DiscoverModulesCommand extends Command
{
    protected $signature = 'discover:modules
                            {--json : Afficher les résultats en JSON}
                            {--install-order : Afficher l\'ordre d\'installation}';

    protected $description = 'Découvre et affiche tous les modules de l\'application';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $this->info('🔍 Découverte des modules');
        $this->newLine();

        $registry = new ModuleRegistry();

        // Afficher les modules découverts
        $modules = $registry->all();

        if ($modules->isEmpty()) {
            $this->warn('⚠️  Aucun module découvert');
            return 1;
        }

        $this->info("✅ {$modules->count()} module(s) découvert(s):");
        $this->newLine();

        if ($this->option('install-order')) {
            return $this->displayInstallationOrder($registry);
        }

        if ($this->option('json')) {
            return $this->displayAsJson($registry);
        }

        // Affichage tabulaire
        $this->displayModulesTable($registry);

        // Afficher les dépendances
        $this->displayDependencies($registry);

        return 0;
    }

    /**
     * Affiche les modules sous forme de tableau.
     */
    protected function displayModulesTable(ModuleRegistry $registry): void
    {
        $headers = ['Nom', 'Version', 'Description', 'Chemin'];
        $rows = [];

        foreach ($registry->all() as $name => $module) {
            try {
                $instance = app($module['class']);
                $rows[] = [
                    $name,
                    $instance->getVersion(),
                    substr($instance->getDescription(), 0, 40),
                    str_replace(base_path(), '.', $module['path']),
                ];
            } catch (\Exception $e) {
                $rows[] = [$name, 'ERROR', $e->getMessage(), ''];
            }
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    /**
     * Affiche les modules en JSON.
     */
    protected function displayAsJson(ModuleRegistry $registry): int
    {
        $this->line($registry->toJson());
        return 0;
    }

    /**
     * Affiche l'ordre d'installation.
     */
    protected function displayInstallationOrder(ModuleRegistry $registry): int
    {
        $this->info('📋 Ordre d\'installation (basé sur les dépendances):');
        $this->newLine();

        $ordered = $registry->getInstallationOrder();
        $i = 1;

        foreach ($ordered as $name => $module) {
            try {
                $instance = app($module['class']);
                $deps = $instance->getDependencies();
                $depStr = empty($deps) ? 'aucune' : implode(', ', $deps);

                $this->line("{$i}. {$name}");
                $this->line("   └─ Dépendances: {$depStr}");
                $i++;
            } catch (\Exception $e) {
                $this->line("{$i}. {$name} (Erreur: {$e->getMessage()})");
                $i++;
            }
        }

        $this->newLine();

        // Valider les dépendances
        $validation = $registry->validateDependencies();
        if ($validation['valid']) {
            $this->info('✅ Toutes les dépendances sont satisfaites');
        } else {
            $this->error('❌ Problèmes de dépendances détectés:');
            foreach ($validation['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }

        return $validation['valid'] ? 0 : 1;
    }

    /**
     * Affiche les dépendances.
     */
    protected function displayDependencies(ModuleRegistry $registry): void
    {
        $validation = $registry->validateDependencies();

        $this->newLine();
        $this->info('🔗 Validation des dépendances:');

        if ($validation['valid']) {
            $this->line('   ✅ Toutes les dépendances sont satisfaites');
        } else {
            foreach ($validation['errors'] as $error) {
                $this->line("   ❌ {$error}");
            }
        }
    }
}
