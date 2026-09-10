<?php

use App\Models\Box;
use App\Models\Movement;
use App\Models\Person;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

test('belongs to person', function () {
    $person = Person::factory()->create();
    $movement = Movement::factory()->create(['person_id' => $person->id]);

    expect($movement->person->id)->toBe($person->id);
});

test('belongs to user', function () {
    $user = User::factory()->create();
    $movement = Movement::factory()->create(['user_id' => $user->id]);

    expect($movement->user->id)->toBe($user->id);
});

test('has transaction', function () {
    $movement = Movement::factory()->create();
    Transaction::factory()->create(['movement_id' => $movement->id]);

    expect($movement->transaction)->not->toBeNull();
});

test('has box', function () {
    $movement = Movement::factory()->create();
    Box::factory()->create(['movement_id' => $movement->id]);

    expect($movement->box)->not->toBeNull();
});

test('get supplier returns supplier when person has supplier', function () {
    $person = Person::factory()->create();
    $supplier = $person->supplier()->create(['description' => 'Test']);
    $movement = Movement::factory()->create(['person_id' => $person->id]);

    expect($movement->supplier->id)->toBe($supplier->id);
});

test('get supplier returns person when no supplier', function () {
    $person = Person::factory()->create();
    $movement = Movement::factory()->create(['person_id' => $person->id]);

    expect($movement->supplier->id)->toBe($person->id);
});

test('calculate label', function () {
    $movement = Movement::factory()->create(['amount' => 100.00]);

    expect($movement->calculate_label)->toBeString();
});

test('date label', function () {
    Carbon::setLocale('es');
    $movement = Movement::factory()->create(['date' => '2026-07-01']);

    expect($movement->date_label)->toBeString();
});

test('formatted last number from transaction', function () {
    $movement = Movement::factory()->create();
    Transaction::factory()->create([
        'movement_id' => $movement->id,
        'number_check' => '001',
    ]);

    expect($movement->formatted_last_number)->toBe('001');
});

test('formatted last number empty when no transaction', function () {
    $movement = Movement::factory()->create();

    expect($movement->formatted_last_number)->toBe('');
});

test('types', function () {
    $d = Movement::factory()->create(['type' => 'D']);
    $c = Movement::factory()->create(['type' => 'C']);
    $b = Movement::factory()->create(['type' => 'B']);

    expect($d->type)->toBe('D')
        ->and($c->type)->toBe('C')
        ->and($b->type)->toBe('B');
});
