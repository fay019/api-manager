<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogSessionDebug
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionCookieName = config('session.cookie');
        $sessionPath = storage_path('framework/sessions');
        $cookieValue = $request->cookie($sessionCookieName);
        $currentSessionId = session()->getId();

        // Compter les fichiers de session existants
        $sessionFiles = [];
        if (is_dir($sessionPath)) {
            $sessionFiles = scandir($sessionPath);
            $sessionFiles = array_filter($sessionFiles, fn ($f) => $f !== '.' && $f !== '..' && is_file($sessionPath.'/'.$f));
        }

        // Décoder le cookie pour voir la session_id qu'il contient
        $cookieSessionId = null;
        if ($cookieValue) {
            try {
                // Le cookie est encrypté, essayer de décoder
                $decrypted = decrypt($cookieValue);
                $cookieSessionId = $decrypted;
            } catch (\Exception $e) {
                $cookieSessionId = 'DECRYPT_FAILED';
            }
        }

        // Checker le fichier de session actuel
        $sessionFilePath = $sessionPath.'/'.$currentSessionId;
        $fileExists = file_exists($sessionFilePath);
        $fileSize = $fileExists ? filesize($sessionFilePath) : 0;

        \Log::channel('installation')->info('🔍 SESSION DEBUG', [
            'path' => $request->path(),
            'session_id_current' => $currentSessionId,
            'session_id_from_cookie' => $cookieSessionId,
            'cookie_matches_session' => $cookieSessionId === $currentSessionId,
            'session_driver' => config('session.driver'),
            'session_path' => $sessionPath,
            'session_path_exists' => is_dir($sessionPath),
            'session_path_writable' => is_writable($sessionPath),
            'session_files_list' => array_values($sessionFiles),
            'session_file_exists' => $fileExists,
            'session_file_size' => $fileSize,
            'session_data_count' => count(session()->all()),
        ]);

        $response = $next($request);

        return $response;
    }
}
