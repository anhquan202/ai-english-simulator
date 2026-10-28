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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('reminder_enabled')->default(true)->after('tier');
            $table->string('reminder_time', 10)->default('20:00')->after('reminder_enabled');
            $table->json('reminder_days')->nullable()->after('reminder_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reminder_enabled', 'reminder_time', 'reminder_days']);
        });
    }
};
