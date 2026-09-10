<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementFactory extends Factory
{
    protected $model = Movement::class;

    public function definition(): array
    {
        return [
            'date' => fake()->date(),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['D', 'C']),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'person_id' => Person::factory(),
            'user_id' => User::factory(),
        ];
    }
}
