# 🎯 Promotional Banners System (Promos Module)

The Promos module allows you to manage promotional or informative banners that are delivered via the API to external clients (mobile apps, websites).

---

## 📋 Overview

### What Are Promos?

Promotional banners are announcements with:
- **Title**: What the promo is about (Multilingual: FR, EN, DE, AR)
- **Content**: Detailed description (Multilingual: FR, EN, DE, AR)
- **Image**: Visual representation (`full_image_url` generated for API, `image_url` for internal storage)
- **Call-to-Action (CTA)**: Button text (Multilingual) and link

### Key Capabilities

✅ **Schedule promotions** with start and end dates
✅ **Priority system** to control which promo shows when multiple are active
✅ **Status management** with automatic state transitions
✅ **Version history** with full audit trail and change tracking
✅ **Caching** for performance optimization

---

## 🏠 Management Interface

### Access Location

**Admin Panel** → **Marketing** → **Promotions**

### Creating a Promotion

1. **Click** "Create" button
2. **Fill in details using the Multilingual Tabs:**
   - The interface provides tabs for **Français**, **English**, **Deutsch**, and **العربية (Arabic)**.
   - **Title**: Give your promo a name for each language.
   - **Content**: Detailed description for each language.
   - **CTA Text**: Button text (e.g., "Shop Now", "Learn More") for each language.
   - Note: Arabic support includes **RTL (Right-to-Left)** layout for better editing experience.

3. **General Settings:**
   - **Image**: Upload a file (API will receive `full_image_url`).
   - **CTA URL**: Where the button links to (same for all languages).

4. **Set dates:**
   - **Starts At** (optional): When to show this promo
   - **Ends At** (optional): When to stop showing it
   - Leave both empty for infinite duration

5. **Upload Image**: Upload a file in the "Média" section. The system will automatically generate a full public URL for the API (`full_image_url`) and store the relative path in the database (`image_url`).

6. **Set priority:** 1-10 scale (10 = highest priority)

7. **Configure Display Features (Optional):**
   - **Fermeture automatique:** Set auto-close time in seconds (0 = disabled)
   - **Afficher le compte à rebours:** Enable to show countdown before auto-close
   - **Style d'animation:** Choose animation style (fade, slide, zoom)

8. **Save**

---

## 🔄 Status System

Promotions automatically transition through statuses based on dates and configuration:

### Status Types

| Status | Meaning | When It Appears |
|--------|---------|-----------------|
| **🖊️ Draft** | Editing mode | Never shown, regardless of dates |
| **📅 Scheduled** | Ready but not yet active | `starts_at` is in the future |
| **🟢 Published** | Currently active & visible | Between `starts_at` and `ends_at` |
| **📦 Archived** | Finished & no longer shown | `ends_at` is in the past |

### Status Transition Logic

```
Draft (manual edit only)
  ↓
[No dates] → Draft (stays)
[Only starts_at, future] → Scheduled
[Only starts_at, past] → Published
[Only ends_at, future] → Published
[Only ends_at, past] → Archived
[starts_at past + ends_at future] → Published
[starts_at future + ends_at any] → Scheduled
[starts_at any + ends_at past] → Archived
[Both dates empty] → Draft
```

---

## 🎨 Editing & Live Validation

### Real-Time Updates

- Status updates **automatically** as you change dates
- Error messages disappear **instantly** when fixed
- No need to save twice
- Changes persist immediately

### Date Validation Rules

✅ `ends_at` must be ≥ `starts_at` (if both set)
✅ Dates use server time for exact scheduling
✅ No tolerance or grace period (precise timing)

### API Preview

- **JSON Preview**: A "Aperçu JSON" button in the promotions table and on the edit page allows you to see exactly what the API response will look like for a specific promo.
- **Success & Error cases**: The modal shows the successful 200 OK response, the 401 Unauthorized error (if security is enabled), the 404 Not Found error (when no promo is active), and the 429 Too Many Requests error (rate limiting).

---

## 🚀 API Integration

### Get Active Promo Banner

**Endpoint:** `GET /api/v1/promo/banner.json`

