<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Incidents - CRUD operations
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::get('/create', [IncidentController::class, 'create'])->name('create');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::get('/export/csv', [IncidentController::class, 'export'])->name('export');
        Route::get('/{incident}', [IncidentController::class, 'show'])->name('show');
        Route::get('/{incident}/edit', [IncidentController::class, 'edit'])->name('edit');
        Route::put('/{incident}', [IncidentController::class, 'update'])->name('update');
        Route::delete('/{incident}', [IncidentController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [IncidentController::class, 'restore'])->name('restore');
    });

    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Users management (admin only)
    Route::middleware('can:manage-users')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
