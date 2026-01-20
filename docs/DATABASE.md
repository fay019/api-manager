# Database Schema

## Overview

8 tables support the API hub system:

```
users
├── api_clients
│   └── api_keys
│       └── api_request_logs
├── promos
│   ├── promo_versions
│   └── promo_events
└── documentation_settings
```

---

## Tables

### users

Default Laravel users table + admin flag.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | Primary key |
| name | string(255) | ❌ | | User display name |
| email | string(255) | ❌ | | Unique email |
| email_verified_at | timestamp | ✅ | null | Email verification |
| password | string(255) | ❌ | | Hashed password |
| **is_admin** | boolean | ❌ | false | Admin access flag |
| remember_token | string(100) | ✅ | null | "Remember me" token |
| created_at | timestamp | ❌ | now() | |
| updated_at | timestamp | ❌ | now() | |

**Indexes:**
- `email` (unique)

**Relationships:**
- `hasMany(Promo)` via `created_by`
- `hasMany(PromoVersion)` via `created_by`

**Seed Data:**
- Default admin: email=`admin@moussouni.dev`, password=`password` (change in production!)

---

### api_clients

API client accounts with configuration.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| name | string(255) | ❌ | | Display name (e.g., "Website A") |
| status | string(20) | ❌ | | `active` or `disabled` |
| allowed_origins | json | ✅ | null | CORS origins: `["https://example.com"]` |
| notes | text | ✅ | null | Internal notes |
| rate_limit_per_minute | integer | ❌ | 60 | Requests/minute for this client |
| created_at | timestamp | ❌ | now() | |
| updated_at | timestamp | ❌ | now() | |

**Indexes:**
- `status`

**Relationships:**
- `hasMany(ApiKey)`
- `hasMany(ApiRequestLog)`

**Queries:**
```php
// Get active clients
ApiClient::where('status', 'active')->get();

// Get client's keys
$client->apiKeys()->where('is_active', true)->get();

// Get client's logs (last 24h)
$client->requestLogs()->where('created_at', '>=', now()->subDay())->get();
```

---

### api_keys

Secure API keys for client authentication.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| api_client_id | bigint | ❌ | | Foreign key to `api_clients` |
| key_hash | string(255) | ❌ | | Bcrypt hash (never show raw) |
| key_prefix | string(8) | ❌ | | e.g., `apk_1234` (first 8 chars) |
| name | string(255) | ❌ | | Display name |
| last_used_at | timestamp | ✅ | null | Last use timestamp |
| expires_at | timestamp | ✅ | null | Expiration date (null = never) |
| is_active | boolean | ❌ | true | Active/revoked flag |
| created_at | timestamp | ❌ | now() | |
| updated_at | timestamp | ❌ | now() | |

**Indexes:**
- `api_client_id, is_active` (composite)
- `key_prefix`

**Security:**
- `key_hash` is bcrypt hashed
- Raw key shown only once on creation
- Never stored in logs

**Query Pattern:**
```php
// Validate key
$key = ApiKey::where('key_prefix', $prefix)
    ->where('is_active', true)
    ->with('apiClient')
    ->first();

if ($key && password_verify($rawKey, $key->key_hash)) {
    // Valid
}
```

**Accessors:**
- `is_expired`: boolean (checks `expires_at`)
- `is_valid`: boolean (checks active + expired + client status)

---

### api_request_logs

Audit trail of all API requests.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| api_client_id | bigint | ✅ | null | Foreign key (public requests = null) |
| api_key_id | bigint | ✅ | null | Foreign key (if authenticated) |
| method | string(10) | ❌ | | HTTP method: GET, POST, etc. |
| path | string(255) | ❌ | | Request path e.g. `/promo/banner.json` |
| status_code | integer | ❌ | | HTTP response code: 200, 404, 429, etc. |
| ip | string(45) | ❌ | | Client IP (IPv4 or IPv6) |
| user_agent | string(255) | ✅ | null | User-Agent header |
| origin | string(255) | ✅ | null | CORS Origin header |
| referer | string(255) | ✅ | null | HTTP Referer header |
| duration_ms | integer | ✅ | null | Request duration in milliseconds |
| created_at | timestamp | ❌ | | Record timestamp (no update) |

**Indexes:**
- `api_client_id, created_at` (composite - for filtering by client+date)
- `status_code, created_at` (composite - for error tracking)
- `created_at` (for pruning old logs)

**Note:** No `updated_at` - logs are immutable

