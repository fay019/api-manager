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
        Schema::table('health_check_settings', function (Blueprint $table) {
            $table->boolean('mail_enabled')->default(true);
            $table->boolean('database_enabled')->default(true);
            $table->boolean('php_extensions_enabled')->default(true);
            $table->boolean('api_response_time_enabled')->default(true);
            $table->boolean('environment_variables_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_check_settings', function (Blueprint $table) {
            $table->dropColumn([
                'mail_enabled',
                'database_enabled',
                'php_extensions_enabled',
                'api_response_time_enabled',
                'environment_variables_enabled',
            ]);
        });
    }
};
