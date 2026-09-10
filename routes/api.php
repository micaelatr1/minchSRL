<?php

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/suppliers/search', function (Request $request) {
    $search = $request->get('search', '');
    $suppliers = Supplier::where('ci', 'LIKE', "%{$search}%")
        ->orWhere('full_name', 'LIKE', "%{$search}%")
        ->limit(10)
        ->get(['ci', 'full_name']); // Traemos CI y nombre

    return $suppliers->map(fn ($s) => [
        'label' => "{$s->ci} - {$s->full_name}",
        'value' => $s->ci, // Importante: el value es el CI
    ]);
})->name('api.suppliers.search');
