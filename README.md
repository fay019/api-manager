# 🚀 API Manager - Laravel 12 + Filament v5

A production-ready, modular API hub system for centralizing multiple APIs with secure API key authentication, CORS per-client control, comprehensive logging, and a Filament v5 admin panel on shared hosting.

---

## ✨ Core Features

| Feature | Description |
|---------|-------------|
| 🔌 **Modular API Architecture** | `/api/v1/` versioning with independent module structure |
| 🔐 **Secure API Keys** | Bcrypt-hashed keys with per-client rate limiting (60/min default) |
| 🌍 **CORS Control** | Per-client allowed origins list with strict validation |
| 📊 **Request Logging** | Complete audit trail with filtering and analytics |
| 🎨 **Filament v5 Admin** | Comprehensive dashboard for managing clients, keys, and logs |
| 🎯 **Promo API Module** | Smart banner selection, event tracking, version history |
| 📦 **Shared Hosting Ready** | No Node.js, minimal dependencies, SQLite/MySQL support |
| 📚 **Dynamic Documentation** | Markdown to HTML conversion with beautiful styling |

---

## 🎯 Quick Start

### Prerequisites

- **PHP 8.4+** with required extensions (bcmath, ctype, json, mbstring, openssl, pdo, tokenizer, xml)
- **MySQL 5.7+** or **SQLite**
- **Composer** installed globally
- **Git** (for version control)

### Installation Steps

```bash
# 1. Clone or download the project
git clone <repository-url>
cd api-manager

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Create database and run migrations
php artisan migrate

# 6. Seed default admin user
php artisan db:seed

# 7. Start development server
php artisan serve
```

**Access the application:**
- 🌐 **Frontend**: http://localhost:8000
- 🔧 **Admin Panel**: http://localhost:8000/admin
- 📚 **Documentation**: http://localhost:8000/docs

**Default Admin Credentials:**
```
Email: admin@moussouni.dev
Password: password

⚠️ IMPORTANT: Change this password immediately in production!
```

---

## 📡 API Endpoints Overview

### Health Check (Public)
```bash
GET /api/v1/health
```

Returns: `{"success": true, "data": {"status": "ok", "timestamp": "..."}}`

### Get Active Promo Banner
```bash
GET /api/v1/promo/banner.json
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Track Promo Event
```bash
POST /api/v1/promo/event
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

