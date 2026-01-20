# API Documentation

## Base URL

```
https://api.moussouni.dev/api/v1
```

## Authentication

All endpoints support optional API key authentication via header:

```
X-API-KEY: apk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

- **Authenticated requests**: 60 requests/minute (configurable per client)
- **Public requests**: 10 requests/minute per IP

## Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Example"
  },
  "meta": {
    "key": "value"
  }
}
```

**HTTP Status**: 200 OK

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human readable error message",
    "details": {
      "field": ["validation error"]
    }
  }
}
```

## Common Error Codes

| Code | Status | Description |
|------|--------|-------------|
| `UNAUTHORIZED` | 401 | Invalid or missing API key |
| `FORBIDDEN` | 403 | Origin not allowed for client |
| `NOT_FOUND` | 404 | Resource not found |
| `VALIDATION_ERROR` | 422 | Request validation failed |
| `RATE_LIMIT_EXCEEDED` | 429 | Too many requests |

## Rate Limiting Headers

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 45
```

---

## Endpoints

### Health Check

**Public endpoint** - No rate limiting

```
GET /health
```

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "ok",
    "timestamp": "2026-01-18T18:17:51Z"
  }
}
```

---

### Get Active Promo Banner

```
GET /promo/banner.json
```

**Query Parameters**: None

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

**Caching**: 60 seconds (configurable)

**Logic**: Returns promo matching ALL criteria:
- `status = "published"`
- `starts_at <= now()` (or NULL)
- `ends_at >= now()` (or NULL)
- Highest `priority` first
- Newest `created_at` as tiebreaker

---

### Track Promo Event

```
POST /promo/event
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

**Parameters:**

| Name | Type | Required | Description |
|------|------|----------|-------------|
| `promo_id` | integer | ✅ Yes | ID of promo to track |
| `event_type` | string | ✅ Yes | One of: `impression`, `click`, `dismiss` |
| `session_id` | string | ❌ No | Session identifier (will be hashed) |
| `url` | string | ❌ No | Referring URL |

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "message": "Event tracked successfully"
  }
}
```

**Errors:**

| Status | Code | Message |
|--------|------|---------|
| 422 | `VALIDATION_ERROR` | `promo_id` not found or invalid `event_type` |
| 404 | `NOT_FOUND` | Promo with given ID doesn't exist |
| 429 | `RATE_LIMIT_EXCEEDED` | Too many requests for this IP/key |
| 401 | `UNAUTHORIZED` | Invalid API key |
| 403 | `FORBIDDEN` | Origin not in allowed list |

**Privacy Note:**
- Session ID: Hashed with SHA256
- IP address: Hashed with SHA256
- User-Agent: Hashed with SHA256
- Raw data never stored

---

## CORS

CORS validation is per API client. When making requests from a browser:

### Allowed Origin
Client must have your domain in their `allowed_origins` list.

**Request:**
```
Origin: https://example.com
```

**Response Headers:**
```
Access-Control-Allow-Origin: https://example.com
Access-Control-Allow-Credentials: true
Access-Control-Allow-Methods: GET, POST, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY
Access-Control-Max-Age: 3600
```

### Disallowed Origin
```
Origin: https://malicious.com
```

**Response:**
```
HTTP 403 Forbidden

{
  "success": false,
  "error": {
    "code": "FORBIDDEN",
    "message": "Origin not allowed"
  }
}
```

### Server-to-Server (No Origin Header)
Allowed if API key is valid, regardless of origins list.

---

## Examples

### JavaScript/Fetch

**Get Banner:**
```javascript
const response = await fetch('https://api.moussouni.dev/api/v1/promo/banner.json', {
  headers: {
    'X-API-KEY': 'apk_your_key_here'
  }
});

const data = await response.json();
if (data.success) {
  console.log(data.data); // Promo data
}
```

**Track Event:**
```javascript
await fetch('https://api.moussouni.dev/api/v1/promo/event', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-API-KEY': 'apk_your_key_here'
  },
  body: JSON.stringify({
    promo_id: 1,
    event_type: 'impression',
    session_id: sessionStorage.getItem('session-id'),
    url: window.location.href
  })
});
```

### cURL

```bash
# Get health
curl https://api.moussouni.dev/api/v1/health

# Get banner with API key
curl -H "X-API-KEY: apk_xxx" \
  https://api.moussouni.dev/api/v1/promo/banner.json

# Track event
curl -X POST https://api.moussouni.dev/api/v1/promo/event \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: apk_xxx" \
  -d '{
    "promo_id": 1,
    "event_type": "click",
    "session_id": "abc123"
  }'
```

### PHP

```php
$client = new GuzzleHttp\Client();

$response = $client->get('https://api.moussouni.dev/api/v1/promo/banner.json', [
    'headers' => [
        'X-API-KEY' => 'apk_your_key'
    ]
]);

$data = json_decode($response->getBody(), true);
```

---

## Versioning

API is versioned at `/api/v1/`. Future versions (`v2`, etc.) will be additive - existing `v1` endpoints remain unchanged.

---

## Support

For issues or questions, contact your system administrator.
