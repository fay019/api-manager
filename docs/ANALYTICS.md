# 📊 Analytics & Monitoring

Monitor your API usage with real-time analytics dashboards and widgets.

---

## 🎯 Overview

The Analytics system provides insights into:

- 📈 **API Request Volume** - Track total requests per day/month
- ⚡ **Performance Metrics** - Monitor average response times
- ✅ **Success Rates** - See percentage of successful vs failed requests
- 🔴 **Error Tracking** - Identify problematic endpoints
- 👥 **Top Clients** - See which clients are using your API most
- 🎯 **Endpoint Analytics** - Performance breakdown by endpoint

---

## 📍 Accessing Analytics

### 1. Dashboard Widget

When you log into the admin panel, you'll see the **API Request Statistics** widget on the dashboard home:

- **Today's Total Requests** - How many API calls today
- **Success Rate %** - Percentage of 2xx responses
- **Errors Today** - Count of 4xx/5xx errors
- **Avg Duration** - Average response time in milliseconds
- **All Time Total** - Total requests since installation

### 2. Analytics Dashboard Page

For detailed analytics, go to:

**Admin Panel** → **Analytics** → **API Analytics**

This page displays:

#### Today's Metrics
- Total requests with visual cards
- Status code distribution (2xx, 3xx, 4xx, 5xx)
- Top performing client
- Average response duration

#### Top Endpoints
- List of most-called endpoints
- Request count per endpoint
- Average duration per endpoint
- Sorted by volume

#### All-Time Stats
- Total requests since launch
- Cumulative metrics

---

## 📋 Request Logs

View detailed logs of every API request made to your system.

### Accessing Request Logs

**Admin Panel** → **API Management** → **Request Logs**

### Log Table Columns

| Column | Description |
|--------|-------------|
| **Time** | When the request was made |
| **Method** | HTTP method (GET, POST, PUT, DELETE, PATCH) |
| **Endpoint** | API path that was called |
| **Status** | HTTP response code (200, 401, 404, 429, 500, etc.) |
| **Client** | Which API client made the request |
| **IP Address** | IP that made the request |
| **Duration** | How long the request took (ms) |
| **User Agent** | Browser/client information |
| **Origin** | HTTP Origin header |

### Viewing Request Details

Click the **View** button on any request to see:

- Complete timestamp
- HTTP method
- Full endpoint path
- Status code
- Total duration
- IP address
- User agent
- Origin header
- Referer header
- Associated API client
- Associated API key

### Filtering & Searching

Filter requests by:

- **Method** - GET, POST, PUT, DELETE, PATCH
- **Status Code** - 2xx Success, 4xx Errors, 5xx Errors
- **API Client** - Filter by specific client
- **Date Range** - Custom date filtering

### Sorting

Click column headers to sort by:
- Time (newest first recommended)
- Status code
- Duration (slowest first)
- Endpoint

---

## 📊 Understanding Metrics

### Status Code Distribution

**Green (2xx Success)**
- ✅ 200 OK - Successful request
- ✅ 201 Created - Resource created
- ✅ 204 No Content - Success, no response body

**Yellow (4xx Client Errors)**
- ⚠️ 400 Bad Request - Invalid parameters
- ⚠️ 401 Unauthorized - Missing/invalid API key
- ⚠️ 403 Forbidden - Origin not allowed
- ⚠️ 404 Not Found - Resource doesn't exist
- ⚠️ 422 Validation Error - Data validation failed
- ⚠️ 429 Too Many Requests - Rate limit exceeded

**Red (5xx Server Errors)**
- ❌ 500 Internal Server Error - Unexpected server error
- ❌ 503 Service Unavailable - Server temporarily down

### Duration Metrics

- **< 50ms** - Excellent (cached responses)
- **50-200ms** - Good (normal operation)
- **200-500ms** - Acceptable (may be processing)
- **> 500ms** - Slow (investigate performance)

Average duration typically indicates:
- **Low** (<100ms) - Well-optimized, likely cached
- **High** (>500ms) - Complex queries, database load, or network issues

---

## 📈 Common Patterns

### High Error Rate (4xx/5xx > 10%)

**Investigate:**
1. Check recent API key changes
2. Review client IP whitelist settings
3. Look for rate limiting hits (429 errors)
4. Check if endpoints are returning validation errors (422)

### Performance Degradation

**If avg duration increased suddenly:**
1. Check recent database migrations
2. Review server resource usage (CPU, memory)
3. Look for slow queries in logs
4. Check if new endpoints were added

### Spike in 401 Errors

**Likely causes:**
- Client using expired/revoked API key
- API key not properly configured
- Origin restriction is blocking requests

**Solution:**
- Verify client's API key is active
- Check client's allowed origins

### Spike in 429 Errors

**Rate limiting is active:**
- Client exceeded request limit
- Rate limit settings may be too strict

**Solutions:**
- Review rate limit thresholds
- Contact client about their usage patterns

---

## 🔍 Analytics Use Cases

### Monitor API Health

**Daily checklist:**
1. ✅ Success rate > 95%?
2. ✅ No unusual error spikes?
3. ✅ Average duration stable?
4. ✅ All key clients still active?

### Capacity Planning

**Analyze trends:**
- How many requests per day on average?
- Growth rate month-over-month?
- Peak hours of the day?
- Largest clients by volume?

### Debugging Issues

**When something goes wrong:**
1. Go to Request Logs
2. Filter by time of incident
3. Look for status code patterns
4. View details of failed requests
5. Check IP address and client

### Security Monitoring

**Watch for suspicious patterns:**
- 401 errors from unknown IPs
- Repeated 403 Forbidden errors
- 429 rate limit hits from same IP
- Unusual request volume spikes

---

## 📚 Related Documentation

- [API Documentation](./API.md) - API endpoints and parameters
- [Promos Module](./PROMOS.md) - Promotional banners
- [Database Schema](./DATABASE.md) - Data structure
- [Debugging & Troubleshooting](../README.md#-debugging--troubleshooting)

---

**Last Updated:** 2026-01-21
**System:** API Analytics v1.0
