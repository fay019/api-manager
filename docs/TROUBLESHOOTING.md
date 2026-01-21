# 🔧 Troubleshooting & Debugging

Guide de dépannage pour les problèmes courants avec API Manager.

---

## 🚀 Installation & Déploiement

### Erreur 500 - "Domain works but Laravel crashes"

Si vous accédez au domaine et voyez une erreur 500 Laravel:

**Cause possible:** Installation incomplète ou configuration manquante

**Solution:**

1. **Allez à la page d'installation:**
   ```
   https://your-domain.com/setup
   ```
   Cette page démarre l'assistant d'installation automatique.

2. **Attendez que l'installation se termine** - Elle va:
   - Installer les dépendances Composer
   - Générer la clé APP_KEY
   - Créer les répertoires nécessaires
   - Créer la base de données
   - Exécuter les migrations
   - Lancer les seeders
   - Créer l'utilisateur administrateur

3. **Si l'erreur persiste**, consultez le fichier de log:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

### "Database file at path does not exist" (Résolu ✅)

**Cause:** La base de données SQLite n'a pas été créée avant le premier accès

**Pourquoi ça ne devrait plus arriver:**

Le middleware `EnsureDatabaseExists` crée maintenant automatiquement:
- ✅ Le fichier `database/database.sqlite`
- ✅ La table `sessions` requise par Laravel
- ✅ Cela se fait **AVANT** le chargement de la session

**Si vous rencontrez encore cette erreur:**

#### Option 1: Via l'assistant d'installation (Recommandé)
- Accédez à `/setup` - le middleware crée tout automatiquement
- Suivez les étapes du wizard

#### Option 2: Vérifier les permissions
```bash
# S'assurer que database/ est accessible
chmod -R 755 database/
ls -la database/
```

#### Option 3: Manuellement en SSH (si le middleware ne fonctionne pas)
```bash
# Créer le fichier database.sqlite
mkdir -p database
touch database/database.sqlite
chmod 666 database/database.sqlite

# Exécuter les migrations
php artisan migrate

# Lancer les seeders
php artisan db:seed
```

---

### Erreur ".env file not found" ou ".env: No such file" (Résolu ✅)

**Symptôme:** 500 error au premier accès avec "No environment file"

**Cause:** Laravel ne trouve pas le fichier `.env`

**Résolution:**

La fonction `ensureApplicationReady()` dans `public/index.php` crée maintenant automatiquement `.env` depuis `.env.example`:
- S'exécute AVANT le chargement de la configuration Laravel
- S'exécute AVANT les middlewares
- Crée `.env` si `.env.example` existe
- Crée aussi tous les répertoires nécessaires

**Résultat:** Aucune erreur "env file not found" au premier accès! ✅

---

### Erreur lors du chargement de la session (résolu ✅)

**Symptôme:** 500 error sur `/setup` avec message "select * from sessions"

**Cause:** Avant, Laravel essayait de charger la session depuis la base de données avant que SQLite existe

**Résolution:**

Le middleware `EnsureDatabaseExists` s'exécute maintenant **AVANT** la session:
- Crée `.env` depuis `.env.example`
- Crée le fichier SQLite si manquant
- Crée la table sessions automatiquement

**Résultat:** Aucun risque de "sessions table not found" au premier accès! ✅

---

### "Cannot redeclare non static Widget::$view"

**Cause:** Incompatibilité de version ou cache obsolète

**Solution:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```

---

### Permissions insuffisantes

**Symptôme:** "Permission denied" lors de l'écriture dans storage/

**Solution:**
```bash
# Sur Linux/Mac
chmod -R 775 storage bootstrap/cache

# Sur certains serveurs, créer les répertoires s'ils manquent
mkdir -p storage/framework/{cache,data,sessions,views,testing}
mkdir -p storage/logs
mkdir -p storage/app
mkdir -p bootstrap/cache
```

---

### "composer: command not found"

**Cause:** Composer n'est pas installé ou non dans le PATH

**Solutions:**

1. **Installer Composer** (si nécessaire):
   ```bash
   curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
   ```

2. **Utiliser Composer global:**
   ```bash
   /usr/local/bin/composer install
   ```

3. **Sur le serveur, si Composer n'est pas disponible:**
   - L'assistant d'installation essayera de le lancer mais créera une erreur si absent
   - Solution: Installer Composer d'abord, ou lancer manuellement:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   ```

