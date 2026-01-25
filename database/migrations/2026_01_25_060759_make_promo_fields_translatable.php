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
            $table->json('title')->change();
            $table->json('content')->change();
            $table->json('cta_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('content')->change();
            $table->string('cta_text')->nullable()->change();
        });
    }
};
