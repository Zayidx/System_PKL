<?php

use App\Livewire\Administrator\DasborPengelolaanPengguna;
use App\Livewire\Administrator\DasborUtama as DasborAdministratorUtama;
use Illuminate\Support\Facades\Route;

// --- GRUP UNTUK SUPERADMIN ---
// Diberi prefix 'administrator', nama 'administrator.', dan dilindungi oleh middleware 'role:superadmin'
Route::prefix('administrator')->name('administrator.')->middleware('role:superadmin')->group(function () {
    Route::get('/dasbor', DasborAdministratorUtama::class)->name('dasbor');

    // Grup untuk data pengguna
    Route::prefix('data-pengguna')->name('data-pengguna.')->group(function () {
        Route::get('/daftar', DasborPengelolaanPengguna::class)->name('daftar');
        // Tambahkan rute master data lainnya di sini...
    });
});
