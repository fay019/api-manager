<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('promo_events');
    }

    public function down(): void
    {
        // We don't recreate the table in rollback since it was dead code
    }
};
