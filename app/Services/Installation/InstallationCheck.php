<?php

namespace App\Services\Installation;

use App\Contracts\Installation\InstallationCheckInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Service pour vérifier l'état d'installation de l'application.
 *
 * Ce service détermine:
 * - Si l'application est complètement installée
 * - Cohérence de l'installation (fichiers, DB, admin user)
 * - Informations d'installation (timestamp, versions)
 *
 * @see InstallationCheckInterface Pour l'interface publique
 *
 * @example
 * $checker = new InstallationCheck();
 * if (!$checker->isInstalled()) {
 *     redirect('/setup');
 * }
 */
class InstallationCheck implements InstallationCheckInterface
{
    /**
     * Chemin du fichier verrou d'installation.
     */
    private string $lockFilePath;

    /**
     * Initialise le vérificateur d'installation.
     */
    public function __construct()
    {
        $this->lockFilePath = storage_path('app/installed.lock');
    }

    /**
     * {@inheritDoc}
     */
    public function isInstalled(): bool
    {
        // Vérifier que le fichier lock existe et est readable
        if (! file_exists($this->lockFilePath) || ! is_readable($this->lockFilePath)) {
            return false;
        }

        try {
            // Parser le contenu JSON
            $content = file_get_contents($this->lockFilePath);
            $data = json_decode($content, true);

            // Valider structure JSON
            if (! is_array($data) || ! isset($data['installed_at'])) {
                return false;
            }

            // Si le fichier existe et a la structure, c'est bon
            // (Validation hash optionnelle pour plus tard)
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validateIntegrity(): array
    {
        $errors = [];
        $warnings = [];
        $details = [];

        // Vérifier installed.lock
        if (! file_exists($this->lockFilePath)) {
            $errors[] = 'Fichier installed.lock manquant';
        } else {
            $details['lock_file'] = [
                'exists' => true,
                'readable' => is_readable($this->lockFilePath),
                'path' => $this->lockFilePath,
            ];

            if (! is_readable($this->lockFilePath)) {
                $errors[] = 'Fichier installed.lock non lisible';
            }
        }

        // Vérifier .env
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            $errors[] = 'Fichier .env manquant';
        } else {
            $details['env_file'] = [
                'exists' => true,
                'readable' => is_readable($envPath),
                'writable' => is_writable($envPath),
            ];

            if (! is_readable($envPath)) {
                $errors[] = 'Fichier .env non lisible';
            }
        }

        // Vérifier APP_KEY configuré
        try {
            $appKey = config('app.key');
            if (empty($appKey) || $appKey === 'base64:') {
                $errors[] = 'APP_KEY non configuré';
            } else {
                $details['app_key'] = ['configured' => true];
            }
        } catch (\Exception $e) {
            $errors[] = 'Impossible lire APP_KEY';
        }

        // Vérifier connexion base de données
        try {
            DB::connection()->getPdo();
            $details['database'] = [
                'connected' => true,
                'driver' => config('database.default'),
            ];
        } catch (\Exception $e) {
            $errors[] = 'Connexion base de données échouée: '.$e->getMessage();
            $details['database'] = ['connected' => false, 'error' => $e->getMessage()];
        }

        // Vérifier table users (si DB connectée)
        if (empty($errors) || ! str_contains(implode(' ', $errors), 'base de données')) {
            try {
                $usersTable = DB::getSchemaBuilder()->hasTable('users');
                if (! $usersTable) {
                    $warnings[] = 'Table users inexistante';
                } else {
                    $details['users_table'] = ['exists' => true];
                }
            } catch (\Exception $e) {
                $warnings[] = 'Impossible vérifier table users: '.$e->getMessage();
            }
        }

        // Vérifier admin user existe (si table users existe)
        if (! isset($errors) || ! str_contains(implode(' ', $errors), 'Table users')) {
            try {
                $adminExists = User::whereIsAdmin(true)->exists();
                if (! $adminExists) {
                    $warnings[] = 'Aucun utilisateur admin détecté';
                } else {
                    $details['admin_user'] = ['exists' => true];
                }
            } catch (\Exception $e) {
                $warnings[] = 'Impossible vérifier admin: '.$e->getMessage();
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'details' => $details,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallationInfo(): ?array
    {
        if (! file_exists($this->lockFilePath)) {
            return null;
        }

        try {
            $content = file_get_contents($this->lockFilePath);

            return json_decode($content, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getInstalledAt(): ?\Carbon\Carbon
    {
        $info = $this->getInstallationInfo();

        if (! $info || ! isset($info['installed_at'])) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($info['installed_at']);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function reset(bool $resetDatabase = false): void
    {
        // Supprimer lock file
        if (file_exists($this->lockFilePath)) {
            if (! unlink($this->lockFilePath)) {
                throw new \Exception("Impossible supprimer {$this->lockFilePath}");
            }
        }

        // Reset base de données si demandé
        if ($resetDatabase) {
            try {
                // Exécuter migrate:reset
                \Illuminate\Support\Facades\Artisan::call('migrate:reset', ['--force' => true]);
            } catch (\Exception $e) {
                throw new \Exception("Erreur reset DB: {$e->getMessage()}");
            }
        }
    }

    /**
     * Valide l'intégrité du hash dans le lock file.
     *
     * Le hash est basé sur:
     * - APP_KEY (si disponible)
     * - first admin user ID (si table users existe)
     * - installed_at timestamp
     *
     * Cela empêche une réinstallation accidentelle après suppression lock file,
     * car le hash ne peut pas être falsifié sans connaître APP_KEY.
     *
     * @param  array  $data  Contenu du lock file
     * @return bool True si hash valide
     */
    private function validateHash(array $data): bool
    {
        // Si pas d'APP_KEY en config, ignorer validation hash
        if (empty(config('app.key'))) {
            return false;
        }

        try {
            // Récupérer premier admin user
            $firstAdmin = User::whereIsAdmin(true)->orderBy('id')->first();
            if (! $firstAdmin) {
                return false; // Pas d'admin trouvé, installation incohérente
            }

            // Calculer hash attendu
            $hashInput = config('app.key').'|'.$firstAdmin->id.'|'.($data['installed_at'] ?? '');
            $expectedHash = hash('sha256', $hashInput);

            // Comparer
            return $data['hash'] === $expectedHash;
        } catch (\Exception $e) {
            // Si erreur lors validation, considérer comme invalide
            return false;
        }
    }

    /**
     * Crée le fichier lock avec intégrité hash.
     *
     * Cette méthode est appelée en fin d'installation pour verrouiller la setup.
     *
     * @return bool True si succès
     *
     * @throws \Exception Si création échoue
     */
    public function createLock(): bool
    {
        try {
            // Récupérer premier admin
            $firstAdmin = User::whereIsAdmin(true)->orderBy('id')->first();
            if (! $firstAdmin) {
                throw new \Exception('Aucun admin détecté pour créer lock');
            }

            // Calculer hash intégrité
            $installedAt = now()->toIso8601String();
            $hashInput = config('app.key').'|'.$firstAdmin->id.'|'.$installedAt;
            $hash = hash('sha256', $hashInput);

            // Préparer données lock
            $data = [
                'installed_at' => $installedAt,
                'hash' => $hash,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database' => config('database.default'),
                'first_admin_id' => $firstAdmin->id,
            ];

            // Créer répertoire s'il n'existe pas
            $dir = dirname($this->lockFilePath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Écrire fichier
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if (file_put_contents($this->lockFilePath, $json) === false) {
                throw new \Exception("Impossible écrire lock file: {$this->lockFilePath}");
            }

            // Définir permissions lisibles (mais pas modifiables par web user)
            chmod($this->lockFilePath, 0644);

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Erreur création lock: {$e->getMessage()}");
        }
    }
}
