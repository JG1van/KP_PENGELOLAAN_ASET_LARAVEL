<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    AsetController,

    PenerimaanAsetController,
    PengecekanAsetController,
    PenghapusanAsetController,
    PengaturanController,
    ProfilController,
    Auth\AuthenticatedSessionController,
    PenempatanController,
    LokasiController,
};
// ===============================
// 🌐 HALAMAN UTAMA (PUBLIC)
// ===============================
Route::get('/', fn() => redirect()->route('login'));

// ===============================
// 🧭 DASHBOARD (SETELAH LOGIN)
// ===============================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ===============================
// 🔐 RUTE UNTUK PENGGUNA LOGIN AKTIF
// ===============================
Route::middleware(['auth', 'cek.status'])->group(function () {

    // 🔓 Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');



    // 🗂️ Kategori
    Route::resource('kategori', KategoriController::class);


    Route::prefix('lokasi')->name('lokasi.')->group(function () {
        Route::get('/', [LokasiController::class, 'index'])->name('index');
        Route::post('/', [LokasiController::class, 'store'])->name('store');
        Route::put('/{id}', [LokasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [LokasiController::class, 'destroy'])->name('destroy');
    });

    // 🏷️ Aset
    Route::resource('aset', AsetController::class);
    // Route::get('/aset/{id}/show', [AsetController::class, 'show'])->name('aset.show');
    Route::put('/aset/{id}/update-detail', [AsetController::class, 'updateDetail'])->name('aset.update.detail');
    Route::post('/aset/penurunan', [AsetController::class, 'prosesPenurunan'])->name('aset.penurunan');

    // 📦 Penerimaan Aset
    Route::prefix('penerimaan')->name('penerimaan.')->group(function () {
        Route::get('/', [PenerimaanAsetController::class, 'index'])->name('index');
        Route::get('/{id}', [PenerimaanAsetController::class, 'show'])->name('show');
        Route::put('/{id}', [PenerimaanAsetController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenerimaanAsetController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/qr', [PenerimaanAsetController::class, 'qr'])->name('qr');
        Route::get('/{id}/qr-pdf', [PenerimaanAsetController::class, 'exportQrPdf'])->name('qr.pdf');
    });


    Route::prefix('penempatan')->name('penempatan.')->group(function () {
        Route::get('/', [PenempatanController::class, 'index'])->name('index');
        Route::get('/create', [PenempatanController::class, 'create'])->name('create');
        Route::post('/store', [PenempatanController::class, 'store'])->name('store');
        Route::get('/penempatan/{id}', [PenempatanController::class, 'show'])->name('penempatan.show');
        Route::get('/{id}', [PenempatanController::class, 'show'])->name('show');
        Route::get('/{id}/export-excel', [PenempatanController::class, 'exportExcel'])
            ->name('export-excel');

    });

    // ✅ Pengecekan Aset
    Route::resource('pengecekan', PengecekanAsetController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/pengecekan/{id}/export-excel', [PengecekanAsetController::class, 'exportExcel'])->name('pengecekan.export-excel');

    // 🗑️ Penghapusan Aset
    Route::resource('penghapusan', PenghapusanAsetController::class)->only(['index', 'create', 'store', 'show']);

    // ⚙️ Pengaturan & Profil Akun
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {

        // Halaman utama pengaturan
        Route::get('/', [PengaturanController::class, 'index'])->name('index');

        // 🔐 Profil Akun (dikelola oleh ProfilController)
        Route::get('/akun', [ProfilController::class, 'index'])->name('profil.index');
        Route::put('/akun/update', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/akun/password', [ProfilController::class, 'updatePassword'])->name('profil.password');
        Route::put('/akun/simpan-semua', [ProfilController::class, 'simpanSemua'])->name('profil.simpanSemua');
        Route::put('/akun', [ProfilController::class, 'updateAll'])->name('profil.updateAll');
        Route::delete('/akun/nonaktifkan', [ProfilController::class, 'destroy'])->name('profil.nonaktif');

        // 👥 Manajemen Pengguna
        Route::get('/pengguna', [PengaturanController::class, 'pengguna'])->name('pengguna');
        Route::post('/pengguna', [PengaturanController::class, 'storePengguna'])->name('pengguna.store');
        Route::put('/pengguna/{id}', [PengaturanController::class, 'updatePengguna'])->name('pengguna.update');
        Route::delete('/pengguna/{id}', [PengaturanController::class, 'deletePengguna'])->name('pengguna.delete');

        // 📊 Laporan Aset
        Route::get('/laporan/aset', [PengaturanController::class, 'laporanAset'])->name('laporan.aset');
        Route::get('/laporan/aset/excel', [PengaturanController::class, 'exportLaporanAset'])->name('laporan.aset.excel');
        // 📝 Laporan Aktivitas
        Route::get('/laporan/aktivitas', [PengaturanController::class, 'laporanAktivitas'])->name('laporan.aktivitas');
        Route::get('/laporan/aktivitas/excel', [PengaturanController::class, 'exportLaporanAktivitas'])->name('laporan.aktivitas.excel');

        // Route::get(
        //     '/laporan/aktivitas/excel',
        //     fn() =>
        //     Excel::download(new LaporanAktivitasExport, 'Laporan-Aktivitas-Aset.xlsx')
        // )->name('laporan.aktivitas.excel');
        // 📝 Pengaktifan Aset
        Route::get('/pengaktifan', [PengaturanController::class, 'pengaktifanIndex'])->name('pengaktifan');
        Route::post('/pengaktifan/{id}', [PengaturanController::class, 'aktifkanAset'])->name('pengaktifan.aktifkan');
    });
});

// ===============================
// 🔐 AUTENTIKASI (LOGIN, REGISTER, DLL)
// ===============================
require __DIR__ . '/auth.php';
