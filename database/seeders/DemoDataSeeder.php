<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Box;
use App\Models\Cooperative;
use App\Models\Customer;
use App\Models\Departament;
use App\Models\Discount;
use App\Models\Employee;
use App\Models\Movement;
use App\Models\Payroll;
use App\Models\Person;
use App\Models\Retention;
use App\Models\Supplier;
use App\Models\Taxe;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;

        // ─── Cooperatives ───
        $cooperatives = collect([
            ['name' => 'COOPERATIVA MINERA UNIFICADA BOLIVAR', 'concession' => 'CB-001', 'mine' => 'Mina Bolívar', 'municipality' => 'Potosí', 'NIM' => 'NIM-001', 'NIT' => '102456021', 'contribution' => 15.00, 'comibol' => 5.00],
            ['name' => 'COOPERATIVA MINERA SAN CRISTOBAL', 'concession' => 'SC-045', 'mine' => 'Mina San Cristóbal', 'municipality' => 'Uncía', 'NIM' => 'NIM-045', 'NIT' => '102789034', 'contribution' => 12.50, 'comibol' => 4.00],
            ['name' => 'COOPERATIVA MINERA LAJO', 'concession' => 'LJ-012', 'mine' => 'Mina Lajo', 'municipality' => 'Oruro', 'NIM' => 'NIM-012', 'NIT' => '103112045', 'contribution' => 18.00, 'comibol' => 6.00],
            ['name' => 'COOPERATIVA MINERA COLQUIRI', 'concession' => 'CQ-078', 'mine' => 'Mina Colquiri', 'municipality' => 'Colquiri', 'NIM' => 'NIM-078', 'NIT' => '103445067', 'contribution' => 10.00, 'comibol' => 3.50],
            ['name' => 'COOPERATIVA MINERA HUANUNI', 'concession' => 'HN-034', 'mine' => 'Mina Huanuni', 'municipality' => 'Huanuni', 'NIM' => 'NIM-034', 'NIT' => '103678089', 'contribution' => 14.00, 'comibol' => 5.00],
        ])->map(fn ($data) => Cooperative::firstOrCreate(['name' => $data['name']], $data));

        // ─── Persons -> Suppliers ───
        $supplierPeople = collect([
            ['ci' => '23456789', 'full_name' => 'JUAN CARLOS MAMANI FLORES', 'phone' => '76543210'],
            ['ci' => '34567890', 'full_name' => 'MARIA ELENA QUISPE CONDORI', 'phone' => '75432109'],
            ['ci' => '45678901', 'full_name' => 'PEDRO PABLO GUTIERREZ VARGAS', 'phone' => '74321098'],
            ['ci' => '56789012', 'full_name' => 'ANA ROSA TORREZ MENDOZA', 'phone' => '73210987'],
            ['ci' => '67890123', 'full_name' => 'LUIS ALBERTO MORALES ROJAS', 'phone' => '72109876'],
        ])->map(fn ($data) => Person::firstOrCreate(['ci' => $data['ci']], $data));

        $suppliers = collect([
            ['description' => 'PROVEEDOR DE MATERIALES DE CONSTRUCCIÓN', 'person_id' => $supplierPeople[0]->id],
            ['description' => 'PROVEEDOR DE EQUIPO DE SEGURIDAD INDUSTRIAL', 'person_id' => $supplierPeople[1]->id],
            ['description' => 'PROVEEDOR DE INSUMOS MINEROS', 'person_id' => $supplierPeople[2]->id],
            ['description' => 'PROVEEDOR DE SERVICIOS DE TRANSPORTE', 'person_id' => $supplierPeople[3]->id],
            ['description' => 'PROVEEDOR DE COMBUSTIBLES Y LUBRICANTES', 'person_id' => $supplierPeople[4]->id],
        ])->map(fn ($data) => Supplier::firstOrCreate(['person_id' => $data['person_id']], $data));

        // ─── Persons -> Customers ───
        $customerPeople = collect([
            ['ci' => '78901234', 'full_name' => 'ROSA MARIA HUANCA CHOQUE', 'phone' => '71098765'],
            ['ci' => '89012345', 'full_name' => 'CARLOS TITO ZEBALLOS', 'phone' => '70987654'],
            ['ci' => '90123456', 'full_name' => 'SONIA PATRICIA CALLE PINTO', 'phone' => '69876543'],
            ['ci' => '10123456', 'full_name' => 'FERNANDO MONTES RIVERA', 'phone' => '68765432'],
            ['ci' => '11123456', 'full_name' => 'GLADYS QUISBERT ARUQUIPA', 'phone' => '67654321'],
            ['ci' => '12123456', 'full_name' => 'MARCELO FERNANDEZ GARCIA', 'phone' => '66543210'],
            ['ci' => '13123456', 'full_name' => 'ELIZABETH CRUZ SANDOVAL', 'phone' => '65432109'],
            ['ci' => '14123456', 'full_name' => 'WALTER SARAVIA CHOQUE', 'phone' => '64321098'],
            ['ci' => '15123456', 'full_name' => 'NANCY APAZA RAMIREZ', 'phone' => '63210987'],
            ['ci' => '16123456', 'full_name' => 'DANIEL CONDORI MAMANI', 'phone' => '62109876'],
        ])->map(fn ($data) => Person::firstOrCreate(['ci' => $data['ci']], $data));

        $customerPeople->zip(range(0, 9))->each(function ($pair) use ($cooperatives) {
            [$person, $idx] = $pair;
            Customer::firstOrCreate(['person_id' => $person->id], [
                'file' => 'CLI-'.str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT),
                'person_id' => $person->id,
                'cooperative_id' => $cooperatives[$idx % 5]->id,
            ]);
        });

        // ─── Retentions ───
        $taxes = Taxe::all();
        $retentionTypes = ['S', 'G'];
        $statuses = ['0', '1'];
        $descriptions = [
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
            'RETENCIÓN POR SERVICIOS DE SEGURIDAD',
            'RETENCIÓN POR COMPRA DE HERRAMIENTAS',
            'RETENCIÓN POR SERVICIOS DE CAPACITACIÓN',
        ];

        $now = Carbon::now();
        $months = collect([$now->copy()->subMonths(2), $now->copy()->subMonth(), $now]);

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            for ($i = 0; $i < 5; $i++) {
                $supplier = $suppliers->random();
                $date = $start->copy()->addDays(rand(0, $end->day - 1));
                $type = $retentionTypes[array_rand($retentionTypes)];
                $amount = rand(1000, 50000);

                DB::transaction(function () use ($descriptions, $i, $supplier, $date, $type, $amount, $userId, $taxes, $statuses) {
                    $maxCode = Retention::whereYear('date', $date->year)
                        ->whereMonth('date', $date->month)
                        ->max('code') ?? 0;

                    $retention = Retention::create([
                        'description' => $descriptions[$i],
                        'summary' => $descriptions[$i],
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

        // ─── Cash (Box) Movements ───
        $allPeople = $supplierPeople->merge($customerPeople);
        $cashDescriptions = [
            'PAGO DE SERVICIOS BÁSICOS',
            'COMPRA DE MATERIAL DE ESCRITORIO',
            'PAGO DE SALARIOS',
            'COBRO POR VENTA DE MINERAL',
            'PAGO A PROVEEDORES MENORES',
            'GASTOS DE TRANSPORTE',
            'COMPRA DE INSUMOS DE OFICINA',
            'PAGO DE SERVICIOS DE LUZ',
            'COBRO DE CUOTAS',
            'GASTOS DE MANTENIMIENTO',
            'PAGO DE AGUA POTABLE',
            'COMPRA DE EQUIPO MENOR',
            'PAGO DE SERVICIOS DE INTERNET',
            'COBRO POR SERVICIOS PRESTADOS',
            'GASTOS DE REPRESENTACIÓN',
            'PAGO DE IMPUESTOS MUNICIPALES',
            'COMPRA DE ÚTILES DE LIMPIEZA',
            'PAGO DE FLETES',
            'COBRO DE ALQUILERES',
            'GASTOS VARIOS',
        ];

        // Initial balance movement (type B)
        DB::transaction(function () use ($userId) {
            $movement = Movement::create([
                'date' => Carbon::now()->subMonths(6)->startOfMonth(),
                'description' => 'SALDO INICIAL DE CAJA',
                'type' => 'B',
                'amount' => 50000,
                'user_id' => $userId,
            ]);

            Box::create(['movement_id' => $movement->id]);
        });

        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(1, 180));
            $type = rand(0, 3) > 0 ? 'D' : 'C'; // 75% debe, 25% haber
            $amount = $type === 'D' ? rand(1000, 30000) : rand(500, 15000);

            DB::transaction(function () use ($date, $type, $amount, $cashDescriptions, $userId, $allPeople) {
                $movement = Movement::create([
                    'date' => $date,
                    'description' => $cashDescriptions[array_rand($cashDescriptions)],
                    'type' => $type,
                    'amount' => $amount,
                    'person_id' => rand(0, 4) > 0 ? $allPeople->random()->id : null,
                    'user_id' => $userId,
                ]);

                Box::create(['movement_id' => $movement->id]);
            });
        }

        // ─── Bank (Transaction) Movements ───
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
            'COBRO POR ARRENDAMIENTO',
            'GASTOS DE COMISIÓN BANCARIA',
            'TRANSFERENCIA ENTRE CUENTAS',
            'PAGO DE MATERIA PRIMA',
            'COBRO DE INTERESES BANCARIOS',
            'PAGO DE SERVICIOS DE ASESORÍA',
            'DEPÓSITO DE CLIENTE',
            'PAGO DE OBLIGACIONES LABORALES',
        ];

        $accounts = Account::all();
        $paymentTypes = ['CH', 'T'];

        // Initial balance movement (type B) for each account
        foreach ($accounts as $account) {
            DB::transaction(function () use ($account, $userId) {
                $movement = Movement::create([
                    'date' => Carbon::now()->subMonths(6)->startOfMonth(),
                    'description' => 'SALDO INICIAL CUENTA '.$account->name,
                    'type' => 'B',
                    'amount' => rand(50000, 500000),
                    'user_id' => $userId,
                ]);

                Transaction::create([
                    'payment_type' => 'T',
                    'account_id' => $account->id,
                    'movement_id' => $movement->id,
                    'number_check' => 'TRF-SI-'.$account->id,
                ]);
            });
        }

        for ($i = 0; $i < 50; $i++) {
            $account = $accounts->random();
            $date = Carbon::now()->subDays(rand(1, 180));
            $type = rand(0, 3) > 0 ? 'D' : 'C';
            $amount = $type === 'D' ? rand(5000, 100000) : rand(2000, 50000);
            $paymentType = $paymentTypes[array_rand($paymentTypes)];

            DB::transaction(function () use ($account, $date, $type, $amount, $paymentType, $bankDescriptions, $userId, $allPeople) {
                $movement = Movement::create([
                    'date' => $date,
                    'description' => $bankDescriptions[array_rand($bankDescriptions)],
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

        // ─── RRHH: Empleados ───
        $rrhhDept = Departament::where('area', 'Recursos Humanos')->first();
        if ($rrhhDept) {
            $employeePeople = collect([
                ['ci' => '78901234', 'full_name' => 'CARLOS ANDRÉS LÓPEZ GARCÍA', 'phone' => '71234567'],
                ['ci' => '89012345', 'full_name' => 'MARÍA FERNANDA ROJAS SANDOVAL', 'phone' => '72345678'],
                ['ci' => '90123456', 'full_name' => 'JOSÉ LUIS MARTÍNEZ PAREDES', 'phone' => '73456789'],
                ['ci' => '11223344', 'full_name' => 'ANA BELÉN CASTRO VARGAS', 'phone' => '74567890'],
            ])->map(fn ($data) => Person::firstOrCreate(['ci' => $data['ci']], $data));

            $employees = collect([
                ['code' => 'EMP-0001', 'position' => 'JEFE DE RECURSOS HUMANOS', 'base_salary' => 12000],
                ['code' => 'EMP-0002', 'position' => 'ASISTENTE DE RRHH', 'base_salary' => 6000],
                ['code' => 'EMP-0003', 'position' => 'ENCARGADO DE PLANILLAS', 'base_salary' => 8000],
                ['code' => 'EMP-0004', 'position' => 'RECEPCIONISTA', 'base_salary' => 4500],
            ])->map(fn ($data, $i) => Employee::create([
                'person_id' => $employeePeople[$i]->id,
                'department_id' => $rrhhDept->id,
                'code' => $data['code'],
                'position' => $data['position'],
                'base_salary' => $data['base_salary'],
                'hire_date' => '2024-01-15',
                'status' => 'active',
            ]));

            // ─── RRHH: Planilla demo ───
            Payroll::create([
                'period' => now()->format('Y-m'),
                'user_id' => $userId,
                'status' => 'pending',
            ]);
        }
    }
}
