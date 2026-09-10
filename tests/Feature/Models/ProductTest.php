<?php

use App\Models\KardexMovement;
use App\Models\Product;
use App\Models\User;

test('has fillable fields', function () {
    $product = Product::factory()->create();

    expect($product->name)->not->toBeEmpty()
        ->and($product->code)->toMatch('/^PRO-\d{4}$/');
});

test('generates code on creating', function () {
    $first = Product::factory()->create();
    $second = Product::factory()->create();

    expect($first->code)->toBe('PRO-0001')
        ->and($second->code)->toBe('PRO-0002');
});

test('belongs to user', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);

    expect($product->user)->toBeInstanceOf(User::class)
        ->and($product->user->id)->toBe($user->id);
});

test('has kardex movements', function () {
    $product = Product::factory()->create();
    KardexMovement::factory()->create([
        'product_id' => $product->id,
        'type' => 'purchase',
        'quantity_in' => 100,
        'quantity_out' => 0,
        'unit_cost' => 10,
        'balance_quantity' => 100,
        'balance_avg_cost' => 10,
        'balance_total_value' => 1000,
        'date' => now(),
    ]);

    expect($product->kardexMovements)->toHaveCount(1);
});

test('casts', function () {
    $product = Product::factory()->create([
        'stock' => 100.50,
        'average_cost' => 25.1234,
        'is_active' => true,
    ]);

    expect((float) $product->stock)->toEqual(100.50)
        ->and((float) $product->average_cost)->toEqual(25.1234)
        ->and($product->is_active)->toBeTrue();
});

test('category label', function () {
    $map = [
        'materia_prima' => 'Materia Prima',
        'insumo' => 'Insumo',
        'repuesto' => 'Repuesto',
        'combustible' => 'Combustible',
        'otro' => 'Otro',
    ];

    foreach ($map as $value => $label) {
        $product = Product::factory()->create(['category' => $value]);
        expect($product->category_label)->toBe($label);
    }
});

test('unit label', function () {
    $map = [
        'kg' => 'Kg',
        'ton' => 'Ton',
        'l' => 'L',
        'u' => 'Unid',
        'm' => 'M',
    ];

    foreach ($map as $value => $label) {
        $product = Product::factory()->create(['unit_of_measure' => $value]);
        expect($product->unit_label)->toBe($label);
    }
});
