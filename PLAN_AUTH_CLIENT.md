# Plan: Authentification externe clients — Production SaaS Final v6

> **Suivi de progression** — Cocher chaque tâche au fur et à mesure de l'implémentation.
> En cas de plantage, reprendre à la dernière tâche non cochée.

---

## Étape 1 — Migrations

- [x] **1a.** Créer migration `create_clients_table`
  - Colonnes : id, name, email (unique), password (hashed), avatar, contact_name, contact_email, description, notes, activation_token (index), activation_expires_at, pending_email, is_active (false), activated_at, last_login_at, remember_token, timestamps
  - `$table->unique('email')` + `$table->index('activation_token')`
  - `activation_expires_at NULL` = token invalide/déjà utilisé

- [x] **1b.** Créer migration `add_client_id_to_api_clients_table`
  - `$table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete()->index()`

- [x] **1c.** Exécuter `php artisan migrate`

---

## Étape 2 — Modèles

- [x] **2a.** Créer `app/Models/Client.php`
  - Extend `Illuminate\Foundation\Auth\User`
  - `$fillable` : name, email, password, avatar, contact_name, contact_email, description, notes, is_active, activated_at, last_login_at, pending_email
  - `$hidden` : password, remember_token, activation_token
  - `casts()` : password→hashed, is_active→boolean, dates→datetime
  - Relation `apiClients()` → HasMany(ApiClient, client_id)

- [x] **2b.** Mettre à jour `app/Models/ApiClient.php`
  - Ajouter `client_id` dans `$fillable`
  - Ajouter `client()` → BelongsTo(Client)

---

## Étape 3 — Guard d'authentification

- [x] **3a.** Mettre à jour `config/auth.php`
  - Ajouter guard `client` (session, provider: clients)
  - Ajouter provider `clients` (eloquent, model: Client)

---

## Étape 4 — Rate Limiting

- [x] **4a.** Ajouter dans `app/Providers/AppServiceProvider.php` méthode `boot()` :
  - `client-login` : 5/min par email + 10/min par IP + 20/min global IP
  - `client-register` : 5/min par IP
  - `client-activate` : 20/min par IP
  - `client-resend` : 3/min par IP

---

## Étape 5 — Form Requests

- [x] **5a.** Créer `app/Http/Requests/Client/RegisterRequest.php`
  - name (required), email (rfc, unique:clients), password (confirmed, Rules\Password::min(8)->mixedCase()->numbers()->symbols())

- [x] **5b.** Créer `app/Http/Requests/Client/LoginRequest.php`
  - email (rfc), password (required)

- [x] **5c.** Créer `app/Http/Requests/Client/UpdateProfileRequest.php`
  - contact_name, contact_email, description, avatar (image/mimes/max:2048), password (optionnel+fort)
  - **ABSENT** : notes, email, is_active

- [x] **5d.** Créer `app/Http/Requests/Client/ResendActivationRequest.php`
  - email (rfc uniquement) — **PAS de règle `exists`** (évite email enumeration)

---

## Étape 6 — Notification d'activation

- [x] **6a.** Créer `app/Notifications/ClientActivation.php`
  - `__construct(private string $rawToken)`
  - `toMail()` : route('client.activate', ['token' => rawToken])
  - Vue : `emails/client-activation.blade.php`

- [x] **6b.** Créer `resources/views/emails/client-activation.blade.php`
  - HTML + texte fallback, nom, lien, date expiration
  - Traductions FR/EN/DE

---

## Étape 7 — Controllers

- [x] **7a.** Créer `app/Http/Controllers/Client/AuthController.php`
  - `showRegister()` / `showLogin()`
  - `register()` : créer client inactif → `$client = Client::create([...])` → `$client->notify()` → log → redirect
  - `activate()` : hash token → lookup SQL → check is_active → check null + isPast() → activer → log
  - `resendActivation()` : traitement silencieux → message générique
  - `login()` : attempt → check is_active → last_login_at → regenerate() → logoutOtherDevices() → log
  - `logout()` : logout → invalidate → regenerateToken

- [x] **7b.** Créer `app/Http/Controllers/Client/DashboardController.php`
  - `index()` : eager-load apiClients avec withCount (active_keys, total_requests, success_requests)

- [x] **7c.** Créer `app/Http/Controllers/Client/ProfileController.php`
  - `edit()` : retourner la vue
  - `update()` : safe()->except avatar/password → avatar (guessExtension, Str::uuid, supprimer ancien) → password (si filled) → update

---

## Étape 8 — Routes

- [x] **8a.** Ajouter dans `routes/web.php` le groupe `/client` :
  - guest:client : register (GET/POST), login (GET/POST)
  - public : activate/{token} (GET, throttle), activate/resend (POST, throttle)
  - auth:client : logout, dashboard, profile (GET/PUT)

---

## Étape 9 — Vues Blade

- [x] **9a.** Créer `resources/views/client/auth/register.blade.php`
- [x] **9b.** Créer `resources/views/client/auth/login.blade.php` (avec old('email'), lien resend)
- [x] **9c.** Créer `resources/views/client/auth/activated.blade.php`
- [x] **9d.** Créer `resources/views/client/dashboard.blade.php` (liste ApiClients + stats)
- [x] **9e.** Créer `resources/views/client/profile/edit.blade.php` (sans notes, sans email)

