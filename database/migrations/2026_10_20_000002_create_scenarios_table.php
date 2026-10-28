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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title', 255);
            $table->string('category', 100); // e.g. daily_life, travel, workplace
            $table->string('cover_image', 255)->nullable();
            $table->tinyInteger('estimated_minutes')->default(3);
            $table->json('supported_levels'); // ["A2", "B1", "B2"]
            $table->string('location_name', 150)->nullable();
            $table->string('context_note', 255)->nullable();
            $table->text('detailed_description');
            $table->text('twist_prompt');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
