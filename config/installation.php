<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Installation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration du système d'installation modulaire et robuste.
    | Cette configuration définit les étapes, validateurs et comportements.
    |
    */

    'enabled' => env('APP_INSTALLATION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Installation Steps
    |--------------------------------------------------------------------------
    |
    | Les étapes à exécuter lors de l'installation, dans l'ordre.
    | Chaque step implémente le contrat InstallationStepContract.
    |
    */
    'steps' => [
        'requirements' => [
            'class' => \App\Installation\Steps\CheckRequirementsStep::class,
            'description' => 'Vérification des prérequis système',
            'critical' => true,
        ],
        'environment' => [
            'class' => \App\Installation\Steps\EnvironmentStep::class,
            'description' => 'Configuration de l\'environnement',
            'critical' => true,
        ],
        'config' => [
            'class' => \App\Installation\Steps\PublishConfigStep::class,
            'description' => 'Publication des fichiers de configuration',
            'critical' => false,
        ],
        'database' => [
            'class' => \App\Installation\Steps\DatabaseStep::class,
            'description' => 'Exécution des migrations de base de données',
            'critical' => true,
        ],
        'modules' => [
            'class' => \App\Installation\Steps\ModulesStep::class,
            'description' => 'Initialisation et découverte des modules',
            'critical' => true,
        ],
        'seeders' => [
            'class' => \App\Installation\Steps\SeedersStep::class,
            'description' => 'Remplissage de la base de données avec les données initiales',
            'critical' => false,
        ],
        'storage' => [
            'class' => \App\Installation\Steps\StorageStep::class,
            'description' => 'Configuration des répertoires de stockage',
            'critical' => true,
        ],
        'assets' => [
            'class' => \App\Installation\Steps\AssetsStep::class,
            'description' => 'Compilation des assets front-end',
            'critical' => false,
        ],
        'health' => [
            'class' => \App\Installation\Steps\HealthCheckStep::class,
            'description' => 'Vérification de la santé de l\'application',
            'critical' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configuration du logging d'installation.
    |
    */
    'logging' => [
        'enabled' => true,
        'channel' => 'installation',
        'file' => 'storage/logs/installation.log',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    |
    | Configuration du rollback automatique en cas d'erreur.
    |
    */
    'rollback' => [
        'enabled' => true,
        'on_error' => true,
        'preserve_database' => false, // Préserver la BD en cas de rollback
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeding
    |--------------------------------------------------------------------------
    |
    | Configuration du seeding conditionnel par environnement.
    |
    */
    'seeding' => [
        'enabled' => true,
        'skip_in_production' => true,
        'seeders' => [
            'development' => [
                \Database\Seeders\AdminUserSeeder::class,
                \Database\Seeders\ApiClientSeeder::class,
                \Database\Seeders\PromoSeeder::class,
                \Database\Seeders\AppSettingSeeder::class,
                \Database\Seeders\DocumentationSettingSeeder::class,
            ],
            'production' => [
                \Database\Seeders\AdminUserSeeder::class,
                \Database\Seeders\AppSettingSeeder::class,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Discovery
    |--------------------------------------------------------------------------
    |
    | Paramètres de découverte automatique des modules.
    |
    */
    'module_discovery' => [
        'enabled' => true,
        'paths' => [
            'app/Modules',
        ],
        'auto_register' => true,
        'cache_registry' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Configuration de la validation pré-installation.
    |
    */
    'validation' => [
        'check_php_version' => true,
        'php_minimum_version' => '8.2',
        'check_extensions' => true,
        'required_extensions' => [
            'bcmath',
            'ctype',
            'fileinfo',
            'json',
            'mbstring',
            'openssl',
            'pdo',
            'tokenizer',
            'xml',
        ],
        'check_permissions' => true,
        'writable_paths' => [
            'storage',
            'bootstrap/cache',
            'database',
        ],
        'check_database' => true,
    ],
];