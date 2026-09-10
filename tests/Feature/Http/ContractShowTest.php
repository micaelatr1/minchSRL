<?php

use App\Livewire\Contracts\ContractShowComponent;
use App\Models\Account;
use App\Models\Contract;
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

    $this->contract = Contract::factory()->create(['total_amount' => 10000]);
});

test('renders contract show page', function () {
    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])->assertOk();
});

test('can create a direct payment', function () {
    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('payment_type', 'direct')
        ->set('payment_amount', 3000)
        ->set('payment_date', now()->format('Y-m-d'))
        ->set('payment_description', 'Pago directo test')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', [
        'contract_id' => $this->contract->id,
        'amount' => 3000,
        'type' => 'D',
    ]);
    expect($this->contract->fresh()->status)->toBe('in_progress');
});

test('can create a cash box payment', function () {
    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('payment_type', 'cash_box')
        ->set('payment_amount', 2500)
        ->set('payment_date', now()->format('Y-m-d'))
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', [
        'contract_id' => $this->contract->id,
        'amount' => 2500,
        'type' => 'D',
    ]);
    $movement = Movement::where('contract_id', $this->contract->id)->first();
    expect($movement->box)->not->toBeNull();
});

test('can create a bank payment', function () {
    $account = Account::factory()->create(['currency_type' => 'BOB']);

    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('payment_type', 'bank')
        ->set('payment_amount', 4000)
        ->set('payment_date', now()->format('Y-m-d'))
        ->set('account_id', $account->id)
        ->set('payment_method', 'T')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('movements', [
        'contract_id' => $this->contract->id,
        'amount' => 4000,
        'type' => 'D',
    ]);
    $movement = Movement::where('contract_id', $this->contract->id)->first();
    expect($movement->transaction)->not->toBeNull();
    expect((int) $movement->transaction->account_id)->toBe((int) $account->id);
});

test('can edit a direct payment', function () {
    $movement = Movement::factory()->create([
        'contract_id' => $this->contract->id,
        'person_id' => $this->contract->person_id,
        'type' => 'D',
        'amount' => 2000,
        'date' => now(),
    ]);

    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('editingMovementId', $movement->id)
        ->set('payment_amount', 3500)
        ->set('payment_date', now()->format('Y-m-d'))
        ->set('payment_description', 'Editado')
        ->set('payment_type', 'direct')
        ->call('store')
        ->assertHasNoErrors();

    expect((float) $movement->fresh()->amount)->toBe(3500.0);
    expect($movement->fresh()->description)->toBe('Editado');
});

test('can edit a bank payment', function () {
    $account1 = Account::factory()->create(['currency_type' => 'BOB']);
    $account2 = Account::factory()->create(['currency_type' => 'BOB']);

    $movement = Movement::factory()->create([
        'contract_id' => $this->contract->id,
        'person_id' => $this->contract->person_id,
        'type' => 'D',
        'amount' => 2000,
        'date' => now(),
    ]);
    $movement->transaction()->create([
        'account_id' => $account1->id,
        'payment_type' => 'CH',
        'number_check' => 'CH-001',
    ]);

    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('editingMovementId', $movement->id)
        ->set('payment_type', 'bank')
        ->set('account_id', $account2->id)
        ->set('payment_method', 'T')
        ->set('number_check', 'TRF-001')
        ->set('payment_amount', 5000)
        ->call('store')
        ->assertHasNoErrors();

    $movement->refresh();
    expect((float) $movement->amount)->toBe(5000.0);
    expect((int) $movement->transaction->account_id)->toBe((int) $account2->id);
    expect($movement->transaction->payment_type)->toBe('T');
});

test('can change payment type from direct to bank', function () {
    $account = Account::factory()->create(['currency_type' => 'BOB']);

    $movement = Movement::factory()->create([
        'contract_id' => $this->contract->id,
        'person_id' => $this->contract->person_id,
        'type' => 'D',
        'amount' => 1000,
        'date' => now(),
    ]);

    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('editingMovementId', $movement->id)
        ->set('payment_type', 'bank')
        ->set('account_id', $account->id)
        ->set('payment_method', 'CH')
        ->set('payment_amount', 1000)
        ->call('store')
        ->assertHasNoErrors();

    $movement->refresh();
    expect($movement->transaction)->not->toBeNull();
    expect((int) $movement->transaction->account_id)->toBe((int) $account->id);
});

test('can change payment type from bank to cash box', function () {
    $account = Account::factory()->create(['currency_type' => 'BOB']);

    $movement = Movement::factory()->create([
        'contract_id' => $this->contract->id,
        'person_id' => $this->contract->person_id,
        'type' => 'D',
        'amount' => 1000,
        'date' => now(),
    ]);
    $movement->transaction()->create([
        'account_id' => $account->id,
        'payment_type' => 'CH',
    ]);

    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('editingMovementId', $movement->id)
        ->set('payment_type', 'cash_box')
        ->set('payment_amount', 1000)
        ->call('store')
        ->assertHasNoErrors();

    $movement->refresh();
    expect($movement->transaction)->toBeNull();
    expect($movement->box)->not->toBeNull();
});

test('can delete a payment', function () {
    $movement = Movement::factory()->create([
        'contract_id' => $this->contract->id,
        'person_id' => $this->contract->person_id,
        'type' => 'D',
        'amount' => 3000,
        'date' => now(),
    ]);

    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->call('delete', $movement->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('movements', ['id' => $movement->id]);
});

test('excess payment amount is rejected', function () {
    Livewire::test(ContractShowComponent::class, [
        'contract' => $this->contract,
    ])
        ->set('payment_type', 'direct')
        ->set('payment_amount', 999999)
        ->set('payment_date', now()->format('Y-m-d'))
        ->call('store')
        ->assertHasErrors('payment_amount');
});
