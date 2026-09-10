<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
        ];
    }
}
