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
        Schema::create('api_cost_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('practice_sessions')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_token')->nullable()->index();
            $table->foreignId('scenario_id')->nullable()->constrained('scenarios')->nullOnDelete();
            $table->string('endpoint');
            $table->string('model_name');
            $table->integer('prompt_tokens')->default(0);
            $table->integer('candidate_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost_usd', 10, 6)->default(0.000000);
            $table->integer('latency_ms')->default(0);
            $table->integer('status_code')->default(200);
            $table->timestamps();

            // Indexes for analytics and reporting
            $table->index(['created_at', 'model_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_cost_logs');
    }
};
