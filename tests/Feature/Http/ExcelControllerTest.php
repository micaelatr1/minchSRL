<?php

use App\Models\Retention;
use App\Models\Supplier;
use App\Models\Taxe;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);

    Taxe::factory()->create(['name' => 'RC-IVA', 'initials' => 'RC-IVA', 'number' => '001', 'applied_discount' => 13.00, 'type' => 'S']);
    Taxe::factory()->create(['name' => 'IT', 'initials' => 'IT', 'number' => '002', 'applied_discount' => 3.00, 'type' => 'A']);
});

test('retention month excel export returns success', function () {
    $supplier = Supplier::factory()->create();
    Retention::factory()->create([
        'supplier_id' => $supplier->id,
        'type' => 'S',
        'date' => now()->format('Y-m-d'),
    ]);

    $date = now()->format('Y-m');
    $response = $this->get(route('retention.month.excel', ['date' => $date, 'type' => 'S']));
    $response->assertSuccessful();
});

test('retention month excel returns file with correct headers', function () {
    $date = now()->format('Y-m');
    $response = $this->get(route('retention.month.excel', ['date' => $date, 'type' => 'S']));
    $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
