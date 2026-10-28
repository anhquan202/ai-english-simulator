<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Scenario>
 */
class ScenarioFactory extends Factory
{
    protected $model = Scenario::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'slug' => Str::slug($title).'-'.Str::random(6),
            'title' => $title,
            'category' => fake()->randomElement(['workplace', 'shopping', 'travel', 'dining']),
            'location_name' => fake()->city(),
            'context_note' => fake()->sentence(10),
            'detailed_description' => fake()->paragraph(),
            'twist_prompt' => fake()->sentence(8),
            'supported_levels' => ['A2', 'B1'],
            'estimated_minutes' => 5,
            'is_custom' => false,
            'is_public' => false,
            'is_active' => true,
        ];
    }
}
