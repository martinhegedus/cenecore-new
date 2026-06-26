<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => Str::slug(fake()->unique()->words(2, true)),
            'icon' => fake()->optional()->word(),
            'website_url' => fake()->optional()->url(),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'is_active' => true,
        ];
    }
}
