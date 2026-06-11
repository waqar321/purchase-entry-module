<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\PurchaseForm;
use App\Livewire\PurchaseIndex;
use App\Livewire\PurchaseShow;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('purchases.index');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware(['auth', 'auth.gates'])->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');

    Route::middleware('can:purchase_view')->group(function () {
        Route::get('purchases', PurchaseIndex::class)->name('purchases.index');
        Route::get('purchases/{purchase}', PurchaseShow::class)->name('purchases.show');
    });

    Route::middleware('can:purchase_create')->group(function () {
        Route::get('purchases/create/new', PurchaseForm::class)->name('purchases.create');
    });

    Route::middleware('can:purchase_edit')->group(function () {
        Route::get('purchases/{purchase}/edit', PurchaseForm::class)->name('purchases.edit');
    });
});
