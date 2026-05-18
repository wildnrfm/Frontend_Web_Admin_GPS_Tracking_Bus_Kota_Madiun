<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HalteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalitikController;
use App\Http\Controllers\TrackingController;

// ========== Public Routes (No Authentication Required) ==========
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// ========== Admin Routes (Authentication Required) ==========
Route::middleware(['web', 'admin.authenticated'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Live Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('admin.tracking');

    // Analitik
    Route::get('/analitik', [AnalitikController::class, 'index'])->name('admin.analitik');
    Route::get('/analitik/export', [AnalitikController::class, 'export'])->name('admin.analitik.export');

    // Pending (redirect to siswa)
    Route::get('/pending', function() {
        return redirect()->route('admin.siswa');
    })->name('admin.pending');

    // Siswa Management
    Route::prefix('siswa')->name('admin.siswa')->group(function () {
        Route::get('', [SiswaController::class, 'index'])->name('');
        Route::get('/create', [SiswaController::class, 'create'])->name('.create');
        Route::post('', [SiswaController::class, 'store'])->name('.store');
        Route::get('/{id}', [SiswaController::class, 'show'])->name('.show');
        Route::get('/{id}/edit', [SiswaController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [SiswaController::class, 'update'])->name('.update');
        Route::delete('/{id}', [SiswaController::class, 'destroy'])->name('.destroy');
        Route::post('/{id}/approve', [SiswaController::class, 'approve'])->name('.approve');
        Route::post('/{id}/reject', [SiswaController::class, 'reject'])->name('.reject');
    });

    // Bus Management
    Route::prefix('bus')->name('admin.bus')->group(function () {
        Route::get('', [BusController::class, 'index'])->name('');
        Route::get('/create', [BusController::class, 'create'])->name('.create');
        Route::post('', [BusController::class, 'store'])->name('.store');
        Route::get('/{id}', [BusController::class, 'show'])->name('.show');
        Route::get('/{id}/edit', [BusController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [BusController::class, 'update'])->name('.update');
        Route::delete('/{id}', [BusController::class, 'destroy'])->name('.destroy');
    });

    // Driver Management
    Route::prefix('driver')->name('admin.driver')->group(function () {
        Route::get('', [DriverController::class, 'index'])->name('');
        Route::get('/create', [DriverController::class, 'create'])->name('.create');
        Route::post('', [DriverController::class, 'store'])->name('.store');
        Route::get('/{id}', [DriverController::class, 'show'])->name('.show');
        Route::get('/{id}/edit', [DriverController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [DriverController::class, 'update'])->name('.update');
        Route::delete('/{id}', [DriverController::class, 'destroy'])->name('.destroy');
    });

    // Halte Management
    Route::prefix('halte')->name('admin.halte')->group(function () {
        Route::get('', [HalteController::class, 'index'])->name('');
        Route::get('/create', [HalteController::class, 'create'])->name('.create');
        Route::post('', [HalteController::class, 'store'])->name('.store');
        Route::get('/{id}', [HalteController::class, 'show'])->name('.show');
        Route::get('/{id}/edit', [HalteController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [HalteController::class, 'update'])->name('.update');
        Route::delete('/{id}', [HalteController::class, 'destroy'])->name('.destroy');
    });

    // Profile & Settings
    Route::get('/profil', [ProfileController::class, 'index'])->name('admin.profil');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('admin.profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('admin.profil.update');
});

// Catch-all: redirect to dashboard if logged in, login if not
Route::get('/{any}', function () {
    $authService = app(\App\Services\AuthService::class);
    if ($authService->isAuthenticated()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->where('any', '.*')->name('fallback');
