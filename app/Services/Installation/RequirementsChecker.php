<?php

namespace App\Services\Installation;

use App\Contracts\Installation\RequirementsCheckerInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Service pour vérifier les prérequis système avant installation.
 *
 * Ce service effectue toutes les vérifications nécessaires avant d'installer
 * l'application. Les résultats sont cachés 5 minutes pour éviter les appels
 * répétés coûteux (vérification permissions, extensions, etc).
 *
 * @see RequirementsCheckerInterface Pour l'interface publique
 *
 * @example
 * $checker = new RequirementsChecker();
 * $results = $checker->check();
 * if (!$results['passed']) {
 *     foreach ($results['errors'] as $error) {
 *         echo "Erreur bloquante: $error";
 *     }
 * }
 */
class RequirementsChecker implements RequirementsCheckerInterface
{
    /**
     * Durée du cache pour les résultats (en minutes).
     */
    private int $cacheDuration = 5;

    /**
     * Clé cache pour stocker les résultats des vérifications.
     */
    private string $cacheKey = 'setup_requirements_check';

    /**
     * PHP version minimale requise.
     *
     * @var string
     */
    private const MIN_PHP_VERSION = '8.3.0';

    /**
     * Extensions PHP obligatoires (toujours requises).
     *
     * @var array<string, string> {extension => description}
     */
    private const REQUIRED_EXTENSIONS = [
        'pdo' => 'Interface d\'accès aux bases de données',
        'mbstring' => 'Manipulation de chaînes multibytes',
        'json' => 'Traitement JSON',
        'ctype' => 'Vérification types caractères',
        'filter' => 'Filtrage des entrées',
        'hash' => 'Fonctions de hachage',
        'openssl' => 'Chiffrement et certificats',
    ];

    /**
     * Extensions PHP optionnelles (selon cas d'usage).
     *
     * @var array<string, string> {extension => description}
     */
    private const OPTIONAL_EXTENSIONS = [
        'pdo_sqlite' => 'Support SQLite',
        'pdo_mysql' => 'Support MySQL/MariaDB',
        'pdo_pgsql' => 'Support PostgreSQL',
        'redis' => 'Support Redis (cache/queue)',
        'memcached' => 'Support Memcached (cache)',
        'gd' => 'Manipulation images (GD)',
        'imagick' => 'Manipulation images (ImageMagick)',
        'curl' => 'Requêtes HTTP/HTTPS',
        'fileinfo' => 'Détection MIME types',
    ];

    /**
     * Répertoires dont on doit vérifier la permission write.
     *
     * @var string[]
     */
    private const REQUIRED_WRITABLE_DIRS = [
        'storage',
        'storage/logs',
        'storage/app',
        'storage/framework',
        'bootstrap/cache',
        '.',  // Racine (pour créer .env)
    ];

