<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Middleware pour s'assurer que la base de données et le fichier .env existent.
 *
 * S'exécute avant la session pour:
 * - Créer .env depuis .env.example si manquant
 * - Créer SQLite si nécessaire
 * - Créer la table sessions
 */
class EnsureDatabaseExists
{
    /**
     * Exécute le middleware (avant la session).
     */
    public function handle(Request $request, Closure $next)
    {
        // S'assurer que .env existe (créé depuis .env.example si manquant)
        $this->ensureEnvExists();

        // Créer la base de données SQLite si nécessaire
        if (config('database.default') === 'sqlite') {
            $this->ensureSqliteExists();
        }

        // Vérifier la connexion à la base de données
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            // Continuer même si la connexion échoue (durant l'installation)
        }

        return $next($request);
    }

    /**
     * Crée le fichier .env depuis .env.example s'il manque.
     */
    protected function ensureEnvExists(): void
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        // Si .env existe déjà, rien à faire
        if (file_exists($envPath)) {
            return;
        }

        // Si .env.example n'existe pas, on ne peut rien faire
        if (!file_exists($envExamplePath)) {
            return;
        }

        // Copier .env.example vers .env
        try {
            copy($envExamplePath, $envPath);
        } catch (\Exception $e) {
            // Silencieusement ignorer si la copie échoue
            // (permissions insuffisantes, etc.)
        }
    }

    /**
     * Crée le fichier SQLite et les tables nécessaires.
     */
    protected function ensureSqliteExists(): void
    {
        $dbPath = config('database.connections.sqlite.database');

        // Créer le fichier database.sqlite s'il n'existe pas
        if (!file_exists($dbPath)) {
            // Créer le répertoire s'il n'existe pas
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Créer le fichier vide
            touch($dbPath);

            // Créer les tables minimales pour la session
            try {
                $pdo = new \PDO("sqlite:{$dbPath}");
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                // Table sessions (pour SESSION_DRIVER=database)
                $pdo->exec('
                    CREATE TABLE IF NOT EXISTS sessions (
                        id TEXT PRIMARY KEY,
                        user_id INTEGER,
                        ip_address VARCHAR(45),
                        user_agent TEXT,
                        payload LONGTEXT,
                        last_activity INTEGER
                    )
                ');
            } catch (\Exception $e) {
                // Ignorer les erreurs - les migrations vont les créer
            }
        }
    }
}
