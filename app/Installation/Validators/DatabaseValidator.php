<?php

namespace App\Installation\Validators;

use Illuminate\Support\Facades\DB;
use PDOException;

/**
 * Validateur de base de données.
 */
class DatabaseValidator
{
    /**
     * Valide la connexion à la base de données.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateConnection(): array
    {
        try {
            DB::connection()->getPdo();

            return [
                'valid' => true,
                'database' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'errors' => [],
            ];
        } catch (PDOException $e) {
            return [
                'valid' => false,
                'database' => config('database.default'),
                'errors' => ['Impossible de se connecter à la base de données: ' . $e->getMessage()],
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'database' => config('database.default'),
                'errors' => ['Erreur de connexion: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Valide que les tables ont été créées (migrations exécutées).
     *
     * @return array ['valid' => bool, 'migrated' => bool, 'errors' => array]
     */
    public static function validateMigrations(): array
    {
        try {
            // Vérifier si la table migrations existe
            if (!DB::getSchemaBuilder()->hasTable('migrations')) {
                return [
                    'valid' => false,
                    'migrated' => false,
                    'errors' => ['Les migrations n\'ont pas été exécutées'],
                ];
            }

            // Vérifier si des migrations ont été exécutées
            $count = DB::table('migrations')->count();

            return [
                'valid' => true,
                'migrated' => $count > 0,
                'migration_count' => $count,
                'errors' => $count === 0 ? ['Aucune migration n\'a été exécutée'] : [],
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'migrated' => false,
                'errors' => ['Erreur lors de la vérification des migrations: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Vérifie que les tables essentielles existent.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateEssentialTables(): array
    {
        $required = [
            'users',
            'api_clients',
            'api_keys',
            'promos',
        ];

        $schema = DB::getSchemaBuilder();
        $missing = [];

        foreach ($required as $table) {
            if (!$schema->hasTable($table)) {
                $missing[] = $table;
            }
        }

        return [
            'valid' => empty($missing),
            'required_tables' => count($required),
            'found_tables' => count($required) - count($missing),
            'missing_tables' => $missing,
            'errors' => empty($missing) ? [] : [
                'Tables manquantes: ' . implode(', ', $missing),
            ],
        ];
    }

    /**
     * Vérifie les permissions de la base de données.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePermissions(): array
    {
        try {
            // Tester la création d'une table temporaire
            $testTableName = 'installation_test_' . uniqid();

            DB::statement("CREATE TABLE {$testTableName} (id INT PRIMARY KEY)");
            DB::statement("DROP TABLE {$testTableName}");

            return [
                'valid' => true,
                'can_create_tables' => true,
                'can_drop_tables' => true,
                'errors' => [],
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'can_create_tables' => false,
                'can_drop_tables' => false,
                'errors' => ['Permissions insuffisantes: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Vérifie la taille et la performance de la base de données.
     *
     * @return array
     */
    public static function validatePerformance(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $duration = (microtime(true) - $start) * 1000;

            $healthy = $duration < 100; // Moins de 100ms est bon

            return [
                'valid' => $healthy,
                'response_time_ms' => round($duration, 2),
                'healthy' => $healthy,
                'errors' => $healthy ? [] : ['Temps de réponse de la BD lent: ' . round($duration, 2) . 'ms'],
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'errors' => ['Erreur lors du test de performance: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Effectue une validation complète de la base de données.
     *
     * @return array
     */
    public static function validateComplete(): array
    {
        $results = [];
        $allValid = true;

        // Connexion
        $results['connection'] = self::validateConnection();
        if (!$results['connection']['valid']) {
            $allValid = false;
        }

        // Migrations
        $results['migrations'] = self::validateMigrations();
        if (!$results['migrations']['valid']) {
            $allValid = false;
        }

        // Tables essentielles
        $results['essential_tables'] = self::validateEssentialTables();
        if (!$results['essential_tables']['valid']) {
            $allValid = false;
        }

        // Permissions
        $results['permissions'] = self::validatePermissions();
        if (!$results['permissions']['valid']) {
            $allValid = false;
        }

        // Performance
        $results['performance'] = self::validatePerformance();

        return [
            'valid' => $allValid,
            'results' => $results,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}