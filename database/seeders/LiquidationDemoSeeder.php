<?php

namespace Database\Seeders;

use App\Models\Liquidation;
use App\Models\User;
use Illuminate\Database\Seeder;

class LiquidationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        if (! $user) {
            $this->command?->warn('No hay usuarios. Ejecute primero el seeder principal.');

            return;
        }

        Liquidation::query()->delete();

        $current = now()->startOfMonth();
        $previous = $current->copy()->subMonth();

        $rows = [
            ['state' => 'pb', 'date' => $current->copy()->addDays(3), 'full_name' => 'Juan Pérez Mamani', 'lote' => 'MCPB-04/26'],
            ['state' => 'pb', 'date' => $current->copy()->addDays(9), 'full_name' => 'María Elena Quispe', 'lote' => 'MCPB-05/26'],
            ['state' => 'pb', 'date' => $current->copy()->addDays(16), 'full_name' => 'Carlos Rojas Vargas', 'lote' => 'MCPB-06/26'],
            ['state' => 'zn', 'date' => $current->copy()->addDays(7), 'full_name' => 'Ana Lucía Flores', 'lote' => 'MCH-ZN-11/26'],
            ['state' => 'zn', 'date' => $current->copy()->addDays(21), 'full_name' => 'Pedro Colque Choque', 'lote' => 'MCH-ZN-12/26'],
            ['state' => 'pb', 'date' => $previous->copy()->addDays(11), 'full_name' => 'Rosa M. Huanca', 'lote' => 'MCPB-03/26'],
            ['state' => 'zn', 'date' => $previous->copy()->addDays(19), 'full_name' => 'Luis A. Ticona', 'lote' => 'MCH-ZN-10/26'],
        ];

        foreach ($rows as $row) {
            Liquidation::factory()
                ->{$row['state']}()
                ->create([
                    'lote' => $row['lote'],
                    'date' => $row['date']->toDateString(),
                    'full_name' => $row['full_name'],
                    'cooperative_name' => 'COOPERATIVA MINEROS DE ORURO',
                    'user_id' => $user->id,
                ]);
        }

        $this->command?->info(Liquidation::query()->count().' liquidaciones de demostración creadas.');
    }
}
