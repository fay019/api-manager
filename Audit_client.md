# 📋 AUDIT COMPLET : SYSTÈME DE CLIENTS ET API

## 🎯 Vue d'ensemble des 4 tables

L'application gère 4 tables liées ensemble :

```
clients
  ├── 1─────N apiClients (relation HasMany)
  │            ├── 1─────N apiKeys (relation HasMany)
  │            │    └── 1─────N apiRequestLogs (relation HasMany)
  │            └── 1─────N apiRequestLogs (relation HasMany)
  └── (authenticate users du client)
```

### **Schéma relationnel complet :**

```
┌─────────────┐
│   clients   │
├─────────────┤
│ id (PK)     │
│ name        │
│ email (UQ)  │
│ password    │
│ ...         │
│ client_id★  │  ← Foreign Key (nullable)
└─────────────┘
      │
      │ 1 ─── N
      └─────────────────┐
                        │
               ┌─────────────────────┐
               │   api_clients       │
               ├─────────────────────┤
               │ id (PK)             │
               │ name                │
               │ client_id (FK)      │ ← Points to clients.id
               │ is_active (boolean) │
               │ rate_limit_per_min  │
               │ monthly_quota       │
               │ ...                 │
               └─────────────────────┘
                      │
         ┌────────────┼────────────┐
         │            │            │
         │   1─────N  │    1─────N │
         │            │            │
      ┌──────────────┐  ┌──────────────────┐
      │  api_keys    │  │ api_request_logs │
      ├──────────────┤  ├──────────────────┤
      │ id (PK)      │  │ id (PK)          │
      │ api_client_id│  │ api_client_id(FK)│
      │ key_encrypted│  │ api_key_id (FK)  │
      │ key_prefix   │  │ method           │
      │ is_active    │  │ path             │
      │ starts_at    │  │ status_code      │
      │ expires_at   │  │ ip               │
      │ ...          │  │ duration_ms      │
      └──────────────┘  │ created_at       │
             │          └──────────────────┘
             │                  ▲
             │                  │
             └──────────────────┘
                    1───────N
```

---

## 📊 TABLE 1 : `clients`

### Structure

| Colonne | Type | Nullable | Défaut | Contraintes | Notes |
|---------|------|----------|--------|-------------|-------|
| `id` | BIGINT | ✗ | - | PRIMARY KEY | Auto-increment |
| `name` | VARCHAR(255) | ✗ | - | - | Nom du client |
| `email` | VARCHAR(255) | ✗ | - | UNIQUE | Email unique, utilisé pour login |
| `password` | VARCHAR(255) | ✗ | - | - | Hash bcrypt via cast 'hashed' |
| `avatar` | VARCHAR(255) | ✓ | NULL | - | URL avatar (optionnel) |
| `contact_name` | VARCHAR(255) | ✓ | NULL | - | Personne de contact |
| `contact_email` | VARCHAR(255) | ✓ | NULL | - | Email contact |
| `description` | TEXT | ✓ | NULL | - | Description du client |
| `notes` | TEXT | ✓ | NULL | - | Notes internes |
| `activation_token` | VARCHAR(64) | ✓ | NULL | INDEX | Token SHA256 (64 chars) |
| `activation_expires_at` | TIMESTAMP | ✓ | NULL | - | Expiration activation (24h) |
| `pending_email` | VARCHAR(255) | ✓ | NULL | - | Email à confirmer avant changement |
| `is_active` | BOOLEAN | ✗ | `false` | - | Compte activé ou pas |
| `activated_at` | TIMESTAMP | ✓ | NULL | - | Date d'activation |
| `last_login_at` | TIMESTAMP | ✓ | NULL | - | Dernier login |
| `password_reset_token` | VARCHAR(64) | ✓ | NULL | INDEX | Token SHA256 reset (1h) |
| `password_reset_expires_at` | TIMESTAMP | ✓ | NULL | - | Expiration reset password |
| `remember_token` | VARCHAR(100) | ✓ | NULL | - | Token "Remember me" |
| `created_at` | TIMESTAMP | ✗ | NOW() | - | Date création |
| `updated_at` | TIMESTAMP | ✗ | NOW() | - | Dernière modification |

