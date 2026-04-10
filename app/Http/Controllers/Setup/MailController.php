<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Services\Installation\EnvManager;
use App\Services\Installation\SetupSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * Contrôleur pour la Phase 4 du wizard: Mail Configuration.
 *
 * Configure la connexion email (SMTP, SendMail, ou Log mailer).
 *
 * Responsabilités:
 * - Afficher formulaire configuration email avec drivers disponibles
 * - Valider configuration et tester connexion SMTP (AJAX)
 * - Stocker en session pour étape suivante
 *
 * Drivers supportés:
 * - smtp: serveur SMTP (Gmail, Mailtrap, etc.)
 * - sendmail: binaire sendmail local
 * - log: log fichier (développement)
 * - mailgun, ses, postmark: providers externes (stockage de clés)
 *
 * @example
 * // Route: GET /setup/mail
 * // Affiche formulaire avec pré-remplissage session
 *
 * // Route: POST /setup/mail/test (AJAX)
 * // Body: {mail_driver: "smtp", mail_host: "smtp.mailtrap.io", ...}
 * // Response: {success: bool, message: string, errors: array}
 *
 * // Route: POST /setup/mail
 * // Body: Données du formulaire + CSRF token
 * // Stocke session + redirection /setup/admin
 */
class MailController extends Controller
{
    /**
     * Affiche le formulaire de configuration email.
     *
     * Actions:
     * - Détecte driver depuis session ou défaut (log)
     * - Pré-remplit avec données session si présentes
     * - Fourni exemples pour drivers populaires
     * - Retourne vue avec formulaire interactif
     *
     * @param  Request  $request  Requête HTTP
     * @return View Vue du formulaire mail
     */
    public function index(Request $request): View
    {
        $setupSession = app(SetupSession::class);

        \Log::channel('installation')->info('📧 ÉTAPE 4: Mail GET /setup/mail', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'has_mail_driver_session' => (bool) $setupSession->get('setup.mail_driver'),
        ]);

        // Déterminer driver par défaut
        $defaultDriver = 'log';

        // Pré-remplissage depuis session setup (stateless)
        $formData = [
            'mail_driver' => $setupSession->get('setup.mail_driver', $defaultDriver),
            'mail_host' => $setupSession->get('setup.mail_host', 'smtp.mailtrap.io'),
            'mail_port' => $setupSession->get('setup.mail_port', '2525'),
            'mail_username' => $setupSession->get('setup.mail_username', ''),
            'mail_password' => $setupSession->get('setup.mail_password', ''),
            'mail_encryption' => $setupSession->get('setup.mail_encryption', 'tls'),
            'mail_from_address' => $setupSession->get('setup.mail_from_address', 'noreply@'.request()->getHost()),
            'mail_from_name' => $setupSession->get('setup.mail_from_name', $setupSession->get('setup.app_name', 'Application')),
            'mail_path' => $setupSession->get('setup.mail_path', '/usr/sbin/sendmail -t -i'),
        ];

        $drivers = [
            'smtp' => [
                'name' => __('setup.steps.mail.drivers.smtp.name'),
                'description' => __('setup.steps.mail.drivers.smtp.description'),
                'example' => 'smtp.gmail.com:587 (TLS) ou smtp.gmail.com:465 (SSL)',
            ],
            'sendmail' => [
                'name' => __('setup.steps.mail.drivers.sendmail.name'),
                'description' => __('setup.steps.mail.drivers.sendmail.description'),
                'example' => '/usr/sbin/sendmail -t -i',
            ],
            'log' => [
                'name' => __('setup.steps.mail.drivers.log.name'),
                'description' => __('setup.steps.mail.drivers.log.description'),
                'example' => __('setup.steps.mail.log_help'),
            ],
            'mailgun' => [
                'name' => __('setup.steps.mail.drivers.mailgun.name'),
                'description' => __('setup.steps.mail.drivers.mailgun.description'),
                'example' => __('setup.steps.mail.mailgun_help'),
            ],
        ];

