# 🧙 Setup Wizard - Installation Interactive (7 Étapes)

Le Setup Wizard est une interface web moderne pour installer et configurer votre application en quelques minutes.

**Status:** ✅ Complètement implémenté et testé

---

## 🎯 Vue d'ensemble

Au lieu de configuration manuelle:

1. **Cloner le projet**
2. **Visiter `/setup/welcome`**
3. **Suivre le wizard (7 étapes)**
4. **Application prête à utiliser!**

```
Durée: 2-5 minutes
Compétences requises: Aucune (interface graphique)
Accessibilité: Responsive (mobile, tablette, desktop)
Sécurité: SSL-ready, password fort, CSRF protégé
```

---

## 📋 Les 7 Étapes

### 1️⃣ Welcome - Vérification des Prérequis
**URL:** `GET /setup/welcome`

Affiche l'état du serveur:

```
✅ PHP 8.3.30
✅ Extension PDO (MySQL, PostgreSQL, SQLite)
✅ Extension mbstring
✅ Extension JSON
✅ Extension ctype
✅ Extension filter
✅ Extension hash
✅ Extension openssl
⚠️ Extension zip (optionnel)
✅ Permissions storage/
✅ Permissions bootstrap/cache/
✅ Permissions database/
```

**Validations:**
- Vérifie PHP 8.3+
- Vérifie extensions requises (7 obligatoires)
- Vérifie extensions optionnelles (3 bonus)
- Vérifie permissions dossiers
- Affiche hostname, HTTPS status, memory limit

**Action suivante:** Continuer vers configuration app

---

### 2️⃣ App Settings - Paramètres Applicatifs
**URL:** `GET /POST /setup/app-settings`

Configuration générale de l'application:

```
┌─────────────────────────────────────────┐
│ Nom de l'Application                    │
│ [auto-détecté: API Manager          ]  │
├─────────────────────────────────────────┤
│ URL de l'Application                    │
│ [auto-détecté: http://api-manager.test] │
├─────────────────────────────────────────┤
│ Environnement                           │
│ ◉ Local (développement)                 │
│ ◉ Staging (test)                        │
│ ◉ Production                            │
├─────────────────────────────────────────┤
│ Fuseau Horaire                          │
│ [Europe/Paris                        ▼] │
├─────────────────────────────────────────┤
│ Langue par Défaut                       │
│ ◉ Français  ◉ English  ◉ Español        │
└─────────────────────────────────────────┘
```

**Auto-Détection:**
- **APP_NAME**: Depuis nom du dossier (api-manager → API Manager)
- **APP_URL**: Depuis HTTP_HOST + HTTPS status
- **APP_ENV**: Détecte localhost (retourne "local", sinon "production")
- **TIMEZONE**: Défaut Europe/Paris
- **LOCALE**: Défaut Français

**Validations:**
- Nom: 3-255 caractères
- URL: Format URL valide
- Environnement: local|staging|production
- Timezone: PHP timezone valide
- Locale: fr|en|es

**Validations Logiques:**
- ⚠️ APP_DEBUG=false obligatoire en production

**Action suivante:** Continuer vers configuration BD

---

### 3️⃣ Database - Configuration Base de Données
**URL:** `GET /POST /setup/database` + `POST /setup/database/test`

Choisir et configurer la base de données:

```
┌──────────────────────────────────────────┐
│ Type de Base de Données                  │
├──────────────────────────────────────────┤
│ ◉ SQLite (Fichier local)                 │
│   Base de données fichier, idéale pour   │
│   développement                          │
│                                          │
│ ◉ MySQL                                  │
│   Serveur MySQL (5.7+) ou MariaDB        │
│                                          │
│ ◉ PostgreSQL                             │
│   Serveur PostgreSQL (10+)               │
└──────────────────────────────────────────┘
```

#### Si SQLite
```
┌─────────────────────────────┐
│ Chemin du fichier           │
│ [api_manager.sqlite      ]  │
│ Sera créé dans storage/database/
└─────────────────────────────┘
```

