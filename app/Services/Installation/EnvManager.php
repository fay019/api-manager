<?php

namespace App\Services\Installation;

use App\Contracts\Installation\EnvManagerInterface;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

/**
 * Service pour gérer le fichier .env pendant l'installation.
 *
 * Ce service gère:
 * - Création .env depuis .env.example
 * - Lecture/écriture clés .env
 * - Parsing avec gestion quotes et commentaires
 * - Write atomique (temp file + rename)
 * - Backup automatiques
 * - Cache config flush
 *
 * @see EnvManagerInterface Pour l'interface publique
 *
 * @example
 * $envManager = new EnvManager();
 * $envManager->update(['APP_NAME' => 'API Manager']);
 * $envManager->flushCache(); // Important après modifications
 */
class EnvManager implements EnvManagerInterface
{
    /**
     * Chemin du fichier .env.
     */
    private string $envPath;

    /**
     * Chemin du fichier .env.example.
     */
    private string $envExamplePath;

    /**
     * Répertoire pour stocker les backups.
     */
    private string $backupDir;

    /**
     * Contenu .env parsé en mémoire (cache local).
     *
     * @var array<string, string|null>|null
     */
    private ?array $cachedEnv = null;

    /**
     * Nombre maximum de backups à garder.
     *
     * @var int
     */
    private const MAX_BACKUPS = 5;