### Indexes

```
UNIQUE KEY `email` (email)
INDEX `activation_token` (activation_token)
INDEX `password_reset_token` (password_reset_token)
PRIMARY KEY (id)
```

### Casts (Eloquent)

```php
'password' => 'hashed'  // Bcrypt automatic hashing
'is_active' => 'boolean'
'activated_at' => 'datetime'
'activation_expires_at' => 'datetime'
'password_reset_expires_at' => 'datetime'
'last_login_at' => 'datetime'
```

### Hidden (Eloquent)

```php
['password', 'remember_token', 'activation_token']
```

### Fillable

```php
['name', 'email', 'password', 'avatar', 'contact_name', 'contact_email',
 'description', 'notes', 'is_active', 'activated_at', 'last_login_at',
 'pending_email', 'activation_token', 'activation_expires_at',
 'password_reset_token', 'password_reset_expires_at']
```

### Relations

```php
public function apiClients(): HasMany {
    return $this->hasMany(ApiClient::class, 'client_id');
}
```

**Signification :**
- Un client peut avoir plusieurs `apiClients` (API clients)
- Liaison via colonne `client_id` de `api_clients`

### Cycle de vie d'un client

1. **Inscription** → `created_at` set, `is_active = false`
2. **Activation** → Email avec token, `activation_token` + `activation_expires_at` set (24h)
3. **Click email** → Token validé, `is_active = true`, `activated_at` set
4. **Login** → `last_login_at` updated
5. **Mot de passe oublié** → `password_reset_token` + `password_reset_expires_at` set (1h)
6. **Reset password** → Password updated, tokens cleared

---

## 📊 TABLE 2 : `api_clients`

### Structure

| Colonne | Type | Nullable | Défaut | Contraintes | Notes |
|---------|------|----------|--------|-------------|-------|
| `id` | BIGINT | ✗ | - | PRIMARY KEY | Auto-increment |
| `client_id` | BIGINT | ✓ | NULL | FOREIGN KEY | Points to `clients.id` |
| `name` | VARCHAR(255) | ✗ | - | - | Nom de l'API client |
| `contact_email` | VARCHAR(255) | ✓ | NULL | - | Contact email |
| `contact_name` | VARCHAR(255) | ✓ | NULL | - | Nom contact |
| `website` | VARCHAR(255) | ✓ | NULL | - | URL site web |
| `client_type` | VARCHAR(255) | ✓ | NULL | - | mobile, web, partner, internal |
| `description` | TEXT | ✓ | NULL | - | Description du client API |
| `is_active` | BOOLEAN | ✗ | `true` | - | Client API actif |
| `status` | VARCHAR(255) | ✗ | - | INDEX | active, disabled (OLD COLUMN) |
| `allowed_origins` | JSON | ✓ | NULL | - | Tableau d'origins CORS |
| `notes` | TEXT | ✓ | NULL | - | Notes internes |
| `rate_limit_per_minute` | INT | ✗ | `60` | - | Limite de requêtes/min |
| `monthly_quota` | BIGINT | ✓ | NULL | - | Quota mensuel de requêtes |
| `webhook_url` | VARCHAR(255) | ✓ | NULL | - | URL webhook pour events |
| `activated_at` | TIMESTAMP | ✓ | NULL | - | Date activation |
| `created_at` | TIMESTAMP | ✗ | NOW() | - | Date création |
| `updated_at` | TIMESTAMP | ✗ | NOW() | - | Dernière modification |

### Indexes

```
INDEX `status` (status)
INDEX `client_id` (client_id)
FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
```

### Casts (Eloquent)

```php
'allowed_origins' => 'array'
'is_active' => 'boolean'
'activated_at' => 'datetime'
```

### Fillable

