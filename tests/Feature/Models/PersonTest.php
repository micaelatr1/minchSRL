<?php

use App\Models\Customer;
use App\Models\Movement;
use App\Models\Person;
use App\Models\Supplier;

test('has supplier', function () {
    $person = Person::factory()->create();
    Supplier::factory()->create(['person_id' => $person->id]);

    expect($person->supplier)->not->toBeNull();
});

test('has customer', function () {
    $person = Person::factory()->create();
    Customer::factory()->create(['person_id' => $person->id]);

    expect($person->customer)->not->toBeNull();
});

test('has movements', function () {
    $person = Person::factory()->create();
    Movement::factory()->create(['person_id' => $person->id]);

    expect($person->movements)->toHaveCount(1);
});

test('fillable fields', function () {
    $person = Person::factory()->create([
        'ci' => '12345678',
        'full_name' => 'Juan Perez',
        'phone' => '77777777',
    ]);

    expect($person->ci)->toBe('12345678')
        ->and($person->full_name)->toBe('Juan Perez')
        ->and($person->phone)->toBe('77777777');
});
