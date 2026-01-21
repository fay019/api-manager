<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Controller pour le Setup Wizard (première installation).
 *
 * Affiche un formulaire pour configurer l'application
 * à la première visite.
 */
class SetupController extends Controller
{
    /**
     * Affiche la page d'installation.
     */
    public function index()
    {
        // Si déjà installé, rediriger vers home
        if ($this->isInstalled()) {
            return redirect()->route('home');
        }

        return view('setup.index');
    }

    /**
     * Étape 1: Infos générales.
     */
    public function stepGeneral()
    {
        if ($this->isInstalled()) {
            return redirect()->route('home');
        }

        // Récupérer les types de base de données disponibles
        $databases = ['sqlite', 'mysql', 'pgsql'];

        return view('setup.step-general', [
            'databases' => $databases,
            'current_db' => config('database.default'),
        ]);
    }

    /**
     * Sauvegarde infos générales (étape 1).
     */
    public function saveGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|min:3|max:255',
            'site_url' => 'required|url',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
            'db_connection' => 'required|in:sqlite,mysql,pgsql',
        ], [
            'site_name.required' => 'Le nom du site est requis',
            'site_url.required' => 'L\'URL du site est requise',
            'site_url.url' => 'L\'URL doit être valide',
            'admin_email.required' => 'L\'email admin est requis',
            'admin_email.email' => 'L\'email doit être valide',
            'admin_password.required' => 'Le mot de passe est requis',
            'admin_password.min' => 'Le mot de passe doit faire au moins 8 caractères',
            'admin_password.confirmed' => 'Les mots de passe ne correspondent pas',
            'db_connection.required' => 'Vous devez choisir un type de base de données',
        ]);

        // Stocker en session
        session(['setup.site_name' => $validated['site_name']]);
        session(['setup.site_url' => $validated['site_url']]);
        session(['setup.admin_email' => $validated['admin_email']]);
        session(['setup.admin_password' => $validated['admin_password']]);
        session(['setup.db_connection' => $validated['db_connection']]);

        // Si SQLite, passer à la confirmation
        // Si MySQL/PostgreSQL, demander les détails de connexion
        if ($validated['db_connection'] === 'sqlite') {
            return redirect()->route('setup.confirm');
        }

        return redirect()->route('setup.database');
    }

    /**
     * Étape 2: Configuration base de données (si MySQL/PostgreSQL).
     */
    public function stepDatabase()
    {
        if ($this->isInstalled()) {
            return redirect()->route('home');
        }

        $dbConnection = session('setup.db_connection', 'sqlite');

        // Si SQLite, rediriger vers la confirmation
        if ($dbConnection === 'sqlite') {
            return redirect()->route('setup.confirm');
        }

        return view('setup.step-database', [
            'db_connection' => $dbConnection,
            'port' => $dbConnection === 'mysql' ? 3306 : 5432,
        ]);
    }

    /**
     * Teste la connexion base de données.
     */
    public function testDatabase(Request $request)
    {
        $validated = $request->validate([
            'db_connection' => 'required|in:sqlite,mysql,pgsql',
            'db_host' => 'nullable|string',
            'db_port' => 'nullable|integer',
            'db_database' => 'required|string',
            'db_username' => 'nullable|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            // Tester la connexion
            $config = [
                'driver' => $validated['db_connection'],
            ];

            if ($validated['db_connection'] !== 'sqlite') {
                $config['host'] = $validated['db_host'] ?? 'localhost';
                $config['port'] = $validated['db_port'] ?? 3306;
                $config['database'] = $validated['db_database'];
                $config['username'] = $validated['db_username'] ?? 'root';
                $config['password'] = $validated['db_password'] ?? '';
            } else {
                $config['database'] = database_path($validated['db_database']);
            }

            // Tenter une connexion
            DB::purge('setup_test');
            DB::addConnection($config, 'setup_test');
            DB::connection('setup_test')->getPdo();

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Sauvegarde config base de données (étape 2).
     */
    public function saveDatabase(Request $request)
    {
        $validated = $request->validate([
            'db_connection' => 'required|in:sqlite,mysql,pgsql',
            'db_host' => 'nullable|string',
            'db_port' => 'nullable|integer',
            'db_database' => 'required|string',
            'db_username' => 'nullable|string',
            'db_password' => 'nullable|string',
        ]);

        // Stocker en session
        session(['setup.db_connection' => $validated['db_connection']]);
        session(['setup.db_host' => $validated['db_host'] ?? 'localhost']);
        session(['setup.db_port' => $validated['db_port'] ?? ($validated['db_connection'] === 'mysql' ? 3306 : 5432)]);
        session(['setup.db_database' => $validated['db_database']]);
        session(['setup.db_username' => $validated['db_username'] ?? 'root']);
        session(['setup.db_password' => $validated['db_password'] ?? '']);

        return redirect()->route('setup.confirm');
    }

    /**
     * Étape 3: Confirmation et installation.
     */
    public function stepConfirm()
    {
        if ($this->isInstalled()) {
            return redirect()->route('home');
        }

        // Récupérer les données depuis la session (clés imbriquées)
        $setup = [];
        $setup['setup.site_name'] = session('setup.site_name');
        $setup['setup.site_url'] = session('setup.site_url');
        $setup['setup.admin_email'] = session('setup.admin_email');
        $setup['setup.db_connection'] = session('setup.db_connection');
        $setup['setup.db_host'] = session('setup.db_host');
        $setup['setup.db_port'] = session('setup.db_port');
        $setup['setup.db_database'] = session('setup.db_database');
        $setup['setup.db_username'] = session('setup.db_username');

        // Vérifier que les informations générales sont remplies
        if (!session('setup.site_name') || !session('setup.site_url') || !session('setup.admin_email') || !session('setup.db_connection')) {
            return redirect()->route('setup.general');
        }

        // Vérifier les infos de base de données selon le type choisi
        $dbConnection = session('setup.db_connection', 'sqlite');
        if ($dbConnection !== 'sqlite') {
            if (!session('setup.db_database') || !session('setup.db_host')) {
                return redirect()->route('setup.database');
            }
        }

        return view('setup.step-confirm', [
            'setup' => $setup,
            'db_connection' => $dbConnection,
        ]);
    }

    /**
     * Finalise l'installation.
     */
    public function finish(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect()->route('home');
        }

        try {
            // Récupérer la config depuis la session
            $siteName = session('setup.site_name', 'API Manager');
            $siteUrl = session('setup.site_url', 'http://localhost:8000');
            $adminEmail = session('setup.admin_email');
            $adminPassword = session('setup.admin_password');
            $dbConnection = session('setup.db_connection', 'sqlite');
            $dbHost = session('setup.db_host', 'localhost');
            $dbPort = session('setup.db_port', $dbConnection === 'mysql' ? 3306 : 5432);
            $dbDatabase = session('setup.db_database', 'database');
            $dbUsername = session('setup.db_username', 'root');
            $dbPassword = session('setup.db_password', '');

            // Vérifier que les infos obligatoires sont présentes
            if (!$adminEmail || !$adminPassword) {
                throw new \Exception('Les informations d\'administration sont manquantes. Veuillez recommencer l\'installation.');
            }

            // Installer les dépendances Composer si nécessaire
            $this->ensureComposerDependencies();

            // Générer la clé APP_KEY si nécessaire
            $this->ensureAppKey();

            // Créer les répertoires nécessaires
            $this->ensureDirectoriesExist();

            // Créer la base de données SQLite si nécessaire (middleware l'a peut-être déjà fait)
            if ($dbConnection === 'sqlite') {
                $dbPath = database_path($dbDatabase);
                if (!file_exists($dbPath)) {
                    touch($dbPath);
                }
                // S'assurer que les tables de session existent
                try {
                    $pdo = new \PDO("sqlite:{$dbPath}");
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $pdo->exec('CREATE TABLE IF NOT EXISTS sessions (
                        id TEXT PRIMARY KEY,
                        user_id INTEGER,
                        ip_address VARCHAR(45),
                        user_agent TEXT,
                        payload LONGTEXT,
                        last_activity INTEGER
                    )');
                } catch (\Exception $e) {
                    // Ignorer - les migrations créeront les tables
                }
            }

            // Mettre à jour .env
            $this->updateEnv([
                'APP_NAME' => $siteName,
                'APP_URL' => $siteUrl,
                'DB_CONNECTION' => $dbConnection,
                'DB_HOST' => $dbConnection === 'sqlite' ? null : $dbHost,
                'DB_PORT' => $dbConnection === 'sqlite' ? null : $dbPort,
                'DB_DATABASE' => $dbConnection === 'sqlite' ? database_path($dbDatabase) : $dbDatabase,
                'DB_USERNAME' => $dbConnection === 'sqlite' ? null : $dbUsername,
                'DB_PASSWORD' => $dbConnection === 'sqlite' ? null : $dbPassword,
            ]);

            // Exécuter les migrations
            Artisan::call('migrate', ['--force' => true]);

            // Lancer les seeders
            Artisan::call('db:seed', ['--force' => true]);

            // Créer l'utilisateur admin
            $admin = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make($adminPassword),
                    'is_admin' => true,
                    'email_verified_at' => now(),
                ]
            );

            // Créer le fichier installed.lock
            $this->markAsInstalled();

            // Nettoyer la session
            session()->forget('setup');

            // Rediriger vers login
            return redirect()->route('filament.admin.auth.login')
                ->with('success', 'Installation réussie! Veuillez vous connecter avec vos identifiants.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors de l\'installation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Génère la clé APP_KEY si nécessaire.
     */
    protected function ensureAppKey(): void
    {
        // Vérifier si APP_KEY est déjà définie
        if (config('app.key') && config('app.key') !== 'base64:') {
            return;
        }

        // Générer la clé
        Artisan::call('key:generate');
    }

    /**
     * Installe les dépendances Composer si nécessaire.
     */
    protected function ensureComposerDependencies(): void
    {
        // Vérifier si vendor/ existe déjà
        if (is_dir(base_path('vendor'))) {
            return;
        }

        // Déterminer si on est en production ou développement
        $isDev = app()->environment('local', 'development');
        $cmd = $isDev ? 'composer install' : 'composer install --no-dev --optimize-autoloader';

        // Exécuter composer install
        $output = shell_exec("{$cmd} 2>&1");

        if ($output === null) {
            throw new \Exception('Impossible de lancer composer install. Assurez-vous que Composer est installé.');
        }
    }

    /**
     * Vérifie et crée les répertoires nécessaires.
     */
    protected function ensureDirectoriesExist(): void
    {
        $directories = [
            'storage/framework/cache',
            'storage/framework/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/framework/testing',
            'storage/logs',
            'storage/app',
            'bootstrap/cache',
        ];

        foreach ($directories as $dir) {
            $path = base_path($dir);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Met à jour le fichier .env.
     */
    protected function updateEnv(array $values): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $content = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            // Échapper les valeurs
            if (str_contains($value, ' ')) {
                $value = "\"{$value}\"";
            }

            // Remplacer ou ajouter la ligne
            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }

    /**
     * Crée le fichier installed.lock.
     */
    protected function markAsInstalled(): void
    {
        $lockFile = storage_path('app/installed.lock');

        // Créer le répertoire s'il n'existe pas
        if (!is_dir(dirname($lockFile))) {
            mkdir(dirname($lockFile), 0755, true);
        }

        // Créer le fichier avec metadata
        $data = [
            'installed_at' => now()->toIso8601String(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
        ];

        file_put_contents($lockFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Vérifie si l'application est installée.
     */
    protected function isInstalled(): bool
    {
        return file_exists(storage_path('app/installed.lock'));
    }
}
