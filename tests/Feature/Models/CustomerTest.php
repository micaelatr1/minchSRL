<?php

use App\Models\Cooperative;
use App\Models\Customer;
use App\Models\Movement;
use App\Models\Person;

test('belongs to person', function () {
    $customer = Customer::factory()->create();

    expect($customer->person)->not->toBeNull();
});

test('belongs to cooperative', function () {
    $cooperative = Cooperative::factory()->create();
    $customer = Customer::factory()->create(['cooperative_id' => $cooperative->id]);

    expect($customer->cooperative->id)->toBe($cooperative->id);
});

test('has movements', function () {
    $person = Person::factory()->create();
    $customer = Customer::factory()->create(['person_id' => $person->id]);
    Movement::factory()->create(['person_id' => $person->id]);

    expect($customer->movements)->toHaveCount(1);
});

test('full name accessor', function () {
    $person = Person::factory()->create(['full_name' => 'Maria Garcia']);
    $customer = Customer::factory()->create(['person_id' => $person->id]);

    expect($customer->full_name)->toBe('Maria Garcia');
});

test('ci accessor', function () {
    $person = Person::factory()->create(['ci' => '12345678']);
    $customer = Customer::factory()->create(['person_id' => $person->id]);

    expect($customer->ci)->toBe('12345678');
});

test('phone accessor', function () {
    $person = Person::factory()->create(['phone' => '71234567']);
    $customer = Customer::factory()->create(['person_id' => $person->id]);

    expect($customer->phone)->toBe('71234567');
});
