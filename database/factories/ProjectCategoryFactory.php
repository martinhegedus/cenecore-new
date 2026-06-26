<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectCategory>
 */
class ProjectCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
