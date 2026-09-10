<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Cemento Portland', 'Dinamita', 'Pernos de anclaje', 'Acero corrugado',
                'Combustible Diesel', 'Arena sílice', 'Cal hidratada', 'Barras de perforación',
                'Cables de acero', 'Lubricante industrial', 'Malla electrosoldada',
                'Reactivos de flotación', 'Bolas de molino', 'Correas transportadoras',
                'Filtros de mangas',
            ]),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['materia_prima', 'insumo', 'repuesto', 'combustible', 'otro']),
            'unit_of_measure' => fake()->randomElement(['kg', 'ton', 'l', 'u', 'm']),
            'stock' => fake()->randomFloat(2, 10, 5000),
            'average_cost' => fake()->randomFloat(4, 0.5, 500),
            'is_active' => true,
            'user_id' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
