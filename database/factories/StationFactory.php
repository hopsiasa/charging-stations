<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Station>
 */
class StationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->latitude($min = -180, $max = 180),
            'company_id' => fake()->numberBetween(1, 10),
            'address' => fake()->address()
        ];
    }
}
