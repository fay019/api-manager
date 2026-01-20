<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('name');
            $table->string('contact_name')->nullable()->after('contact_email');
            $table->string('website')->nullable()->after('contact_name');
            $table->string('client_type')->nullable()->after('website'); // mobile, web, partner, internal
            $table->text('description')->nullable()->after('client_type');
            $table->unsignedBigInteger('monthly_quota')->nullable()->after('rate_limit_per_minute');
            $table->string('webhook_url')->nullable()->after('monthly_quota');
            $table->timestamp('activated_at')->nullable()->after('webhook_url');
        });
    }

    public function down(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropColumn([
                'contact_email',
                'contact_name',
                'website',
                'client_type',
                'description',
                'monthly_quota',
                'webhook_url',
                'activated_at',
            ]);
        });
    }
};
