# 🚀 Deployment Guide - Shared Hosting

This guide covers deploying the API Hub to shared hosting (e.g., api.moussouni.dev).

## ✅ Prerequisites Checklist

- [ ] PHP 8.4+ with required extensions
- [ ] MySQL 5.7+ OR SQLite support
- [ ] SSH/Terminal access
- [ ] Composer installed globally
- [ ] HTTPS certificate (Let's Encrypt via hosting provider)
- [ ] Cron job support
- [ ] Git or FTP access

### Required PHP Extensions

Your hosting must have:
- `bcmath` - Hashing
- `ctype` - String validation
- `json` - JSON handling
- `mbstring` - String manipulation
- `openssl` - Encryption
- `pdo` - Database abstraction
- `pdo_mysql` - MySQL driver (if using MySQL)
- `tokenizer` - Code tokenization
- `xml` - XML parsing

**Check on shared hosting:**
```bash
php -m | grep -E "bcmath|ctype|json|mbstring|openssl|pdo|tokenizer|xml"
```

---

## 📁 Step 1: Upload Project

### Option A: Git (Recommended)

```bash
# Local
git init
git add .
git commit -m "Initial API hub setup"
git remote add origin https://github.com/yourname/api-manager.git
git push -u origin main

# On server
ssh user@api.moussouni.dev
cd /path/to/api-manager
git clone https://github.com/yourname/api-manager.git .
```

### Option B: FTP/SFTP

1. Download project locally
2. Use FileZilla or hosting control panel
3. Upload to document root or subdirectory
4. Ensure file permissions preserved

---

## 🔧 Step 2: SSH Access & Environment

```bash
ssh user@api.moussouni.dev
cd /path/to/api-manager

# Create .env from template
cp .env.example .env
nano .env  # Or use hosting control panel editor
```

### Configure .env for Shared Hosting

```env
APP_NAME="API Manager"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxx  # Already generated, should be there

APP_URL=https://api.moussouni.dev

# Database - Change based on hosting provider
DB_CONNECTION=mysql
DB_HOST=localhost  # or hosting provider hostname
DB_PORT=3306
DB_DATABASE=yourusername_apidb
DB_USERNAME=yourusername_user
DB_PASSWORD=strong_password_here

# Cache & Queue for shared hosting
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info  # Don't use debug in production

# API Settings
API_DEFAULT_RATE_LIMIT=60
API_UNAUTH_RATE_LIMIT=10
PROMO_CACHE_TTL=60
API_LOG_RETENTION_DAYS=90
PROMO_EVENT_RETENTION_DAYS=180

# Optional: Use SQLite instead of MySQL
# DB_CONNECTION=sqlite
# DB_DATABASE=/path/to/database.sqlite
```

---

## 📦 Step 3: Install Dependencies

```bash
# Install composer dependencies (production only)
composer install --no-dev --optimize-autoloader

# Generate application key (if not already done)
php artisan key:generate

# Or verify it exists
grep APP_KEY .env | grep -v "^#"
```

**Expected output:**
```
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
```

---

## 🗄️ Step 4: Database Setup

### Create Database via Hosting Control Panel

1. Log into cPanel/Plesk
2. Create MySQL database
3. Create database user with password
4. Grant all privileges to user on database
5. Copy credentials to `.env`

### Run Migrations

```bash
# Create all tables
php artisan migrate --force

# Seed admin user
php artisan db:seed --force

# Verify
php artisan tinker
>>> User::where('is_admin', true)->first();
```

**Expected output:**
```
=> App\Models\User {#xxxx
     id: 1,
     name: "Admin",
     email: "admin@moussouni.dev",
     is_admin: true,
   }
```

---

## 🌐 Step 5: Configure Web Server

### Document Root (Important!)

The web server document root **must point to `/path/to/api-manager/public`**, NOT the project root.

#### cPanel File Manager

1. Go to **Addon Domains** or **Domains**
2. For `api.moussouni.dev`:
   - Set **Document Root** to `/path/to/api-manager/public`
3. **Save**

#### Manual .htaccess (if not using public root)

Create `/path/to/api-manager/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## 🔐 Step 6: File Permissions

```bash
# SSH into server
cd /path/to/api-manager

# Set directory permissions (755 = rwxr-xr-x)
chmod -R 755 storage bootstrap/cache

# Set owner to web server user
chown -R www-data:www-data storage bootstrap/cache

# Verify
ls -la storage/ | head -5
# drwxr-xr-x  www-data www-data
```

**On cPanel:** File Manager automatically sets permissions correctly.

---

## ⚡ Step 7: Production Optimization

```bash
# Clear any cached files
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cache for production (these must be redone on each deploy)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

**Verify caching worked:**
```bash
ls -la bootstrap/cache/
# Should show config.php, routes*.php, views.php, events.php
```

---

## ⏰ Step 8: Set Up Cron Jobs

Scheduled tasks must run every minute. Add to hosting control panel:

### cPanel Cron Jobs

1. Log into cPanel
2. Go to **Cron Jobs**
3. Set **Common Settings** to **Once per Minute** if available, or manually add:

```
* * * * * cd /path/to/api-manager && php artisan schedule:run >> /dev/null 2>&1
```

### Cron Output

Check cron logs:
```bash
# View cron output
tail /path/to/api-manager/storage/logs/laravel.log | grep "schedule"
```

### What Gets Scheduled

- **Daily at 2 AM UTC**: Prune API request logs (older than 90 days)
- **Daily at 3 AM UTC**: Prune promo events (older than 180 days)

Edit `bootstrap/app.php` to adjust times:
```php
$schedule->command('api:prune-logs')->daily();
$schedule->command('promo:prune-events')->dailyAt('03:00');
```

---

## 🔒 Step 9: HTTPS & Redirects

### Force HTTPS

Create or edit `/public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### Let's Encrypt on Shared Hosting

1. cPanel: **AutoSSL** - Usually automatic
2. Plesk: **Let's Encrypt** extension
3. Manual: Use hosting provider's SSL manager

**Verify HTTPS:**
```bash
curl -I https://api.moussouni.dev
# HTTP/2 200
```

---

## ✅ Step 10: Test Deployment

### Health Check

```bash
curl https://api.moussouni.dev/api/v1/health

# Expected response:
# {"success":true,"data":{"status":"ok","timestamp":"2026-01-18T..."}
```

### Admin Login

1. Visit: `https://api.moussouni.dev/admin`
2. Login: `admin@moussouni.dev` / `password` (change immediately!)
3. Create test API client with key
4. Test API endpoints

### Test API Endpoint

```bash
# Get banner (no auth)
curl https://api.moussouni.dev/api/v1/promo/banner.json

# With API key
curl -H "X-API-KEY: apk_xxxxx" https://api.moussouni.dev/api/v1/promo/banner.json
```

---

## 🛡️ Step 11: Security Hardening

### Change Admin Password

```bash
# SSH into server
php artisan tinker

# In tinker shell
>>> $user = User::find(1);
>>> $user->update(['password' => bcrypt('new-strong-password-here')]);
>>> exit;
```

### Protect Sensitive Files

Create `/public/.htaccess` or `/path/to/.htaccess`:
```apache
# Prevent access to sensitive files
<FilesMatch "^\.env|composer\.(json|lock)|artisan|\.git">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
```

### Backup .env

```bash
# Do NOT commit .env to git
# Create a backup outside web root
cp .env /home/user/backups/api-manager.env

# Set restrictive permissions
chmod 600 /home/user/backups/api-manager.env
```

---

## 📊 Step 12: Monitoring & Logs

### Check Application Logs

```bash
# View recent logs
tail -f storage/logs/laravel.log

# Search for errors
grep "ERROR" storage/logs/laravel.log

# View by date
ls -la storage/logs/
```

### Monitor Disk Space

```bash
# Check disk usage
du -sh .
du -sh storage/

# Monitor storage growth
df -h /path/to/api-manager
```

### Database Size

```bash
mysql -u user -p -h localhost
> SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = 'yourusername_apidb' ORDER BY size_mb DESC;
```

---

## 📋 Deployment Checklist

Before going live:

- [ ] PHP version 8.4+ confirmed
- [ ] All required extensions present
- [ ] Database created and accessible
- [ ] .env configured with production values
- [ ] Migrations run successfully
- [ ] Admin user created (password changed)
- [ ] Document root points to `/public`
- [ ] HTTPS working and redirects configured
- [ ] File permissions set (755 on storage)
- [ ] Cron job configured (runs every minute)
- [ ] Health endpoint returns 200
- [ ] Admin panel accessible and working
- [ ] API endpoints tested with curl
- [ ] Logs accessible and clean
- [ ] Backups strategy in place
- [ ] APP_DEBUG = false in production
- [ ] APP_ENV = production

---

## ⚠️ Troubleshooting

### 500 Error on Admin Panel

**Problem:** White screen or 500 error

**Solution:**
```bash
# Check logs
tail storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear

# Verify permissions
ls -la storage/
chmod -R 755 storage bootstrap/cache
```

### API Endpoints Return 404

**Problem:** Routes not found

**Solution:**
```bash
# Verify routes cached
php artisan route:list | grep "v1"

# Clear and recache
php artisan route:clear
php artisan route:cache
```

### Database Connection Error

**Problem:** "SQLSTATE[HY000]: General error"

**Solution:**
```bash
# Verify .env credentials
grep DB_ .env

# Test MySQL connection
mysql -u user -p -h host database_name

# Verify migrations ran
php artisan migrate:status
```

### Cron Jobs Not Running

**Problem:** Logs not pruning, schedule:run not executing

**Solution:**
```bash
# Add debug cron that logs output
* * * * * cd /path/to/api-manager && php artisan schedule:run >> storage/logs/cron.log 2>&1

# Check cron log
tail storage/logs/cron.log

# Manually test
php artisan schedule:work  # Will show what runs
```

### HTTPS Redirect Loop

**Problem:** Site redirects infinitely

**Solution:**
1. Remove HTTPS redirect from both `.htaccess` files (if multiple exist)
2. Let hosting provider handle SSL redirect
3. Or use only one redirect in `public/.htaccess`

---

## 🔄 Updates & Maintenance

### Pull Latest Changes

```bash
cd /path/to/api-manager

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations if needed
php artisan migrate --force

# Clear and recache
php artisan config:clear && php artisan config:cache
php artisan route:clear && php artisan route:cache
```

### Regular Maintenance

**Daily:**
- Review storage space usage
- Check application logs for errors

**Weekly:**
- Verify cron jobs ran (check logs)
- Test API endpoints
- Monitor database size

**Monthly:**
- Review API request patterns
- Check failed request rates
- Update admin password

**Quarterly:**
- Update Laravel and packages
- Review security logs
- Audit API clients

---

## 📈 Scaling Considerations

### When to Upgrade

- **Storage**: If `storage/` exceeds 1GB, increase hosting plan or prune more frequently
- **Database**: If queries slow down, optimize indexes or upgrade database plan
- **Traffic**: If API consistently near rate limit, increase limits or upgrade hosting

### Migration to Dedicated Server

When moving from shared to dedicated hosting:

1. Provision new server with same PHP/MySQL versions
2. Copy project code and database
3. Update DNS `api.moussouni.dev` to point to new server IP
4. Verify all endpoints working
5. Keep shared hosting as backup for 48 hours

---

## 💬 Support

For issues:

1. Check `storage/logs/laravel.log`
2. Verify `.env` configuration
3. Test endpoints with `curl`
4. Review deployment checklist above
5. Contact hosting provider if infrastructure issue

---

## 📚 Resources

- [Laravel Deployment](https://laravel.com/docs/12/deployment)
- [Shared Hosting Optimization](https://laravel.com/docs/12/configuration#optimization)
- [Database Backups](https://laravel.com/docs/12/database#databases)
