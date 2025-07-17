<?php

use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;

// Import semua komponen Livewire yang akan digunakan di rute
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserDashboard as AdminUserManagement; // Alias agar lebih jelas
use App\Livewire\User\Dashboard as UserDashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dan semuanya
| akan ditugaskan ke grup middleware "web".
|
*/

// Rute default, mengarahkan ke halaman login
Route::get("/", function () {
    return view("welcome");
});

// Grup untuk tamu (pengguna yang BELUM login)
// Middleware 'guest' akan mengarahkan pengguna yang sudah login ke home/dashboard
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');

// Grup untuk pengguna yang SUDAH login
// Middleware 'auth' memastikan hanya pengguna terotentikasi yang bisa mengakses
Route::middleware('auth')->group(function () {
    
    // Rute Logout (diletakkan di sini agar hanya bisa diakses saat login)
    // Menggunakan POST untuk keamanan (mencegah CSRF)
    Route::post('/logout', [Login::class, 'logout'])->name('logout');

    // --- GRUP UNTUK SUPERADMIN ---
    // Diberi prefix 'admin', nama 'admin.', dan dilindungi oleh middleware 'role:superadmin'
    Route::prefix('admin')->name('admin.')->middleware('role:superadmin')->group(function () {
        
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        // Grup untuk data master (contoh: manajemen user oleh admin)
        // Diberi prefix 'master-user' dan nama 'master-user.'
        Route::prefix('master-user')->name('master-user.')->group(function () {
            // Nama rute ini adalah 'users', sehingga nama lengkapnya 'admin.master-user.users'
            Route::get('/users', AdminUserManagement::class)->name('users');
            // Tambahkan rute master data lainnya di sini...
        });
    });


    // --- GRUP UNTUK USER BIASA ---
    // Diberi prefix 'user', nama 'user.', dan dilindungi oleh middleware 'role:user'
    Route::prefix('user')->name('user.')->middleware('role:user')->group(function () {
        
        Route::get('/dashboard', UserDashboard::class)->name('dashboard');
        // Tambahkan rute lain untuk user biasa di sini...
        // Route::get('/profile', UserProfile::class)->name('profile');

    });

    Route::post('/send-otp', [OtpController::class, 'sendOtp']);


});
