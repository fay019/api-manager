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
        // Drop old app_settings table if exists
        Schema::dropIfExists('app_settings');

        // Create documentation_settings table
        Schema::create('documentation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('doc_name')->unique();  // 'readme', 'api', 'database', etc.
            $table->string('path');                 // '/README.md', '/docs/API.md', etc.
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_settings');
    }
};
