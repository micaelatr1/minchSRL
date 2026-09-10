<?php

use App\Models\Account;
use App\Models\Movement;
use App\Models\Transaction;
use App\Models\User;

test('has transactions', function () {
    $account = Account::factory()->create();
    Transaction::factory()->create(['account_id' => $account->id]);

    expect($account->transactions)->toHaveCount(1);
});

test('has movements through transactions', function () {
    $account = Account::factory()->create();
    $movement = Movement::factory()->create();
    Transaction::factory()->create([
        'account_id' => $account->id,
        'movement_id' => $movement->id,
    ]);

    expect($account->movements)->toHaveCount(1)
        ->and($account->movements->first()->id)->toBe($movement->id);
});

test('has departments', function () {
    $account = Account::factory()->create();
    $user = User::factory()->create();
    $account->departaments()->create([
        'area' => 'Test',
        'description' => 'Test Dept',
        'user_id' => $user->id,
    ]);

    expect($account->departaments)->toHaveCount(1);
});

test('currency types', function () {
    $bob = Account::factory()->create(['currency_type' => 'BOB']);
    $usd = Account::factory()->create(['currency_type' => 'USD']);
    $eur = Account::factory()->create(['currency_type' => 'EUR']);

    expect($bob->currency_type)->toBe('BOB')
        ->and($usd->currency_type)->toBe('USD')
        ->and($eur->currency_type)->toBe('EUR');
});
