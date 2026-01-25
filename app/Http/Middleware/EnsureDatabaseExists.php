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
        }

        // NE PAS créer les tables ici - laisser les migrations de Laravel le faire
        // Cela évite les conflits "table already exists" pendant les migrations
        // Le middleware ne doit que s'assurer que la BD et le fichier existent
    }
}
