<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('ollama');
            $table->string('base_url');
            $table->string('default_model');
            $table->json('allowed_models');
            $table->integer('timeout')->default(120);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Infüge Standardwerte basierend auf .env
        DB::table('ai_settings')->insert([
            'provider' => 'ollama',
            'base_url' => env('OLLAMA_URL', 'https://ia.fayotech.com'),
            'default_model' => env('OLLAMA_DEFAULT_MODEL', 'llama3.2:3b'),
            'allowed_models' => json_encode(explode(',', env('OLLAMA_ALLOWED_MODELS', 'llama3.2:3b'))),
            'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
