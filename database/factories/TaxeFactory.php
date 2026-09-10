<?php

namespace Database\Factories;

use App\Models\Taxe;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxeFactory extends Factory
{
    protected $model = Taxe::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'initials' => fake()->unique()->lexify('???'),
            'number' => fake()->unique()->numerify('###'),
            'applied_discount' => fake()->randomFloat(2, 1, 20),
            'type' => fake()->randomElement(['S', 'G', 'A']),
        ];
    }
}
