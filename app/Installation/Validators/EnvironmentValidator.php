<?php

namespace App\Installation\Validators;

/**
 * Validateur d'environnement du système.
 */
class EnvironmentValidator
{
    /**
     * Valide la version PHP requise.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePhpVersion(string $required = '8.2'): array
    {
        $current = PHP_VERSION;
        $valid = version_compare($current, $required, '>=');

        return [
            'valid' => $valid,
            'current' => $current,
            'required' => $required,
            'errors' => $valid ? [] : [
                "PHP version {$required} ou supérieure requise, version actuelle: {$current}",
            ],
        ];
    }

    /**
     * Valide les extensions PHP requises.
     *
     * @param array $extensions Extensions requises
     *
     * @return array ['valid' => bool, 'errors' => array, 'missing' => array]
     */
    public static function validateExtensions(array $extensions): array
    {
        $missing = [];
        $loaded = get_loaded_extensions();

        foreach ($extensions as $extension) {
            // PDO est une pseudo-extension, vérifier avec extension_loaded()
            if ($extension === 'pdo') {
                if (!extension_loaded('pdo')) {
                    $missing[] = $extension;
                }
            } elseif (!in_array($extension, $loaded)) {
                $missing[] = $extension;
            }
        }

        $valid = empty($missing);

        return [
            'valid' => $valid,
            'missing' => $missing,
            'loaded' => count($loaded),
            'errors' => $valid ? [] : [
                'Extensions manquantes: ' . implode(', ', $missing),
            ],
        ];
    }

    /**
     * Valide les permissions d'écriture sur les répertoires.
     *
     * @param array $paths Chemins à vérifier
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateWritableDirectories(array $paths): array
    {
        $errors = [];
        $failed = [];

        foreach ($paths as $path) {
            $fullPath = base_path($path);

            if (!is_dir($fullPath)) {
                $errors[] = "Le répertoire {$path} n'existe pas";
                $failed[] = $path;
                continue;
            }

            if (!is_writable($fullPath)) {
                $errors[] = "Le répertoire {$path} n'est pas accessible en écriture";
                $failed[] = $path;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'failed_paths' => $failed,
            'checked' => count($paths),
        ];
    }

    /**
     * Valide les fichiers de configuration requis.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateConfigFiles(): array
    {
        $requiredFiles = [
            '.env' => base_path('.env'),
            'composer.json' => base_path('composer.json'),
            'package.json' => base_path('package.json'),
        ];

        $errors = [];
        $missing = [];

        foreach ($requiredFiles as $name => $path) {
            if (!file_exists($path)) {
                $errors[] = "Le fichier de configuration {$name} est manquant";
                $missing[] = $name;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'missing' => $missing,
            'found' => count($requiredFiles) - count($missing),
        ];
    }

    /**
     * Valide les variables d'environnement essentielles.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateEnvironmentVariables(): array
    {
        $required = [
            'APP_NAME' => 'Nom de l\'application',
            'APP_ENV' => 'Environnement (local, production, etc.)',
            'APP_DEBUG' => 'Mode debug',
            'APP_URL' => 'URL de l\'application',
            'DB_CONNECTION' => 'Type de base de données',
            'DB_DATABASE' => 'Nom de la base de données',
        ];

        $errors = [];
        $missing = [];

        foreach ($required as $var => $description) {
            if (empty(env($var))) {
                $errors[] = "Variable d'environnement {$var} manquante ({$description})";
                $missing[] = $var;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'missing' => $missing,
            'checked' => count($required),
        ];
    }

    /**
     * Effectue une validation complète du système.
     *
     * @return array ['valid' => bool, 'results' => array]
     */
    public static function validateComplete(): array
    {
        $results = [];
        $allValid = true;

        // PHP Version
        $results['php_version'] = self::validatePhpVersion(config('installation.validation.php_minimum_version', '8.2'));
        if (!$results['php_version']['valid']) {
            $allValid = false;
        }

        // Extensions
        $results['extensions'] = self::validateExtensions(config('installation.validation.required_extensions', []));
        if (!$results['extensions']['valid']) {
            $allValid = false;
        }

        // Permissions
        $results['writable_paths'] = self::validateWritableDirectories(config('installation.validation.writable_paths', []));
        if (!$results['writable_paths']['valid']) {
            $allValid = false;
        }

        // Config files
        $results['config_files'] = self::validateConfigFiles();
        if (!$results['config_files']['valid']) {
            $allValid = false;
        }

        // Variables d'environnement
        $results['environment_vars'] = self::validateEnvironmentVariables();
        if (!$results['environment_vars']['valid']) {
            $allValid = false;
        }

        return [
            'valid' => $allValid,
            'results' => $results,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}