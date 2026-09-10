<?php

use App\Models\Person;
use App\Models\Supplier;
use App\Services\PersonSupplierService;

beforeEach(function () {
    $this->service = app(PersonSupplierService::class);
});

test('resolves supplier from existing person', function () {
    $person = Person::factory()->create(['ci' => '12345', 'full_name' => 'Juan Perez']);

    $supplier = $this->service->resolve('12345', 'Juan Perez Updated');

    expect($supplier)->toBeInstanceOf(Supplier::class)
        ->and($supplier->person_id)->toBe($person->id);
});

test('resolves creates new person and supplier', function () {
    $supplier = $this->service->resolve('99999', 'Nueva Persona', '77777777');

    expect($supplier)->toBeInstanceOf(Supplier::class)
        ->and($supplier->person->ci)->toBe('99999')
        ->and($supplier->person->full_name)->toBe('Nueva Persona')
        ->and($supplier->description)->toBe('Proveedor registrado automáticamente');
});

test('updates existing person name and phone', function () {
    $person = Person::factory()->create([
        'ci' => '11111',
        'full_name' => 'Old Name',
        'phone' => null,
    ]);

    $this->service->resolve('11111', 'New Name', '76543210');
    $person->refresh();

    expect($person->full_name)->toBe('New Name')
        ->and($person->phone)->toBe('76543210');
});

test('reuses existing supplier', function () {
    $person = Person::factory()->create(['ci' => '22222']);
    $existing = Supplier::factory()->create([
        'person_id' => $person->id,
        'description' => 'Existing Supplier',
    ]);

    $supplier = $this->service->resolve('22222', 'Some Name');

    expect($supplier->id)->toBe($existing->id);
});
