<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuruPiketController;
use App\Http\Controllers\WalikelasController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Siswa\IzinController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LaporanAbsensiController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\JadwalPelajaranController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// =====================================================================
// RUTE PENGALIHAN SETELAH LOGIN
// =====================================================================
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    switch ($role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'walikelas':
            return redirect()->route('walikelas.dashboard');
        case 'guru':
        case 'guru_piket':
            return redirect()->route('guru.dashboard');
        case 'siswa':
            return redirect()->route('siswa.dashboard');
        default:
            Auth::logout();
            return redirect()->route('login')->with('error', 'Peran tidak dikenali.');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// Rute Profil Bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =====================================================================
// GRUP ROUTE KHUSUS ADMIN
// =====================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('kelas', KelasController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('jadwal', JadwalPelajaranController::class);
    Route::resource('mapel', MataPelajaranController::class);

    // Rute untuk menangani permintaan pencarian AJAX
    Route::get('/siswa/search', [SiswaController::class, 'search'])->name('siswa.search');

    Route::get('/laporan/absensi', [LaporanAbsensiController::class, 'index'])->name('laporan.absensi.index');
    Route::get('/laporan/absensi/export-excel', [LaporanAbsensiController::class, 'exportExcel'])->name('laporan.absensi.export.excel');
    Route::get('/laporan/absensi/export-pdf', [LaporanAbsensiController::class, 'exportPdf'])->name('laporan.absensi.export.pdf');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    Route::patch('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
});


// =====================================================================
// GRUP ROUTE KHUSUS GURU, GURU PIKET & WALIKELAS
// =====================================================================
Route::middleware(['auth', 'role:admin,guru,guru_piket,walikelas'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    Route::get('/riwayat-absensi', [GuruDashboardController::class, 'history'])->name('absensi.history');
    Route::get('/riwayat-absensi/{jadwal}/{tanggal}', [GuruDashboardController::class, 'historyShow'])->name('absensi.history.show');

    Route::get('/absensi/create/{jadwal}', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/absensi/store/{jadwal}', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::put('/absensi/update', [AbsensiController::class, 'update'])->name('absensi.update');

    Route::get('/rekap-absensi/export-excel', [RekapAbsensiController::class, 'exportExcel'])->name('rekap.export.excel');
    Route::get('/rekap-absensi/export-pdf', [RekapAbsensiController::class, 'exportPdf'])->name('rekap.export.pdf');

    // Sub-grup khusus Guru Piket
    Route::middleware('role:admin,guru,guru_piket,walikelas')->prefix('piket')->name('piket.')->group(function () {
        Route::get('/dashboard-data', [GuruPiketController::class, 'getDashboardData'])->name('dashboard.data');
        Route::post('/hadirkan-manual', [GuruPiketController::class, 'hadirkanManual'])->name('hadirkan.manual');

        Route::get('/dashboard', [GuruPiketController::class, 'dashboard'])->name('dashboard');
        Route::get('/scan', [GuruPiketController::class, 'scan'])->name('scan');
        Route::post('/scan/record', [GuruPiketController::class, 'record'])->name('record');

        Route::get('/izin', [GuruPiketController::class, 'indexIzin'])->name('izin.index');
        Route::patch('/izin/{izin}/approve', [GuruPiketController::class, 'approveIzin'])->name('izin.approve');
        Route::patch('/izin/{izin}/reject', [GuruPiketController::class, 'rejectIzin'])->name('izin.reject');
    });
});


// =====================================================================
// GRUP ROUTE KHUSUS SISWA
// =====================================================================
Route::middleware(['auth', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-qrcode', [SiswaDashboardController::class, 'showMyQrCode'])->name('my_qrcode');

    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
});


// =====================================================================
// GRUP ROUTE KHUSUS WALI KELAS
// =====================================================================
Route::middleware(['auth', 'role:walikelas'])->prefix('walikelas')->name('walikelas.')->group(function () {

    // Dashboard Wali Kelas
    Route::get('/dashboard', [WalikelasController::class, 'dashboard'])->name('dashboard');

    // Rekap Absensi Harian (Detail per Tanggal + Foto)
    Route::get('/rekap-harian', [WalikelasController::class, 'rekapHarian'])->name('rekap.harian');
    // Route untuk update status harian manual
    Route::post('/rekap-harian/update', [WalikelasController::class, 'updateStatus'])->name('rekap.harian.update');
});


// Rute Autentikasi dari Breeze
require __DIR__ . '/auth.php';
