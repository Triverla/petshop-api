<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(4, true);
        $file = File::factory()->create();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->text(200),
            'metadata' => json_encode(
                [
                    'author' => $this->faker->name(),
                    'image' => $file->uuid,
                ]
            ),

        ];
    }
}
