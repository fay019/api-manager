# 🔧 AMÉLIORATIONS ARCHITECTURE

## 1. INDEXES MANQUANTS

```php
// Migration à ajouter
Schema::table('clients', function (Blueprint $table) {
    $table->index('email');           // Login queries
    $table->index('is_active');       // Filter active users
    $table->index('created_at');      // Recent clients
});

Schema::table('api_clients', function (Blueprint $table) {
    $table->index(['client_id', 'is_active']);  // Combined for queries
    $table->index('created_at');      // Sorting
});

Schema::table('api_keys', function (Blueprint $table) {
    $table->index('created_at');      // Recent keys
    $table->index(['is_active', 'expires_at']);  // Validation queries
});

Schema::table('api_request_logs', function (Blueprint $table) {
    $table->index(['api_key_id', 'created_at']);  // Key usage history
    $table->index(['status_code']);   // Error analysis
});
```

## 2. SCOPES ELOQUENT POUR N+1

```php
// app/Models/Client.php
public function scopeActive($query) {
    return $query->where('is_active', true);
}

public function scopeWithApiMetrics($query) {
    return $query->with([
        'apiClients' => fn($q) => $q->active(),
        'apiClients.apiKeys',
        'apiClients.requestLogs' => fn($q) => $q->latest('created_at')->limit(100)
    ]);
}

// app/Models/ApiClient.php
public function scopeActive($query) {
    return $query->where('is_active', true);
}

public function scopeWithMetrics($query) {
    return $query->withCount([
        'apiKeys as active_keys' => fn($q) => $q->where('is_active', true),
        'requestLogs as total_requests',
        'requestLogs as success_requests' => fn($q) => $q->whereBetween('status_code', [200, 299])
    ]);
}

// app/Models/ApiKey.php
public function scopeValid($query) {
    return $query->where('is_active', true)
        ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
        ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
}
```

## 3. RESOURCE CLASSES API

```php
// app/Http/Resources/ClientResource.php
class ClientResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'api_clients_count' => $this->apiClients()->count(),
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
        ];
    }
}

// app/Http/Resources/ApiClientResource.php
class ApiClientResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'rate_limit' => $this->rate_limit_per_minute,
            'keys_count' => $this->apiKeys()->count(),
            'requests_today' => $this->requestLogs()
                ->whereDate('created_at', today())->count(),
        ];
    }
}
```

## 4. VALIDATIONS AMÉLIORÉES

```php
// app/Http/Requests/Client/RegisterRequest.php
public function rules(): array {
    return [
        'name' => ['required', 'string', 'max:255', 'not_regex:/^\d+$/'],
        'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:clients'],
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
        ],
    ];
}

// app/Filament/Resources/ApiClientResource.php
'allowed_origins' => [
    'nullable',
    'array',
    new ValidCorsOrigins,  // Custom rule
],
'webhook_url' => [
    'nullable',
    'url',
    'active_url',  // Vérifie domaine existe
],
'monthly_quota' => [
    'nullable',
    'numeric',
    'min:100',      // Minimum practical value
    'multiple_of:100',
],
```

## 5. CACHE & QUERY OPTIMIZATION

```php
// app/Models/ApiClient.php
protected $casts = [
    'allowed_origins' => 'array',
    'is_active' => 'boolean',
    'activated_at' => 'datetime',
];

// Dashboard query optimized
public function getDashboardData() {
    return Cache::remember(
        "client_{$this->id}_dashboard",
        minutes: 5,
        callback: fn() => [
            'api_clients' => $this->apiClients()
                ->select('id', 'name', 'is_active', 'rate_limit_per_minute')
                ->withCount(['apiKeys', 'requestLogs'])
                ->get(),
            'recent_logs' => ApiRequestLog::where('api_client_id', $this->id)
                ->latest('created_at')
                ->limit(50)
                ->select('id', 'api_client_id', 'status_code', 'duration_ms', 'created_at')
                ->get(),
        ]
    );
}
```

## 6. SOFT DELETES POUR AUDIT

```php
// Migration
Schema::table('clients', function (Blueprint $table) {
    $table->softDeletes();
    $table->index('deleted_at');
});

// app/Models/Client.php
use SoftDeletes;
protected $dates = ['deleted_at'];

// Preserve logs after user deletion
Schema::table('api_request_logs', function (Blueprint $table) {
    // Keep: api_client_id NULL, api_key_id NULL
    // Logs never deleted, only client soft-deleted
});
```

## 7. MONITORING FIELDS

```php
// Migration
Schema::table('api_keys', function (Blueprint $table) {
    $table->bigInteger('usage_count')->default(0);
    $table->timestamp('last_rotated_at')->nullable();
    $table->string('rotation_reason')->nullable();
});

// app/Models/ApiKey.php
protected $fillable = [
    // ... existing
    'usage_count',
    'last_rotated_at',
    'rotation_reason',
];

// Middleware
ApiKeyService::validateKey() → $key->update(['usage_count' => $key->usage_count + 1]);
```

## 8. SEPARATION CONCERNS (OPTIONAL)

```php
// app/Models/ClientProfile.php - Future split
class ClientProfile extends Model {
    protected $table = 'clients';  // Shared table
    protected $with = ['contact_info'];
    
    public function contactInfo() {
        return $this->selectRaw(
            'id, contact_name, contact_email, description, notes'
        );
    }
}

// app/Models/ClientAuth.php - Future split
class ClientAuth extends Model {
    protected $table = 'clients';
    protected $only = ['id', 'email', 'password', 'is_active', 'last_login_at'];
}
```

## 9. VALIDATION CUSTOM RULES

```php
// app/Rules/ValidCorsOrigins.php
class ValidCorsOrigins implements Rule {
    public function passes($attribute, $value): bool {
        if (!is_array($value)) return false;
        
        return collect($value)->every(function($origin) {
            return filter_var($origin, FILTER_VALIDATE_URL) !== false
                && str_starts_with($origin, ['http://', 'https://']);
        });
    }
}

// app/Rules/UniqueApiKeyName.php
class UniqueApiKeyName implements Rule {
    public function passes($attribute, $value): bool {
        return !ApiKey::where('api_client_id', $this->apiClientId)
            ->where('name', $value)
            ->when($this->keyId, fn($q) => $q->where('id', '!=', $this->keyId))
            ->exists();
    }
}
```

## 10. POLICY AUTHORIZATION

```php
// app/Policies/ApiClientPolicy.php
public function view(Client $user, ApiClient $apiClient): bool {
    return $user->id === $apiClient->client_id || $user->is_admin;
}

public function update(Client $user, ApiClient $apiClient): bool {
    return $user->id === $apiClient->client_id;
}

// Usage
$this->authorize('view', $apiClient);
```

---

## STATUS: Ready to implement without breaking changes
