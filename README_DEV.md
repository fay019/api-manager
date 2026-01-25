# 🛠️ API Manager - Developer Guide

## ⚙️ Application Lifecycle: Binary State Switch

The application operates in two mutually exclusive modes based on the presence of `storage/app/installed.lock`.

### 1. PRE-INSTALL Mode (Setup Wizard)
*   **Trigger**: `installed.lock` is missing.
*   **Behavior**: 
    *   Only `routes/setup.php` is loaded.
    *   Business routes (`web.php`, `api.php`, Filament) are **not registered**.
    *   Stateless architecture: No standard Laravel sessions or encrypted cookies are used.
    *   Progress is stored in temporary JSON files: `storage/app/setup/progress_[token].json`.
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

If you need to restart the installation process or reset the environment, use the dedicated CLI command.

### `php artisan app:danger-reset`

**What it does (DESTRUCTIVE):**
1.  Removes `storage/app/installed.lock`.
2.  Deletes the SQLite database (`database/database.sqlite`).
3.  Cleans `storage/app/setup/` progress files.
4.  Backs up `.env` and clears application caches.
5.  Truncates logs.

**Usage:**
```bash
php artisan app:danger-reset
```
*Follow the interactive prompts. You will be asked to type "CONFIRMER" to proceed.*

> ⚠️ **WARNING**: This command is forbidden in `production` environment.

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
