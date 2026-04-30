<?php

use App\Http\Controllers\BusinessManagementController;
use App\Http\Controllers\StorePanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'backpack.auth'])->prefix('panel')->name('panel.')->group(function () {
    Route::get('/', [StorePanelController::class, 'index'])->name('index');
    Route::get('/caja', [StorePanelController::class, 'pos'])->name('pos');
    Route::get('/api/productos', [StorePanelController::class, 'searchProducts'])->name('api.products');
    Route::post('/api/ventas', [StorePanelController::class, 'storeSale'])->name('api.sales');
});

Route::middleware(['web', 'backpack.auth'])->prefix('gestion')->name('gestion.')->group(function () {
    Route::get('/', [BusinessManagementController::class, 'index'])->name('index');
});
