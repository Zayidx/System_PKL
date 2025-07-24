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
// Import komponen-komponen baru
use App\Livewire\Admin\PengajuanDashboard;
use App\Livewire\Admin\PengajuanSiswaDashboard;
use App\Livewire\Admin\StatusPengajuanSiswaDashboard;
use App\Livewire\User\Pengajuan;
use App\Http\Controllers\PengajuanApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomePage::class)->name('homepage');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');

Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [Login::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->middleware('role:superadmin')->group(function () {
        
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        Route::prefix('master-data')->name('master.')->group(function () {
            Route::get('/users', AdminUserManagement::class)->name('users');
            Route::get('/perusahaan', PerusahaanDashboard::class)->name('perusahaan');
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

     Route::prefix('staffhubin')->name('staffhubin.')->middleware('role:staffhubin')->group(function () {
        Route::get('/dashboard', StaffHubinDashboard::class)->name('dashboard');
        Route::prefix('master-data')->name('master.')->group(function () {
         // --- RUTE PENGAJUAN YANG BARU ---
            // 1. Halaman utama menampilkan daftar kelas
            Route::get('/pengajuan', PengajuanDashboard::class)->name('pengajuan');
            // 2. Halaman untuk menampilkan siswa dalam satu kelas
            Route::get('/pengajuan/kelas/{id_kelas}', PengajuanSiswaDashboard::class)->name('pengajuan.siswa');
            // 3. Halaman untuk menampilkan status pengajuan per siswa
            Route::get('/pengajuan/siswa/{nis}', StatusPengajuanSiswaDashboard::class)->name('pengajuan.status');
        });

    });


    Route::prefix('user')->name('user.')->middleware('role:user')->group(function () {
        Route::get('/dashboard', UserDashboard::class)->name('dashboard');
        Route::get('/pengajuan', Pengajuan::class)->name('pengajuan');
        Route::get('/pengajuan/proses/{id_perusahaan}', \App\Livewire\User\ProsesPengajuan::class)->name('pengajuan.proses');
    });

    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
});

Route::get('/pengajuan/approve/{token}', [PengajuanApprovalController::class, 'approve'])->name('pengajuan.approve');
Route::get('/pengajuan/decline/{token}', [PengajuanApprovalController::class, 'decline'])->name('pengajuan.decline');
