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
        Schema::create('practice_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('guest_token', 100)->nullable()->index();
            $table->foreignId('scenario_id')->constrained('scenarios');
            $table->foreignId('character_id')->constrained('characters');
            $table->string('level', 10)->default('A2');
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->string('current_stage', 50)->default('INIT')->nullable();
            $table->decimal('csi_score', 5, 2)->nullable();
            $table->decimal('score_resolution', 5, 2)->nullable();
            $table->decimal('resolution_score', 5, 2)->nullable();
            $table->decimal('score_adaptability', 5, 2)->nullable();
            $table->decimal('adaptability_score', 5, 2)->nullable();
            $table->decimal('score_flow_fluency', 5, 2)->nullable();
            $table->decimal('flow_score', 5, 2)->nullable();
            $table->text('ai_closing_note')->nullable();
            $table->boolean('is_mastered')->default(false);
            $table->tinyInteger('total_turns')->default(0);
            $table->unsignedBigInteger('weakest_turn_id')->nullable();
            $table->decimal('delta_improvement', 5, 2)->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_sessions');
    }
};