Body: {
  "promo_id": 1,
  "event_type": "impression|click|dismiss",
  "session_id": "abc123",
  "url": "https://example.com"
}
```

For complete API documentation, see **[API.md](./docs/API.md)**

---

## 🔑 Creating API Clients & Keys

### Via Admin Panel (Recommended)

1. **Login** to `/admin`
2. **Go to** API Management → API Clients
3. **Create new client** with:
   - Name (e.g., "Mobile App", "Web Dashboard")
   - Type (MOBILE, WEB, PARTNER, INTERNAL)
   - Contact info
   - Rate limit (default: 60 req/min)
   - Allowed origins (CORS)
4. **Save client**
5. **Go to** API Keys tab
6. **Create API key** - Copy it immediately (won't be shown again)

### Via API (Programmatically)

See **[CLIENTS.md](./docs/CLIENTS.md)** for detailed client management documentation.

---

## 📁 Project Structure

```
api-manager/
├── app/
│   ├── Enums/                          # Application enums
│   ├── Filament/                       # Filament admin resources
│   │   ├── Pages/                      # Custom pages
│   │   └── Resources/                  # CRUD resources
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    # API controllers
│   │   │   └── DocsController.php      # Documentation
│   │   ├── Middleware/                 # Authentication, CORS, rate limiting
│   │   └── Responses/                  # Standard API responses
│   ├── Models/                         # Eloquent models
│   ├── Modules/
│   │   └── Promo/                      # Promo module
│   ├── Services/                       # Business logic
│   ├── Observers/                      # Model observers
│   └── Providers/                      # Service providers
│
├── config/
│   └── api.php                         # API configuration
│
├── database/
│   ├── migrations/                     # Database tables
│   └── seeders/                        # Database seeders
│
├── routes/
│   ├── api.php                         # API route definitions
│   ├── api/
│   │   └── v1.php                      # API v1 routes
│   └── web.php                         # Web routes
│
├── resources/
│   ├── views/
│   │   ├── docs/                       # Documentation views
│   │   └── home.blade.php              # Homepage
│   └── css/                            # Stylesheets
│
├── docs/                               # Markdown documentation
│   ├── API.md                          # API reference
│   ├── DATABASE.md                     # Database schema
│   ├── DEPLOYMENT.md                   # Deployment guide
│   ├── CLIENTS.md                      # Client management
│   └── PROMOS.md                       # Promotions system
│
├── public/                             # Web server root
├── storage/                            # Logs, cache, uploads
├── .env.example                        # Environment template
├── artisan                             # Laravel CLI
└── composer.json                       # PHP dependencies
```

---

## 🔧 Adding New API Modules

To create a new API module:

1. **Create module structure:**
   ```bash
   mkdir -p app/Modules/YourModule/Http/Controllers
   ```

2. **Create controller:**
   ```php
   // app/Modules/YourModule/Http/Controllers/YourController.php
   namespace App\Modules\YourModule\Http\Controllers;

   use App\Http\Responses\ApiResponse;

   class YourController {
       public function index() {
           return ApiResponse::success([
               'message' => 'Hello from YourModule'
           ]);
       }
   }
   ```

3. **Create routes file:**
   ```php
   // routes/api/modules/yourmodule.php
   Route::get('/your-endpoint', [\App\Modules\YourModule\Http\Controllers\YourController::class, 'index']);
   ```

4. **Include in v1 routes:**
   ```php
   // routes/api/v1.php
   require __DIR__ . '/modules/yourmodule.php';
   ```

**Automatic Benefits:**
- ✅ API key authentication
- ✅ CORS validation
- ✅ Rate limiting
- ✅ Request logging
- ✅ Standard error handling

---

## 📊 Database Schema

The system uses 8 interconnected tables:

```
┌─────────────────────────────────────────────┐
│                   users                     │
├─────────────────────────────────────────────┤
│ - Admin user management                     │
│ - Password stored as bcrypt hash            │
└──────────┬──────────────────────────────────┘
           │
           ├─── api_clients ──────────────────────┐
           │                                      │
           ├─── promos ──────────────┐            │
           │                         │            │
           └─── promo_versions       └─ promo_events
                                     └─ api_keys ────┬─── api_request_logs
                                                     │
                                                     └─ documentation_settings
