<?php

use App\Models\Cooperative;
use App\Models\Customer;
use App\Models\Person;

test('has customers', function () {
    $cooperative = Cooperative::factory()->create();
    Customer::factory()->create(['cooperative_id' => $cooperative->id]);

    expect($cooperative->customers)->toHaveCount(1);
});

test('has suppliers through person', function () {
    $cooperative = Cooperative::factory()->create();
    $person = Person::factory()->create();
    $cooperative->customers()->create(['person_id' => $person->id]);

    expect($cooperative->customers)->toHaveCount(1);
});

test('fillable fields', function () {
    $cooperative = Cooperative::factory()->create([
        'name' => 'COOP MINERA TEST',
        'concession' => 'CCC-001',
        'mine' => 'Mina Test',
    ]);

    expect($cooperative->name)->toBe('COOP MINERA TEST');
});
