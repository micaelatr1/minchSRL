<?php

use App\Models\Contract;
use App\Models\Movement;
use App\Models\Person;
use App\Models\User;
use Carbon\CarbonImmutable;

test('belongs to person', function () {
    $person = Person::factory()->create();
    $contract = Contract::factory()->create(['person_id' => $person->id]);

    expect($contract->person->id)->toBe($person->id);
});

test('belongs to user', function () {
    $user = User::factory()->create();
    $contract = Contract::factory()->create(['user_id' => $user->id]);

    expect($contract->user->id)->toBe($user->id);
});

test('has many movements', function () {
    $contract = Contract::factory()->create();
    Movement::factory()->count(3)->create([
        'contract_id' => $contract->id,
        'person_id' => $contract->person_id,
    ]);

    expect($contract->movements)->toHaveCount(3);
});

test('generates code on creating', function () {
    $contract = Contract::factory()->create();

    expect($contract->code)->toMatch('/^CONT-\d{5}$/');
});

test('consecutive codes increment', function () {
    $first = Contract::factory()->create();
    $second = Contract::factory()->create();

    expect((int) substr($second->code, 5))->toBe((int) substr($first->code, 5) + 1);
});

test('paid amount sums credit movements', function () {
    $contract = Contract::factory()->create(['total_amount' => 10000]);
    Movement::factory()->create([
        'contract_id' => $contract->id,
        'person_id' => $contract->person_id,
        'type' => 'C',
        'amount' => 3000,
    ]);
    Movement::factory()->create([
        'contract_id' => $contract->id,
        'person_id' => $contract->person_id,
        'type' => 'C',
        'amount' => 1500,
    ]);

    expect($contract->paid_amount)->toBe(4500.0);
});

test('remaining amount', function () {
    $contract = Contract::factory()->create(['total_amount' => 10000]);
    Movement::factory()->create([
        'contract_id' => $contract->id,
        'person_id' => $contract->person_id,
        'type' => 'C',
        'amount' => 4000,
    ]);

    expect($contract->remaining_amount)->toBe(6000.0);
});

test('remaining amount never negative', function () {
    $contract = Contract::factory()->create(['total_amount' => 10000]);
    Movement::factory()->create([
        'contract_id' => $contract->id,
        'person_id' => $contract->person_id,
        'type' => 'C',
        'amount' => 15000,
    ]);

    expect($contract->remaining_amount)->toBe(0.0);
});

test('progress percentage', function () {
    $contract = Contract::factory()->create(['total_amount' => 10000]);
    Movement::factory()->create([
        'contract_id' => $contract->id,
        'person_id' => $contract->person_id,
        'type' => 'C',
        'amount' => 2500,
    ]);

    expect($contract->progress)->toBe(25.0);
});

test('progress zero when no payments', function () {
    $contract = Contract::factory()->create(['total_amount' => 10000]);

    expect($contract->progress)->toBe(0.0);
});

test('cast dates', function () {
    $contract = Contract::factory()->create([
        'start_date' => '2026-07-01',
    ]);

    expect($contract->start_date)->toBeInstanceOf(CarbonImmutable::class);
});
