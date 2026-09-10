<?php

use App\Livewire\Retentions\RetentionComponent;
use App\Livewire\Retentions\RetentionComponentForm;
use App\Models\Retention;
use App\Models\Supplier;
use App\Models\Taxe;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);

    Taxe::factory()->create(['name' => 'RC-IVA', 'initials' => 'RC-IVA', 'number' => '001', 'applied_discount' => 13.00, 'type' => 'S']);
    Taxe::factory()->create(['name' => 'IT', 'initials' => 'IT', 'number' => '002', 'applied_discount' => 3.00, 'type' => 'A']);
    Taxe::factory()->create(['name' => 'IUE', 'initials' => 'IUE', 'number' => '003', 'applied_discount' => 5.00, 'type' => 'G']);
});

test('renders retention list', function () {
    $supplier = Supplier::factory()->create();
    Retention::factory()->create([
        'supplier_id' => $supplier->id,
        'date' => now()->format('Y-m-d'),
        'type' => 'S',
    ]);

    Livewire::test(RetentionComponent::class)
        ->assertOk();
});

test('can create a retention', function () {
    Livewire::test(RetentionComponentForm::class)
        ->set('ci', '3333337')
        ->set('full_name', 'Supplier Test')
        ->set('type', 'S')
        ->set('amount', 1000.00)
        ->set('date', now()->format('Y-m-d'))
        ->set('description', 'Retention test description for validation')
        ->set('summary', 'Summary for retention test here for validation')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('retentions', ['type' => 'S']);
});
