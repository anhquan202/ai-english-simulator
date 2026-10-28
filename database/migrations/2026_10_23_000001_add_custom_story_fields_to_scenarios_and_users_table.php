<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('is_custom')->default(false)->after('twist_prompt');
            $table->boolean('is_public')->default(false)->after('is_custom');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('tier', 20)->default('free')->after('is_admin');
        });

        // Set all existing users to unlimited tier by default
        DB::table('users')->update(['tier' => 'unlimited']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'is_custom', 'is_public']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tier');
        });
    }
};