#### Si MySQL
```
┌─────────────────────────────┐
│ Serveur / Host              │
│ [localhost              ]   │
├─────────────────────────────┤
│ Port                        │
│ [3306                   ]   │
├─────────────────────────────┤
│ Base de Données             │
│ [api_manager            ]   │
├─────────────────────────────┤
│ Utilisateur                 │
│ [root                   ]   │
├─────────────────────────────┤
│ Mot de Passe                │
│ [••••••••                ]  │
│
[🔧 Tester la connexion] ← AJAX test
└─────────────────────────────┘
```

#### Si PostgreSQL
```
┌─────────────────────────────┐
│ Serveur / Host              │
│ [localhost              ]   │
├─────────────────────────────┤
│ Port                        │
│ [5432                   ]   │
├─────────────────────────────┤
│ Base de Données             │
│ [api_manager            ]   │
├─────────────────────────────┤
│ Utilisateur                 │
│ [postgres               ]   │
├─────────────────────────────┤
│ Mot de Passe                │
│ [••••••••                ]  │
│
[🔧 Tester la connexion] ← AJAX test
└─────────────────────────────┘
```

**Test AJAX:**
- Clique "Tester la connexion"
- Valide PDO connection en temps réel
- Affiche: ✅ Connexion réussie OU ❌ Erreur détaillée

**Validations:**
- SQLite: nom fichier 1-255 chars
- MySQL/PostgreSQL:
  - Host: 1-255 chars
  - Port: 1-65535
  - Database: 1-255 chars
  - Username: 1-255 chars
  - Password: optionnel

**Erreurs Courantes:**
- ❌ "Connexion refusée" → Vérifier host et port
- ❌ "Access denied" → Vérifier username/password
- ❌ "Unknown database" → Créer BD avant continuer

**Action suivante:** Continuer vers configuration email

---

### 4️⃣ Mail - Configuration Email
**URL:** `GET /POST /setup/mail` + `POST /setup/mail/test`

Choisir et configurer l'envoi email:

```
┌──────────────────────────────────────────┐
│ Serveur Email                            │
├──────────────────────────────────────────┤
│ ◉ SMTP                                   │
│   Serveur SMTP (Gmail, Mailtrap, etc)    │
│                                          │
│ ◉ SendMail                               │
│   Binaire sendmail local                 │
│                                          │
│ ◉ Log (Développement)                    │
│   Enregistre emails dans logs            │
│                                          │
│ ◉ Mailgun                                │
│   Service Mailgun (API)                  │
└──────────────────────────────────────────┘
```

#### Si SMTP
```
📌 Exemples:
   • Gmail: smtp.gmail.com:587 (TLS)
     ⚠️ Utiliser App Password, pas mot de passe Google
   • Mailtrap: smtp.mailtrap.io:2525 (TLS)
   • Sendgrid: smtp.sendgrid.net:587 (TLS)
     Username: apikey

┌─────────────────────────────┐
│ Serveur SMTP                │
│ [smtp.mailtrap.io       ]   │
├─────────────────────────────┤
│ Port                        │
│ [2525                   ]   │ ← TLS: 587, SSL: 465
├─────────────────────────────┤
│ Chiffrement                 │
│ ◉ TLS (587)  ◉ SSL (465)   │
├─────────────────────────────┤
│ Nom d'utilisateur           │
│ [utilisateur@example.com]   │
├─────────────────────────────┤
│ Mot de Passe                │
│ [••••••••                ]  │
│
[🔧 Tester la connexion] ← AJAX test SMTP
└─────────────────────────────┘
```

#### Si SendMail
```
┌─────────────────────────────┐
│ Chemin du binaire sendmail  │
│ [/usr/sbin/sendmail -t -i]  │
│ Défaut: /usr/sbin/sendmail -t -i
└─────────────────────────────┘
```

#### Si Log
```
✅ Mode Log activé
   Les emails seront enregistrés dans
   storage/logs/ (développement)
```

#### Configuration Commune
```
┌──────────────────────────┐
│ Adresse Source (From)    │
├──────────────────────────┤
│ Adresse Email            │
│ [noreply@api-manager.test│
├──────────────────────────┤
│ Nom Source               │
│ [API Manager         ]   │
└──────────────────────────┘
```

**Test AJAX (SMTP seulement):**
- Clique "Tester la connexion SMTP"
- Valide connexion Symfony Mailer
- Affiche: ✅ OK OU ❌ Erreur spécifique

