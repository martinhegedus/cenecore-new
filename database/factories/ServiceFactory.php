<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'icon' => fake()->optional()->word(),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'status' => 'draft',
            'published_at' => null,
        ];
    }
}
