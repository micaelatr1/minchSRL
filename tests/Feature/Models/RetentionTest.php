<?php

use App\Models\Discount;
use App\Models\Retention;
use App\Models\Supplier;
use Carbon\Carbon;

test('belongs to supplier', function () {
    $supplier = Supplier::factory()->create();
    $retention = Retention::factory()->create(['supplier_id' => $supplier->id]);

    expect($retention->supplier->id)->toBe($supplier->id);
});

test('has discounts', function () {
    $retention = Retention::factory()->create();
    Discount::factory()->create(['retention_id' => $retention->id]);

    expect($retention->discounts)->toHaveCount(1);
});

test('calculate total', function () {
    $retention = Retention::factory()->create(['amount' => 1000]);
    Discount::factory()->create(['retention_id' => $retention->id, 'amount' => 130]);
    Discount::factory()->create(['retention_id' => $retention->id, 'amount' => 30]);

    expect($retention->calculate_total)->toEqual(1160.0);
});

test('calculate label', function () {
    $retention = Retention::factory()->create(['amount' => 1000]);
    Discount::factory()->create(['retention_id' => $retention->id, 'amount' => 130]);

    expect($retention->calculate_label)->toBeString();
});

test('date label', function () {
    Carbon::setLocale('es');
    $retention = Retention::factory()->create(['date' => '2026-07-01']);

    expect($retention->date_label)->toBeString();
});

test('date code returns month abbreviation with code', function () {
    $retention = Retention::factory()->create(['date' => '2026-07-15', 'code' => '005']);

    expect($retention->date_code)->toBe('JUL-005');
});

test('date code for december', function () {
    $retention = Retention::factory()->create(['date' => '2026-12-01', 'code' => '010']);

    expect($retention->date_code)->toBe('DIC-010');
});

test('date code for january', function () {
    $retention = Retention::factory()->create(['date' => '2026-01-15', 'code' => '001']);

    expect($retention->date_code)->toBe('ENE-001');
});

test('type s and g', function () {
    $s = Retention::factory()->create(['type' => 'S']);
    $g = Retention::factory()->create(['type' => 'G']);

    expect($s->type)->toBe('S')
        ->and($g->type)->toBe('G');
});

test('status', function () {
    $active = Retention::factory()->create(['status' => '0']);
    $inactive = Retention::factory()->create(['status' => '1']);

    expect($active->status)->toBe('0')
        ->and($inactive->status)->toBe('1');
});
