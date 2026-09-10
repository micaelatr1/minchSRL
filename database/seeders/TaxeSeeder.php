<?php

namespace Database\Seeders;

use App\Models\Taxe;
use Illuminate\Database\Seeder;

class TaxeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Taxe::create([
            'name' => 'Régimen Complementario al Impuesto al Valor Agregado',
            'initials' => 'RC-IVA',
            'number' => '604',
            'applied_discount' => 13,
            'type' => 'S',
        ]);
        Taxe::create([
            'name' => 'Impuesto a las Utilidades de las Empresas',
            'initials' => 'IUE',
            'number' => '570',
            'applied_discount' => 5,
            'type' => 'G',
        ]);
        Taxe::create([
            'name' => 'Impuesto a las Transacciones',
            'initials' => 'IT',
            'number' => '410',
            'applied_discount' => 3,
            'type' => 'A',
        ]);
    }
}
