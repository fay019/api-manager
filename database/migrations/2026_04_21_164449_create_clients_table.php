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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->string('language', 2)->default('en');
            $table->string('billing_email', 255)->nullable();
            $table->json('address_json')->nullable();
            $table->string('activation_token', 64)->nullable();
            $table->dateTime('activation_expires_at')->nullable();
            $table->string('password_reset_token', 64)->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->string('pending_email')->nullable();
            $table->boolean('is_active')->default(false);
            $table->dateTime('activated_at')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('last_failed_login_at')->nullable();
            $table->timestamp('locked_until_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('activation_token');
            $table->index('password_reset_token');
            $table->index('country');
            $table->index('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
