<?php

use App\Models\Liquidation;
use App\Services\LiquidationCalculator;

test('replica los pesos tms y tmns del formulario', function () {
    $c = LiquidationCalculator::calculate(Liquidation::factory()->zn()->make());

    // tmh 22.720 con 11.45% de humedad y 1% de merma
    expect($c['tms'])->toEqualWithDelta(20.119, 0.0001)
        ->and($c['tmns'])->toEqualWithDelta(19.918, 0.0001)
        ->and($c['ag'])->toEqualWithDelta(691, 0.0001);
});

test('en zinc valorMetal se redondea a 2 decimales, igual que el formulario', function () {
    $c = LiquidationCalculator::calculate(Liquidation::factory()->zn()->make());

    $esperado = (54.06 - 8) * 3434 / 100;

    expect($c['valorMetal'])->toEqualWithDelta(round($esperado, 2), 1e-9)
        ->and($c['totalplata'])->toEqualWithDelta(round($c['totalplata'], 2), 1e-9);
});

test('en plomo valorMetal se redondea a 2 decimales y aplica 0.95', function () {
    $c = LiquidationCalculator::calculate(Liquidation::factory()->pb()->make());

    $base = (52.88 - 3) * 1936 / 100;
    $esperado = round($base, 2) * 0.95;

    expect($c['valorMetal'])->toEqualWithDelta($esperado, 1e-9);
});

test('calcula las penalidades por contaminantes como el formulario', function () {
    $c = LiquidationCalculator::calculate(Liquidation::factory()->zn()->make());

    expect($c['feTotal'])->toEqualWithDelta(4.5, 1e-9)      // (9.5 - 8) * (3 / 1)
        ->and($c['sio2Total'])->toEqualWithDelta(6, 1e-9)   // (5 - 3) * (3 / 1)
        ->and($c['asTotal'])->toEqualWithDelta(0, 1e-9)
        ->and($c['sbTotal'])->toEqualWithDelta(0, 1e-9)
        ->and($c['snTotal'])->toEqualWithDelta(0, 1e-9)
        ->and($c['totalCT'])->toEqualWithDelta(165.6, 1e-9); // 90 maquila + 65.1 base + 10.5 penalidades
});

test('mantiene las identidades del cuadro de produccion', function () {
    $l = Liquidation::factory()->zn()->make();
    $c = LiquidationCalculator::calculate($l);

    expect($c['totalDeducc'])->toEqualWithDelta($c['totalRM'] + $c['totalAportes'], 1e-6)
        ->and($c['liquidoPagable'])->toEqualWithDelta($c['totalBs'] - $c['totalDeducc'], 1e-6)
        ->and($c['totalBs'])->toEqualWithDelta($c['totalUSD'] * (float) $l->tc, 1e-6)
        ->and($c['totalUSD'])->toEqualWithDelta($c['totalNetoUSD'] - $c['totalGastos'], 1e-6);
});

test('suma los aportes sobre el valor liquidado en bolivianos', function () {
    $l = Liquidation::factory()->pb()->make();
    $c = LiquidationCalculator::calculate($l);

    $esperado = $c['totalBs'] * (
        (float) $l->cns_pct + (float) $l->comibol_pct + (float) $l->fedecomin_pct
        + (float) $l->fencomin_pct + (float) $l->aporte_coop_pct
    ) / 100;

    expect($c['totalAportes'])->toEqualWithDelta($esperado, 1e-6)
        ->and($c['totalAportes'])->toBeGreaterThan(0);
});
