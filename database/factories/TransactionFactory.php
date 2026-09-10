<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Movement;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $movement = Movement::factory()->create();

        return [
            'payment_type' => fake()->randomElement(['T', 'CH']),
            'number' => fake()->unique()->numberBetween(1, 9999),
            'number_check' => fake()->optional()->numerify('########'),
            'account_id' => Account::factory(),
            'movement_id' => $movement->id,
        ];
    }
}
