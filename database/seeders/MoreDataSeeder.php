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

class MoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;
        $taxes = Taxe::all();
        $suppliers = Supplier::with('person')->get();
        $accounts = Account::all();
        $allPeople = Person::all();

        if ($suppliers->isEmpty() || $taxes->isEmpty() || $accounts->isEmpty()) {
            $this->command->warn('Ejecuta primero los seeders base (TaxeSeeder, PermissionSeeder, DemoDataSeeder).');

            return;
        }

        $now = Carbon::now();
        $months = collect([$now->copy()->subMonth(), $now]);

        // ─── Retentions (15 per month) ───
        $retentionDescriptions = [
            'RETENCIÓN POR SERVICIOS DE INGENIERÍA',
            'RETENCIÓN POR COMPRA DE REPUESTOS',
            'RETENCIÓN POR SERVICIOS DE AUDITORÍA',
            'RETENCIÓN POR ADQUISICIÓN DE SOFTWARE',
            'RETENCIÓN POR SERVICIOS LEGALES',
            'RETENCIÓN POR COMPRA DE MAQUINARIA PESADA',
            'RETENCIÓN POR SERVICIOS DE CONSULTORÍA AMBIENTAL',
            'RETENCIÓN POR SUMINISTRO DE COMUNICACIONES',
            'RETENCIÓN POR SERVICIOS DE VIGILANCIA',
            'RETENCIÓN POR COMPRA DE VEHÍCULOS',
            'RETENCIÓN POR SERVICIOS DE PUBLICIDAD',
            'RETENCIÓN POR ALQUILER DE EQUIPOS',
            'RETENCIÓN POR SERVICIOS DE TOPOGRAFÍA',
            'RETENCIÓN POR COMPRA DE LABORATORIO',
            'RETENCIÓN POR SERVICIOS DE SEGUROS',
        ];
        $retentionTypes = ['S', 'G'];
        $statuses = ['0', '1'];

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            for ($i = 0; $i < 15; $i++) {
                $supplier = $suppliers->random();
                $date = $start->copy()->addDays(rand(0, $end->day - 1));
                $type = $retentionTypes[array_rand($retentionTypes)];
                $amount = rand(1000, 50000);

                DB::transaction(function () use ($retentionDescriptions, $i, $supplier, $date, $type, $amount, $userId, $taxes, $statuses) {
                    $maxCode = Retention::whereYear('date', $date->year)
                        ->whereMonth('date', $date->month)
                        ->max('code') ?? 0;

                    $retention = Retention::create([
                        'description' => $retentionDescriptions[$i % 15],
                        'summary' => $retentionDescriptions[$i % 15],
                        'amount' => $amount,
                        'code' => $maxCode + 1,
                        'status' => $statuses[array_rand($statuses)],
                        'type' => $type,
                        'date' => $date,
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
        }

        // ─── Cash (Box) Movements (15 per month) ───
        $cashDescriptions = [
            'PAGO DE HONORARIOS PROFESIONALES',
            'COMPRA DE ARTÍCULOS DE FERRETERÍA',
            'REEMBOLSO DE GASTOS DE VIAJE',
            'PAGO POR SERVICIO DE FOTOCOPIAS',
            'COBRO POR VENTA DE ACTIVOS FIJOS',
            'PAGO DE GASTOS DE CAPACITACIÓN',
            'COMPRA DE EXTINTORES',
            'PAGO DE ARBITRIOS MUNICIPALES',
            'COBRO POR ARRENDAMIENTO DE EQUIPOS',
            'PAGO DE SERVICIOS CONTABLES',
            'COMPRA DE MATERIAL DIDÁCTICO',
            'PAGO DE GASTOS DE TRANSPORTE',
            'COBRO POR VENTA DE CHATARRA',
            'PAGO DE SERVICIOS MÉDICOS',
            'COMPRA DE ARTÍCULOS DE COCINA',
        ];

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            for ($i = 0; $i < 15; $i++) {
                $date = $start->copy()->addDays(rand(0, $end->day - 1));
                $type = rand(0, 3) > 0 ? 'D' : 'C';
                $amount = $type === 'D' ? rand(1000, 30000) : rand(500, 15000);

                DB::transaction(function () use ($date, $type, $amount, $cashDescriptions, $i, $userId, $allPeople) {
                    $movement = Movement::create([
                        'date' => $date,
                        'description' => $cashDescriptions[$i],
                        'type' => $type,
                        'amount' => $amount,
                        'person_id' => rand(0, 4) > 0 ? $allPeople->random()->id : null,
                        'user_id' => $userId,
                    ]);

                    Box::create(['movement_id' => $movement->id]);
                });
            }
        }

        // ─── Bank (Transaction) Movements (15 per month) ───
        $bankDescriptions = [
            'TRANSFERENCIA POR SERVICIOS DE INGENIERÍA',
            'PAGO DE DIVIDENDOS A ACCIONISTAS',
            'COBRO DE DEVOLUCIÓN DE IMPUESTOS',
            'PAGO DE PRIMA DE SEGUROS',
            'DEPÓSITO DE FONDOS DE TERCEROS',
            'PAGO DE REGALÍAS MINERAS',
            'TRANSFERENCIA POR VENTA DE ACTIVOS',
            'PAGO DE GASTOS ADMINISTRATIVOS',
            'COBRO POR SERVICIOS DE ASESORAMIENTO',
            'PAGO DE OBLIGACIONES TRIBUTARIAS',
            'TRANSFERENCIA POR COMPRA DE TÍTULOS',
            'PAGO DE INTERESES POR PRÉSTAMO',
            'COBRO DE INDEMNIZACIONES',
            'PAGO DE COMPROMISOS SINDICALES',
            'TRANSFERENCIA POR SERVICIOS TÉCNICOS',
        ];

        $paymentTypes = ['CH', 'T'];

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            for ($i = 0; $i < 15; $i++) {
                $account = $accounts->random();
                $date = $start->copy()->addDays(rand(0, $end->day - 1));
                $type = rand(0, 3) > 0 ? 'D' : 'C';
                $amount = $type === 'D' ? rand(5000, 100000) : rand(2000, 50000);
                $paymentType = $paymentTypes[array_rand($paymentTypes)];

                DB::transaction(function () use ($account, $date, $type, $amount, $paymentType, $bankDescriptions, $i, $userId, $allPeople) {
                    $movement = Movement::create([
                        'date' => $date,
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
        }
    }
}
