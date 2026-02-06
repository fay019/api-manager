# 🛠️ API Manager - Developer Guide

## ⚙️ Application Lifecycle: Binary State Switch

The application operates in two mutually exclusive modes based on the presence of `storage/app/installed.lock`.

### 1. PRE-INSTALL Mode (Setup Wizard)
*   **Trigger**: `installed.lock` is missing.
*   **Behavior**: 
    *   Only `routes/setup.php` is loaded.
    *   Business routes (`web.php`, `api.php`, Filament) are **not registered**.
    *   **Stateless architecture**: No standard Laravel sessions or encrypted cookies are used. This prevents `DECRYPT_FAILED` errors during installation when the `APP_KEY` is generated or changed.
    *   **Token Identification**: Identification is handled via an unencrypted cookie `api_manager_setup_token` and a fallback URL parameter `?setup_token=...`.
    *   Progress is stored in temporary JSON files: `storage/app/setup/progress_[sha256(token)].json`.
*   **Security**: Uses a custom `_setup_token` for CSRF protection instead of standard Laravel CSRF.

### 2. POST-INSTALL Mode (Standard Laravel)
*   **Trigger**: `installed.lock` exists.
*   **Behavior**:
    *   Normal Laravel lifecycle.
    *   Standard sessions and encrypted cookies are active.
    *   Setup routes are physically inaccessible (not loaded).
    *   Filament and Livewire are fully enabled.

---

## 🛑 Resetting the Application

If you need to restart the installation process or reset the environment, use the dedicated CLI command or the Admin UI.

### CLI: `php artisan app:danger-reset`
**What it does (DESTRUCTIVE):**
1.  Removes `storage/app/installed.lock`.
2.  Deletes the SQLite database (`database/database.sqlite`).
3.  Cleans `storage/app/setup/` progress files.
4.  Backs up `.env` and clears application caches.
5.  Clears all user sessions.
6.  Truncates logs.

**Usage:**
```bash
php artisan app:danger-reset
```
*Follow the interactive prompts. You will be asked to type "CONFIRMER" to proceed.*

### Admin UI: Settings > Danger Zone
The same reset process can be triggered from the Admin Panel by typing "Confirmer" in the modal.

> ⚠️ **WARNING**: Resetting is strictly forbidden in `production` environment.

---

## 🛠️ Internal Maintenance

### Documentation Scanner
The documentation scanner automatically indexes Markdown files in the project.
*   **Paths scanned**: Project root and `docs/` (recursive).
*   **Admin Access**: System > Documentation Settings.
*   **Status**: New files are hidden by default for security.

---

## 🗄️ Database Configuration

### Database Selection During Installation
The installation wizard (`/setup/database`) allows you to choose between:
*   **SQLite** - Default option, file-based, no server required
*   **MySQL** - Production-ready, requires MySQL server
*   **PostgreSQL** - Alternative production option

You can select your preferred database during the setup wizard. The configuration is stored in `.env`.

### Development Environment
*   **Recommended**: SQLite (`database/database.sqlite`)
*   **Setup**: Choose during installation wizard
*   **Location**: `database/` directory (must be writable)

### Production Environment (api.moussouni.dev)
*   **Database**: MySQL
*   **Configuration**: `.env` file
*   **Credentials**: Set in `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`
*   **Migration**: As of February 2026, production was migrated from SQLite to MySQL for better performance and scalability

**Data Recovery (if needed):**
1. Backup the MySQL database first
2. Export data from `database/database.sqlite.backup-*` (if available)
3. Use tinker to re-import data following the JSON export/import process

---

## 🧪 Troubleshooting Installation

### DECRYPT_FAILED Errors
This usually happens if you have old cookies from a previous installation with a different `APP_KEY`.
**Solution**: Clear your browser cookies for the domain or use an Incognito window.

### 404 on Livewire/Filament
If you see 404s on `/livewire/*` or `/admin`, it means the application is likely stuck in PRE-INSTALL mode or the `installed.lock` is not readable.
**Solution**: Complete the installation wizard or check filesystem permissions.

