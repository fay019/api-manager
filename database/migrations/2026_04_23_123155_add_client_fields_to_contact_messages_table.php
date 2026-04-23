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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->onDelete('set null');
            $table->enum('type', ['person', 'company'])->default('person')->after('client_id');
            $table->string('contact_email')->nullable()->after('email');
            $table->string('billing_email')->nullable()->after('contact_email');
            $table->string('phone')->nullable()->after('billing_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'type', 'contact_email', 'billing_email', 'phone']);
        });
    }
};
