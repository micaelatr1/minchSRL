<?php

use App\Livewire\Transactions\TransactionFormComponent;
use App\Models\Account;
use App\Models\Movement;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
    $this->account = Account::factory()->create(['currency_type' => 'BOB']);
});

test('renders transaction form', function () {
    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
    ])->assertOk();
});

test('can store a debit transaction', function () {
    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
    ])
        ->set('ci', '123456')
        ->set('full_name', 'Juan Perez')
        ->set('phone', '77712345')
        ->set('amount', 500.00)
        ->set('type', 'D')
        ->set('date', now()->format('Y-m-d'))
        ->set('description', 'Pago de servicios')
        ->set('payment_type', 'T')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['amount' => 500.00, 'type' => 'D']);
});

test('can store a credit transaction', function () {
    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
    ])
        ->set('ci', '654321')
        ->set('full_name', 'Maria Lopez')
        ->set('amount', 1000.00)
        ->set('type', 'C')
        ->set('date', now()->format('Y-m-d'))
        ->set('description', 'Deposito bancario')
        ->set('payment_type', 'CH')
        ->set('number_check', 'CH-001')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['amount' => 1000.00, 'type' => 'C']);
});

test('validates required fields on store', function () {
    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
    ])
        ->set('date', '')
        ->call('store')
        ->assertHasErrors(['ci', 'full_name', 'amount', 'type', 'date', 'description']);
});

test('can edit and update a transaction', function () {
    $movement = Movement::factory()->create([
        'amount' => 200.00,
        'type' => 'D',
        'date' => now()->format('Y-m-d'),
    ]);
    $movement->transaction()->create(['account_id' => $this->account->id]);

    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
        'id' => $movement->id,
    ])
        ->set('amount', 300.00)
        ->set('type', 'C')
        ->set('description', 'Updated description')
        ->set('ci', '999999')
        ->set('full_name', 'Updated Name')
        ->set('date', now()->format('Y-m-d'))
        ->set('payment_type', 'T')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', ['id' => $movement->id, 'amount' => 300.00, 'type' => 'C']);
});

test('supplier selected listener sets person fields', function () {
    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
    ])
        ->dispatch('supplier-selected', [
            'person_id' => 1,
            'ci' => '1111111',
            'full_name' => 'Supplier Name',
            'phone' => '7777777',
        ])
        ->assertSet('person_id', 1)
        ->assertSet('ci', '1111111')
        ->assertSet('full_name', 'Supplier Name')
        ->assertSet('phone', '7777777');
});

test('clear resets the form', function () {
    Livewire::test(TransactionFormComponent::class, [
        'date_account' => now()->format('Y-m'),
        'account_id' => $this->account->id,
    ])
        ->set('ci', '123')
        ->set('full_name', 'Test')
        ->set('amount', 100)
        ->call('clear')
        ->assertSet('ci', '')
        ->assertSet('full_name', '')
        ->assertSet('amount', null);
});
