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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('role', 100);
            $table->string('role_title', 100)->nullable();
            $table->string('avatar_path', 255)->nullable();
            $table->string('avatar_url', 255)->nullable();
            $table->string('voice_id', 100)->nullable();
            $table->string('voice_name', 100)->nullable();
            $table->string('personality', 255)->nullable();
            $table->string('tone_of_voice', 100)->nullable();
            $table->decimal('speech_rate', 3, 2)->default(1.00)->nullable();
            $table->text('first_message');
            $table->text('system_prompt');
            $table->text('system_persona_prompt')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
