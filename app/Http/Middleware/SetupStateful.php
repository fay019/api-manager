<?php

namespace App\Http\Middleware;

use App\Services\Installation\SetupSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetupStateful
{
    /**
     * Gère la persistance de session stateless pour le wizard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On résout le singleton de session
        $setupSession = app(SetupSession::class);

        // Validation CSRF simplifiée pour les requêtes POST/PUT/DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $token = $request->input('_setup_token') ?? $request->header('X-Setup-Token');
            $secret = $setupSession->getCsrfToken();

            // Si le secret est absent (session perdue?), on essaie de le recharger si on a un cookie
            if (! $secret && $request->cookie('api_manager_setup_token')) {
                \Log::channel('installation')->info('🔄 Tentative de rechargement du secret CSRF depuis le cookie');
                // SetupSession se charge déjà du chargement via cookie dans son constructeur
                // Si secret est toujours nul, c'est que le fichier est absent ou expiré
                $secret = $setupSession->getCsrfToken();
            }

            // TEMP DISABLE FOR DEBUG

            if (! $secret || ! hash_equals($secret, (string) $token)) {
                \Log::channel('installation')->warning('🚫 CSRF Token Mismatch (Setup Mode)', [
                    'provided_token' => $token,
                    'expected_token' => $secret,
                    'setup_token' => $setupSession->getToken(),
                    'cookie' => $request->cookie('api_manager_setup_token'),
                ]);
                // abort(403, 'CSRF Token Mismatch (Setup Mode)');
            }

            \Log::channel('installation')->info('🛠 DEBUG: CSRF bypass activé pour le setup', [
                'provided_token' => $token,
                'expected_token' => $secret,
                'setup_token' => $setupSession->getToken(),
            ]);
        }

        $response = $next($request);

        // Si on a un token mais pas encore le cookie, on l'ajoute (non chiffré)
        $currentToken = $setupSession->getToken();
        if ($currentToken && $request->cookie('api_manager_setup_token') !== $currentToken) {
            \Log::channel('installation')->info('🍪 Envoi du cookie setup dans Middleware', ['token' => $currentToken]);
            $response->headers->setCookie(cookie(
                'api_manager_setup_token',
                $currentToken,
                120, // 2h
                '/', // Path
                null, // Domain
                false, // Secure
                true,   // HttpOnly
                false,  // Raw
                'Lax'   // SameSite
            ));
        }

        return $response;
    }
}