---

## 📊 Configuration & Environnement

### APP_KEY manquante ou vide

**Symptôme:** "No application encryption key has been specified"

**Cause:** `APP_KEY` non défini dans `.env`

**Solution:**
```bash
php artisan key:generate
```

L'assistant d'installation le fait automatiquement.

---

### Database connection refused

**Cause:**
- Identifiants MySQL/PostgreSQL incorrects
- Serveur de base de données non accessible
- Base de données non créée

**Debug:**
1. Testez la connexion via l'assistant d'installation
2. Vérifiez les identifiants `.env`:
   ```
   DB_HOST=
   DB_PORT=
   DB_DATABASE=
   DB_USERNAME=
   DB_PASSWORD=
   ```

3. Testez manuellement:
   ```bash
   # Pour MySQL
   mysql -h DB_HOST -u DB_USERNAME -p DB_PASSWORD -e "use DB_DATABASE;"

   # Pour PostgreSQL
   psql -h DB_HOST -U DB_USERNAME -d DB_DATABASE
   ```

---

## 🔍 Debugging & Logs

### Consulter les logs

**Log principal Laravel:**
```bash
tail -f storage/logs/laravel.log
```

**Installation log:**
```bash
cat storage/logs/installation.log
```

**Logs en temps réel (Pail):**
```bash
php artisan pail
```

---

### Mode debug activé

Pour plus d'informations, activez le mode debug dans `.env`:
```env
APP_DEBUG=true
```

⚠️ **Attention:** Désactivez-le en production (`APP_DEBUG=false`)

---

### Vérifier l'état de l'application

```bash
# Vue d'ensemble de la santé
php artisan tinker

# Dans la console Tinker:
>>> app()->isInstalled()
>>> config('database.default')
>>> DB::connection()->getPdo()
```

---

## 🌐 Serveur Web

### Nginx - Erreur "Access Denied"

**Cause:** Permissions sur le répertoire public/

**Solution:**
```bash
# S'assurer que Nginx peut lire public/
chmod -R 755 public
chown -R www-data:www-data . # Ou l'utilisateur Nginx
```

**Configuration Nginx exemple:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

### Apache - mod_rewrite non activé

**Symptôme:** Les routes ne fonctionnent pas, tout redirect vers 404

**Solution:**
```bash
# Activer mod_rewrite
a2enmod rewrite
systemctl restart apache2
```

**Vérifier le `.htaccess` dans public/:**
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ /index.php [L]
</IfModule>
```

---

## 📱 API Issues

### "Unauthorized" 401 sur les routes API

**Cause:** Clé API manquante ou invalide

**Debug:**
1. Vérifiez que la clé API est envoyée dans le header:
   ```
   X-API-KEY: apk_xxxxx
   ```

2. Consultez les logs des requêtes:
   ```
   Admin Panel → API Management → Request Logs
   ```

---

### Erreur "429 Too Many Requests"

**Cause:** Rate limiting activé

**Solution:**
1. Vérifier les paramètres du client API dans l'admin panel
2. Augmenter la limite si nécessaire
3. Attendre le reset du rate limiter (par défaut: 1 minute)

---

## 🆘 Page de Maintenance

Une page de maintenance statique est disponible à:
```
public/maintenance.html
```

Cette page s'affiche automatiquement si Laravel plante et peut être consultée sans dépendance Laravel. Elle vous guidera pour:
- Vérifier la connexion au domaine
- Accéder à l'assistant d'installation
- Exécuter les commandes de correction

**Note:** Cette page se recharge automatiquement toutes les 10 secondes pour vérifier la récupération de l'application.

---

## 📞 Besoin d'aide supplémentaire?

1. **Consultez les logs:** `storage/logs/laravel.log`
2. **Lancez l'installation:** Accédez à `/setup`
3. **Vérifiez la documentation:** [Deployment Guide](./DEPLOYMENT.md)
4. **Contacter le support:** GitHub Issues ou support email

---

**Last Updated:** 2026-01-21
**Version:** API Manager v1.0
