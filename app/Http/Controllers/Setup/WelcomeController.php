<?php

namespace App\Http\Controllers\Setup;

use App\Contracts\Installation\InstallationCheckInterface;
use App\Contracts\Installation\RequirementsCheckerInterface;
use App\Http\Controllers\Controller;
use App\Services\Installation\EnvManager;
use App\Services\Installation\SetupSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

/**
 * Contrôleur pour la Phase 1 du wizard: Welcome & Requirements Check.
 *
 * Affiche la page d'accueil et les résultats des vérifications de prérequis système.
 *
 * Responsabilités:
 * - Afficher page accueil
 * - Exécuter vérifications (PHP version, extensions, permissions)
 * - Afficher résultats (avec refresh possible)
 * - Détecter installation déjà complétée
 * - Rediriger vers phase suivante si OK
 *
 * @example
 * // Route: GET /setup/welcome
 * // Affiche la page avec résultats vérifications
 *
 * @see RequirementsCheckerInterface Service pour les vérifications
 */
class WelcomeController extends Controller
{
    /**
     * Service pour vérifier les prérequis système.
     */
    private RequirementsCheckerInterface $checker;

    /**
     * Service pour vérifier l'état d'installation.
     */
    private InstallationCheckInterface $installationCheck;

    /**
     * Initialise le contrôleur avec l'injection de dépendances.
     *
     * @param  RequirementsCheckerInterface  $checker  Vérificateur de prérequis
     * @param  InstallationCheckInterface  $installationCheck  Vérificateur d'installation
     */
    public function __construct(
        RequirementsCheckerInterface $checker,
        InstallationCheckInterface $installationCheck
    ) {
        $this->checker = $checker;
        $this->installationCheck = $installationCheck;
    }

    /**
     * Affiche la page d'accueil avec vérifications prérequis.
     *
     * Actions:
     * - Vérifie que app n'est pas déjà installée (redirect si installée)
     * - Exécute vérifications système (PHP, extensions, permissions)
     * - Cache résultats 5 minutes pour performance
     * - Affiche page avec résultats
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue pour page welcome
     */
    public function index(Request $request): View
    {
        // Clear all caches and optimizations at the start of installation wizard
        // to avoid conflicts with previous state (DB, routes, config, etc.)
        Artisan::call('optimize:clear');

        // Initialiser la session de setup si inexistante ou si le token cookie ne pointe vers rien
        $setupSession = app(SetupSession::class);
        if (! $setupSession->getToken() || ! $setupSession->getCsrfToken()) {
            $token = $setupSession->initialize();
            \Log::channel('installation')->info('🆕 Session setup initialisée (Nouvelle)', ['token' => $token]);
        }

        \Log::channel('installation')->info('🎬 ÉTAPE 1: Welcome GET /setup/welcome', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
        ]);

        // Vérifier que l'installation n'est pas déjà complétée
        if ($this->installationCheck->isInstalled()) {
            return redirect('/');
        }

        // Exécuter vérifications (cachées 5 minutes)
        $checkResults = $this->checker->check();

        // Déterminer état affichage
        $canContinue = $checkResults['passed'];

        return view('setup.steps.welcome', [
            'checkResults' => $checkResults,
            'canContinue' => $canContinue,
            'errorCount' => count($checkResults['errors']),
            'warningCount' => count($checkResults['warnings']),
        ]);
    }

    /**
     * Relance les vérifications (après correction d'erreurs).
     *
     * Actions:
     * - Invalide cache des vérifications
     * - Relance vérifications
     * - Affiche page avec nouveaux résultats
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue pour page welcome
     */
    public function store(Request $request): View
    {
        // Invalider le cache pour forcer re-check
        // (Une implémentation plus avancée pourrait invalidiser sélectivement)

        // Relancer vérifications (cache disabled temporairement)
        $this->checker->enableCache(0); // 0 = pas de cache
        $checkResults = $this->checker->check();
        $this->checker->enableCache(5); // Re-enable cache

        $canContinue = $checkResults['passed'];

        return view('setup.steps.welcome', [
            'checkResults' => $checkResults,
            'canContinue' => $canContinue,
            'errorCount' => count($checkResults['errors']),
            'warningCount' => count($checkResults['warnings']),
            'rechecked' => true, // Flag pour afficher "Vérifications relancées"
        ]);
    }

    /**
     * Change la langue pour le wizard d'installation.
     *
     * @return RedirectResponse
     */
    public function setLocale(Request $request)
    {
        $locale = $request->input('locale');
        $supportedLocales = ['fr', 'en', 'de'];
        $setupSession = app(SetupSession::class);

        \Log::channel('installation')->info('🌐 POST setLocale', [
            'locale' => $locale,
            'token' => $setupSession->getToken(),
        ]);

        if (in_array($locale, $supportedLocales)) {
            // Utiliser SetupSession pour persister la langue pendant l'installation
            // car le driver session standard (DB) peut ne pas être prêt.
            $setupSession->set('locale', $locale);
            $setupSession->set('setup.locale', $locale); // Keep consistent with AppSettings

            // Mettre à jour le fichier .env immédiatement pour refléter le choix de langue
            try {
                $envManager = new EnvManager;
                $envManager->update(['APP_LOCALE' => $locale]);
                $envManager->flushCache();
                \Log::channel('installation')->info('✅ APP_LOCALE mis à jour dans .env', ['locale' => $locale]);
            } catch (\Exception $e) {
                \Log::channel('installation')->warning('⚠️ Impossible de mettre à jour APP_LOCALE dans .env: '.$e->getMessage());
            }

            // S'assurer que les données sont bien écrites sur disque avant redirection
            $setupSession->save();

            // Tenter aussi la session standard si disponible
            try {
                session(['locale' => $locale]);
            } catch (\Exception $e) {
                // Ignorer si la session n'est pas accessible
            }

            App::setLocale($locale);
        }

        return redirect()->route('setup.welcome', ['setup_token' => $setupSession->getToken()])->withCookie(cookie(
            'locale',
            $locale,
            60 * 24 * 365, // 1 an
            '/',
            null,
            false,
            false, // non HttpOnly pour lecture JS si besoin
            false,
            'Lax'
        ));
    }
}
