# 📦 Création de Modules Personnalisés

Ce guide explique comment créer de nouveaux modules dans l'architecture modulaire de l'application.

## Table des matières

- [Concept des modules](#concept-des-modules)
- [Structure d'un module](#structure-dun-module)
- [Étapes de création](#étapes-de-création)
- [Exemple complet](#exemple-complet)
- [Avancé](#avancé)

---

## Concept des modules

Un **module** est une unité fonctionnelle self-contained contenant:
- Models et migrations
- Controllers
- Routes
- Services
- Configuration spécifique
- Tests unitaires

### Avantages

✅ **Isolation**: Chaque module est isolé et indépendant
✅ **Réutilisabilité**: Facile à copier dans d'autres projets
✅ **Scalabilité**: Ajouter des features = ajouter un module
✅ **Maintenabilité**: Code organisé par fonctionnalité
✅ **Auto-découverte**: Les modules sont détectés automatiquement
✅ **Gestion des dépendances**: Ordre d'installation automatique

---

## Structure d'un module

```
app/Modules/
├── {ModuleName}/                      # Nom du module (PascalCase)
│   ├── {ModuleName}Module.php         # Classe principal (hérite BaseModule)
│   │
│   ├── Models/                        # Modèles Eloquent
│   │   └── {Model}.php
│   │
│   ├── Controllers/                   # Controllers HTTP
│   │   ├── Http/
│   │   │   └── {Controller}.php
│   │   └── Api/
│   │       └── {ApiController}.php
│   │
│   ├── Migrations/                    # Migrations BD (auto-découvertes)
│   │   ├── 2026_01_21_000001_create_{table}.php
│   │   └── 2026_01_21_000002_alter_{table}.php
│   │
│   ├── Seeders/                       # Seeders initiaux (optionnel)
│   │   └── {ModuleName}Seeder.php
│   │
│   ├── Routes/
│   │   ├── routes.php                 # Routes du module (optionnel)
│   │   └── api.php                    # Routes API (optionnel)
│   │
│   ├── Services/                      # Logique métier
│   │   └── {Service}.php
│   │
│   ├── Views/                         # Vues Blade (optionnel)
│   │   └── *.blade.php
│   │
│   ├── Config/                        # Configuration du module
│   │   └── {module}.php
│   │
│   ├── Tests/                         # Tests (optionnel)
│   │   ├── Unit/
│   │   └── Feature/
│   │
│   └── Resources/                     # Resources Filament (optionnel)
│       └── {Resource}.php
```

---

## Étapes de création

### 1️⃣ Créer la structure de base

```bash
# Créer les répertoires du module
mkdir -p app/Modules/Analytics/Models
mkdir -p app/Modules/Analytics/Http/Controllers
mkdir -p app/Modules/Analytics/Migrations
mkdir -p app/Modules/Analytics/Seeders
mkdir -p app/Modules/Analytics/Services
mkdir -p app/Modules/Analytics/Routes
mkdir -p app/Modules/Analytics/Config
mkdir -p app/Modules/Analytics/Tests
mkdir -p app/Modules/Analytics/Filament/Resources
```

### 2️⃣ Créer la classe du module

Fichier: `app/Modules/Analytics/AnalyticsModule.php`

```php
<?php

namespace App\Modules\Analytics;

use App\Modules\BaseModule;

class AnalyticsModule extends BaseModule
{
    protected string $moduleName = 'Analytics';
    protected string $description = 'Module d\'analyse et statistiques';
    protected string $version = '1.0.0';

    public function boot(): void
    {
        // Enregistrer les routes
        $this->registerRoutes();

        // Enregistrer les observateurs
        $this->registerObservers();

        parent::boot();
    }

    protected function registerRoutes(): void
    {
        // Routes du module
    }

    protected function registerObservers(): void
    {
        // Observateurs du module
    }

    public function validateInstallation(): array
    {
        $errors = [];

        if (!class_exists(\App\Modules\Analytics\Models\Metric::class)) {
            $errors[] = 'Model Metric not found';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function getDependencies(): array
    {
        // Modules dont dépend ce module (ex: ['Promo'])
        return [];
    }
}
```

### 3️⃣ Créer le modèle

Fichier: `app/Modules/Analytics/Models/Metric.php`

```php
<?php

namespace App\Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    protected $fillable = [
        'name',
        'value',
        'category',
        'tracked_at',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
        'value' => 'float',
    ];
}
```

### 4️⃣ Créer la migration

Fichier: `app/Modules/Analytics/Migrations/2026_01_21_000001_create_metrics_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('value');
            $table->string('category')->nullable();
            $table->timestamp('tracked_at')->useCurrent();
            $table->timestamps();

            $table->index(['category', 'tracked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
```

### 5️⃣ Créer le controller (optionnel)

Fichier: `app/Modules/Analytics/Http/Controllers/MetricController.php`

```php
<?php

namespace App\Modules\Analytics\Http\Controllers;

use App\Modules\Analytics\Models\Metric;
use Illuminate\Http\JsonResponse;

class MetricController
{
    public function index(): JsonResponse
    {
        $metrics = Metric::latest('tracked_at')
            ->paginate(50);

        return response()->json($metrics);
    }

    public function store(Request $request): JsonResponse
    {
        $metric = Metric::create($request->validate([
            'name' => 'required|string',
            'value' => 'required|numeric',
            'category' => 'nullable|string',
        ]));

        return response()->json($metric, 201);
    }
}
```

### 6️⃣ Créer le seeder (optionnel)

Fichier: `app/Modules/Analytics/Seeders/AnalyticsSeeder.php`

```php
<?php

namespace App\Modules\Analytics\Seeders;

use App\Modules\Analytics\Models\Metric;
use Illuminate\Database\Seeder;

class AnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        Metric::create([
            'name' => 'page_views',
            'value' => 1500,
            'category' => 'traffic',
        ]);
    }
}
```

### 7️⃣ Créer la configuration (optionnel)

Fichier: `app/Modules/Analytics/Config/analytics.php`

```php
<?php

return [
    'enabled' => true,
    'retention_days' => 90,
    'batch_size' => 100,
];
```

### 8️⃣ Créer les routes (optionnel)

Fichier: `app/Modules/Analytics/Routes/routes.php`

```php
<?php

Route::middleware(['api', 'auth:sanctum'])
    ->prefix('api/v1')
    ->group(function () {
        Route::apiResource('metrics', \App\Modules\Analytics\Http\Controllers\MetricController::class);
    });
```

---

## Exemple complet: Module Notifications

### Arborescence

```
app/Modules/Notifications/
├── NotificationsModule.php
├── Models/
│   ├── Notification.php
│   └── NotificationTemplate.php
├── Http/Controllers/
│   └── NotificationController.php
├── Migrations/
│   ├── 2026_01_21_000001_create_notifications_table.php
│   └── 2026_01_21_000002_create_notification_templates_table.php
├── Seeders/
│   └── NotificationsSeeder.php
├── Services/
│   └── NotificationService.php
├── Routes/
│   └── routes.php
├── Config/
│   └── notifications.php
└── Tests/
    ├── Unit/
    │   └── NotificationTest.php
    └── Feature/
        └── NotificationApiTest.php
```

### Utilisation

```bash
# 1. Vérifier que le module est découvert
php artisan discover:modules

# 2. Réinstaller (auto-découverte + migration)
php artisan install

# 3. Utiliser le module dans l'application
# Les routes, modèles et services sont automatiquement disponibles
```

---

## Avancé

### Dépendances entre modules

Si votre module dépend d'un autre, déclarez-le:

```php
public function getDependencies(): array
{
    return ['Promo', 'Analytics']; // Ce module nécessite Promo et Analytics
}
```

L'ordre d'installation sera automatiquement calculé.

### Hooks de cycle de vie

```php
// Avant installation
public function validateInstallation(): array
{
    // Validations
}

// Après installation réussie
public function onInstall(): void
{
    // Initialisation
}

// Désinstallation
public function onUninstall(): void
{
    // Nettoyage
}
```

### Enregistrer des commandes Artisan

```php
protected function registerCommands(): void
{
    $this->commands([
        \App\Modules\Analytics\Console\Commands\ProcessMetricsCommand::class,
    ]);
}
```

### Intégration Filament

Ajouter un resource Filament au module:

```php
// app/Modules/Analytics/Filament/Resources/MetricResource.php

namespace App\Modules\Analytics\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;

class MetricResource extends Resource
{
    // ...
}
```

### Tests du module

```php
// app/Modules/Analytics/Tests/Feature/MetricApiTest.php

namespace App\Modules\Analytics\Tests\Feature;

use Tests\TestCase;

class MetricApiTest extends TestCase
{
    public function test_can_list_metrics()
    {
        $response = $this->get('/api/v1/metrics');
        $response->assertStatus(200);
    }
}
```

---

## Checklist de création

- [ ] Structure de répertoires créée
- [ ] Classe `{Module}Module.php` créée
- [ ] Modèles créés (`app/Modules/{Module}/Models/`)
- [ ] Migrations créées (`app/Modules/{Module}/Migrations/`)
- [ ] Controllers créés (si routes)
- [ ] Routes définies (optionnel)
- [ ] Seeders créés (optionnel)
- [ ] Configuration créée (optionnel)
- [ ] Tests écrits (optionnel)
- [ ] Documentation du module mise à jour

---

## Déploiement du module

```bash
# 1. Vérifier la découverte
php artisan discover:modules --json

# 2. Vérifier l'ordre d'installation
php artisan discover:modules --install-order

# 3. Exécuter l'installation
php artisan install

# 4. Valider
php artisan validate:install

# 5. Tester
php artisan test app/Modules/{Module}/Tests/
```

---

## Troubleshooting

### Module non découvert

```bash
# Invalider le cache
php artisan cache:forget app:module:registry

# Redécouvrir
php artisan discover:modules
```

### Migration non exécutée

```bash
# S'assurer que la migration est dans Migrations/
# Le fichier doit avoir le format: YYYY_MM_DD_HHMMSS_name.php

# Exécuter manuellement
php artisan migrate --path=app/Modules/{Module}/Migrations/
```

### Dépendance manquante

```bash
# Vérifier les dépendances
php artisan discover:modules --install-order

# Erreur: Module X dépend de Y qui n'est pas installé
# Solution: Installer le module Y en premier
```

---

**Dernière mise à jour**: 21 janvier 2026
**Version**: 1.0.0
