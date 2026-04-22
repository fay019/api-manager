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
            $table->string('password_reset_token', 64)->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->index('password_reset_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['password_reset_token']);
            $table->dropColumn('password_reset_token');
            $table->dropColumn('password_reset_expires_at');
        });
    }
};
