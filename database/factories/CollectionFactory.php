<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Collection>
 */
class CollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'authToken' => uniqid(),
            'defaultTotalReturnDocuments' => fake()->numberBetween(1, 3),
            'loader_id' => 1,
            'splitter_id' => 1,
            'embedder_id' => 1,
        ];
    }
}
