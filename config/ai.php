<?php

return [
    'ollama' => [
        'url' => env('OLLAMA_URL', 'https://ia.fayotech.com'),
        'default_model' => env('OLLAMA_DEFAULT_MODEL', 'llama3.2:3b'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
        'allowed_models' => explode(',', env('OLLAMA_ALLOWED_MODELS', 'llama3.2:3b')),
        'internal_token' => env('OLLAMA_INTERNAL_TOKEN'),
    ],
];
