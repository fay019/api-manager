# 📋 AUDIT COMPLET - SYSTÈME CLIENTS & API

## 🎯 Vue d'ensemble

**4 Tables interconnectées:**
- `clients` - Utilisateurs authentifiés (register/login/password)
- `api_clients` - Applications API (rate limit, quota, configuration)
- `api_keys` - Clés API chiffrées (AES-256, validation multi-étapes)
- `api_request_logs` - Journal d'audit (toutes requêtes)

---

## 📊 TABLE: `clients`

### Schéma complet
```
id BIGINT PK
├─ Authentification
│  ├─ email VARCHAR(255) UNIQUE INDEX
│  ├─ password VARCHAR(255) [Bcrypt hash via cast]
│  └─ remember_token VARCHAR(100)
├─ Activation (24h)
│  ├─ is_active BOOLEAN DEFAULT false
│  ├─ activation_token VARCHAR(64) INDEX [64 random chars]
│  ├─ activation_expires_at TIMESTAMP
│  └─ activated_at TIMESTAMP
├─ Password Reset (1h)
│  ├─ password_reset_token VARCHAR(64) INDEX [64 random chars]
│  └─ password_reset_expires_at TIMESTAMP
├─ Profil
│  ├─ name VARCHAR(255)
│  ├─ avatar VARCHAR(255)
│  ├─ contact_name VARCHAR(255)
│  ├─ contact_email VARCHAR(255)
│  ├─ description TEXT
│  └─ notes TEXT
├─ Métadonnées
│  ├─ last_login_at TIMESTAMP
│  ├─ pending_email VARCHAR(255)
│  ├─ created_at TIMESTAMP
│  └─ updated_at TIMESTAMP
└─ Relation
   └─ apiClients() HasMany → api_clients.client_id
```

### Casts
```php
'password' => 'hashed'              // Bcrypt auto
'is_active' => 'boolean'
'activated_at' => 'datetime'
'activation_expires_at' => 'datetime'
'password_reset_expires_at' => 'datetime'
'last_login_at' => 'datetime'
```

### Hidden
```php
['password', 'remember_token', 'activation_token']
```

### Validations (Form Requests)
```
register:
  - name: required, string, max:255
  - email: required, email, unique:clients
  - password: required, confirmed, min:8, mixed_case, numbers, symbols

login:
  - email: required, email
  - password: required

forgot_password:
  - email: required, email  [PAS d'exists check = anti-enum]

reset_password:
  - token: required, string
  - email: required, email
  - password: required, confirmed, min:8, mixed_case, numbers, symbols

resend_activation:
  - email: required, email

update_profile:
  - name: required, string, max:255
  - email: required, email, unique:clients
  - avatar: nullable, image, max:2048
```

### Tokens
```
activation_token:
  - Généré: Str::random(64)
  - Stockage: clients.activation_token [EN CLAIR]
  - Expiration: 24h (activation_expires_at)
  - Usage: /client/activate/{token}
  - Comparaison: === [direct, pas de hash]

password_reset_token:
  - Généré: Str::random(64)
  - Stockage: clients.password_reset_token [EN CLAIR]
  - Expiration: 1h (password_reset_expires_at)
  - Usage: /client/password/reset/{token}
  - Comparaison: === [direct, pas de hash]
```

### Routes associées
```
POST   /client/register              → register()
GET    /client/activate/{token}      → activateEmail()
POST   /client/resend-activation     → resendActivation()
GET    /client/login                 → showLogin()
POST   /client/login                 → login()
POST   /client/logout                → logout()
GET    /client/password/forgot       → showForgotPassword()
POST   /client/password/forgot       → sendPasswordReset() [anti-enum]
GET    /client/password/reset/{token}→ showResetPassword()
POST   /client/password/reset        → resetPassword()
GET    /client/profile               → showProfile()
POST   /client/profile               → updateProfile()
```

---

## 📊 TABLE: `api_clients`

