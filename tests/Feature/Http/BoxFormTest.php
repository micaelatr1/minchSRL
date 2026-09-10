<?php

use App\Livewire\AccountBoxes\BoxFormComponent;
use App\Models\Movement;
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

test('renders box form', function () {
    Livewire::test(BoxFormComponent::class)
        ->assertOk();
});

test('can store a box debit movement', function () {
    Livewire::test(BoxFormComponent::class)
        ->set('ci', '111111')
        ->set('full_name', 'Carlos Box')
        ->set('phone', '70012345')
        ->set('amount', 250.00)
        ->set('type', 'D')
        ->set('date', now()->format('Y-m-d'))
        ->set('description', 'Gasto de caja chica')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['amount' => 250.00, 'type' => 'D']);
});

test('can store a box credit movement', function () {
    Livewire::test(BoxFormComponent::class)
        ->set('ci', '222222')
        ->set('full_name', 'Ana Box')
        ->set('amount', 500.00)
        ->set('type', 'C')
        ->set('date', now()->format('Y-m-d'))
        ->set('description', 'Reposicion de caja')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['amount' => 500.00, 'type' => 'C']);
});

test('validates required fields on box store', function () {
    Livewire::test(BoxFormComponent::class)
        ->set('date', '')
        ->call('store')
        ->assertHasErrors(['ci', 'full_name', 'amount', 'type', 'date', 'description']);
});

test('can update a box movement', function () {
    $person = Person::factory()->create(['ci' => '333333', 'phone' => '7777777', 'full_name' => 'Original Boxer']);
    $movement = Movement::factory()->create([
        'person_id' => $person->id,
        'amount' => 100.00,
        'type' => 'D',
        'date' => now()->format('Y-m-d'),
    ]);
    $movement->box()->create([]);

    Livewire::test(BoxFormComponent::class, ['id' => $movement->id])
        ->set('amount', 150.00)
        ->set('type', 'C')
        ->set('description', 'Updated box')
        ->set('ci', '333333')
        ->set('full_name', 'Updated Boxer')
        ->set('phone', '7777777')
        ->set('date', now()->format('Y-m-d'))
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['id' => $movement->id, 'amount' => 150.00, 'type' => 'C']);
});

test('supplier selected listener fills fields', function () {
    Livewire::test(BoxFormComponent::class)
        ->dispatch('supplier-selected', [
            'person_id' => 5,
            'ci' => '555555',
            'full_name' => 'Box Supplier',
            'phone' => '70555555',
        ])
        ->assertSet('ci', '555555')
        ->assertSet('person_id', 5)
        ->assertSet('full_name', 'Box Supplier')
        ->assertSet('phone', '70555555');
});

test('clear resets box form', function () {
    Livewire::test(BoxFormComponent::class)
        ->set('ci', '123')
        ->set('full_name', 'Test')
        ->set('amount', 100)
        ->call('clear')
        ->assertSet('ci', '')
        ->assertSet('full_name', '')
        ->assertSet('amount', null);
});
