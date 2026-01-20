# API Manager - Laravel 12 + Filament v5

A production-ready, modular API hub system for centralizing multiple APIs with secure API key authentication, CORS per-client control, comprehensive logging, and a Filament v5 admin panel on shared hosting.

## Core Features

✅ **Modular API Architecture** - `/api/v1/` versioning with independent module structure
✅ **Secure API Keys** - Bcrypt-hashed keys with per-client rate limiting (60/min default)
✅ **CORS Control** - Per-client allowed origins list with strict validation
✅ **Request Logging** - Complete audit trail with filtering and analytics
✅ **Filament v5 Admin** - Comprehensive dashboard for managing clients, keys, and logs
✅ **Promo API Module** - Smart banner selection, event tracking, version history
✅ **Shared Hosting Ready** - No Node.js, minimal dependencies, SQLite/MySQL support

## Quick Start

```bash
# Install
composer install
cp .env.example .env
php artisan key:generate

# Setup
php artisan migrate
php artisan db:seed

# Run
php artisan serve
# Admin: http://localhost:8000/admin
# Email: admin@moussouni.dev | Password: password
```

## API Endpoints

### Health Check
```bash
GET /api/v1/health
```

### Get Active Promo
```bash
GET /api/v1/promo/banner.json
Header: X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Track Promo Event
```bash
POST /api/v1/promo/event
Header: X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Body: {
  "promo_id": 1,
  "event_type": "impression",
  "session_id": "abc123",
  "url": "https://example.com"
}
```

## Creating API Clients

1. Login to `/admin` (Email: admin@moussouni.dev)
2. Go to **API Clients** → **Create**
3. Set name, is_active status, allowed origins, rate limit
4. Save and generate API key
5. Copy raw key immediately (won't be shown again)

## Project Structure

```
app/
├── Enums/                  # Enum definitions
├── Http/
│   ├── Controllers/Api/
│   ├── Middleware/         # API key auth, CORS, rate limiting, logging
│   └── Responses/
├── Models/                 # Database models
├── Modules/Promo/          # Promo API module
├── Services/               # Business logic
├── Observers/              # Auto version snapshots
└── Providers/
config/
├── api.php                 # API configuration
database/
├── migrations/             # 8 tables (users, api_clients, api_keys, documentation_settings, etc.)
└── seeders/
routes/
├── api/v1.php              # API v1 routes
└── api/modules/promo.php   # Promo endpoints
```

## Deployment

See [DEPLOYMENT.md](docs/DEPLOYMENT.md) for shared hosting setup, cron jobs, file permissions, and security checklist.

## Documentation

- [API Documentation](./docs/API.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Database Schema](./docs/DATABASE.md)

## Adding New Modules

1. Create `app/Modules/NewModule/Http/Controllers/`
2. Implement controllers with standard API response format
3. Create `routes/api/modules/newmodule.php`
4. Include in `routes/api/v1.php`

All modules automatically inherit authentication, CORS, rate limiting, and logging.

## Security

- API keys hashed with bcrypt, never logged
- IP/User-Agent hashed for privacy
- CORS enforced per client
- Rate limiting with configurable limits
- Input validation on all endpoints

## License

Private - Internal use only