**Required Headers:**
```
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Query Parameters

Choose ONE of these modes:

#### Mode 1: Single Language (Default)
Return promo in a specific language (texts as STRING):

```bash
# Français (default if no param)
GET /api/v1/promo/banner.json

# English
GET /api/v1/promo/banner.json?lang=en

# Deutsch
GET /api/v1/promo/banner.json?lang=de

# العربية
GET /api/v1/promo/banner.json?lang=ar
```

**Supported languages:** `fr` (default), `en`, `de`, `ar`

#### Mode 2: All Languages
Return promo with all translations (texts as OBJECT):

```bash
GET /api/v1/promo/banner.json?all_langs=true
```

**Request Examples:**
```bash
# Get in English
curl -H "X-API-KEY: apk_xxx" \
  https://api.example.com/api/v1/promo/banner.json?lang=en

# Get all languages
curl -H "X-API-KEY: apk_xxx" \
  https://api.example.com/api/v1/promo/banner.json?all_langs=true
```

**Success Response - Mode 1 (Single Language, `?lang=en`):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "version": 3,
    "locale": "en",
    "author_name": "Marketing Team",
    "author_role": "Campaign Manager",
    "title": "Summer Sale",
    "content": "Get 50% off on selected items",
    "cta_text": "Shop Now",
    "image_url": "https://cdn.example.com/summer-banner.jpg",
    "cta_url": "https://example.com/summer-sale",
    "priority": 10,
    "max_impressions": 5,
    "cooldown_seconds": 86400,
    "display_mode": "fixed_count",
    "start_date": "2026-01-25",
    "end_date": "2026-02-25",
    "auto_close_timer": 15,
    "show_countdown": true,
    "animation_style": "fade"
  }
}
```

**Note:**
- Texts (`title`, `content`, `cta_text`) are **STRING** (single language)
- `locale` field indicates the language of returned content
- Optional fields (`auto_close_timer`, `show_countdown`, `animation_style`) are only included when configured

