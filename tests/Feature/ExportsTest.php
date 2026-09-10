<?php

use App\Exports\BankBookReportExport;
use App\Exports\BaseReportExport;
use App\Exports\BoxReportExport;
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
