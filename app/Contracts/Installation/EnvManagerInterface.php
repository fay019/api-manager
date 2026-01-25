<?php

namespace App\Contracts\Installation;

/**
 * Interface pour gérer le fichier .env pendant l'installation.
 *
 * Responsabilités:
 * - Créer .env si absent (depuis .env.example)
 * - Lire/modifier clés .env
 * - Valider cohérence .env
 * - Gérer atomicité (backup + restore si erreur)
 *
 * @example
 * $envManager = resolve(EnvManagerInterface::class);
 * $envManager->update([
 *     'APP_NAME' => 'My App',
 *     'DB_HOST' => 'localhost'
 * ]);
 */
interface EnvManagerInterface
{
    /**
     * Détecte si .env existe et est readable.
     *
     * Actions:
     * - Si absent: Optionnel copier depuis .env.example
     * - Si présent mais non-readable: Retourner false
     *
     * @param  bool  $createIfMissing  Si true, copier depuis .env.example si absent
     * @return bool True si .env exists et readable
     */
    public function envExists(bool $createIfMissing = true): bool;

    /**
     * Crée .env en copiant .env.example.
     *
     * Actions:
     * - Copier .env.example → .env
     * - Vérifier permissions (chmod 644)
     * - Vérifier contient clés obligatoires
     *
     * @return bool True si succès
     *
     * @throws \Exception Si .env.example absent ou copie échoue
     */
    public function create(): bool;

    /**
     * Lit une clé .env et retourne sa valeur.
     *
     * Parsing:
     * - Ignorer commentaires (#)
     * - Gérer quotes (KEY="value with spaces")
     * - Gérer empty values (KEY=)
     *
     * @param  string  $key  Clé à lire (ex: APP_NAME)
     * @param  mixed  $default  Valeur par défaut si clé absent
     * @return mixed Valeur (string, null, bool?)
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Écrit/modifie une ou plusieurs clés .env.
     *
     * Actions:
     * - Si clé existe: replacer sa valeur
     * - Si clé absent: ajouter en fin de fichier
     * - Gérer quotes si valeur contient espaces
     * - Gérer escaping (slashes, quotes)
     * - Atomic write (temp file + rename)
     *
     * @param  array<string, mixed>  $values  Clés à mettre à jour {KEY => VALUE, ...}
     * @return bool True si succès
     *
     * @throws \Exception Si write échoue
     */
    public function update(array $values): bool;

    /**
     * Lit TOUT le contenu .env parsed.
     *
     * @return array<string, mixed> Toutes les clés .env {KEY => VALUE}
     */
    public function all(): array;

    /**
     * Valide la cohérence du fichier .env actuel.
     *
     * Vérifications:
     * - Clés obligatoires présentes (APP_NAME, DB_CONNECTION, etc.)
     * - Types valides (URLs, nombres, boolean)
     * - Pas de conflits (APP_DEBUG avec APP_ENV=production)
     * - APP_KEY non vide
     *
     * @return array {
     *               'valid' => bool,
     *               'errors' => string[],
     *               'warnings' => string[],
     *               'missing_keys' => string[]
     *               }
     */
    public function validate(): array;

    /**
     * Crée un backup du .env actuel.
     *
     * Actions:
     * - Copier .env → .env.backup-[TIMESTAMP]
     * - Garder max 5 backups (supprimer plus anciens)
     *
     * @return string Chemin du fichier backup créé
     *
     * @throws \Exception Si backup échoue
     */
    public function backup(): string;

    /**
     * Restore depuis un backup précédent.
     *
     * @param  string  $backupFile  Chemin du fichier backup (ex: .env.backup-2026-01-24-14-23-00)
     * @return bool True si succès
     *
     * @throws \Exception Si restore échoue ou backup absent
     */
    public function restore(string $backupFile): bool;

    /**
     * Récupère la liste des backups disponibles.
     *
     * @return array Liste des backups {filename => timestamp, ...}
     */
    public function listBackups(): array;

    /**
     * Force rechargement du fichier .env en mémoire.
     *
     * Utile après modifications externes ou lors de tests.
     */
    public function reload(): void;

    /**
     * Vide le cache configuration Laravel après modifications .env.
     *
     * Actions:
     * - Appeler config:cache si possible
     * - Vider opcache si disponible
     * - Recharger config() values
     *
     * @throws \Exception Si cache clear échoue de façon critique
     */
    public function flushCache(): void;
}
