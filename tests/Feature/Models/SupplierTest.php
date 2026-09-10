<?php

use App\Models\Movement;
use App\Models\Person;
use App\Models\Retention;
use App\Models\Supplier;

test('belongs to person', function () {
    $supplier = Supplier::factory()->create();

    expect($supplier->person)->not->toBeNull();
});

test('has retentions', function () {
    $supplier = Supplier::factory()->create();
    Retention::factory()->create(['supplier_id' => $supplier->id]);

    expect($supplier->retentions)->toHaveCount(1);
});

test('has movements', function () {
    $person = Person::factory()->create();
    $supplier = Supplier::factory()->create(['person_id' => $person->id]);
    Movement::factory()->create(['person_id' => $person->id]);

    expect($supplier->movements)->toHaveCount(1);
});

test('full name accessor', function () {
    $person = Person::factory()->create(['full_name' => 'Carlos Lopez']);
    $supplier = Supplier::factory()->create(['person_id' => $person->id]);

    expect($supplier->full_name)->toBe('Carlos Lopez');
});

test('ci accessor', function () {
    $person = Person::factory()->create(['ci' => '87654321']);
    $supplier = Supplier::factory()->create(['person_id' => $person->id]);

    expect($supplier->ci)->toBe('87654321');
});

test('phone accessor', function () {
    $person = Person::factory()->create(['phone' => '76543210']);
    $supplier = Supplier::factory()->create(['person_id' => $person->id]);

    expect($supplier->phone)->toBe('76543210');
});
