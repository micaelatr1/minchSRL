<?php

use App\Models\Box;
use App\Models\Customer;
use App\Models\Movement;
use App\Models\Person;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Services\AccountStatementService;

beforeEach(function () {
    $this->service = app(AccountStatementService::class);
});

test('types constant', function () {
    expect(AccountStatementService::TYPES)->toBe(['supplier', 'customer']);
});

test('resolve supplier', function () {
    $supplier = Supplier::factory()->create();

    $result = $this->service->resolve('supplier', $supplier->id);

    expect($result)->toBeInstanceOf(Supplier::class)
        ->and($result->id)->toBe($supplier->id);
});

test('resolve customer', function () {
    $customer = Customer::factory()->create();

    $result = $this->service->resolve('customer', $customer->id);

    expect($result)->toBeInstanceOf(Customer::class)
        ->and($result->id)->toBe($customer->id);
});

test('resolve throws for invalid type', function () {
    $this->service->resolve('invalid', 1);
})->throws(InvalidArgumentException::class);

test('holder label', function () {
    expect($this->service->holderLabel('supplier'))->toBe('Proveedor')
        ->and($this->service->holderLabel('customer'))->toBe('Cliente')
        ->and($this->service->holderLabel('other'))->toBe('Titular');
});

test('movements for person', function () {
    $person = Person::factory()->create();
    Movement::factory()->create(['person_id' => $person->id, 'type' => 'D', 'amount' => 100, 'date' => '2026-07-01']);
    Movement::factory()->create(['person_id' => $person->id, 'type' => 'C', 'amount' => 50, 'date' => '2026-07-15']);

    $query = $this->service->movementsForPerson($person->id);
    $movements = $query->get();

    expect($movements)->toHaveCount(2)
        ->and($movements[0]->balance)->toEqual(100.0)
        ->and($movements[1]->balance)->toEqual(50.0);
});

test('totals for person', function () {
    $person = Person::factory()->create();
    Movement::factory()->create(['person_id' => $person->id, 'type' => 'D', 'amount' => 200]);
    Movement::factory()->create(['person_id' => $person->id, 'type' => 'B', 'amount' => 100]);
    Movement::factory()->create(['person_id' => $person->id, 'type' => 'C', 'amount' => 50]);

    $totals = $this->service->totalsForPerson($person->id);

    expect($totals['amountD'])->toBe(300.0)
        ->and($totals['amountC'])->toBe(50.0)
        ->and($totals['balance'])->toBe(250.0);
});

test('document reference from box', function () {
    $movement = Movement::factory()->create();
    $box = Box::factory()->create(['movement_id' => $movement->id, 'number' => 3]);
    $movement->setRelation('box', $box);

    $ref = $this->service->documentReference($movement);

    expect($ref)->toBe($box->number_label);
});

test('document reference from transaction', function () {
    $movement = Movement::factory()->create();
    $transaction = Transaction::factory()->create([
        'movement_id' => $movement->id,
        'payment_type' => 'T',
        'number_check' => '001',
    ]);
    $movement->setRelation('transaction', $transaction);

    $ref = $this->service->documentReference($movement);

    expect($ref)->toBe($transaction->number_label);
});

test('document reference empty when no box or transaction', function () {
    $movement = Movement::factory()->create();

    $ref = $this->service->documentReference($movement);

    expect($ref)->toBe('');
});
