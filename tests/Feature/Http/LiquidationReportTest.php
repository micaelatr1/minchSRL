<?php

use App\Livewire\Reports\LiquidationReportsComponent;
use App\Models\Liquidation;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

test('renders the production chart', function () {
    Livewire::test(LiquidationReportsComponent::class)->assertOk();
});

test('lists only the liquidations of the selected month', function () {
    $dentro = Liquidation::factory()->pb()->create(['date' => now()->startOfMonth()->addDays(2)]);
    $fuera = Liquidation::factory()->zn()->create(['date' => now()->subMonth()->startOfMonth()->addDays(2)]);

    Livewire::test(LiquidationReportsComponent::class)
        ->assertSee($dentro->lote)
        ->assertDontSee($fuera->lote);
});

test('filters by metal', function () {
    $pb = Liquidation::factory()->pb()->create(['date' => now()->startOfMonth()->addDays(2)]);
    $zn = Liquidation::factory()->zn()->create(['date' => now()->startOfMonth()->addDays(5)]);

    Livewire::test(LiquidationReportsComponent::class)
        ->set('metal', 'pb')
        ->assertSee($pb->lote)
        ->assertDontSee($zn->lote);
});

test('switches the selected month', function () {
    $anterior = Liquidation::factory()->pb()->create(['date' => now()->subMonth()->startOfMonth()->addDays(2)]);
    $actual = Liquidation::factory()->zn()->create(['date' => now()->startOfMonth()->addDays(2)]);

    Livewire::test(LiquidationReportsComponent::class)
        ->set('mes', now()->subMonth()->format('Y-m'))
        ->assertSee($anterior->lote)
        ->assertDontSee($actual->lote);
});

test('shows the totals footer for the period', function () {
    Liquidation::factory()->pb()->create(['date' => now()->startOfMonth()->addDays(2)]);

    Livewire::test(LiquidationReportsComponent::class)
        ->assertSee('Total Deducc')
        ->assertSee('Liquido Pagable');
});

test('exports the production chart to excel', function () {
    Carbon::setTestNow('2026-10-06 10:20:30');

    Liquidation::factory()->pb()->create(['date' => '2026-10-05']);

    Excel::fake();

    Livewire::test(LiquidationReportsComponent::class)->call('exportarExcel');

    Excel::assertDownloaded('cuadro_produccion_202610_102030.xlsx');
});
