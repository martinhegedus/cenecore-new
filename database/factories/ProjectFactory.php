<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_category_id' => ProjectCategory::factory(),
            'client_name' => fake()->company(),
            'external_url' => fake()->optional()->url(),
            'country_code' => fake()->optional()->countryCode(),
            'year' => fake()->numberBetween(2018, 2026),
            'started_at' => fake()->optional()->date(),
            'completed_at' => null,
            'status' => 'draft',
            'is_featured' => false,
            'sort_order' => fake()->numberBetween(0, 100),
            'published_at' => null,
        ];
    }
}
