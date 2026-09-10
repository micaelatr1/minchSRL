<?php

namespace Database\Factories;

use App\Models\KardexMovement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class KardexMovementFactory extends Factory
{
    protected $model = KardexMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => 'purchase',
            'quantity_in' => 100,
            'quantity_out' => 0,
            'unit_cost' => 10,
            'balance_quantity' => 100,
            'balance_avg_cost' => 10,
            'balance_total_value' => 1000,
            'date' => now(),
        ];
    }
}
