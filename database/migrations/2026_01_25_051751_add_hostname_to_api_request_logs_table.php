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
        Schema::table('api_request_logs', function (Blueprint $table) {
            $table->string('hostname')->nullable()->after('ip');
        });
    }

    public function down(): void
    {
        Schema::table('api_request_logs', function (Blueprint $table) {
            $table->dropColumn('hostname');
        });
    }
};
