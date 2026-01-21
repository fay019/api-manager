# 🔴 DEBUG: 419 Session Expirée - Étape 1 Setup Wizard

**Date:** 2026-01-21
**Status:** ❌ NON RÉSOLU - À INVESTIGUER DEMAIN
**Priorité:** HAUTE - Bloque l'installation complète

---

## Symptôme
Quand l'utilisateur remplit le formulaire de l'**étape 1** du setup wizard et clique sur **"Suivant"**, il reçoit une erreur:
```
419 Session expirée
Votre session a expiré. Veuillez rafraîchir la page et réessayer.
```

### Contexte
- URL: `https://api.moussouni.dev/setup/general` (POST request)
- Action: Cliquer sur "Suivant" après remplir les champs de l'étape 1
- Serveur: Production (not localhost)
- Base de données: SQLite (fresh install)

---

## Tentatives de Correction

### ❌ Tentative 1: Augmenter SESSION_LIFETIME
**Commit:** 75e7a21
**Changement:** SESSION_LIFETIME 120 → 2880 minutes dans .env.example
**Résultat:** Pas d'effet. L'utilisateur a toujours l'erreur 419.

### ❌ Tentative 2: Pré-créer les tables dans SetupController::finish()
**Commit:** 75e7a21
**Changement:** Ajouter création des tables sessions/cache dans finish() method
**Résultat:** Pas d'effet. L'erreur survient bien AVANT finish(), à l'étape 1.

### ❌ Tentative 3: Créer database.sqlite en bootstrap
**Commit:** 1791434
**Changement:**
- Créer le fichier `database/database.sqlite` avant Laravel boot
- Créer les tables sessions/cache/jobs dans `ensureRequiredDatabaseTables()`
**Résultat:** Toujours erreur 419. L'utilisateur dit "pareille" (pareil/même).

---

## Hypothèses à Tester Demain

### 1️⃣ Middleware Order Problem
**Hypothèse:** Le middleware de session charge AVANT que ensureRequiredDatabaseTables() s'exécute.

```
public/index.php execute
    ↓
ensureRequiredDatabaseTables() runs ✅
    ↓
Bootstrap Laravel
    ↓
Service providers load
    ↓
SessionMiddleware runs → cherche table sessions
    ↓
ERROR 419 (si tables créées trop tard)
```

**À vérifier:**
- L'ordre d'exécution du bootstrap
- Si SessionMiddleware accède à la base avant nos fonctions

### 2️⃣ Database Directory Permissions
**Hypothèse:** Le fichier database.sqlite ne peut pas être créé/accédé (permissions).

**À vérifier:**
```bash
ls -la storage/
ls -la database/
ls -la public/index.php
```

### 3️⃣ Session Storage Not SQLite
**Hypothèse:** Le .env du serveur a SESSION_DRIVER=file au lieu de database.

**À vérifier:**
```bash
# Sur le serveur de production
grep "SESSION_DRIVER" .env
# Doit être: SESSION_DRIVER=database
```

### 4️⃣ Cache Configuration Issue
**Hypothèse:** Le cache driver essaie aussi d'accéder à la base (CACHE_STORE=file dans .env.example).

**À vérifier:**
```bash
# Sur le serveur
grep "CACHE_STORE" .env
# Doit être: CACHE_STORE=file (pas "database")
```

### 5️⃣ PDO Not Working on Server
**Hypothèse:** sqlite:// PDO driver n'est pas disponible sur le serveur.

**À vérifier:**
```bash
php -m | grep -i pdo
php -m | grep -i sqlite
```

---

## Prochaines Étapes

### Demain matin:
1. **Créer un fichier diagnostic** dans public/ pour vérifier:
   - Si database.sqlite est créé avec les tables
   - Si SESSION_DRIVER=database
   - Si CACHE_STORE=file
   - Si PDO/SQLite sont disponibles

2. **Ajouter du logging** dans la fonction de session:
   - Vérifier si tables existent vraiment
   - Vérifier si PDO s'initialise correctement

3. **Tester l'order d'exécution** du bootstrap:
   - Ajouter des debug timestamps dans public/index.php
   - Vérifier que ensureRequiredDatabaseTables() s'exécute AVANT SessionMiddleware

### Code à Essayer:
```php
// Dans public/index.php, après ensureRequiredDatabaseTables()
// Ajouter une vérification/debug:
function debugDatabaseTables(): void
{
    $dbPath = dirname(__DIR__) . '/database/database.sqlite';
    if (!file_exists($dbPath)) {
        // Log que le fichier n'existe pas
        return;
    }

    try {
        $pdo = new PDO("sqlite:{$dbPath}");
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        // Log les tables créées
    } catch (Throwable $e) {
        // Log l'erreur
    }
}
```

---

## Notes
- L'utilisateur va **dormir et vérifier les fichiers .md demain**
- Erreur reproduite de manière **consistente** (toujours pareille)
- L'étape 1 du formulaire s'affiche correctement, c'est juste la submission qui échoue
- Les tentatives précédentes (APP_KEY, créer répertoires, etc.) ont bien fonctionné

---

## Ressources
- **Route Setup:** `routes/web.php` (setup.general → saveGeneral)
- **Controller:** `app/Http/Controllers/SetupController.php`
- **View:** `resources/views/setup/step-general.blade.php`
- **Middleware:** `app/Http/Middleware/CheckInstallation.php`
- **Bootstrap:** `public/index.php` (ensureRequiredDatabaseTables)
