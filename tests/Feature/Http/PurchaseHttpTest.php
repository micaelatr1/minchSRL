<?php

use App\Livewire\Inventory\PurchaseComponent;
use App\Livewire\Inventory\PurchaseFormComponent;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::all());
    $this->actingAs($user);
});

test('purchase list page can be rendered', function () {
    $this->get(route('inventory.purchases'))
        ->assertStatus(200);
});

test('purchase form page can be rendered', function () {
    $this->get(route('inventory.purchases.form'))
        ->assertStatus(200);
});

test('purchase form has required fields', function () {
    Livewire::test(PurchaseFormComponent::class)
        ->set('supplier_id', 999)
        ->call('save')
        ->assertHasErrors(['items']);
});

test('purchase component shows empty state', function () {
    Livewire::test(PurchaseComponent::class)
        ->assertSee('Sin compras registradas');
});
