<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

// --- ROUTE PUBLIK (Bisa diakses siapa saja) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// --- ROUTE TERPROTEKSI (Hanya yang sudah login) ---
Route::middleware(['auth'])->group(function () {
    
    // Pindahkan route guest kamu ke sini
    Route::get('/guest', [GuestController::class, 'guest'])->name('guest');
    
    // Route logout juga harus di sini agar hanya orang login yang bisa logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
});

Route::get('/superadmin', [AdminController::class, 'index'])
    ->name('superadmin');

/*Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin', [AdminController::class, 'index'])->name('');
    // Tambahkan route khusus super admin lainnya di sini...
});
*/
Route::post('/store', [AdminController::class, 'store'])->name('superadmin.store');
Route::delete('/destroy/{username}', [AdminController::class, 'destroy'])->name('destroy');

// Halaman pengisian form (Publik)
Route::get('/', [GuestController::class, 'create']);
Route::post('/form-tamu', [GuestController::class, 'store'])->name('tamu.store');

Route::get('/pulang', [GuestController::class, 'showCheckoutForm'])->name('tamu.checkout.form');
Route::post('/pulang', [GuestController::class, 'processCheckout'])->name('tamu.checkout.process');
