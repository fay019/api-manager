<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;

/**
 * Classe de base pour tous les modules de l'application.
 *
 * Tous les modules doivent hériter de cette classe et implémenter
 * les méthodes requises pour l'installation, migration et bootstrapping.
 */
abstract class BaseModule extends ServiceProvider
{
    /**
     * Nom unique du module.
     */
    protected string $moduleName;

    /**
     * Description du module.
     */
    protected string $description = '';

    /**
     * Version du module.
     */
    protected string $version = '1.0.0';

    /**
     * Chemin racine du module.
     */
    protected string $modulePath;

    /**
     * Namespace du module.
     */
    protected string $moduleNamespace;

    /**
     * Constructeur du module.
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->modulePath = dirname(__DIR__.'/Modules/'.$this->getModuleName());
        $this->moduleNamespace = 'App\Modules\\'.$this->getModuleName();
    }

    /**
     * Bootstrap le module.
     */
    public function boot(): void
    {
        // Enregistrement des routes
        $this->registerRoutes();

        // Enregistrement des migrations
        $this->registerMigrations();

        // Publication des resources
        $this->publishResources();

        // Commandes Artisan spécifiques au module
        $this->registerCommands();
    }

    /**
     * Enregistre les services du module.
     */
    public function register(): void
    {
        // À implémenter par le module
    }

    /**
     * Obtient le nom unique du module.
     */
    public function getModuleName(): string
    {
        return $this->moduleName ?? class_basename($this);
    }

    /**
     * Obtient la description du module.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Obtient la version du module.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Obtient le chemin racine du module.
     */
    public function getModulePath(): string
    {
        return $this->modulePath;
    }

    /**
     * Obtient le namespace du module.
     */
    public function getModuleNamespace(): string
    {
        return $this->moduleNamespace;
    }

    /**
     * Enregistre les routes du module.
     * À surcharger dans les modules qui ont des routes.
     */
    protected function registerRoutes(): void
    {
        $routesPath = $this->getModulePath().'/Routes';
        if (is_dir($routesPath)) {
            $this->loadRoutesFrom($routesPath.'/routes.php');
        }
    }

    /**
     * Enregistre les migrations du module.
     */
    protected function registerMigrations(): void
    {
        $migrationsPath = $this->getModulePath().'/Migrations';
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Publie les resources du module.
     */
    protected function publishResources(): void
    {
        $moduleName = $this->getModuleName();

        // Publier les views
        $viewsPath = $this->getModulePath().'/Views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, strtolower($moduleName));
        }

        // Publier la configuration
        $configPath = $this->getModulePath().'/Config';
        if (is_dir($configPath)) {
            $this->publishes([
                $configPath => config_path('modules'),
            ], "{$moduleName}-config");
        }
    }

    /**
     * Enregistre les commandes Artisan du module.
     * À surcharger dans les modules qui ont des commandes.
     */
    protected function registerCommands(): void
    {
        // À implémenter par les modules
    }

    /**
     * Hooks d'installation du module.
     * Appelé lors du processus d'installation.
     */
    public function onInstall(): void
    {
        // À implémenter par le module si nécessaire
    }

    /**
     * Hooks de désinstallation du module.
     */
    public function onUninstall(): void
    {
        // À implémenter par le module si nécessaire
    }

    /**
     * Valide que le module peut être installé.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateInstallation(): array
    {
        return [
            'valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Obtient les prérequis du module.
     */
    public function getRequirements(): array
    {
        return [];
    }

    /**
     * Obtient les dépendances du module (autres modules requis).
     *
     * @return array Noms des modules dépendances
     */
    public function getDependencies(): array
    {
        return [];
    }

    /**
     * Obtient les informations du module.
     */
    public function getInfo(): array
    {
        return [
            'name' => $this->getModuleName(),
            'description' => $this->getDescription(),
            'version' => $this->getVersion(),
            'namespace' => $this->getModuleNamespace(),
            'path' => $this->getModulePath(),
            'dependencies' => $this->getDependencies(),
        ];
    }
}