```php
['name', 'client_id', 'contact_email', 'contact_name', 'website',
 'client_type', 'description', 'is_active', 'allowed_origins', 'notes',
 'rate_limit_per_minute', 'monthly_quota', 'webhook_url', 'activated_at']
```

### Relations

```php
public function client(): BelongsTo {
    return $this->belongsTo(Client::class);
}

public function apiKeys(): HasMany {
    return $this->hasMany(ApiKey::class);
}

public function requestLogs(): HasMany {
    return $this->hasMany(ApiRequestLog::class);
}
```

**Significations :**
- Chaque `api_client` appartient à optionnellement un `client`
- Un `api_client` peut avoir plusieurs `apiKeys`
- Un `api_client` peut avoir plusieurs `requestLogs`

### Note : Colonnes chevauchées

- `status` (ancien) vs `is_active` (nouveau) : Problème de duplication
  - À nettoyer : utiliser uniquement `is_active`

---

## 📊 TABLE 3 : `api_keys`

### Structure

| Colonne | Type | Nullable | Défaut | Contraintes | Notes |
|---------|------|----------|--------|-------------|-------|
| `id` | BIGINT | ✗ | - | PRIMARY KEY | Auto-increment |
| `api_client_id` | BIGINT | ✗ | - | FOREIGN KEY | Points to `api_clients.id` |
| `key_encrypted` | TEXT | ✓ | NULL | - | **Clé chiffrée** (Laravel Crypt) |
| `key_prefix` | VARCHAR(8) | ✗ | - | INDEX | `apk_XXXX` (4 chars) |
| `name` | VARCHAR(255) | ✗ | - | - | Nom descriptif de la clé |
| `starts_at` | TIMESTAMP | ✓ | NULL | - | Clé valide à partir de... |
| `expires_at` | TIMESTAMP | ✓ | NULL | - | Clé expire à... |
| `is_active` | BOOLEAN | ✗ | `true` | - | Clé révoquée ou pas |
| `last_used_at` | TIMESTAMP | ✓ | NULL | - | Dernière utilisation |
| `created_at` | TIMESTAMP | ✗ | NOW() | - | Date création |
| `updated_at` | TIMESTAMP | ✗ | NOW() | - | Dernière modification |

### Indexes

```
INDEX (api_client_id, is_active)
INDEX (key_prefix)
FOREIGN KEY (api_client_id) REFERENCES api_clients(id) ON DELETE CASCADE
```

### Casts (Eloquent)

```php
'is_active' => 'boolean'
'last_used_at' => 'datetime'
'starts_at' => 'datetime'
'expires_at' => 'datetime'
```

### Hidden (Eloquent)

```php
['key_encrypted']  // Jamais exposer la clé chiffrée dans JSON
```

### Fillable

```php
['api_client_id', 'key_encrypted', 'key_prefix', 'name',
 'starts_at', 'expires_at', 'is_active']
```

### Relations

```php
public function apiClient(): BelongsTo {
    return $this->belongsTo(ApiClient::class);
}

public function requestLogs(): HasMany {
    return $this->hasMany(ApiRequestLog::class);
}
```

### Attributs calculés

```php
public function getIsExpiredAttribute(): bool {
    return $this->expires_at && $this->expires_at->isPast();
}

public function getIsValidAttribute(): bool {
    $now = now();
    return $this->is_active
        && (!$this->starts_at || $this->starts_at->isPast())
        && (!$this->expires_at || $this->expires_at->isFuture())
        && $this->apiClient->is_active;
}
```

**Explications :**
- `is_expired` : true si la date `expires_at` est dans le passé
- `is_valid` : true si :
  - La clé est active (`is_active = true`)
  - Soit `starts_at` est NULL, soit elle est dans le passé
  - Soit `expires_at` est NULL, soit elle est dans le futur
  - Le client API parent est actif

### Génération et chiffrement

**Service :** `App\Services\ApiKeyService::generateKey()`

