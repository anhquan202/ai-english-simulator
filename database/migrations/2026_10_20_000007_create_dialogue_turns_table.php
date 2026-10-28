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
        Schema::create('dialogue_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('practice_sessions')->cascadeOnDelete();
            $table->unsignedBigInteger('practice_session_id')->nullable();
            $table->tinyInteger('turn_number')->default(1);
            $table->tinyInteger('turn_index')->nullable();
            $table->enum('speaker', ['user', 'ai']);
            $table->text('raw_transcript')->nullable();
            $table->text('user_transcript')->nullable();
            $table->text('ai_reply_text')->nullable();
            $table->decimal('thinking_seconds', 4, 1)->nullable();
            $table->tinyInteger('redo_count')->default(0);
            $table->boolean('hints_used')->default(false);
            $table->text('context_alternative')->nullable();
            $table->text('native_alternative_expression')->nullable();
            $table->json('word_analysis')->nullable();
            $table->decimal('turn_penalty_score', 5, 2)->nullable();
            $table->boolean('is_weakest_slice')->default(false);
            $table->timestamps();

            $table->index(['session_id', 'turn_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dialogue_turns');
    }
};
