<?php

return [
    '404' => [
        'title' => 'Page non trouvée',
        'message' => 'La page que vous recherchez n\'existe pas...',
        'back' => 'Page précédente',
        'back_home' => 'Retour à l\'accueil',
        'back_previous' => 'Page précédente',
        'help_title' => '💡 Ce que vous pouvez faire :',
        'help_1' => 'Vérifier l\'URL pour les erreurs de frappe',
        'help_2' => 'Retourner à l\'accueil et naviguer à partir de là',
        'help_3' => 'Utiliser la recherche ou nous contacter pour obtenir de l\'aide',
    ],

    '403' => [
        'title' => 'Accès refusé',
        'message' => 'Vous n\'avez pas la permission d\'accéder à cette ressource...',
        'back' => 'Page précédente',
        'back_home' => 'Retour à l\'accueil',
        'back_previous' => 'Page précédente',
        'help_title' => '💡 Ce que vous pouvez faire :',
        'help_1' => 'Vous n\'avez pas la permission d\'accéder à cette ressource',
        'help_2' => 'Contactez un administrateur si vous pensez que c\'est une erreur',
        'help_3' => 'Retournez à une page à laquelle vous avez accès',
    ],

    '401' => [
        'title' => 'Non authentifié',
        'message' => 'Vous devez vous connecter pour accéder à cette ressource...',
        'back' => 'Page précédente',
        'back_home' => 'Retour à l\'accueil',
        'back_previous' => 'Page précédente',
        'help_title' => '💡 Prochaines étapes :',
        'help_1' => 'Se connecter à votre compte',
        'help_2' => 'Créer un nouveau compte si vous n\'en avez pas',
        'help_3' => 'Contacter le support pour obtenir de l\'aide',
    ],

    '419' => [
        'title' => 'Session expirée',
        'message' => 'Votre session a expiré. Veuillez actualiser et réessayer...',
        'back' => 'Page précédente',
        'back_home' => 'Retour à l\'accueil',
        'back_previous' => 'Page précédente',
        'help_title' => '💡 Ce que vous pouvez faire :',
        'help_1' => 'Actualiser la page et réessayer',
        'help_2' => 'Effacer les cookies de votre navigateur et vous reconnecter',
        'help_3' => 'Retourner à l\'accueil si le problème persiste',
    ],

    '503' => [
        'title' => 'Service indisponible',
        'message' => 'Le service est actuellement en maintenance...',
        'back' => 'Page précédente',
        'back_home' => 'Retour à l\'accueil',
        'back_previous' => 'Page précédente',
        'debug_title' => '💡 Nous serons bientôt de retour!',
        'debug_message' => 'Notre service est temporairement indisponible pour maintenance. Merci de votre patience. Nous serons en ligne très bientôt.',
        'back_online' => 'De retour en ligne dans',
        'need_help' => 'Besoin d\'aide ?',
        'contact_us' => 'Contactez-nous à :',
        'click_to_email' => 'Cliquez pour envoyer un e-mail',
        'refreshing' => 'Actualisation...',
    ],

    '500' => [
        'title' => 'Erreur serveur',
        'message' => 'Une erreur inattendue s\'est produite...',
        'back' => 'Page précédente',
        'back_home' => 'Retour à l\'accueil',
        'back_previous' => 'Page précédente',
        'debug_enabled' => 'Mode Debug Activé',
        'enable_debug' => 'Activer le Mode Debug',
        'recent_logs' => 'Logs Récents',
        'full_log' => 'Fichier Log Complet',
        'no_logs' => 'Aucun log trouvé',
    ],

    'maintenance_reasons' => [
        'default' => 'Notre service est temporairement indisponible pour maintenance.',
        'database' => 'Maintenance de la base de données. Nous serons de retour d\'ici 22h.',
        'update' => 'Mise à jour majeure du système en cours.',
    ],
];
