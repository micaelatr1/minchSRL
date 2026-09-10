<?php

use App\Livewire\AccountStatements\AccountStatementView;
use App\Models\Customer;
use App\Models\Movement;
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

test('renders account statement view for supplier', function () {
    $supplier = Supplier::factory()->create();

    Livewire::test(AccountStatementView::class, [
        'type' => 'supplier',
        'id' => $supplier->id,
    ])->assertOk();
});

test('renders account statement view for customer', function () {
    $customer = Customer::factory()->create();

    Livewire::test(AccountStatementView::class, [
        'type' => 'customer',
        'id' => $customer->id,
    ])->assertOk();
});

test('aborts 404 for invalid type', function () {
    $response = $this->get(route('accounts.statement.view', ['type' => 'invalid', 'id' => 1]));
    $response->assertNotFound();
});

test('can store a movement in account statement', function () {
    $supplier = Supplier::factory()->create();

    Livewire::test(AccountStatementView::class, [
        'type' => 'supplier',
        'id' => $supplier->id,
    ])
        ->set('amount', 1000.00)
        ->set('type', 'D')
        ->set('date', now()->format('Y-m-d'))
        ->set('description', 'Movement description')
        ->set('vol', 'VOL-001')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['amount' => 1000.00, 'person_id' => $supplier->person_id]);
});

test('can update a movement in account statement', function () {
    $supplier = Supplier::factory()->create();
    $movement = Movement::factory()->create([
        'person_id' => $supplier->person_id,
        'amount' => 500.00,
        'type' => 'D',
        'date' => now()->format('Y-m-d'),
    ]);

    Livewire::test(AccountStatementView::class, [
        'type' => 'supplier',
        'id' => $supplier->id,
    ])
        ->dispatch('load::movement', $movement)
        ->set('amount', 750.00)
        ->set('description', 'Updated movement')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['id' => $movement->id, 'amount' => 750.00]);
});

test('validates required fields on store', function () {
    $supplier = Supplier::factory()->create();

    Livewire::test(AccountStatementView::class, [
        'type' => 'supplier',
        'id' => $supplier->id,
    ])
        ->set('date', '')
        ->call('store')
        ->assertHasErrors(['amount', 'type', 'date', 'description']);
});

test('clear resets movement form', function () {
    $supplier = Supplier::factory()->create();

    Livewire::test(AccountStatementView::class, [
        'type' => 'supplier',
        'id' => $supplier->id,
    ])
        ->set('amount', 100)
        ->set('description', 'test')
        ->call('clear')
        ->assertSet('amount', null)
        ->assertSet('description', null);
});
