<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomElements = $this->faker->randomElement([
            'Royal Canin',
            'Blue',
            'Petsafe',
            'Acana',
            'Manapro',
            'Iris',
            'Frontline',
            'Kitzy',
            'Wag',
            'Hills'
        ]);
        return [
            'title' => $randomElements,
            'slug' => strtolower(str_replace(' ', '-', $randomElements)),
        ];
    }
}
