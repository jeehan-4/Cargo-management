<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية أصبحت تعرض ملف الـ welcome الترحيبي مباشرة
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

// ───────────────────────────────────────────────
// Smart Dashboard Route (auth)
// ───────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'driver') {
            return redirect()->route('driver.dashboard');
        }
        return app(DashboardController::class)->index();
    })->name('dashboard');
});

// ───────────────────────────────────────────────
// Admin Routes (role:admin)
// ───────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Shipments
    Route::get('shipments/board', [ShipmentController::class, 'board'])->name('shipments.board');
    Route::get('shipments/export/excel', [ShipmentController::class, 'exportExcel'])->name('shipments.export');

    Route::resource('shipments', ShipmentController::class);
    Route::post('shipments/{shipment}/assign', [ShipmentController::class, 'assign'])->name('shipments.assign');
    Route::post('shipments/{shipment}/unassign', [ShipmentController::class, 'unassign'])->name('shipments.unassign');
    Route::post('shipments/{shipment}/cancel', [ShipmentController::class, 'cancel'])->name('shipments.cancel');
    Route::post('shipments/{shipment}/archive', [ShipmentController::class, 'archive'])->name('shipments.archive');
    Route::post('shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');

    // Drivers
    Route::resource('drivers', DriverController::class);

    // Trucks
    Route::resource('trucks', TruckController::class);

    // Destinations
    Route::resource('destinations', DestinationController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// ───────────────────────────────────────────────
// Driver Routes (role:driver)
// ───────────────────────────────────────────────
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {

    Route::get('/', [DriverDashboardController::class, 'index'])->name('dashboard');
    Route::get('/shipments', [DriverDashboardController::class, 'shipments'])->name('shipments');
    Route::get('/shipments/{shipment}', [DriverDashboardController::class, 'showShipment'])->name('shipments.show');
    Route::post('/shipments/{shipment}/status', [DriverDashboardController::class, 'updateStatus'])->name('shipments.status');
    Route::get('/profile', [DriverDashboardController::class, 'profile'])->name('profile');
});

Route::get('/home', function () {
    if (auth()->check() && auth()->user()->role === 'driver') {
        return redirect()->route('driver.dashboard');
    }
    return redirect()->route('dashboard');
})->name('home');