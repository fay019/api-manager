<?php

return [
    '404' => [
        'title' => 'Page Not Found',
        'message' => 'The page you are looking for does not exist...',
        'back' => 'Previous Page',
        'back_home' => 'Back to Home',
        'back_previous' => 'Previous Page',
        'help_title' => '💡 What you can do:',
        'help_1' => 'Check the URL for typos',
        'help_2' => 'Return to the homepage and navigate from there',
        'help_3' => 'Use the search or contact us for help',
    ],

    '403' => [
        'title' => 'Access Denied',
        'message' => 'You do not have permission to access this resource...',
        'back' => 'Previous Page',
        'back_home' => 'Back to Home',
        'back_previous' => 'Previous Page',
        'help_title' => '💡 What you can do:',
        'help_1' => 'You don\'t have permission to access this resource',
        'help_2' => 'Contact an administrator if you believe this is an error',
        'help_3' => 'Return to a page you have access to',
    ],

    '401' => [
        'title' => 'Not Authenticated',
        'message' => 'You must be logged in to access this resource...',
        'back' => 'Previous Page',
        'back_home' => 'Back to Home',
        'back_previous' => 'Previous Page',
        'help_title' => '💡 Next steps:',
        'help_1' => 'Log in to your account',
        'help_2' => 'Create a new account if you don\'t have one',
        'help_3' => 'Contact support for help',
    ],

    '419' => [
        'title' => 'Session Expired',
        'message' => 'Your session has expired. Please refresh and try again...',
        'back' => 'Previous Page',
        'back_home' => 'Back to Home',
        'back_previous' => 'Previous Page',
        'help_title' => '💡 What you can do:',
        'help_1' => 'Refresh the page and try again',
        'help_2' => 'Clear your browser cookies and log back in',
        'help_3' => 'Return to the homepage if the problem persists',
    ],

    '503' => [
        'title' => 'Service Unavailable',
        'message' => 'The service is currently under maintenance...',
        'back' => 'Previous Page',
        'back_home' => 'Back to Home',
        'back_previous' => 'Previous Page',
        'debug_title' => '💡 We\'re back soon!',
        'debug_message' => 'Our service is temporarily unavailable for maintenance. Thank you for your patience. We\'ll be back online shortly.',
        'back_online' => 'Back online in',
        'need_help' => 'Need help?',
        'contact_us' => 'Contact us at:',
        'click_to_email' => 'Click to open email',
        'refreshing' => 'Refreshing...',
    ],

    '500' => [
        'title' => 'Server Error',
        'message' => 'An unexpected error occurred...',
        'back' => 'Previous Page',
        'back_home' => 'Back to Home',
        'back_previous' => 'Previous Page',
        'debug_enabled' => 'Debug Mode Enabled',
        'enable_debug' => 'Enable Debug Mode',
        'recent_logs' => 'Recent Logs',
        'full_log' => 'Full Log File',
        'no_logs' => 'No logs found',
    ],

    'maintenance_reasons' => [
        'default' => 'Notre service est temporairement indisponible pour maintenance.',
        'database' => 'Maintenance de la base de données. Nous serons de retour d\'ici 22h.',
        'update' => 'Mise à jour majeure du système en cours.',
    ],
];
