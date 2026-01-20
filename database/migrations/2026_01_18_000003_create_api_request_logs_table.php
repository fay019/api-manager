<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 10);
            $table->string('path');
            $table->integer('status_code');
            $table->string('ip', 45);
            $table->string('user_agent')->nullable();
            $table->string('origin')->nullable();
            $table->string('referer')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestamp('created_at');
            $table->index(['api_client_id', 'created_at']);
            $table->index(['status_code', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
