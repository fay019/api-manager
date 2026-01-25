<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setup\DatabaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur pour la Phase 3 du wizard: Database Configuration.
 *
 * Configure la connexion base de données (sqlite/mysql/pgsql).
 *
 * Responsabilités:
 * - Afficher formulaire avec sélection driver
 * - Valider configuration et tester connexion (AJAX)
 * - Stocker en session pour étape suivante
 * - Support multi-BD: SQLite, MySQL, PostgreSQL
 *
 * Drivers supportés:
 * - sqlite: base de données fichier local (database/database.sqlite)
 * - mysql: TCP/IP avec host, port, user, pass
 * - pgsql: TCP/IP avec host, port, user, pass
 *
 * @example
 * // Route: GET /setup/database
 * // Affiche formulaire avec pré-remplissage session
 *
 * // Route: POST /setup/database/test (AJAX)
 * // Body: {database_driver: "mysql", database_host: "localhost", ...}
 * // Response: {success: bool, message: string, errors: array}
 *
 * // Route: POST /setup/database
 * // Body: Données du formulaire + CSRF token
 * // Stocke session + redirection /setup/mail
 */
class DatabaseController extends Controller
{
    /**
     * Affiche le formulaire de configuration base de données.
     *
     * Actions:
     * - Détecte driver depuis session ou défaut (sqlite)
     * - Pré-remplit avec données session si présentes
     * - Fourni listes drivers, ports par défaut
     * - Retourne vue avec formulaire interactif
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue du formulaire database
     */
    public function index(Request $request): View
    {
        $setupSession = app(\App\Services\Installation\SetupSession::class);

        \Log::channel('installation')->info('🗄️ ÉTAPE 3: Database GET /setup/database', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'has_database_driver_session' => (bool) $setupSession->get('setup.database_driver'),
        ]);

        // Déterminer driver par défaut
        $defaultDriver = 'sqlite';

        // Pré-remplissage depuis session setup (stateless)
        $formData = [
            'database_driver' => $setupSession->get('setup.database_driver', $defaultDriver),
            'database_host' => $setupSession->get('setup.database_host', ''),
            'database_port' => $setupSession->get('setup.database_port', $this->getDefaultPort('mysql')),
            'database_database' => $setupSession->get('setup.database_database', ''),
            'database_username' => $setupSession->get('setup.database_username', ''),
            'database_password' => $setupSession->get('setup.database_password', ''),
        ];

        // Configurations disponibles par driver
        $drivers = [
            'sqlite' => [
                'name' => 'SQLite (Fichier local)',
                'description' => 'Base de données fichier local, idéale pour développement',
                'default_port' => null,
                'requires_host' => false,
                'requires_username' => false,
            ],
            'mysql' => [
                'name' => 'MySQL',
                'description' => 'Serveur MySQL (5.7+) ou MariaDB',
                'default_port' => 3306,
                'requires_host' => true,
                'requires_username' => true,
            ],
            'pgsql' => [
                'name' => 'PostgreSQL',
                'description' => 'Serveur PostgreSQL (10+)',
                'default_port' => 5432,
                'requires_host' => true,
                'requires_username' => true,
            ],
        ];

