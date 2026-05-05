<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn('ia_token');
            $table->string('ia_token_hash')->unique()->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn('ia_token_hash');
            $table->string('ia_token')->unique()->nullable()->after('is_active');
        });
    }
};
