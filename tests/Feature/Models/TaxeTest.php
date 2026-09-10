<?php

use App\Models\Retention;
use App\Models\Taxe;

test('has discounts', function () {
    $taxe = Taxe::factory()->create();
    $retention = Retention::factory()->create();
    $taxe->discounts()->create(['amount' => 100, 'retention_id' => $retention->id]);

    expect($taxe->discounts)->toHaveCount(1);
});

test('type s', function () {
    $taxe = Taxe::factory()->create(['type' => 'S']);
    expect($taxe->type)->toBe('S');
});

test('type g', function () {
    $taxe = Taxe::factory()->create(['type' => 'G']);
    expect($taxe->type)->toBe('G');
});

test('type a', function () {
    $taxe = Taxe::factory()->create(['type' => 'A']);
    expect($taxe->type)->toBe('A');
});

test('applied discount field', function () {
    $taxe = Taxe::factory()->create(['applied_discount' => 13.00]);

    expect((float) $taxe->applied_discount)->toBe(13.00);
});
