<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Character;
use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'name' => fake()->firstName(),
            'role' => fake()->jobTitle(),
            'role_title' => fake()->jobTitle(),
            'personality' => 'Friendly and helpful',
            'tone_of_voice' => 'friendly',
            'speech_rate' => 1.0,
            'first_message' => 'Hello! How can I help you today?',
            'system_prompt' => fake()->paragraph(),
            'is_default' => true,
        ];
    }
}
