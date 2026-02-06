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
| **Domain** | Origin/Referer header | The website domain calling the API (e.g., `moussouni.dev`) |
| **IP Address** | Remote IP | The server's IP address |
| **Hostname** | Reverse DNS lookup | The hostname of the server (e.g., `server-12345.likuid.com`) |
| **User Agent** | User-Agent header | Browser/client information (hidden by default) |
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
2. It extracts the **domain** from the `Origin` header (or `Referer` as fallback)
3. It performs a reverse DNS lookup for the **hostname**
4. All information is logged to the `api_request_logs` table
5. You can view and filter the logs in the admin panel

### Filtering and Searching

The logs table supports:
- **Search**: Find by domain, IP, endpoint, or other fields
- **Filters**: Filter by HTTP method, status code range, API client, or date range
- **Sort**: Click column headers to sort
- **Toggle columns**: Show/hide optional columns like User Agent and Origin

### Detail View

Click the **View** button on any log entry to see all captured information, including the full Origin and Referer headers.
