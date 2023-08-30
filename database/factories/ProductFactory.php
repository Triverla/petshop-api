<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = Category::all();

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'category_uuid' => $categories->random()->uuid,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'metadata' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
