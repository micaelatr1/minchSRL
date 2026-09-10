<?php

use App\Livewire\Inventory\ProductComponent;
use App\Livewire\Inventory\ProductFormComponent;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::all());
    $this->actingAs($user);
});

test('product list page can be rendered', function () {
    $this->get(route('inventory.products'))
        ->assertStatus(200);
});

test('product form page can be rendered', function () {
    $this->get(route('inventory.products.form'))
        ->assertStatus(200);
});

test('product list shows products', function () {
    Product::factory()->create(['name' => 'Cemento Test']);

    Livewire::test(ProductComponent::class)
        ->assertSee('Cemento Test');
});

test('product list shows empty state', function () {
    Livewire::test(ProductComponent::class)
        ->assertSee('Sin productos');
});

test('can search products', function () {
    Product::factory()->create(['name' => 'Cemento Portland']);
    Product::factory()->create(['name' => 'Arena Sílice']);

    Livewire::test(ProductComponent::class)
        ->set('search', 'Cemento')
        ->assertSee('Cemento Portland')
        ->assertDontSee('Arena Sílice');
});

test('can filter by category', function () {
    Product::factory()->create(['name' => 'Dinamita', 'category' => 'insumo']);
    Product::factory()->create(['name' => 'Cemento', 'category' => 'materia_prima']);

    Livewire::test(ProductComponent::class)
        ->set('categoryFilter', 'insumo')
        ->assertSee('Dinamita')
        ->assertDontSee('Cemento');
});

test('can delete a product', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductComponent::class)
        ->call('delete', $product->id);

    expect(Product::find($product->id))->toBeNull();
});

test('form validates required fields', function () {
    Livewire::test(ProductFormComponent::class)
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('form validates category', function () {
    Livewire::test(ProductFormComponent::class)
        ->set('category', 'invalid')
        ->call('save')
        ->assertHasErrors(['category' => 'in']);
});

test('form validates unit_of_measure', function () {
    Livewire::test(ProductFormComponent::class)
        ->set('unit_of_measure', 'invalid')
        ->call('save')
        ->assertHasErrors(['unit_of_measure' => 'in']);
});

test('can create a product', function () {
    Livewire::test(ProductFormComponent::class)
        ->set('name', 'Nuevo Producto Test')
        ->set('category', 'insumo')
        ->set('unit_of_measure', 'kg')
        ->call('save')
        ->assertRedirect(route('inventory.products'));

    expect(Product::where('name', 'Nuevo Producto Test')->exists())->toBeTrue();
});

test('can edit a product', function () {
    $product = Product::factory()->create(['name' => 'Original']);

    Livewire::test(ProductFormComponent::class, ['id' => $product->id])
        ->set('name', 'Actualizado')
        ->call('save')
        ->assertRedirect(route('inventory.products'));

    expect($product->fresh()->name)->toBe('Actualizado');
});

test('edit form loads existing data', function () {
    $product = Product::factory()->create([
        'name' => 'Producto Cargado',
        'category' => 'repuesto',
        'unit_of_measure' => 'u',
    ]);

    Livewire::test(ProductFormComponent::class, ['id' => $product->id])
        ->assertSet('name', 'Producto Cargado')
        ->assertSet('category', 'repuesto')
        ->assertSet('unit_of_measure', 'u');
});
