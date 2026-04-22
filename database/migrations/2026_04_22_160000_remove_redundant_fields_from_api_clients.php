<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_email', 'description', 'notes']);
        });
    }

    public function down(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('name');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->text('description')->nullable()->after('client_type');
            $table->text('notes')->nullable()->after('description');
        });
    }
};
