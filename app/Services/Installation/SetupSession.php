<?php

namespace App\Services\Installation;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class SetupSession
{
    protected string $storagePath;

    protected ?string $token = null;

    protected array $data = [];

    public function __construct()
    {
        $this->storagePath = storage_path('app/setup');
        if (! is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }

        // On cherche le token dans le paramètre d'URL d'abord, puis dans le cookie
        $this->token = request()->query('setup_token') ?? request()->input('_setup_token') ?? request()->cookie('api_manager_setup_token');

        if ($this->token) {
            $this->load();
        }
    }

    /**
     * Initialise une nouvelle session de setup.
     */
    public function initialize(): string
    {
        $this->token = Str::uuid()->toString();
        $this->data = [
            'created_at' => now()->toDateTimeString(),
            'csrf_secret' => Str::random(40),
        ];
        $this->save();

        \Log::channel('installation')->info('📁 Création du fichier de session', [
            'token' => $this->token,
            'filename' => $this->getFilename(),
            'csrf_secret' => $this->data['csrf_secret'],
        ]);

        // On force le cookie immédiatement pour les redirections suivantes
        // Utilisation de Cookie::queue pour que Laravel l'ajoute à la réponse
        Cookie::queue(
            'api_manager_setup_token',
            $this->token,
            120,
            '/',
            null,
            false,
            true,
            false,
            'Lax'
        );

        return $this->token;
    }

    /**
     * Charge les données depuis le fichier JSON.
     */
    protected function load(): void
    {
        $filename = $this->getFilename();
        if (file_exists($filename)) {
            // Vérifier le TTL (2 heures)
            if (filemtime($filename) < time() - 7200) {
                \Log::channel('installation')->warning('⌛ Fichier de session expiré', ['filename' => $filename]);
                @unlink($filename);
                $this->token = null;
                $this->data = [];

                return;
            }

            $content = file_get_contents($filename);
            $this->data = json_decode($content, true) ?: [];

            if (empty($this->data)) {
                \Log::channel('installation')->warning('⚠️ Fichier de session vide ou invalide', ['filename' => $filename]);
            }
        } else {
            \Log::channel('installation')->warning('❓ Fichier de session manquant pour le token fourni', [
                'token' => $this->token,
                'expected_filename' => $filename,
            ]);
        }
    }

    /**
     * Sauvegarde les données dans le fichier JSON.
     */
    public function save(): void
    {
        if (! $this->token) {
            return;
        }

        $result = file_put_contents($this->getFilename(), json_encode($this->data));

        if ($result === false) {
            \Log::error('❌ Impossible d\'écrire le fichier de session setup', [
                'filename' => $this->getFilename(),
            ]);
        }
    }

    /**
     * Récupère une valeur de la session de setup.
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Définit une valeur dans la session de setup.
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->save();
    }

    /**
     * Récupère toutes les données.
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Supprime la session et le fichier.
     */
    public function flush(): void
    {
        if ($this->token && file_exists($this->getFilename())) {
            @unlink($this->getFilename());
        }
        $this->token = null;
        $this->data = [];
    }

    /**
     * Retourne le chemin du fichier pour le token actuel.
     */
    protected function getFilename(): string
    {
        return $this->storagePath.'/progress_'.hash('sha256', $this->token).'.json';
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getCsrfToken(): ?string
    {
        return $this->data['csrf_secret'] ?? null;
    }
}
