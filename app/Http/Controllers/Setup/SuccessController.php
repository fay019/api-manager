<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Installation\EnvManager;
use App\Services\Installation\SetupSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Contrôleur pour la Phase Finale du wizard: Installation & Success.
 *
 * Exécute les actions techniques finales pour mettre l'application en service.
 */
class SuccessController extends Controller
{
    /**
     * Exécute l'installation technique (Step 7).
     */
    public function install(Request $request): RedirectResponse
    {
        $setupSession = app(SetupSession::class);

        \Log::channel('installation')->info('⚙️ ÉTAPE 7: Install POST /setup/install', [
            'setup_token' => $setupSession->getToken(),
            'has_all_setup_data' => (bool) $setupSession->get('setup.app_name'),
        ]);

        try {
            // Récupérer toutes les données de session setup
            $appSettings = [
                'name' => $setupSession->get('setup.app_name'),
                'url' => $setupSession->get('setup.app_url'),
                'env' => $setupSession->get('setup.app_env'),
                'debug' => $setupSession->get('setup.app_debug', false),
                'timezone' => $setupSession->get('setup.timezone'),
                'locale' => $setupSession->get('setup.locale'),
            ];

            $database = [
                'driver' => $setupSession->get('setup.database_driver'),
                'host' => $setupSession->get('setup.database_host'),
                'port' => $setupSession->get('setup.database_port'),
                'database' => $setupSession->get('setup.database_database'),
                'username' => $setupSession->get('setup.database_username'),
                'password' => $setupSession->get('setup.database_password'),
            ];

            $mail = [
                'driver' => $setupSession->get('setup.mail_driver'),
                'host' => $setupSession->get('setup.mail_host'),
                'port' => $setupSession->get('setup.mail_port'),
                'username' => $setupSession->get('setup.mail_username'),
                'password' => $setupSession->get('setup.mail_password'),
                'encryption' => $setupSession->get('setup.mail_encryption'),
                'from_address' => $setupSession->get('setup.mail_from_address'),
                'from_name' => $setupSession->get('setup.mail_from_name'),
                'path' => $setupSession->get('setup.mail_path'),
            ];

            $admin = [
                'name' => $setupSession->get('setup.admin_name'),
                'email' => $setupSession->get('setup.admin_email'),
                'password' => $setupSession->get('setup.admin_password'),
            ];

            // 1. Valider données
            $this->validateInstallationData($appSettings, $database, $mail, $admin);

            // 2. Générer APP_KEY une seule fois
            $appKey = 'base64:'.base64_encode(random_bytes(32));

            // 3. Configurer .env
            $envManager = new EnvManager;
            $this->configureEnvironment($envManager, array_merge($appSettings, ['key' => $appKey]), $database, $mail);

            // 4. Injecter config runtime
            config([
                'app.key' => $appKey,
                'app.name' => $appSettings['name'],
                'app.url' => $appSettings['url'],
                'database.connections.sqlite.database' => database_path($database['database']),
            ]);

            // 5. Préparer base de données
            $dbPath = database_path($database['database']);
            $dbDir = dirname($dbPath);

            DB::disconnect();

            if ($database['driver'] === 'sqlite') {
                if (! is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }

                if (! is_writable($dbDir)) {
                    throw new \Exception("Le dossier database/ n'est pas accessible en écriture.");
                }

                if (file_exists($dbPath)) {
                    @unlink($dbPath);
                }
                touch($dbPath);
                chmod($dbPath, 0664);
            }

            // 6. Migrations + Seed
            Artisan::call('migrate', ['--force' => true]);
            $this->createAdminUser($admin);

            // PUBLISH ASSETS to avoid "Unable to locate a class or view for component" in production
            Artisan::call('vendor:publish', ['--tag' => 'laravel-exceptions-renderer-views', '--force' => true]);
            Artisan::call('filament:assets');

            // 7. Create Lock BEFORE cleaning cache
            // This ensures InstallationServiceProvider sees the app as installed
            // when optimize is called.
            $lockData = [
                'installed_at' => now()->toDateTimeString(),
                'version' => '1.0.0',
            ];
            file_put_contents(storage_path('app/installed.lock'), json_encode($lockData));

            // 8. Clear cache and Optimize
            Artisan::call('optimize:clear');
            Artisan::call('optimize');

            // 9. Flush setup session
            $setupSession->flush();

            // 10. Redirect
            return redirect()->route('login.show')
                ->withCookie(cookie()->forget('api_manager_setup_token'))
                ->withCookie(cookie()->forget(Str::slug($appSettings['name']).'_session'))
                ->header('Clear-Site-Data', '"cookies", "storage"');
        } catch (\Exception $e) {
            \Log::channel('installation')->error('❌ ÉCHEC INSTALLATION: '.$e->getMessage());

            $setupSession = app(SetupSession::class);
            $setupSession->set('errors', ['error' => [$e->getMessage()]]);

            return redirect()->back()->withInput();
        }
    }

