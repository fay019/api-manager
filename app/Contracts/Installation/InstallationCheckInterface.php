<?php

namespace App\Contracts\Installation;

/**
 * Interface pour vérifier l'état d'installation de l'application.
 *
 * Responsabilités:
 * - Déterminer si l'application est installée
 * - Valider la cohérence de l'installation
 * - Fournir des informations sur l'état
 *
 * @example
 * $checker = resolve(InstallationCheckInterface::class);
 * if ($checker->isInstalled()) {
 *     // Application déjà installée
 * }
 */
interface InstallationCheckInterface
{
    /**
     * Vérifie si l'application est complètement installée.
     *
     * Critères:
     * - Fichier installed.lock doit exister
     * - Hash d'intégrité doit être valide
     * - Table users doit contenir au moins un admin
     *
     * @return bool True si installée, false sinon
     */
    public function isInstalled(): bool;

    /**
     * Vérifie la cohérence de l'installation actuelle.
     *
     * Vérifie:
     * - .env fichier existe et readable
     * - APP_KEY défini et non vide
     * - Connexion DB fonctionnelle
     * - Table users accessible
     * - Admin user existe
     *
     * @return array {
     *               'valid' => bool,
     *               'errors' => string[],
     *               'warnings' => string[],
     *               'details' => array
     *               }
     */
    public function validateIntegrity(): array;

    /**
     * Récupère les informations d'installation (timestamp, versions, etc).
     *
     * @return array|null Contenu du installed.lock, ou null si non installé
     */
    public function getInstallationInfo(): ?array;

    /**
     * Récupère la date d'installation.
     *
     * @return \Carbon\Carbon|null Timestamp d'installation, ou null
     */
    public function getInstalledAt(): ?\Carbon\Carbon;

    /**
     * Réinitialise l'installation (DANGER: pour testing seulement).
     *
     * Actions:
     * - Supprimer installed.lock
     * - Optionnel: Reset DB
     *
     * @param  bool  $resetDatabase  Si true, reset base de données aussi
     *
     * @throws \Exception Si opération échoue
     */
    public function reset(bool $resetDatabase = false): void;
}
