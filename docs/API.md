# 📡 API Documentation

Complete reference for the API Manager REST API with authentication, rate limiting, endpoints, and client implementation examples.

---

## 🌐 Base URL

```
https://api.moussouni.dev/api/v1
```

All API requests are made to this base URL. Use HTTPS in production.

---

## 🔐 Authentication

### API Key Authentication

Most endpoints require authentication via API key in the request header:

```
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Key Lifecycle & Status

Each API key has a lifecycle controlled by dates and manual toggle:

| Property | Description |
|----------|-------------|
| **Starts At** | Key is invalid before this date/time |
| **Expires At** | Key is invalid after this date/time |
| **is_active** | Manual toggle (true = usable, false = revoked) |
| **Client Status** | If client is disabled, all its keys are invalidated |

### Key Status Indicators

| Status | Color | Meaning |
|--------|-------|---------|
| 🟢 **Active** | Green | Key is valid and usable now |
| 🔵 **Scheduled** | Blue | Key will become active in the future |
| 🟠 **Expired** | Orange | `expires_at` date has passed |
| 🔴 **Revoked** | Red | Key manually disabled or client is inactive |

### Rate Limits

| Type | Limit | Notes |
|------|-------|-------|
| **Authenticated** | Per-client (default: 60/min) | Configured per API client |
| **Public** | 10 requests/min per IP | For unauthenticated requests |
| **Monthly Quota** | Configurable | Optional limit per client |

### Rate Limit Headers

Every response includes rate limit information:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 45
```

### Key Security

API keys are encrypted with AES-256 reversible encryption:
- ✅ Admins can view full key if lost
- ✅ Keys are never logged
- ✅ Raw key shown only once on creation
- ✅ Must be copied immediately

---

## 📤 Response Format

### Success Response (2xx)

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Example",
    "status": "active"
  },
  "meta": {
    "request_id": "req_abc123",
    "timestamp": "2026-01-20T10:30:00Z"
  }
}
```

**Status:** 200 OK, 201 Created, etc.

### Error Response (4xx, 5xx)

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": {
      "field_name": ["Validation error message"]
    }
  }
}
```

---

## ❌ Error Codes & Status

### Common Error Codes

| Code | Status | Description | Solution |
|------|--------|-------------|----------|
| `UNAUTHORIZED` | 401 | Invalid or missing API key | Verify API key is correct |
| `FORBIDDEN` | 403 | Origin not in allowed list (CORS) | Add origin to client's allowed_origins |
| `NOT_FOUND` | 404 | Resource doesn't exist | Check resource ID exists |
| `VALIDATION_ERROR` | 422 | Invalid request body | Check parameter types and required fields |
| `RATE_LIMIT_EXCEEDED` | 429 | Too many requests | Wait and retry after `Retry-After` seconds |
| `INTERNAL_ERROR` | 500 | Server error | Contact support, check logs |

---

## 🔗 Endpoints

### 1. Health Check

**Public endpoint** - No authentication or rate limiting required

```
GET /health
```

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "ok",
    "timestamp": "2026-01-20T10:30:00Z",
    "version": "1.0"
  }
}
```

**Use Case:** Health monitoring, uptime checks, CI/CD pipelines

---

### 2. Get Active Promo Banner

Returns the active promotional banner for this client.

```
GET /promo/banner.json
```

**Authentication:** Required (X-API-KEY header)

**Response (200 OK):**
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

**Response (404 - No active promo):**
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

**Caching:** Results cached for 60 seconds

**Selection Logic:**
- Status must be `published`
- `starts_at` ≤ current time (or NULL)
- `ends_at` ≥ current time (or NULL)
- Highest priority first
- Newest creation date as tiebreaker

**Use Case:** Display banners in mobile apps or websites

---

### 3. Track Promo Event

Records user interactions with promotional banners.

```
POST /promo/event
```

**Authentication:** Required (X-API-KEY header)

**Content-Type:** `application/json`

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
| `promo_id` | integer | ✅ Yes | ID of promo to track |
| `event_type` | string | ✅ Yes | `impression` \| `click` \| `dismiss` |
| `session_id` | string | ❌ No | Session identifier (will be hashed) |
| `url` | string | ❌ No | Referring URL |

**Event Types:**
- **impression**: Promo was displayed
- **click**: User clicked the CTA button
- **dismiss**: User closed/dismissed the promo

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "message": "Event tracked successfully"
  }
}
```

