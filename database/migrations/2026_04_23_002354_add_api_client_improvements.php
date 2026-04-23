<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->string('slug', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('icon_url', 255)->nullable();
            $table->string('webhook_secret', 255)->nullable();
            $table->enum('environment', ['development', 'staging', 'production'])->default('production');
            $table->unique(['client_id', 'slug']);
            $table->index('slug');
            $table->index(['client_id', 'environment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropUnique(['client_id', 'slug']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['client_id', 'environment']);
            $table->dropColumn(['slug', 'description', 'icon_url', 'webhook_secret', 'environment']);
        });
    }
};
