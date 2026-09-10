<?php

use App\Livewire\Accounts\AccountComponent;
use App\Models\Account;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('renders account list', function () {
    $account = Account::factory()->create(['name' => 'BNB']);

    Livewire::test(AccountComponent::class)
        ->assertOk()
        ->assertSee('BNB');
});

test('can create an account', function () {
    Livewire::test(AccountComponent::class)
        ->set('name', 'Test Bank')
        ->set('account_number', '123456789')
        ->set('initials', 'TST')
        ->set('color', 'blue')
        ->set('currency_type', 'BOB')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('accounts', ['name' => 'Test Bank']);
});

test('can edit an account', function () {
    $account = Account::factory()->create(['name' => 'Old Name']);

    Livewire::test(AccountComponent::class)
        ->dispatch('load::account', $account->id)
        ->set('name', 'New Name')
        ->call('update')
        ->assertHasNoErrors();

    expect($account->fresh()->name)->toBe('New Name');
});

test('can delete an account', function () {
    $account = Account::factory()->create();

    Livewire::test(AccountComponent::class)
        ->call('delete', $account->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
});
