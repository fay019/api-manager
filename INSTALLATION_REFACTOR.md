# Refonte Système d'Installation - API Manager

## 📊 État d'avancement

### ✅ Phase 1: Services & Contrats (100%)
- **RequirementsChecker** ✓
  - Vérifie PHP version, extensions, permissions
  - Cache résultats 5 minutes

- **EnvManager** ✓
  - Gère création/lecture/modification .env
  - Write atomique, backup automatiques
  - Gestion clés .env exhaustive

- **InstallationCheck** ✓
  - Vérifier installation complétée
  - Validation intégrité
  - Création lock avec hash sécurisé

- **Config installation.php** ✓
  - Tous les paramètres configurés
  - Étendus à partir config existante

- **Exceptions** ✓
  - InstallationException base
  - Méthodes utilitaires toArray(), toJson()

### ✅ Phase 2: Middleware & Routes (100%)
- **Middleware CheckInstallation** ✓
  - Bloque routes app si non installé
  - Bloque /setup après installation (403)
  - Routes autorisées avant install définies
  - Enregistré dans bootstrap/app.php

- **Routes setup.php** ✓
  - 7 étapes complètement documentées
  - Routes group avec middleware
  - Inclus dans web.php

### ⚠️ Phase 3: Contrôleurs & Vues (50%)

#### Contrôleurs créés:
- **WelcomeController** ✓
  - Affiche prérequis et résultats vérifications
  - Détecte installation déjà complétée
  - Permet relancer vérifications

- **AppSettingsController** ✓
  - Détection auto APP_NAME, APP_URL, APP_ENV
  - Formulaire avec pré-remplissage
  - Validation cohérence (DEBUG ≠ PROD)
  - Stockage en session

#### Contrôleurs à créer:
```
DatabaseController
├─ GET /setup/database → Form config DB
├─ POST /setup/database → Save config
└─ POST /setup/database/test → Test connexion (AJAX)

MailController
├─ GET /setup/mail → Form config email
├─ POST /setup/mail → Save config
└─ POST /setup/mail/test → Test SMTP (AJAX)

AdminController
├─ GET /setup/admin → Form création admin
└─ POST /setup/admin → Create user + store session

ReviewController
├─ GET /setup/review → Afficher récapitulatif
└─ (POST handle dans SuccessController)

SuccessController
├─ POST /setup/install → Lancer installation
└─ GET /setup/success → Afficher succès
```

#### FormRequests créés:
- **AppSettingsRequest** ✓

#### FormRequests à créer:
- DatabaseRequest
- MailRequest
- AdminRequest

#### Vues créées:
- **layout.blade.php** ✓ (layout de base améliorable)

#### Vues à créer:
```
steps/
├─ welcome.blade.php (Phase 1)
├─ app-settings.blade.php (Phase 2)
├─ database.blade.php (Phase 3)
├─ mail.blade.php (Phase 4)
├─ admin.blade.php (Phase 5)
├─ review.blade.php (Phase 6)
└─ success.blade.php (Phase 7)

components/
├─ alert.blade.php
├─ form-field.blade.php
├─ progress-bar.blade.php
├─ db-test-result.blade.php
└─ mail-test-result.blade.php
```

### ⏳ Phase 4: Tests (0%)
À créer:
- Tests unitaires RequirementsChecker
- Tests unitaires EnvManager
- Tests unitaires InstallationCheck
- Tests fonctionnels wizard complet
- Tests erreurs et edge cases

### ⏳ Phase 5: Documentation (0%)
À créer:
- INSTALLATION.md (guide client)
- TROUBLESHOOTING.md (dépannage)
- Update .env.example (commentaires clés)
- README installation

---

## 🏗️ Architecture implémentée

```
app/
├─ Contracts/Installation/
│  ├─ InstallationCheckInterface.php ✓
│  ├─ RequirementsCheckerInterface.php ✓
│  └─ EnvManagerInterface.php ✓
│
├─ Services/Installation/
│  ├─ RequirementsChecker.php ✓
│  ├─ EnvManager.php ✓
│  ├─ InstallationCheck.php ✓
│  └─ [À créer: DatabaseValidator, MailValidator, PermissionsValidator]
│
├─ Http/Controllers/Setup/
│  ├─ WelcomeController.php ✓
│  ├─ AppSettingsController.php ✓
│  ├─ DatabaseController.php [À créer]
│  ├─ MailController.php [À créer]
│  ├─ AdminController.php [À créer]
│  ├─ ReviewController.php [À créer]
│  └─ SuccessController.php [À créer]
│
├─ Http/Middleware/
│  ├─ CheckInstallation.php ✓
│  └─ RateLimitSetup.php [À créer]
│
├─ Http/Requests/Setup/
│  ├─ AppSettingsRequest.php ✓
│  ├─ DatabaseRequest.php [À créer]
│  ├─ MailRequest.php [À créer]
│  └─ AdminRequest.php [À créer]
│
├─ Exceptions/Installation/
│  └─ InstallationException.php ✓
│
└─ Events/Installation/ [À créer si needed]

config/
└─ installation.php ✓

routes/
├─ web.php [Mis à jour - include setup.php] ✓
├─ setup.php ✓
└─ api.php [À vérifier CheckInstallation sur routes API]

resources/views/setup/
├─ layout.blade.php ✓
├─ steps/ [À créer - 7 fichiers]
└─ components/ [À créer - 5 fichiers]

bootstrap/
└─ app.php [CheckInstallation déjà enregistré] ✓

database/migrations/
└─ [À vérifier - sessions et cache tables existantes]
```

