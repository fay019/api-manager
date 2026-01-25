<?php

namespace App\Contracts\Installation;

/**
 * Interface pour vérifier les prérequis système avant installation.
 *
 * Responsabilités:
 * - Vérifier PHP version
 * - Vérifier extensions PHP requises/optionnelles
 * - Vérifier permissions fichiers système
 * - Détecter configuration serveur
 *
 * @example
 * $checker = resolve(RequirementsCheckerInterface::class);
 * $results = $checker->check();
 * if (!$results['passed']) {
 *     // Afficher erreurs à l'utilisateur
 * }
 */
interface RequirementsCheckerInterface
{
    /**
     * Exécute TOUTES les vérifications et retourne un rapport complet.
     *
     * @return array {
     *               'passed' => bool,              // Tous vérifications bloquantes passées?
     *               'warnings' => string[],        // Vérifications non-bloquantes échouées
     *               'errors' => string[],          // Vérifications bloquantes échouées
     *               'checks' => [
     *               'php_version' => ['passed' => bool, 'message' => string, 'current' => string],
     *               'extensions' => [
     *               'required' => ['pdo' => [...], 'mbstring' => [...]],
     *               'optional' => ['pdo_mysql' => [...], ...]
     *               ],
     *               'permissions' => [...],
     *               'env_file' => [...],
     *               'server_info' => [...]
     *               ]
     *               }
     */
    public function check(): array;

    /**
     * Vérifie la version PHP minimale requise.
     *
     * Minimum requis: PHP 8.3
     *
     * @return array {
     *               'passed' => bool,
     *               'required' => '8.3',
     *               'current' => PHP_VERSION,
     *               'message' => string
     *               }
     */
    public function checkPhpVersion(): array;

    /**
     * Vérifie les extensions PHP obligatoires.
     *
     * Extensions obligatoires:
     * - pdo (base de données)
     * - mbstring (manipulation strings)
     * - json (handling JSON)
     * - ctype (character type checking)
     * - filter (input filtering)
     * - hash (hashing)
     * - openssl (encryption)
     *
     * @return array {
     *               'passed' => bool,
     *               'required' => string[],
     *               'installed' => string[],
     *               'missing' => string[],
     *               'details' => [...]
     *               }
     */
    public function checkRequiredExtensions(): array;

    /**
     * Vérifie les extensions PHP optionnelles selon environnement.
     *
     * Extensions optionnelles:
     * - pdo_sqlite (si sqlite souhaité)
     * - pdo_mysql (si mysql souhaité)
     * - pdo_pgsql (si postgresql souhaité)
     * - redis (optionnel cache/queue)
     * - imagick/gd (optionnel images)
     *
     * @return array {
     *               'passed' => bool,
     *               'optional' => string[],
     *               'installed' => string[],
     *               'missing' => string[],
     *               'details' => [...]
     *               }
     */
    public function checkOptionalExtensions(): array;

    /**
     * Vérifie les permissions d'écriture des répertoires critiques.
     *
     * Répertoires testés:
     * - storage/ (create files, write)
     * - storage/logs/
     * - storage/app/
     * - bootstrap/cache/ (create files, write)
     * - . (racine, pour .env)
     *
     * @return array {
     *               'passed' => bool,
     *               'required_dirs' => string[],
     *               'writable' => string[],
     *               'not_writable' => string[],
     *               'details' => [...]
     *               }
     */
    public function checkPermissions(): array;

    /**
     * Vérifie l'existence et la validité du fichier .env.example.
     *
     * Actions:
     * - Vérifier .env.example existe
     * - Vérifier contient clés obligatoires
     * - Optionnel: copier vers .env si absent
     *
     * @return array {
     *               'passed' => bool,
     *               'env_example_exists' => bool,
     *               'env_exists' => bool,
     *               'message' => string
     *               }
     */
    public function checkEnvFile(): array;

    /**
     * Récupère les informations serveur (pour détection auto-config).
     *
     * Infos:
     * - HTTP_HOST (pour détecter URL)
     * - HTTPS (pour http vs https)
     * - PHP SAPI (cli vs fpm vs cgi)
     * - Système opérateur (Linux, Darwin, Windows)
     *
     * @return array {
     *               'host' => string,
     *               'is_https' => bool,
     *               'sapi' => string,
     *               'os' => string,
     *               'memory_limit' => string,
     *               'max_execution_time' => int,
     *               'upload_max_filesize' => string
     *               }
     */
    public function getServerInfo(): array;

    /**
     * Cache résultats des vérifications pendant X minutes (pour perf).
     *
     * Par défaut: 5 minutes
     *
     * @param  int  $minutes  Durée du cache (0 = pas de cache)
     */
    public function enableCache(int $minutes = 5): void;
}
