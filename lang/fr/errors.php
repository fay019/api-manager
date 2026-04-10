<?php

return [
    '404' => [
        'title' => 'Page non trouvée',
        'message' => 'La page que vous recherchez n\'existe pas...',
        'back' => '← Page précédente',
    ],

    '403' => [
        'title' => 'Accès refusé',
        'message' => 'Vous n\'avez pas la permission d\'accéder à cette ressource...',
        'back' => '← Page précédente',
    ],

    '401' => [
        'title' => 'Non authentifié',
        'message' => 'Vous devez vous connecter pour accéder à cette ressource...',
        'back' => '← Page précédente',
    ],

    '419' => [
        'title' => 'Session expirée',
        'message' => 'Votre session a expiré. Veuillez actualiser et réessayer...',
        'back' => '← Page précédente',
    ],

    '503' => [
        'title' => 'Service indisponible',
        'message' => 'Le service est actuellement en maintenance...',
        'back' => '← Page précédente',
    ],

    '500' => [
        'title' => 'Erreur serveur',
        'message' => 'Une erreur inattendue s\'est produite...',
        'back' => '← Page précédente',
        'debug_enabled' => 'Mode Debug Activé',
        'enable_debug' => 'Activer le Mode Debug',
        'recent_logs' => 'Logs Récents',
        'full_log' => 'Fichier Log Complet',
        'no_logs' => 'Aucun log trouvé',
    ],
];
