<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        $month = $this->faker->numberBetween(1, 12);
        $year = $this->faker->numberBetween(2025, 2026);

        return [
            'period' => sprintf('%04d-%02d', $year, $month),
            'user_id' => User::factory(),
            'status' => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
        ]);
    }
}
