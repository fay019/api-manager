<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // Add JSON column for visible docs with all docs visible by default
            $table->json('visible_docs')->default('["readme", "api", "database", "deployment"]')->after('show_admin_credentials');
        });

        // Drop the old boolean columns
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'docs_index_visible',
                'docs_readme_visible',
                'docs_api_visible',
                'docs_database_visible',
                'docs_deployment_visible',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // Re-add the old boolean columns
            $table->boolean('docs_index_visible')->default(true)->after('show_admin_credentials');
            $table->boolean('docs_readme_visible')->default(true);
            $table->boolean('docs_api_visible')->default(true);
            $table->boolean('docs_database_visible')->default(true);
            $table->boolean('docs_deployment_visible')->default(true);
        });

        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn('visible_docs');
        });
    }
};
