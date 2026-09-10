<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'account_number' => fake()->unique()->bankAccountNumber(),
            'initials' => fake()->unique()->lexify('???'),
            'color' => fake()->hexColor(),
            'currency_type' => fake()->randomElement(['BOB', 'USD', 'EUR']),
        ];
    }
}
