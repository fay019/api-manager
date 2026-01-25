# 🔧 Dépannage - FAQ Installation

**Version:** 2.0
**Dernière mise à jour:** 24 janvier 2026

---

## 📋 Index rapide

- [Erreurs PHP/Système](#erreurs-phpsystème)
- [Erreurs Base de données](#erreurs-base-de-données)
- [Erreurs Email/SMTP](#erreurs-emailsmtp)
- [Erreurs Permissions](#erreurs-permissions)
- [Problèmes Installation](#problèmes-installation)
- [Après Installation](#après-installation)

---

## 🔴 Erreurs PHP/Système

### ❌ "PHP 8.3 est requis"

**Cause**: Votre serveur utilise PHP < 8.3

**Solution**:
1. Vérifiez votre version PHP:
   ```bash
   php --version
   ```

2. **Si vous pouvez changer la version**:
   - Contactez votre hébergeur
   - Demandez la mise à jour vers PHP 8.3+
   - Vérifiez les paramètres de votre panel d'administration

3. **Si vous ne pouvez pas changer**:
   - Changez d'hébergeur
   - Utilisez un serveur local avec PHP 8.3+

---

### ❌ "Extension XXX manquante"

**Cause**: Une extension PHP requise n'est pas installée

**Extensions requises**:
```
PDO, mbstring, JSON, ctype, filter, hash, OpenSSL
```

**Solution**:

1. **Demander à votre hébergeur** (méthode recommandée)
   - Email: support@hosting.com
   - "Veuillez installer l'extension PHP: xxx"

2. **Si vous avez accès SSH**:
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php8.3-pdo php8.3-mbstring php8.3-json

   # CentOS/RHEL
   sudo yum install php83-pdo php83-mbstring php83-json

   # Après installation, redémarrer PHP
   sudo systemctl restart php-fpm
   ```

3. **Vérifier après installation**:
   ```bash
   php -m | grep pdo
   ```

---

## 🔴 Erreurs Base de données

### ❌ "Impossible se connecter à la base de données"

**Écrans concernés**:
- Étape 3: Configuration BD
- Test de connexion

**Causes possibles**:

#### SQLite
```
✅ Pas besoin de configurer
✅ Fonctionne toujours
```

#### MySQL - Serveur non accessible
```
❌ Hôte inaccessible
❌ Port fermé (3306)
```

**Solution**:
1. Vérifier que MySQL est démarré:
   ```bash
   sudo systemctl status mysql
   ```

2. Vérifier que vous pouvez vous connecter:
   ```bash
   mysql -u root -p
   ```

3. Si ça ne fonctionne pas:
   - Redémarrer MySQL: `sudo systemctl restart mysql`
   - Vérifier le pare-feu: `sudo ufw allow 3306`

#### MySQL - Authentification échouée
```
❌ Utilisateur invalide
❌ Mot de passe incorrect
```

**Solution**:
1. Vérifier les identifiants:
   ```bash
   mysql -u api_user -p -h localhost
   ```

2. Créer un nouvel utilisateur:
   ```sql
   CREATE USER 'api_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON api_manager.* TO 'api_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

#### PostgreSQL - Serveur non accessible
```
Port: 5432 (par défaut)
```

**Solution**:
1. Vérifier PostgreSQL:
   ```bash
   sudo systemctl status postgresql
   ```

2. Redémarrer:
   ```bash
   sudo systemctl restart postgresql
   ```

---

### ❌ "Database XXX n'existe pas"

**Solution**:
1. **MySQL**:
   ```sql
   CREATE DATABASE api_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **PostgreSQL**:
   ```sql
   CREATE DATABASE api_manager;
   ```

3. **Vérifier la création**:
   ```bash
   # MySQL
   mysql -e "SHOW DATABASES;" | grep api_manager

   # PostgreSQL
   psql -l | grep api_manager
   ```

---

## 🔴 Erreurs Email/SMTP

### ❌ "Connexion SMTP échouée"

**Écran concerné**: Étape 4, Test de connexion email

**Causes possibles**:

#### Host invalide
```
❌ smtp.example.com n'existe pas
```

**Vérification**:
```bash
ping smtp.gmail.com
nslookup smtp.gmail.com
```

#### Port fermé
```
❌ Port 587 ou 465 fermé
❌ Pare-feu bloque la connexion
```

**Commandes de test**:
```bash
# Tester la connexion
nc -zv smtp.gmail.com 587

# Si firewalls:
telnet smtp.gmail.com 587
```

#### Authentification refusée
```
❌ Utilisateur incorrect
❌ Mot de passe incorrect
```

**Solutions par service**:

#### Gmail
```
❌ ERREUR: Mot de passe incorrect
✅ SOLUTION: Utiliser un "App Password"

1. Aller à: https://myaccount.google.com/security
2. Activer "2-Step Verification" si pas fait
3. Générer "App Passwords"
4. Choisir Mail → Windows/Linux/Mac
5. Copier le mot de passe généré (16 caractères)
6. L'utiliser dans le wizard
```

#### Mailtrap
```
❌ Authentification échouée
✅ SOLUTION: Utiliser les bonnes identifiants

1. Aller à: https://mailtrap.io/inboxes
2. Cliquer sur "SMTP Settings"
3. Copier le "Username" et "Password"
4. Port: 2525
5. Chiffrement: TLS
```

#### Sendgrid
```
❌ Authentification échouée
✅ SOLUTION: Utiliser API Key

1. Aller à: https://app.sendgrid.com/settings/api_keys
2. Créer une clé
3. Copier la clé
4. Utilisateur: "apikey"
5. Mot de passe: [La clé]
```

---

### ❌ "Les emails ne s'envoient pas"

**Après installation**: Les emails configurés ne sont pas envoyés

**Solution**:

1. **Vérifier la configuration .env**:
   ```bash
   cat .env | grep MAIL
   ```

2. **Tester l'envoi manuel**:
   ```bash
   php artisan tinker
   > Mail::raw('Test', function ($m) {
   >   $m->to('votre-email@example.com')->send();
   > });
   ```

3. **Vérifier les logs**:
   ```bash
   tail -50 storage/logs/laravel.log | grep -i mail
   ```

4. **Si mode Log**: Vérifier les logs directs
   ```bash
   grep -i "test message" storage/logs/laravel.log
   ```

---

## 🔴 Erreurs Permissions

### ❌ "Répertoire non writable"

**Répertoires concernés**:
```
📁 storage/
📁 bootstrap/cache/
📁 database/
```

**Cause**: Permissions insuffisantes

**Solution - Simple**:
```bash
chmod -R 775 storage bootstrap/cache database
```

**Solution - Sécurisée** (recommandée):
```bash
# Utilisateur web: www-data (Nginx/Apache2)
sudo chown -R www-data:www-data storage bootstrap/cache database
sudo chmod -R 755 storage bootstrap/cache database
```

**Sur cPanel/Shared Hosting**:
```bash
chmod -R 755 storage bootstrap/cache database
chmod -R 644 storage bootstrap/cache database/.gitkeep
```

---

## 🔴 Problèmes Installation

### ❌ "Installation timeout (60s dépassé)"

**Cause**: Opération trop lente (migrations, seeds)

**Solution**:
1. **Augmenter le timeout** (fichier `.htaccess`):
   ```apache
   php_value max_execution_time 300
   ```

2. **Ou via PHP-FPM** (`/etc/php/8.3/fpm/php.ini`):
   ```ini
   max_execution_time = 300
   ```

3. **Puis redémarrer**:
   ```bash
   sudo systemctl restart php-fpm
   ```

---

### ❌ "Mot de passe non valide"

**Critères oubliés**:
```
✅ Min 8 caractères
✅ Au moins 1 MAJUSCULE
✅ Au moins 1 minuscule
✅ Au moins 1 chiffre
✅ Au moins 1 spécial (@$!%*?&)
```

**Exemples**:
```
❌ password         (pas MAJ, pas chiffre, pas spécial)
❌ Password         (pas chiffre, pas spécial)
❌ Password1        (pas spécial)
✅ Password1!       (tout bon)
✅ SecurePass123@   (tout bon)
```

---

### ❌ "Installation déjà complétée"

**Message**: "Setup locked - Application already installed"

**Cause**: Fichier `storage/app/installed.lock` existe

**Solution**:

Pour **réinstaller**, voir la section "Test Installation Réel" en bas.

---

## 🟢 Après Installation

### ✅ "Comment se connecter à l'admin?"

```
URL:     http://api-manager.test/admin
Email:   [Celui que vous avez créé]
Mot de passe: [Celui que vous avez créé]
```

---

### ✅ "Changer le mot de passe admin"

1. Se connecter à `/admin`
2. Cliquer sur son profil (haut droite)
3. "Paramètres de compte"
4. "Changer le mot de passe"

---

### ✅ "Réinitialiser mot de passe oublié"

1. Via SSH/Terminal:
   ```bash
   php artisan tinker
   > $user = App\Models\User::first();
   > $user->password = Hash::make('NewPassword123!');
   > $user->save();
   > exit
   ```

2. Puis vous connecter avec `NewPassword123!`

---

## 📞 Besoin d'aide?

Si le problème n'est pas listé:

1. **Vérifier les logs**:
   ```bash
   tail -100 storage/logs/laravel.log
   ```

2. **Copier le message d'erreur complet**

3. **Contacter le support**:
   - Email: support@example.com
   - Inclure les logs et la configuration

---

**Dernière mise à jour**: 24 janvier 2026
**Version du wizard**: 7 étapes, fully automatic