        return view('setup.steps.database', [
            'formData' => $formData,
            'drivers' => $drivers,
            'currentStep' => 3,
            'totalSteps' => 7,
            'errors' => $setupSession->get('errors', []),
        ]);
    }

    /**
     * Sauvegarde la configuration base de données.
     *
     * Actions:
     * - Valider données
     * - Vérifier cohérence par driver
     * - Stocker en session
     * - Rediriger /setup/mail
     *
     * @param  Request  $request  Requête HTTP
     * @return RedirectResponse Redirection vers /setup/mail
     */
    public function store(Request $request): RedirectResponse
    {
        $setupSession = app(\App\Services\Installation\SetupSession::class);

        \Log::channel('installation')->info('📝 ÉTAPE 3: Database POST /setup/database', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'database_driver' => $request->input('database_driver'),
        ]);

        // Valider données manuellement
        $validator = \Validator::make($request->all(), [
            'database_driver' => 'required|in:sqlite,mysql,pgsql',
            'database_host' => 'required_if:database_driver,mysql,pgsql|nullable|string',
            'database_port' => 'required_if:database_driver,mysql,pgsql|nullable|integer',
            'database_database' => 'required_if:database_driver,mysql,pgsql|nullable|string',
            'database_username' => 'required_if:database_driver,mysql,pgsql|nullable|string',
            'database_password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $setupSession->set('errors', $validator->errors()->toArray());
            return redirect()->back()->withInput();
        }

        $validated = $validator->validated();
        $setupSession->set('errors', []);

        // Pour SQLite, utiliser le chemin par défaut
        $databaseName = $validated['database_database'] ?? 'database.sqlite';
        if ($validated['database_driver'] === 'sqlite') {
            $databaseName = 'database.sqlite';
        }

        // Stocker en session setup
        $setupSession->set('setup.database_driver', $validated['database_driver']);
        $setupSession->set('setup.database_host', $validated['database_host'] ?? null);
        $setupSession->set('setup.database_port', $validated['database_port'] ?? null);
        $setupSession->set('setup.database_database', $databaseName);
        $setupSession->set('setup.database_username', $validated['database_username'] ?? null);
        $setupSession->set('setup.database_password', $validated['database_password'] ?? null);

        // Rediriger vers étape suivante
        return redirect()->route('setup.mail', ['setup_token' => $setupSession->getToken()]);
    }

    /**
     * Teste la connexion base de données (endpoint AJAX).
     *
     * Actions:
     * - Récupérer données depuis request
     * - Construire DSN selon driver
     * - Tenter connexion PDO
     * - Retourner statut + détails erreur
     *
     * Réponse:
     * - {success: true, message: "..."}
     * - {success: false, errors: {field: message}}
     *
     * @param  Request  $request  Requête avec données de test
     * @return JsonResponse Statut de la connexion
     */
    public function test(Request $request): JsonResponse
    {
        // Valider données minimales
        $driver = $request->input('database_driver');

        if (! in_array($driver, ['sqlite', 'mysql', 'pgsql'])) {
            return response()->json([
                'success' => false,
                'errors' => ['database_driver' => 'Driver invalide'],
            ], 422);
        }

        try {
            // Construire et tester connexion selon driver
            $dsn = $this->buildDsn($driver, $request);

            $options = [
                \PDO::ATTR_TIMEOUT => 5,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];

            // Tentative de connexion
            $pdo = new \PDO(
                $dsn,
                $request->input('database_username'),
                $request->input('database_password'),
                $options
            );

            // Test requête simple
            $pdo->query('SELECT 1');

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
            ]);
        } catch (\PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connexion échouée',
                'errors' => [
                    'connection' => $this->formatPdoError($e),
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test',
                'errors' => [
                    'exception' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Construit le DSN PDO selon le driver.
     *
     * SQLite: sqlite:/path/to/database.db
     * MySQL:  mysql:host=localhost;port=3306;dbname=api_manager
     * PgSQL:  pgsql:host=localhost;port=5432;dbname=api_manager
     *
     * @param  string  $driver  Driver (sqlite|mysql|pgsql)
     * @param  Request  $request  Requête avec paramètres
     * @return string DSN formaté pour PDO
     */
    private function buildDsn(string $driver, Request $request): string
    {
        return match ($driver) {
            'sqlite' => $this->buildSqliteDsn($request),
            'mysql' => $this->buildMysqlDsn($request),
            'pgsql' => $this->buildPgsqlDsn($request),
            default => throw new \InvalidArgumentException("Driver non supporté: {$driver}"),
        };
    }

    /**
     * Construit DSN SQLite.
     *
     * Pour SQLite, on utilise toujours le chemin par défaut de Laravel: database/database.sqlite
     * Cela évite les conflits et complications avec les noms personnalisés.
     *
     * Format: sqlite:path/to/database.db
     *
     * @param  Request  $request  Requête (ignorée pour SQLite, utilise le chemin par défaut)
     * @return string DSN SQLite
     */
    private function buildSqliteDsn(Request $request): string
    {
        // Utiliser le chemin par défaut de Laravel pour SQLite
        $database = database_path('database.sqlite');

        // Créer le répertoire s'il n'existe pas
        $dir = dirname($database);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Créer le fichier s'il n'existe pas (PDO ne le crée pas automatiquement)
        if (! file_exists($database)) {
            touch($database);
        }

        return "sqlite:{$database}";
    }

    /**
     * Construit DSN MySQL.
     *
     * Format: mysql:host=localhost;port=3306;dbname=api_manager
     *
     * @param  Request  $request  Requête avec host, port, database_database
     * @return string DSN MySQL
     */
    private function buildMysqlDsn(Request $request): string
    {
        $host = $request->input('database_host', 'localhost');
        $port = $request->input('database_port', 3306);
        $database = $request->input('database_database', 'api_manager');

        return "mysql:host={$host};port={$port};dbname={$database}";
    }

    /**
     * Construit DSN PostgreSQL.
     *
     * Format: pgsql:host=localhost;port=5432;dbname=api_manager
     *
     * @param  Request  $request  Requête avec host, port, database_database
     * @return string DSN PostgreSQL
     */
    private function buildPgsqlDsn(Request $request): string
    {
        $host = $request->input('database_host', 'localhost');
        $port = $request->input('database_port', 5432);
        $database = $request->input('database_database', 'api_manager');

        return "pgsql:host={$host};port={$port};dbname={$database}";
    }

    /**
     * Retourne le port par défaut pour un driver.
     *
     * @param  string  $driver  Driver (mysql|pgsql|sqlite)
     * @return int|null Port par défaut ou null
     */
    private function getDefaultPort(string $driver): ?int
    {
        return match ($driver) {
            'mysql' => 3306,
            'pgsql' => 5432,
            default => null,
        };
    }

    /**
     * Formate un message d'erreur PDO de manière lisible.
     *
     * Masque les détails sensibles et propose des solutions.
     *
     * @param  \PDOException  $e  Exception PDO
     * @return string Message formaté pour l'utilisateur
     */
    private function formatPdoError(\PDOException $e): string
    {
        $message = $e->getMessage();

        // Masquer données sensibles
        if (str_contains($message, 'password')) {
            return 'Authentification échouée (vérifier nom d\'utilisateur et mot de passe)';
        }

        if (str_contains($message, 'Unknown database')) {
            return 'Base de données inexistante (la créer avant de continuer)';
        }

        if (str_contains($message, 'Lost connection')) {
            return 'Connexion perdue avec le serveur (vérifier serveur en ligne)';
        }

        if (str_contains($message, 'Access denied')) {
            return 'Accès refusé (vérifier permissions utilisateur)';
        }

        if (str_contains($message, 'Connection refused')) {
            return 'Connexion refusée (vérifier host et port)';
        }

        if (str_contains($message, 'SQLSTATE[28000]')) {
            return 'Erreur d\'authentification';
        }

        // Message par défaut sûr
        return 'Connexion échouée (vérifier paramètres de connexion)';
    }
}
