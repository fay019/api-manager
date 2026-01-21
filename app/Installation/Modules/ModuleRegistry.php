<?php

namespace App\Installation\Modules;

use App\Modules\BaseModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Registre centralisé pour la découverte et gestion des modules.
 *
 * Cette classe gère:
 * - Découverte automatique des modules
 * - Chargement des modules en tant que ServiceProviders
 * - Validation des dépendances entre modules
 * - Caching du registre pour les performances
 */
class ModuleRegistry
{
    /**
     * Collection de modules découverts.
     *
     * @var Collection
     */
    protected Collection $modules;

    /**
     * Cache des informations du registre.
     *
     * @var bool
     */
    protected bool $useCache;

    /**
     * Clé de cache pour le registre.
     *
     * @var string
     */
    protected string $cacheKey = 'app:module:registry';

    /**
     * Constructeur.
     */
    public function __construct()
    {
        $this->modules = new Collection();
        $this->useCache = config('modules.cache_registry', false);
        $this->loadRegistry();
    }

    /**
     * Charge le registre des modules.
     */
    public function loadRegistry(): void
    {
        // Essayer de charger depuis le cache
        if ($this->useCache && Cache::has($this->cacheKey)) {
            $this->modules = new Collection(Cache::get($this->cacheKey));
            return;
        }

        // Découvrir les modules
        $this->discoverModules();

        // Mettre en cache
        if ($this->useCache) {
            Cache::put($this->cacheKey, $this->modules->toArray(), 3600);
        }
    }

    /**
     * Découvre automatiquement tous les modules.
     */
    public function discoverModules(): void
    {
        $paths = config('modules.paths', [base_path('app/Modules')]);

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $directories = File::directories($path);

            foreach ($directories as $directory) {
                $this->registerModuleFromPath($directory);
            }
        }
    }

    /**
     * Enregistre un module à partir de son chemin.
     */
    protected function registerModuleFromPath(string $modulePath): void
    {
        $moduleName = basename($modulePath);

        // Chercher le fichier principal du module
        $moduleClass = config('modules.namespace', 'App\Modules') . '\\' . $moduleName . '\\' . $moduleName . 'Module';

        if (!class_exists($moduleClass)) {
            return;
        }

        // Vérifier que c'est une classe BaseModule
        if (!is_subclass_of($moduleClass, \App\Modules\BaseModule::class)) {
            return;
        }

        // Enregistrer le module
        $module = [
            'name' => $moduleName,
            'class' => $moduleClass,
            'path' => $modulePath,
            'namespace' => config('modules.namespace', 'App\Modules') . '\\' . $moduleName,
        ];

        $this->modules->put($moduleName, $module);
    }

    /**
     * Obtient tous les modules enregistrés.
     */
    public function all(): Collection
    {
        return $this->modules;
    }

    /**
     * Obtient un module par son nom.
     */
    public function get(string $name): ?array
    {
        return $this->modules->get($name);
    }

    /**
     * Vérifie si un module est enregistré.
     */
    public function has(string $name): bool
    {
        return $this->modules->has($name);
    }

    /**
     * Enregistre un module manuellement.
     */
    public function register(string $name, string $class, string $path): void
    {
        $this->modules->put($name, [
            'name' => $name,
            'class' => $class,
            'path' => $path,
            'namespace' => config('modules.namespace', 'App\Modules') . '\\' . $name,
        ]);

        // Invalider le cache
        if ($this->useCache) {
            Cache::forget($this->cacheKey);
        }
    }

    /**
     * Invalide le cache du registre.
     */
    public function invalidateCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Obtient l'ordre d'installation des modules basé sur les dépendances.
     *
     * @return Collection
     */
    public function getInstallationOrder(): Collection
    {
        $ordered = collect();
        $visited = collect();

        foreach ($this->modules as $name => $module) {
            $this->resolveDependencies($name, $ordered, $visited);
        }

        return $ordered;
    }

    /**
     * Résout les dépendances d'un module (algorithme DFS).
     */
    protected function resolveDependencies(string $moduleName, Collection $ordered, Collection $visited): void
    {
        if ($visited->has($moduleName)) {
            return;
        }

        $visited->put($moduleName, true);

        $module = $this->get($moduleName);
        if (!$module) {
            return;
        }

        // Obtenir les dépendances du module
        try {
            $moduleInstance = app($module['class']);
            $dependencies = $moduleInstance->getDependencies();

            foreach ($dependencies as $dependency) {
                $this->resolveDependencies($dependency, $ordered, $visited);
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs d'instanciation
        }

        // Ajouter le module à l'ordre d'installation
        $ordered->put($moduleName, $module);
    }

    /**
     * Valide les dépendances de tous les modules.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateDependencies(): array
    {
        $errors = [];

        foreach ($this->modules as $name => $module) {
            try {
                $moduleInstance = app($module['class']);
                $dependencies = $moduleInstance->getDependencies();

                foreach ($dependencies as $dependency) {
                    if (!$this->has($dependency)) {
                        $errors[] = "Module '{$name}' dépend de '{$dependency}' qui n'est pas installé.";
                    }
                }
            } catch (\Exception $e) {
                // Ignorer
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Obtient les migrations de tous les modules.
     */
    public function getMigrations(): Collection
    {
        $migrations = collect();

        foreach ($this->modules as $name => $module) {
            $migrationsPath = $module['path'] . '/Migrations';

            if (is_dir($migrationsPath)) {
                $files = File::files($migrationsPath);
                foreach ($files as $file) {
                    $migrations->push($migrationsPath . '/' . $file->getBasename());
                }
            }
        }

        return $migrations;
    }

    /**
     * Obtient les seeders de tous les modules.
     */
    public function getSeeders(): Collection
    {
        $seeders = collect();

        foreach ($this->modules as $name => $module) {
            $seedersPath = $module['path'] . '/Seeders';

            if (is_dir($seedersPath)) {
                $namespace = $module['namespace'];
                $files = File::files($seedersPath);

                foreach ($files as $file) {
                    $className = pathinfo($file->getBasename(), PATHINFO_FILENAME);
                    $seeders->push($namespace . '\\Seeders\\' . $className);
                }
            }
        }

        return $seeders;
    }

    /**
     * Enregistre tous les modules comme service providers.
     */
    public function registerAllModules(\Illuminate\Foundation\Application $app): void
    {
        $ordered = $this->getInstallationOrder();

        foreach ($ordered as $name => $module) {
            try {
                $app->register($module['class']);
            } catch (\Exception $e) {
                \Log::warning("Failed to register module {$name}: " . $e->getMessage());
            }
        }
    }

    /**
     * Obtient les informations de tous les modules.
     */
    public function getModulesInfo(): Collection
    {
        return $this->modules->map(function ($module) {
            try {
                $instance = app($module['class']);
                return $instance->getInfo();
            } catch (\Exception $e) {
                return [
                    'name' => $module['name'],
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Nettoie le registre.
     */
    public function clear(): void
    {
        $this->modules = new Collection();
        $this->invalidateCache();
    }

    /**
     * Exporte le registre en JSON pour le débogage.
     */
    public function toJson(): string
    {
        return json_encode($this->getModulesInfo()->toArray(), JSON_PRETTY_PRINT);
    }
}