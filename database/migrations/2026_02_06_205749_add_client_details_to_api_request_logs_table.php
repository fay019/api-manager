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
            $table->string('site_name')->nullable()->after('domain');
            $table->string('page_path')->nullable()->after('site_name');
            $table->string('full_url')->nullable()->after('page_path');
            $table->timestamp('client_request_time')->nullable()->after('full_url');
            $table->string('client_user_agent')->nullable()->after('client_request_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_request_logs', function (Blueprint $table) {
            $table->dropColumn(['site_name', 'page_path', 'full_url', 'client_request_time', 'client_user_agent']);
        });
    }
};
