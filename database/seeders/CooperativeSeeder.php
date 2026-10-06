<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $rows = [
            ['name' => 'COOP.MINERA CALAMARCA R.L', 'concession' => 'SANTIAGO DE CALAMARCA 2', 'mine' => null, 'NIM' => '05-0737-06', 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA CALAMARCA R.L', 'concession' => 'SANTIAGO DE CALAMARCA 3', 'mine' => null, 'NIM' => '05-0737-06', 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA HUARI HUARI', 'concession' => 'SAN JOSE', 'mine' => 'ESPERANZA', 'NIM' => '05-0290-06', 'nit' => 'sin definir', 'contribution' => 3, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA HUARI HUARI', 'concession' => 'SAN JOSE', 'mine' => 'SANTO TOMAS', 'NIM' => '05-0290-06', 'nit' => 'sin definir', 'contribution' => 3, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA HUARI HUARI', 'concession' => 'SAN JOSE', 'mine' => null, 'NIM' => '05-0290-06', 'nit' => 'sin definir', 'contribution' => 3, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA HUARI HUARI', 'concession' => 'SAN JOSE', 'mine' => 'SAN JOSE', 'NIM' => '05-0290-06', 'nit' => 'sin definir', 'contribution' => 3, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA HUARI HUARI', 'concession' => 'SAN JOSE', 'mine' => 'SANTA ROSA', 'NIM' => '05-0290-06', 'nit' => 'sin definir', 'contribution' => 3, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA JAYAQUILA L.R', 'concession' => 'SORPRESA JALLAQUILA', 'mine' => 'BLANQUITA 1', 'NIM' => '05-0639-06', 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA JAYAQUILA L.R', 'concession' => 'SORPRESA JALLAQUILA', 'mine' => 'BLANQUITA 2', 'NIM' => '05-0639-06', 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA NUEVO PORVENIR R.L', 'concession' => 'AREA MINERA SAN JOSE', 'mine' => 'MILAGROS', 'NIM' => '05-1106-06', 'nit' => 'sin definir', 'contribution' => 1.5, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA NUEVO PORVENIR R.L', 'concession' => 'AREA MINERA SAN JOSE', 'mine' => null, 'NIM' => '05-1106-07', 'nit' => 'sin definir', 'contribution' => 1.5, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA OLLERIAS R.L', 'concession' => 'AREA MINERA LA RESERVADA', 'mine' => null, 'NIM' => '05-1127-06', 'nit' => 'sin definir', 'contribution' => 1.5, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA SAN ANDRES R.L', 'concession' => 'SAN ISIDRO COPA ll', 'mine' => 'PAMPA QKOYA', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 2, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA SAN ANDRES R.L', 'concession' => 'SAN ISIDRO COPA ll', 'mine' => null, 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 2, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA TOLLOJCHI R.L', 'concession' => 'CONCESION ROSARIO 1', 'mine' => null, 'NIM' => '05-0467-06', 'nit' => 'sin definir', 'contribution' => 8, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA CERRO RICO R.L', 'concession' => null, 'mine' => 'SANTA CATALINA', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA 10 DE NOV R.L', 'concession' => null, 'mine' => null, 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA CERRO RICO R.L', 'concession' => null, 'mine' => null, 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA OLLERIAS R.L', 'concession' => 'AREA MINERA LA RESERVADA', 'mine' => 'SANTA CATALINA', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 1.5, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA ROSARIO R.L', 'concession' => null, 'mine' => 'WASHINGTON', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA ROSARIO R.L', 'concession' => null, 'mine' => 'CANDELARIA 70', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA COMPOTOSI R.L', 'concession' => null, 'mine' => 'ENCINAS', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA TOLLOJCHIR.L', 'concession' => null, 'mine' => null, 'NIM' => '05-0467-06', 'nit' => 'sin definir', 'contribution' => 8, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA KORY MAYU R.L', 'concession' => null, 'mine' => 'BOCAMEJORA MONJA 2', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA UNIFICADA DE POTOSI R.L', 'concession' => null, 'mine' => 'ROSARIO BAJO', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 1, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA UNIFICADA DE POTOSI R.L', 'concession' => null, 'mine' => 'SANTA RITA', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 1, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA UNIFICADA DE POTOSI R.L', 'concession' => null, 'mine' => 'FORZADOS 1', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 1, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA UNIFICADA DE POTOSI R.L', 'concession' => null, 'mine' => 'SANTA ELENA', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 1, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA UNIFICADA DE POTOSI R.L', 'concession' => null, 'mine' => null, 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 1, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA PAILAVIRI UNIFICADA R.L', 'concession' => null, 'mine' => 'BOCAMEJORA REAL SOCAVON', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'COOP.MINERA PAILAVIRI UNIFICADA R.L', 'concession' => null, 'mine' => null, 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI'],
            ['name' => 'CKACCHAS LIBRES Y PALLIRIS', 'concession' => null, 'mine' => 'SAN JUAN DEL ORO', 'NIM' => null, 'nit' => 'sin definir', 'contribution' => 0, 'comibol' => 1, 'municipality' => 'POTOSI']
        ];

        $rows = array_map(function ($row) use ($now) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;

            return $row;
        }, $rows);

        DB::table('cooperatives')->insert($rows);
    }
}
