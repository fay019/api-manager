# 🧹 Plan de Suppression - Ancien Système d'Installation

## Fichiers à supprimer

### 1. Contrôleurs anciens (3 fichiers)
```
❌ app/Http/Controllers/SetupController.php
❌ app/Http/Controllers/BootstrapController.php
❌ app/Http/Controllers/InstallerController.php
```

**Raison:** Remplacés par les nouveaux contrôleurs dans `app/Http/Controllers/Setup/`

### 2. Fichiers publics legacy (3 fichiers)
```
❌ public/install.php
❌ public/setup.php
❌ public/diagnostic.php
```

**Raison:** Remplacés par les routes Laravel dans `routes/setup.php`

### 3. Vues anciennes (4 fichiers)
```
❌ resources/views/setup/index.blade.php
❌ resources/views/setup/step-general.blade.php
❌ resources/views/setup/step-database.blade.php
❌ resources/views/setup/step-confirm.blade.php
```

**Raison:** Remplacées par les nouvelles vues Blade du wizard

**ATTENTION:** Garder `layout.blade.php` (réutilisable)

### 4. Routes web.php (anciennes)
Dans `routes/web.php`, SUPPRIMER:
```php
// ❌ Ces lignes:
use App\Http\Controllers\SetupController;
use App\Http\Controllers\BootstrapController;
use App\Http\Controllers\InstallerController;

Route::get('/setup.php', [BootstrapController::class, 'setup'])->name('bootstrap.setup');

Route::prefix('setup')->name('setup.')->group(function () {
    Route::get('/', [SetupController::class, 'index'])->name('index');
    Route::get('/general', [SetupController::class, 'stepGeneral'])->name('general');
    Route::post('/save-general', [SetupController::class, 'saveGeneral'])->name('save-general');
    Route::get('/database', [SetupController::class, 'stepDatabase'])->name('database');
    Route::post('/test-database', [SetupController::class, 'testDatabase'])->name('test-database');
    Route::post('/save-database', [SetupController::class, 'saveDatabase'])->name('save-database');
    Route::get('/confirm', [SetupController::class, 'stepConfirm'])->name('confirm');
    Route::post('/finish', [SetupController::class, 'finish'])->name('finish');
});
```

**Ces routes sont maintenant dans `routes/setup.php` (déjà intégré)**

---

## Checklist de suppression

```bash
# 1. Supprimer contrôleurs anciens
rm app/Http/Controllers/SetupController.php
rm app/Http/Controllers/BootstrapController.php
rm app/Http/Controllers/InstallerController.php

# 2. Supprimer fichiers publics
rm public/install.php
rm public/setup.php
rm public/diagnostic.php

# 3. Supprimer vues anciennes
rm resources/views/setup/index.blade.php
rm resources/views/setup/step-general.blade.php
rm resources/views/setup/step-database.blade.php
rm resources/views/setup/step-confirm.blade.php

# 4. Vérifier qu'il n'y a plus de références aux anciens contrôleurs
grep -r "SetupController\|BootstrapController\|InstallerController" \
    app/ routes/ --include="*.php" | grep -v "Setup/" || echo "✓ Aucune référence trouvée"

# 5. Tester les routes
php artisan route:list | grep setup

# 6. Vérifier l'intégrité
php artisan config:cache
php artisan route:cache
```

---

## Vérifications après suppression

✅ **Toutes les routes `/setup/*` doivent venir de `routes/setup.php`**
```
GET  /setup/welcome
GET  /setup/app-settings
POST /setup/app-settings
GET  /setup/database
POST /setup/database
etc...
```

✅ **Les middlewares doivent être actifs:**
- `CheckInstallation` doit bloquer `/setup` après install (403)
- `RateLimitSetup` doit limiter tentatives

✅ **Les services doivent être accessibles:**
```bash
php artisan tinker
> resolve('App\Services\Installation\RequirementsChecker')->check()
# Doit retourner les vérifications
```

✅ **Les vues doivent utiliser le nouveau layout:**
```blade
@extends('setup.layout')
@section('content')
  <!-- contenu -->
@endsection
```

---

## ⚠️ Points d'attention