```php
public function generateKey(): array {
    $rawKeyPart = Str::random(32);  // 32 caractères aléatoires
    $prefix = 'apk_' . Str::random(4);  // "apk_" + 4 chars
    $fullRawKey = "{$prefix}{$rawKeyPart}";  // apk_XXXX + 32 chars = 40 chars
    
    $encrypted = Crypt::encryptString($fullRawKey);  // Crypt avec APP_KEY
    
    return [
        'raw' => $fullRawKey,        // Montré une fois à l'utilisateur
        'prefix' => $prefix,         // Stocké en clair (indexé)
        'encrypted' => $encrypted,   // Stocké en DB
    ];
}
```

**Processus :**
1. Générer clé brute = `apk_XXXX` + 32 chars random
2. Chiffrer avec `Crypt::encryptString()` (Laravel Crypt using APP_KEY)
3. Stocker :
   - `key_encrypted` = version chiffrée
   - `key_prefix` = les 8 premiers chars en clair (pour recherche rapide)
   - Retourner `raw` à l'utilisateur (une seule fois)

**Validation :** `App\Services\ApiKeyService::validateKey(string $rawKey)`

```php
public function validateKey(string $rawKey): ?ApiKey {
    $prefix = substr($rawKey, 0, 8);  // Extraire le prefix
    
    $keys = ApiKey::where('key_prefix', $prefix)  // Trouver par prefix (index)
                   ->where('is_active', true)      // Seulement actives
                   ->with('apiClient')
                   ->get();
    
    foreach ($keys as $key) {
        try {
            $decrypted = Crypt::decryptString($key->key_encrypted);
            
            if ($decrypted === $rawKey) {  // Comparaison exacte
                // Vérifications supplémentaires :
                if ($key->starts_at && $key->starts_at->isFuture())
                    continue;  // Pas encore valide
                
                if ($key->expires_at && $key->expires_at->isPast())
                    continue;  // Expiré
                
                if (!$key->apiClient->is_active)
                    continue;  // Client API désactivé
                
                return $key;  // ✓ Clé valide
            }
        } catch (\Exception $e) {
            continue;  // Erreur déchiffrement → prochaine clé
        }
    }
    
    return null;  // Aucune clé correspondante
}
```

**Processus de validation :**
1. Extraire le prefix de la clé fournie (8 premiers chars)
2. Chercher toutes les clés avec ce prefix et `is_active = true`
3. Pour chaque clé trouvée :
   - Déchiffrer `key_encrypted`
   - Comparer avec la clé brute fournie
   - Si égales, vérifier les dates et l'état du client
   - Si tout OK, retourner la clé
4. Si aucune clé ne matche, retourner NULL

---

## 📊 TABLE 4 : `api_request_logs`

### Structure

| Colonne | Type | Nullable | Défaut | Contraintes | Notes |
|---------|------|----------|--------|-------------|-------|
| `id` | BIGINT | ✗ | - | PRIMARY KEY | Auto-increment |
| `api_client_id` | BIGINT | ✓ | NULL | FOREIGN KEY | Points to `api_clients.id` |
| `api_key_id` | BIGINT | ✓ | NULL | FOREIGN KEY | Points to `api_keys.id` |
| `method` | VARCHAR(10) | ✗ | - | - | GET, POST, PUT, DELETE, PATCH |
| `path` | VARCHAR(255) | ✗ | - | - | /api/endpoint/path |
| `status_code` | INT | ✗ | - | - | 200, 401, 403, 404, 500, etc |
| `ip` | VARCHAR(45) | ✗ | - | - | IPv4 ou IPv6 |
| `hostname` | VARCHAR(255) | ✓ | NULL | - | Hostname de la requête |
| `domain` | VARCHAR(255) | ✓ | NULL | - | Domain de la requête |
| `site_name` | VARCHAR(255) | ✓ | NULL | - | Nom du site client |
| `page_path` | VARCHAR(255) | ✓ | NULL | - | Path de la page client |
| `full_url` | TEXT | ✓ | NULL | - | URL complète de la requête |
| `client_request_time` | VARCHAR(255) | ✓ | NULL | - | Temps côté client |
| `client_user_agent` | VARCHAR(255) | ✓ | NULL | - | User Agent client |
| `user_agent` | VARCHAR(255) | ✓ | NULL | - | User Agent serveur |
| `origin` | VARCHAR(255) | ✓ | NULL | - | Header Origin CORS |
| `referer` | VARCHAR(255) | ✓ | NULL | - | Referrer |
| `duration_ms` | INT | ✓ | NULL | - | Temps d'exécution en ms |
| `created_at` | TIMESTAMP | ✗ | NOW() | - | Date/heure de la requête |