**Analytics Queries:**
```php
// Requests in last 24 hours
ApiRequestLog::where('created_at', '>=', now()->subDay())->count();

// Error rate
$errors = ApiRequestLog::where('status_code', '>=', 400)
    ->where('created_at', '>=', now()->subDay())
    ->count();

// Top endpoints
ApiRequestLog::selectRaw('path, count(*) as count')
    ->groupBy('path')
    ->orderByDesc('count')
    ->limit(10)
    ->get();

// Top clients
ApiRequestLog::selectRaw('api_client_id, count(*) as count')
    ->groupBy('api_client_id')
    ->with('apiClient')
    ->orderByDesc('count')
    ->limit(10)
    ->get();
```

**Pruning:**
```bash
# Delete logs older than 90 days
php artisan api:prune-logs --days=90
```

---

### documentation_settings

Configuration for documentation files displayed in admin panel.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| doc_name | string(255) | ❌ | | Unique document identifier (e.g., `readme`, `api`, `deployment`) |
| path | string(255) | ❌ | | File path relative to project root (e.g., `/README.md`, `/docs/API.md`) |
| is_visible | boolean | ❌ | true | Whether document is shown in admin panel |
| show_admin_credentials | boolean | ❌ | true | Display admin credentials on home page |
| created_at | timestamp | ❌ | now() | |
| updated_at | timestamp | ❌ | now() | |

**Indexes:**
- `doc_name` (unique)

**Relationships:**
- No foreign keys (independent configuration)

**Purpose:**
- Track which documentation files exist in the project
- Control visibility of each document in Filament admin panel
- Store admin credentials visibility preference
- Synced automatically via `DocumentationScanner::sync()`

**Queries:**
```php
// Get all visible docs
DocumentationSetting::where('is_visible', true)->get();

// Check specific doc visibility
DocumentationSetting::where('doc_name', 'api')->first()->is_visible;

// Should show admin credentials (local env only)
DocumentationSetting::shouldShowCredentials();

// Get doc by name
DocumentationSetting::getByName('readme');
```

