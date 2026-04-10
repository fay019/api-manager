# 🔐 Public Authentication & User Management

Complete guide for the public authentication system, user roles, and user management in the admin panel.

## Table of Contents

- [Overview](#overview)
- [User Roles](#user-roles)
- [Public Login](#public-login)
- [Profile Management](#profile-management)
- [Admin User Management](#admin-user-management)
- [Security](#security)
- [Multilingual Support](#multilingual-support)
- [Troubleshooting](#troubleshooting)

---

## Overview

The application features a **complete public authentication system** that is **separate from the Filament admin panel**:

- 👥 **Public Users** - Can login and manage their own profiles
- 👨‍💼 **Admin Users** - Have access to admin panel plus all public features
- 🔐 **Password Security** - All passwords hashed with bcrypt
- 🌐 **Multilingual** - Login and profile pages available in FR, EN, DE
- 🌙 **Dark Mode** - Full dark mode support on all authentication pages

### Architecture

```
┌─────────────────────────────────────────┐
│       Authentication System v2.0        │
├─────────────────────────────────────────┤
│                                         │
│  Public Routes (No Auth Required)       │
│  ├── GET  /login          Login page    │
│  └── POST /login          Authenticate  │
│                                         │
│  Protected Routes (Login Required)      │
│  ├── GET  /profile        Profile page  │
│  ├── POST /profile        Edit profile  │
│  └── POST /logout         Logout        │
│                                         │
│  Admin Routes (Admin Only)              │
│  └── /admin/resources/users  User CRUD  │
│                                         │
└─────────────────────────────────────────┘
```

---

## User Roles

### Public User

A user account without admin privileges.

**Permissions:**
- ✅ Login to public login page (`/login`)
- ✅ Access profile page (`/profile`)
- ✅ Edit own name and email
- ✅ Change own password
- ❌ Cannot access admin panel (`/admin`)

**Database Field:** `is_admin = false`

### Admin User

A user account with full admin privileges.

**Permissions:**
- ✅ Login to public login page (`/login`)
- ✅ Automatically redirected to admin panel (`/admin`)
- ✅ Full access to admin panel (all resources, settings)
- ✅ Manage all users (create, edit, delete)
- ✅ View all API clients, keys, logs
- ✅ Configure application settings

**Database Field:** `is_admin = true`

---

## Public Login

### Login Page

**URL:** `GET /login`

The login page provides a simple, secure form for users to authenticate.

#### Features

- 📱 **Responsive Design** - Works on mobile and desktop
- 🌙 **Dark Mode** - Respects browser and user theme preference
- 🌍 **Multilingual** - Available in FR, EN, DE
- ⚠️ **Error Messages** - Clear feedback on failed attempts
- ✅ **Form Validation** - Client-side and server-side validation

#### Form Fields

```
┌─────────────────────────────────────┐
│         Login to API Manager        │
├─────────────────────────────────────┤
│                                     │
│ Email Address:                      │
│ [_____________________________]     │
│                                     │
│ Password:                           │
│ [_____________________________]     │
│                                     │
│ [ ] Remember me                     │
│                                     │
│          [Login Button]             │
│                                     │
└─────────────────────────────────────┘
```

### Authentication Process

**Step 1: User submits form**
```bash
POST /login
Content-Type: application/x-www-form-urlencoded

email=user@example.com&password=secret123
```

**Step 2: Application validates**
- Email exists in database
- Password matches hash
- Account is not deleted

**Step 3: Success → Role-based redirect**

```
If is_admin = true:
  → Redirect to GET /admin
  
If is_admin = false:
  → Redirect to GET /profile
```

**Step 4: Error → Show form with message**

```
Invalid email or password. Please try again.
```

### Form Validation

**Client-Side (Browser):**
- Email: Required, must be valid email format
- Password: Required, minimum 8 characters

**Server-Side (Laravel):**
- Email: Required, email format, must exist in users table
- Password: Required, must match bcrypt hash

### Session Management

After successful login:
- User session created with encrypted cookie
- `auth()->user()` available in controllers/views
- Session expires after 2 hours of inactivity (configurable)
- Session can be cleared by logout

### Failed Login Attempts

Currently, the application:
- Shows generic error message
- Does not lock accounts after multiple failures
- Logs failed attempts in application logs

**Note:** For production, consider adding:
- Rate limiting per IP (e.g., 5 attempts per 15 minutes)
- Account lockout after N failed attempts
- Email notification on suspicious activity

---

## Profile Management

### Profile Page

**URL:** `GET /profile` (requires login)

Protected page where logged-in users can view and edit their profile.

#### Features

- 📝 **Edit Name & Email** - Update personal information
- 🔑 **Change Password** - Secure password update
- 📧 **Email Verification** - Optional email verification on change
- ✅ **Form Validation** - All fields validated
- 💾 **Persistent** - All changes saved immediately
- 📱 **Responsive** - Works on all devices
- 🌙 **Dark Mode** - Full dark mode support

### Edit Profile Section

Allows users to update their basic information:

```
┌─────────────────────────────────────────┐
│            Edit Profile                 │
├─────────────────────────────────────────┤
│                                         │
│ Full Name:                              │
│ [John Doe_____________________]         │
│                                         │
│ Email Address:                          │
│ [john@example.com____________]         │
│                                         │
│           [Save Changes]                │
│                                         │
│ ✅ Changes saved successfully!          │
│                                         │
└─────────────────────────────────────────┘
```

**Fields:**
- `name` (Required, minimum 2 characters)
- `email` (Required, valid email format, must be unique)

**Validation:**
- Name: `required|string|min:2|max:255`
- Email: `required|email|unique:users,email,{id}|max:255`

**Success:** Form clears, success message shown, database updated

**Error:** Field errors displayed inline, existing data retained

### Change Password Section

Allows users to securely update their password:

```
┌─────────────────────────────────────────┐
│         Change Password                 │
├─────────────────────────────────────────┤
│                                         │
│ Current Password:                       │
│ [________________________]              │
│                                         │
│ New Password:                           │
│ [________________________]              │
│                                         │
│ Confirm Password:                       │
│ [________________________]              │
│                                         │
│           [Update Password]             │
│                                         │
│ ✅ Password updated successfully!       │
│                                         │
└─────────────────────────────────────────┘
```

**Fields:**
- `current_password` (Required, must match user's password)
- `new_password` (Required, minimum 8 characters, different from current)
- `password_confirmation` (Required, must match new_password)

**Validation:**
- Current password: `required|current_password`
- New password: `required|string|min:8|confirmed|different:current_password`

**Security:**
- Current password verified before allowing change
- New password hashed with bcrypt before storage
- Old password immediately invalidated
- User not logged out (session continues)

**Success:** Password hash updated in database, success message shown

**Error:** Validation errors displayed inline, password not changed

---

## Admin User Management

### User Resource

**URL:** `/admin/resources/users`

Only accessible to admin users. Provides full CRUD operations for all users.

**Features:**
- ✅ **Create Users** - Add new user accounts
- ✅ **Read Users** - List all users with search/filter
- ✅ **Update Users** - Edit user details and password
- ✅ **Delete Users** - Remove user accounts
- ✅ **Search** - Find users by name or email
- ✅ **Pagination** - View 10 users per page
- 🌐 **Multilingual** - Resource labels in FR, EN, DE
- 🌙 **Dark Mode** - Full Filament dark mode support

### Create User

**Action:** Click "Create" button in user list

```
┌────────────────────────────────────────┐
│          Create New User               │
├────────────────────────────────────────┤
│                                        │
│ Name:                                  │
│ [_____________________________]        │
│                                        │
│ Email:                                 │
│ [_____________________________]        │
│                                        │
│ Password:                              │
│ [_____________________________]        │
│                                        │
│ Admin:                                 │
│ [No ▼] ← Toggle to make admin          │
│                                        │
│ [Cancel]                [Create]       │
│                                        │
└────────────────────────────────────────┘
```

**Required Fields:**
- `name` - Full name (required, 2-255 characters)
- `email` - Email address (required, valid email, unique)
- `password` - Initial password (required, min 8 chars, auto-hashed)
- `is_admin` - Admin role toggle (optional, default false)

**After Creation:**
- User receives initial password (communicate securely to user)
- User can login with email and password
- User can change password on first login
- Admin user gets `/admin` access

### Read/List Users

**Action:** Navigate to `/admin/resources/users`

Shows a table of all users with:
- Name
- Email
- Admin status (badge)
- Created date
- Last updated date
- Action buttons (Edit, Delete)

**Search:** Enter name or email to filter users

**Pagination:** 10 users per page, navigate with arrows

### Edit User

**Action:** Click "Edit" button next to user

```
┌────────────────────────────────────────┐
│          Edit User (John Doe)          │
├────────────────────────────────────────┤
│                                        │
│ Name:                                  │
│ [John Doe_____________________]        │
│                                        │
│ Email:                                 │
│ [john@example.com____________]        │
│                                        │
│ Change Password:                       │
│ [_____ New password (optional)____]   │
│ [Password is only updated if filled]   │
│                                        │
│ Admin:                                 │
│ [Yes ▼] ← Toggle to change role        │
│                                        │
│ Created: Jan 15, 2026 at 10:30 AM      │
│ Updated: Mar 20, 2026 at 02:15 PM      │
│                                        │
│ [Cancel]                [Update]       │
│                                        │
└────────────────────────────────────────┘
```

**Editable Fields:**
- `name` - Full name (required, 2-255 characters)
- `email` - Email address (required, valid email, unique except self)
- `password` - Change password (optional, only if filled, min 8 chars)
- `is_admin` - Admin role toggle

**Important:**
- Password field is optional
- If left empty, password is NOT changed
- If filled, it MUST be at least 8 characters
- Password is automatically hashed with bcrypt before saving
- Email uniqueness checked (except for this user's current email)

**After Update:**
- User data immediately updated in database
- Changes take effect on user's next page load
- User session continues (not forced to logout)

### Delete User

**Action:** Click "Delete" button next to user

```
┌────────────────────────────────────────┐
│  Are you sure you want to delete this? │
│                                        │
│  This action cannot be undone.         │
│                                        │
│  User: john@example.com                │
│                                        │
│  [Cancel]               [Delete]       │
│                                        │
└────────────────────────────────────────┘
```

**Warning:** Deletion is permanent and cannot be undone

**Effects:**
- User account completely removed from database
- User can no longer login
- User's API keys become inaccessible (if future feature added)
- User cannot recover their account

---

## Security

### Password Hashing

All passwords are hashed using **bcrypt** with:
- **Algorithm:** bcrypt (PHP's `password_hash()`)
- **Cost:** 12 (default)
- **Verification:** `password_verify()` or Laravel's `Hash::check()`

**Never Stored:**
- Plain text passwords
- Encrypted passwords
- Passwords in logs
- Passwords in error messages

### Session Security

Laravel sessions are:
- **Encrypted** - AES-256-CBC encryption
- **Signed** - HMAC-SHA256 signature for tampering detection
- **Secure Cookie** - `secure` flag set (HTTPS only in production)
- **HttpOnly** - Prevents JavaScript access to session cookie
- **SameSite=strict** - Prevents CSRF attacks

**Session Expiration:**
- Default: 2 hours of inactivity
- Configurable in `config/session.php`

### CSRF Protection

Every POST request (login, profile update) includes:
- `@csrf` token in form (Blade)
- Token verified by middleware before processing
- Prevents cross-site request forgery attacks

### Rate Limiting (Optional)

Consider adding in production:

```php
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:5,15');  // 5 attempts per 15 minutes per IP
```

---

## Multilingual Support

All authentication pages are **fully internationalized** in French, English, and German.

### Translation Keys

**Login Page** (`auth.login.*`):
```
auth.login.title              → Page title
auth.login.email              → Email label
auth.login.password           → Password label
auth.login.remember           → Remember me checkbox
auth.login.button             → Submit button text
auth.login.no_account         → Sign up link text
```

**Profile Page** (`auth.profile.*`):
```
auth.profile.title                  → Page title
auth.profile.edit_profile           → Section heading
auth.profile.name                   → Name field label
auth.profile.email                  → Email field label
auth.profile.change_password        → Section heading
auth.profile.current_password       → Field label
auth.profile.new_password           → Field label
auth.profile.confirm_password       → Field label
auth.profile.save                   → Save button text
```

**Validation Messages** (`auth.validation.*`):
```
auth.validation.email_required      → Email required error
auth.validation.email_invalid       → Invalid email format error
auth.validation.password_required   → Password required error
auth.validation.password_min        → Password too short error
auth.validation.name_required       → Name required error
```

### Switching Languages

Click the language button (F/EN/DE) in navbar to switch languages immediately.

Language preference is saved in session and persists across pages.

---

## Troubleshooting

### Cannot Login

**Symptom:** "Invalid email or password" message, but credentials seem correct

**Solutions:**
1. Verify email is spelled correctly (case-insensitive)
2. Reset password via admin panel and try new password
3. Check if account is deleted (verify in `/admin/resources/users`)
4. Check application logs: `tail -f storage/logs/laravel.log`

### Password Change Not Working

**Symptom:** Form submits but password doesn't change

**Solutions:**
1. Verify current password field is correct
2. Verify new password is at least 8 characters
3. Verify password confirmation matches new password
4. Check form validation errors displayed inline
5. Clear browser cache and try again

### Cannot Access Admin Panel

**Symptom:** Redirected back to profile after login

**Solutions:**
1. Verify user `is_admin` is set to `true` in database:
   ```bash
   php artisan tinker
   >>> User::where('email', 'user@example.com')->first()->is_admin
   ```

2. Set user as admin from existing admin account via `/admin/resources/users`

3. Or via tinker:
   ```bash
   >>> $user = User::where('email', 'user@example.com')->first();
   >>> $user->update(['is_admin' => true]);
   ```

### "Remember Me" Not Working

**Symptom:** Gets logged out after closing browser despite checking "Remember me"

**Note:** Current implementation does not use "Remember me" feature. Session ends when browser is closed.

**Future:** Can be implemented with API tokens or persistent login cookies.

### Session Expires Too Quickly

**Symptom:** Gets logged out after 2 hours of activity

**Solution:** This is by design for security. Adjust in `config/session.php`:

```php
'lifetime' => env('SESSION_LIFETIME', 120),  // minutes
```

Set to higher value (e.g., 1440 for 24 hours) for longer sessions.

---

## API Integration (Future)

The public authentication system can be extended with:

- **API Tokens** - Allow users to generate tokens for API access
- **OAuth2** - Social login via Google, GitHub, etc.
- **Two-Factor Authentication** - TOTP/SMS based 2FA
- **Email Verification** - Require email verification on signup
- **Password Reset** - Self-service password recovery
- **User Registration** - Allow public signup with admin approval

---

## Admin Panel User Management

See [README.md](../README.md) for overview of Filament user management and role-based access.

See [MULTILINGUAL.md](./MULTILINGUAL.md) for i18n keys used in authentication pages.

---

**Last Updated:** 2026-04-10
**Version:** 2.0.0
**Status:** ✅ Production Ready
