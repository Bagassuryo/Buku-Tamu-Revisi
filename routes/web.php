<?php

use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. ROUTE PUBLIK (HANYA HALAMAN LOGIN YANG BISA DIAKSES TANPA LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    // Login User/Admin Biasa (OPD)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Login Khusus Super Admin
    Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLogin'])->name('superadmin.login');
    Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login'])->name('superadmin.login.process');
});


// =========================================================================
// 2. ROUTE TERPROTEKSI (Semua menu di bawah ini WAJIB LOGIN terlebih dahulu)
// =========================================================================
Route::middleware(['auth'])->group(function () {
    
    // --- FORM & DAFTAR TAMU (Sekarang sudah di dalam auth / tidak publik lagi) ---
    Route::get('/', [GuestController::class, 'create'])->name('tamu.create');
    Route::post('/form-tamu', [GuestController::class, 'store'])->name('tamu.store');
    Route::get('/pulang', [GuestController::class, 'showCheckoutForm'])->name('tamu.checkout.form');
    Route::post('/pulang', [GuestController::class, 'processCheckout'])->name('tamu.checkout.process');
    
    // Halaman daftar tamu (Bisa diakses Admin & Super Admin)
    Route::get('/guest', [GuestController::class, 'index'])->name('guest');
    Route::get('/guest/export', [GuestController::class, 'export'])->name('guest.export');
    
    // --- REVISI NAVBAR: REKAP ---
    // Menambahkan rute rekap yang menggantikan fungsi login di dalam navbar
    Route::get('/rekap', [GuestController::class, 'index'])->name('rekap.index'); // Sesuaikan method/controllernya nanti jika ada controller khusus rekap

    // Logout admin
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Logout superadmin
    Route::post('/superadmin/logout',[SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');

    // =====================================================================
    // 3. KHUSUS SUPER ADMIN (Kelola Admin)
    // =====================================================================
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/superadmin', [AdminController::class, 'index'])->name('superadmin');
        Route::post('/store', [AdminController::class, 'store'])->name('superadmin.store');
        Route::put('/admin/update/{username}', [AdminController::class, 'update'])->name('superadmin.update');
        Route::delete('/destroy/{username}', [AdminController::class, 'destroy'])->name('destroy');
    });

});