<?php

use App\Livewire\Customers\CustomerComponent;
use App\Models\Cooperative;
use App\Models\Customer;
use App\Models\Person;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

test('renders customer list', function () {
    $person = Person::factory()->create(['full_name' => 'Cliente Test']);
    Customer::factory()->create(['person_id' => $person->id]);

    Livewire::test(CustomerComponent::class)
        ->assertOk()
        ->assertSee('Cliente Test');
});

test('can create a customer', function () {
    $cooperative = Cooperative::factory()->create();

    Livewire::test(CustomerComponent::class)
        ->set('full_name', 'Nuevo Cliente')
        ->set('ci', '2222222')
        ->set('cooperative_id', $cooperative->id)
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('people', ['ci' => '2222222']);
    $this->assertDatabaseHas('customers', ['cooperative_id' => $cooperative->id]);
});

test('can edit a customer', function () {
    $person = Person::factory()->create(['full_name' => 'Original']);
    $customer = Customer::factory()->create(['person_id' => $person->id]);

    Livewire::test(CustomerComponent::class)
        ->dispatch('load::customer', $customer->id)
        ->set('full_name', 'Updated')
        ->call('update')
        ->assertHasNoErrors();

    expect($person->fresh()->full_name)->toBe('Updated');
});
