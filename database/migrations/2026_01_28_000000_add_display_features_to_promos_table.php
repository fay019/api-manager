<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->integer('auto_close_timer')->nullable()->default(null)->after('display_mode')
                ->comment('Seconds before auto-close (0 = disabled, null = feature not used)');
            $table->boolean('show_countdown')->nullable()->default(null)->after('auto_close_timer')
                ->comment('Show countdown timer before auto-close');
            $table->string('animation_style')->nullable()->default(null)->after('show_countdown')
                ->comment('Animation style: fade, slide, zoom, etc.');
        });
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['auto_close_timer', 'show_countdown', 'animation_style']);
        });
    }
};
