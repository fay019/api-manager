# 🤖 Architecture SaaS - Focus IA (API Manager)

## Focus: IA uniquement (pour maintenant)

La BD existante a **déjà une excellente structure**. Pour l'instant, on **enrichit pour l'IA**, et Payment/Custom viendront plus tard:

- ✅ `api_clients` → concept d'**Application IA**
- ✅ `api_keys` → concept de **Credential IA**
- ✅ `api_request_logs` → concept d'**Audit IA**
- ✅ `clients` → concept de **Client/Entreprise**

**Stratégie:** Au lieu de refondre, on **enrichit les tables pour l'IA**, avec une architecture **prête à accueillir Payment/Custom** sans refonte.

---

## 📊 Structure BD Actuelle vs Cible

### 1. Table `api_clients` (Applications)

**Existant:**
```
id, name, website, client_type, is_active, allowed_origins, 
rate_limit_per_minute, monthly_quota, webhook_url, 
client_id (FK→clients), slug, description, icon_url, 
webhook_secret, environment
```

**À ajouter (migration):**
```
- type VARCHAR(50) DEFAULT 'ia' — catégorise le service (pour l'instant: 'ia' uniquement)
- allowed_endpoints JSON — restreint les endpoints IA accessibles
```

**Raison:** 
- Permet de tracker quel client utilise quel service IA
- Restreindre les endpoints si nécessaire (ex: seulement `/api/v1/ai/generate`)
- **Architecture prête pour Payment/Custom:** on ajoute juste de nouveaux types plus tard

---

### 2. Table `api_keys` (Credentials)

**Existant:**
```
id, api_client_id (FK), key_encrypted, key_prefix, name, slug,
starts_at, expires_at, last_used_at, is_active, key_hash,
rotation_required_at, ip_whitelist
```

**À ajouter (migration):**
```
- type ENUM('api_key', 'oauth_token', 'basic_auth', 'custom') DEFAULT 'api_key'
- allowed_endpoints JSON — spécifique à cette clé
```

**Raison:** Généraliser pour supporter OAuth tokens, Basic Auth, Custom tokens.

---

### 3. Table `api_request_logs` (Audit)

**Existant:**
```
id, api_client_id, api_key_id, method, path, status_code,
ip, user_agent, origin, referer, duration_ms, 
request_size, response_size, error_message, cached,
hostname, domain, site_name, page_path, full_url, client_request_time
```

**À ajouter (migration):**
```
- service VARCHAR(50) — "payment", "ia", "custom"
- endpoint VARCHAR(255) — normalisé (chemin API uniquement)
- client_id BIGINT (FK→clients) — direct, denormalisé pour perf
```

**Raison:** Traçabilité du **Qui** (client), **Quoi** (application), **Où** (endpoint).

---

## 🛣️ Chemin d'implémentation

### Phase 1: Enrichir les migrations (2-3 jours)

```bash
# 1. Ajouter colonnes à api_clients (pour l'IA)
php artisan make:migration add_type_and_allowed_endpoints_to_api_clients_table --table=api_clients

# 2. Enrichir api_request_logs (pour tracker les appels IA)
php artisan make:migration add_service_client_endpoint_to_api_request_logs --table=api_request_logs
php artisan make:migration add_service_client_endpoint_to_api_request_logs_archive --table=api_request_logs_archive

# 3. Migrer
php artisan migrate
```

**Migrations détaillées:**