**Success Response - Mode 2 (All Languages, `?all_langs=true`):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "version": 3,
    "author_name": "Marketing Team",
    "author_role": "Campaign Manager",
    "translations": {
      "title": {
        "fr": "Soldes d'été",
        "en": "Summer Sale",
        "de": "Sommerschlussverkauf",
        "ar": "تخفيضات الصيف"
      },
      "content": {
        "fr": "Profitez de 50% de réduction sur les articles sélectionnés",
        "en": "Get 50% off on selected items",
        "de": "Erhalten Sie 50% Rabatt auf ausgewählte Artikel",
        "ar": "احصل على خصم 50٪ على العناصر المختارة"
      },
      "cta_text": {
        "fr": "Acheter maintenant",
        "en": "Shop Now",
        "de": "Jetzt einkaufen",
        "ar": "تسوق الآن"
      }
    },
    "image_url": "https://cdn.example.com/summer-banner.jpg",
    "cta_url": "https://example.com/summer-sale",
    "priority": 10,
    "max_impressions": 5,
    "cooldown_seconds": 86400,
    "display_mode": "fixed_count",
    "start_date": "2026-01-25",
    "end_date": "2026-02-25",
    "auto_close_timer": 15,
    "show_countdown": true,
    "animation_style": "fade"
  }
}
```

**Note:**
- Texts (`title`, `content`, `cta_text`) are **OBJECTS** with all 4 languages (fr, en, de, ar)
- ⚠️ **NO** `locale` field in this mode (you have all languages available)
- Optional fields are still only included when configured

### Response Fields

#### Always Present

| Field | Type | Description |
|-------|------|-------------|
| **id** | integer | Unique promo identifier |
| **version** | integer | Version number of the promo |
| **author_name** | string | Name of the promo author |
| **author_role** | string | Role/title of the promo author |
| **priority** | integer | Priority level (1-10, where 10 is highest) |
| **max_impressions** | integer | Max number of views before disappearing |
| **cooldown_seconds** | integer | Wait time (seconds) after manual close |
| **display_mode** | string | Display frequency: `fixed_count`, `unlimited`, `once_per_day`, `once_per_week` |
| **image_url** | string | Full URL to promo image |
| **cta_url** | string | Full URL for CTA button |
| **start_date** | string | Campaign start date (YYYY-MM-DD) |
| **end_date** | string | Campaign end date (YYYY-MM-DD) |

#### Text Fields (Mode Dependent)

| Field | Type (Mode 1) | Type (Mode 2) | Description |
|-------|---|---|-------------|
| **title** | string | object | `{fr: "...", en: "...", de: "...", ar: "..."}` |
| **content** | string | object | `{fr: "...", en: "...", de: "...", ar: "..."}` |
| **cta_text** | string | object | `{fr: "...", en: "...", de: "...", ar: "..."}` |
| **locale** | string | absent | Language code of returned content (Mode 1 only) |

#### Optional Fields (Only if Configured)

| Field | Type | Description |
|-------|------|-------------|
| **auto_close_timer** | integer | Seconds before auto-close (0 = disabled) |
| **show_countdown** | boolean | Display countdown timer before auto-close |
| **animation_style** | string | Animation style: `fade`, `slide`, `zoom` |

### Advanced Display Features (Optional)

The API supports optional display behavior fields that enable advanced client-side features:

#### **auto_close_timer** (integer, optional)
- **Default:** Not included (auto-close disabled)
- **Valid Values:** 0 or positive integers (seconds)
- **Example:** `15` = Banner closes automatically after 15 seconds
- **Use Case:** Time-limited announcements that dismiss themselves

#### **show_countdown** (boolean, optional)
- **Default:** Not included
- **Valid Values:** `true` or `false`
- **Use Case:** Displays a countdown timer before the banner auto-closes
- **Note:** Only meaningful if `auto_close_timer` is configured

#### **animation_style** (string, optional)
- **Default:** Not included
- **Valid Values:** `fade`, `slide`, `zoom`
- **Examples:**
  - `"fade"` - Smooth opacity transition
  - `"slide"` - Slide in from top/side
  - `"zoom"` - Zoom/scale animation
- **Use Case:** Control how the banner appears on screen

### Backward Compatibility

**Important:** Optional fields are only included in the API response when they have been explicitly set in the admin panel. This ensures **100% backward compatibility** with existing client implementations:

- Clients that don't expect these fields simply ignore them
- Clients can check for field presence before using advanced features
- No changes needed to existing integrations

**Example:** A client that only needs basic banner display won't receive `auto_close_timer`, `show_countdown`, or `animation_style` if they're not configured.

**No Active Promo (404 Not Found):**
```json
{
  "success": false,
  "error": {
    "code": "NOT_FOUND",
    "message": "No active promo available",
    "details": {}
  }
}
```

### Selection Algorithm

The API returns the **first active promo** that matches:

1. ✅ Status = `published`
2. ✅ `starts_at` ≤ now (or NULL)
3. ✅ `ends_at` ≥ now (or NULL)
4. **Sorted by:** Highest `priority` first
5. **Tiebreaker:** Newest `created_at` first

---

## 📜 Version History & Audit Trail

### Overview

Every change to a promotion is automatically tracked and saved in the version history system. This allows you to:

- 👀 View all previous versions of a promo
- 📊 See exactly what changed between versions
- 🔄 Understand who made changes and when
- 🔍 Track the complete lifecycle of a promotion

### Accessing Version History

1. Go to **Admin Panel** → **Marketing** → **Promotions**
2. Click on a promo to edit it
3. In the **edit page**, you'll see a **"Version History"** button in the top-right corner
4. Click the button to open a modal with the full version timeline

### Version Timeline

The modal displays:

- **Version number** (v1, v2, v3, etc.)
- **Current version indicator** - Shows which version is currently live
- **Created by** - Who made the change
- **Timestamp** - Exact date and time of the change
- **Changes** - Expandable section showing field-by-field changes

### Viewing Changes

For each version (except the first), you can:

1. Click **"Latest changes"** or **"Changes (n fields)"** to expand
2. See a **Before/After comparison** with:
   - **Red strikethrough text** = Old value
   - **Green text** = New value

### Fields Tracked

All fields are versioned:
- Title, Content, Image URL
- CTA Text and CTA URL
- Status, Start Date, End Date
- Priority
- Created By (user)

### Example

```
v5 (CURRENT)
├─ Latest changes (2 fields)
│  ├─ Title: "Summer Sale" → "Summer Mega Sale"
│  └─ Priority: 5 → 10
│
v4
├─ Changes (3 fields)
│  ├─ Status: draft → published
│  ├─ Starts At: 2026-01-15 → 2026-01-20
│  └─ Ends At: 2026-02-15 → 2026-02-28
```

### Technical Details

- **Automatic tracking**: Changes are captured by the `PromoObserver`
- **Storage**: Each version is stored in the `promo_versions` table
- **Immutable**: Historical versions cannot be edited
- **No versioning overhead**: Uses efficient JSON storage

---

## ⚡ Performance & Caching

### Caching Strategy

The active promo is **cached for 60 seconds** (configurable):

```env
# In .env
PROMO_CACHE_TTL=60
```

### Cache Invalidation

Cache is **automatically cleared** when you:
- Create a new promo
- Update an existing promo
- Delete a promo
- Change status or dates

**No manual cache clearing needed.**

---

## 🔧 Configuration

### Environment Variables

```env
# Cache duration in seconds (default: 60)
PROMO_CACHE_TTL=60
```

---

## 📱 Client Implementation Examples

### JavaScript/Fetch

```javascript
// Get active banner in English
const response = await fetch('https://api.example.com/api/v1/promo/banner.json?lang=en', {
  headers: {
    'X-API-KEY': 'apk_your_key_here'
  }
});

