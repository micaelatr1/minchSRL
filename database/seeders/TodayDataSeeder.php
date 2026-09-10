<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Box;
use App\Models\Discount;
use App\Models\Movement;
use App\Models\Person;
use App\Models\Retention;
use App\Models\Supplier;
use App\Models\Taxe;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodayDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;
        $today = Carbon::now()->format('Y-m-d');
        $taxes = Taxe::all();
        $suppliers = Supplier::with('person')->get();
        $accounts = Account::all();
        $allPeople = Person::all();

        if ($suppliers->isEmpty() || $taxes->isEmpty() || $accounts->isEmpty()) {
            $this->command->warn('Ejecuta primero los seeders base (TaxeSeeder, PermissionSeeder, DemoDataSeeder, MoreDataSeeder).');

            return;
        }

        // ─── 10 Retentions for today ───
        $retentionDescriptions = [
            'RETENCIÓN POR SERVICIOS DE CONSULTORÍA',
            'RETENCIÓN POR SERVICIOS DE MANTENIMIENTO',
            'RETENCIÓN POR COMPRA DE MATERIALES',
            'RETENCIÓN POR SERVICIOS DE TRANSPORTE',
            'RETENCIÓN POR SERVICIOS PROFESIONALES',
            'RETENCIÓN POR EQUIPAMIENTO MINERO',
            'RETENCIÓN POR SERVICIOS VARIOS',
            'RETENCIÓN POR SUMINISTROS DE OFICINA',
            'RETENCIÓN POR SERVICIOS TÉCNICOS',
            'RETENCIÓN POR COMPRA DE INSUMOS',
            'RETENCIÓN POR SERVICIOS DE LIMPIEZA',
            'RETENCIÓN POR ALQUILER DE MAQUINARIA',
        ];
        $retentionTypes = ['S', 'G'];
        $statuses = ['0', '1'];

        for ($i = 0; $i < 12; $i++) {
            $supplier = $suppliers->random();
            $type = $retentionTypes[array_rand($retentionTypes)];
            $amount = rand(1000, 50000);

            DB::transaction(function () use ($retentionDescriptions, $i, $supplier, $today, $type, $amount, $userId, $taxes, $statuses) {
                $maxCode = Retention::whereDate('date', $today)->max('code') ?? 0;

                $retention = Retention::create([
                    'description' => $retentionDescriptions[$i],
                    'summary' => $retentionDescriptions[$i],
                    'amount' => $amount,
                    'code' => $maxCode + 1,
                    'status' => $statuses[array_rand($statuses)],
                    'type' => $type,
                    'date' => $today,
                    'supplier_id' => $supplier->id,
                    'user_id' => $userId,
                ]);

                $filteredTaxes = $taxes->filter(fn ($t) => $t->type === $type || $t->type === 'A');
                foreach ($filteredTaxes as $taxe) {
                    $total = round($amount / (1 - ($filteredTaxes->sum('applied_discount') / 100)), 2);
                    Discount::create([
                        'amount' => round($total * $taxe->applied_discount / 100, 2),
                        'taxe_id' => $taxe->id,
                        'retention_id' => $retention->id,
                    ]);
                }
            });
        }

        // ─── 10 Cash (Box) Movements for today ───
        $cashDescriptions = [
            'PAGO DE SERVICIOS BÁSICOS',
            'COMPRA DE MATERIAL DE ESCRITORIO',
            'PAGO A PROVEEDORES MENORES',
            'GASTOS DE TRANSPORTE',
            'COMPRA DE INSUMOS DE OFICINA',
            'PAGO DE SERVICIOS DE LUZ',
            'GASTOS DE MANTENIMIENTO',
            'COBRO POR SERVICIOS PRESTADOS',
            'GASTOS DE REPRESENTACIÓN',
            'COBRO DE ALQUILERES',
            'GASTOS VARIOS',
            'PAGO DE VIÁTICOS',
        ];

        for ($i = 0; $i < 12; $i++) {
            $type = rand(0, 3) > 0 ? 'D' : 'C';
            $amount = $type === 'D' ? rand(1000, 30000) : rand(500, 15000);

            DB::transaction(function () use ($today, $type, $amount, $cashDescriptions, $i, $userId, $allPeople) {
                $movement = Movement::create([
                    'date' => $today,
                    'description' => $cashDescriptions[$i],
                    'type' => $type,
                    'amount' => $amount,
                    'person_id' => rand(0, 4) > 0 ? $allPeople->random()->id : null,
                    'user_id' => $userId,
                ]);

                Box::create(['movement_id' => $movement->id]);
            });
        }

        // ─── 10 Bank (Transaction) Movements for today ───
        $bankDescriptions = [
            'TRANSFERENCIA BANCARIA A PROVEEDOR',
            'PAGO DE PLANILLA DE SUELDOS',
            'COBRO DE FACTURA POR VENTA',
            'PAGO DE SERVICIOS PROFESIONALES',
            'DEPÓSITO DE INGRESOS',
            'PAGO DE IMPUESTOS NACIONALES',
            'TRANSFERENCIA RECIBIDA POR VENTA',
            'PAGO A PROVEEDOR EXTERIOR',
            'COBRO DE SERVICIOS MINEROS',
            'PAGO DE SEGUROS',
            'COMPRA DE DIVISAS',
            'PAGO DE CRÉDITO BANCARIO',
        ];
        $paymentTypes = ['CH', 'T'];

        for ($i = 0; $i < 12; $i++) {
            $account = $accounts->random();
            $type = rand(0, 3) > 0 ? 'D' : 'C';
            $amount = $type === 'D' ? rand(5000, 100000) : rand(2000, 50000);
            $paymentType = $paymentTypes[array_rand($paymentTypes)];

            DB::transaction(function () use ($account, $today, $type, $amount, $paymentType, $bankDescriptions, $i, $userId, $allPeople) {
                $movement = Movement::create([
                    'date' => $today,
                    'description' => $bankDescriptions[$i],
                    'type' => $type,
                    'amount' => $amount,
                    'person_id' => rand(0, 4) > 0 ? $allPeople->random()->id : null,
                    'user_id' => $userId,
                ]);

                Transaction::create([
                    'payment_type' => $paymentType,
                    'account_id' => $account->id,
                    'movement_id' => $movement->id,
                    'number_check' => $paymentType === 'CH' ? 'CH-'.rand(1000, 9999) : 'TRF-'.rand(10000, 99999),
                ]);
            });
        }

        $this->command->info('12 retenciones, 12 movimientos de caja y 12 movimientos bancarios creados para la fecha '.$today);
    }
}
