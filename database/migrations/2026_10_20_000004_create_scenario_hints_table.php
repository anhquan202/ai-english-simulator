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
        Schema::create('scenario_hints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->string('level', 10)->default('A2');
            $table->string('intent_label', 255)->nullable();
            $table->text('vietnamese_intent')->nullable();
            $table->text('sentence_frame');
            $table->json('keywords_data')->nullable();
            $table->json('keyword_bank')->nullable();
            $table->tinyInteger('sort_order')->default(1);
            $table->tinyInteger('step_order')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_hints');
    }
};
