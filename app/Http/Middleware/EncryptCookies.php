<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Ne pas chiffrer le cookie de session pour éviter les problèmes de CSRF
        // lors de l'installation. Laravel chiffre déjà les cookies sensitifs.
        'api-manager-session',
        'XSRF-TOKEN',
    ];
}
