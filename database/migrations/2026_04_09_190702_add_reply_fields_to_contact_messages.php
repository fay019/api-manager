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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->text('reply_message')->nullable()->after('message');
            $table->timestamp('replied_at')->nullable()->after('reply_message');
            $table->string('replied_by')->nullable()->after('replied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['reply_message', 'replied_at', 'replied_by']);
        });
    }
};
