<?php

use App\Exports\BankBookReportExport;
use App\Exports\BaseReportExport;
use App\Exports\BoxReportExport;
use App\Exports\LiquidationReportExport;
use App\Exports\RetentionReportExport;
use Illuminate\Support\Collection;

test('bank book report export columns and mapRow', function () {
    $data = [
        ['number' => 1, 'date' => '2025-10-01', 'description' => 'Test', 'ref' => 'F001', 'debito' => 100, 'credito' => 0, 'saldo' => 100],
        ['number' => 2, 'date' => '2025-10-02', 'description' => 'Test 2', 'ref' => 'F002', 'debito' => 0, 'credito' => 50, 'saldo' => 50],
    ];
    $export = new BankBookReportExport($data);

    $collection = $export->collection();
    expect($collection)->toBeInstanceOf(Collection::class);

    $reflection = new ReflectionClass($export);
    $titleProp = $reflection->getProperty('title');
    expect($titleProp->getValue($export))->toBe('LIBRO DE BANCOS');
});

test('box report export columns and mapRow', function () {
    $data = [
        ['number' => 1, 'date' => '2025-10-01', 'description' => 'Box test', 'debe' => 200, 'haber' => 0, 'saldo' => 200],
    ];
    $export = new BoxReportExport($data);

    $reflection = new ReflectionClass($export);
    $titleProp = $reflection->getProperty('title');
    expect($titleProp->getValue($export))->toBe('LIBRO DE CAJA');
});

test('retention report export columns and mapRow', function () {
    $data = [
        ['code' => 'R001', 'date' => '2025-10-01', 'supplier' => 'Supplier A', 'type' => 'S', 'nit' => '12345', 'amount' => 1000, 'discounts' => 130, 'total' => 1130],
    ];
    $export = new RetentionReportExport($data);

    $reflection = new ReflectionClass($export);
    $titleProp = $reflection->getProperty('title');
    expect($titleProp->getValue($export))->toBe('REPORTE DE RETENCIONES');
});

test('base report export numToLetter', function () {
    $mock = new class([]) extends BaseReportExport
    {
        protected function mapRow($item): array
        {
            return [];
        }
    };

    $reflection = new ReflectionClass($mock);
    $method = $reflection->getMethod('numToLetter');

    expect($method->invoke($mock, 1))->toBe('A');
    expect($method->invoke($mock, 26))->toBe('Z');
    expect($method->invoke($mock, 27))->toBe('AA');
    expect($method->invoke($mock, 52))->toBe('AZ');
    expect($method->invoke($mock, 53))->toBe('BA');
});

test('base report export calculateTotals', function () {
    $data = [
        ['number' => 1, 'debito' => 100, 'credito' => 0, 'saldo' => 100],
        ['number' => 2, 'debito' => 50, 'credito' => 30, 'saldo' => 120],
    ];
    $export = new class($data) extends BaseReportExport
    {
        protected string $title = 'TEST';

        protected array $columns = [
            ['label' => 'N', 'align' => 'L'],
            ['label' => 'Debe', 'align' => 'R'],
            ['label' => 'Haber', 'align' => 'R'],
            ['label' => 'Saldo', 'align' => 'R'],
        ];

        protected function mapRow($item): array
        {
            return [$item['number'], $item['debito'], $item['credito'], $item['saldo']];
        }
    };

    $reflection = new ReflectionClass($export);
    $method = $reflection->getMethod('calculateTotals');
    $totals = $method->invoke($export);

    expect($totals[0])->toBe(150.0);
    expect($totals[1])->toBe(30.0);
    expect($totals[2])->toBe(220.0);
});

test('liquidation report export title and columns', function () {
    $export = new LiquidationReportExport([]);

    $reflection = new ReflectionClass($export);

    expect($reflection->getProperty('title')->getValue($export))->toBe('CUADRO PRODUCCION')
        ->and($reflection->getProperty('subtitle')->getValue($export))->toBe('(EXPRESADO EN DÓLARES AMERICANOS Y BOLIVIANOS)')
        ->and($reflection->getProperty('columns')->getValue($export))->toHaveCount(27)
        ->and($export->collection())->toBeInstanceOf(Collection::class);
});

test('liquidation report export mapRow keeps the 27 column order', function () {
    $export = new LiquidationReportExport([]);

    $method = (new ReflectionClass($export))->getMethod('mapRow');
    $row = $method->invoke($export, [
        'n' => 1,
        'date' => '05/10/2026',
        'lote' => 'MCPB-04/26',
        'proveedor' => 'Juan Pérez Mamani',
        'tmh' => 35.92,
        'tmns' => 31.9,
        'valorBrutoUsd' => 1000.5,
        'liquidoPagable' => 900.5,
        'valorLiq' => 962.5,
    ]);

    expect($row)->toHaveCount(27)
        ->and($row[0])->toBe(1)
        ->and($row[1])->toBe('05/10/2026')
        ->and($row[2])->toBe('MCPB-04/26')
        ->and($row[3])->toBe('Juan Pérez Mamani')
        ->and($row[7])->toBe(31.9)
        ->and($row[16])->toBe(1000.5)
        ->and($row[25])->toBe(900.5)
        ->and($row[26])->toBe(962.5);
});

test('liquidation report export only totals the meaningful columns', function () {
    $data = [
        ['tmh' => 10, 'tmns' => 9, 'valorBrutoUsd' => 100, 'liquidoPagable' => 90, 'valorLiq' => 100],
        ['tmh' => 20, 'tmns' => 18, 'valorBrutoUsd' => 200, 'liquidoPagable' => 180, 'valorLiq' => 200],
    ];

    $export = new LiquidationReportExport($data);
    $totals = (new ReflectionClass($export))->getMethod('calculateTotals')->invoke($export);

    expect($totals)->toHaveCount(26)
        ->and($totals[2])->toBeNull()                   // PROVEEDOR (columna 4)
        ->and($totals[3])->toEqualWithDelta(30, 1e-6)   // PESO BRUTO TMH
        ->and($totals[4])->toBeNull()                   // HUMEDAD (porcentaje, no se suma)
        ->and($totals[6])->toEqualWithDelta(27, 1e-6)   // PESO NETO TMS
        ->and($totals[15])->toEqualWithDelta(300, 1e-6) // VALOR BRUTO USD
        ->and($totals[24])->toEqualWithDelta(270, 1e-6) // LIQUIDO PAGABLE Bs
        ->and($totals[25])->toEqualWithDelta(300, 1e-6); // VALOR LIQ. Bs
});
