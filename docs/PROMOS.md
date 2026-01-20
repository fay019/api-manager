# 🎯 Promotional Banners System (Promos Module)

The Promos module allows you to manage promotional or informative banners that are delivered via the API to external clients (mobile apps, websites).

---

## 📋 Overview

### What Are Promos?

Promotional banners are announcements with:
- **Title**: What the promo is about
- **Content**: Detailed description
- **Image**: Visual representation (URL)
- **Call-to-Action (CTA)**: Button text and link

### Key Capabilities

✅ **Schedule promotions** with start and end dates
✅ **Priority system** to control which promo shows when multiple are active
✅ **Status management** with automatic state transitions
✅ **Event tracking** (impressions, clicks, dismissals)
✅ **Version history** for audit trail and rollback
✅ **Caching** for performance optimization

---

## 🏠 Management Interface

### Access Location

**Admin Panel** → **Marketing** → **Promotions**

### Creating a Promotion

1. **Click** "Create" button
2. **Fill in details:**
   - **Title**: Give your promo a name
   - **Content**: Detailed description
   - **Image URL**: Link to banner image
   - **CTA Text**: Button text (e.g., "Shop Now", "Learn More")
   - **CTA URL**: Where the button links to

3. **Set dates:**
   - **Starts At** (optional): When to show this promo
   - **Ends At** (optional): When to stop showing it
   - Leave both empty for infinite duration

4. **Set priority:** 1-10 scale (10 = highest priority)

5. **Save**

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

---

## 🚀 API Integration

### Get Active Promo Banner

**Endpoint:** `GET /api/v1/promo/banner.json`

**Required Headers:**
```
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Request Example:**
```bash
curl -H "X-API-KEY: apk_xxx" \
  https://api.moussouni.dev/api/v1/promo/banner.json
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Summer Sale",
    "content": "Get 50% off on selected items",
    "image_url": "https://cdn.example.com/summer-banner.jpg",
    "cta_text": "Shop Now",
    "cta_url": "https://example.com/summer-sale",
    "priority": 10
  }
}
```

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

## 📊 Track Promo Events

### Event Tracking Endpoint

**Endpoint:** `POST /api/v1/promo/event`

**Required Headers:**
```
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: application/json
```

**Request Body:**
```json
{
  "promo_id": 1,
  "event_type": "impression",
  "session_id": "user-session-abc123",
  "url": "https://client-site.com/landing"
}
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `promo_id` | integer | ✅ Yes | ID of the promo |
| `event_type` | string | ✅ Yes | One of: `impression`, `click`, `dismiss` |
| `session_id` | string | ❌ No | Session identifier (hashed) |
| `url` | string | ❌ No | Referring URL |

### Event Types Explained

- **Impression**: Promo was displayed to the user
- **Click**: User clicked the CTA button
- **Dismiss**: User closed/dismissed the promo

### Success Response (201 Created)

```json
{
  "success": true,
  "data": {
    "message": "Event tracked successfully"
  }
}
```

### Error Responses

| Status | Code | Reason |
|--------|------|--------|
| 422 | `VALIDATION_ERROR` | Invalid `promo_id` or `event_type` |
| 404 | `NOT_FOUND` | Promo doesn't exist |
| 429 | `RATE_LIMIT_EXCEEDED` | Too many requests |
| 401 | `UNAUTHORIZED` | Invalid/missing API key |
| 403 | `FORBIDDEN` | Origin not in allowed list |

---

## 🔒 Privacy & Security

### Data Hashing

All sensitive data is hashed with SHA256:

- **Session ID**: `SHA256(session_id)`
- **IP Address**: `SHA256(ip_address)`
- **User-Agent**: `SHA256(user_agent)`

**Raw values NEVER stored** - only hashes for privacy compliance.

### Stored Data

- `referer`: HTTP Referer header (as-is)
- `origin`: HTTP Origin header (as-is)

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

## 📈 Analytics Dashboard

### View Promo Performance

