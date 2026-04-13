<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\MyServiceRequestController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Guest self-service requests
    Route::resource('my-services', MyServiceRequestController::class);

    // Staff + Admin: full service requests & guests
    Route::resource('service-requests', ServiceRequestController::class);
    Route::resource('guests', GuestController::class);

    // Admin only: user management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });

});

require __DIR__.'/auth.php';