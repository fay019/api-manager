<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour s'assurer que le répertoire de session existe.
 *
 * Ce middleware garantit que storage/framework/sessions/ existe et est writable
 * pour que le session driver file puisse fonctionner correctement.
 */
class EnsureSessionDirectoryExists
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionPath = storage_path('framework/sessions');

        // Créer le répertoire s'il n'existe pas
        if (! is_dir($sessionPath)) {
            mkdir($sessionPath, 0755, true);
        }

        // Vérifier les permissions
        if (! is_writable($sessionPath)) {
            chmod($sessionPath, 0755);
        }

        // Vérifier aussi storage/framework
        $frameworkPath = storage_path('framework');
        if (! is_dir($frameworkPath)) {
            mkdir($frameworkPath, 0755, true);
        }
        if (! is_writable($frameworkPath)) {
            chmod($frameworkPath, 0755);
        }

        // Vérifier aussi storage/logs
        $logsPath = storage_path('logs');
        if (! is_dir($logsPath)) {
            mkdir($logsPath, 0755, true);
        }
        if (! is_writable($logsPath)) {
            chmod($logsPath, 0755);
        }

        return $next($request);
    }
}