### SQLite Permissions
Ensure the `database/` directory is writable by the web server (www-data/nginx). SQLite needs to create `-wal` and `-shm` temporary files in that folder during operations.

---

## 📊 API Request Logs

The **API Request Logs** section in the admin panel records every request made to the API. This feature helps you monitor and debug API usage.

### What Gets Logged

Every API request captures the following information:

| Field | Source | Purpose |
|-------|--------|---------|
| **Timestamp** | Request time | When the request was made |
| **HTTP Method** | HTTP method | GET, POST, PUT, DELETE, or PATCH |
| **Endpoint** | Request path | The API endpoint path |
| **Status Code** | Response status | HTTP response code (200, 404, 500, etc.) |
| **Duration (ms)** | Response time | How long the request took to process |
| **Domain** | Origin/Referer header or X-Site-Domain | The website domain calling the API (e.g., `moussouni.dev`) |
| **Site Name** | X-Site-Name header | Name of the client site (e.g., `Portfolio`) |
| **Page Path** | X-Site-Page header | Exact page/path that triggered the request (e.g., `/blog/article`) |
| **Full URL** | X-Site-Full-Url header | Complete URL of the calling page |
| **Client Request Time** | X-Request-Time header | Timestamp when the client made the request |
| **Client Browser** | X-User-Agent header | Client's browser/user agent information |
| **IP Address** | Remote IP | The server's IP address |
| **Hostname** | Reverse DNS lookup | The hostname of the server (e.g., `server-12345.likuid.com`) |
| **User Agent** | User-Agent header | Server's HTTP client information (hidden by default) |
| **Origin** | Origin header | HTTP Origin header (hidden by default) |
| **Referer** | Referer header | HTTP Referer header (details page only) |
| **API Client** | Request auth | Which API client made the request |
| **API Key** | Request auth | Which API key was used |

### Understanding Domain vs. Hostname

**Domain** (e.g., `moussouni.dev`):
- Extracted from the `Origin` or `Referer` HTTP header
- Shows the actual website that called the API
- More useful for identifying which site is making requests

**Hostname** (e.g., `server-12345.likuid.com`):
- Obtained via reverse DNS lookup of the source IP
- Shows the server's infrastructure hostname
- Useful for technical debugging and server identification

### How It Works

When a request comes in:
1. The `LogApiRequest` middleware captures the request details
2. It extracts the **domain** from the `Origin` header (or `Referer`, then `X-Site-Domain` as fallback)
3. It extracts **client details** from custom headers if provided:
   - `X-Site-Name` → Site name
   - `X-Site-Page` → Exact page path (e.g., `/blog/article`)
   - `X-Site-Full-Url` → Complete URL
   - `X-Request-Time` → Client's request timestamp
   - `X-User-Agent` → Client's browser info
4. It performs a reverse DNS lookup for the **hostname**
5. All information is logged to the `api_request_logs` table
6. You can view and filter the logs in the admin panel

### Custom Headers (Client Integration)

If your client sends these custom headers, you get much richer data:

```
X-Site-Domain: moussouni.dev
X-Site-Name: Portfolio
X-Site-Page: /blog/article
X-Site-Full-Url: https://moussouni.dev/blog
X-Request-Time: 1738939200
X-User-Agent: Mozilla/5.0...
X-Site-Lang: fr
```

These headers are typically sent by JavaScript/PHP clients that track analytics. They allow you to pinpoint exactly which page triggered each API request.

### Filtering and Searching

The logs table supports:
- **Search**: Find by domain, IP, endpoint, or other fields
- **Filters**: Filter by HTTP method, status code range, API client, or date range
- **Sort**: Click column headers to sort
- **Toggle columns**: Show/hide optional columns like User Agent and Origin

### Detail View

Click the **View** button on any log entry to see all captured information, including the full Origin and Referer headers.