**Sync Logic:**
- Automatically discovers `.md` files in project root and `/docs` directory
- Creates new entries with `is_visible = true` for newly discovered docs
- Preserves existing `is_visible` settings on sync (doesn't reset to true)
- Deletes entries for files that no longer exist (except 'settings' doc)

**Auto-Caching:**
- Cache cleared on any create/update/delete via model observer
- Cache key: `documentation_settings`

---

### promos

Promotional banners.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| title | string(255) | ❌ | | Promo title |
| content | text | ❌ | | Promo description (plain text only) |
| image_url | string(255) | ✅ | null | Banner image URL |
| cta_text | string(255) | ✅ | null | Call-to-action button text |
| cta_url | string(255) | ✅ | null | CTA link destination |
| status | string(20) | ❌ | | `draft`, `scheduled`, `published`, `archived` |
| starts_at | timestamp | ✅ | null | When to show (null = now) |
| ends_at | timestamp | ✅ | null | When to hide (null = forever) |
| priority | integer | ❌ | 0 | Sort order (higher = shown first) |
| created_by | bigint | ❌ | | Foreign key to `users` (admin who created) |
| created_at | timestamp | ❌ | now() | |
| updated_at | timestamp | ❌ | now() | |

**Indexes:**
- `status, priority, starts_at, ends_at` (composite - for active promo query)

**Status Values:**
- `draft`: Not yet published
- `scheduled`: Will be published at `starts_at`
- `published`: Currently active (shown if dates match)
- `archived`: No longer shown

**Active Promo Selection:**
```php
// Uses scope: Promo::active()
Promo::where('status', 'published')
    ->where(function ($q) {
        $q->whereNull('starts_at')
          ->orWhere('starts_at', '<=', now());
    })
    ->where(function ($q) {
        $q->whereNull('ends_at')
          ->orWhere('ends_at', '>=', now());
    })
    ->orderByDesc('priority')
    ->orderByDesc('created_at')
    ->first();
```

**Caching:**
- Cached for 60 seconds with tag `promo:active`
- Cache cleared on create/update/delete
- Updates also create `promo_versions` entry via observer

---

### promo_versions

Version history snapshots (automatic on change).

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| promo_id | bigint | ❌ | | Foreign key to `promos` |
| version | integer | ❌ | | Sequential version number (1, 2, 3...) |
| payload_json | json | ❌ | | Full promo snapshot at this version |
| created_by | bigint | ❌ | | Admin user who made change |
| created_at | timestamp | ❌ | | Version timestamp (no update) |

**Composite Unique Index:**
- `promo_id, version`

**Payload Structure:**
```json
{
  "title": "Summer Sale",
  "content": "50% off",
  "image_url": "https://...",
  "cta_text": "Shop Now",
  "cta_url": "https://...",
  "status": "published",
  "starts_at": "2026-06-01T00:00:00Z",
  "ends_at": "2026-08-31T23:59:59Z",
  "priority": 10
}
```

**Observer:**
- Automatically creates version on `Promo::create()`
- Automatically creates version on `Promo::update()`
- Incrementing version number

**Query:**
```php
// Get promo history
$promo->versions()->orderByDesc('version')->get();

// Compare versions
$v1 = $promo->versions()->where('version', 1)->first();
$v2 = $promo->versions()->where('version', 2)->first();
```

**Note:** No `updated_at` - versions are immutable records

---

### promo_events

User interactions with promos (impressions, clicks, dismissals).

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | ❌ | auto | |
| promo_id | bigint | ❌ | | Foreign key to `promos` |
| event_type | string(20) | ❌ | | `impression`, `click`, `dismiss` |
| session_hash | string(64) | ✅ | null | SHA256(session_id) |
| ip_hash | string(64) | ✅ | null | SHA256(ip_address) |
| user_agent_hash | string(64) | ✅ | null | SHA256(user_agent) |
| referer | string(255) | ✅ | null | HTTP Referer (not hashed) |
| origin | string(255) | ✅ | null | HTTP Origin (not hashed) |
| created_at | timestamp | ❌ | | Event timestamp (no update) |

**Indexes:**
- `promo_id, event_type, created_at` (composite - for analytics)
- `created_at` (for pruning)

**Privacy:**
- Session ID, IP, User-Agent hashed with SHA256
- Referer and Origin stored as-is (needed for analytics)
- Never store raw identifiers

**Analytics Queries:**
```php
// Event count by type
PromoEvent::where('promo_id', $id)
    ->selectRaw('event_type, count(*) as count')
    ->groupBy('event_type')
    ->get();

// Click-through rate
$impressions = PromoEvent::where('promo_id', $id)
    ->where('event_type', 'impression')
    ->count();
$clicks = PromoEvent::where('promo_id', $id)
    ->where('event_type', 'click')
    ->count();
$ctr = ($impressions > 0) ? ($clicks / $impressions) * 100 : 0;

// Trend (last 7 days)
PromoEvent::where('promo_id', $id)
    ->where('created_at', '>=', now()->subDays(7))
    ->selectRaw('DATE(created_at) as date, event_type, count(*) as count')
    ->groupBy('date', 'event_type')
    ->orderBy('date')
    ->get();
```

**Pruning:**
```bash
# Delete events older than 180 days
php artisan promo:prune-events --days=180
```

**Note:** No `updated_at` - events are immutable

---

## Relationships

```
User
├── hasMany(Promo) via created_by
└── hasMany(PromoVersion) via created_by

ApiClient
├── hasMany(ApiKey)
└── hasMany(ApiRequestLog)

ApiKey
├── belongsTo(ApiClient)
└── hasMany(ApiRequestLog)

ApiRequestLog
├── belongsTo(ApiClient)
└── belongsTo(ApiKey)

Promo
├── belongsTo(User) via created_by
├── hasMany(PromoVersion)
└── hasMany(PromoEvent)

PromoVersion
├── belongsTo(Promo)
└── belongsTo(User) via created_by

PromoEvent
└── belongsTo(Promo)
```

---

## Performance Considerations

### Indexes
All critical queries have indexes:
- API client lookups by status
- API key lookups by prefix
- Active promo queries
- Request log analytics
- Event analytics

### Pagination
- API request logs: paginate by 50
- Promo events: paginate by 100
- Query large result sets incrementally

### Caching
- Active promo cached 60 seconds
- Use cache tags for invalidation

### Pruning
- Run `api:prune-logs` daily (scheduled)
- Run `promo:prune-events` daily (scheduled)
- Keeps database size manageable

### Query Optimization
- Use `selectRaw()` for aggregations
- Eager load relationships with `with()`
- Limit date ranges in analytics queries

---

## Backups

For shared hosting:
- Export MySQL: `mysqldump -u user -p dbname > backup.sql`
- Export SQLite: `cp database.sqlite backup.sqlite`
- Include `.env` in backups (don't commit to git)
