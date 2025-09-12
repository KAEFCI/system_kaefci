<?php

use App\Http\Controllers\MngaccountController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EmployeeController;

// Route untuk halaman login (tampilan form login) - accessible without auth
Route::get('/login', function () {
    return view('login/login');
})->name('login');

// Route untuk mengirim data login (POST request) - accessible without auth
Route::post('/login', [AccountController::class, 'login']);

// Route untuk logout (POST request)
Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
// Route logout per role (tanpa memutus guard lain)
Route::post('/logout/{role}', [AccountController::class, 'logoutRole'])->name('logout.role');

// Redirect root: cek semua guard, pilih pertama yang aktif
Route::get('/', function () {
    $guards = ['superadmin', 'hrd', 'supervisor', 'karyawan'];
    foreach ($guards as $g) {
        if (auth()->guard($g)->check()) {
            $role = auth()->guard($g)->user()->role;
            return match ($role) {
                'superadmin' => redirect()->route('dashboard'),
                'hrd' => redirect()->route('dashboard.hrd'),
                'supervisor' => redirect()->route('dashboard.supervisor'),
                'karyawan' => redirect()->route('dashboard.karyawan'),
                default => redirect()->route('login')
            };
        }
    }
    return redirect()->route('login');
});

// ================= SUPERADMIN (guard: superadmin) =================
Route::middleware(['auth:superadmin', 'touch.online'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/manageaccount', [MngaccountController::class, 'index'])->name('manageaccount');
    Route::get('/analysisdata', [AnalysisController::class, 'index'])->name('analysis');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');
    Route::put('/account/{id}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account/{id}', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/accounts', [AccountController::class, 'index'])->name('account.index');
    Route::get('/sidebar', function () {
        return view('sidebar');
    });
});

// ================= HRD (guard: hrd) =================
Route::middleware(['auth:hrd', 'touch.online'])->group(function () {
    Route::get('/dashboard-hrd', function () {
        // Ambil total dari tabel staff (model Staff) sesuai permintaan
        $totalKaryawan = \App\Models\Staff::where('role', 'karyawan')->count();
        return view('hrd.dashboard_hrd', compact('totalKaryawan'));
    })->name('dashboard.hrd');
    Route::get('/hrd/manage-data', [\App\Http\Controllers\Hrd\StaffController::class, 'index'])->name('managedata.hrd');
    Route::get('/hrd/absensi', function () {
        return view('hrd.absensi');
    })->name('absensi.hrd');
    Route::get('/hrd/penggajian', function () {
        return view('hrd.penggajian');
    })->name('penggajian.hrd');
    // Staff (AJAX API JSON)
    Route::get('/hrd/staff/list', [\App\Http\Controllers\Hrd\StaffController::class, 'list'])->name('staff.list');
    Route::post('/hrd/staff', [\App\Http\Controllers\Hrd\StaffController::class, 'store'])->name('staff.store');
    Route::put('/hrd/staff/{staff}', [\App\Http\Controllers\Hrd\StaffController::class, 'update'])->name('staff.update');
    Route::delete('/hrd/staff/{staff}', [\App\Http\Controllers\Hrd\StaffController::class, 'destroy'])->name('staff.destroy');
    // Teams API
    Route::get('/hrd/teams', [\App\Http\Controllers\Hrd\StaffController::class, 'teamsIndex'])->name('teams.index');
    Route::get('/hrd/staff/unassigned', [\App\Http\Controllers\Hrd\StaffController::class, 'unassigned'])->name('staff.unassigned');
    Route::post('/hrd/teams', [\App\Http\Controllers\Hrd\StaffController::class, 'teamStore'])->name('teams.store');
    Route::put('/hrd/teams/{team}', [\App\Http\Controllers\Hrd\StaffController::class, 'teamUpdate'])->name('teams.update');
    Route::delete('/hrd/teams/{team}', [\App\Http\Controllers\Hrd\StaffController::class, 'teamDestroy'])->name('teams.destroy');
    Route::get('/hrd/hiskaryawan', [\App\Http\Controllers\Hrd\HistoryKaryawanController::class, 'index'])->name('hiskaryawan.hrd');
    // History Karyawan endpoints (CRUD minimal)
    Route::post('/history-karyawan', [\App\Http\Controllers\Hrd\HistoryKaryawanController::class, 'store'])->name('history.karyawan.store');
    Route::put('/history-karyawan/{historyKaryawan}', [\App\Http\Controllers\Hrd\HistoryKaryawanController::class, 'update'])->name('history.karyawan.update');
    Route::delete('/history-karyawan/{id}', [\App\Http\Controllers\Hrd\HistoryKaryawanController::class, 'destroy'])->name('history.karyawan.destroy');
    // Endpoint untuk modal tambah team
    Route::get('/modal/add-team', function () {
        return view('hrd.add-team');
    });
    // Endpoint untuk modal edit/tambah karyawan
    Route::get('/modal/edit', function () {
        return view('hrd.edit');
    });
});

// ================= SUPERVISOR (guard: supervisor) =================
Route::middleware(['auth:supervisor', 'touch.online'])->group(function () {
    Route::get('/dashboard-supervisor', function () {
        return view('supervisor.dashboard_supervisor');
    })->name('dashboard.supervisor');
});

// ================= KARYAWAN (guard: karyawan) =================
Route::middleware(['auth:karyawan', 'touch.online'])->group(function () {
    Route::get('/dashboard-karyawan', function () {
        return view('karyawan.dashboard_karyawan');
    })->name('dashboard.karyawan');
});
