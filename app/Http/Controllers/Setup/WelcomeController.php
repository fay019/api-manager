<?php

namespace App\Http\Controllers\Setup;

use App\Contracts\Installation\InstallationCheckInterface;
use App\Contracts\Installation\RequirementsCheckerInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        // Initialiser la session de setup si inexistante ou si le token cookie ne pointe vers rien
        $setupSession = app(\App\Services\Installation\SetupSession::class);
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
}