const data = await response.json();
if (data.success) {
  // Display banner
  console.log(data.data); // { id, title, content, image_url, cta_text, cta_url }

  // Display the banner UI
  const banner = document.createElement('div');
  banner.innerHTML = `
    <div class="promo-banner">
      <img src="${data.data.image_url}" alt="${data.data.title}">
      <h3>${data.data.title}</h3>
      <p>${data.data.content}</p>
      <a href="${data.data.cta_url}" class="cta-button">${data.data.cta_text}</a>
    </div>
  `;
  document.body.appendChild(banner);
}
```

### cURL

```bash
# Get active promo in Arabic
curl -H "X-API-KEY: apk_xxx" \
  https://api.example.com/api/v1/promo/banner.json?lang=ar
```

### PHP (Guzzle)

```php
$client = new GuzzleHttp\Client();

// Get banner in German
$response = $client->get('https://api.example.com/api/v1/promo/banner.json', [
    'query' => ['lang' => 'de'],
    'headers' => [
        'X-API-KEY' => 'apk_your_key'
    ]
]);

$promo = json_decode($response->getBody(), true);

if ($promo['success']) {
    // Display promo
    echo $promo['data']['title'];
    echo $promo['data']['content'];
    echo '<img src="' . $promo['data']['image_url'] . '">';
    echo '<a href="' . $promo['data']['cta_url'] . '">' . $promo['data']['cta_text'] . '</a>';
}
```

---

## 🐛 Troubleshooting

### No Promo Showing

**Problem:** API returns 404 even though you have active promos

**Solutions:**
1. ✅ Verify promo status is "Published" (not Draft)
2. ✅ Check `starts_at` is in the past or empty
3. ✅ Check `ends_at` is in the future or empty
4. ✅ Clear admin cache: `php artisan cache:forget promo:active`

### Wrong Promo Showing

**Problem:** Lower priority promo showing instead of higher

**Solution:**
- Check both promos have correct status
- Higher priority value (e.g., 10) shows first
- Verify dates don't exclude the expected promo

---

## 📚 Related Documentation

- [API Documentation](./API.md) - Complete API reference
- [Analytics Dashboard](./ANALYTICS.md) - API request analytics and monitoring
- [Database Schema](./DATABASE.md) - Promo tables and relationships
- [Deployment Guide](./DEPLOYMENT.md) - Production setup

---

**Last Updated:** 2026-01-28
**Module:** Promos v1.3
**New in v1.3:** Advanced display features (auto-close, countdown, animations)