**Validations:**
- SMTP: host, port, username obligatoires
- Password: optionnel
- Encryption: tls|ssl|none
- Email from: format email valide

**Erreurs Courantes:**
- ❌ "Authentification échouée" → Vérifier username/password
- ❌ "TLS non supporté" → Essayer SSL ou aucun chiffrement
- ❌ "Connection refused" → Vérifier host:port

**Action suivante:** Continuer vers création administrateur

---

### 5️⃣ Admin - Créer l'Administrateur
**URL:** `GET /POST /setup/admin`

Créer le premier utilisateur administrateur:

```
⚠️ Information importante:
   Cet utilisateur aura accès administrateur
   complet. Conservez les informations en lieu sûr.

┌─────────────────────────────┐
│ Nom Complet                 │
│ [Jean Dupont            ]   │
├─────────────────────────────┤
│ Adresse Email               │
│ [admin@example.com      ]   │ ← Email unique
├─────────────────────────────┤
│ Mot de Passe                │
│ [••••••••                ]  │
│
│ Force: ▓░░░ Faible         ← 4 barres indicateur
│
│ ✅ Minimum 8 caractères     ← Checklist en temps réel
│ ❌ Au moins une MAJUSCULE
│ ✅ Au moins une minuscule
│ ✅ Au moins un chiffre
│ ❌ Au moins un spécial (@$!%*?&)
│
│ Exemple: Azerty123!
├─────────────────────────────┤
│ Confirmer le Mot de Passe   │
│ [••••••••                ]  │
├─────────────────────────────┤
│ ☑ J'accepte la responsabilité
│   de cet accès administrateur
└─────────────────────────────┘
```

**Indicateur Force en Temps Réel:**
- 4 barres de couleur (progresse au fur et à mesure)
- Checklist ✅/❌ pour chaque requirement
- Bouton "Continuer" désactivé jusqu'à password fort

**Validations:**
- Nom: 3-255 caractères
- Email: Format email valide
- Password: 8+, majuscule, minuscule, chiffre, spécial (regex)
- Confirmation: Doit correspondre

**Sécurité:**
- Password pattern stricte: `^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$`
- Pas de validation email unique ici (fait à la création réelle)
- Confirmation obligatoire

**Action suivante:** Continuer vers récapitulatif

---

### 6️⃣ Review - Récapitulatif Complet
**URL:** `GET /setup/review`

Affiche la configuration complète avant installation:

```
┌──────────────────────────────────────────┐
│ 📋 PARAMÈTRES APPLICATIFS                │
├──────────────────────────────────────────┤
│ Nom                │ API Manager          │
│ URL                │ http://api-manager.test
│ Environnement      │ Local (développement) │
│ Fuseau             │ Europe/Paris         │
│ Langue             │ FR                   │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│ 🗄️ BASE DE DONNÉES                       │
├──────────────────────────────────────────┤
│ Type               │ SQLite               │
│ Base de Données    │ api_manager.sqlite   │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│ 📧 EMAIL                                 │
├──────────────────────────────────────────┤
│ Type               │ SMTP                 │
│ Serveur            │ smtp.mailtrap.io:2525│
│ Adresse Source     │ noreply@api-manager.test
│ Nom Source         │ API Manager          │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│ 👤 ADMINISTRATEUR                        │
├──────────────────────────────────────────┤
│ Nom                │ Jean Dupont          │
│ Email              │ admin@example.com    │
│ Mot de Passe       │ ••••••••             │ ← Masqué
└──────────────────────────────────────────┘

ℹ️ Notes importantes:
   • Les informations sensibles ne sont pas affichées
   • Vous devrez vous connecter après l'installation
   • Vous pourrez créer d'autres utilisateurs ensuite
```

**Validations:**
- Affiche avertissements si données manquantes
- Bouton "Installer" désactivé si incomplet
- Masque les passwords (••••••••)

**Action suivante:** Confirmer + Installer

---

### 7️⃣ Success - Installation Réussie
**URL:** `POST /setup/install` → `GET /setup/success`

Installation finale et confirmation:

