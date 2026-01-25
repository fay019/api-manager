<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des modules de l'application.
    | Les modules sont auto-découverts à partir du répertoire 'paths'.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Module Paths
    |--------------------------------------------------------------------------
    |
    | Chemins où chercher les modules.
    |
    */
    'paths' => [
        base_path('app/Modules'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Registry Cache
    |--------------------------------------------------------------------------
    |
    | Mettre en cache le registre des modules pour améliorer les performances.
    |
    */
    'cache_registry' => env('APP_ENV') === 'production',
    'cache_key' => 'app:module:registry',

    /*
    |--------------------------------------------------------------------------
    | Module Bootstrap
    |--------------------------------------------------------------------------
    |
    | Modules à charger au démarrage.
    | Les modules sont généralement auto-bootstrap.
    |
    */
    'bootstrap' => [
        // Les modules sont auto-détectés
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Namespace pour les modules. Utilisé pour la découverte automatique.
    |
    */
    'namespace' => 'App\Modules',

    /*
    |--------------------------------------------------------------------------
    | Installed Modules
    |--------------------------------------------------------------------------
    |
    | Liste des modules actuellement installés (remplie automatiquement).
    | Cette section est mise en cache après découverte.
    |
    */
    'installed' => [
        // Auto-découverte
    ],
];