### Indexes

```
INDEX (api_client_id, created_at)
INDEX (status_code, created_at)
INDEX (created_at)
FOREIGN KEY (api_client_id) REFERENCES api_clients(id) ON DELETE SET NULL
FOREIGN KEY (api_key_id) REFERENCES api_keys(id) ON DELETE SET NULL
```

### Notes

```
$timestamps = false;  // Pas de updated_at, seulement created_at
```

### Casts (Eloquent)

```php
'created_at' => 'datetime'
```

### Fillable

```php
['api_client_id', 'api_key_id', 'method', 'path', 'status_code', 'ip',
 'hostname', 'domain', 'site_name', 'page_path', 'full_url',
 'client_request_time', 'client_user_agent', 'user_agent', 'origin',
 'referer', 'duration_ms', 'created_at']
```

### Relations

```php
public function apiClient(): BelongsTo {
    return $this->belongsTo(ApiClient::class);
}

public function apiKey(): BelongsTo {
    return $this->belongsTo(ApiKey::class);
}
```

### Note : Soft Delete

- `api_client_id` et `api_key_id` sont nullable
- Les étapes côté DB sont `ON DELETE SET NULL`
- Donc si une clé est supprimée, les logs restent (avec `api_key_id = NULL`)

---

## 🔐 Sécurité et chiffrement

### API Key Encryption

**Technologie :** Laravel Crypt (AES-256-GCM par défaut)

```php
// Chiffrement
$encrypted = Crypt::encryptString($fullRawKey);

// Déchiffrement
$decrypted = Crypt::decryptString($encrypted);
```

**Clé de chiffrement :** Stockée dans `.env` → `APP_KEY`

**Format de la clé générée :**

```
Longueur totale = 40 chars
Format : apk_XXXX[32 chars random]
Exemple : apk_a7k9_xY2mN9q3kL8pP1rT4vW7zC0bD5fG2hJ6
```

**Stockage :**

