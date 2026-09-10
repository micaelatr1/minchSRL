<?php

use App\Livewire\Suppliers\SupplierComponent;
use App\Models\Person;
use App\Models\Supplier;
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

test('renders supplier list', function () {
    $person = Person::factory()->create(['full_name' => 'Proveedor Test']);
    Supplier::factory()->create(['person_id' => $person->id]);

    Livewire::test(SupplierComponent::class)
        ->assertOk()
        ->assertSee('Proveedor Test');
});

test('can create a supplier', function () {
    Livewire::test(SupplierComponent::class)
        ->set('full_name', 'Nuevo Proveedor')
        ->set('ci', '1111111')
        ->set('description', 'Proveedor de prueba')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('people', ['ci' => '1111111']);
    $this->assertDatabaseHas('suppliers', ['description' => 'Proveedor de prueba']);
});

test('can edit a supplier', function () {
    $person = Person::factory()->create(['full_name' => 'Original']);
    $supplier = Supplier::factory()->create(['person_id' => $person->id]);

    Livewire::test(SupplierComponent::class)
        ->dispatch('load::supplier', $supplier->id)
        ->set('full_name', 'Updated')
        ->call('update')
        ->assertHasNoErrors();

    expect($person->fresh()->full_name)->toBe('Updated');
});