### Schéma complet
```
id BIGINT PK
├─ Relation Client
│  └─ client_id BIGINT INDEX FK → clients.id [NULLABLE]
├─ Identifiant
│  └─ name VARCHAR(255) [REQUIRED]
├─ Configuration API
│  ├─ client_type VARCHAR(255) [MOBILE/WEB/PARTNER/INTERNAL]
│  ├─ website VARCHAR(255)
│  ├─ is_active BOOLEAN DEFAULT true
│  ├─ allowed_origins JSON [CORS, array]
│  ├─ rate_limit_per_minute INT DEFAULT 60
│  ├─ monthly_quota BIGINT
│  └─ webhook_url VARCHAR(255)
├─ Relations
│  ├─ apiKeys() HasMany → api_keys.api_client_id
│  └─ requestLogs() HasMany → api_request_logs.api_client_id
├─ Métadonnées
│  ├─ activated_at TIMESTAMP
│  ├─ created_at TIMESTAMP
│  └─ updated_at TIMESTAMP
└─ Relation
   └─ client() BelongsTo → clients.id
```

### Casts
```php
'allowed_origins' => 'array'
'is_active' => 'boolean'
'activated_at' => 'datetime'
```

### Fillable
```php
['name', 'client_id', 'website', 'client_type', 'is_active',
 'allowed_origins', 'rate_limit_per_minute', 'monthly_quota',
 'webhook_url', 'activated_at']
```

### Validations (Filament Form)
```
name:          required, max:255
client_id:     nullable, exists:clients
website:       nullable, url
client_type:   nullable, in:MOBILE,WEB,PARTNER,INTERNAL
is_active:     boolean
allowed_origins: nullable, array
rate_limit_per_minute: numeric, min:1, default:60
monthly_quota: nullable, numeric, min:0
webhook_url:   nullable, url
activated_at:  nullable, datetime
```

### Requêtes fréquentes
```sql
-- API clients actifs d'un client user
SELECT * FROM api_clients 
WHERE client_id = ? AND is_active = true

-- API client avec ses clés actives
SELECT ac.*, COUNT(ak.id) as active_keys
FROM api_clients ac
LEFT JOIN api_keys ak ON ac.id = ak.api_client_id AND ak.is_active = true
WHERE ac.id = ?
GROUP BY ac.id
```

---

## 📊 TABLE: `api_keys`

### Schéma complet
```
id BIGINT PK
├─ Relation
│  └─ api_client_id BIGINT INDEX FK → api_clients.id [REQUIRED]
├─ Clé (AES-256 encrypted)
│  ├─ key_encrypted TEXT [Crypt::encryptString]
│  └─ key_prefix VARCHAR(8) INDEX [apk_XXXX en clair]
├─ Métadonnées
│  ├─ name VARCHAR(255)
│  ├─ is_active BOOLEAN DEFAULT true
│  └─ last_used_at TIMESTAMP
├─ Validité
│  ├─ starts_at TIMESTAMP [clé active à partir de...]
│  └─ expires_at TIMESTAMP [clé expire à...]
├─ Relation
│  ├─ apiClient() BelongsTo → api_clients.id
│  └─ requestLogs() HasMany → api_request_logs.api_key_id
├─ Attributs calculés
│  ├─ is_expired : bool = expires_at && expires_at->isPast()
│  └─ is_valid : bool = is_active && date_check && client_active
├─ Métadonnées
│  ├─ created_at TIMESTAMP
│  └─ updated_at TIMESTAMP
└─ Hidden
   └─ ['key_encrypted']
```

### Casts
```php
'is_active' => 'boolean'
'last_used_at' => 'datetime'
'starts_at' => 'datetime'
'expires_at' => 'datetime'
```

### Génération (ApiKeyService::generateKey())
```
1. $rawKeyPart = Str::random(32)       // 32 chars aléatoires
2. $prefix = 'apk_' . Str::random(4)   // Format: apk_XXXX
3. $fullRawKey = "{$prefix}{$rawKeyPart}"  // Total: 40 chars
4. $encrypted = Crypt::encryptString($fullRawKey)  // AES-256-GCM

Return: [
  'raw' => $fullRawKey,     // Montré 1x à l'user
  'prefix' => $prefix,      // Stocké en clair (indexed)
  'encrypted' => $encrypted // Stocké en DB
]
```

### Validation (ApiKeyService::validateKey($rawKey))
```
1. $prefix = substr($rawKey, 0, 8)
2. SELECT * FROM api_keys WHERE key_prefix = $prefix AND is_active = true
3. Pour chaque clé trouvée:
   a. $decrypted = Crypt::decryptString($key->key_encrypted)
   b. if ($decrypted === $rawKey)
      - Vérifie: starts_at pas dans futur
      - Vérifie: expires_at pas dans passé
      - Vérifie: apiClient.is_active = true
      - Retourne clé valide
4. Si aucune clé valide, retourne null
```

