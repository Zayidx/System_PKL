<?php

use App\Http\Controllers\OtpController;
use App\Livewire\Admin\GuruDashboard;
use App\Livewire\Admin\KelasDashboard;
use App\Livewire\Admin\PerusahaanDashboard;
use App\Livewire\Admin\SiswaDashboard;
use App\Livewire\HomePage;
use Illuminate\Support\Facades\Route;

// Import semua komponen Livewire yang akan digunakan di rute
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\JurusanDashboard;
use App\Livewire\Admin\KepalaProgramDashboard;
use App\Livewire\Admin\KepalaSekolahDashboard;
use App\Livewire\Admin\PembimbingPerusahaan;
use App\Livewire\Admin\PembimbingPerusahaanDashboard;
use App\Livewire\Admin\PembimbingSekolahDashboard;
use App\Livewire\Admin\StaffHubinDashboard;
use App\Livewire\Admin\UserDashboard as AdminUserManagement; // Alias agar lebih jelas
use App\Livewire\Admin\WaliKelasDashboard;
use App\Livewire\Auth\ForgotPassword;
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
    Route::get('/', HomePage::class)->name('homepage');

// Grup untuk tamu (pengguna yang BELUM login)
// Middleware 'guest' akan mengarahkan pengguna yang sudah login ke home/dashboard
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
  Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
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

     Route::prefix('master-data')->name('master.')->group(function () {
    Route::get('/users', AdminUserManagement::class)->name('users'); // Nama lengkap: admin.master.users
    Route::get('/perusahaan', action: PerusahaanDashboard::class)->name('perusahaan'); // Nama lengkap: admin.master.perusahaan
    Route::get('/siswa', SiswaDashboard::class)->name('siswa');
    Route::get('/kelas', KelasDashboard::class)->name('kelas');
    Route::get('/jurusan', JurusanDashboard::class)->name('jurusan');
        Route::get('/guru', GuruDashboard::class)->name('guru');
        Route::get('/walikelas', WaliKelasDashboard::class)->name('walikelas');
        Route::get('/pembimbing-perusahaan', PembimbingPerusahaanDashboard::class)->name('pembimbing-perusahaan');
         Route::get('/pembimbing-sekolah', PembimbingSekolahDashboard::class)->name('pembimbing-sekolah');
          Route::get('/staff-hubin', StaffHubinDashboard::class)->name('staff-hubin');
          Route::get('/kepala-sekolah', KepalaSekolahDashboard::class)->name('kepala-sekolah');
          Route::get('/kepala-program', KepalaProgramDashboard::class)->name('kepala-program');

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
