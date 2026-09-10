<?php

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('taxes'))->assertRedirect(route('login'));
    $this->get(route('accounts'))->assertRedirect(route('login'));
    $this->get(route('retentions'))->assertRedirect(route('login'));
});

test('authenticated user can visit dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertOk();
});

test('dashboard renders livewire component', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertOk();
});
