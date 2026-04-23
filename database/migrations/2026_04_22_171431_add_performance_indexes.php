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
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasIndex('clients', 'clients_email_index')) {
                $table->index('email');
            }
            if (! Schema::hasIndex('clients', 'clients_is_active_index')) {
                $table->index('is_active');
            }
            if (! Schema::hasIndex('clients', 'clients_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('api_clients', function (Blueprint $table) {
            if (! Schema::hasIndex('api_clients', 'api_clients_client_id_is_active_index')) {
                $table->index(['client_id', 'is_active']);
            }
            if (! Schema::hasIndex('api_clients', 'api_clients_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('api_keys', function (Blueprint $table) {
            if (! Schema::hasIndex('api_keys', 'api_keys_created_at_index')) {
                $table->index('created_at');
            }
            if (! Schema::hasIndex('api_keys', 'api_keys_is_active_expires_at_index')) {
                $table->index(['is_active', 'expires_at']);
            }
        });

        Schema::table('api_request_logs', function (Blueprint $table) {
            if (! Schema::hasIndex('api_request_logs', 'api_request_logs_api_key_id_created_at_index')) {
                $table->index(['api_key_id', 'created_at']);
            }
            if (! Schema::hasIndex('api_request_logs', 'api_request_logs_status_code_index')) {
                $table->index('status_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasIndex('clients', 'email')) {
                $table->dropIndex(['email']);
            }
            if (Schema::hasIndex('clients', 'is_active')) {
                $table->dropIndex(['is_active']);
            }
            if (Schema::hasIndex('clients', 'created_at')) {
                $table->dropIndex(['created_at']);
            }
        });

        Schema::table('api_clients', function (Blueprint $table) {
            if (Schema::hasIndex('api_clients', 'client_id_is_active')) {
                $table->dropIndex(['client_id', 'is_active']);
            }
            if (Schema::hasIndex('api_clients', 'created_at')) {
                $table->dropIndex(['created_at']);
            }
        });

        Schema::table('api_keys', function (Blueprint $table) {
            if (Schema::hasIndex('api_keys', 'created_at')) {
                $table->dropIndex(['created_at']);
            }
            if (Schema::hasIndex('api_keys', 'is_active_expires_at')) {
                $table->dropIndex(['is_active', 'expires_at']);
            }
        });

        Schema::table('api_request_logs', function (Blueprint $table) {
            if (Schema::hasIndex('api_request_logs', 'api_key_id_created_at')) {
                $table->dropIndex(['api_key_id', 'created_at']);
            }
            if (Schema::hasIndex('api_request_logs', 'status_code')) {
                $table->dropIndex(['status_code']);
            }
        });
    }
};
