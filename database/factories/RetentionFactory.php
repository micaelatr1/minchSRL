<?php

namespace Database\Factories;

use App\Models\Retention;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RetentionFactory extends Factory
{
    protected $model = Retention::class;

    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
            'summary' => fake()->paragraph(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'code' => fake()->unique()->numerify('###'),
            'status' => '0',
            'type' => fake()->randomElement(['S', 'G']),
            'date' => fake()->date(),
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
        ];
    }
}
