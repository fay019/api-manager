# 📦 Guide d'Installation Complet

Ce document décrit le système d'installation modulaire et robuste de l'application API Manager.

**GitHub Repository**: [fay019/api-manager](https://github.com/fay019/api-manager)

## Table des matières

- [Installation Rapide](#installation-rapide)
- [Installation depuis GitHub](#installation-depuis-github)
- [Installation Détaillée](#installation-détaillée)
- [Installation par Étapes](#installation-par-étapes)
- [Validation](#validation)
- [Dépannage](#dépannage)
- [Environnements de Déploiement](#environnements-de-déploiement)

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