| Donné | Où | Clair? | Sensible? |
|------|-----|--------|----------|
| `key_encrypted` | `api_keys` table | ✗ Non | ✓ Très sensible |
| `key_prefix` | `api_keys` table | ✓ Oui | ✗ Non |
| `raw key` | Jamais (retourné à l'utilisateur) | - | - |

### Password Hashing

**Client password :**
- Hash : Bcrypt (via Laravel cast 'hashed')
- Stocké dans `clients.password`
- Jamais retourné en clair

**Activation token :**
- Généré aléatoirement (64 chars)
- **Pas hashé en DB** (stocké en clair)
- Transmis en URL : `?token=XXXXXXXX`
- Comparaison directe lors de validation

**Password reset token :**
- Même processus que activation token
- 64 chars aléatoires
- Stocké en clair dans `clients.password_reset_token`

### Anti-énumération

**Mot de passe oublié :**

```
POST /client/password/forgot
- Request : email=user@example.com
- Response : Toujours "Si cet email existe..." (même si email inconnu)
- Raison : Empêcher de savoir si un email est inscrit
```

---

## 🔄 FLUX DE DONNÉES

### 1️⃣ Flux : Création d'un API Client

```
Client (user) -> Filament Admin
                    ↓
            Crée un ApiClient
                    ↓
            Stocke : name, client_id, is_active, etc.
                    ↓
            ApiClient.id créé
```

**Résultat :**
- 1 ligne dans `api_clients` table

### 2️⃣ Flux : Génération d'une clé API

```
Admin clique "Créer une clé"
         ↓
   ApiKeyService::generateKey()
         ↓
   Génère : fullRawKey = "apk_XXXX" + 32 chars
         ↓
   Chiffre avec Crypt::encryptString()
         ↓
   Retourne :
   - raw (montré une fois)
   - prefix (stocké en clair)
   - encrypted (stocké en DB)
         ↓
   Crée ApiKey record :
   - api_client_id = XXX
   - key_encrypted = chiffré
   - key_prefix = "apk_XXXX"
   - is_active = true
   - starts_at = null
   - expires_at = null
```

**Résultat :**
- 1 ligne dans `api_keys` table
- Clé brute affichée à l'utilisateur (une seule fois)

### 3️⃣ Flux : Utilisation d'une clé (validation)

```
Requête client -> API endpoint
         ↓
   Header: Authorization: Bearer apk_XXXX...
         ↓
   Middleware extrait la clé
         ↓
   ApiKeyService::validateKey($key)
         ↓
   - Extraire prefix (8 chars)
   - Chercher clés avec ce prefix + is_active
   - Déchiffrer key_encrypted pour chaque clé
   - Comparer avec la clé fournie
   - Vérifier dates (starts_at, expires_at)
   - Vérifier is_active du client API
         ↓
   Clé trouvée et valide ? → Autoriser
   Clé invalide/pas trouvée → 401 Unauthorized
```

### 4️⃣ Flux : Logging d'une requête

```
Requête API validée
         ↓
   Middleware LogRequest
         ↓
   Crée ApiRequestLog :
   - api_client_id = celui de la clé
   - api_key_id = celui utilisé
   - method = GET/POST/PUT/DELETE
   - path = /api/...
   - status_code = 200/404/500
   - ip = client IP
   - user_agent = client user agent
   - duration_ms = temps execution
   - created_at = now()
         ↓
   Logs stockés pour analyse
```

---

## 📈 REQUÊTES SQL COURANTES

### Obtenir tous les clients avec leurs API clients

```sql
SELECT 
    c.id, c.name, c.email, c.is_active,
    COUNT(ac.id) as api_clients_count,
    SUM(CASE WHEN ak.is_active = true THEN 1 ELSE 0 END) as active_keys
FROM clients c
LEFT JOIN api_clients ac ON c.id = ac.client_id
LEFT JOIN api_keys ak ON ac.id = ak.api_client_id
GROUP BY c.id
ORDER BY c.created_at DESC;
```

### Obtenir les clés API valides d'un client

```sql
SELECT *
FROM api_keys
WHERE api_client_id = ? 
  AND is_active = true
  AND (starts_at IS NULL OR starts_at <= NOW())
  AND (expires_at IS NULL OR expires_at >= NOW())
ORDER BY created_at DESC;
```

### Obtenir les logs de requête du dernier mois

```sql
SELECT 
    arl.id,
    ac.name as client_name,
    ak.key_prefix,
    arl.method,
    arl.path,
    arl.status_code,
    arl.duration_ms,
    arl.created_at
FROM api_request_logs arl
LEFT JOIN api_clients ac ON arl.api_client_id = ac.id
LEFT JOIN api_keys ak ON arl.api_key_id = ak.id
WHERE arl.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
ORDER BY arl.created_at DESC
LIMIT 1000;
```

### Compter les requêtes par client (derniers 7 jours)

```sql
SELECT 
    ac.name,
    COUNT(*) as total_requests,
    SUM(CASE WHEN arl.status_code BETWEEN 200 AND 299 THEN 1 ELSE 0 END) as success,
    SUM(CASE WHEN arl.status_code BETWEEN 400 AND 599 THEN 1 ELSE 0 END) as errors,
    AVG(arl.duration_ms) as avg_duration_ms
FROM api_request_logs arl
JOIN api_clients ac ON arl.api_client_id = ac.id
WHERE arl.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY ac.id, ac.name
ORDER BY total_requests DESC;
```

---

## 🚀 VALIDATIONS ET CONTRAINTES

### Client (clients table)

✓ `email` : Unique, format email  
✓ `password` : Min 8 chars, must hash  
✓ `is_active` : Boolean, default false  
✓ `activation_token` : 64 chars, nullable  
✓ `activation_expires_at` : Must be 24h from now  

### API Client (api_clients table)

✓ `name` : Required  
✓ `is_active` : Boolean, default true  
✓ `client_id` : Optional FK  
✓ `rate_limit_per_minute` : Numeric, min 1  
✓ `allowed_origins` : JSON array, comma-separated  

### API Key (api_keys table)

✓ `api_client_id` : Required FK  
✓ `name` : Required, max 255  
✓ `key_prefix` : Generated, 8 chars  
✓ `key_encrypted` : Generated via Crypt  
✓ `starts_at` : Optional, datetime  
✓ `expires_at` : Optional, datetime  
✓ `is_active` : Boolean, default true  

---

## 📝 NOTES IMPORTANTES

### ⚠️ Problèmes existants

1. **Colonne `status` dupliquée**
   - `api_clients.status` (ancien) vs `is_active` (nouveau)
   - À nettoyer : supprimer `status`

2. **Token stockés en clair**
   - `activation_token` et `password_reset_token` ne sont pas hashés
   - Comparés directement avec `$token === hash('sha256', $rawToken)`
   - ✓ Accepté pour les tokens temps-limité (1-24h)

3. **KEY_ENCRYPTED vs KEY_HASH**
   - Migration change `key_hash` → `key_encrypted`
   - Les anciennes clés hashées ne peuvent pas être récupérées
   - Solution : Régénérer les clés ou vider la table avant migration

### ✅ Points forts

1. **Chiffrement AES-256** pour les clés API
2. **Indexes optimisés** pour recherche rapide
3. **Soft delete** sur les logs (FK nullable)
4. **Anti-énumération** sur password reset
5. **Validation multi-étapes** des clés API (préfixe + déchiffrement + dates)

---

## 🎓 RÉSUMÉ HIÉRARCHIQUE

```
Tier 1 : Clients (Utilisateurs)
├─ Authentification : email + password (bcrypt)
├─ Token d'activation (64 chars, 24h)
└─ Token de reset password (64 chars, 1h)

Tier 2 : API Clients (Applications associées)
├─ Rate limiting (req/min)
├─ Monthly quota
└─ Allowed origins (CORS)

Tier 3 : API Keys (Accès aux APIs)
├─ Clé chiffrée (AES-256)
├─ Validation multi-critères
│  ├─ Existence + déchiffrement
│  ├─ Préfixe matching (index)
│  ├─ Dates de validité
│  └─ État du client API
└─ 1 clé : plusieurs requêtes

Tier 4 : Request Logs (Audit trail)
├─ Qui (api_key_id, api_client_id)
├─ Quoi (method, path, status)
├─ Où (ip, origin, referer)
├─ Quand (created_at)
└─ Combien de temps (duration_ms)
```

---

## 📦 Fichiers clés

```
app/Models/
├─ Client.php              (Utilisateur client)
├─ ApiClient.php           (Application API)
├─ ApiKey.php              (Clé API)
└─ ApiRequestLog.php       (Log de requête)

app/Services/
└─ ApiKeyService.php       (Génération + validation clés)

app/Filament/Resources/
├─ Clients/ClientResource.php
├─ ApiClientResource.php
└─ ApiKeyResource.php

app/Http/Requests/Client/
├─ ForgotPasswordRequest.php
└─ ResetPasswordRequest.php

app/Http/Controllers/Client/
└─ AuthController.php

database/migrations/
├─ *_create_clients_table.php
├─ *_add_password_reset_to_clients_table.php
├─ *_create_api_clients_table.php
├─ *_create_api_keys_table.php
├─ *_create_api_request_logs_table.php
└─ ... (modifications)
```

---

**Généré le :** 2026-04-22  
**Version :** 1.0  
**Application :** API Manager - Client & Key Management System