    /**
     * Initialise le gestionnaire .env.
     *
     * Le constructeur établit les chemins et prépare les répertoires.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
        $this->backupDir = storage_path('app/backups');

        // Créer répertoire backups s'il n'existe pas
        if (! is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function envExists(bool $createIfMissing = true): bool
    {
        $exists = file_exists($this->envPath) && is_readable($this->envPath);

        // Si absent et créer demandé, essayer créer
        if (! $exists && $createIfMissing) {
            try {
                $this->create();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return $exists;
    }

    /**
     * {@inheritDoc}
     */
    public function create(): bool
    {
        // Vérifier .env.example existe
        if (! file_exists($this->envExamplePath)) {
            throw new \Exception('Fichier .env.example manquant (clone Git incomplet)');
        }

        // Vérifier .env n'existe pas déjà (ne pas overwrite)
        if (file_exists($this->envPath)) {
            return true; // Silencieusement OK si existe déjà
        }

        try {
            // Copier .env.example → .env
            copy($this->envExamplePath, $this->envPath);

            // Définir permissions lisibles/écrivables
            chmod($this->envPath, 0644);

            // Vider cache local
            $this->cachedEnv = null;

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Impossible créer .env: {$e->getMessage()}");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return $all[$key] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $values): bool
    {
        // Vérifier que .env existe
        if (! $this->envExists()) {
            throw new \Exception('.env n\'existe pas');
        }

        try {
            // Lire contenu actuel
            $content = file_get_contents($this->envPath);

            // Pour chaque valeur à updater
            foreach ($values as $key => $value) {
                // Ignorer les clés null (pour supprimer)
                if ($value === null) {
                    continue;
                }

                // Formatter la valeur (quotes si espaces)
                $formatted = $this->formatValue($value);

                // Si clé existe déjà, la replacer
                if (preg_match("/^{$key}=/m", $content)) {
                    // Pattern: KEY=old_value (gérer quotes, espaces, etc)
                    $pattern = "/^{$key}=.*/m";
                    $content = preg_replace($pattern, "{$key}={$formatted}", $content);
                } else {
                    // Clé n'existe pas, l'ajouter en fin de fichier
                    $content .= "\n{$key}={$formatted}";
                }
            }

            // Write atomique: créer temp file, puis rename
            $tmpPath = $this->envPath.'.tmp.'.uniqid();

            if (file_put_contents($tmpPath, $content) === false) {
                throw new \Exception('Impossible écrire fichier temporaire');
            }

            // Renamer atomiquement
            if (! rename($tmpPath, $this->envPath)) {
                @unlink($tmpPath);
                throw new \Exception('Impossible renommer fichier');
            }

            // Vider cache local
            $this->cachedEnv = null;

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors update .env: {$e->getMessage()}");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        // Retourner cache local si disponible
        if ($this->cachedEnv !== null) {
            return $this->cachedEnv;
        }

        // Parser le fichier
        if (! file_exists($this->envPath)) {
            return [];
        }

        $content = file_get_contents($this->envPath);
        $lines = explode("\n", $content);
        $env = [];

        foreach ($lines as $line) {
            // Ignorer lignes vides et commentaires
            if (empty(trim($line)) || strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parser KEY=VALUE
            if (! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);

            // Parser value (gérer quotes)
            $value = trim($value);

            // Supprimer quotes si présentes
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            $env[$key] = $value === '' ? null : $value;
        }

        // Mettre en cache
        $this->cachedEnv = $env;

        return $env;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(): array
    {
        $all = $this->all();
        $errors = [];
        $warnings = [];
        $missingKeys = [];

        // Clés obligatoires
        $requiredKeys = [
            'APP_NAME',
            'APP_ENV',
            'APP_KEY',
            'APP_URL',
            'DB_CONNECTION',
            'DB_DATABASE',
        ];

        // Vérifier clés obligatoires
        foreach ($requiredKeys as $key) {
            if (! isset($all[$key]) || $all[$key] === '' || $all[$key] === null) {
                $missingKeys[] = $key;
                $errors[] = "Clé obligatoire manquante: {$key}";
            }
        }

        // Vérifier APP_DEBUG cohérent avec APP_ENV
        if (isset($all['APP_ENV']) && $all['APP_ENV'] === 'production' &&
            isset($all['APP_DEBUG']) && $all['APP_DEBUG'] === 'true') {
            $warnings[] = 'APP_DEBUG devrait être false en production';
        }

        // Vérifier APP_URL format
        if (isset($all['APP_URL'])) {
            if (! filter_var($all['APP_URL'], FILTER_VALIDATE_URL)) {
                $errors[] = 'APP_URL format invalide: '.$all['APP_URL'];
            }
        }

        // Vérifier DB_CONNECTION valide
        if (isset($all['DB_CONNECTION'])) {
            if (! in_array($all['DB_CONNECTION'], ['sqlite', 'mysql', 'pgsql'])) {
                $errors[] = 'DB_CONNECTION invalide: '.$all['DB_CONNECTION'];
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'missing_keys' => $missingKeys,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function backup(): string
    {
        if (! file_exists($this->envPath)) {
            throw new \Exception('.env n\'existe pas, impossible backup');
        }

        // Créer nom backup avec timestamp
        $timestamp = now()->format('Y-m-d-H-i-s');
        $backupPath = "{$this->backupDir}/.env.backup-{$timestamp}";

        try {
            // Copier fichier
            copy($this->envPath, $backupPath);

            // Nettoyer anciens backups si > MAX
            $this->cleanOldBackups();

            return $backupPath;
        } catch (\Exception $e) {
            throw new \Exception("Erreur créer backup: {$e->getMessage()}");
        }
    }

    /**
     * Supprime les anciens backups pour garder max MAX_BACKUPS.
     */
    private function cleanOldBackups(): void
    {
        if (! is_dir($this->backupDir)) {
            return;
        }

        $files = array_diff(scandir($this->backupDir), ['.', '..']);
        $backups = array_filter($files, fn ($f) => str_starts_with($f, '.env.backup-'));

        // Si plus de MAX backups, supprimer les plus anciens
        if (count($backups) > self::MAX_BACKUPS) {
            // Trier par date (filename)
            rsort($backups);

            // Supprimer files au-delà du max
            foreach (array_slice($backups, self::MAX_BACKUPS) as $oldBackup) {
                @unlink("{$this->backupDir}/{$oldBackup}");
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function restore(string $backupFile): bool
    {
        $backupPath = str_starts_with($backupFile, '/')
            ? $backupFile
            : "{$this->backupDir}/{$backupFile}";

        if (! file_exists($backupPath)) {
            throw new \Exception("Fichier backup absent: {$backupFile}");
        }

        try {
            // Créer backup du .env actuel (avant restore)
            if (file_exists($this->envPath)) {
                $this->backup();
            }

            // Copier backup → .env
            copy($backupPath, $this->envPath);
            chmod($this->envPath, 0644);

            // Vider cache local
            $this->cachedEnv = null;

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Erreur restore backup: {$e->getMessage()}");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function listBackups(): array
    {
        if (! is_dir($this->backupDir)) {
            return [];
        }

        $files = array_diff(scandir($this->backupDir), ['.', '..']);
        $backups = [];

        foreach ($files as $file) {
            if (str_starts_with($file, '.env.backup-')) {
                // Extraire timestamp du filename
                $timestamp = str_replace(['.env.backup-', '.txt'], '', $file);
                $backups[$file] = $timestamp;
            }
        }

        return $backups;
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): void
    {
        $this->cachedEnv = null;
    }

    /**
     * {@inheritDoc}
     */
    public function flushCache(): void
    {
        // Vider cache local
        $this->cachedEnv = null;

        try {
            // Essayer exécuter config:cache
            // Note: Utiliser Process au lieu d'Artisan pour éviter issues lifecycle
            $process = new Process([PHP_BINARY, 'artisan', 'config:cache']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(10);
            $process->run();
        } catch (\Exception $e) {
            // Ignorer erreur (peut ne pas être disponible)
        }

        // Vider opcache si disponible
        if (function_exists('opcache_reset')) {
            try {
                @opcache_reset();
            } catch (\Exception $e) {
                // Ignorer erreur
            }
        }
    }

    /**
     * Formate une valeur pour écriture en .env (gère quotes, escaping).
     *
     * Règles:
     * - Si contient espaces → entourer de quotes
     * - Si contient = ou # → entourer de quotes (pour éviter confusion parser)
     * - Garder null comme string vide
     *
     * @param  mixed  $value  Valeur à formatter
     * @return string Valeur formatée
     */
    private function formatValue(mixed $value): string
    {
        if ($value === null || $value === '' || $value === false) {
            return '';
        }

        if ($value === true) {
            return 'true';
        }

        $value = (string) $value;

        // Determiner si quotes nécessaires
        $needsQuotes = str_contains($value, ' ') ||
                      str_contains($value, '=') ||
                      str_contains($value, '#');

        if ($needsQuotes) {
            // Escaper quotes internes et backslashes
            $value = str_replace('\\', '\\\\', $value);
            $value = str_replace('"', '\\"', $value);
            $value = "\"{$value}\"";
        }

        return $value;
    }
}