**Error Responses:**

| Status | Code | Reason |
|--------|------|--------|
| 422 | `VALIDATION_ERROR` | Invalid `promo_id` or `event_type` |
| 404 | `NOT_FOUND` | Promo with given ID doesn't exist |
| 429 | `RATE_LIMIT_EXCEEDED` | Too many requests for this IP/key |
| 401 | `UNAUTHORIZED` | Invalid API key |
| 403 | `FORBIDDEN` | Origin not in allowed list |

**Privacy Protection:**
- Session ID hashed: `SHA256(session_id)`
- IP address hashed: `SHA256(ip_address)`
- User-Agent hashed: `SHA256(user_agent)`
- Raw data never stored in database

**Use Case:** Analytics, conversion tracking, user engagement metrics

---

## 🌍 CORS (Cross-Origin Resource Sharing)

### Browser Requests

When making requests from a browser, CORS is enforced:

**Request:**
```
Origin: https://example.com
```

**Response (if origin allowed):**
```
Access-Control-Allow-Origin: https://example.com
Access-Control-Allow-Credentials: true
Access-Control-Allow-Methods: GET, POST, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY
Access-Control-Max-Age: 3600
```

**Response (if origin blocked):**
```
HTTP/1.1 403 Forbidden

{
  "success": false,
  "error": {
    "code": "FORBIDDEN",
    "message": "Origin not allowed"
  }
}
```

### Server-to-Server Requests

No Origin header needed - only API key validation. CORS origins list is ignored.

### Managing CORS

Edit your API client in admin panel:
1. Go to **API Management** → **API Clients**
2. Edit your client
3. Set **Allowed Origins** (e.g., `https://example.com`, `https://app.example.com`)
4. Leave empty to allow all origins ⚠️ (not recommended for production)

---

## 💻 Client Implementation Examples

### JavaScript (Fetch API)

```javascript
// Get banner
async function getPromo() {
  const response = await fetch('https://api.moussouni.dev/api/v1/promo/banner.json', {
    headers: {
      'X-API-KEY': 'apk_your_key_here'
    }
  });

  const data = await response.json();

  if (data.success) {
    // Display banner
    document.getElementById('banner').innerHTML = `
      <div class="promo-card">
        <img src="${data.data.image_url}" alt="${data.data.title}">
        <h2>${data.data.title}</h2>
        <p>${data.data.content}</p>
        <a href="${data.data.cta_url}" class="cta-button">
          ${data.data.cta_text}
        </a>
      </div>
    `;

    // Track impression
    trackEvent(data.data.id, 'impression');
  }
}

// Track event
async function trackEvent(promoId, eventType) {
  await fetch('https://api.moussouni.dev/api/v1/promo/event', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-API-KEY': 'apk_your_key_here'
    },
    body: JSON.stringify({
      promo_id: promoId,
      event_type: eventType,
      session_id: sessionStorage.getItem('session-id'),
      url: window.location.href
    })
  });
}

getPromo();
```

### cURL

```bash
# Check health
curl https://api.moussouni.dev/api/v1/health

# Get banner
curl -H "X-API-KEY: apk_xxx" \
  https://api.moussouni.dev/api/v1/promo/banner.json

# Track event
curl -X POST https://api.moussouni.dev/api/v1/promo/event \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: apk_xxx" \
  -d '{
    "promo_id": 1,
    "event_type": "impression",
    "session_id": "abc123",
    "url": "https://example.com"
  }'
```

### PHP (Guzzle HTTP Client)

```php
use GuzzleHttp\Client;

$client = new Client();

// Get health
$response = $client->get('https://api.moussouni.dev/api/v1/health');
$health = json_decode($response->getBody(), true);

// Get banner
$response = $client->get('https://api.moussouni.dev/api/v1/promo/banner.json', [
    'headers' => [
        'X-API-KEY' => 'apk_your_key'
    ]
]);

$promo = json_decode($response->getBody(), true);

if ($promo['success']) {
    echo "Banner: " . $promo['data']['title'];

    // Track event
    $client->post('https://api.moussouni.dev/api/v1/promo/event', [
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
}
```

