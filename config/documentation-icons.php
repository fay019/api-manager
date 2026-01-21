<?php

/**
 * Curated list of documentation icons
 *
 * These are the icons available for selection in the documentation management interface.
 * Add or remove icons as needed - keep the list curated and visually consistent.
 */

return [
    'curated' => [
        // Documentation icons
        '📖' => 'Book - Documentation',
        '📚' => 'Books - Library',
        '📝' => 'Notepad - Notes',
        '📄' => 'Page - Document',

        // Setup and Installation
        '⚙️' => 'Gear - Configuration',
        '🧙' => 'Wizard - Setup',
        '🔧' => 'Wrench - Tools',
        '🛠️' => 'Tools - Maintenance',

        // API and Backend
        '📡' => 'Satellite - API',
        '🔌' => 'Plug - Integration',
        '🌐' => 'Globe - Network',
        '🔗' => 'Link - Connection',

        // Database
        '🗄️' => 'Database - Storage',
        '📊' => 'Chart - Data',
        '💾' => 'Floppy Disk - Save',

        // Modules and Packages
        '📦' => 'Package - Module',
        '🎁' => 'Gift - Feature',

        // Deployment and Production
        '🚀' => 'Rocket - Launch',
        '🌍' => 'Earth - Deployment',
        '☁️' => 'Cloud - Hosting',
        '🖥️' => 'Server - Infrastructure',

        // Security
        '🔐' => 'Lock - Security',
        '🔑' => 'Key - Authentication',
        '🛡️' => 'Shield - Protection',

        // Features and Management
        '⭐' => 'Star - Featured',
        '✨' => 'Sparkles - Enhancement',
        '🎯' => 'Target - Goal',
        '📋' => 'Clipboard - List',

        // General purpose
        '✅' => 'Check - Done',
        '❌' => 'Cross - Error',
        '⚠️' => 'Warning - Alert',
        '💡' => 'Lightbulb - Idea',
    ],

    /**
     * Default icons for specific documentation pages
     * Used during scan and creation if not explicitly set
     */
    'defaults' => [
        'readme' => '📖',
        'installation' => '⚙️',
        'setup_wizard' => '🧙',
        'module_creation' => '📦',
        'api' => '📡',
        'database' => '🗄️',
        'deployment' => '🚀',
        'clients' => '🔑',
        'promos' => '🎯',
    ],

    /**
     * Fallback icon if none is specified
     */
    'fallback' => '📄',
];
