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
        'php_minimum_version' => '8.3',
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

    /*
    |--------------------------------------------------------------------------
    | Wizard Installation Configuration (Refactor 2026)
    |--------------------------------------------------------------------------
    |
    | Configuration pour le nouveau système d'installation wizard par étapes.
    | Ce système remplace progressivement l'ancien système basé sur Steps.
    |
    */

    'wizard' => [
        /**
         * Configuration session setup.
         * Paramètres affectant la session pendant l'installation.
         */
        'session' => [
            'timeout' => 60,
            'key_prefix' => 'setup.',
        ],

        /**
         * Configuration rate limiting.
         * Protection contre brute force sur /setup.
         */
        'rate_limit' => [
            'max_attempts' => 30,
            'decay_minutes' => 10,
        ],

        /**
         * Configuration requirements check cache.
         * Les vérifications sont coûteuses (permissions, extensions).
         */
        'requirements' => [
            'cache_duration' => 5,
            'min_php_version' => '8.3.0',
        ],

        /**
         * Configuration validation passwords admin.
         * Règles pour le mot de passe admin initial.
         */
        'password' => [
            'min_length' => 12,
            'require_mixed_case' => true,
            'require_numbers' => true,
            'require_symbols' => false,
            'check_compromised' => true,
        ],

        /**
         * Configuration base de données.
         * Paramètres pour tests connexion DB.
         */
        'database' => [
            'connection_timeout' => 10,
            'supported_drivers' => ['sqlite', 'mysql', 'pgsql'],
            'preferred_charset' => 'utf8mb4',
        ],

        /**
         * Configuration email/SMTP.
         * Paramètres pour setup mail.
         */
        'mail' => [
            'supported_mailers' => ['smtp', 'log'],
            'default_mailer' => 'log',
            'test_timeout' => 10,
        ],

        /**
         * Configuration sécurité.
         * Paramètres affectant sécurité du setup.
         */
        'security' => [
            'force_https' => true,
            'allow_localhost_setup' => true,
            'hide_secrets_in_logs' => true,
            'allow_production_reset' => env('APP_ALLOW_PRODUCTION_RESET', false),
        ],
    ],
];