**Règles :** `{{ }}` partout, `@csrf` sur tous formulaires, `@method('PUT')` sur profil, `old('email')` sur login.

---

## Étape 10 — Filament : ClientResource

- [x] **10a.** Créer `app/Filament/Resources/ClientResource.php`
  - Nav groupe: API Management, sort: 0, icône: heroicon-o-user-group
  - Form : Auth (name, email, is_active, activated_at disabled, last_login_at disabled) + Contact (contact_name, contact_email, avatar FileUpload) + Admin (description, notes)
  - Table : name, email, is_active (badge), nb ApiClients (computed), last_login_at, activated_at

- [x] **10b.** Créer `app/Filament/Resources/ClientResource/RelationManagers/ApiClientsRelationManager.php`

- [x] **10c.** Mettre à jour `app/Filament/Resources/ApiClientResource.php`
  - Form : ajouter `Select::make('client_id')->relationship('client', 'name')->nullable()->searchable()->preload()`
  - Table : ajouter colonne `client.name` (nullable, placeholder `-`)

---

## Étape 11 — Traductions (3 langues)

- [x] **11a.** Ajouter dans `lang/fr/filament.php` : clés `client.*` + `client_auth.*`
- [x] **11b.** Ajouter dans `lang/en/filament.php` : mêmes clés EN
- [x] **11c.** Ajouter dans `lang/de/filament.php` : mêmes clés DE

**Clés requises :**
```
client: plural, singular, name, email, contact_name, contact_email, description, notes, is_active, avatar, activated_at, last_login, applications
client_auth: register_title, login_title, activation_email_subject, activation_success, already_activated, register_success, inactive_account, invalid_credentials, invalid_token, expired_token, resend_success, dashboard_title, profile_title, profile_updated, my_applications, active_keys, total_requests, success_requests, password_change, password_hint, resend_link
```

---

## Étape 12 — Headers de sécurité production (optionnel)

- [ ] **12a.** Créer middleware `SecurityHeaders` + enregistrer dans `bootstrap/app.php`
  - Content-Security-Policy, X-Frame-Options: DENY, X-Content-Type-Options: nosniff, Referrer-Policy
  - **Note :** À implémenter si nécessaire en production

---

## Étape 13 — Vérification finale

- [ ] **13a.** `php artisan migrate` → aucune erreur
- [ ] **13b.** Inscription → email envoyé (log), token hashé en BDD
- [ ] **13c.** Activation → is_active=true, token=null
- [ ] **13d.** Réutiliser lien → erreur invalid_token
- [ ] **13e.** Token expiré → erreur + lien resend
- [ ] **13f.** Resend avec email inconnu → message générique (pas de fuite)
- [ ] **13g.** Login 5× mauvais MDP → 429
- [ ] **13h.** Login compte inactif → message générique
- [ ] **13i.** Login réussi → dashboard, last_login_at mis à jour
- [ ] **13j.** Upload avatar → `avatars/uuid.ext` en storage, ancien supprimé
- [ ] **13k.** Profil → notes absent, email non modifiable
- [ ] **13l.** Filament ClientResource → notes admin visible, relation manager OK
- [ ] **13m.** `php artisan test --compact` → tous tests existants verts
- [ ] **13n.** `vendor/bin/pint --dirty` → aucune erreur de style

---

## Référence — Fichiers

### À modifier
| Fichier | Modification |
|---|---|
| `config/auth.php` | Guard `client` + provider `clients` |
| `routes/web.php` | Groupe `/client` |
| `app/Providers/AppServiceProvider.php` | 4 RateLimiters |
| `app/Models/ApiClient.php` | `client_id` + relation |
| `app/Filament/Resources/ApiClientResource.php` | Select client_id + colonne |
| `lang/fr/filament.php` | Traductions |
| `lang/en/filament.php` | Traductions |
| `lang/de/filament.php` | Traductions |

### À créer
```
database/migrations/*_create_clients_table.php
database/migrations/*_add_client_id_to_api_clients_table.php
app/Models/Client.php
app/Notifications/ClientActivation.php
app/Http/Requests/Client/RegisterRequest.php
app/Http/Requests/Client/LoginRequest.php
app/Http/Requests/Client/UpdateProfileRequest.php
app/Http/Requests/Client/ResendActivationRequest.php
app/Http/Controllers/Client/AuthController.php
app/Http/Controllers/Client/DashboardController.php
app/Http/Controllers/Client/ProfileController.php
app/Filament/Resources/ClientResource.php
app/Filament/Resources/ClientResource/Pages/ListClients.php
app/Filament/Resources/ClientResource/Pages/CreateClient.php
app/Filament/Resources/ClientResource/Pages/EditClient.php
app/Filament/Resources/ClientResource/RelationManagers/ApiClientsRelationManager.php
resources/views/client/auth/register.blade.php
resources/views/client/auth/login.blade.php
resources/views/client/auth/activated.blade.php
resources/views/client/dashboard.blade.php
resources/views/client/profile/edit.blade.php
resources/views/emails/client-activation.blade.php
```