In **Admin Panel** → **Marketing** → **Promotions**:

- **View counts**: Total impressions
- **Click counts**: Total clicks
- **Dismiss counts**: Total dismissals
- **Click-through rate (CTR)**: `clicks / impressions * 100`
- **Trend graphs**: Activity over time

### Example Query

```php
// Get event counts by type
$promo = Promo::find($id);
$stats = $promo->events()
    ->selectRaw('event_type, count(*) as count')
    ->groupBy('event_type')
    ->get();

// Calculate CTR
$impressions = $promo->events()->where('event_type', 'impression')->count();
$clicks = $promo->events()->where('event_type', 'click')->count();
$ctr = ($impressions > 0) ? ($clicks / $impressions) * 100 : 0;
```

---

## 🔧 Configuration

### Environment Variables

```env
# Cache duration in seconds (default: 60)
PROMO_CACHE_TTL=60

# Event retention in days (default: 180)
PROMO_EVENT_RETENTION_DAYS=180
```

### Prune Old Events

```bash
# Delete events older than 180 days
php artisan promo:prune-events --days=180
```

---

## 📱 Client Implementation Examples

### JavaScript/Fetch

```javascript
// Get active banner
const response = await fetch('https://api.example.com/api/v1/promo/banner.json', {
  headers: {
    'X-API-KEY': 'apk_your_key_here'
  }
});

const data = await response.json();
if (data.success) {
  // Display banner
  console.log(data.data); // { id, title, content, image_url, cta_text, cta_url }
}

// Track impression
await fetch('https://api.example.com/api/v1/promo/event', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-API-KEY': 'apk_your_key_here'
  },
  body: JSON.stringify({
    promo_id: data.data.id,
    event_type: 'impression',
    session_id: sessionStorage.getItem('session-id'),
    url: window.location.href
  })
});

// Track click
document.getElementById('cta-button').addEventListener('click', async () => {
  await fetch('https://api.example.com/api/v1/promo/event', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-API-KEY': 'apk_your_key_here'
    },
    body: JSON.stringify({
      promo_id: data.data.id,
      event_type: 'click',
      session_id: sessionStorage.getItem('session-id'),
      url: window.location.href
    })
  });
  // Navigate to CTA URL
  window.location.href = data.data.cta_url;
});
```

### cURL

```bash
# Get active promo
curl -H "X-API-KEY: apk_xxx" \
  https://api.example.com/api/v1/promo/banner.json

# Track event
curl -X POST https://api.example.com/api/v1/promo/event \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: apk_xxx" \
  -d '{
    "promo_id": 1,
    "event_type": "impression",
    "session_id": "user123",
    "url": "https://example.com"
  }'
```

### PHP (Guzzle)

```php
$client = new GuzzleHttp\Client();

// Get banner
$response = $client->get('https://api.example.com/api/v1/promo/banner.json', [
    'headers' => [
        'X-API-KEY' => 'apk_your_key'
    ]
]);

$promo = json_decode($response->getBody(), true);

// Track event
$client->post('https://api.example.com/api/v1/promo/event', [
    'headers' => [
        'X-API-KEY' => 'apk_your_key',
        'Content-Type' => 'application/json'
    ],
    'json' => [
        'promo_id' => $promo['data']['id'],
        'event_type' => 'impression',
        'session_id' => session_id(),
        'url' => $_SERVER['HTTP_REFERER'] ?? null
    ]
]);
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

### Events Not Tracking

**Problem:** Events endpoint returns errors

**Solutions:**
1. ✅ Verify `promo_id` exists
2. ✅ Verify `event_type` is one of: impression, click, dismiss
3. ✅ Check API key is valid
4. ✅ Verify origin is in client's allowed list

---

## 📚 Related Documentation

- [API Documentation](./API.md) - Complete API reference
- [Database Schema](./DATABASE.md) - Promo tables and relationships
- [Deployment Guide](./DEPLOYMENT.md) - Production setup

---

**Last Updated:** 2026-01-20
**Module:** Promos v1.0