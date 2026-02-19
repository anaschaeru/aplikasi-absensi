<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\LaporanHarianController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Guru\IzinController as GuruIzinController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\GuruPiketController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanAbsensiController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\Siswa\IzinController as SiswaIzinController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\WalikelasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// =====================================================================
// [1] RUTE KHUSUS MODE KIOSK / PIKET (TANPA LOGIN)
// =====================================================================
// Route untuk layar scan/standby
Route::prefix('piket')->name('piket.')->group(function () {
    Route::get('/dashboard', [GuruPiketController::class, 'dashboard'])->name('dashboard');
    Route::post('/scan/record', [GuruPiketController::class, 'record'])->name('record');
    Route::get('/dashboard-data', [GuruPiketController::class, 'getDashboardData'])->name('dashboard.data');
    Route::post('/hadirkan-manual', [GuruPiketController::class, 'hadirkanManual'])->name('hadirkan.manual');
});


// =====================================================================
// [2] RUTE PENGALIHAN SETELAH LOGIN
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


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =====================================================================
// [3] GRUP ROUTE KHUSUS ADMIN
// =====================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('kelas', KelasController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('jadwal', JadwalPelajaranController::class);
    Route::resource('mapel', MataPelajaranController::class);

    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::post('/kelas/import', [KelasController::class, 'import'])->name('kelas.import');
    Route::post('/mapel/import', [MataPelajaranController::class, 'import'])->name('mapel.import');
    Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::post('/jadwal/import', [JadwalPelajaranController::class, 'import'])->name('jadwal.import');
    Route::get('/siswa/search', [SiswaController::class, 'search'])->name('siswa.search');

    Route::get('/laporan/absensi', [LaporanAbsensiController::class, 'index'])->name('laporan.absensi.index');
    Route::get('/laporan/absensi/export-excel', [LaporanAbsensiController::class, 'exportExcel'])->name('laporan.absensi.export.excel');
    Route::get('/laporan/absensi/export-pdf', [LaporanAbsensiController::class, 'exportPdf'])->name('laporan.absensi.export.pdf');

    Route::get('/laporan/harian', [LaporanHarianController::class, 'index'])->name('laporan.harian');

    Route::get('/laporan/harian/excel', [LaporanHarianController::class, 'exportExcel'])->name('laporan.harian.excel');
    Route::get('/laporan/harian/pdf', [LaporanHarianController::class, 'exportPdf'])->name('laporan.harian.pdf');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    Route::patch('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
});


// =====================================================================
// [4] GRUP ROUTE KHUSUS GURU, PIKET & WALIKELAS (Authenticated)
// =====================================================================
Route::middleware(['auth', 'role:admin,guru,guru_piket,walikelas'])->group(function () {

    // --- GROUP GURU (Prefix: guru) ---
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

        Route::get('/riwayat-absensi', [GuruDashboardController::class, 'history'])->name('absensi.history');
        Route::get('/riwayat-absensi/{jadwal}/{tanggal}', [GuruDashboardController::class, 'historyShow'])->name('absensi.history.show');

        Route::get('/absensi/create/{jadwal}', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi/store/{jadwal}', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::put('/absensi/update', [AbsensiController::class, 'update'])->name('absensi.update');

        Route::get('/rekap-absensi/export-excel', [RekapAbsensiController::class, 'exportExcel'])->name('rekap.export.excel');
        Route::get('/rekap-absensi/export-pdf', [RekapAbsensiController::class, 'exportPdf'])->name('rekap.export.pdf');
    });

    // --- GROUP MANAJEMEN PIKET (Prefix: piket) ---
    // Dipisah dari grup 'guru' di atas agar namanya tetap 'piket.izin.index'
    // Tapi tetap di dalam middleware Auth
    Route::prefix('piket')->name('piket.')->group(function () {
        Route::resource('izin', GuruIzinController::class);
    });
});


// =====================================================================
// [5] GRUP ROUTE KHUSUS SISWA
// =====================================================================
Route::middleware(['auth', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-qrcode', [SiswaDashboardController::class, 'showMyQrCode'])->name('my_qrcode');

    Route::get('/izin', [SiswaIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [SiswaIzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [SiswaIzinController::class, 'store'])->name('izin.store');
});


// =====================================================================
// [6] GRUP ROUTE KHUSUS WALI KELAS
// =====================================================================
Route::middleware(['auth', 'role:walikelas'])->prefix('walikelas')->name('walikelas.')->group(function () {
    Route::get('/dashboard', [WalikelasController::class, 'dashboard'])->name('dashboard');
    Route::get('/rekap-harian', [WalikelasController::class, 'rekapHarian'])->name('rekap.harian');
    Route::post('/rekap-harian/update', [WalikelasController::class, 'updateStatus'])->name('rekap.harian.update');
    Route::get('/rekap-harian/export-pdf', [WalikelasController::class, 'cetakPdf'])->name('rekap.export');
});

require __DIR__ . '/auth.php';
