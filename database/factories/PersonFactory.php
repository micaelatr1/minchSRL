<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'ci' => fake()->unique()->numerify('########'),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
