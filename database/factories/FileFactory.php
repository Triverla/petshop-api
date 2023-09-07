<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'path' => $this->faker->filePath(),
            'size' => $this->faker->numberBetween(100, 500),
            'type' => $this->faker->mimeType(),
        ];
    }
}
