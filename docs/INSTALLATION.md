# 📖 Guide d'Installation - API Manager

**Version:** 2.0 (Wizard Complet)
**Dernière mise à jour:** 24 janvier 2026
**Status:** ✅ Production Ready

---

## 📋 Table des matières

1. [Prérequis](#prérequis)
2. [Accès au wizard](#accès-au-wizard)
3. [Étape 1: Vérification des prérequis](#étape-1-vérification-des-prérequis)
4. [Étape 2: Paramètres applicatifs](#étape-2-paramètres-applicatifs)
5. [Étape 3: Configuration base de données](#étape-3-configuration-base-de-données)
6. [Étape 4: Configuration email](#étape-4-configuration-email)
7. [Étape 5: Créer administrateur](#étape-5-créer-administrateur)
8. [Étape 6: Récapitulatif](#étape-6-récapitulatif)
9. [Étape 7: Installation finale](#étape-7-installation-finale)
10. [Après installation](#après-installation)
11. [Dépannage](#dépannage)

---

## 🛠️ Prérequis

Avant de commencer, assurez-vous que votre serveur dispose de:

### Système
- **PHP**: 8.3.0 ou supérieur
- **Serveur Web**: Apache 2.4+, Nginx 1.18+
- **Port**: 80 (HTTP) ou 443 (HTTPS)

### Extensions PHP requises
```
✅ PDO (PHP Data Objects)
✅ mbstring (Multi-byte string)
✅ JSON
✅ ctype
✅ filter
✅ hash
✅ OpenSSL
```

### Extensions PHP optionnelles
```
⭐ zip (recommandé)
⭐ fileinfo (recommandé)
⭐ intl (optionnel)
```

### Répertoires writable
```
📁 storage/              (logs, sessions, uploads)
📁 bootstrap/cache/      (cache applicatif)
📁 database/             (base de données SQLite)
```

### Base de données (choisissez une)
- **SQLite**: Inclus (parfait pour démarrer)
- **MySQL**: 5.7+ ou 8.0+
- **PostgreSQL**: 10+

---

## 🚀 Accès au wizard

Une fois l'application déployée:

```
http://votre-domaine.com/setup/welcome
```

Ou en local (Laravel Herd):

```
http://api-manager.test/setup/welcome
```

---

## ✅ Étape 1: Vérification des prérequis

Le système vérifie automatiquement:
- ✅ Version de PHP (8.3+)
- ✅ Extensions requises
- ✅ Permissions des répertoires
- ✅ Fichier .env présent

**Si vous voyez des ❌**:

1. **Extension manquante**: Contactez votre hébergeur
2. **Permission refusée**:
   ```bash
   chmod -R 775 storage bootstrap/cache database
   ```
3. **Fichier .env manquant**: Sera créé automatiquement

**Suite**: Cliquez **"Continuer"** →

---

## 📝 Étape 2: Paramètres applicatifs

### À remplir

**Nom de l'application**
```
Exemple: "API Manager"
```

**URL de l'application**
```
https://api.example.com    (production)
http://api-manager.test    (local)
```
- Doit commencer par `http://` ou `https://`
- Sans trailing slash

**Environnement**
```
local       - Développement
staging     - Test
production  - Production
```

**Fuseau horaire**
```
UTC, Europe/Paris, America/New_York, Asia/Tokyo
```

**Langue**
```
en (English), fr (Français)
```

**Suite**: **"Continuer"** →

---

## 🗄️ Étape 3: Configuration base de données

### SQLite (Recommandé pour démarrer)
```
Chemin: database.sqlite
✅ Aucune installation requise
```

### MySQL
```
Hôte:       localhost
Port:       3306
Base:       api_manager
Utilisateur: api_user
Mot de passe: [votre password]
```

**Créer la BD**:
```sql
CREATE DATABASE api_manager;
CREATE USER 'api_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON api_manager.* TO 'api_user'@'localhost';
FLUSH PRIVILEGES;
```

### PostgreSQL
```
Hôte:       localhost
Port:       5432
Base:       api_manager
Utilisateur: postgres
```

**Test**: Cliquez "Tester la connexion" ✓

**Suite**: **"Continuer"** →

---

## 📧 Étape 4: Configuration email

### SMTP (Recommandé)

**Gmail**
```
Hôte:       smtp.gmail.com
Port:       587
Chiffrement: TLS
Utilisateur: votre-email@gmail.com
Mot de passe: [App Password, pas votre password Gmail]

⚠️ Créez un "App Password" dans les paramètres Gmail (2FA requis)
```

**Mailtrap** (Test)
```
Hôte:       smtp.mailtrap.io
Port:       2525
Chiffrement: TLS
Utilisateur: [Votre ID]
Mot de passe: [Votre token]
```

**Sendgrid**
```
Hôte:       smtp.sendgrid.net
Port:       587
Utilisateur: apikey
Mot de passe: [Votre API Key]
```

### Autres options
- **SendMail**: `/usr/sbin/sendmail -t -i`
- **Log**: Pour développement (emails dans logs)

### Adresse source
```
De: noreply@votre-domaine.com
Nom: API Manager
```

**Test**: Cliquez "Tester la connexion SMTP" ✓

**Suite**: **"Continuer"** →

---

## 👤 Étape 5: Créer administrateur

### Informations

**Nom complet**
```
Exemple: "John Doe"
```

**Email**
```
admin@votre-domaine.com
```
- Utilisé pour se connecter à l'admin

**Mot de passe**

**Critères obligatoires**:
```
✅ Minimum 8 caractères
✅ Au moins 1 MAJUSCULE
✅ Au moins 1 minuscule
✅ Au least 1 chiffre (0-9)
✅ Au moins 1 caractère spécial (@$!%*?&)
```

**Exemples valides**:
```
✅ SecurePass123!
✅ MyApp@2024Admin
✅ Prod#Pass99
```

**Indicateur de force**: Les barres colorées montrent la force en temps réel

**Suite**: **"Continuer"** →

---

## 📋 Étape 6: Récapitulatif

Vérifiez que tout est correct:
- ✓ Nom, URL, environnement
- ✓ Type de BD et identifiants
- ✓ Configuration email
- ✓ Admin email

**⚠️ Attention**: Les mots de passe sont masqués par sécurité

Si tout est OK → **"Lancer l'installation"** ↓

---

## 🚀 Étape 7: Installation finale

Le système exécute automatiquement:

```
1. ✅ Configuration .env
2. ✅ Migrations base de données
3. ✅ Création utilisateur admin
4. ✅ Verrouillage installation (installed.lock)
5. ✅ Redirection tableau de bord
```

**Durée**: 5-30 secondes

**Après**: Page de succès avec redirection automatique vers l'admin

---

## ✨ Après installation

### Première connexion

```
URL:  /admin
Email: admin@votre-domaine.com
Password: [Celui que vous avez créé]
```

### Premiers pas
1. Changer le mot de passe (recommandé)
2. Compléter votre profil
3. Explorer le tableau de bord
4. Lire la documentation

---

## 🔧 Dépannage

Voir **docs/TROUBLESHOOTING.md** pour:
- ❌ Erreurs de connexion BD
- ❌ Problèmes SMTP/email
- ❌ Erreurs de permissions
- ❌ Erreurs PHP/extensions

---

## 📞 Support

- Email: support@example.com
- Documentation: `/admin/help`
- Bugs: GitHub Issues

---

**Installation réussie? 🎉 Bienvenue dans API Manager!**
