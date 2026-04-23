# Audit Complet du Système API Manager

## Table des matières
1. [Vue d'ensemble](#vue-densemble)
2. [Table `clients`](#table-clients)
3. [Table `api_clients`](#table-api_clients)
4. [Table `api_keys`](#table-api_keys)
5. [Table `api_request_logs`](#table-api_request_logs)
6. [Relations Eloquent](#relations-eloquent)
7. [Ressources Filament](#ressources-filament)
8. [Flux de données](#flux-de-données)
9. [Sécurité](#sécurité)
10. [Performance](#performance)

---

## Vue d'ensemble

Le système API Manager gère 4 tables principales interconnectées:

```
clients (Utilisateurs qui se registrent)
   ├── api_clients (Applications API créées par les clients)
   │    └── api_keys (Clés d'accès pour chaque app)
   │         └── api_request_logs (Historique des requêtes)
```

**Hiérarchie**: Un `client` peut avoir plusieurs `api_clients`, chaque `api_client` peut avoir plusieurs `api_keys`, et chaque `api_key` génère des `api_request_logs`.

---

## Table `clients`

### Schéma
```
id                          bigint unsigned    PRIMARY KEY AUTO_INCREMENT
name                        varchar(255)       NOT NULL (nom du client)
email                       varchar(255)       NOT NULL UNIQUE
password                    varchar(255)       NOT NULL (hashed)
avatar                      varchar(255)       NULLABLE
contact_name                varchar(255)       NULLABLE
contact_email               varchar(255)       NULLABLE
description                 text               NULLABLE
notes                       text               NULLABLE
activation_token            varchar(64)        NULLABLE (token unique activation)
activation_expires_at       datetime           NULLABLE (expire en 24h)
pending_email               varchar(255)       NULLABLE (email en attente)
is_active                   tinyint(1)         DEFAULT 0
activated_at                datetime           NULLABLE (quand activé)
last_login_at               datetime           NULLABLE
remember_token              varchar(100)       NULLABLE (Laravel auth)
password_reset_token        varchar(64)        NULLABLE (token reset 1h)
password_reset_expires_at   timestamp          NULLABLE
created_at                  timestamp          AUTO
updated_at                  timestamp          AUTO
```

### Indexes
| Nom | Colonnes | Type | But |
|-----|----------|------|-----|
| `PRIMARY` | id | UNIQUE | Clé primaire |
| `clients_email_unique` | email | UNIQUE | Un email = un client |
| `clients_email_index` | email | BTREE | Recherche par email |
| `clients_is_active_index` | is_active | BTREE | Filtrer clients actifs |
| `clients_created_at_index` | created_at | BTREE | Trier par date création |
| `clients_activation_token_index` | activation_token | BTREE | Chercher token activation |
| `clients_password_reset_token_index` | password_reset_token | BTREE | Chercher token reset |

### Modèle Eloquent (`app/Models/Client.php`)

**Fillable:**
```php
'name', 'email', 'password', 'avatar', 'contact_name', 'contact_email',
'description', 'notes', 'is_active', 'activated_at', 'last_login_at',
'pending_email', 'activation_token', 'activation_expires_at',
'password_reset_token', 'password_reset_expires_at'
```

**Casts:**
```php
'password' => 'hashed'              // Hash bcrypt automatique
'is_active' => 'boolean'
'activated_at' => 'datetime'
'activation_expires_at' => 'datetime'
'password_reset_expires_at' => 'datetime'
'last_login_at' => 'datetime'
```

**Relationships:**
```php
hasMany(ApiClient::class)           // Un client => plusieurs apps API
```

**Scopes:**
```php
scopeActive()                       // where('is_active', true)
scopeWithApiMetrics()               // Eager load apiClients + apiKeys
```

### Authentification
- Classe: `Illuminate\Foundation\Auth\User as Authenticatable`
- Guard: `web` (voir `config/auth.php`)
- Provider: `users`

### Fonctionnalités

#### 1. **Inscription (Registration)**
- Route: `POST /client/register` → `ClientAuthController@register()`
- Form Request: `RegisterRequest` avec validations:
  - Email unique, DNS validation
  - Mot de passe 8+ chars, mixed case, numbers, symbols, non-compromised
  - Activation token généré (64 chars random)
  - Token expiration: 24h
- État initial: `is_active = false`, email non validé

#### 2. **Activation Email**
- Notification: `ClientActivation`
- Envoi d'email avec lien `/client/activate/{token}?email=...`
- Token validé: `hash_equals()` côté serveur
- Une fois activé: `is_active = true`, `activated_at = now()`

#### 3. **Login (Connexion)**
- Route: `POST /client/login` → `ClientAuthController@login()`
- Validation: email + password requis
- Guard utilisé: `client` (voir `config/auth.php`)
- Mise à jour: `last_login_at = now()` à chaque login

#### 4. **Mot de passe oublié**
- Route GET: `/client/password/forgot` (formulaire)
- Route POST: `POST /client/password/forgot` → `sendPasswordReset()`
  - Validation: juste l'email (pas de "exists" check → anti-enumeration)
  - Génère token: `password_reset_token` (64 chars)
  - Expiration: 1h
  - Notification: `ClientPasswordReset` envoyée

#### 5. **Réinitialisation mot de passe**
- Route GET: `/client/password/reset/{token}?email=...` (formulaire)
- Route POST: `POST /client/password/reset` → `resetPassword()`
  - Validation: token + email + new password
  - Vérifications:
    1. Client existe et est actif
    2. Token matches (hash_equals)
    3. Token pas expiré
  - Met à jour: `password` + clear tokens
  - Log: `Log::info('client.password.reset', ['id' => $client->id])`

#### 6. **Profil Client**
- Route: `GET /client/dashboard` → vue avec données client
- Affiche: email, nom, applications API créées, clés d'accès
- Actions: modifier profil, voir/copier clés d'accès

### Sécurité
| Aspect | Implémentation |
|--------|-----------------|
| Mots de passe | Hash bcrypt, uncompromised check |
| Email | Unique, DNS validation, verification token |
| Activation | Token random 64 chars, expire 24h |
| Reset password | Token random 64 chars, expire 1h, hash_equals |
| Énumération | Forgot password retourne message générique |

---

## Table `api_clients`

### Schéma
```
id                      bigint unsigned    PRIMARY KEY AUTO_INCREMENT
name                    varchar(255)       NOT NULL (ex: "Mobile App v1")
client_id               bigint unsigned    NULLABLE FK → clients.id (ON DELETE SET NULL)
website                 varchar(255)       NULLABLE (URL du site)
client_type             varchar(255)       NULLABLE (MOBILE, WEB, PARTNER, INTERNAL)
is_active               tinyint(1)         DEFAULT 1
allowed_origins         json               NULLABLE (["https://example.com", ...])
rate_limit_per_minute   int                DEFAULT 60 (requêtes/min)
monthly_quota           bigint unsigned    NULLABLE (0 = illimité)
webhook_url             varchar(255)       NULLABLE (POST callback après requête)
activated_at            timestamp          NULLABLE
created_at              timestamp          AUTO
updated_at              timestamp          AUTO
```

### Indexes
| Nom | Colonnes | Type | But |
|-----|----------|------|-----|
| `PRIMARY` | id | UNIQUE | Clé primaire |
| `api_clients_client_id_index` | client_id | BTREE | FK lookup |
| `api_clients_is_active_index` | is_active | BTREE | Filtrer apps actives |
| `api_clients_created_at_index` | created_at | BTREE | Trier par date |
| `api_clients_client_id_is_active_index` | (client_id, is_active) | BTREE | Combo lookup |

### Modèle Eloquent (`app/Models/ApiClient.php`)

**Fillable:**
```php
'name', 'client_id', 'website', 'client_type', 'is_active',
'allowed_origins', 'rate_limit_per_minute', 'monthly_quota',
'webhook_url', 'activated_at'
```

**Casts:**
```php
'allowed_origins' => 'array'        // JSON → array
'is_active' => 'boolean'
'activated_at' => 'datetime'
```

**Relationships:**
```php
belongsTo(Client::class)            // Une app → un client (parent)
hasMany(ApiKey::class)              // Une app → plusieurs clés
hasMany(ApiRequestLog::class)       // Une app → plusieurs logs
```

**Scopes:**
```php
scopeActive()                       // where('is_active', true)

scopeWithMetrics()                  // Eager load counts:
                                    // - active_keys (count clés actives)
                                    // - total_requests (count tous logs)
                                    // - success_requests (count status 200-299)
```

### Fonctionnalités

#### 1. **Création d'application**
- Admin panel: `/admin/api-clients/create`
- Resource Filament: `ApiClientResource`
- Fields:
  - Client (select relation)
  - Name (requis, max 255 chars)
  - Type (select: MOBILE, WEB, PARTNER, INTERNAL)
  - Website URL (validation URL)
  - Rate limit (défaut 60 req/min)
  - Monthly quota (optionnel)
  - Webhook URL (optionnel, validation URL)
  - Allowed origins (JSON array, séparé par virgules)
  - Status (toggle is_active)

#### 2. **Modification**
- Route: `PATCH /admin/api-clients/{id}` → Filament
- Tous les champs modifiables
- Historique via `updated_at`

#### 3. **Suppression**
- Soft delete: NON (suppression réelle)
- Cascade: `api_keys` supprimées en cascade (CASCADE)
- Logs: `api_request_logs` restent (ON DELETE SET NULL)

#### 4. **Relation avec Client**
- `client_id` peut être NULL
- Permet apps créées par l'admin (non liées à un client)
- Exemple: app interne, partenaire

#### 5. **Rate Limiting & Quotas**
- `rate_limit_per_minute`: contrôle débit instantané
- `monthly_quota`: contrôle utilisation mensuelle
- Appliqués via middleware: `ThrottleApiClient`
- Stockés en cache Redis

#### 6. **CORS & Origins**
- `allowed_origins`: JSON array
- Validation CORS appliquée par middleware: `CorsPerClient`
- Si vide: allow all origins

### Gestion Admin
- Vue listée: montre stats (clés actives, requêtes)
- Relation Manager: `ApiKeysRelationManager` pour gérer clés
- Actions: Edit, Delete, Bulk Delete

---

## Table `api_keys`

### Schéma
```
id                  bigint unsigned    PRIMARY KEY AUTO_INCREMENT
api_client_id       bigint unsigned    NOT NULL FK → api_clients.id (CASCADE)
key_encrypted       text               NULLABLE (clé encryptée AES-256)
key_prefix          varchar(8)         NOT NULL (ex: "sk_test_")
name                varchar(255)       NOT NULL (ex: "Mobile App Key")
starts_at           timestamp          NULLABLE (activation différée)
last_used_at        timestamp          NULLABLE (tracking)
expires_at          timestamp          NULLABLE (expiration)
is_active           tinyint(1)         DEFAULT 1
created_at          timestamp          AUTO
updated_at          timestamp          AUTO
```

### Indexes
| Nom | Colonnes | Type | But |
|-----|----------|------|-----|
| `PRIMARY` | id | UNIQUE | Clé primaire |
| `api_keys_api_client_id_index` | api_client_id | BTREE | FK lookup |
| `api_keys_key_prefix_index` | key_prefix | BTREE | Recherche par prefix |
| `api_keys_created_at_index` | created_at | BTREE | Trier par création |
| `api_keys_is_active_expires_at_index` | (is_active, expires_at) | BTREE | Validation rapide |

### Modèle Eloquent (`app/Models/ApiKey.php`)

**Fillable:**
```php
'api_client_id', 'key_encrypted', 'key_prefix', 'name',
'starts_at', 'expires_at', 'is_active'
```

**Hidden:**
```php
['key_encrypted']               // Jamais retourné au client
```

**Casts:**
```php
'is_active' => 'boolean'
'last_used_at' => 'datetime'
'starts_at' => 'datetime'
'expires_at' => 'datetime'
```

**Attributes calculés:**
```php
$key->is_expired                // true si expires_at < now()
$key->is_valid                  // true si active + dans validité + app active
```

**Relationships:**
```php
belongsTo(ApiClient::class)     // Une clé → une app
hasMany(ApiRequestLog::class)   // Une clé → plusieurs logs
```

**Scopes:**
```php
scopeValid()                    // Clés actives + dans validité + app active
scopeExpired()                  // Clés expirées (expires_at < now)
```

### Fonctionnalités

#### 1. **Génération de clé**
- Côté serveur: génération random 64 chars
- Format: `key_prefix + random_part` (ex: `sk_test_abc123...`)
- Stockage: key encryptée AES-256 (Laravel `Crypt`)
- Retour au client: une seule fois (affichage bootstrap)

#### 2. **Sécurité des clés**
- Base de données: clé encryptée
- API: requête → validation hash(prefix)
- Pas de stockage en clair
- Prefix visible dans les logs (pour tracer requêtes)
- Hidden: `key_encrypted` jamais retourné dans JSON

#### 3. **Validation de clé**
- Vérification:
  1. `is_active = true`
  2. `starts_at` passé ou NULL
  3. `expires_at` futur ou NULL
  4. App parent actif (`api_client.is_active = true`)
- Scope: `ApiKey::valid()`

#### 4. **Durée de vie**
- `starts_at`: activation future (optionnel)
- `expires_at`: expiration (optionnel, illimité si NULL)
- `last_used_at`: tracking de dernier usage
- Exemple:
  - Clé créée: 2026-04-23, expire 2026-04-30
  - Usage: appels API valident durée

#### 5. **Gestion du cycle de vie**
- Active → Inactive: toggle `is_active`
- Rotation: créer nouvelle clé, désactiver ancienne
- Audit trail: `created_at`, `updated_at`

### Admin Operations
- CRUD: Create, Read, Update, Delete
- Relation Manager dans `ApiClientResource`
- Table des clés avec stats:
  - Status (actif/inactif badge)
  - Dates (création, expiration)
  - Last used (dernier appel)
  - Requests count (stats)

---

## Table `api_request_logs`

### Schéma
```
id                      bigint unsigned    PRIMARY KEY AUTO_INCREMENT
api_client_id           bigint unsigned    NULLABLE FK → api_clients.id (SET NULL)
api_key_id              bigint unsigned    NULLABLE FK → api_keys.id (SET NULL)
method                  varchar(10)        NOT NULL (GET, POST, PUT, DELETE, PATCH)
path                    varchar(255)       NOT NULL (ex: "/api/users/123")
status_code             int                NOT NULL (200, 400, 401, 403, 404, 500...)
ip                      varchar(45)        NOT NULL (IPv4 ou IPv6)
hostname                varchar(255)       NULLABLE
domain                  varchar(255)       NULLABLE
site_name               varchar(255)       NULLABLE (qui l'a appelée)
page_path               varchar(255)       NULLABLE
full_url                varchar(255)       NULLABLE
client_request_time     timestamp          NULLABLE
client_user_agent       varchar(255)       NULLABLE
user_agent              varchar(255)       NULLABLE (serveur)
origin                  varchar(255)       NULLABLE (CORS origin)
referer                 varchar(255)       NULLABLE
duration_ms             int                NULLABLE (temps réponse)
created_at              timestamp          NOT NULL (time du log)
```

### Indexes
| Nom | Colonnes | Type | But |
|-----|----------|------|-----|
| `PRIMARY` | id | UNIQUE | Clé primaire |
| `api_request_logs_api_client_id_created_at_index` | (api_client_id, created_at) | BTREE | Logs par app/date |
| `api_request_logs_api_key_id_created_at_index` | (api_key_id, created_at) | BTREE | Logs par clé/date |
| `api_request_logs_status_code_index` | status_code | BTREE | Filtrer erreurs |
| `api_request_logs_status_code_created_at_index` | (status_code, created_at) | BTREE | Erreurs + date |
| `api_request_logs_created_at_index` | created_at | BTREE | Trier par date |

### Modèle Eloquent (`app/Models/ApiRequestLog.php`)

**Fillable:**
```php
'api_client_id', 'api_key_id', 'method', 'path', 'status_code', 'ip',
'hostname', 'domain', 'site_name', 'page_path', 'full_url',
'client_request_time', 'client_user_agent', 'user_agent',
'origin', 'referer', 'duration_ms', 'created_at'
```

**Casts:**
```php
'created_at' => 'datetime'
```

**Relationships:**
```php
belongsTo(ApiClient::class)     // Vers app API
belongsTo(ApiKey::class)        // Vers clé (optional)
```

### Enregistrement des logs

**Middleware:** `LogApiRequest` (appliqué à toutes requêtes `/api/*`)

**Données capturées:**
```
✓ HTTP method (GET, POST, etc.)
✓ Path (/api/endpoint)
✓ Status code (200, 404, 500...)
✓ Client IP (incluant proxy headers)
✓ User-Agent (client)
✓ Origin (CORS)
✓ Referer
✓ Duration (temps réponse en ms)
✓ Timestamp (created_at)
✓ Request metadata (optional)
```

### Utilisations

#### 1. **Dashboard Analytics**
- Stats par app: requêtes total/jour/mois
- Stats par status code: 2xx, 4xx, 5xx
- Top endpoints
- Top errors

#### 2. **Monitoring Performance**
- `duration_ms`: temps réponse
- Identification endpoints lents
- Alertes si durée > seuil

#### 3. **Security Audit**
- Tracking par IP (détection abuse)
- Tracking par clé (qui accède quoi)
- Historique complet requêtes
- Détection patterns suspects

#### 4. **Quotas & Rate Limiting**
- Utilisé pour compter requêtes/minute (rate limit)
- Utilisé pour compter requêtes/mois (monthly quota)
- Middleware: `ThrottleApiClient` (check rates)

---

## Relations Eloquent

**Client → ApiClient → ApiKey → ApiRequestLog**

```php
// Obtenir toutes les apps d'un client
$client->apiClients()->get()

// Obtenir toutes les clés d'une app
$app->apiKeys()->get()

// Obtenir logs d'une clé
$key->requestLogs()->get()

// Eager loading anti N+1
ApiClient::with('apiKeys', 'requestLogs')->get()
```

---

## Ressources Filament

### ApiClientResource (`/admin/api-clients`)
- Create/Edit/Delete applications API
- Relation Manager pour gérer clés API
- Affiche stats (clés actives, requêtes)
- Filtres par status, type, client

### ApiRequestLogResource (`/admin/api-request-logs`)
- Historique complet des requêtes
- Filtres par status code, app, date range
- Visualise patterns d'accès, erreurs, performances

---

## Sécurité & Performance

### Sécurité
- **Passwords**: BCrypt + uncompromised validation
- **API Keys**: AES-256 encryption, hash validation
- **Tokens**: Random 64 chars, hash_equals comparison
- **Rate Limiting**: protection DoS par IP
- **CORS**: validation origins par app

### Indexes Performance
- Composite indexes pour requêtes fréquentes
- Foreign key lookups optimisés
- Date range queries rapides

### Caching
- Rate limit counters en cache
- Can upgrade to Redis/Memcached
