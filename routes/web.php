<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\MyServiceRequestController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::resource('guests', GuestController::class);
    Route::resource('guests', GuestController::class);
    Route::resource('service-requests', ServiceRequestController::class);

     Route::resource('my-services', MyServiceRequestController::class);
});

require __DIR__.'/auth.php';