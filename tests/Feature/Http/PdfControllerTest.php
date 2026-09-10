<?php

use App\Models\Account;
use App\Models\Movement;
use App\Models\Retention;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

test('retention form PDF returns success', function () {
    $retention = Retention::factory()->create();

    $response = $this->get(route('retention.pdf.form', $retention->id));
    $response->assertSuccessful();
});

test('account statement PDF returns success', function () {
    $supplier = Supplier::factory()->create();

    $response = $this->get(route('account.statement.pdf', ['type' => 'supplier', 'id' => $supplier->id]));
    $response->assertSuccessful();
});

test('account statement PDF returns 404 for invalid type', function () {
    $response = $this->get(route('account.statement.pdf', ['type' => 'invalid', 'id' => 1]));
    $response->assertNotFound();
});

test('transaction account PDF returns success', function () {
    $account = Account::factory()->create();
    $movement = Movement::factory()->create([
        'date' => now()->format('Y-m-d'),
    ]);
    $movement->transaction()->create(['account_id' => $account->id]);

    $start = now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
    $end = now()->endOfMonth()->format('Y-m-d H:i:s');

    $response = $this->get(route('transaction.account.pdf', ['start' => $start, 'end' => $end, 'id' => $account->id]));
    $response->assertSuccessful();
});

test('receipt transaction PDF returns success', function () {
    $account = Account::factory()->create(['currency_type' => 'BOB']);
    $movement = Movement::factory()->create();
    $movement->transaction()->create(['account_id' => $account->id]);

    $response = $this->get(route('receipt.transaction.pdf', $movement->id));
    $response->assertSuccessful();
});

test('account box PDF returns success', function () {
    $response = $this->get(route('account.box.pdf'));
    $response->assertSuccessful();
});

test('receipt box PDF returns success', function () {
    $movement = Movement::factory()->create();
    $movement->box()->create([]);

    $response = $this->get(route('receipt.box.pdf', $movement->id));
    $response->assertSuccessful();
});
