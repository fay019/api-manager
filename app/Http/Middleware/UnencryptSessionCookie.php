<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour enlever le chiffrement du cookie de session pendant le setup.
 *
 * Ce middleware s'exécute APRÈS tous les autres middlewares et modifie
 * le header Set-Cookie pour enlever le chiffrement du cookie de session
 * pour les routes /setup.
 */
class UnencryptSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Pour les routes setup, créer le cookie de session non-chiffré
        if ($request->is('setup/*') || $request->is('setup')) {
            $sessionCookie = config('session.cookie');
            $sessionId = session()->getId();

            // Supprimer le cookie de session des headers et le recréer sans chiffrement
            $existingCookies = $response->headers->getCookies();

            // Filtrer les anciens cookies de session
            $newCookies = array_filter($existingCookies, function ($cookie) use ($sessionCookie) {
                return $cookie->getName() !== $sessionCookie;
            });

            // Recréer les cookies (en supprimant les anciens)
            foreach ($existingCookies as $cookie) {
                if ($cookie->getName() === $sessionCookie) {
                    // Supprimer l'ancien cookie chiffré
                    $response->headers->removeCookie($sessionCookie);
                }
            }

            // Créer un nouveau cookie NON-chiffré
            $response->cookie(
                $sessionCookie,
                $sessionId,
                $this->getSessionLifetime(),
                config('session.path', '/'),
                config('session.domain'),
                config('session.secure_cookie', false),
                config('session.http_only', true),
                false // httpOnly
            );
        }

        return $response;
    }

    private function getSessionLifetime(): int
    {
        return config('session.lifetime', 120) * 60; // Convertir en secondes
    }
}
