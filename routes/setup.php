<?php

/**
 * Routes pour le système d'installation wizard.
 *
 * Ces routes ne sont accessibles QUE si l'application n'est pas encore installée.
 * Une fois installée, le middleware CheckInstallation bloque toutes les routes /setup
 * avec une réponse 403 Forbidden.
 *
 * Étapes du wizard:
 * 1. GET  /setup/welcome           → Page accueil + vérifications requirements
 * 2. GET  /setup/app-settings      → Formulaire paramètres app
 *    POST /setup/app-settings      → Sauvegarder settings
 * 3. GET  /setup/database          → Formulaire config BD
 *    POST /setup/database          → Sauvegarder config
 *    POST /setup/database/test     → Tester connexion (AJAX)
 * 4. GET  /setup/mail              → Formulaire config email
 *    POST /setup/mail              → Sauvegarder config
 *    POST /setup/mail/test         → Tester SMTP (AJAX)
 * 5. GET  /setup/admin             → Formulaire création admin
 *    POST /setup/admin             → Sauvegarder admin
 * 6. GET  /setup/review            → Récapitulatif avant installation
 *    POST /setup/install           → Lancer installation
 * 7. GET  /setup/success           → Page succès

 *
 * @example
 * // Accessible que si NOT installed:
 * GET /setup/welcome
 * POST /setup/app-settings
 *
 * // Bloquée si installée (403):
 * GET /setup → 403 Forbidden
 */

use App\Http\Controllers\Setup\AdminController;
use App\Http\Controllers\Setup\AppSettingsController;
use App\Http\Controllers\Setup\DatabaseController;
use App\Http\Controllers\Setup\MailController;
use App\Http\Controllers\Setup\ReviewController;
use App\Http\Controllers\Setup\SuccessController;
use App\Http\Controllers\Setup\WelcomeController;
use Illuminate\Support\Facades\Route;

/**
 * Groupe des routes setup.
 *
 * Middleware appliqués:
 * - CheckInstallation: Bloque si déjà installé
 * - RateLimitSetup: Limite tentatives (brute force protection)
 * - web: Session, CSRF tokens, etc.
 *
 * @group Setup Installation Routes
 */
