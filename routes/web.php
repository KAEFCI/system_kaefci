<?php

use App\Http\Controllers\MngaccountController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\SettingsController;

// Route untuk halaman login (tampilan form login) - accessible without auth
Route::get('/login', function () {
    return view('login/login');
})->name('login');

// Route untuk mengirim data login (POST request) - accessible without auth
Route::post('/login', [AccountController::class, 'login']);

// Route untuk logout (POST request)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root ke login jika belum login, ke dashboard jika sudah login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Protected routes - harus login terlebih dahulu
Route::middleware(['auth','touch.online'])->group(function () {
    // Route untuk dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk manage account
    Route::get('/manageaccount', [MngaccountController::class, 'index'])->name('manageaccount');
    
    // Route untuk analysis data
    Route::get('/analysisdata', [AnalysisController::class, 'index'])->name('analysis');
    
    // Route untuk settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    
    // Route untuk account management
    Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');
    Route::put('/account/{id}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account/{id}', [AccountController::class, 'destroy'])->name('account.destroy');
    
    // Route untuk sidebar
    Route::get('/sidebar', function () {
        return view('sidebar');
    });
    
    Route::get('/accounts', [AccountController::class, 'index'])->name('account.index');
});
