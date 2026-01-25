# 🚀 API Manager - Laravel 12 + Filament v5

A production-ready, modular API hub system for centralizing multiple APIs with secure API key authentication, CORS per-client control, comprehensive logging, and a Filament v5 admin panel on shared hosting.

**📦 GitHub Repository**: [fay019/api-manager](https://github.com/fay019/api-manager)
**🔗 Clone & Deploy**: `git clone https://github.com/fay019/api-manager.git`
**⭐ Version**: 1.0.0 | **✨ Installation**: 1 command (`php artisan install`)

---

## ⚙️ Application Lifecycle: Binary State Switch

The application operates in two mutually exclusive modes based on the presence of `storage/app/installed.lock`.

### 1. PRE-INSTALL Mode (Setup Wizard)
*   **Trigger**: `installed.lock` is missing.
*   **Behavior**: 
    *   Only `routes/setup.php` is loaded.
    *   Standard app routes are **not registered**, preventing conflicts with Livewire v3.
    *   **Stateless architecture**: No standard Laravel sessions or encrypted cookies are used to avoid `DECRYPT_FAILED` errors when `APP_KEY` changes.
    *   Progress is stored in `storage/app/setup/progress_[token].json`.

### 2. POST-INSTALL Mode (Standard Laravel)
*   **Trigger**: `installed.lock` exists.
*   **Behavior**: Normal Laravel lifecycle with Filament and Livewire fully enabled. The setup wizard is physically inaccessible.

---

## 🛑 Resetting the Application

If you need to restart the installation process, use the dedicated CLI command (forbidden in production):

```bash
php artisan app:danger-reset
```

*You can also reset the application from the **Admin Panel > Settings > Danger Zone**.*

---

## ✨ Core Features

| Feature | Description |
|---------|-------------|
| 🔌 **Modular API Architecture** | `/api/v1/` versioning with independent module structure |
| 🔐 **Secure API Keys** | Bcrypt-hashed keys with per-client rate limiting (60/min default) |
| 🌍 **CORS Control** | Per-client allowed origins list with strict validation |
| 📊 **Request Logging** | Complete audit trail with filtering and analytics |
| 🎨 **Filament v5 Admin** | Comprehensive dashboard for managing clients, keys, and logs |
| 🎯 **Promo API Module** | Multilingual banners (FR, EN, DE, AR), smart selection, tracking, version history |
| 📦 **Shared Hosting Ready** | No Node.js, minimal dependencies, SQLite/MySQL support |
| 📚 **Dynamic Documentation** | Markdown to HTML conversion with beautiful styling |

---

## 🎯 Quick Start

### Prerequisites

- **PHP 8.2+** with required extensions (bcmath, ctype, json, mbstring, openssl, pdo, tokenizer, xml)
- **MySQL 5.7+** or **SQLite** (for local development)
- **Composer** installed globally
- **Node.js** 18+ (optional, for frontend assets)

### Installation from GitHub (Recommended)

```bash
# 1. Clone from GitHub
git clone https://github.com/fay019/api-manager.git
cd api-manager

# 2. Install dependencies
composer install

# 3. Automated installation (handles everything in 9 steps!)
php artisan install
```

**That's it!** Your application is ready. ✨

### Start Development Server

```bash
php artisan serve
# Or use composer dev (includes queue + logs)
composer dev
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

## ⚙️ Installation System

### 🆘 Bootstrap Diagnostics (If Something Goes Wrong)

If you see a **500 error on first access**, visit one of these completely independent pages:

**Plain Text Diagnostic** (ultra-simple, works if HTML fails):
```
https://your-domain.com/diagnostic.php
```

**Full HTML Installation Page** (interactive with UI):
```
https://your-domain.com/install.php
```

Both pages will:
- ✅ **Show diagnostic information** (PHP version, extensions, permissions)
- ✅ **Create missing directories** automatically
- ✅ **Create .env file** from .env.example
- ✅ **Test filesystem permissions** and report issues
- ✅ **Detect Composer status** and run install if needed
- ✅ **Create SQLite database** and sessions table
- ✅ **Write detailed logs** to `storage/logs/install-diagnostic.log`
- ✅ **Work even if Laravel is completely broken!**

**Why these exist:**
- These files have **zero Laravel dependencies**
- They run **before Laravel bootstraps**
- They execute **even if .env is missing or directories don't exist**
- They **capture all PHP errors** and log them for debugging

### Web-Based Setup Wizard (Recommended)

**Automatic detection:** Visit any page → Setup Wizard appears if not installed

The wizard asks for:
1. **Site Information** + **Database Type** (SQLite/MySQL/PostgreSQL)
2. **Database Details** (if MySQL/PostgreSQL - skipped for SQLite)
3. **Confirmation** → Installation completes automatically

**Automated tasks:**
- ✅ Installs Composer dependencies
- ✅ Generates APP_KEY encryption key
- ✅ Creates all required directories
- ✅ Creates SQLite database file (if chosen)
- ✅ Updates .env with all settings
- ✅ Runs all database migrations
- ✅ Initializes modules
- ✅ Seeds initial data
- ✅ Creates admin user account

### One-Command Installation (CLI Alternative)

```bash
php artisan install
```

For servers without web access or CI/CD pipelines.

### Useful Commands

```bash
# Validate installation
php artisan validate:install

# Discover modules
php artisan discover:modules

# Step-by-step installation (for CI/CD)
php artisan install --step=requirements
php artisan install --step=database
php artisan install --step=modules

# Development
php artisan serve
composer dev        # Concurrent: server + queue + logs + Vite

# Testing
composer test
```

### Creating New Modules

The architecture is **fully modular**. Add features by creating modules:

```bash
mkdir -p app/Modules/YourModule/Models
mkdir -p app/Modules/YourModule/Migrations
mkdir -p app/Modules/YourModule/Http/Controllers

# Create YourModuleModule.php (extends BaseModule)
# Add migrations - auto-discovered!
# Add routes - auto-registered!

# Reinstall
php artisan install
```

See [MODULE_CREATION.md](./docs/MODULE_CREATION.md) for complete guide.

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

### Deploy from GitHub (Recommended)

```bash
# 1. Clone from GitHub
git clone https://github.com/fay019/api-manager.git
cd api-manager

# 2. Install dependencies (production mode)
composer install --no-dev --optimize-autoloader

# 3. Configure environment
cp .env.example .env
nano .env  # Set DB_CONNECTION=mysql, DB_HOST, etc.

# 4. Run installation
php artisan install --force --skip-seeds=dev

# 5. Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data .
```

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
- [ ] PHP 8.2+ with all extensions
- [ ] Database created and accessible
- [ ] .env configured for production
- [ ] php artisan install --force completed
- [ ] Storage permissions: chmod -R 755 storage bootstrap/cache
- [ ] Cron job configured (runs every minute)
- [ ] HTTPS enabled
- [ ] Admin password changed
- [ ] APP_DEBUG = false
- [ ] APP_ENV = production
```

### Docker Deployment

```dockerfile
FROM php:8.2-fpm

RUN docker-php-ext-install bcmath json pdo pdo_mysql

COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader
RUN php artisan install --force

RUN chown -R www-data:www-data /var/www/html/storage

EXPOSE 9000
```

---

## 🆘 Maintenance Page & Error Recovery

### Static Maintenance Page

If Laravel encounters an error during installation or operation, a **static maintenance page** is available:

**URL:** `https://your-domain.com/maintenance.html`

This page works **independently of Laravel** and provides:
- ✅ Confirmation that the domain is accessible
- ℹ️ Current server time and status
- 🔗 Link to installation wizard (`/setup`)
- 📋 Troubleshooting steps
- 💾 Common solutions (cache clear, permissions, etc.)
- 🔄 Auto-refreshes every 10 seconds to detect recovery

### Bootstrap-Level Protection

The application prepares itself automatically on first request:

**1. In `public/index.php` (BEFORE Laravel loads):**
- ✅ Creates required directories (`storage/`, `bootstrap/cache/`, etc.)
- ✅ Creates `.env` file from `.env.example` if missing
- Eliminates "No environment file" and "Permission denied" errors

**2. In middleware `EnsureDatabaseExists` (BEFORE session loading):**
- ✅ Creates SQLite file if chosen database type
- ✅ Creates sessions table automatically
- Prevents "Sessions table not found" errors

These protections run before Laravel's configuration loads, ensuring the application is ready for anything.

**Result:** Zero 500 errors on first deployment! ✅

### When Static Page Is Useful

- **First deployment**: Domain works but Laravel not yet configured
- **500 errors**: Shows that web server is fine, Laravel has issues
- **Installation failures**: Guides user through troubleshooting
- **Maintenance mode**: Can be manually activated if needed

See **[TROUBLESHOOTING.md](./docs/TROUBLESHOOTING.md)** for detailed error recovery procedures.

---

## 📚 Documentation

### Complete Documentation Suite

| Document | Purpose |
|----------|---------|
| [**TROUBLESHOOTING.md**](./docs/TROUBLESHOOTING.md) | 🆕 Common issues, debugging, error solutions |
| [**INSTALLATION.md**](./docs/INSTALLATION.md) | Complete installation guide, deployment, troubleshooting |
| [**MODULE_CREATION.md**](./docs/MODULE_CREATION.md) | Create custom modules (tutorial + examples) |
| [**API.md**](./docs/API.md) | Complete API reference with endpoints, authentication, examples |
| [**DATABASE.md**](./docs/DATABASE.md) | Database schema, relationships, migrations, queries |
| [**DEPLOYMENT.md**](./docs/DEPLOYMENT.md) | Production deployment guide for shared hosting |
| [**CLIENTS.md**](./docs/CLIENTS.md) | API client management and configuration |
| [**PROMOS.md**](./docs/PROMOS.md) | Promotional banners system |
| [**ANALYTICS.md**](./docs/ANALYTICS.md) | API analytics, monitoring, request logs |

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

### Getting Help

**For detailed troubleshooting guides, see:** [**TROUBLESHOOTING.md**](./docs/TROUBLESHOOTING.md)

That document covers:
- ✅ 500 errors and Laravel crashes
- ✅ Database connection issues
- ✅ Permission problems
- ✅ Installation failures
- ✅ API issues (401, 429, etc.)
- ✅ Server configuration (Nginx, Apache)
- ✅ Debugging with logs and commands

### Quick Common Issues

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

**Domain works but getting 500 error?**
→ Visit `https://your-domain.com/maintenance.html` for guided troubleshooting

---

## 📝 License

Private - Internal use only

---

## 🐛 Debugging & Troubleshooting

### Enhanced Error Pages

The application includes **custom error pages** that provide clear information when something goes wrong:

| Error Code | Page | What It Shows |
|-----------|------|---------------|
| **500** | Server Error | Recent logs when `APP_DEBUG=true` |
| **404** | Not Found | Page doesn't exist |
| **403** | Forbidden | Permission denied |
| **401** | Unauthorized | Authentication required |
| **419** | Session Expired | CSRF token expired |
| **503** | Service Unavailable | Application in maintenance |

### Enabling Debug Mode

To see detailed error information and logs in the browser:

```bash
# In .env file
APP_DEBUG=true
```

**When enabled:**
- ✅ Error pages display the last 20 log entries
- ✅ Shows exception class, message, file, and line number
- ✅ Displays request URL, method, and user information
- ✅ Links to the complete log file

**Important:** Disable in production:
```bash
# In .env file
APP_DEBUG=false
```

### Viewing Logs

**Real-time logs:**
```bash
tail -f storage/logs/laravel.log
```

**Last 50 lines:**
```bash
tail -50 storage/logs/laravel.log
```

**Search for errors:**
```bash
grep -i "error" storage/logs/laravel.log
grep -i "exception" storage/logs/laravel.log
```

### Common Issues

| Issue | Solution |
|-------|----------|
| 500 error on first visit | Enable APP_DEBUG=true to see details in error page |
| Logs not showing | Check file permissions: `chmod 666 storage/logs/laravel.log` |
| Page keeps redirecting | Check if already installed: `storage/app/installed.lock` exists |
| Database connection error | Verify DB_* values in .env or check `/admin/manage-app-settings` |

---

## 🙋 Need Help?

1. **Enable Debug Mode** - Set `APP_DEBUG=true` in .env to see detailed errors
2. **Check Error Pages** - Visit the page that errors to see logs in browser
3. **Review Application Logs** - `tail -f storage/logs/laravel.log`
4. **Check Documentation** - See relevant documentation files above
5. **Check Admin Panel** - Review detailed error messages in `/admin`
6. **Contact Administrator** - Reach out for production support

---

---

## 🔗 Quick Links

- **GitHub Repository**: [fay019/api-manager](https://github.com/fay019/api-manager)
- **Clone**: `git clone https://github.com/fay019/api-manager.git`
- **Issues & Bugs**: [GitHub Issues](https://github.com/fay019/api-manager/issues)
- **Start Installation**: `php artisan install`

---

## 📊 Project Stats

| Metric | Value |
|--------|-------|
| **Version** | 1.0.0 |
| **Framework** | Laravel 12 |
| **Admin Panel** | Filament v5 |
| **PHP Minimum** | 8.2 |
| **Database** | MySQL 5.7+ / SQLite |
| **Installation Steps** | 9 (automated) |
| **Installation Time** | < 2 minutes |
| **Modules** | Promo (built-in) + Custom |
| **Commands** | 50+ Artisan commands |

---

**Last Updated:** 2026-01-25
**Installation System**: v1.1.0 (Modular, Idempotent, Auto-Discovery)
**Status**: ✅ Production Ready
