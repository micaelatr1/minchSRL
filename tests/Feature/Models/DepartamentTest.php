<?php

use App\Models\Account;
use App\Models\Departament;
use App\Models\User;

test('belongs to account', function () {
    $account = Account::factory()->create();
    $departament = Departament::factory()->create(['account_id' => $account->id]);

    expect($departament->account->id)->toBe($account->id);
});

test('belongs to user', function () {
    $user = User::factory()->create();
    $departament = Departament::factory()->create(['user_id' => $user->id]);

    expect($departament->user->id)->toBe($user->id);
});

test('fillable', function () {
    $departament = Departament::factory()->create([
        'area' => 'Contabilidad',
        'description' => 'Departamento de contabilidad',
    ]);

    expect($departament->area)->toBe('Contabilidad')
        ->and($departament->description)->toBe('Departamento de contabilidad');
});
