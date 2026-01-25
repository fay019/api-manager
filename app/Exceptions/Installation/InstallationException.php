<?php

namespace App\Exceptions\Installation;

use Exception;

/**
 * Exception base pour tous les erreurs d'installation.
 *
 * Cette exception est la classe parent pour toutes les exceptions
 * spécifiques au système d'installation. Elle inclut des métadonnées
 * utiles pour logging et feedback utilisateur.
 *
 * @example
 * throw new InstallationException(
 *     'Impossible écrire fichier .env',
 *     context: ['path' => '.env']
 * );
 */
class InstallationException extends Exception
{
    /**
     * Contexte additionnelautre pour l'erreur.
     *
     * Peut contenir des informations pour debugging:
     * - Chemins de fichiers
     * - Valeurs de configuration
     * - Stack traces partielles
     */
    protected array $context = [];

    /**
     * Code d'erreur personnalisé (pour catégorisation).
     */
    protected string $code_category = 'INSTALLATION_ERROR';

    /**
     * Initialise l'exception d'installation.
     *
     * @param  string  $message  Message d'erreur visible user
     * @param  int  $code  Code HTTP ou numérique
     * @param  \Throwable|null  $previous  Exception précédente (for chaining)
     * @param  array  $context  Contexte additionnel
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Récupère le contexte de l'erreur.
     *
     * @return array Contexte additionnel
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Défini le contexte de l'erreur.
     *
     * @param  array  $context  Contexte à stocker
     * @return self Pour chaînage
     */
    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Ajoute une valeur au contexte.
     *
     * @param  string  $key  Clé du contexte
     * @param  mixed  $value  Valeur
     * @return self Pour chaînage
     */
    public function addContextValue(string $key, mixed $value): self
    {
        $this->context[$key] = $value;

        return $this;
    }

    /**
     * Récupère la catégorie d'erreur.
     *
     * @return string Catégorie (ex: INSTALLATION_ERROR, DB_ERROR, etc)
     */
    public function getCodeCategory(): string
    {
        return $this->code_category;
    }

    /**
     * Convertit l'exception en array (pour logging JSON, API responses).
     *
     * @return array {message: string, code: int, context: array, trace: array}
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'category' => $this->getCodeCategory(),
            'context' => $this->getContext(),
            'trace' => config('app.debug') ? $this->getTrace() : [],
        ];
    }

    /**
     * Convertit en JSON pour l'API ou logs.
     *
     * @return string JSON encodé
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
