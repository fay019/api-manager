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
        // On ajoute une colonne temporaire is_active
        Schema::table('api_clients', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status');
        });

        // On migre les données : active -> true, disabled -> false
        \Illuminate\Support\Facades\DB::table('api_clients')
            ->where('status', 'disabled')
            ->update(['is_active' => false]);

        // On supprime l'index avant de supprimer la colonne (nécessaire pour SQLite)
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        // On supprime l'ancienne colonne status
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // On ajoute un index sur is_active
        Schema::table('api_clients', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('api_clients', function (Blueprint $table) {
            $table->string('status')->default('active')->after('name');
        });

        \Illuminate\Support\Facades\DB::table('api_clients')
            ->where('is_active', false)
            ->update(['status' => 'disabled']);

        Schema::table('api_clients', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
