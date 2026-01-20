<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->cascadeOnDelete();
            $table->integer('version');
            $table->json('payload_json');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at');
            $table->unique(['promo_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_versions');
    }
};
