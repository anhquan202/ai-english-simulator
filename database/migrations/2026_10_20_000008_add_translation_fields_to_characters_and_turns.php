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
        Schema::table('characters', function (Blueprint $table) {
            if (! Schema::hasColumn('characters', 'first_message_translation')) {
                $table->text('first_message_translation')->nullable()->after('first_message');
            }
        });

        Schema::table('dialogue_turns', function (Blueprint $table) {
            if (! Schema::hasColumn('dialogue_turns', 'ai_reply_translation')) {
                $table->text('ai_reply_translation')->nullable()->after('ai_reply_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            if (Schema::hasColumn('characters', 'first_message_translation')) {
                $table->dropColumn('first_message_translation');
            }
        });

        Schema::table('dialogue_turns', function (Blueprint $table) {
            if (Schema::hasColumn('dialogue_turns', 'ai_reply_translation')) {
                $table->dropColumn('ai_reply_translation');
            }
        });
    }
};
