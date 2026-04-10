<?php

return [
    '404' => [
        'title' => 'Seite nicht gefunden',
        'message' => 'Die gesuchte Seite existiert nicht...',
        'back' => '← Vorherige Seite',
    ],

    '403' => [
        'title' => 'Zugriff verweigert',
        'message' => 'Sie haben keine Berechtigung für diese Ressource...',
        'back' => '← Vorherige Seite',
    ],

    '401' => [
        'title' => 'Nicht authentifiziert',
        'message' => 'Sie müssen angemeldet sein, um auf diese Ressource zuzugreifen...',
        'back' => '← Vorherige Seite',
    ],

    '419' => [
        'title' => 'Sitzung abgelaufen',
        'message' => 'Ihre Sitzung ist abgelaufen. Bitte aktualisieren Sie und versuchen Sie es erneut...',
        'back' => '← Vorherige Seite',
    ],

    '503' => [
        'title' => 'Service nicht verfügbar',
        'message' => 'Der Service wird derzeit gewartet...',
        'back' => '← Vorherige Seite',
    ],

    '500' => [
        'title' => 'Serverfehler',
        'message' => 'Ein unerwarteter Fehler ist aufgetreten...',
        'back' => '← Vorherige Seite',
        'debug_enabled' => 'Debug-Modus aktiviert',
        'enable_debug' => 'Debug-Modus aktivieren',
        'recent_logs' => 'Aktuelle Protokolle',
        'full_log' => 'Vollständige Protokolldatei',
        'no_logs' => 'Keine Protokolle gefunden',
    ],
];
