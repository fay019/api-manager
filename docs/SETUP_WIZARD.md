# 🧙 Setup Wizard - Installation Interactive

Le Setup Wizard est une interface web pour configurer votre application à la première visite.

**GitHub**: [fay019/api-manager](https://github.com/fay019/api-manager)

## 🎯 Vue d'ensemble

Au lieu d'exécuter `php artisan install` via CLI, vous pouvez désormais:

1. **Cloner le projet**
2. **Visiter http://localhost:8000 ou http://api-manager.test**
3. **Suivre le formulaire Setup Wizard** (3 étapes faciles)
4. **L'app est installée et prête!**

---

## 📋 Les 3 Étapes

### Étape 1: Infos Générales
```
Nom du Site        → "Mon API Manager"
URL de l'App       → "https://api.example.com"
Email Admin        → "admin@example.com"
Mot de Passe Admin → "••••••••" (min 8 caractères)
```

### Étape 2: Base de Données
Choix entre:
- **SQLite** (simple, pour dev)
  ```
  Chemin: database/database.sqlite
  ```

- **MySQL** (production)
  ```
  Hôte: localhost
  Port: 3306
  Base: api_manager
  User: root
  Password: ••••••••

  Test de connexion intégré ✅
  ```

- **PostgreSQL** (production)
  ```
  Hôte: localhost
  Port: 5432
  Base: api_manager
  User: postgres
  Password: ••••••••
  ```

### Étape 3: Confirmation
Vérification complète avant installation:
- Récapitulatif de la config
- Bouton "Finaliser" pour installer
- Installation automatique avec:
  - Mise à jour du .env
  - Exécution des migrations
  - Création de l'utilisateur admin
  - Création du flag installed.lock

---

## 🔒 Comment Ça Marche?

### Détection d'Installation

**Middleware CheckInstallation** (`app/Http/Middleware/CheckInstallation.php`):

```
Chaque requête web vérifie:
  ↓
  Est-ce une route setup/admin/login?
    ├─ OUI → Laisser passer
    └─ NON → Vérifier installed.lock
            ├─ Existe → App normale ✅
            └─ N'existe pas → Redirige vers /setup 🧙
```

### Flag File

**`storage/app/installed.lock`** = Indicateur d'installation

```json
{
  "installed_at": "2026-01-21T13:00:00Z",
  "php_version": "8.3.30",
  "laravel_version": "12.0.0",
  "database": "mysql"
}
```

---

## 🚀 Déploiement

### Scénario 1: Serveur Vierge
```bash
# 1. Cloner depuis GitHub
git clone https://github.com/fay019/api-manager.git
cd api-manager

# 2. Installer les dépendances
composer install

# 3. Visiter http://api-manager.test
# → Setup Wizard s'affiche automatiquement
# → Remplir le formulaire
# → L'app est installée!
```

### Scénario 2: Déploiement Automatisé (CI/CD)
```bash
# Ou utiliser CLI (script d'installation)
composer install
php artisan install --force
```

---

## 🛠️ Configuration Extensible

### Admin Settings Page

Après installation, accédez à:
```
Admin Panel → Paramètres
```

**Onglets actuels:**
- ✅ **Général** - Infos de base (lecture seule)
- ⏳ **Email** - SMTP config (ajouter plus tard)
- ⏳ **Cache & Performance** - Redis, etc (ajouter plus tard)
- ⏳ **Queue & Jobs** - Configuration queue (ajouter plus tard)
- ⏳ **API** - Paramètres API (ajouter plus tard)

**Comment ajouter un nouvel onglet:**

```php
// app/Filament/Pages/Settings.php

Forms\Components\Tabs\Tab::make('Email')
    ->schema([
        Forms\Components\Section::make('Configuration Email')
            ->schema([
                Forms\Components\TextInput::make('mail_host')
                    ->label('Serveur SMTP'),
                Forms\Components\TextInput::make('mail_port')
                    ->label('Port'),
                // ... autres champs
            ]),
    ]),
```

---

## 📱 Architecture

### Routes Setup (`routes/web.php`)
```php
/setup                    → Page d'accueil Setup
/setup/general           → Étape 1 (formulaire)
/setup/save-general      → Sauvegarde étape 1
/setup/database          → Étape 2 (BD)
/setup/test-database     → Test connexion BD
/setup/save-database     → Sauvegarde étape 2
/setup/confirm           → Étape 3 (confirmation)
/setup/finish            → Finalise l'installation
```

### Controller (`app/Http/Controllers/SetupController.php`)
- `index()` - Page d'accueil
- `stepGeneral()` - Formulaire infos
- `saveGeneral()` - Validation + session
- `stepDatabase()` - Formulaire BD
- `testDatabase()` - AJAX test connexion
- `saveDatabase()` - Validation + session
- `stepConfirm()` - Vérification
- `finish()` - Installation finale

### Middleware (`app/Http/Middleware/CheckInstallation.php`)
- Vérifie si installed.lock existe
- Redirige vers /setup si absent
- Exclut les routes setup/admin/login

---

## ✨ Fonctionnalités

### Formulaire Intelligent
- ✅ Validation côté serveur
- ✅ Affichage des erreurs
- ✅ Valeurs par défaut
- ✅ Help text informatif

### Test de Connexion BD (AJAX)
```javascript
// Bouton "Tester la connexion"
// Teste la config sans soumettre
// Affiche erreur/succès en temps réel
```

### Sécurité
- ✅ Tokens CSRF
- ✅ Validation stricte
- ✅ Mots de passe hashés
- ✅ Confirmation mot de passe

### UI/UX
- 🎨 Design moderne et responsive
- 📱 Mobile-friendly
- 🎯 Étapes visuelles
- 👁️ Toggle affichage mot de passe
- ⏱️ Durée estimée (2-3 min)

---

## 🔄 Processus Complet

```
[Visiteur]
    ↓
[Accès http://api-manager.test]
    ↓
[Middleware CheckInstallation]
    ↓
    ├─ installed.lock existe?
    │   ├─ OUI → App normale
    │   └─ NON → Redirige /setup
    ↓
[Setup Wizard]
    ├─ Étape 1: Infos générales
    │   └─ Session::setup.site_name
    │   └─ Session::setup.admin_email
    ├─ Étape 2: Base de données
    │   └─ Session::setup.db_connection
    │   └─ Session::setup.db_host
    └─ Étape 3: Confirmation
        └─ SetupController::finish()
            ├─ Mettre à jour .env
            ├─ Exécuter migrations
            ├─ Créer admin user
            ├─ Créer installed.lock
            └─ Redirige vers /admin/login
                ↓
            [Installation complète!]
```

---

## ❓ FAQ

**Q: Peut-on ignorer le Setup Wizard?**
A: Non, il s'affiche automatiquement à la première visite. Ou utilisez CLI: `php artisan install`

**Q: Que faire si on oublie le mot de passe admin?**
A: Créer un nouvel admin via CLI:
```bash
php artisan tinker
>>> App\Models\User::create([
...   'name' => 'Admin 2',
...   'email' => 'admin2@example.com',
...   'password' => Hash::make('password'),
...   'is_admin' => true
... ])
```

**Q: Comment réinstaller?**
A: Supprimer `storage/app/installed.lock` et revisiter la page

**Q: Configuration stockée où?**
A: Dans le `.env` et `storage/app/installed.lock` (metadata)

**Q: Peut-on ajouter d'autres configurations?**
A: OUI! Ajouter des onglets dans `app/Filament/Pages/Settings.php`

---

## 🎓 Exemple: Ajouter Configuration Email

**Jour 1:** Installation avec Setup Wizard
**Jour 30:** Ajouter configuration email

```bash
# 1. Créer migration pour stocker config email
php artisan make:migration create_email_settings_table

# 2. Ajouter onglet Email dans app/Filament/Pages/Settings.php
Forms\Components\Tabs\Tab::make('Email')
    ->schema([...])

# 3. L'admin peut maintenant configurer email depuis le panel
```

---

**Dernière mise à jour:** 2026-01-21
**Version:** 1.0.0
**Status:** ✅ Production Ready