    /**
     * {@inheritDoc}
     */
    public function check(): array
    {
        // Retourner du cache si disponible
        if ($cached = $this->getCache()) {
            return $cached;
        }

        // Exécuter toutes les vérifications
        $phpVersion = $this->checkPhpVersion();
        $requiredExt = $this->checkRequiredExtensions();
        $optionalExt = $this->checkOptionalExtensions();
        $permissions = $this->checkPermissions();
        $envFile = $this->checkEnvFile();
        $serverInfo = $this->getServerInfo();

        // Déterminer état global
        $errors = array_merge(
            $phpVersion['passed'] ? [] : [$phpVersion['message']],
            $requiredExt['missing'] ? array_map(
                fn ($ext) => "Extension manquante: {$ext}",
                $requiredExt['missing']
            ) : [],
            $permissions['not_writable'] ? array_map(
                fn ($dir) => "Répertoire non accessible en écriture: {$dir}",
                $permissions['not_writable']
            ) : []
        );

        // Avertissements (non-bloquants)
        $warnings = array_merge(
            $optionalExt['missing'] ? array_map(
                fn ($ext) => "Extension optionnelle manquante: {$ext}",
                $optionalExt['missing']
            ) : [],
            $envFile['passed'] ? [] : [$envFile['message']]
        );

        // Construire résultat final
        $result = [
            'passed' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'checks' => [
                'php_version' => $phpVersion,
                'extensions' => [
                    'required' => $requiredExt,
                    'optional' => $optionalExt,
                ],
                'permissions' => $permissions,
                'env_file' => $envFile,
                'server_info' => $serverInfo,
            ],
        ];

        // Cacher les résultats
        $this->setCache($result);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function checkPhpVersion(): array
    {
        $current = PHP_VERSION;
        $passed = version_compare($current, self::MIN_PHP_VERSION, '>=');

        return [
            'passed' => $passed,
            'required' => self::MIN_PHP_VERSION,
            'current' => $current,
            'message' => $passed
                ? "PHP {$current} ✓"
                : "PHP {$current} requis, minimum ".self::MIN_PHP_VERSION,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function checkRequiredExtensions(): array
    {
        $installed = [];
        $missing = [];

        foreach (self::REQUIRED_EXTENSIONS as $extension => $description) {
            if (extension_loaded($extension)) {
                $installed[] = $extension;
            } else {
                $missing[] = $extension;
            }
        }

        return [
            'passed' => empty($missing),
            'required' => array_keys(self::REQUIRED_EXTENSIONS),
            'installed' => $installed,
            'missing' => $missing,
            'details' => $this->buildExtensionDetails(self::REQUIRED_EXTENSIONS, $installed, $missing),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function checkOptionalExtensions(): array
    {
        $installed = [];
        $missing = [];

        foreach (self::OPTIONAL_EXTENSIONS as $extension => $description) {
            if (extension_loaded($extension)) {
                $installed[] = $extension;
            } else {
                $missing[] = $extension;
            }
        }

        return [
            'passed' => empty($missing),  // Pour optional, passed = all present (mais warn si missing)
            'optional' => array_keys(self::OPTIONAL_EXTENSIONS),
            'installed' => $installed,
            'missing' => $missing,
            'details' => $this->buildExtensionDetails(self::OPTIONAL_EXTENSIONS, $installed, $missing),
        ];
    }

    /**
     * Construit les détails de chaque extension pour affichage.
     *
     * @param  array<string, string>  $extensions  Extensions à vérifier {ext => description}
     * @param  string[]  $installed  Extensions installées
     * @param  string[]  $missing  Extensions manquantes
     * @return array<string, array> {extension => {installed: bool, description: string}}
     */
    private function buildExtensionDetails(array $extensions, array $installed, array $missing): array
    {
        $details = [];

        foreach ($extensions as $extension => $description) {
            $details[$extension] = [
                'installed' => in_array($extension, $installed),
                'description' => $description,
            ];
        }

        return $details;
    }

    /**
     * {@inheritDoc}
     */
    public function checkPermissions(): array
    {
        $writable = [];
        $notWritable = [];

        foreach (self::REQUIRED_WRITABLE_DIRS as $dir) {
            $path = base_path($dir);

            // Créer répertoire s'il n'existe pas (pour tester write)
            if (! is_dir($path)) {
                @mkdir($path, 0755, true);
            }

            if (is_writable($path)) {
                $writable[] = $dir;
            } else {
                $notWritable[] = $dir;
            }
        }

        return [
            'passed' => empty($notWritable),
            'required_dirs' => self::REQUIRED_WRITABLE_DIRS,
            'writable' => $writable,
            'not_writable' => $notWritable,
            'details' => $this->buildPermissionDetails(self::REQUIRED_WRITABLE_DIRS),
        ];
    }

    /**
     * Construit les détails de permissions pour affichage.
     *
     * @param  string[]  $directories  Répertoires à vérifier
     * @return array<string, array> {directory => {writable: bool, perms: string}}
     */
    private function buildPermissionDetails(array $directories): array
    {
        $details = [];

        foreach ($directories as $dir) {
            $path = base_path($dir);
            $details[$dir] = [
                'path' => $path,
                'exists' => is_dir($path),
                'writable' => is_writable($path),
                'permissions' => is_dir($path) ? substr(sprintf('%o', fileperms($path)), -3) : 'N/A',
            ];
        }

        return $details;
    }

    /**
     * {@inheritDoc}
     */
    public function checkEnvFile(): array
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        $envExists = file_exists($envPath) && is_readable($envPath);
        $envExampleExists = file_exists($envExamplePath);

        // Si .env absent mais .env.example présent, c'est récupérable
        if (! $envExists && $envExampleExists) {
            return [
                'passed' => true,
                'env_exists' => false,
                'env_example_exists' => true,
                'message' => '.env sera créé depuis .env.example',
            ];
        }

        // Si .env.example absent, c'est bloquant
        if (! $envExampleExists) {
            return [
                'passed' => false,
                'env_exists' => $envExists,
                'env_example_exists' => false,
                'message' => 'Fichier .env.example manquant (clone Git incomplet?)',
            ];
        }

        // Si .env existe, c'est OK
        if ($envExists) {
            return [
                'passed' => true,
                'env_exists' => true,
                'env_example_exists' => true,
                'message' => 'Fichier .env détecté',
            ];
        }

        return [
            'passed' => false,
            'env_exists' => false,
            'env_example_exists' => true,
            'message' => 'Impossible créer .env',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getServerInfo(): array
    {
        $isHttps = ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return [
            'host' => $host,
            'is_https' => $isHttps,
            'scheme' => $isHttps ? 'https' : 'http',
            'sapi' => php_sapi_name(),
            'os' => PHP_OS_FAMILY,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function enableCache(int $minutes = 5): void
    {
        $this->cacheDuration = $minutes;
    }

    /**
     * Récupère les résultats mis en cache s'ils existent.
     *
     * @return array|null Résultats cachés, ou null si pas de cache
     */
    private function getCache(): ?array
    {
        if ($this->cacheDuration === 0) {
            return null;
        }

        return Cache::get($this->cacheKey);
    }

    /**
     * Stocke les résultats des vérifications en cache.
     *
     * @param  array  $results  Résultats à cacher
     */
    private function setCache(array $results): void
    {
        if ($this->cacheDuration > 0) {
            Cache::put($this->cacheKey, $results, now()->addMinutes($this->cacheDuration));
        }
    }
}