### Python (Requests)

```python
import requests

api_url = "https://api.moussouni.dev/api/v1"
api_key = "apk_your_key_here"

headers = {
    "X-API-KEY": api_key
}

# Get banner
response = requests.get(f"{api_url}/promo/banner.json", headers=headers)
promo = response.json()

if promo['success']:
    print(f"Banner: {promo['data']['title']}")

    # Track event
    event_data = {
        "promo_id": promo['data']['id'],
        "event_type": "impression",
        "session_id": "user123",
        "url": "https://example.com"
    }

    response = requests.post(
        f"{api_url}/promo/event",
        json=event_data,
        headers=headers
    )
    print(f"Event tracked: {response.json()}")
```

---

## 📊 Rate Limiting & Quotas

### How Rate Limiting Works

1. **Per-client limit** (default: 60 requests/minute)
   - Applies to all requests using that API key
   - Configurable per client in admin panel

2. **Monthly quota** (optional, per-client)
   - Total requests allowed per month
   - Once exceeded, client gets 403 error

3. **Public/unauthenticated** (10 requests/minute per IP)
   - Applies to requests without API key
   - Based on client IP address

### When Rate Limited

```
HTTP/1.1 429 Too Many Requests

{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Rate limit exceeded. Please try again after 45 seconds",
    "details": {}
  }
}
```

**Header:**
```
Retry-After: 45
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1674150645
```

### Tips

- ✅ Always check `X-RateLimit-Remaining` header
- ✅ Wait `Retry-After` seconds before retrying
- ✅ Implement exponential backoff for retries
- ✅ Cache responses locally to reduce requests

---

## 🔄 Versioning

The API is versioned at `/api/v1/`.

**Future compatibility:**
- `v2`, `v3`, etc. will be additive
- Existing `v1` endpoints never change
- No breaking changes to current version
- Deprecated endpoints provided with migration path

---

## 🔗 API Client Management

Each API client is configured with:
- **Name** & **Type** (MOBILE, WEB, PARTNER, INTERNAL)
- **Rate Limit** (requests per minute)
- **Monthly Quota** (optional)
- **Allowed Origins** (CORS)
- **Contact Info** (name, email, website)

For detailed client management, see **[CLIENTS.md](./CLIENTS.md)**

---

## 📚 Request Logging & Analytics

Every API request is logged with:

- Request method & path
- Client information
- Response status code
- IP address (hashed)
- User-Agent (hashed)
- Response time in milliseconds
- Timestamp

Access logs from **Admin Panel** → **API Management** → **Request Logs**

---

## 🆘 Troubleshooting

### 401 Unauthorized

**Problem:** `"Invalid or missing API key"`

**Solutions:**
1. ✅ Verify header name: must be `X-API-KEY` (case-sensitive)
2. ✅ Verify API key is correct (starts with `apk_`)
3. ✅ Check key hasn't expired (`expires_at` date)
4. ✅ Check key is active (not revoked)
5. ✅ Check client is active (not disabled)

### 403 Forbidden

**Problem:** `"Origin not allowed"`

**Solutions:**
1. ✅ Add your domain to client's `Allowed Origins`
2. ✅ Or verify request is server-to-server (no Origin header)

### 429 Rate Limited

**Problem:** Too many requests

**Solutions:**
1. ✅ Wait `Retry-After` seconds
2. ✅ Check monthly quota not exceeded
3. ✅ Implement request caching
4. ✅ Contact support to increase limit

### CORS Not Working

**Problem:** Browser blocks request with CORS error

**Solutions:**
1. ✅ API must return proper `Access-Control-Allow-*` headers
2. ✅ Client's `Allowed Origins` must match request origin
3. ✅ Request must include proper headers (Content-Type, etc.)

---

## 📞 Support

For API issues:
1. Check this documentation
2. Review application logs
3. Test with curl first
4. Check admin panel for client settings
5. Contact your system administrator

---

**Last Updated:** 2026-01-20
**Version:** v1.0
**Base URL:** https://api.moussouni.dev/api/v1