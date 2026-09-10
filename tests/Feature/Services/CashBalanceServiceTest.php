<?php

use App\Models\Movement;
use App\Models\User;
use App\Services\CashBalanceService;

beforeEach(function () {
    $this->service = app(CashBalanceService::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('creates balance movements after recalculate', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 500, 'date' => '2026-07-15',
    ]);
    $movement->box()->create([]);

    $this->service->recalculateFromDate('2026-08-01');

    $balances = Movement::where('type', 'B')
        ->whereDoesntHave('transaction')
        ->get();

    expect($balances)->not->toBeEmpty();
});

test('recalculate creates previous month and current month balances', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 1000, 'date' => '2026-07-15',
    ]);
    $movement->box()->create([]);

    $this->service->recalculateFromDate('2026-08-01');

    $augustBalance = Movement::where('type', 'B')
        ->whereDoesntHave('transaction')
        ->whereMonth('date', 8)->whereYear('date', 2026)
        ->first();

    $septemberBalance = Movement::where('type', 'B')
        ->whereDoesntHave('transaction')
        ->whereMonth('date', 9)->whereYear('date', 2026)
        ->first();

    expect($augustBalance)->not->toBeNull()
        ->and($septemberBalance)->not->toBeNull();
});

test('updates existing balance movement', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 1000, 'date' => '2026-07-15',
    ]);
    $movement->box()->create([]);

    $this->service->recalculateFromDate('2026-08-01');

    $balance = Movement::where('type', 'B')
        ->whereDoesntHave('transaction')
        ->whereMonth('date', 8)->whereYear('date', 2026)
        ->first();

    expect($balance)->not->toBeNull()
        ->and($balance->amount)->toBeGreaterThan(0);
});

test('creates balance movements for modified and next month', function () {
    $movement = Movement::factory()->create([
        'type' => 'D', 'amount' => 200, 'date' => '2026-06-15',
    ]);
    $movement->box()->create([]);

    $this->service->recalculateFromDate('2026-08-01');

    $augustBalance = Movement::where('type', 'B')
        ->whereDoesntHave('transaction')
        ->whereMonth('date', 8)->whereYear('date', 2026)
        ->first();

    $septemberBalance = Movement::where('type', 'B')
        ->whereDoesntHave('transaction')
        ->whereMonth('date', 9)->whereYear('date', 2026)
        ->first();

    expect($augustBalance)->not->toBeNull()
        ->and($septemberBalance)->not->toBeNull();
});
