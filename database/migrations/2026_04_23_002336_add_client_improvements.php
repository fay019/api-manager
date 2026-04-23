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
            $table->string('first_name', 255)->nullable()->after('name');
            $table->string('last_name', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->string('language', 2)->default('en');
            $table->string('billing_email', 255)->nullable();
            $table->json('address_json')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('last_failed_login_at')->nullable();
            $table->timestamp('locked_until_at')->nullable();
            $table->index('country');
            $table->index('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['country']);
            $table->dropIndex(['company_name']);
            $table->dropColumn([
                'first_name', 'last_name', 'company_name', 'phone', 'country',
                'timezone', 'language', 'billing_email', 'address_json',
                'failed_login_attempts', 'last_failed_login_at', 'locked_until_at'
            ]);
        });
    }
};
