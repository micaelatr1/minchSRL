<?php

namespace Database\Factories;

use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Factories\Factory;

class CooperativeFactory extends Factory
{
    protected $model = Cooperative::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'concession' => fake()->word(),
            'mine' => fake()->word(),
            'municipality' => fake()->city(),
            'NIM' => fake()->unique()->numerify('########'),
            'NIT' => fake()->unique()->numerify('########'),
            'contribution' => fake()->randomFloat(2, 0, 20),
            'comibol' => fake()->randomFloat(2, 0, 20),
        ];
    }
}
