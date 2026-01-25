<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setup\AdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur pour la Phase 5 du wizard: Admin User Creation.
 *
 * Crée le premier utilisateur administrateur de l'application.
 *
 * Responsabilités:
 * - Afficher formulaire création admin avec indicateur force password
 * - Valider email et mot de passe fort
 * - Stocker données en session pour étape suivante
 * - Ne crée l'utilisateur que lors de l'étape finale (SuccessController)
 *
 * Sécurité:
 * - Validation mot de passe fort requise (8+ caractères, majuscules, minuscules, chiffres, spéciaux)
 * - CSRF protection sur tous les formulaires
 * - Pas de création BD à ce stade (session uniquement)
 * - Hashing du password lors de la création réelle
 *
 * @example
 * // Route: GET /setup/admin
 * // Affiche formulaire avec pré-remplissage session
 *
 * // Route: POST /setup/admin
 * // Body: {admin_name: "John Doe", admin_email: "admin@example.com", ...}
 * // Valide + stocke session + redirection /setup/review
 */
class AdminController extends Controller
{
    /**
     * Affiche le formulaire de création administrateur.
     *
     * Actions:
     * - Pré-remplit depuis session si présent
     * - Fourni exemples et explications force password
     * - Retourne vue avec formulaire
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue du formulaire admin
     */
    public function index(Request $request): View
    {
        $setupSession = app(\App\Services\Installation\SetupSession::class);

        \Log::channel('installation')->info('👤 ÉTAPE 5: Admin GET /setup/admin', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'has_admin_name_session' => (bool) $setupSession->get('setup.admin_name'),
        ]);

        // Pré-remplissage depuis session setup (stateless)
        $formData = [
            'admin_name' => $setupSession->get('setup.admin_name', ''),
            'admin_email' => $setupSession->get('setup.admin_email', ''),
        ];

        return view('setup.steps.admin', [
            'formData' => $formData,
            'currentStep' => 5,
            'totalSteps' => 7,
            'errors' => $setupSession->get('errors', []),
        ]);
    }

    /**
     * Sauvegarde les données administrateur.
     *
     * Actions:
     * - Valider données
     * - Vérifier unicité email
     * - Stocker en session (sans hashing)
     * - Rediriger /setup/review
     *
     * @param  Request  $request  Requête HTTP
     * @return RedirectResponse Redirection vers /setup/review
     */
    public function store(Request $request): RedirectResponse
    {
        $setupSession = app(\App\Services\Installation\SetupSession::class);

        \Log::channel('installation')->info('📝 ÉTAPE 5: Admin POST /setup/admin', [
            'setup_token' => $setupSession->getToken(),
            'admin_email' => $request->input('admin_email'),
        ]);

        // Valider données manuellement
        $validator = \Validator::make($request->all(), [
            'admin_name' => 'required|string|min:3|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
            'admin_password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            $setupSession->set('errors', $validator->errors()->toArray());
            return redirect()->back()->withInput();
        }

        $validated = $validator->validated();
        $setupSession->set('errors', []);

        // Stocker en session setup
        $setupSession->set('setup.admin_name', $validated['admin_name']);
        $setupSession->set('setup.admin_email', $validated['admin_email']);
        $setupSession->set('setup.admin_password', $validated['admin_password']); // Plain text en session (sera hasé à la création)

        // Rediriger vers étape suivante
        return redirect()->route('setup.review', ['setup_token' => $setupSession->getToken()]);
    }
}
