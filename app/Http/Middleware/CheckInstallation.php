<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour vérifier si l'application est installée.
 *
 * Si non installée, redirige vers /setup
 * Si installée, laisse passer
 */
class CheckInstallation
{
    /**
     * Exécute le middleware.
     */
    public function handle(Request $request, Closure $next)
    {
        // Ne pas checker setup, admin/login, setup routes
        if ($this->isSetupRequest($request)) {
            return $next($request);
        }

        // Vérifier si l'application est installée
        if (!$this->isInstalled()) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }

    /**
     * Vérifie si c'est une requête setup/login.
     */
    protected function isSetupRequest(Request $request): bool
    {
        $path = $request->path();

        // Routes exclues de la vérification d'installation
        $excluded = [
            'setup',
            'admin/login',
            'api/v1/health',
            'livewire',
            'filament',
            'storage',
        ];

        foreach ($excluded as $exclude) {
            if (str_starts_with($path, $exclude)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'application est installée.
     *
     * Regarde si le fichier installed.lock existe dans storage/app/
     */
    protected function isInstalled(): bool
    {
        return file_exists(storage_path('app/installed.lock'));
    }
}
