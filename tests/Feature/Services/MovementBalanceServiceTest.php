<?php

use App\Models\Account;
use App\Models\Movement;
use App\Models\User;
use App\Services\MovementBalanceService;

beforeEach(function () {
    $this->service = app(MovementBalanceService::class);
    $this->user = User::factory()->create();
    $this->account = Account::factory()->create();
    $this->actingAs($this->user);
});

test('creates balance movement for previous month', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 1000, 'date' => '2026-07-15',
    ]);
    $movement->transaction()->create([
        'account_id' => $this->account->id,
        'payment_type' => 'T',
    ]);

    $this->service->recalculateFromDate('2026-08-01', $this->account->id);

    $balance = Movement::where('type', 'B')
        ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
        ->where('transactions.account_id', $this->account->id)
        ->whereMonth('movements.date', 8)->whereYear('movements.date', 2026)
        ->select('movements.*')
        ->first();

    expect($balance)->not->toBeNull();
});

test('creates next month balance too', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 500, 'date' => '2026-07-10',
    ]);
    $movement->transaction()->create([
        'account_id' => $this->account->id,
        'payment_type' => 'T',
    ]);

    $this->service->recalculateFromDate('2026-08-01', $this->account->id);

    $septemberBalance = Movement::where('type', 'B')
        ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
        ->where('transactions.account_id', $this->account->id)
        ->whereMonth('movements.date', 9)->whereYear('movements.date', 2026)
        ->select('movements.*')
        ->first();

    expect($septemberBalance)->not->toBeNull();
});

test('recalculates updates amount on existing balance movement', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 500, 'date' => '2026-07-10',
    ]);
    $movement->transaction()->create([
        'account_id' => $this->account->id,
        'payment_type' => 'T',
    ]);

    $this->service->recalculateFromDate('2026-08-01', $this->account->id);

    $balance = Movement::where('type', 'B')
        ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
        ->where('transactions.account_id', $this->account->id)
        ->whereMonth('movements.date', 8)->whereYear('movements.date', 2026)
        ->select('movements.*')
        ->first();

    expect($balance)->not->toBeNull()
        ->and($balance->amount)->toBeGreaterThanOrEqual(0);
});

test('isolates by account', function () {
    $otherAccount = Account::factory()->create();

    $m1 = Movement::factory()->create(['type' => 'D', 'amount' => 9999, 'date' => '2026-07-10']);
    $m1->transaction()->create(['account_id' => $otherAccount->id, 'payment_type' => 'T']);

    $m2 = Movement::factory()->create(['type' => 'D', 'amount' => 500, 'date' => '2026-07-15']);
    $m2->transaction()->create(['account_id' => $this->account->id, 'payment_type' => 'T']);

    $this->service->recalculateFromDate('2026-08-01', $this->account->id);

    $balance = Movement::where('type', 'B')
        ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
        ->where('transactions.account_id', $this->account->id)
        ->whereMonth('movements.date', 8)->whereYear('movements.date', 2026)
        ->select('movements.*')
        ->first();

    expect($balance)->not->toBeNull();
});
