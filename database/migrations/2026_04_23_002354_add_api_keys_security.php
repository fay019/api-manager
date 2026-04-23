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
            if (! Schema::hasColumn('api_keys', 'key_hash')) {
                $table->char('key_hash', 64)->nullable()->comment('SHA256(raw_key) for fast lookup');
            }
            if (! Schema::hasColumn('api_keys', 'rotation_required_at')) {
                $table->timestamp('rotation_required_at')->nullable();
            }
            if (! Schema::hasColumn('api_keys', 'ip_whitelist')) {
                $table->json('ip_whitelist')->nullable();
            }
        });

        Schema::table('api_keys', function (Blueprint $table) {
            if (! Schema::hasIndex('api_keys', 'api_keys_key_hash_index')) {
                $table->index('key_hash');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            if (Schema::hasIndex('api_keys', 'api_keys_key_hash_index')) {
                $table->dropIndex('api_keys_key_hash_index');
            }
            $table->dropColumn(['key_hash', 'rotation_required_at', 'ip_whitelist']);
        });
    }
};
