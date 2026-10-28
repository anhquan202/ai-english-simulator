<?php

declare(strict_types=1);

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
        Schema::create('sticky_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('practice_session_id')->nullable()->constrained('practice_sessions')->nullOnDelete();
            $table->foreignId('dialogue_turn_id')->nullable()->constrained('dialogue_turns')->nullOnDelete();
            $table->string('language', 10)->default('en');
            $table->text('phrase');
            $table->string('phonetic', 255)->nullable();
            $table->text('meaning_vi');
            $table->text('user_note')->nullable();
            $table->text('cultural_tip')->nullable();
            $table->string('color', 20)->default('yellow');
            $table->string('pin_style', 30)->default('thumbtack_brass');
            $table->string('stamp_emoji', 20)->nullable();
            $table->string('stamp_name', 50)->nullable();
            $table->unsignedTinyInteger('review_stage')->default(1);
            $table->dateTime('next_review_at');
            $table->dateTime('last_reviewed_at')->nullable();
            $table->unsignedTinyInteger('consecutive_correct')->default(0);
            $table->boolean('is_mastered')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_mastered', 'next_review_at']);
            $table->index(['user_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sticky_notes');
    }
};
