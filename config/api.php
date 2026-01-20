<?php

return [
    'default_rate_limit' => (int) env('API_DEFAULT_RATE_LIMIT', 60),
    'unauthenticated_rate_limit' => (int) env('API_UNAUTH_RATE_LIMIT', 10),
    'promo_cache_ttl' => (int) env('PROMO_CACHE_TTL', 60),
    'request_log_retention_days' => (int) env('API_LOG_RETENTION_DAYS', 90),
    'event_log_retention_days' => (int) env('PROMO_EVENT_RETENTION_DAYS', 180),
];
