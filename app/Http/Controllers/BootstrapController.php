<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class BootstrapController extends Controller
{
    /**
     * Serve install.php - Bootstrap installer
     */
    public function install()
    {
        return $this->servePhpFile('install.php', 'text/html');
    }

    /**
     * Serve diagnostic.php - Plain text diagnostics
     */
    public function diagnostic()
    {
        return $this->servePhpFile('diagnostic.php', 'text/plain');
    }

    /**
     * Serve setup.php - Setup guide
     */
    public function setup()
    {
        return $this->servePhpFile('setup.php', 'text/html');
    }

    /**
     * Execute a PHP file and return its output
     */
    private function servePhpFile(string $filename, string $contentType)
    {
        $path = public_path($filename);
        if (!file_exists($path)) {
            abort(404);
        }

        // Start output buffering to capture PHP output
        ob_start();
        include $path;
        $output = ob_get_clean();

        return response($output, 200, ['Content-Type' => $contentType]);
    }
}