### Ne PAS supprimer:
- `resources/views/setup/layout.blade.php` (réutilisable)
- `config/installation.php` (nouveau)
- `app/Services/Installation/` (nouveau)
- `app/Contracts/Installation/` (nouveau)
- `routes/setup.php` (nouveau)

### Vérifier les imports:
Après suppression des contrôleurs anciens, vérifier:
- `routes/web.php` - Aucune référence aux anciens contrôleurs
- `bootstrap/app.php` - Le middleware est bien enregistré

### Routes à conserver:
```php
// GARDER CES ROUTES:
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::prefix('docs')->name('docs.')->group(function () {
    // ...
});
// Etc... (toutes les routes non-setup)
```

---

## Étapes d'exécution

### 1. Vérifier avant suppression
```bash
# Lister tous les fichiers legacy
echo "=== Contrôleurs anciens ==="
ls -la app/Http/Controllers/{Setup,Bootstrap,Installer}Controller.php 2>&1

echo "=== Fichiers publics ==="
ls -la public/{install,setup,diagnostic}.php 2>&1

echo "=== Vues anciennes ==="
ls -la resources/views/setup/{index,step-*.blade.php} 2>&1
```

### 2. Sauvegarder (optionnel)
```bash
# Créer archive de backup juste au cas
tar -czf /tmp/old_setup_backup.tar.gz \
    app/Http/Controllers/SetupController.php \
    app/Http/Controllers/BootstrapController.php \
    app/Http/Controllers/InstallerController.php \
    public/install.php \
    public/setup.php \
    public/diagnostic.php \
    resources/views/setup/index.blade.php \
    resources/views/setup/step-*.blade.php
```

### 3. Supprimer les fichiers
```bash
# Supprimer contrôleurs
rm -f app/Http/Controllers/SetupController.php
rm -f app/Http/Controllers/BootstrapController.php
rm -f app/Http/Controllers/InstallerController.php

# Supprimer fichiers publics
rm -f public/install.php public/setup.php public/diagnostic.php

# Supprimer vues anciennes
rm -f resources/views/setup/index.blade.php
rm -f resources/views/setup/step-general.blade.php
rm -f resources/views/setup/step-database.blade.php
rm -f resources/views/setup/step-confirm.blade.php
```

### 4. Nettoyer routes/web.php
```php
// Supprimer ces imports:
// ❌ use App\Http\Controllers\SetupController;
// ❌ use App\Http\Controllers\BootstrapController;
// ❌ use App\Http\Controllers\InstallerController;

// Supprimer ces routes (déjà dans routes/setup.php):
// ❌ Route::get('/setup.php', ...)
// ❌ Route::prefix('setup')->name('setup.')->group(...)
```

### 5. Vérifier l'intégrité
```bash
# Vérifier qu'il n'y a pas d'imports cassés
php artisan config:cache

# Vérifier les routes
php artisan route:list | grep -E "setup|Setup"

# Tester le middleware
curl -I http://api-manager.test/setup/welcome  # Doit retourner 200
curl -I http://api-manager.test/setup  # Doit rediriger ou retourner 200

# Si installé, /setup doit retourner 403:
# curl -I http://api-manager.test/setup/welcome  # 403 (car installé)
```

### 6. Commit git
```bash
git add -A
git commit -m "cleanup: supprimer ancien système installation legacy

- Supprimer SetupController, BootstrapController, InstallerController
- Supprimer fichiers publics install.php, setup.php, diagnostic.php
- Supprimer vues anciennes (garder layout.blade.php)
- Routes setup remplacées par routes/setup.php (nouveau système)
- Services installation refactorisés (RequirementsChecker, EnvManager, etc)"
```

---

## ✨ Après cleanup

Le système est alors **100% refactorisé**:

✅ Ancien système complètement supprimé
✅ Nouveau système (services + middleware + routes) en place
✅ Zéro conflit de routes
✅ Zéro fichier legacy
✅ Prêt pour implémenter les contrôleurs manquants

**Aucune ambiguïté sur les routes ou contrôleurs à utiliser.**

---

**Créé:** 24 janvier 2026
**Version:** 1.0
