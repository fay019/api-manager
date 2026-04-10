<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Middleware pour s'assurer que la base de données est prête.
 *
 * S'exécute avant la session pour:
 * - Créer SQLite si nécessaire
 * - Créer la table sessions
 *
 * Note: Le fichier .env est créé dans public/index.php AVANT ce middleware
 */
class EnsureDatabaseExists
{
    /**
     * Exécute le middleware (avant la session).
     */
    public function handle(Request $request, Closure $next)
    {
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
     * Crée le fichier SQLite et les tables nécessaires.
     */
    protected function ensureSqliteExists(): void
    {
        $dbPath = config('database.connections.sqlite.database');

        // Créer le répertoire s'il n'existe pas
        $dir = dirname($dbPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Créer le fichier database.sqlite s'il n'existe pas
        if (! file_exists($dbPath)) {
            touch($dbPath);
            chmod($dbPath, 0666);
        }

        // S'assurer que le fichier est scriptable s'il existe déjà
        if (file_exists($dbPath) && ! is_writable($dbPath)) {
            chmod($dbPath, 0666);
        }

        // S'assurer que le répertoire est scriptable pour que SQLite puisse créer ses fichiers journaux (-journal, -wal)
        if (is_dir($dir) && ! is_writable($dir)) {
            chmod($dir, 0777);
        }

        // Créer les tables minimales si elles n'existent pas pour permettre à Laravel de fonctionner
        try {
            $pdo = DB::connection('sqlite')->getPdo();

            // 1. Table sessions
            $hasSessionsTable = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='sessions'")->fetch();
            if (! $hasSessionsTable) {
                $pdo->exec('
                    CREATE TABLE sessions (
                        id VARCHAR(255) PRIMARY KEY,
                        user_id INTEGER,
                        ip_address VARCHAR(45),
                        user_agent TEXT,
                        payload TEXT NOT NULL,
                        last_activity INTEGER NOT NULL
                    )
                ');
                $pdo->exec('CREATE INDEX sessions_last_activity_index ON sessions (last_activity)');
                $pdo->exec('CREATE INDEX sessions_user_id_index ON sessions (user_id)');
            }

            // 2. Table cache (nécessaire pour optimize:clear si driver database/file est configuré)
            $hasCacheTable = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='cache'")->fetch();
            if (! $hasCacheTable) {
                $pdo->exec('
                    CREATE TABLE cache (
                        key VARCHAR(255) PRIMARY KEY,
                        value TEXT NOT NULL,
                        expiration INTEGER NOT NULL
                    )
                ');
            }

            // 3. Table cache_locks (souvent utilisée avec le driver cache)
            $hasCacheLocksTable = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='cache_locks'")->fetch();
            if (! $hasCacheLocksTable) {
                $pdo->exec('
                    CREATE TABLE cache_locks (
                        key VARCHAR(255) PRIMARY KEY,
                        owner VARCHAR(255) NOT NULL,
                        expiration INTEGER NOT NULL
                    )
                ');
            }
        } catch (\Exception $e) {
            // Ignorer si la connexion échoue ici, le handle() principal gérera l'erreur
        }
    }
}
