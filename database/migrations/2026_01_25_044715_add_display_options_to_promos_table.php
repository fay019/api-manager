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
        Schema::table('promos', function (Blueprint $table) {
            $table->integer('max_impressions')->default(9999)->after('priority');
            $table->integer('cooldown_seconds')->default(0)->after('max_impressions');
            $table->string('display_mode')->default('fixed_count')->after('cooldown_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['max_impressions', 'cooldown_seconds', 'display_mode']);
        });
    }
};
