<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'client_company' => fake()->optional()->company(),
            'client_role' => fake()->optional()->jobTitle(),
            'project_id' => Project::factory(),
            'rating' => fake()->optional()->numberBetween(1, 5),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'is_active' => true,
        ];
    }
}
