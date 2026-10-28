<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('default_level');
        });

        // Grant admin privileges to existing demo user 'learner'
        User::where('username', 'learner')->update(['is_admin' => true]);

        // Create or update dedicated 'admin' account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'name' => 'System Administrator',
                'email' => 'admin@learner.molo.local',
                'password' => Hash::make('Admin@123456'),
                'is_admin' => true,
                'native_language' => 'vi',
                'target_language' => 'en',
                'target_level' => 'B2',
                'default_level' => 'B2',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
