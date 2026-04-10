<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Services\Installation\SetupSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur pour la Phase 6 du wizard: Review & Install.
 *
 * Affiche le récapitulatif complet de la configuration avant l'installation finale.
 *
 * Responsabilités:
 * - Récupérer toutes les données de session
 * - Afficher récapitulatif sans informations sensibles (passwords masqués)
 * - Permettre à l'utilisateur de revoir avant confirmation
 * - Rediriger vers /setup/install pour étape finale
 *
 * Sécurité:
 * - Les mots de passe ne sont PAS affichés (sécurité utilisateur)
 * - Validation que toutes les étapes précédentes sont complètes
 * - Affichage de avertissements si données manquantes
 *
 * @example
 * // Route: GET /setup/review
 * // Affiche récapitulatif depuis session
 * // Vérifie que toutes les étapes précédentes sont complètes
 */
class ReviewController extends Controller
{
    /**
     * Affiche le récapitulatif avant installation.
     *
     * Actions:
     * - Récupérer données depuis session
     * - Valider que toutes les données sont présentes
     * - Afficher récapitulatif avec masquage des secrets
     * - Afficher avertissements si données manquantes
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue du récapitulatif
     */
    public function index(Request $request): View
    {
        $setupSession = app(SetupSession::class);

        \Log::channel('installation')->info('🔍 ÉTAPE 6: Review GET /setup/review', [
            'setup_token' => $setupSession->getToken(),
            'has_app_name' => (bool) $setupSession->get('setup.app_name'),
        ]);

        // Récupérer données depuis session setup
        $appSettings = [
            'app_name' => $setupSession->get('setup.app_name', 'N/A'),
            'app_url' => $setupSession->get('setup.app_url', 'N/A'),
            'app_env' => $setupSession->get('setup.app_env', 'N/A'),
            'timezone' => $setupSession->get('setup.timezone', 'N/A'),
            'locale' => $setupSession->get('setup.locale', 'N/A'),
        ];

        $database = [
            'driver' => $setupSession->get('setup.database_driver', 'N/A'),
            'host' => $setupSession->get('setup.database_host', 'N/A'),
            'port' => $setupSession->get('setup.database_port', 'N/A'),
            'database' => $setupSession->get('setup.database_database', 'N/A'),
            'username' => $setupSession->get('setup.database_username', 'N/A'),
        ];

        $mail = [
            'driver' => $setupSession->get('setup.mail_driver', 'N/A'),
            'host' => $setupSession->get('setup.mail_host', 'N/A'),
            'port' => $setupSession->get('setup.mail_port', 'N/A'),
            'from_address' => $setupSession->get('setup.mail_from_address', 'N/A'),
            'from_name' => $setupSession->get('setup.mail_from_name', 'N/A'),
        ];

        $admin = [
            'name' => $setupSession->get('setup.admin_name', 'N/A'),
            'email' => $setupSession->get('setup.admin_email', 'N/A'),
        ];

        // Vérifier que toutes les données sont présentes
        $isComplete = true;
        $warnings = [];

        if (! $setupSession->get('setup.app_name')) {
            $isComplete = false;
            $warnings[] = __('setup.steps.review.sections.app');
        }
        if (! $setupSession->get('setup.database_driver')) {
            $isComplete = false;
            $warnings[] = __('setup.steps.review.sections.database');
        }
        if (! $setupSession->get('setup.mail_driver')) {
            $isComplete = false;
            $warnings[] = __('setup.steps.review.sections.mail');
        }
        if (! $setupSession->get('setup.admin_name')) {
            $isComplete = false;
            $warnings[] = __('setup.steps.review.sections.admin');
        }

        return view('setup.steps.review', [
            'appSettings' => $appSettings,
            'database' => $database,
            'mail' => $mail,
            'admin' => $admin,
            'isComplete' => $isComplete,
            'warnings' => $warnings,
            'currentStep' => 6,
            'totalSteps' => 7,
        ]);
    }
}
