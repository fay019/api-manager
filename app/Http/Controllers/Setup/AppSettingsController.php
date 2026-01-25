<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur pour la Phase 2 du wizard: App Settings.
 *
 * Configure les paramètres applicatifs:
 * - APP_NAME (nom de l'application)
 * - APP_URL (URL d'accès)
 * - APP_ENV (environnement: local, staging, production)
 * - APP_DEBUG (mode debug)
 * - TIMEZONE (fuseau horaire)
 * - LOCALE (langue par défaut)
 *
 * Responsabilités:
 * - Pré-remplir avec détection automatique si possible
 * - Valider les données
 * - Stocker en session (pas en .env encore)
 * - Rediriger vers étape suivante (database)
 *
 * @example
 * // Route: GET /setup/app-settings
 * // Affiche formulaire avec pré-remplissage
 *
 * // Route: POST /setup/app-settings
 * // Body: {app_name: "API Manager", app_url: "http://...", ...}
 * // Stocker session + rediriger /setup/database
 */
class AppSettingsController extends Controller
{
    /**
     * Affiche le formulaire des paramètres applicatifs.
     *
     * Actions:
     * - Détecte APP_NAME depuis dossier projet (kebab-case → Title Case)
     * - Détecte APP_URL depuis HTTP_HOST et HTTPS
     * - Détecte APP_ENV (local si localhost/127.0.0.1, sinon production)
     * - Pré-remplit depuis session si présent (retour formulaire avec erreurs)
     * - Récupère liste timezones et locales
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue du formulaire app-settings
     */
    public function index(Request $request): View
    {
        $setupSession = app(\App\Services\Installation\SetupSession::class);

        \Log::channel('installation')->info('✅ ÉTAPE 2: App Settings GET /setup/app-settings', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'http_host' => $request->getHost(),
            'app_url_config' => config('app.url'),
        ]);

        // Détection automatique APP_NAME
        $defaultAppName = $this->detectAppName();

        // Détection automatique APP_URL
        $defaultAppUrl = $this->detectAppUrl();

        // Détection automatique APP_ENV
        $defaultAppEnv = $this->detectAppEnv();

        // Pré-remplissage depuis session setup (stateless)
        $formData = [
            'app_name' => $setupSession->get('setup.app_name', $defaultAppName),
            'app_url' => $setupSession->get('setup.app_url', $defaultAppUrl),
            'app_env' => $setupSession->get('setup.app_env', $defaultAppEnv),
            'app_debug' => $setupSession->get('setup.app_debug', $defaultAppEnv === 'local'),
            'timezone' => $setupSession->get('setup.timezone', config('app.timezone', 'UTC')),
            'locale' => $setupSession->get('setup.locale', config('app.locale', 'fr')),
        ];

        // Listes timezones et locales
        $timezones = timezone_identifiers_list();
        $locales = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'];

        return view('setup.steps.app-settings', [
            'formData' => $formData,
            'timezones' => $timezones,
            'locales' => $locales,
            'environments' => ['local' => 'Local (Développement)', 'staging' => 'Staging (Test)', 'production' => 'Production'],
            'errors' => $setupSession->get('errors', []),
        ]);
    }

    /**
     * Sauvegarde les paramètres applicatifs.
     *
     * Actions:
     * - Valider les données
     * - Vérifier cohérence (APP_DEBUG false en production)
     * - Stocker en session
     * - Rediriger /setup/database
     *
     * @param  Request  $request  Requête HTTP avec formulaire
     * @return RedirectResponse Redirection vers /setup/database
     */
    public function store(Request $request): RedirectResponse
    {
        $setupSession = app(\App\Services\Installation\SetupSession::class);

        \Log::channel('installation')->info('📝 ÉTAPE 2: App Settings POST /setup/app-settings', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'provided_token' => $request->input('_setup_token'),
            'request_data' => $request->except(['_setup_token']),
        ]);

        // Valider données (Manuelle car plus de session standard pour validation auto)
        $validator = \Validator::make($request->all(), [
            'app_name' => 'required|string|min:3|max:255',
            'app_url' => 'required|url',
            'app_env' => 'required|in:local,staging,production',
            'app_debug' => 'nullable|boolean',
            'timezone' => 'required|timezone',
            'locale' => 'required|string|in:fr,en,es',
        ]);

        if ($validator->fails()) {
            $setupSession->set('errors', $validator->errors()->toArray());
            return redirect()->back()->withInput();
        }

        $validated = $validator->validated();

        // Nettoyer les erreurs si succès
        $setupSession->set('errors', []);

        // Vérifier cohérence: DEBUG=false en production
        if ($validated['app_env'] === 'production' && ($validated['app_debug'] ?? false)) {
            $setupSession->set('errors', ['app_debug' => ['APP_DEBUG doit être false en production']]);
            return redirect()->back()->withInput();
        }

        // Stocker en session setup
        $setupSession->set('setup.app_name', $validated['app_name']);
        $setupSession->set('setup.app_url', $validated['app_url']);
        $setupSession->set('setup.app_env', $validated['app_env']);
        $setupSession->set('setup.app_debug', $validated['app_debug'] ?? ($validated['app_env'] === 'local'));
        $setupSession->set('setup.timezone', $validated['timezone']);
        $setupSession->set('setup.locale', $validated['locale']);

        // Rediriger vers étape suivante
        return redirect()->route('setup.database', ['setup_token' => $setupSession->getToken()]);
    }

    /**
     * Détecte le nom de l'application depuis le dossier du projet.
     *
     * Prend le nom du dossier racine (kebab-case) et le convertit
     * en Title Case.
     *
     * @example
     * /path/to/api-manager → "API Manager"
     * /path/to/my-awesome-app → "My Awesome App"
     *
     * @return string Nom détecté
     */
    private function detectAppName(): string
    {
        // Récupérer le nom du dossier racine
        $basePath = basename(base_path());

        // Convertir kebab-case en Title Case
        // api-manager → Api Manager (puis API Manager manuellement)
        $name = str_replace('-', ' ', $basePath);
        $name = ucwords($name);

        // Corrections courantes
        $name = str_replace('Api ', 'API ', $name);
        $name = str_replace('Db ', 'DB ', $name);

        return $name ?: 'Application';
    }

    /**
     * Détecte l'URL de l'application depuis la requête actuelle.
     *
     * - Si localhost/127.0.0.1: http://hostname
     * - Sinon: https://hostname (sûr pour production)
     *
     * @return string URL détectée
     */
    private function detectAppUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $isHttps = ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1']);

        // Déterminer protocole
        $scheme = ($isLocalhost || ! $isHttps) ? 'http' : 'https';

        return "{$scheme}://{$host}";
    }

    /**
     * Détecte l'environnement d'exécution.
     *
     * - Si localhost/127.0.0.1: "local"
     * - Sinon: "production" (requiert config manuelle)
     *
     * @return string Environnement détecté
     */
    private function detectAppEnv(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1', 'localhost:8000'])
                    || str_ends_with($host, '.test')
                    || str_ends_with($host, '.local');

        return $isLocalhost ? 'local' : 'production';
    }
}
