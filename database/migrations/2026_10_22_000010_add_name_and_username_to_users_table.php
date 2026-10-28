<?php

declare(strict_types=1);

use App\Models\User;
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
            $table->string('first_name', 50)->nullable()->after('id');
            $table->string('last_name', 50)->nullable()->after('first_name');
            $table->string('username', 30)->nullable()->unique()->index()->after('last_name');
            $table->string('email')->nullable()->change();
        });

        // Backfill existing seeded demo user if present
        $demoUser = User::where('email', 'learner@example.com')->whereNull('username')->first();
        if ($demoUser) {
            $demoUser->update([
                'first_name' => 'Demo',
                'last_name' => 'Learner',
                'username' => 'learner',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropColumn(['first_name', 'last_name', 'username']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
