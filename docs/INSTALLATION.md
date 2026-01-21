# 📦 Guide d'Installation Complet

Ce document décrit le système d'installation modulaire et robuste de l'application API Manager.

**GitHub Repository**: [fay019/api-manager](https://github.com/fay019/api-manager)

## Table des matières

- [Installation Rapide](#installation-rapide)
- [Installation Interactive (Setup Wizard)](#installation-interactive-setup-wizard)
- [Installation depuis GitHub](#installation-depuis-github)
- [Installation Détaillée](#installation-détaillée)
- [Installation par Étapes](#installation-par-étapes)
- [Validation](#validation)
- [Dépannage](#dépannage)
- [Environnements de Déploiement](#environnements-de-déploiement)

---

## 🆘 Troubleshooting Rapide - Erreur 500?

Si vous voyez une **erreur 500 au premier accès**, accédez à l'une de ces pages:

### Option 1: Diagnostic Simple (Texte brut)
```
https://your-domain.com/diagnostic.php
```
Ultra-simple, texte brut, fonctionne si tout échoue.

### Option 2: Page d'Installation Interactive (HTML)
```
https://your-domain.com/install.php
```
Interface graphique complète avec tous les détails.

Ces pages **indépendantes de Laravel** vont:
- ✅ Afficher les diagnostiques (PHP version, extensions, permissions)
- ✅ Créer les répertoires manquants automatiquement
- ✅ Créer le fichier `.env` depuis `.env.example`
- ✅ Tester les permissions du système de fichiers
- ✅ Lancer Composer automatiquement si disponible
- ✅ Créer la base de données SQLite et table sessions
- ✅ Écrire les logs détaillés dans `storage/logs/install-diagnostic.log`
- ✅ Proposer des solutions spécifiques aux erreurs

**Elles fonctionnent même si Laravel est complètement cassé!**

Les logs de diagnostic sont disponibles via SSH:
```bash
tail -f storage/logs/install-diagnostic.log
```

---

## Installation Rapide

### Prérequis

- **PHP**: 8.2 ou supérieur
- **Composer**: Dernier version
- **Node.js**: 18+ (optionnel pour les assets)
- **Permissions**: Écriture sur `storage/`, `bootstrap/cache/`, `database/`
- **Base de données**: SQLite (dev) ou MySQL 5.7+ (prod)

### Commande d'installation unique

```bash
# 1. Cloner le projet depuis GitHub
git clone https://github.com/fay019/api-manager.git
cd api-manager

# 2. Installation des dépendances PHP
composer install

# 3. Lancer l'installation complète
php artisan install
```

C'est tout! L'application est maintenant prête.

### Vérification

```bash
# Vérifier que l'installation est correcte
php artisan validate:install

# Démarrer le serveur de développement
php artisan serve
```

Accédez à:
- **Application**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **Documentation**: http://localhost:8000/docs
- **Health Check**: http://localhost:8000/api/v1/health

---

## Installation Interactive (Setup Wizard)

### 🧙 Nouvelle méthode: Interface Web

À la place d'utiliser la CLI, vous pouvez maintenant installer l'application via une interface web interactive!

#### Comment ça marche?

**Première visite → Auto-détection → Setup Wizard s'affiche**

```
1. Cloner le projet
   git clone https://github.com/fay019/api-manager.git
   cd api-manager

2. Installer les dépendances
   composer install

3. Démarrer le serveur
   php artisan serve

4. Visiter http://localhost:8000
   ↓
   ✨ Setup Wizard s'affiche automatiquement!
```

#### Les Étapes du Wizard (Flux Adaptatif)

**Étape 1: Infos Générales + Choix Base de Données**
```
├─ Nom du Site (ex: "Mon API Manager")
├─ URL de l'Application (ex: "https://api.example.com")
├─ Email Admin (ex: "admin@example.com")
├─ Mot de Passe Admin (min 8 caractères)
│
└─ ✨ Sélection du type de base de données:
   ├─ SQLite (Développement/Test - aucune config supplémentaire)
   ├─ MySQL (Production - nécessite une deuxième étape)
   └─ PostgreSQL (Production - nécessite une deuxième étape)
```

**Étape 2: Configuration Base de Données (si MySQL/PostgreSQL)**

*Cette étape s'affiche SEULEMENT si vous avez choisi MySQL ou PostgreSQL*

```
Pour MySQL:
├─ Hôte: localhost
├─ Port: 3306
├─ Base de données: api_manager
├─ Utilisateur: root
├─ Mot de passe: ••••••••
└─ Bouton "Test Connexion" ✅ (avant de continuer)

Pour PostgreSQL:
├─ Hôte: localhost
├─ Port: 5432
├─ Base de données: api_manager
├─ Utilisateur: postgres
├─ Mot de passe: ••••••••
└─ Bouton "Test Connexion" ✅ (avant de continuer)
```

**Étape 2 ou 3: Confirmation**

*Pour SQLite: Étape 2 | Pour MySQL/PostgreSQL: Étape 3*

```
Récapitulatif final:
├─ Nom du Site
├─ URL de l'Application
├─ Type de BD
├─ Configuration BD
└─ Bouton "Finaliser l'Installation" → Tout se configure automatiquement!
```

#### Après le Setup Wizard

L'application effectue automatiquement TOUT ce qui est nécessaire:

1. ✅ **Composer Install** - Installe les dépendances PHP si `vendor/` n'existe pas
2. ✅ **APP_KEY** - Génère la clé de chiffrement si absente
3. ✅ **Répertoires** - Crée `storage/`, `bootstrap/cache/` et tous les sous-répertoires
4. ✅ **Fichier SQLite** - Crée la BD SQLite + table sessions (si SQLite choisi)
5. ✅ **Mise à jour .env** - Configure tous les paramètres (DB, URL, etc.)
6. ✅ **Migrations** - Exécute la création de toutes les tables
7. ✅ **Seeders** - Lance les seeders pour initialiser les données
8. ✅ **Admin User** - Crée l'utilisateur administrateur avec les identifiants saisis
9. ✅ **Flag Installation** - Crée `storage/app/installed.lock` pour marquer comme installé

**Puis vous êtes redirigé vers le login admin!**

### ⚡ Bootstrap Automatique

L'application crée automatiquement TOUT ce qui est nécessaire au démarrage:

**1. Dans `public/index.php` (avant tout le reste):**
- ✅ Crée les répertoires: `storage/`, `bootstrap/cache/`, etc.
- ✅ Crée `.env` depuis `.env.example` si manquant
- ✅ S'exécute AVANT que Laravel charge sa configuration
- Élimine les erreurs "No environment file" et "Permission denied"

**2. Dans le middleware `EnsureDatabaseExists` (avant la session):**
- ✅ Crée le fichier SQLite s'il manque
- ✅ Crée la table sessions automatiquement
- Élimine les erreurs "Sessions table not found"

**Résultat:** Aucune erreur 500 au premier accès! ✅

#### Identifiants

```
Email: admin@example.com        (celui rempli dans le formulaire)
Password: (celui rempli dans le formulaire)
```

---

### 🔄 Comment fonctionne la détection?

**Middleware CheckInstallation** vérifie à chaque requête:

```
Requête Web
    ↓
[CheckInstallation Middleware]
    ↓
Fichier storage/app/installed.lock existe?
    ├─ OUI  → App normale ✅
    └─ NON  → Redirige vers /setup 🧙
```

**Le flag file** (`storage/app/installed.lock`):
```json
{
  "installed_at": "2026-01-21T13:00:00Z",
  "php_version": "8.3.30",
  "laravel_version": "12.0.0",
  "database": "mysql"
}
```

### 📱 Interface Responsive

Le Setup Wizard est entièrement responsive:
- ✅ Desktop
- ✅ Tablette
- ✅ Mobile

Avec design moderne et intuitif!

### 🔗 Routes Setup

```
GET  /setup                 → Page d'accueil Setup
GET  /setup/general        → Étape 1 (formulaire infos + sélection BD)
POST /setup/save-general   → Sauvegarde étape 1
GET  /setup/database       → Étape 2 (SEULEMENT si MySQL/PostgreSQL)
POST /setup/test-database  → Test connexion BD (AJAX)
POST /setup/save-database  → Sauvegarde étape 2
GET  /setup/confirm        → Confirmation (Étape 2 ou 3 selon BD)
POST /setup/finish         → Finalise l'installation complète
```

### ✨ Problèmes Résolus

| Problème | Solution |
|----------|----------|
| **Erreur 500 au premier accès** | Middleware crée `.env` depuis `.env.example` avant tout le reste |
| **".env file not found"** | Middleware `EnsureDatabaseExists` le crée automatiquement |
| **"Database file does not exist"** | Middleware crée le fichier + table sessions avant session loading |
| **"Sessions table not found"** | Middleware prépare la table sessions automatiquement |
| **"Composer not found"** | Setup installe Composer automatiquement si `vendor/` manque |
| **"Permission denied" sur storage/** | Setup crée tous les répertoires avec permissions correctes |
| **"APP_KEY not set"** | Setup génère APP_KEY automatiquement |
| **Oublier les infos SQLite** | SQLite est créé automatiquement, aucune config manuelle nécessaire |
| **Configuration MySQL oubliée** | Setup demande TOUT d'abord avant de procéder |

### 📖 Documentation Complète

Pour plus de détails sur le Setup Wizard, voir: [SETUP_WIZARD.md](./SETUP_WIZARD.md)

---

## Installation depuis GitHub

### Clone & Deploy (Déploiement Recommandé)

Cette méthode est idéale pour le déploiement en production sur des serveurs:

```bash
# 1. Cloner depuis GitHub
git clone https://github.com/fay019/api-manager.git
cd api-manager

# 2. Configurer pour votre environnement
cp .env.example .env
nano .env  # Éditer les variables

# 3. Installer les dépendances (sans packages de dev)
composer install --no-dev --optimize-autoloader

# 4. Lancer l'installation complète
php artisan install --force

# 5. Optimiser pour la production
php artisan optimize
php artisan config:cache
php artisan route:cache
```

### Variables d'Environnement Importantes

```bash
# .env pour production
APP_NAME="API Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=api_manager
DB_USERNAME=user
DB_PASSWORD=secure_password

# Performance
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### Push vers GitHub

```bash
# Après la configuration locale
git add .
git commit -m "chore: configure for production"
git push origin master
```

### Déploiement Continu (CI/CD)

Vous pouvez maintenant créer des pipelines GitHub Actions pour automatiser:

```yaml
name: Deploy API Manager

on:
  push:
    branches: [main, master]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2

      - name: Install PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Run installation
        run: php artisan install --force

      - name: Deploy to server
        run: ./deploy.sh
```

---

## Installation Détaillée

### Étape 1: Cloner le projet

```bash
git clone <repository-url>
cd api-manager
```

### Étape 2: Installer les dépendances

```bash
# Dépendances PHP via Composer
composer install

# Dépendances Node.js (optionnel, pour les assets front-end)
npm install
```

### Étape 3: Configuration de l'environnement

```bash
# Copier le fichier d'environnement template
cp .env.example .env

# Générer la clé de l'application
php artisan key:generate

# Adapter .env à votre environnement
nano .env
```

**Variables essentielles dans `.env`:**

```bash
APP_NAME="API Manager"
APP_ENV=local              # local | testing | production
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=sqlite       # sqlite | mysql | pgsql
DB_DATABASE=database/database.sqlite

# Pour MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=api_manager
# DB_USERNAME=root
# DB_PASSWORD=password

# Cache & Session (optionnel)
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### Étape 4: Installation complète

```bash
php artisan install
```

Cette commande exécute tous les processus:
1. ✅ Vérification des prérequis système
2. ✅ Configuration de l'environnement
3. ✅ Publication des fichiers de configuration
4. ✅ Exécution des migrations de base de données
5. ✅ Découverte et initialisation des modules
6. ✅ Remplissage initial de la base de données (seeding)
7. ✅ Configuration du stockage
8. ✅ Compilation des assets front-end
9. ✅ Vérification de la santé de l'application

### Étape 5: Vérification

```bash
php artisan validate:install
```

Tous les checks doivent être verts ✅.

---

## Installation par Étapes

Pour un déploiement progressif ou pour tester chaque étape individuellement:

### Exécuter une étape spécifique

```bash
# Vérifier les prérequis
php artisan install --step=requirements

# Configurer l'environnement
php artisan install --step=environment

# Exécuter les migrations
php artisan install --step=database

# Initialiser les modules
php artisan install --step=modules

# Remplir la base de données
php artisan install --step=seeders

# Compiler les assets
php artisan install --step=assets

# Vérifier la santé
php artisan install --step=health
```

### Étapes disponibles

| Étape | Description | Critique |
|-------|-------------|----------|
| `requirements` | Vérification PHP, extensions, permissions | ✅ OUI |
| `environment` | Configuration .env, APP_KEY, BD SQLite | ✅ OUI |
| `config` | Publication des fichiers de config | ❌ NON |
| `database` | Migrations, création tables | ✅ OUI |
| `modules` | Découverte et init des modules | ✅ OUI |
| `seeders` | Données initiales (dev only) | ❌ NON |
| `storage` | Configuration répertoires de stockage | ✅ OUI |
| `assets` | Compilation front-end (npm run build) | ❌ NON |
| `health` | Vérification finale de santé | ✅ OUI |

**Notes:**
- Les étapes critiques arrêtent l'installation en cas d'erreur
- Les étapes non-critiques continuent même en cas d'erreur
- L'ordre des étapes est important et respecte les dépendances

---

## Validation

### Vérifier que tout fonctionne

```bash
php artisan validate:install
```

Affiche:
- ✅ PHP version
- ✅ Extensions requises
- ✅ Permissions d'écriture
- ✅ Connexion base de données
- ✅ Migrations exécutées
- ✅ Tables essentielles
- ✅ Performance BD

### Découvrir les modules

```bash
# Lister tous les modules
php artisan discover:modules

# Afficher l'ordre d'installation (basé sur les dépendances)
php artisan discover:modules --install-order

# Exporter les modules en JSON
php artisan discover:modules --json
```

---

## Dépannage

### Erreur: "Cannot connect to database"

**Solutions:**
```bash
# 1. Vérifier .env DB_CONNECTION et DB_DATABASE
nano .env

# 2. Pour SQLite, créer le fichier
touch database/database.sqlite
chmod 666 database/database.sqlite

# 3. Pour MySQL, vérifier la connexion
mysql -h 127.0.0.1 -u root -p

# 4. Exécuter les migrations manuellement
php artisan migrate --force
```

### Erreur: "Insufficient permissions"

**Solutions:**
```bash
# Donner les permissions d'écriture
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod 666 database/database.sqlite
```

### Erreur: "Node.js not installed"

**Solution (optionnel):**
```bash
# Si vous ne compilez pas les assets, ce n'est pas grave
# Sinon, installer Node.js:
brew install node        # macOS
apt-get install nodejs   # Ubuntu/Debian
```

### Erreur: "Extension missing"

**Solutions:**
```bash
# Sur macOS avec PHP via Homebrew
pecl install bcmath
pecl install json

# Sur Ubuntu/Debian
apt-get install php-bcmath
apt-get install php-json

# Sur Docker
# Ajouter à votre Dockerfile les extensions manquantes
```

### Réinitialiser l'installation

```bash
# ⚠️ ATTENTION: Cela supprime toutes les données!

# Supprimer la base de données
rm database/database.sqlite

# Réinitialiser le cache des modules
php artisan cache:forget app:module:registry

# Relancer l'installation
php artisan install
```

---

## Environnements de Déploiement

### Développement

```bash
# Dans .env
APP_ENV=local
APP_DEBUG=true

# Installation
php artisan install

# Démarrer
php artisan serve
```

### Staging

```bash
# Dans .env
APP_ENV=staging
APP_DEBUG=false
DB_CONNECTION=mysql

# Installation
php artisan install --force

# Vérifier
php artisan validate:install
```

### Production

```bash
# Dans .env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql

# Installation (skip les seeders de dev)
php artisan install --force --skip-seeds=dev

# Optimiser pour production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier
php artisan validate:install
```

### Docker

```dockerfile
FROM php:8.2-fpm

# Installer les extensions requises
RUN docker-php-ext-install bcmath json pdo pdo_mysql

# Copier le projet
COPY . /var/www/html

WORKDIR /var/www/html

# Installer les dépendances
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage

# Installation
RUN php artisan install --force

EXPOSE 9000
```

---

## Mise à jour de l'application

### Après une mise à jour du code

```bash
# 1. Installer les nouvelles dépendances
composer install

# 2. Exécuter les migrations
php artisan migrate --force

# 3. Vider les caches
php artisan cache:clear
php artisan config:clear

# 4. Redémarrer les services
php artisan queue:restart
```

### Ajouter un nouveau module

Voir [MODULE_CREATION.md](./MODULE_CREATION.md) pour les instructions complètes.

---

## Variables d'environnement avancées

### Installation

```bash
# Désactiver l'installation automatique (CLI seulement)
APP_INSTALLATION_ENABLED=false

# Logging d'installation
INSTALLATION_LOGGING=true
```

### Modules

```bash
# Mettre en cache le registre des modules (production)
APP_ENV=production  # Auto-active le cache

# Chemins personnalisés pour les modules
MODULE_PATHS=/app/modules,/custom/modules
```

### Base de données

```bash
# Mode strict (production)
DB_STRICT=true

# Pool de connexions
DB_POOL_SIZE=10

# Timeout
DB_TIMEOUT=30
```

---

## Support

En cas de problème:

1. Consultez les [logs d'installation](../storage/logs/installation.log)
2. Exécutez `php artisan validate:install`
3. Consultez [DATABASE.md](./DATABASE.md) pour les problèmes de BD
4. Consultez [MODULE_CREATION.md](./MODULE_CREATION.md) pour les modules

---

**Dernière mise à jour**: 21 janvier 2026
**Version du système d'installation**: 1.0.0
