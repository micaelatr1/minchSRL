<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\Retention;
use App\Models\Taxe;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 1, 5000),
            'taxe_id' => Taxe::factory(),
            'retention_id' => Retention::factory(),
        ];
    }
}
