<?php

namespace Database\Seeders;

use App\Models\AiSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class AiSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer ou mettre à jour la configuration IA
        $data = [
            'provider' => 'ollama',
            'base_url' => env('OLLAMA_URL', 'https://ia.fayotech.com'),
            'default_model' => env('OLLAMA_DEFAULT_MODEL', 'llama3.2:3b'),
            'allowed_models' => json_decode(json_encode(explode(',', env('OLLAMA_ALLOWED_MODELS', 'llama3.2:3b'))), true),
            'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
            'is_active' => true,
        ];

        // Ajouter le token s'il existe
        if (env('OLLAMA_INTERNAL_TOKEN')) {
            $data['ia_token_hash'] = env('OLLAMA_INTERNAL_TOKEN');
        }

        AiSetting::updateOrCreate([], $data);
    }
}
