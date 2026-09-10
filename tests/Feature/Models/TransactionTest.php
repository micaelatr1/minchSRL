<?php

use App\Models\Movement;
use App\Models\Transaction;

test('belongs to account', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->account)->not->toBeNull();
});

test('belongs to movement', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->movement)->not->toBeNull();
});

test('number label transfer', function () {
    $transaction = Transaction::factory()->create([
        'payment_type' => 'T',
        'number_check' => '12345',
    ]);

    expect($transaction->number_label)->toBe('T-12345');
});

test('number label cheque', function () {
    $transaction = Transaction::factory()->create([
        'payment_type' => 'CH',
        'number_check' => '98765',
    ]);

    expect($transaction->number_label)->toBe('CH-98765');
});

test('number label doc default', function () {
    $transaction = Transaction::factory()->create([
        'number_check' => null,
    ]);

    expect($transaction->number_label)->toMatch('/^DOC-\d{8}$/');
});

test('number label doc contains actual number', function () {
    $movement = Movement::factory()->create(['type' => 'D', 'date' => '2026-07-15']);
    $transaction = Transaction::factory()->create([
        'movement_id' => $movement->id,
        'number_check' => null,
    ]);

    expect($transaction->number_label)->toBe('DOC-'.str_pad($transaction->number, 8, '0', STR_PAD_LEFT));
});

test('formatted last number format', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->formatted_last_number)->toMatch('/^\d{8}$/');
});

test('payment types', function () {
    $t = Transaction::factory()->create(['payment_type' => 'T']);
    $ch = Transaction::factory()->create(['payment_type' => 'CH']);

    expect($t->payment_type)->toBe('T')
        ->and($ch->payment_type)->toBe('CH');
});