```
✅ Installation Réussie!
Étape 7/7

✨ Prochaines étapes:
   • Vous allez être redirigé vers la page de connexion
   • Connectez-vous avec l'email et mot de passe créés
   • Vous aurez accès au tableau de bord complet

ℹ️ Informations importantes:
   • L'application est bloquée contre les réinstallations
   • Les informations de configuration sont sauvegardées
   • Vous pouvez créer d'autres utilisateurs maintenant

[🚀 Accéder au Tableau de Bord]

Redirection automatique dans 5 secondes...
```

**Actions Orchestrées Pendant Installation:**
1. ✅ Valide toutes les données de session
2. ✅ Configure .env (app, BD, email)
3. ✅ Exécute migrations: `php artisan migrate --force`
4. ✅ Crée utilisateur admin (password hashé)
5. ✅ Crée `installed.lock` (SHA256 hash)
6. ✅ Nettoie session
7. ✅ Redirige vers succès

**Après Installation:**
- ✅ `/setup/welcome` retourne 403 Forbidden
- ✅ Application est accessible sur `/admin`
- ✅ BD est peuplée avec migrations
- ✅ Utilisateur admin créé et hashé

---

## 🔐 Sécurité

### ✅ Validations Complètes
- FormRequest pour tous les formulaires
- Validation conditionnelle par type (driver)
- Messages d'erreur détaillés en Français
- Regex password très fort

### ✅ Protection Routes
- Middleware CheckInstallation
- 403 Forbidden après installation
- Rate limiting (30 req/10 min sur /setup)

### ✅ Secrets Masqués
- Passwords non affichés dans les vues
- Credentials stockés en session uniquement
- Password hashé avec Laravel Hash::make()

### ✅ CSRF Protection
- @csrf token sur tous les formulaires
- Validation automatique Laravel

### ✅ Installation Lock
- Hash SHA256 empêche réinstallation
- Metadata: date, php_version, db type

---

## 📱 Responsive Design

- ✅ Mobile (320px+)
- ✅ Tablette (768px+)
- ✅ Desktop (1024px+)
- ✅ Touch-friendly buttons (48px minimum)
- ✅ Readable fonts (14px minimum)

---

## ⚡ Tests AJAX

### Database Test
```javascript
POST /setup/database/test
{
  "database_driver": "sqlite|mysql|pgsql",
  "database_host": "localhost",     // if not sqlite
  "database_port": 3306,            // if not sqlite
  "database_database": "api_manager",
  "database_username": "root",      // if not sqlite
  "database_password": "password"   // optional
}

Response:
{
  "success": true,
  "message": "Connexion réussie"
}
// or
{
  "success": false,
  "errors": {
    "connection": "Connexion refusée (vérifier host et port)"
  }
}
```

### Mail SMTP Test
```javascript
POST /setup/mail/test
{
  "mail_driver": "smtp",
  "mail_host": "smtp.mailtrap.io",
  "mail_port": "2525",
  "mail_username": "user@example.com",
  "mail_password": "password",
  "mail_encryption": "tls"
}

Response:
{
  "success": true,
  "message": "Connexion SMTP réussie"
}
// or
{
  "success": false,
  "message": "Connexion SMTP échouée",
  "errors": {
    "connection": "Authentification échouée..."
  }
}
```

---

## 🚀 Utilisation

### Pour L'Utilisateur Final
```bash
1. Cloner le projet
2. Visiter http://api-manager.test/setup/welcome
3. Suivre les 7 étapes
4. Se connecter au tableau de bord
```

### Pour Le Développeur
```bash
# Vérifier routes
php artisan route:list --path=setup

# Tester services
php artisan tinker
> resolve('App\Services\Installation\RequirementsChecker')->check()
> resolve('App\Services\Installation\EnvManager')->all()

# Accéder wizard
http://api-manager.test/setup/welcome

# Vérifier installation
ls -la storage/app/installed.lock
```

---

## 📚 Documentation Supplémentaire

- **SYSTEM_READY.md** - État final du système
- **NEXT_STEPS.md** - Tâches réalisées + prochaines étapes
- **INSTALLATION.md** - Guide client complet
- **TROUBLESHOOTING.md** - Dépannage courant

---

**Status:** ✅ Complètement implémenté
**Mis à jour:** 24 janvier 2026
**Prêt pour:** Production
