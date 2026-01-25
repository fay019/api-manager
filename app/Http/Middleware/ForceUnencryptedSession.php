<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour désactiver l'encryption de session pendant le setup.
 *
 * Durant le setup, l'encryption de session peut causer des problèmes de CSRF
 * car le cookie ne peut pas être décrypté correctement. Ce middleware
 * désactive l'encryption pour les routes /setup.
 */
class ForceUnencryptedSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Désactiver l'encryption de session pour les routes /setup
        if ($request->is('setup/*') || $request->is('setup')) {
            config(['session.encrypt' => false]);
        }

        return $next($request);
    }
}
