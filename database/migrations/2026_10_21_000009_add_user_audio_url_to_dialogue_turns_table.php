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
        Schema::table('dialogue_turns', function (Blueprint $table) {
            if (! Schema::hasColumn('dialogue_turns', 'user_audio_url')) {
                $table->string('user_audio_url', 1024)->nullable()->after('ai_reply_translation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dialogue_turns', function (Blueprint $table) {
            if (Schema::hasColumn('dialogue_turns', 'user_audio_url')) {
                $table->dropColumn('user_audio_url');
            }
        });
    }
};
