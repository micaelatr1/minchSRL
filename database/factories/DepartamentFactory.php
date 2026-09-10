<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Departament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartamentFactory extends Factory
{
    protected $model = Departament::class;

    public function definition(): array
    {
        return [
            'area' => fake()->word(),
            'description' => fake()->sentence(),
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
        ];
    }
}
