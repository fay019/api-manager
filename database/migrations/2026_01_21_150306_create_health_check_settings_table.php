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
        Schema::create('health_check_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('cache_enabled')->default(true);
            $table->boolean('logs_enabled')->default(true);
            $table->boolean('disk_space_enabled')->default(true);
            $table->boolean('storage_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default row
        \DB::table('health_check_settings')->insert([
            'cache_enabled' => true,
            'logs_enabled' => true,
            'disk_space_enabled' => true,
            'storage_enabled' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_check_settings');
    }
};