    /**
     * Affiche la page de succès.
     */
    public function index(Request $request): View
    {
        return view('setup.steps.success', [
            'currentStep' => 7,
            'totalSteps' => 7,
        ]);
    }

    /**
     * Valide que toutes les données nécessaires sont présentes.
     */
    private function validateInstallationData(array $appSettings, array $database, array $mail, array $admin): void
    {
        $missing = [];

        if (! $appSettings['name']) {
            $missing[] = 'Nom application';
        }
        if (! $database['driver']) {
            $missing[] = 'Type base de données';
        }
        if (! $mail['driver']) {
            $missing[] = 'Type email';
        }
        if (! $admin['name'] || ! $admin['email'] || ! $admin['password']) {
            $missing[] = 'Données administrateur';
        }

        if (! empty($missing)) {
            throw new \Exception('Données manquantes: '.implode(', ', $missing));
        }
    }

    /**
     * Configure le fichier .env avec toutes les données.
     */
    private function configureEnvironment(EnvManager $envManager, array $appSettings, array $database, array $mail): void
    {
        $envManager->update([
            'APP_NAME' => $appSettings['name'],
            'APP_URL' => $appSettings['url'],
            'APP_ENV' => $appSettings['env'],
            'APP_KEY' => $appSettings['key'],
            'APP_DEBUG' => $appSettings['debug'] ? 'true' : 'false',
            'APP_ALLOW_PRODUCTION_RESET' => ($appSettings['allow_production_reset'] ?? false) ? 'true' : 'false',
            'APP_TIMEZONE' => $appSettings['timezone'],
            'APP_LOCALE' => $appSettings['locale'],
            'DB_CONNECTION' => $database['driver'],
        ]);

        if ($database['driver'] === 'sqlite') {
            $envManager->update(['DB_DATABASE' => database_path('database.sqlite')]);
        } else {
            $envManager->update([
                'DB_HOST' => $database['host'],
                'DB_PORT' => $database['port'],
                'DB_DATABASE' => $database['database'],
                'DB_USERNAME' => $database['username'],
                'DB_PASSWORD' => $database['password'],
            ]);
        }

        $envManager->update([
            'MAIL_MAILER' => $mail['driver'],
            'MAIL_HOST' => $mail['host'],
            'MAIL_PORT' => $mail['port'],
            'MAIL_USERNAME' => $mail['username'],
            'MAIL_PASSWORD' => $mail['password'],
            'MAIL_ENCRYPTION' => $mail['encryption'],
            'MAIL_FROM_ADDRESS' => $mail['from_address'],
            'MAIL_FROM_NAME' => $mail['from_name'],
        ]);

        $envManager->flushCache();
    }

    /**
     * Crée le premier utilisateur administrateur.
     */
    private function createAdminUser(array $admin): void
    {
        try {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make($admin['password']),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Erreur création utilisateur admin: '.$e->getMessage());
        }
    }
}
