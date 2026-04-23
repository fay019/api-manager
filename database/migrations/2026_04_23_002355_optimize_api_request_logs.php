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
        // Create archive table if it doesn't exist
        if (! Schema::hasTable('api_request_logs_archive')) {
            Schema::create('api_request_logs_archive', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('api_client_id')->nullable();
                $table->unsignedBigInteger('api_key_id')->nullable();
                $table->string('method', 10);
                $table->string('path', 255);
                $table->integer('status_code');
                $table->string('ip', 45);
                $table->string('hostname', 255)->nullable();
                $table->string('domain', 255)->nullable();
                $table->string('site_name', 255)->nullable();
                $table->string('page_path', 255)->nullable();
                $table->string('full_url', 255)->nullable();
                $table->timestamp('client_request_time')->nullable();
                $table->string('client_user_agent', 255)->nullable();
                $table->string('request_user_agent', 255)->nullable();
                $table->string('origin', 255)->nullable();
                $table->string('referer', 255)->nullable();
                $table->integer('duration_ms')->nullable();
                $table->integer('request_size')->nullable();
                $table->integer('response_size')->nullable();
                $table->string('error_message', 500)->nullable();
                $table->tinyInteger('cached')->default(0);
                $table->timestamp('created_at');
            });
        }

        Schema::table('api_request_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('api_request_logs', 'request_size')) {
                $table->integer('request_size')->nullable();
            }
            if (! Schema::hasColumn('api_request_logs', 'response_size')) {
                $table->integer('response_size')->nullable();
            }
            if (! Schema::hasColumn('api_request_logs', 'error_message')) {
                $table->string('error_message', 500)->nullable();
            }
            if (! Schema::hasColumn('api_request_logs', 'cached')) {
                $table->tinyInteger('cached')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_request_logs_archive');

        Schema::table('api_request_logs', function (Blueprint $table) {
            $table->dropColumn(['request_size', 'response_size', 'error_message', 'cached']);
        });
    }
};
