<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed demo learner user
        User::firstOrCreate(
            ['email' => 'learner@example.com'],
            [
                'name' => 'Demo Learner',
                'password' => bcrypt('password'),
                'native_language' => 'vi',
                'target_language' => 'en',
                'target_level' => 'A2',
                'default_level' => 'A2',
            ]
        );

        // 2. Seed 3 core Hybrid Scenarios with Personas & Hints
        $this->call(ScenarioSeeder::class);
    }
}
