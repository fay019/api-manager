# 🌐 Multilingual Support (i18n) Guide

Complete internationalization support for **French (FR), English (EN), and German (DE)**.

## Table of Contents

- [Overview](#overview)
- [Supported Languages](#supported-languages)
- [Architecture](#architecture)
- [Language Files](#language-files)
- [Using Translations](#using-translations)
- [Adding New Strings](#adding-new-strings)
- [Locale Detection](#locale-detection)
- [Switching Languages](#switching-languages)
- [Best Practices](#best-practices)

---

## Overview

The application is **fully internationalized** with automatic browser language detection, user-selectable language switching, and session-based persistence.

### Key Features

✅ **Automatic Detection** - Browser `Accept-Language` header detection  
✅ **User Selection** - Language switcher in navbar (FR/EN/DE)  
✅ **Session Persistence** - Selected language persists across pages (Session & Cookie Sync)  
✅ **Clean URLs** - No URL prefixes (`/admin` not `/en/admin`)  
✅ **Complete Coverage** - Setup Wizard, Public pages, Admin panel, Errors, Forms  
✅ **Dark Mode Aware** - Translations respect light/dark themes  
✅ **Fallback System** - Missing translations fallback to English → key name  
✅ **Manual Sync** - `.env` sync for `APP_LOCALE` on user selection

---

## Supported Languages

| Code | Language | Region | Flag |
|------|----------|--------|------|
| `fr` | Français | France | 🇫🇷 |
| `en` | English | United States | 🇬🇧 |
| `de` | Deutsch | Germany | 🇩🇪 |

**Default Language**: French (`fr`) - set in `config/app.php` as `APP_LOCALE=fr`

---

## Architecture

### SetLocale Middleware

The `app/Http/Middleware/SetLocale.php` middleware automatically detects and applies the user's language:

```php
// Detection order:
1. SetupSession ('locale' key)      // Only during installation
2. Session ('locale' key)           // User previously selected
3. Cookie ('locale' key)            // Persists 1 year
4. Browser Accept-Language header   // Browser preference
5. config('app.locale')             // Application default (FR)
```

**Registration:**
- Added to `bootstrap/app.php` for web middleware
- Added to `AdminPanelProvider.php` for Filament panel

### Language Files Structure

```
lang/
├── fr/                          # French
│   ├── app.php                  # Public pages (home, navbar, footer, docs, theme)
│   ├── auth.php                 # Authentication (login, profile, password)
│   ├── filament.php             # Admin panel (resources, pages, forms, labels)
│   ├── errors.php               # Error pages (401-503)
│   ├── contact.php              # Contact form & emails
│   └── setup.php                # Installation Wizard
│
├── en/                          # English
│   ├── app.php
│   ├── auth.php
│   ├── filament.php
│   ├── errors.php
│   ├── contact.php
│   └── setup.php
│
└── de/                          # German
    ├── app.php
    ├── auth.php
    ├── filament.php
    ├── errors.php
    ├── contact.php
    └── setup.php
```

---

## Language Files

### app.php (Public Pages)

Used for homepage, navbar, footer, documentation, theme toggle:

```php
return [
    'home' => [
        'title' => 'Welcome to API Manager',
        'subtitle' => 'Manage your APIs centrally',
    ],
    
    'nav' => [
        'home' => 'Home',
        'docs' => 'Documentation',
        'admin' => 'Admin Panel',
        'profile' => 'My Profile',
        'logout' => 'Logout',
    ],
    
    'footer' => [
        'copyright' => '© 2026 API Manager',
        'docs_link' => 'Documentation',
        'change_theme' => 'Change theme',
    ],
    
    'theme' => [
        'toggle_label' => 'Toggle dark mode',
        'switch_dark' => 'Dark mode',
        'switch_light' => 'Light mode',
    ],
];
```

### auth.php (Authentication)

Used for public login and profile pages:

```php
return [
    'login' => [
        'title' => 'Login',
        'email' => 'Email',
        'password' => 'Password',
        'remember' => 'Remember me',
        'button' => 'Login',
        'no_account' => 'Don\'t have an account?',
    ],
    
    'profile' => [
        'title' => 'My Profile',
        'edit_profile' => 'Edit Profile',
        'name' => 'Name',
        'email' => 'Email',
        'change_password' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'save' => 'Save Changes',
    ],
    
    'validation' => [
        'email_required' => 'Email is required',
        'email_invalid' => 'Please enter a valid email',
        'password_required' => 'Password is required',
        'password_min' => 'Password must be at least 8 characters',
        'name_required' => 'Name is required',
    ],
];
```

### filament.php (Admin Panel)

Used for Filament resources, pages, forms, labels:

```php
return [
    // Navigation
    'nav' => [
        'api_management' => 'API Management',
        'clients' => 'API Clients',
        'keys' => 'API Keys',
    ],
    
    // ApiClientResource
    'client' => [
        'singular' => 'Client',
        'plural' => 'Clients',
        'name' => 'Client Name',
        'type' => 'Client Type',
    ],
    
    // SettingsPage
    'settings' => [
        'general_section' => 'General Settings',
        'site_name' => 'Site Name',
        'environment' => 'Environment',
    ],
];
```

### errors.php (Error Pages)

Used for 401, 403, 404, 419, 500, 503 error pages:

```php
return [
    '404' => [
        'title' => 'Page Not Found',
        'message' => 'The page you are looking for does not exist.',
        'back_home' => '← Back to Home',         // Returns to homepage (/)
        'back_previous' => '← Previous Page',   // Returns to previous page
    ],
    
    '500' => [
        'title' => 'Server Error',
        'message' => 'Something went wrong on our end.',
        'back_home' => '← Back to Home',
        'back_previous' => '← Previous Page',
        'debug_enabled' => 'Debug Mode Enabled',
        'enable_debug' => 'Enable Debug Mode',
        'recent_logs' => 'Recent Logs',
        'full_log' => 'Full Log File',
        'no_logs' => 'No logs found',
    ],
];
```

**Note:** Each error code has two separate button translations:
- `back_home` - Navigates to homepage with `url('/')` 
- `back_previous` - Navigates to previous page with `url()->previous()`

This allows users to either go back to the last visited page or return to the homepage.

### contact.php (Contact Form)

Used for contact form pages and email notifications:

```php
return [
    'form' => [
        'name' => 'Your Name',
        'email' => 'Your Email',
        'subject' => 'Subject',
        'message' => 'Message',
    ],
    
    'validation' => [
        'required' => 'This field is required.',
        'email' => 'Please enter a valid email address.',
    ],
];
```

---

## Using Translations

### In Blade Templates

Use the `__()` helper function:

```blade
<!-- Public pages -->
<h1>{{ __('app.home.title') }}</h1>
<p>{{ __('app.home.subtitle') }}</p>

<!-- Navigation -->
<a href="/">{{ __('app.nav.home') }}</a>

<!-- Forms -->
<label>{{ __('contact.form.name') }}</label>
<input type="text" placeholder="{{ __('contact.form.name') }}">
```

### In Filament Resources

Use `__()` for all labels, descriptions, and messages:

```php
// TextInput field
TextInput::make('name')
    ->label(__('filament.client.name'))
    ->placeholder(__('filament.client.name_placeholder'))
    ->required();

// Section heading
Section::make(__('filament.settings.general_section'))
    ->description(__('filament.settings.general_section_desc'))
    ->columns(3);

// Action button
Action::make('edit')
    ->label(__('filament.actions.edit'));

// Validation messages
'name' => 'required',
'name.required' => __('validation.name_required'),
```

### In PHP Classes

Use `__()` for any user-facing text:

```php
// Services
throw new Exception(__('errors.database_connection'));

// Jobs
Mail::send(new WelcomeEmail(__('email.welcome_subject')));

// Controllers
return response()->json([
    'message' => __('api.success.created'),
    'data' => $data,
]);
```

### Nested Keys

Use dot notation for nested keys:

```php
// Key structure:
// 'client' => ['name' => '...']

// Usage in Blade:
{{ __('filament.client.name') }}

// Usage in PHP:
trans('filament.client.name')  // Alternative syntax
```

---

## Adding New Strings

### Step 1: Add Key to All Language Files

**1. `lang/fr/app.php` (or appropriate file):**
```php
'my_new_key' => 'Ma nouvelle clé en français',
```

**2. `lang/en/app.php`:**
```php
'my_new_key' => 'My new key in English',
```

**3. `lang/de/app.php`:**
```php
'my_new_key' => 'Mein neuer Schlüssel auf Deutsch',
```

### Step 2: Use in Code

**Blade template:**
```blade
<h1>{{ __('app.my_new_key') }}</h1>
```

**Filament resource:**
```php
TextInput::make('field')
    ->label(__('filament.my_new_key'))
```

### Step 3: Test All Languages

Before committing:
1. Click F button in navbar → verify text appears in French
2. Click EN button → verify text appears in English
3. Click DE button → verify text appears in German

### Best Practices for New Keys

✅ **Use consistent naming:**
```php
// ✅ Good
'client' => [
    'name' => '...',
    'type' => '...',
]

// ❌ Avoid
'client_name' => '...',
'clientType' => '...',
```

✅ **Group related keys:**
```php
'settings' => [
    'general_section' => '...',
    'general_section_desc' => '...',
    'contact_section' => '...',
]
```

✅ **Use descriptive key names:**
```php
// ✅ Clear
'error_database_connection_failed' => '...'

// ❌ Vague
'error1' => '...'
```

---

## Locale Detection

### How It Works

When a user visits the application:

1. **Check Session** - If user previously selected a language, use it
2. **Parse Accept-Language** - Check browser's language preferences
3. **Apply Default** - Fall back to `config('app.locale')` (French)

### Browser Accept-Language Header

The browser sends this header automatically:
```
Accept-Language: en-US,en;q=0.9,fr;q=0.8
```

The application:
1. Parses supported locales from the header
2. Finds the best match (exact code or base language)
3. Stores in session for persistence

### Supported Locale Codes

The middleware accepts these locales:
```php
['fr', 'en', 'de']
```

If a user's browser is set to `en-US`, it matches `en`. If set to `de-DE`, it matches `de`.

---

## Switching Languages

### Via UI (Navbar Button)

In the admin panel and public pages, click the language button:
- **F** - Français (French)
- **EN** - English
- **FR** - Français (alternative)
- **DE** - Deutsch (German)

This posts to `/locale/{locale}` and persists the choice.

### Via POST Request (Programmatically)

```bash
POST /locale/en
POST /locale/fr
POST /locale/de
```

**With cURL:**
```bash
curl -X POST https://api-manager.test/locale/en \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=csrf_token"
```

**With JavaScript:**
```javascript
fetch('/locale/en', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/x-www-form-urlencoded',
    },
})
.then(() => location.reload());
```

**In Blade Form:**
```blade
<form action="{{ route('locale.switch', 'en') }}" method="POST">
    @csrf
    <button type="submit">English</button>
</form>
```

### Route Definition

```php
// routes/web.php
Route::post('/locale/{locale}', function (string $locale) {
    return redirect()->back()->cookie('locale', $locale);
})->name('locale.switch')->where('locale', 'fr|en|de');
```

---

## Fallback System

If a translation key is missing, the application gracefully handles it:

1. **Locale File** - Check `lang/{current_locale}/file.php`
2. **English** - Fall back to `lang/en/file.php`
3. **Key Name** - Return the key itself (e.g., `filament.client.name`)

This ensures the application **never crashes** due to missing translations, though you'll see the key name instead of translated text.

---

## Testing Translations

### Manual Testing

1. **Login to admin panel** (`/admin`)
2. **Click language buttons** in top navbar (F/EN/DE)
3. **Verify all text changes**:
   - Sidebar navigation
   - Page headings
   - Form labels
   - Button text
   - Validation messages

4. **Test public pages**:
   - Visit homepage (`/`)
   - Click language switcher in navbar
   - Verify footer and all text changes

5. **Test error pages**:
   - Visit `/404` in each language
   - Verify error message is translated

### Automated Testing

Create a test to verify all keys exist in all languages:

```php
// tests/Feature/TranslationTest.php
public function test_all_translation_keys_exist()
{
    $locales = ['fr', 'en', 'de'];
    $files = ['app', 'filament', 'errors', 'contact'];
    
    foreach ($locales as $locale) {
        foreach ($files as $file) {
            $path = lang_path("{$locale}/{$file}.php");
            $this->assertFileExists($path);
        }
    }
}
```

---

## Performance Considerations

### Caching

Translation files are cached in production:

```bash
# Cache translations (production)
php artisan config:cache

# Clear cache (after modifying translations)
php artisan config:clear
php artisan cache:clear
```

### In Development

Caching is disabled by default, so changes appear immediately.

### Lazy Loading

Translations are loaded per-locale as needed, not all at once. This keeps memory usage low.

---

## Best Practices

### ✅ DO:

- Always use `__('key')` in Blade templates
- Keep translation keys organized by feature
- Translate all user-facing text
- Test in all 3 languages before committing
- Add keys to all 3 language files simultaneously
- Use consistent key naming conventions
- Document complex or context-specific translations

### ❌ DON'T:

- Hardcode English and translate later
- Use complex logic in translation keys
- Mix translated and hardcoded text
- Forget to add keys to all 3 language files
- Use special characters in key names
- Assume English fallback is sufficient
- Leave translations incomplete

---

## Common Translation Keys

### app.php

```php
app.home.title
app.home.subtitle
app.nav.home
app.nav.docs
app.nav.admin
app.nav.profile
app.nav.logout
app.footer.copyright
app.footer.change_theme
app.theme.toggle_label
```

### auth.php

```php
auth.login.title
auth.login.email
auth.login.password
auth.login.button
auth.profile.title
auth.profile.edit_profile
auth.profile.name
auth.profile.email
auth.profile.change_password
auth.validation.email_required
auth.validation.password_min
```

### filament.php

```php
filament.client.singular
filament.client.name
filament.key.plural
filament.log.timestamp
filament.settings.general_section
filament.settings.site_name
filament.users.singular
filament.users.plural
filament.users.create
filament.users.edit
```

### errors.php

```php
errors.401.title
errors.401.back_home
errors.401.back_previous
errors.404.title
errors.404.back_home
errors.404.back_previous
errors.500.title
errors.500.back_home
errors.500.back_previous
```

### contact.php

```php
contact.form.name
contact.form.email
contact.form.message
```

---

## Troubleshooting

### Translations Not Updating

1. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. Clear browser cache and cookies

3. Verify file syntax:
   ```bash
   php artisan tinker
   >>> trans('app.home.title')
   ```

### Missing Key Shows Fallback

This is expected behavior - the key name is shown if the translation doesn't exist.

**Solution**: Add the key to all 3 language files.

### Browser Language Not Detected

1. Check browser language settings
2. Verify `Accept-Language` header:
   ```bash
   curl -I https://api-manager.test -H "Accept-Language: de-DE"
   ```

3. Clear session cookies and try again

### Wrong Language After Login

Session may be overridden by default locale.

**Solution**: Click language button again to set preference in session.

---

## Migration Guide

### If Adding Translations to Existing Page

1. Extract all hardcoded text
2. Create keys in all 3 language files
3. Replace hardcoded text with `__('key')`
4. Test in all 3 languages
5. Commit with message: `refactor: add i18n for {feature}`

### If Creating New Module

Always include translations from day one:
- Create `lang/{locale}/` entries
- Use `__()` in all Blade templates
- Document translation keys in module README

---

## Support & Questions

For detailed implementation examples:
- See [CLAUDE.md](/CLAUDE.md) - Developer guidelines
- See [README.md](/README.md) - Feature overview
- See [README_DEV.md](/README_DEV.md) - Development notes
