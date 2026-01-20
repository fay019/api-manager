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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('show_admin_credentials')->default(true);
            $table->boolean('docs_index_visible')->default(true);
            $table->boolean('docs_readme_visible')->default(true);
            $table->boolean('docs_api_visible')->default(true);
            $table->boolean('docs_database_visible')->default(true);
            $table->boolean('docs_deployment_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
