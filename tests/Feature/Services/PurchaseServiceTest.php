<?php

use App\Models\Account;
use App\Models\KardexMovement;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(PurchaseService::class);
});

test('create a purchase with items updates stock and avg cost', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create([
        'stock' => 100,
        'average_cost' => 10,
    ]);

    $date = Carbon::today();
    $items = [
        ['product_id' => $product->id, 'quantity' => 50, 'unit_cost' => 12],
    ];

    $purchase = $this->service->createPurchase($date, 'FACT-001', $supplier, $user, $items);

    expect($purchase->total)->toEqual(600.0)
        ->and($purchase->invoice_number)->toBe('FACT-001')
        ->and($purchase->supplier_id)->toBe($supplier->id);

    $product->refresh();
    expect((float) $product->stock)->toEqual(150.0)
        ->and((float) $product->average_cost)->toEqual(10.6667); // (100*10 + 50*12) / 150

    expect($purchase->items)->toHaveCount(1);
    expect($purchase->items[0]->product_id)->toBe($product->id);

    $kardex = KardexMovement::where('product_id', $product->id)->first();
    expect($kardex)->not->toBeNull()
        ->and((float) $kardex->quantity_in)->toEqual(50.0)
        ->and((float) $kardex->balance_quantity)->toEqual(150.0)
        ->and((float) $kardex->unit_cost)->toEqual(12.0);
});

test('purchase with bank movement creates credit transaction', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create(['stock' => 0, 'average_cost' => 0]);
    $account = Account::factory()->create();

    $movement = Movement::create([
        'date' => Carbon::today()->subDay(),
        'description' => 'Saldo inicial',
        'type' => 'D',
        'amount' => 5000,
        'user_id' => $user->id,
    ]);
    \App\Models\Transaction::create([
        'account_id' => $account->id,
        'movement_id' => $movement->id,
        'payment_type' => 'T',
    ]);

    $date = Carbon::today();
    $items = [
        ['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 100],
    ];

    $purchase = $this->service->createPurchase($date, 'FACT-002', $supplier, $user, $items, $account->id);

    expect($purchase->movement_id)->not->toBeNull();

    $movement = Movement::find($purchase->movement_id);
    expect($movement)->not->toBeNull()
        ->and($movement->type)->toBe('C')
        ->and((float) $movement->amount)->toEqual(1000.0);

    expect($movement->transaction)->not->toBeNull()
        ->and($movement->transaction->account_id)->toBe($account->id);
});

test('create purchase with multiple items', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();
    $product1 = Product::factory()->create(['stock' => 0, 'average_cost' => 0]);
    $product2 = Product::factory()->create(['stock' => 0, 'average_cost' => 0]);

    $date = Carbon::today();
    $items = [
        ['product_id' => $product1->id, 'quantity' => 20, 'unit_cost' => 50],
        ['product_id' => $product2->id, 'quantity' => 30, 'unit_cost' => 40],
    ];

    $purchase = $this->service->createPurchase($date, null, $supplier, $user, $items);

    expect((float) $purchase->total)->toEqual(2200.0); // 20*50 + 30*40
    expect($purchase->items)->toHaveCount(2);
    expect(KardexMovement::count())->toEqual(2);
});

test('process single entry creates lightweight purchase', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create(['stock' => 0, 'average_cost' => 0]);

    $date = Carbon::today();
    $kardex = $this->service->processSingleEntry(
        $product, 25, 80, 'FACT-003', $supplier, 'Nota opcional', $date, $user
    );

    expect($kardex)->toBeInstanceOf(KardexMovement::class)
        ->and((float) $kardex->quantity_in)->toEqual(25.0);

    $purchase = $kardex->reference->purchase;
    expect($purchase)->not->toBeNull()
        ->and((float) $purchase->total)->toEqual(2000.0);

    $product->refresh();
    expect((float) $product->stock)->toEqual(25.0);
});
