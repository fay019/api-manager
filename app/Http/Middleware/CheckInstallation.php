<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstallation
{
    public function handle(Request $request, Closure $next)
    {
        $isInstalled = file_exists(storage_path('app/installed.lock'));

        if ($isInstalled) {
            return response('Application already installed', 403);
        }

        return $next($request);
    }
}