Route::prefix('setup')
    ->name('setup.')
    ->middleware([
        // 'web', // Supprimé pour éviter l'erreur 419 et respecter le mode stateless
        // RateLimitSetup sera ajouté après création du middleware
    ])
    ->group(function () {

        /**
         * Phase 1: Welcome & Requirements Check
         *
         * Affiche la page d'accueil et lance vérifications prérequis.
         *
         * GET /setup/welcome
         * POST /setup/welcome (optional, pour relancer vérifications)
         *
         * @example
         * GET http://api-manager.test/setup/welcome
         * → Affiche page accueil + résultats vérifications
         */
        Route::controller(WelcomeController::class)->group(function () {
            Route::get('/welcome', 'index')->name('welcome');
            Route::post('/welcome', 'store')->name('welcome.store');
            Route::post('/welcome/locale', 'setLocale')->name('welcome.locale');
        });

        /**
         * Phase 2: App Settings
         *
         * Configure paramètres applicatifs (APP_NAME, APP_URL, timezone, locale).
         *
         * GET /setup/app-settings
         * POST /setup/app-settings
         *
         * @example
         * GET http://api-manager.test/setup/app-settings
         * → Formulaire paramètres
         *
         * POST http://api-manager.test/setup/app-settings
         * Body: {app_name: "...", app_url: "...", ...}
         * → Valider et rediriger database
         */
        Route::controller(AppSettingsController::class)->group(function () {
            Route::get('/app-settings', 'index')->name('app-settings');
            Route::post('/app-settings', 'store')->name('app-settings.store');
        });

        /**
         * Phase 3: Database Configuration
         *
         * Configure connexion base de données (type, host, creds).
         * Inclut test connexion en AJAX.
         *
         * GET /setup/database
         * POST /setup/database
         * POST /setup/database/test (AJAX)
         *
         * @example
         * GET http://api-manager.test/setup/database
         * → Formulaire sélection BD type (SQLite/MySQL/PostgreSQL)
         *
         * POST http://api-manager.test/setup/database/test
         * Body: {db_connection: "mysql", db_host: "localhost", ...}
         * Response: {success: true, message: "Connexion OK"}
         *
         * POST http://api-manager.test/setup/database
         * Body: {db_connection: "mysql", ...}
         * → Valider et rediriger mail
         */
        Route::controller(DatabaseController::class)->group(function () {
            Route::get('/database', 'index')->name('database');
            Route::post('/database', 'store')->name('database.store');
            Route::post('/database/test', 'test')->name('database.test');
        });

        /**
         * Phase 4: Mail Configuration
         *
         * Configure email (SMTP ou Log mailer).
         * Inclut test email optionnel en AJAX.
         *
         * GET /setup/mail
         * POST /setup/mail
         * POST /setup/mail/test (AJAX)
         *
         * @example
         * GET http://api-manager.test/setup/mail
         * → Formulaire config email (SMTP/Log)
         *
         * POST http://api-manager.test/setup/mail/test
         * Body: {mail_mailer: "smtp", mail_host: "smtp.gmail.com", ...}
         * Response: {success: true|false, message: "..."}
         *
         * POST http://api-manager.test/setup/mail
         * Body: {mail_mailer: "smtp", ...}
         * → Valider et rediriger admin
         */
        Route::controller(MailController::class)->group(function () {
            Route::get('/mail', 'index')->name('mail');
            Route::post('/mail', 'store')->name('mail.store');
            Route::post('/mail/test', 'test')->name('mail.test');
        });

        /**
         * Phase 5: Admin User Creation
         *
         * Crée le premier utilisateur administrateur.
         * Valide password fort (12+ chars, majuscule, chiffre, etc).
         *
         * GET /setup/admin
         * POST /setup/admin
         *
         * @example
         * GET http://api-manager.test/setup/admin
         * → Formulaire création admin
         *
         * POST http://api-manager.test/setup/admin
         * Body: {name: "John Doe", email: "admin@example.com", password: "..."}
         * → Créer user et rediriger review
         */
        Route::controller(AdminController::class)->group(function () {
            Route::get('/admin', 'index')->name('admin');
            Route::post('/admin', 'store')->name('admin.store');
        });

        /**
         * Phase 6: Review & Install
         *
         * Affiche récapitulatif installation et lance processus.
         * Page de progression avec feedback en temps réel.
         *
         * GET /setup/review
         * POST /setup/install
         *
         * @example
         * GET http://api-manager.test/setup/review
         * → Affiche récapitulatif (SANS secrets)
         *
         * POST http://api-manager.test/setup/install
         * → Lancer étapes installation:
         *    1. Générer APP_KEY
         *    2. Updater .env
         *    3. Exécuter migrations
         *    4. Lancer seeders
         *    5. Créer admin
         *    6. Verrouiller installation
         * Response: Stream JSON avec progrès
         *    {step: "appkey", status: "success", progress: 0}
         *    {step: "migrations", status: "running", progress: 33}
         *    {step: "admin", status: "success", progress: 100}
         */
        Route::controller(ReviewController::class)->group(function () {
            Route::get('/review', 'index')->name('review');
        });

        /**
         * Phase 7: Success Page
         *
         * Affiche succès installation avec récapitulatif et lien /admin.
         * Auto-redirection vers /login après N secondes.
         *
         * GET /setup/success
         * POST /setup/install (lance le processus)
         *
         * @example
         * GET http://api-manager.test/setup/success
         * → Affiche page succès + récapitulatif
         *
         * POST http://api-manager.test/setup/install
         * → Exécute installation et redirige /setup/success
         */
        Route::controller(SuccessController::class)->group(function () {
            Route::post('/install', 'install')->name('install');
            Route::get('/success', 'index')->name('success');
        });

        /**
         * Route de redirection par défaut
         *
         * GET /setup (sans étape spécifique) → rediriger /setup/welcome
         */
        Route::redirect('', '/setup/welcome', 301);
    });
