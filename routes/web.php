<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\LayananController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. ROUTE PUBLIK (HANYA HALAMAN LOGIN YANG BISA DIAKSES TANPA LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    // Satu Pintu Login untuk Semua User (Admin & Super Admin)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// =========================================================================
// 2. ROUTE TERPROTEKSI (Semua menu di bawah ini WAJIB LOGIN terlebih dahulu)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- FORM & DAFTAR TAMU ---
    Route::get('/', [GuestController::class, 'create'])->name('tamu.create');
    Route::post('/form-tamu', [GuestController::class, 'store'])->name('tamu.store');
    Route::get('/pulang', [GuestController::class, 'showCheckoutForm'])->name('tamu.checkout.form');
    Route::post('/pulang', [GuestController::class, 'processCheckout'])->name('tamu.checkout.process');

    // Halaman daftar tamu (Bisa diakses Admin & Super Admin)
    Route::get('/guest', [GuestController::class, 'index'])->name('guest');
    Route::get('/guest/export', [GuestController::class, 'export'])->name('guest.export');

    // --- REVISI NAVBAR: REKAP ---
    Route::get('/guest', [GuestController::class, 'index'])->name('rekap.index');

    // Satu Rute Logout untuk Semua User
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =====================================================================
    // 3. KHUSUS SUPER ADMIN (Kelola Admin)
    // =====================================================================
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/superadmin', [AdminController::class, 'index'])->name('superadmin');
        Route::post('/store', [AdminController::class, 'store'])->name('superadmin.store');
        Route::put('/admin/update/{username}', [AdminController::class, 'update'])->name('superadmin.update');
        Route::delete('/destroy/{username}', [AdminController::class, 'destroy'])->name('destroy');

        Route::post('/instansi/store', [InstansiController::class, 'store'])->name('instansi.store');
        Route::put('/instansi/update/{id}', [InstansiController::class, 'update'])->name('instansi.update');
        Route::delete('/instansi/destroy/{id}', [InstansiController::class, 'destroy'])->name('instansi.destroy');

        // Layanan (untuk edit/hapus per layanan)
        Route::delete('/layanan/destroy/{id}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    });

    // 4. INSTANSI DAN LAYANAN (SUPER ADMIN)
    // Instansi

    Route::get('/api/instansi', [InstansiController::class, 'getAll'])->name('api.instansi');
    Route::get('/api/layanan/{instansi_id}', [InstansiController::class, 'getLayanan'])->name('api.layanan');
});