### Validations (Filament Form)
```
name:              required, max:255
is_active:         boolean
starts_at:         nullable, datetime
expires_at:        nullable, datetime
```

### Requêtes fréquentes
```sql
-- Clés API valides d'un client
SELECT * FROM api_keys
WHERE api_client_id = ? 
  AND is_active = true
  AND (starts_at IS NULL OR starts_at <= NOW())
  AND (expires_at IS NULL OR expires_at >= NOW())

-- Clé par prefix (validation requête API)
SELECT * FROM api_keys
WHERE key_prefix = ?
  AND is_active = true
```

---

## 📊 TABLE: `api_request_logs`

### Schéma complet
```
id BIGINT PK
├─ Relations
│  ├─ api_client_id BIGINT INDEX FK → api_clients.id [NULLABLE SET NULL]
│  └─ api_key_id BIGINT INDEX FK → api_keys.id [NULLABLE SET NULL]
├─ Requête
│  ├─ method VARCHAR(10) [GET/POST/PUT/DELETE/PATCH]
│  ├─ path VARCHAR(255)
│  ├─ status_code INT
│  └─ duration_ms INT
├─ Client
│  ├─ ip VARCHAR(45) [IPv4/IPv6]
│  ├─ hostname VARCHAR(255)
│  ├─ domain VARCHAR(255)
│  ├─ user_agent VARCHAR(255)
│  ├─ origin VARCHAR(255) [CORS]
│  └─ referer VARCHAR(255)
├─ Contexte Client
│  ├─ site_name VARCHAR(255)
│  ├─ page_path VARCHAR(255)
│  ├─ full_url TEXT
│  ├─ client_request_time VARCHAR(255)
│  └─ client_user_agent VARCHAR(255)
├─ Relations
│  ├─ apiClient() BelongsTo → api_clients.id
│  └─ apiKey() BelongsTo → api_keys.id
├─ Métadonnées
│  └─ created_at TIMESTAMP [seul timestamp]
└─ Note
   └─ $timestamps = false [pas d'updated_at]
```

### Casts
```php
'created_at' => 'datetime'
```

### Indexes
```
PRIMARY KEY (id)
FOREIGN KEY (api_client_id) ON DELETE SET NULL
FOREIGN KEY (api_key_id) ON DELETE SET NULL
INDEX (api_client_id, created_at)
INDEX (status_code, created_at)
INDEX (created_at)
```

### Requêtes fréquentes
```sql
-- Logs derniers 7 jours par client
SELECT ac.name, COUNT(*) as requests,
  SUM(CASE WHEN status_code BETWEEN 200 AND 299 THEN 1 ELSE 0 END) as success,
  AVG(duration_ms) as avg_duration
FROM api_request_logs arl
JOIN api_clients ac ON arl.api_client_id = ac.id
WHERE arl.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY ac.id

-- Logs d'erreur (4xx/5xx)
SELECT * FROM api_request_logs
WHERE status_code >= 400 AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY created_at DESC
```

---

## 🔐 SÉCURITÉ VÉRIFIÉE

✅ **Password:** Bcrypt hash via cast (jamais exposé)  
✅ **API Keys:** AES-256-GCM encryption (key_encrypted hidden)  
✅ **Tokens:** 64 chars random (acceptable pour tokens temps-limité)  
✅ **Anti-enum:** Messages génériques password forgot  
✅ **CSRF:** Laravel middleware (session cookies)  
✅ **XSS:** Blade escaping + Alpine.js safe bindings  
✅ **SQL Injection:** Eloquent parameterized queries  
✅ **Rate Limiting:** 5 endpoints protégés (register/login/pwd)  
✅ **AJAX Fetching:** Authorization check avant déchiffrement clés  
✅ **Tokens in URLs:** HTTPS only (via middleware)  

---

## 🌍 TRADUCTIONS (FR/EN/DE)

### Clés présentes dans `lang/{locale}/filament.php`

