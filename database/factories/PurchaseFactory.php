<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'date' => Carbon::today(),
            'invoice_number' => fake()->optional()->numerify('FACT-####'),
            'supplier_id' => Supplier::factory(),
            'total' => 0,
            'status' => 'completed',
            'user_id' => User::factory(),
        ];
    }
}
