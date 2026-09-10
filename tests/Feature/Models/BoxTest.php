<?php

use App\Models\Box;
use App\Models\Movement;

test('belongs to movement', function () {
    $movement = Movement::factory()->create();
    $box = Box::factory()->create(['movement_id' => $movement->id]);

    expect($box->movement->id)->toBe($movement->id);
});

test('number label format', function () {
    $movement = Movement::factory()->create(['type' => 'D', 'date' => '2026-07-15']);
    $box = Box::factory()->create(['movement_id' => $movement->id]);

    expect($box->number_label)->toMatch('/^DOC-\d{8}$/');
});

test('formatted last number format', function () {
    $movement = Movement::factory()->create(['type' => 'D', 'date' => '2026-07-15']);
    $box = Box::factory()->create(['movement_id' => $movement->id]);

    expect($box->formatted_last_number)->toMatch('/^\d{8}$/');
});

test('fillable fields', function () {
    $movement = Movement::factory()->create(['type' => 'D', 'date' => '2026-07-15']);
    $box = Box::factory()->create(['movement_id' => $movement->id]);

    expect($box->movement_id)->toBe($movement->id)
        ->and($box->number)->not->toBeNull();
});