**Client section:**
```
filament.client.singular              [EN: Client]
filament.client.plural                [EN: Clients]
filament.client.name                  [EN: Name]
filament.client.active                [EN: Active]
filament.client.contact_name          [EN: Contact Name]
filament.client.contact_email         [EN: Contact Email]
filament.client.website               [EN: Website]
filament.client.description           [EN: Description]
filament.client.notes                 [EN: Notes]
filament.client.type                  [EN: Type]
filament.client.type_mobile           [EN: Mobile]
filament.client.type_web              [EN: Web]
filament.client.type_partner          [EN: Partner]
filament.client.type_internal         [EN: Internal]
filament.client.activated_at          [EN: Activated At]
filament.client.status                [EN: Status]
filament.client.section_info          [EN: Basic Information]
filament.client.section_info_desc     [EN: Client identification and type]
filament.client.section_contact       [EN: Contact Information]
filament.client.section_contact_desc  [EN: Contact details and website]
filament.client.section_technical     [EN: Technical Configuration]
filament.client.section_technical_desc[EN: Rate limiting and CORS]
filament.client.rate_limit            [EN: Rate Limit]
filament.client.rate_limit_suffix     [EN: req/min]
filament.client.monthly_quota         [EN: Monthly Quota]
filament.client.monthly_quota_placeholder [EN: Unlimited]
filament.client.allowed_origins       [EN: Allowed Origins]
filament.client.allowed_origins_placeholder [EN: Domain URLs]
filament.client.allowed_origins_help  [EN: CORS whitelist]
```

**Key section:**
```
filament.key.plural                   [EN: API Keys]
filament.key.singular                 [EN: API Key]
filament.key.name                     [EN: Key Name]
filament.key.client                   [EN: Client]
filament.key.prefix                   [EN: Prefix]
filament.key.status_active            [EN: Status]
filament.key.status_revoked           [EN: Revoked]
filament.key.status_scheduled         [EN: Scheduled]
filament.key.status_expired           [EN: Expired]
filament.key.is_active                [EN: Is Active]
filament.key.starts_at                [EN: Starts At]
filament.key.expires_at               [EN: Expires At]
filament.key.last_used                [EN: Last Used]
filament.key.full_key                 [EN: Full Key]
filament.key.decrypt_error            [EN: Could not decrypt]
filament.key.never                    [EN: Never]
```

✅ **Toutes les traductions FR/EN/DE présentes et synchronisées**

---

## 📈 RELATIONS & EAGER LOADING

### Bonnes pratiques
```php
// ✅ Correct
$clients = Client::with('apiClients.apiKeys')->get();
$apiClients = ApiClient::with('apiKeys', 'requestLogs')->get();

// ❌ Éviter (N+1)
foreach (Client::all() as $client):
  $clients->apiClients;  // Query par client
endforeach;
```

### Pattern suggéré
```php
// Dashboard
$client->load('apiClients.apiKeys:id,api_client_id,key_prefix,is_active');

// Admin list
ApiClient::with('client:id,name', 'apiKeys:id,api_client_id,is_active')->get();

// Request logs analysis
ApiRequestLog::with('apiClient:id,name', 'apiKey:id,key_prefix')->get();
```

---

## 🚨 DÉPENDANCES & CONTRAINTES FK

| FK | Table | Relation | OnDelete | Status |
|---|---|---|---|---|
| client_id | api_clients | clients | SET NULL | ✅ OK |
| api_client_id | api_keys | api_clients | CASCADE | ✅ OK |
| api_client_id | api_request_logs | api_clients | SET NULL | ✅ OK |
| api_key_id | api_request_logs | api_keys | SET NULL | ✅ OK |

**Implication:** Supprimer un client → api_clients.client_id = NULL (logs préservés)

---

## ✅ IMPLÉMENTATION COMPLÈTE

| Composant | Status | Notes |
|---|---|---|
| Schéma BD | ✅ | 4 tables, FK, indexes |
| Authentification | ✅ | Register/Login/Password/Email |
| API Keys | ✅ | AES-256, validation, revoke |
| Logs | ✅ | Audit trail complet |
| Admin Filament | ✅ | Resources, relation managers |
| Validations | ✅ | FormRequests, rules |
| Sécurité | ✅ | Hash, encrypt, anti-enum |
| Traductions | ✅ | FR/EN/DE complet |
| Rate Limiting | ✅ | 5 endpoints protégés |
| AJAX Keys | ✅ | Fetch on-demand, sécurisé |

---

**Généré:** 2026-04-22  
**Statut:** Production Ready