```

For detailed schema information, see **[DATABASE.md](./docs/DATABASE.md)**

---

## 🚀 Deployment

### Production Deployment

For complete deployment guide to shared hosting:

See **[DEPLOYMENT.md](./docs/DEPLOYMENT.md)** for:
- ✅ PHP requirements verification
- ✅ Database setup & configuration
- ✅ Environment configuration
- ✅ File permissions
- ✅ HTTPS & security hardening
- ✅ Cron jobs setup
- ✅ Monitoring & logging
- ✅ Troubleshooting

### Quick Deploy Checklist

```bash
# Before pushing to production:
- [ ] PHP 8.4+ with all extensions
- [ ] Database created and accessible
- [ ] .env configured for production
- [ ] Migrations run: php artisan migrate --force
- [ ] Seeds run: php artisan db:seed --force
- [ ] Storage permissions: chmod -R 755 storage bootstrap/cache
- [ ] Cron job configured (runs every minute)
- [ ] HTTPS enabled
- [ ] Admin password changed
- [ ] APP_DEBUG = false
- [ ] APP_ENV = production
```

---

## 📚 Documentation

### Complete Documentation Suite

| Document | Purpose |
|----------|---------|
| [**API.md**](./docs/API.md) | Complete API reference with endpoints, authentication, examples |
| [**DATABASE.md**](./docs/DATABASE.md) | Database schema, relationships, migrations, queries |
| [**DEPLOYMENT.md**](./docs/DEPLOYMENT.md) | Production deployment guide for shared hosting |
| [**CLIENTS.md**](./docs/CLIENTS.md) | API client management and configuration |
| [**PROMOS.md**](./docs/PROMOS.md) | Promotional banners system |

### Accessing Documentation

1. **In Application**: Visit `/docs` in your browser
2. **In Admin Panel**: Settings → Documentation Settings (manage visibility)
3. **View Raw**: Check `/docs/` directory for markdown files

---

## 🔐 Security Features

| Security Feature | Implementation |
|-----------------|----------------|
| **API Key Hashing** | Bcrypt hashing - raw keys shown only once |
| **Data Privacy** | Session ID, IP, User-Agent hashed with SHA256 |
| **CORS Protection** | Per-client origin whitelist validation |
| **Rate Limiting** | Per-client rate limits (configurable, default 60 req/min) |
| **Request Logging** | Complete audit trail for security monitoring |
| **Input Validation** | All endpoints validate input before processing |
| **HTTPS** | SSL/TLS enforcement for all API calls |
| **Admin Access** | Protected admin panel with login required |

---

## 📊 Monitoring & Analytics

### Admin Dashboard

Track everything from the admin panel:

- **Request Logs**: Filter by client, method, status, date range
- **API Clients**: Monitor active clients and their usage
- **API Keys**: Track key lifecycle (active, expired, revoked, scheduled)
- **Promo Analytics**: Monitor banner impressions, clicks, dismissals
- **Error Tracking**: Identify and troubleshoot failed requests

### Command Line Monitoring

```bash
# Prune old request logs (default: 90 days)
php artisan api:prune-logs --days=90

# Prune old promo events (default: 180 days)
php artisan promo:prune-events --days=180

# View application logs
tail -f storage/logs/laravel.log
```

---

## 🛠️ Configuration

### API Configuration

Edit `config/api.php`:

```php
return [
    // Default rate limit (requests per minute)
    'default_rate_limit' => env('API_DEFAULT_RATE_LIMIT', 60),

    // Public request rate limit (per IP)
    'unauth_rate_limit' => env('API_UNAUTH_RATE_LIMIT', 10),

    // Promo cache duration (seconds)
    'promo_cache_ttl' => env('PROMO_CACHE_TTL', 3600),

    // Request log retention (days)
    'log_retention_days' => env('API_LOG_RETENTION_DAYS', 90),
];
```

### Environment Variables

Key `.env` variables:

```env
# Application
APP_NAME="API Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=api_manager
DB_USERNAME=user
DB_PASSWORD=password

# API Configuration
API_DEFAULT_RATE_LIMIT=60
API_UNAUTH_RATE_LIMIT=10
PROMO_CACHE_TTL=3600
API_LOG_RETENTION_DAYS=90
PROMO_EVENT_RETENTION_DAYS=180
```

---

## 🤝 Support & Troubleshooting

### Common Issues

**500 Error on Admin Panel**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Verify permissions
chmod -R 755 storage bootstrap/cache
```

**API Endpoints Return 404**
```bash
# Clear and recache routes
php artisan route:clear
php artisan route:cache
```

**Database Connection Error**
```bash
# Verify .env configuration
grep DB_ .env

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

For comprehensive troubleshooting, see **[DEPLOYMENT.md](./docs/DEPLOYMENT.md#troubleshooting)**

---

## 📝 License

Private - Internal use only

---

## 🙋 Need Help?

1. Check the relevant documentation file (see above)
2. Review application logs: `tail -f storage/logs/laravel.log`
3. Test endpoints with curl or Postman
4. Check admin panel for detailed error messages
5. Contact your system administrator

---

**Last Updated:** 2026-01-20
**Version:** 1.0.0
**Framework:** Laravel 12
**Admin Panel:** Filament v5
**Database:** MySQL 5.7+ / SQLite