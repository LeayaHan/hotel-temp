<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\MyServiceRequestController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ── GET LOGOUT (avoids 419 CSRF expiry) ──────────────────────────
Route::get('/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout.get');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Guest self-service requests
    Route::get('my-services/history', [MyServiceRequestController::class, 'history'])->name('my-services.history');
    Route::resource('my-services', MyServiceRequestController::class);

    // Staff, Front Desk + Admin: full service requests & guest management
    Route::middleware('staff')->group(function () {
        Route::get('service-requests/history', [ServiceRequestController::class, 'history'])->name('service-requests.history');
        Route::resource('service-requests', ServiceRequestController::class);
    });

    // Only admin, manager, and front_desk can manage guests — not housekeeping staff
    Route::middleware('admin_frontdesk')->group(function () {
        Route::resource('guests', GuestController::class);
    });

  Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

});

require __DIR__.'/auth.php';