**api_clients (pour l'IA):**
```php
Schema::table('api_clients', function (Blueprint $table) {
    // Pour l'instant: toutes les apps sont des services IA
    // Plus tard: on ajoute 'payment', 'custom'
    $table->string('type', 50)->default('ia')->after('client_type');
    $table->json('allowed_endpoints')->nullable()->after('type');
    // Exemple pour l'IA: ["api/v1/ai/*"] ou ["api/v1/ai/generate", "api/v1/ai/models"]
});
```

**api_request_logs (pour tracker les appels IA):**
```php
Schema::table('api_request_logs', function (Blueprint $table) {
    $table->string('service', 50)->default('ia')->after('method');
    $table->bigInteger('client_id')->unsigned()->nullable()->after('api_key_id');
    $table->string('endpoint', 255)->nullable()->after('path');
    
    $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
});
```

**api_request_logs_archive (même schéma):**
```php
Schema::table('api_request_logs_archive', function (Blueprint $table) {
    $table->string('service', 50)->default('ia')->after('method');
    $table->bigInteger('client_id')->unsigned()->nullable()->after('api_key_id');
    $table->string('endpoint', 255)->nullable()->after('path');
    
    $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
});
```

### Phase 2: Modèles Laravel (1-2 jours)

**ApiClient (Application IA) — Gestion des accès IA**
```php
// app/Models/ApiClient.php

class ApiClient extends Model
{
    protected function casts(): array
    {
        return [
            'allowed_origins' => 'array',
            'allowed_endpoints' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function keys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function logs()
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    /**
     * Vérifier si un endpoint IA est autorisé pour cette app
     * 
     * Pour l'instant: type='ia' uniquement
     * Structure extensible: payment/custom ajoutent juste de nouveaux types
     */
    public function canAccessIaEndpoint(string $endpoint): bool
    {
        // Si allowed_endpoints est défini, respecter les restrictions
        if ($this->allowed_endpoints) {
            return $this->matchesAnyPattern($endpoint, $this->allowed_endpoints);
        }

        // Sinon accès libre à tous les endpoints IA
        return true;
    }

    /**
     * Helper: vérifie si endpoint matche un des patterns
     * Exemple patterns: ["api/v1/ai/*"] ou ["api/v1/ai/generate", "api/v1/ai/models"]
     */
    private function matchesAnyPattern(string $endpoint, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $regex = str_replace('*', '.*', preg_quote($pattern, '/'));
            if (preg_match("/^{$regex}$/", $endpoint)) {
                return true;
            }
        }
        return false;
    }
}
```

**ApiKey (Credential IA)**
```php
// app/Models/ApiKey.php

class ApiKey extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'ip_whitelist' => 'array',
            'allowed_endpoints' => 'array',
        ];
    }

    public function application()
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }

    public function logs()
    {
        return $this->hasMany(ApiRequestLog::class, 'api_key_id');
    }

    /**
     * Vérifier validité complète pour l'IA
     */
    public function isValidForIa(): bool
    {
        return $this->is_active
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Vérifier si IP est autorisée
     */
    public function isIpAllowed(?string $ip): bool
    {
        if (! $this->ip_whitelist) {
            return true; // Pas de restriction
        }
        return in_array($ip, $this->ip_whitelist);
    }

    /**
     * Vérifier accès endpoint IA
     * Cascade: d'abord clé, puis app
     */
    public function canAccessIaEndpoint(string $endpoint): bool
    {
        // Vérifier restriction au niveau de la clé
        if ($this->allowed_endpoints && ! $this->matchesPattern($endpoint)) {
            return false; // Clé trop restreinte
        }

        // Puis vérifier au niveau de l'app
        return $this->application->canAccessIaEndpoint($endpoint);
    }

    private function matchesPattern(string $endpoint): bool
    {
        foreach ($this->allowed_endpoints as $pattern) {
            $regex = str_replace('*', '.*', preg_quote($pattern, '/'));
            if (preg_match("/^{$regex}$/", $endpoint)) {
                return true;
            }
        }
        return false;
    }
}
```

**ApiRequestLog (AuditLog)**
```php
// app/Models/ApiRequestLog.php

class ApiRequestLog extends Model
{
    const UPDATED_AT = null; // Pas de updated_at

    protected function casts(): array
    {
        return [
            'cached' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function application()
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }

    public function key()
    {
        return $this->belongsTo(ApiKey::class, 'api_key_id');
    }
}
```

### Phase 3: Service d'authentification IA (1-2 jours)

**AuthenticationService (pour l'IA)**
```php
// app/Services/AuthenticationService.php

class AuthenticationService
{
    /**
     * Authentifier une clé API pour l'IA
     */
    public function authenticateApiKeyForIa(string $rawKey): ?ApiKey
    {
        $hash = hash('sha256', $rawKey);
        
        return ApiKey::where('key_hash', $hash)
            ->where('is_active', true)
            ->with(['application', 'application.client'])
            ->first();
    }

    /**
     * Vérifier si la clé est valide pour l'IA
     */
    public function isValidForIa(ApiKey $key): bool
    {
        return $key->isValidForIa()
            && $key->application->is_active
            && $key->application->type === 'ia';
    }

    /**
     * Vérifier accès endpoint IA
     */
    public function canAccessIaEndpoint(ApiKey $key, string $endpoint): bool
    {
        return $key->canAccessIaEndpoint($endpoint);
    }

    /**
     * Vérifier IP
     */
    public function verifyIp(ApiKey $key, ?string $ip): bool
    {
        return $key->isIpAllowed($ip);
    }
}
```

**UniversalAuthentication Middleware (pour l'IA)**
```php
// app/Http/Middleware/UniversalAuthentication.php

class UniversalAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        $authService = app(AuthenticationService::class);
        
        // Récupérer clé depuis header
        $rawKey = $request->header('X-API-KEY') ?? $request->bearerToken();
        
        if (! $rawKey) {
            return ApiResponse::error('UNAUTHORIZED', 'Missing API key', [], 401);
        }

        // Authentifier
        $key = $authService->authenticateApiKeyForIa($rawKey);
        
        if (! $key) {
            return ApiResponse::error('UNAUTHORIZED', 'Invalid API key', [], 401);
        }

        // Vérifier validité pour l'IA
        if (! $authService->isValidForIa($key)) {
            return ApiResponse::error('FORBIDDEN', 'API key not valid for IA', [], 403);
        }

        // Vérifier IP
        if (! $authService->verifyIp($key, $request->ip())) {
            return ApiResponse::error('IP_NOT_ALLOWED', 'Your IP is not whitelisted', [], 403);
        }

        // Vérifier accès endpoint IA
        if (! $authService->canAccessIaEndpoint($key, $request->path())) {
            return ApiResponse::error('ENDPOINT_NOT_ALLOWED', 'Endpoint not allowed', [], 403);
        }

        // Stocker dans request
        $request->merge([
            'authenticated_api_key' => $key,
            'authenticated_api_client' => $key->application,
            'authenticated_client' => $key->application->client,
        ]);

        return $next($request);
    }
}
```

### Phase 4: Middleware de logging enrichi (1-2 jours)

**LogApiRequest Middleware - ENRICHIR**
```php
// app/Http/Middleware/LogApiRequest.php

class LogApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = (int) ((microtime(true) - $start) * 1000);

        // Récupérer auth info si dispo
        $key = $request->get('authenticated_key');
        $application = $request->get('authenticated_application');
        $client = $request->get('authenticated_client');

        ApiRequestLog::create([
            'api_client_id' => $application?->id,
            'api_key_id' => $key?->id,
            'client_id' => $client?->id,  // ← NOUVEAU
            'method' => $request->method(),
            'path' => $request->path(),
            'service' => $application?->type,  // ← NOUVEAU
            'endpoint' => $request->path(),  // ← NOUVEAU (normalisé)
            'status_code' => $response->status(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'origin' => $request->header('Origin'),
            'referer' => $request->header('Referer'),
            'duration_ms' => $duration,
            'request_size' => strlen($request->getContent()),
            'response_size' => strlen($response->content()),
            'error_message' => $response->status() >= 400 ? 'HTTP '.$response->status() : null,
            'cached' => false,
        ]);

        return $response;
    }
}
```

### Phase 5: Enrichir Filament Resources (1 jour)

**ApiClientResource (pour l'IA)**
```php
// app/Filament/Resources/ApiClientResource.php

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Get;

// Dans le formulaire:
Select::make('type')
    ->options([
        'ia' => '🤖 IA Service',
    ])
    ->default('ia')
    ->disabled() // ← Pour l'instant, tout est 'ia'
    ->helperText('Pour maintenant: tout est IA. Payment/Custom viendront plus tard.'),

TagsInput::make('allowed_endpoints')
    ->label('Endpoints IA autorisés')
    ->placeholder('api/v1/ai/*, api/v1/ai/generate, ...')
    ->helperText('Laisse vide pour accès libre à tous les endpoints IA')
    ->hint('Exemple: api/v1/ai/generate, api/v1/ai/models'),
```

**ApiKeyResource - ajouter type et allowed_endpoints**
```php
Select::make('type')
    ->options(['api_key' => 'API Key', 'oauth_token' => 'OAuth Token', ...])
    ->default('api_key'),

TagsInput::make('allowed_endpoints')
    ->label('Endpoint Restrictions'),
```

**ApiRequestLogResource - ajouter colonnes service, client, endpoint**
```php
TextColumn::make('service')->sortable(),
TextColumn::make('client.name')->label('Client'),
TextColumn::make('endpoint')->sortable(),
TextColumn::make('status_code')->sortable(),
TextColumn::make('duration_ms')->label('Duration (ms)'),
```

---

## 🔄 Flux d'exécution complet - Comment ça marche

Quand une requête arrive, voici le chemin exact qu'elle prend:

### Exemple: Un client "Payment" appelle `/api/v1/payments/charge`

```
1️⃣ CLIENT ENVOIE LA REQUÊTE
   POST /api/v1/payments/charge
   Header: X-API-KEY: apk_abc123xyz...
   Body: {"amount": 99.99}

2️⃣ MIDDLEWARE: LogApiRequest (commence chrono)
   ⏱️ Démarre le timer

3️⃣ MIDDLEWARE: UniversalAuthentication
   🔑 Récupère le header X-API-KEY
   🔍 Cherche en DB: ApiKey avec key_hash = SHA256(apk_abc123xyz...)
   ✅ Trouve la clé → charge la relation: ApiKey.application (ApiClient)
   
4️⃣ SERVICE: AuthenticationService.authenticateApiKey()
   📋 Récupère ApiClient et ses settings:
      - id: 5
      - type: 'payment' ← TYPE EST PAYMENT!
      - allowed_endpoints: ['api/v1/payments/*']
      - is_active: true
   
5️⃣ SERVICE: AuthenticationService.verifyValidity()
   ✅ ApiKey.is_active = true
   ✅ ApiKey.expires_at > now
   ✅ Tous les checks passent
   
6️⃣ SERVICE: AuthenticationService.verifyIp()
   🌐 Request IP: 192.168.1.100
   ✅ Pas de ip_whitelist OU IP dans whitelist
   
7️⃣ SERVICE: ApiClient.canAccessEndpoint()
   🚦 Vérifie l'accès selon TYPE:
      - Type = 'payment'
      - Endpoint demandé = 'api/v1/payments/charge'
      - Matche 'api/v1/payments/*' ? ✅ OUI!
      - Autorisé? ✅ OUI!
   
8️⃣ ROUTE HANDLER: PaymentController.charge()
   💰 Traite la requête
   ✅ Succès → Response 200

9️⃣ MIDDLEWARE: LogApiRequest (termine chrono)
   📝 Crée un log dans api_request_logs:
      - api_client_id: 5
      - api_key_id: 12
      - client_id: 3 ← Client propriétaire de l'app
      - service: 'payment' ← Copié depuis app.type!
      - endpoint: 'api/v1/payments/charge'
      - status_code: 200
      - duration_ms: 145
      - ip: '192.168.1.100'
      - method: 'POST'

🔟 RESPONSE RETOURNÉE AU CLIENT
    {"success": true, "data": {...}}
```

---

### Exemple 2: Un client "IA" essaie d'accéder à `/api/v1/payments/charge` ❌

```
1️⃣-6️⃣ Même jusqu'à verifyIp()... tout OK

7️⃣ SERVICE: ApiClient.canAccessEndpoint()
   🚦 Vérifie l'accès selon TYPE:
      - Type = 'ia' ← C'EST IA!
      - Endpoint demandé = 'api/v1/payments/charge'
      - Commence par 'api/v1/ai'? ❌ NON!
      - Autorisé? ❌ NON!
   
   ⛔ REJETTE LA REQUÊTE
   
8️⃣ MIDDLEWARE: UniversalAuthentication
   ❌ Retourne 403 Forbidden
   
9️⃣ RESPONSE:
    {
      "success": false,
      "error": {
        "code": "ENDPOINT_NOT_ALLOWED",
        "message": "You cannot access this endpoint"
      }
    }
```

---

### Exemple 3: Un client "Custom" avec allowed_endpoints restreints

```
Scénario:
- Type: 'custom'
- allowed_endpoints: ["api/v1/custom/export/*", "api/v1/reports/*"]
- Essaie d'appeler: POST /api/v1/custom/export/pdf

7️⃣ SERVICE: ApiClient.canAccessEndpoint()
   🚦 Vérifie l'accès selon TYPE:
      - Type = 'custom'
      - Endpoint demandé = 'api/v1/custom/export/pdf'
      - allowed_endpoints défini? ✅ OUI
      - Matche un pattern?
         - 'api/v1/custom/export/*' ? ✅ OUI! (export/pdf matche export/*)
      - Autorisé? ✅ OUI!
   
   ✅ AUTORISE LA REQUÊTE
```

---

### Schéma des décisions

```
REQUÊTE ARRIVE
    ↓
UniversalAuthentication Middleware
    ├─ Header X-API-KEY présent? → NON → ❌ 401 UNAUTHORIZED
    └─ Header X-API-KEY présent? → OUI
         ↓
    ApiKey existe en DB? → NON → ❌ 401 UNAUTHORIZED
    ApiKey existe en DB? → OUI
         ↓
    ApiKey.is_active = true? → NON → ❌ 403 FORBIDDEN
    ApiKey.is_active = true? → OUI
         ↓
    ApiKey valide temporellement? → NON → ❌ 403 FORBIDDEN
    ApiKey valide temporellement? → OUI
         ↓
    IP autorisée? → NON → ❌ 403 IP_NOT_ALLOWED
    IP autorisée? → OUI
         ↓
    ApiClient.canAccessEndpoint()?
         ├─ Si type='ia' → endpoint commence par 'api/v1/ai'? → NON → ❌ 403
         ├─ Si type='payment' → endpoint commence par 'api/v1/payments'? → NON → ❌ 403
         ├─ Si type='custom' → matche allowed_endpoints? → NON → ❌ 403
         └─ Toutes les vérifications passent? → OUI
              ↓
         ✅ ROUTE HANDLER
              ↓
         LogApiRequest enregistre tout
```

---

### Table de vérité - Qui peut accéder à quoi?

| Type | Endpoint demandé | Autorisé | Raison |
|------|------------------|----------|--------|
| `ia` | `/api/v1/ai/generate` | ✅ OUI | Matche type='ia' + pattern |
| `ia` | `/api/v1/payments/charge` | ❌ NON | Type est 'ia', pas 'payment' |
| `payment` | `/api/v1/payments/charge` | ✅ OUI | Matche type='payment' + pattern |
| `payment` | `/api/v1/ai/models` | ❌ NON | Type est 'payment', pas 'ia' |
| `custom` | `/api/v1/custom/export` | ✅ OUI | Type='custom' + endpoint matche allowed_endpoints |
| `custom` | `/api/v1/anything` | ❌ NON | Type='custom' mais endpoint ne matche pas |
| `custom` (sans allowed_endpoints) | `/api/v1/anything` | ✅ OUI | Custom sans restrictions = accès libre |

---

### Données créées dans les logs

Pour chaque requête, voici ce qui se crée dans `api_request_logs`:

```
INSERT INTO api_request_logs (
    api_client_id,        // 5 (l'app)
    api_key_id,           // 12 (la clé utilisée)
    client_id,            // 3 (le client propriétaire) ← NOUVEAU!
    service,              // 'payment' (copié depuis app.type) ← NOUVEAU!
    endpoint,             // 'api/v1/payments/charge' ← NOUVEAU!
    method,               // 'POST'
    path,                 // '/api/v1/payments/charge'
    status_code,          // 200
    ip,                   // '192.168.1.100'
    user_agent,           // 'curl/7.68.0'
    origin,               // 'https://example.com'
    duration_ms,          // 145
    request_size,         // 42
    response_size,        // 256
    error_message,        // null
    cached                // false
) VALUES (...)
```

**Avantage:** Tu peux facilement chercher:
- `WHERE client_id = 3` → Tous les appels du client 3
- `WHERE service = 'payment'` → Tous les appels au service Payment
- `WHERE endpoint LIKE 'api/v1/payments/%'` → Tous les appels spécifiques
- `WHERE api_key_id = 12 AND status_code >= 400` → Erreurs d'une clé spécifique

---

## 📋 Checklist d'implémentation

- [ ] **Phase 1: Migrations**
  - [ ] `add_type_to_api_clients_table`
  - [ ] `add_allowed_endpoints_to_api_clients_table`
  - [ ] `add_type_to_api_keys_table`
  - [ ] `add_allowed_endpoints_to_api_keys_table`
  - [ ] `add_service_and_client_to_api_request_logs`
  - [ ] `add_service_and_client_to_api_request_logs_archive`
  - [ ] `php artisan migrate`

- [ ] **Phase 2: Modèles**
  - [ ] Enrichir `ApiClient` avec `canAccessEndpoint()`
  - [ ] Enrichir `ApiKey` avec `canAccessEndpoint()`, `isValid()`, `isIpAllowed()`
  - [ ] Enrichir `ApiRequestLog` relations

- [ ] **Phase 3: Service & Middleware**
  - [ ] Créer `AuthenticationService`
  - [ ] Créer/enrichir `UniversalAuthentication` middleware
  - [ ] Tester authentification

- [ ] **Phase 4: Logging**
  - [ ] Enrichir `LogApiRequest` middleware pour capturer `service`, `client_id`, `endpoint`
  - [ ] Tester logs dans DB

- [ ] **Phase 5: Filament**
  - [ ] Ajouter champs type et allowed_endpoints à ApiClientResource
  - [ ] Ajouter champs type et allowed_endpoints à ApiKeyResource
  - [ ] Ajouter colonnes service, client, endpoint à ApiRequestLogResource

---

## 🎯 Avantages de cette approche

✅ **Pas de refonte** — on réutilise la BD existante bien pensée  
✅ **Migration progressive** — enrichir au lieu de remplacer  
✅ **Zero downtime** — anciennes colonnes restent, nouvelles sont optionnelles  
✅ **Multi-services** — Payment, IA, Custom avec `type` enum  
✅ **Accès granulaire** — allowed_endpoints au niveau app ET clé  
✅ **Audit complet** — client_id direct dans logs pour traçabilité  
✅ **Compatibilité** — code existant continue de marcher  

---

## 📈 Timeline révisée

- **Phase 1 (Migrations)**: 1 jour
- **Phase 2 (Modèles)**: 1 jour
- **Phase 3 (Service & Auth)**: 1-2 jours
- **Phase 4 (Logging)**: 1 jour
- **Phase 5 (Filament)**: 1 jour
- **Testing**: 1-2 jours

**Total: ~1-2 semaines** (vs 3-4 semaines pour refonde complète)

---

## 🔌 Schéma d'architecture - Flux de communication Client ↔ API Manager ↔ Ollama

### Vue d'ensemble complète

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          CLIENT (Entreprise)                                 │
│                    (Plugin WP, App IA, etc.)                                │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │  REQUEST (HTTP POST)   │
                    │ /api/v1/ai/generate    │
                    │ X-API-KEY: apk_xyz...  │
                    │ Body: {prompt: "..."}  │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼──────────────────────────────────────────┐
                    │        API MANAGER (Laravel)                           │
                    │                                                        │
                    │  ┌──────────────────────────────────────────────────┐ │
                    │  │ 1️⃣  MIDDLEWARE: LogApiRequest (START)           │ │
                    │  │     ⏱️ Démarre chrono                          │ │
                    │  └──────────────────┬───────────────────────────────┘ │
                    │                     │                                   │
                    │  ┌──────────────────▼───────────────────────────────┐ │
                    │  │ 2️⃣  MIDDLEWARE: UniversalAuthentication         │ │
                    │  │     🔑 Récupère X-API-KEY                      │ │
                    │  │     🔍 Cherche en BD: ApiKey.key_hash         │ │
                    │  │     ✅ Trouve → Charge ApiClient + Client     │ │
                    │  │     ✅ Vérifie: is_active, IP, expiration     │ │
                    │  │     ✅ Vérifie: endpoint autorisé              │ │
                    │  │     📝 $request->merge(['authenticated_...'])  │ │
                    │  └──────────────────┬───────────────────────────────┘ │
                    │                     │                                   │
                    │  ┌──────────────────▼───────────────────────────────┐ │
                    │  │ 3️⃣  ROUTE: /api/v1/ai/generate                  │ │
                    │  │     → AiController::generate()                  │ │
                    │  └──────────────────┬───────────────────────────────┘ │
                    │                     │                                   │
                    │  ┌──────────────────▼───────────────────────────────┐ │
                    │  │ 4️⃣  VALIDATION: GenerateAiRequest               │ │
                    │  │     ✅ prompt: required, string, max:10000      │ │
                    │  │     ✅ model: nullable, dans allowed_models     │ │
                    │  └──────────────────┬───────────────────────────────┘ │
                    │                     │                                   │
                    │  ┌──────────────────▼───────────────────────────────┐ │
                    │  │ 5️⃣  SERVICE: OllamaService::generate()           │ │
                    │  │     📤 POST /api/generate                      │ │
                    │  │        {model, prompt, stream: false}          │ │
                    │  │        Timeout: DB config (min 60s)            │ │
                    │  └──────────────────┬───────────────────────────────┘ │
                    │                     │                                   │
                    └─────────────────────┼───────────────────────────────────┘
                                         │
                        ┌────────────────▼────────────────┐
                        │    OLLAMA (IA Server)           │
                        │  https://ia.fayotech.com        │
                        │                                  │
                        │  6️⃣  POST /api/generate          │
                        │      Traite: model + prompt      │
                        │      Génère réponse IA           │
                        │      Retourne: {model, response, │
                        │                  total_duration,  │
                        │                  eval_count, ...} │
                        └────────────────┬────────────────┘
                                         │
                    ┌────────────────────▼────────────────────────────────┐
                    │        API MANAGER (réponse)                         │
                    │                                                      │
                    │  ┌──────────────────────────────────────────────┐   │
                    │  │ 7️⃣  CONTROLLER: ApiController::generate()    │   │
                    │  │     📝 Mappe réponse Ollama:                │   │
                    │  │        - total_duration (ns) → duration_ms  │   │
                    │  │        - response, model, done              │   │
                    │  │     📦 ApiResponse::success(data, meta)     │   │
                    │  └──────────────────┬──────────────────────────┘   │
                    │                     │                                │
                    │  ┌──────────────────▼──────────────────────────┐   │
                    │  │ 8️⃣  MIDDLEWARE: LogApiRequest (END)         │   │
                    │  │     ⏱️ Arrête chrono                       │   │
                    │  │     📝 INSERT api_request_logs:            │   │
                    │  │        - api_client_id: 5                 │   │
                    │  │        - api_key_id: 12                   │   │
                    │  │        - client_id: 3                     │   │
                    │  │        - service: 'ia'                    │   │
                    │  │        - endpoint: 'api/v1/ai/generate'   │   │
                    │  │        - status_code: 200                 │   │
                    │  │        - duration_ms: 245                 │   │
                    │  │        - ip, user_agent, etc.            │   │
                    │  └──────────────────┬──────────────────────────┘   │
                    │                     │                                │
                    │  ┌──────────────────▼──────────────────────────┐   │
                    │  │ 9️⃣  RESPONSE (JSON)                        │   │
                    │  │     HTTP 200 OK                           │   │
                    │  │     {                                     │   │
                    │  │       "success": true,                    │   │
                    │  │       "data": {                          │   │
                    │  │         "model": "llama3.2:3b",          │   │
                    │  │         "response": "Bonjour! Comment...",│   │
                    │  │         "done": true                    │   │
                    │  │       },                                 │   │
                    │  │       "meta": {                          │   │
                    │  │         "duration_ms": 245,             │   │
                    │  │         "prompt_eval_count": 26,        │   │
                    │  │         "eval_count": 88               │   │
                    │  │       }                                 │   │
                    │  │     }                                     │   │
                    │  └──────────────────┬──────────────────────────┘   │
                    │                     │                                │
                    └─────────────────────┼────────────────────────────────┘
                                         │
                    ┌────────────────────▼────────────────┐
                    │   CLIENT REÇOIT RESPONSE            │
                    │   (JSON avec réponse IA)            │
                    │                                      │
                    │   ✅ Affiche la réponse             │
                    │   ✅ Temps: 245ms                   │
                    │   ✅ Tokens utilisés: 88            │
                    └──────────────────────────────────────┘
```

---

### Détail des données à chaque étape

#### 📤 REQUEST (Client → API Manager)

```http
POST /api/v1/ai/generate HTTP/1.1
Host: api-manager.test
X-API-KEY: apk_abc123def456...
Content-Type: application/json

{
  "prompt": "Explique-moi la physique quantique en 3 lignes",
  "model": "llama3.2:3b"
}
```

**Métadonnées capturées:**
- Method: `POST`
- Path: `/api/v1/ai/generate`
- IP: `192.168.1.100`
- User-Agent: `curl/7.68.0`
- Request Size: ~150 bytes
- Timestamp: `2026-05-06 10:30:45`

---

#### 🔐 Authentification & Validation (API Manager)

```
1. Récupère X-API-KEY: "apk_abc123def456..."
2. Calcule: SHA256("apk_abc123def456...") = "hash_abc123..."
3. Cherche en BD:
   SELECT * FROM api_keys WHERE key_hash = "hash_abc123..."
   ✅ Trouve:
      - id: 12
      - api_client_id: 5
      - is_active: true
      - ip_whitelist: null (pas de restriction)
      - allowed_endpoints: null (tous les endpoints IA)

4. Charge relations:
   - ApiClient (id=5): type='ia', is_active=true
   - Client (id=3): name='faycalmoussouni.dev'

5. Vérifie:
   ✅ ApiKey.is_active = true
   ✅ ApiKey.expires_at > now
   ✅ ApiClient.is_active = true
   ✅ IP 192.168.1.100 autorisée
   ✅ Endpoint 'api/v1/ai/generate' autorisé pour type='ia'
   
6. Stocke dans $request:
   $request->authenticated_api_key = ApiKey(id=12, ...)
   $request->authenticated_api_client = ApiClient(id=5, ...)
   $request->authenticated_client = Client(id=3, ...)
```

---

#### ✅ Validation de formulaire (Request validation)

```
Règles appliquées:
- prompt: required, string, min:1, max:10000
  ✅ "Explique-moi..." passe (99 chars)
  
- model: nullable, string, in(allowed_models)
  ✅ "llama3.2:3b" dans ["llama3.2:3b"] → OK
  
Résultat: $validated = [
  'prompt' => 'Explique-moi la physique quantique en 3 lignes',
  'model' => 'llama3.2:3b'
]
```

---

#### 🤖 Appel Ollama (OllamaService)

```
POST https://ia.fayotech.com/api/generate
Headers:
  X-INTERNAL-AI-TOKEN: FVVpuEwsKtxn21DEVgS3d0...
  Content-Type: application/json
  Timeout: 120s (depuis DB)

Body:
{
  "model": "llama3.2:3b",
  "prompt": "Explique-moi la physique quantique en 3 lignes",
  "stream": false
}

⏱️ Durée: 245ms

Response (Ollama):
{
  "model": "llama3.2:3b",
  "created_at": "2026-05-06T10:30:47.123Z",
  "response": "La physique quantique...",
  "done": true,
  "context": [...],
  "total_duration": 245000000,    ← Nanosecondes!
  "load_duration": 123456,
  "prompt_eval_count": 26,
  "prompt_eval_duration": 111111111,
  "eval_count": 88,
  "eval_duration": 133888889
}
```

---

#### 📝 Transformation & Response (AiController)

```
// Reçoit réponse Ollama
$result = $ollama->generate($prompt, $model);

// Transforme:
$durationMs = (int) (245000000 / 1_000_000) = 245ms

// Construit response:
ApiResponse::success(
  data: [
    'model' => 'llama3.2:3b',
    'response' => 'La physique quantique...',
    'done' => true
  ],
  meta: [
    'duration_ms' => 245,
    'prompt_eval_count' => 26,
    'eval_count' => 88
  ]
)
```

---

#### 📊 Logging (api_request_logs)

```
INSERT INTO api_request_logs (
  api_client_id,      = 5
  api_key_id,         = 12
  client_id,          = 3          ← Nouveau
  method,             = 'POST'
  path,               = '/api/v1/ai/generate'
  service,            = 'ia'        ← Nouveau
  endpoint,           = 'api/v1/ai/generate'  ← Nouveau
  status_code,        = 200
  ip,                 = '192.168.1.100'
  user_agent,         = 'curl/7.68.0'
  origin,             = null
  referer,            = null
  duration_ms,        = 245
  request_size,       = 150
  response_size,      = 1240
  error_message,      = null
  cached,             = false
  created_at          = now()
)

✅ Enregistrement créé
```

**Permet de rechercher:**
```sql
-- Qui a utilisé le service IA?
SELECT * FROM api_request_logs 
WHERE service = 'ia' AND client_id = 3

-- Combien de temps prennent les appels IA?
SELECT AVG(duration_ms) FROM api_request_logs 
WHERE service = 'ia'

-- Quel endpoint est le plus utilisé?
SELECT endpoint, COUNT(*) FROM api_request_logs 
WHERE service = 'ia' GROUP BY endpoint

-- Erreurs sur une clé spécifique?
SELECT * FROM api_request_logs 
WHERE api_key_id = 12 AND status_code >= 400
```

---

#### 📤 RESPONSE (API Manager → Client)

```http
HTTP/1.1 200 OK
Content-Type: application/json
Content-Length: 1240

{
  "success": true,
  "data": {
    "model": "llama3.2:3b",
    "response": "La physique quantique étudie le comportement des particules à l'échelle atomique. Elle révèle que certaines propriétés (position, vitesse) ne peuvent pas être déterminées avec précision simultanément. Cette théorie probabiliste remplace la mécanique classique au niveau subatomique.",
    "done": true
  },
  "meta": {
    "duration_ms": 245,
    "prompt_eval_count": 26,
    "eval_count": 88
  }
}
```

**Métadonnées retournées:**
- `duration_ms`: Temps réel d'exécution (pour optimisation)
- `prompt_eval_count`: Tokens du prompt (pour facturation)
- `eval_count`: Tokens générés (pour facturation)

---

## 💾 État de la BD après la requête

### Avant:
```
api_clients: 5 clients existants
api_keys: 12 clés existantes
api_request_logs: 9999 logs existants
```

### Après:
```
api_clients: 5 clients (inchangé)
api_keys: 12 clés (inchangé)
api_request_logs: 10000 logs (+ 1 ligne)
```

**Nouvelle ligne dans api_request_logs:**
```
id: 10000
api_client_id: 5
api_key_id: 12
client_id: 3
service: 'ia'
endpoint: 'api/v1/ai/generate'
method: 'POST'
status_code: 200
duration_ms: 245
ip: '192.168.1.100'
created_at: '2026-05-06 10:30:47'
```

---

## 🔄 Réutilisabilité

**Même flux pour d'autres modèles:**
```
POST /api/v1/ai/generate
- model: "mistral"
→ Même authentification
→ Même validation
→ Même logging
→ Même response format
```

**Même flux pour Payment (plus tard):**
```
POST /api/v1/payments/charge
- type='payment' au lieu de 'ia'
→ Même authentification
→ Même validation
→ Même logging (service='payment')
→ Même response format
```

---



### 1. Le `type` définit l'accès automatiquement

```
Quand un ApiClient a type='ia':
- ✅ Peut appeler /api/v1/ai/* (automatique)
- ❌ Ne peut PAS appeler /api/v1/payments/* (bloqué par le type)
- ❌ Ne peut PAS appeler /api/v1/custom/* (bloqué par le type)
```

**C'est sécurisant:** Un client Payment ne peut pas "accidentellement" accéder à la IA.

---

### 2. Custom = Flexible

```
Si tu veux une app qui accède à PLUSIEURS services:
- Crée une app avec type='custom'
- Définis allowed_endpoints avec les patterns:
  ["api/v1/payments/*", "api/v1/reports/*", "api/v1/custom/*"]
```

**C'est puissant:** Une seule clé peut accéder à plusieurs services.

---

### 3. Les logs enregistrent TOUT

```
Chaque requête crée une ligne dans api_request_logs avec:
- Qui a appelé (client_id + api_key_id)
- Quel service (service = 'payment', 'ia', ou autre)
- Quel endpoint exact (endpoint = 'api/v1/payments/charge')
- Succès ou erreur (status_code)
- Temps d'exécution (duration_ms)
```

**C'est transparent:** Tu as une piste d'audit complète.

---

### 4. Filament aide à la saisie

```
Quand tu crées un ApiClient dans Filament:
1. Tu sélectionnes le type (ia / payment / custom)
2. Les allowed_endpoints se remplissent automatiquement
3. Si type != 'custom', tu vois juste un read-only preview
4. Si type = 'custom', tu peux éditer allowed_endpoints
```

**C'est user-friendly:** L'interface guide l'utilisateur.

---

## 🌍 Cas d'usage réels

### Cas 1: WordPress Plugin Payment

**Dans Filament:**
```
Créer un ApiClient:
- Name: "WP Shop Plugin"
- Type: payment ← Sélectionné
- Allowed endpoints: (auto-rempli) ["api/v1/payments/*"]
- Description: "Accepte les paiements depuis notre WP Shop"
```

**Ce qui se passe:**
```
Plugin WP appelle: POST /api/v1/payments/charge
  → ApiClient.type = 'payment'
  → Endpoint 'api/v1/payments/charge' matche 'api/v1/payments/*'
  → ✅ AUTORISÉ

Plugin WP essaie: GET /api/v1/ai/models
  → ApiClient.type = 'payment'
  → Endpoint 'api/v1/ai/models' ne matche pas 'api/v1/payments/*'
  → ❌ REFUSÉ (403 ENDPOINT_NOT_ALLOWED)
```

---

### Cas 2: Application IA complexe

**Dans Filament:**
```
Créer un ApiClient:
- Name: "Internal AI System"
- Type: custom ← Flexible!
- Allowed endpoints:
  - api/v1/ai/* (génération, modèles, etc.)
  - api/v1/reports/* (rapports IA)
  - api/v1/custom/training/* (entraînement custom)
- Description: "Système IA interne avec accès multi-services"
```

**Ce qui se passe:**
```
Système IA appelle: POST /api/v1/ai/generate
  → ApiClient.type = 'custom'
  → Endpoint 'api/v1/ai/generate' matche 'api/v1/ai/*'
  → ✅ AUTORISÉ

Système IA appelle: POST /api/v1/reports/monthly
  → ApiClient.type = 'custom'
  → Endpoint 'api/v1/reports/monthly' matche 'api/v1/reports/*'
  → ✅ AUTORISÉ

Système IA essaie: DELETE /api/v1/customers
  → ApiClient.type = 'custom'
  → Endpoint 'api/v1/customers' ne matche aucun pattern
  → ❌ REFUSÉ
```

---

### Cas 3: Clé réstreinte (ApiKey level)

**Même si l'app autorise tout, une clé peut être plus restrictive:**

```
ApiClient "Custom App":
- allowed_endpoints: ["api/v1/*"]  ← Tout!

Mais ApiKey "Limited Key":
- allowed_endpoints: ["api/v1/reports/read-only/*"]  ← Restreint

Quand on utilise "Limited Key":
  → D'abord vérifie ApiKey.allowed_endpoints
  → Puis vérifie ApiClient.allowed_endpoints
  → Les deux doivent être OK
```

**C'est sécurisant:** Tu peux donner une clé très restreinte même si l'app est flexible.

---

## 🚀 Demain matin - Commencer Phase 1

### Step 1: Créer les migrations (30 min)

```bash
php artisan make:migration add_type_and_allowed_endpoints_to_api_clients_table --table=api_clients
php artisan make:migration add_service_client_endpoint_to_api_request_logs --table=api_request_logs
php artisan make:migration add_service_client_endpoint_to_api_request_logs_archive --table=api_request_logs_archive
```

### Step 2: Écrire et tester les migrations (1h)

Copie le SQL de Phase 1, teste avec `php artisan migrate`.

### Step 3: Enrichir les modèles (1-2h)

Ajoute les méthodes dans ApiClient, ApiKey, ApiRequestLog (copy-paste depuis Phase 2).

### Step 4: Tester le service & middleware (1h)

Crée AuthenticationService et UniversalAuthentication (copy-paste depuis Phase 3).

### Step 5: Enrichir Filament (1h)

Ajoute les champs dans ApiClientResource (Type et AllowedEndpoints).

---

**Total: ~5-6h pour les 5 phases** ✅

Prêt ? 🚀
