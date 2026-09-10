<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\Movement;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoxFactory extends Factory
{
    protected $model = Box::class;

    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 9999),
            'movement_id' => Movement::factory(),
        ];
    }
}
