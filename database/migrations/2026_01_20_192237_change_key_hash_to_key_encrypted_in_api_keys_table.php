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
        Schema::table('api_keys', function (Blueprint $table) {
            $table->text('key_encrypted')->after('api_client_id')->nullable();
        });

        // Comme on ne peut pas dé-hasher les clés existantes, on va les supprimer ou les invalider.
        // Pour un environnement de dev/test, on peut se permettre de vider la table ou laisser l'utilisateur régénérer.
        // Ici on va juste renommer la colonne si elle existe ou la supprimer plus tard.

        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn('key_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->string('key_hash')->after('api_client_id')->nullable();
            $table->dropColumn('key_encrypted');
        });
    }
};
