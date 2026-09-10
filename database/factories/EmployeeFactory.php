<?php

namespace Database\Factories;

use App\Models\Departament;
use App\Models\Employee;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'department_id' => fn () => Departament::factory(),
            'code' => 'EMP-'.$this->faker->unique()->numerify('####'),
            'position' => $this->faker->jobTitle(),
            'base_salary' => $this->faker->randomFloat(2, 2500, 15000),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }
}