        return view('setup.steps.mail', [
            'formData' => $formData,
            'drivers' => $drivers,
            'currentStep' => 4,
            'totalSteps' => 7,
            'errors' => $setupSession->get('errors', []),
        ]);
    }

    /**
     * Sauvegarde la configuration email.
     *
     * Actions:
     * - Valider données
     * - Vérifier cohérence par driver
     * - Stocker en session
     * - Rediriger /setup/admin
     *
     * @param  Request  $request  Requête HTTP
     * @return RedirectResponse Redirection vers /setup/admin
     */
    public function store(Request $request): RedirectResponse
    {
        $setupSession = app(SetupSession::class);

        \Log::channel('installation')->info('📝 ÉTAPE 4: Mail POST /setup/mail', [
            'setup_token' => $setupSession->getToken(),
            'csrf_secret' => $setupSession->getCsrfToken(),
            'mail_driver' => $request->input('mail_driver'),
        ]);

        // Valider données manuellement
        $validator = \Validator::make($request->all(), [
            'mail_driver' => 'required|in:smtp,sendmail,log,mailgun',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_path' => 'required_if:mail_driver,sendmail|nullable|string',
        ]);

        if ($validator->fails()) {
            $setupSession->set('errors', $validator->errors()->toArray());

            return redirect()->back()->withInput();
        }

        $validated = $validator->validated();
        $setupSession->set('errors', []);

        // Stocker en session setup
        $setupSession->set('setup.mail_driver', $validated['mail_driver']);
        $setupSession->set('setup.mail_host', $validated['mail_host'] ?? null);
        $setupSession->set('setup.mail_port', $validated['mail_port'] ?? null);
        $setupSession->set('setup.mail_username', $validated['mail_username'] ?? null);
        $setupSession->set('setup.mail_password', $validated['mail_password'] ?? null);
        $setupSession->set('setup.mail_encryption', $validated['mail_encryption'] ?? null);
        $setupSession->set('setup.mail_from_address', $validated['mail_from_address']);
        $setupSession->set('setup.mail_from_name', $validated['mail_from_name']);
        $setupSession->set('setup.mail_path', $validated['mail_path'] ?? null);

        // Mettre à jour le .env immédiatement pour assurer la cohérence
        try {
            $envManager = app(EnvManager::class);
            $envManager->update([
                'MAIL_MAILER' => $validated['mail_driver'],
                'MAIL_HOST' => $validated['mail_host'] ?? '',
                'MAIL_PORT' => $validated['mail_port'] ?? '',
                'MAIL_USERNAME' => $validated['mail_username'] ?? '',
                'MAIL_PASSWORD' => $validated['mail_password'] ?? '',
                'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? '',
                'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
                'MAIL_FROM_NAME' => $validated['mail_from_name'],
            ]);
            $envManager->flushCache();

            \Log::channel('installation')->info('✅ .env mis à jour avec la configuration mail');
        } catch (\Exception $e) {
            \Log::channel('installation')->warning('⚠️ Impossible de mettre à jour le .env: '.$e->getMessage());
        }

        // Rediriger vers étape suivante
        return redirect()->route('setup.admin', ['setup_token' => $setupSession->getToken()]);
    }

    /**
     * Teste la connexion SMTP (endpoint AJAX).
     *
     * Actions:
     * - Récupérer données depuis request
     * - Créer transport SMTP selon config
     * - Tenter connexion et ping
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
        $driver = $request->input('mail_driver');

        if (! in_array($driver, ['smtp', 'sendmail', 'log', 'mailgun'])) {
            return response()->json([
                'success' => false,
                'errors' => ['mail_driver' => 'Driver invalide'],
            ], 422);
        }

        // Pour sendmail et log, pas de test requis
        if ($driver === 'sendmail') {
            return response()->json([
                'success' => true,
                'message' => 'Configuration Sendmail',
            ]);
        }

        if ($driver === 'log') {
            return response()->json([
                'success' => true,
                'message' => __('setup.steps.mail.log_mode'),
            ]);
        }

        if ($driver === 'mailgun') {
            return response()->json([
                'success' => true,
                'message' => __('setup.steps.mail.mailgun_title'),
            ]);
        }

        // Test SMTP
        if ($driver === 'smtp') {
            return $this->testSmtpConnection($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Driver non supporté pour le test',
        ], 422);
    }

    /**
     * Teste la connexion SMTP.
     *
     * Crée un transport SMTP et tente de se connecter.
     *
     * @param  Request  $request  Requête avec paramètres SMTP
     * @return JsonResponse Statut de la connexion
     */
    private function testSmtpConnection(Request $request): JsonResponse
    {
        try {
            $host = $request->input('mail_host');
            $port = (int) $request->input('mail_port', 587);
            $username = $request->input('mail_username');
            $password = $request->input('mail_password');
            $encryption = $request->input('mail_encryption');

            // Valider données minimales
            if (! $host || ! $username) {
                return response()->json([
                    'success' => false,
                    'errors' => ['smtp' => 'Host et username requis'],
                ], 422);
            }

            // Créer DSN Symfony Mailer
            $dsn = sprintf(
                'smtp%s://%s:%s@%s:%d',
                $encryption === 'ssl' ? 's' : '',
                urlencode($username),
                urlencode($password ?? ''),
                $host,
                $port
            );

            // Créer et tester le transport
            $transport = $this->createTransport($dsn);

            // Tenter une connexion
            $transport->start();
            $transport->stop();

            return response()->json([
                'success' => true,
                'message' => __('setup.steps.database.test_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('setup.steps.database.test_failed'),
                'errors' => [
                    'connection' => $this->formatSmtpError($e),
                ],
            ], 422);
        }
    }

    /**
     * Crée un transport Symfony Mailer depuis un DSN.
     *
     * @param  string  $dsn  DSN au format Symfony Mailer
     * @return TransportInterface Transport configuré
     */
    private function createTransport(string $dsn): TransportInterface
    {
        return Transport::fromDsn($dsn);
    }

    /**
     * Formate un message d'erreur SMTP de manière lisible.
     *
     * Masque les détails sensibles et propose des solutions.
     *
     * @param  \Exception  $e  Exception SMTP
     * @return string Message formaté pour l'utilisateur
     */
    private function formatSmtpError(\Exception $e): string
    {
        $message = $e->getMessage();

        // Masquer données sensibles
        if (str_contains($message, 'Authentication failed')) {
            return __('setup.steps.database.errors.auth');
        }

        if (str_contains($message, 'Connection refused')) {
            return __('setup.steps.database.errors.refused');
        }

        if (str_contains($message, 'Connection timed out')) {
            return __('setup.steps.database.errors.lost');
        }

        if (str_contains($message, 'No route to host')) {
            return __('setup.steps.database.errors.lost');
        }

        if (str_contains($message, 'Unable to negotiate TLS')) {
            return __('setup.steps.database.errors.generic');
        }

        // Message par défaut sûr
        return __('setup.steps.database.errors.generic');
    }
}