---

## 🎯 Prochaines étapes (Priorité)

### 1. DatabaseController (HAUTE)
```php
// Template structure
class DatabaseController extends Controller
{
    public function index() {}      // Afficher form choix BD
    public function store() {}      // Sauvegarder config
    public function test() {}       // Test connexion AJAX
}
```

**Validations requises:**
- `DatabaseRequest` avec règles pour sqlite/mysql/pgsql
- Détection extension PDO selon driver
- Test connexion efficace (timeout 10s)
- Gestion erreurs claires

### 2. Views (HAUTE)
Créer template welcome.blade.php en premier (le plus simple)
```blade
@extends('setup.layout')

@section('content')
    <h2>Vérification des prérequis</h2>

    @foreach($checkResults['checks'] as $check)
        <!-- Afficher résultat -->
    @endforeach

    @if($canContinue)
        <a href="{{ route('setup.app-settings') }}">Continuer →</a>
    @endif
@endsection
```

### 3. MailController (MOYENNE)
Similaire à Database mais avec SMTP config et test optionnel.

### 4. AdminController (MOYENNE)
- Validation password fort (12+ chars, majuscule, chiffre)
- Password strength meter (JS)
- Création User avec is_admin=true

### 5. ReviewController & SuccessController (MOYENNE)
- Afficher récapitulatif
- Lancer installation complète
- Gérer progressBar/feedback

### 6. Tests & Documentation (BASSE)

---

## 🔧 Commandes utiles

```bash
# Tester requirements localement
php artisan tinker
> resolve(RequirementsChecker::class)->check()

# Tester EnvManager
php artisan tinker
> resolve(EnvManager::class)->all()

# Tester installation lock
php artisan tinker
> resolve(InstallationCheck::class)->isInstalled()

# Relancer migrations (après modifications .env)
php artisan config:cache
php artisan migrate

# Tests
php artisan test --filter=Installation
```

---

## 📝 Points d'attention

### Sécurité
- [ ] Rate limiting /setup enregistré (RateLimitSetup middleware)
- [ ] Secrets pas loggés (DB password, MAIL_PASSWORD)
- [ ] APP_DEBUG = false en production
- [ ] CSRF tokens sur tous formulaires
- [ ] Session timeout 60 min
- [ ] installed.lock avec hash intégrité

### Performance
- [ ] Cache vérifications 5 minutes
- [ ] Tests SMTP/DB timeout 10s
- [ ] Migrations idempotentes
- [ ] Assets compilés (npm run build)

### Compatibilité
- [ ] Python 8.3+ checked
- [ ] SQLite/MySQL/PostgreSQL supportés
- [ ] Responsive mobile (Tailwind or Bootstrap)
- [ ] Dark mode optionnel

### UX
- [ ] Progress bar visible
- [ ] Messages d'erreur clairs
- [ ] Boutons de navigation logiques
- [ ] Helptext sur champs techniques
- [ ] Auto-détection configurations

---

## 📚 Ressources

### Laravel 12 features used:
- Contracts (interfaces)
- Service providers
- Middleware
- Form requests
- Blade templates
- Artisan commands
- Cache facade

### Conventions suivies:
- PSR-12 (code style)
- Laravel directory structure
- Route naming (setup.*)
- Controller actions (index, store)
- Session key prefix (setup.*)

---

## ✨ Améliorations futures

- [ ] Wizard en Filament Page (au lieu Blade)
- [ ] Support multi-langue complet
- [ ] Setup CLI alternative
- [ ] Environment variables detection
- [ ] Docker support detection
- [ ] Import config from existing app
- [ ] Database backup avant migration
- [ ] Email template customization
- [ ] 2FA setup optional
- [ ] API key generation initial

---

**Créé le:** 24 janvier 2026
**Version:** 1.0 Beta
**Statut:** Phase 3 (50%) - Prêt pour continuation
