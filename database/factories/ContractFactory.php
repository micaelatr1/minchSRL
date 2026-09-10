<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
            'total_amount' => fake()->randomFloat(2, 1000, 100000),
            'person_id' => Person::factory(),
            'type' => fake()->randomElement(['supplier', 'customer']),
            'status' => 'in_progress',
            'start_date' => fake()->date(),
            'user_id' => User::factory(),
        ];
    }
}
