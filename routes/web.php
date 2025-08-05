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
use App\Livewire\StaffHubin\Dashboard as StaffHubinDashboard;
use App\Livewire\Admin\JurusanDashboard;
use App\Livewire\Admin\KepalaProgramDashboard;
use App\Livewire\Admin\KepalaSekolahDashboard;
use App\Livewire\Admin\PembimbingPerusahaan;
use App\Livewire\Admin\PembimbingPerusahaanDashboard;
use App\Livewire\Admin\PembimbingSekolahDashboard;
use App\Livewire\Admin\UserDashboard as AdminUserManagement; // Alias agar lebih jelas
use App\Livewire\Admin\WaliKelasDashboard;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\User\Dashboard as UserDashboard;
use App\Livewire\User\Pengajuan;
use App\Livewire\User\AjukanPerusahaanBaru;
use App\Livewire\User\ProsesPengajuan;
use App\Livewire\User\ProsesMagang;
use App\Livewire\User\RiwayatPrakerin;
use App\Livewire\StaffHubin\MitraPerusahaan;
use App\Http\Controllers\PengajuanApprovalController;
use App\Livewire\Admin\PengajuanDashboard;
use App\Livewire\Admin\PengajuanSiswaDashboard;
use App\Livewire\Admin\StatusPengajuanSiswaDashboard;
use App\Livewire\StaffHubin\PrakerinDashboard;
use App\Livewire\StaffHubin\PrakerinSiswaDashboard;
use App\Livewire\StaffHubin\StatusPrakerinSiswaDashboard;
use App\Livewire\User\NilaiSiswa;
use App\Livewire\StaffHubin\NilaiSiswaDashboard;
use App\Livewire\Admin\KompetensiNilaiDashboard;
use App\Livewire\Admin\DetailNilaiPkl;
use App\Livewire\Admin\DaftarPenilaianPkl;
use App\Http\Controllers\PenilaianController;

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
            Route::get('/kompetensi', KompetensiNilaiDashboard::class)->name('kompetensi');
            Route::get('/penilaian-pkl', DaftarPenilaianPkl::class)->name('penilaian-pkl');
            Route::get('/penilaian-pkl/detail/{id}', DetailNilaiPkl::class)->name('penilaian-pkl.detail');
        });
    });

     Route::prefix('staffhubin')->name('staffhubin.')->middleware('role:staffhubin')->group(function () {
        Route::get('/dashboard', StaffHubinDashboard::class)->name('dashboard');
        Route::prefix('master-data')->name('master.')->group(function () {
            Route::get('/pengajuan', PengajuanDashboard::class)->name('pengajuan');
            Route::get('/pengajuan/kelas/{id_kelas}', PengajuanSiswaDashboard::class)->name('pengajuan.siswa');
            Route::get('/pengajuan/siswa/{nis}', StatusPengajuanSiswaDashboard::class)->name('pengajuan.status');
            Route::get('/mitra-perusahaan', MitraPerusahaan::class)->name('mitra-perusahaan');
            Route::get('/prakerin', PrakerinDashboard::class)->name('prakerin');
            Route::get('/prakerin/kelas/{id_kelas}', PrakerinSiswaDashboard::class)->name('prakerin.siswa');
            Route::get('/prakerin/status/{nis}', StatusPrakerinSiswaDashboard::class)->name('prakerin.status');
            Route::get('/nilai/kelas/{id_kelas}', NilaiSiswaDashboard::class)->name('nilai.siswa');
        });
    });

    Route::prefix('user')->name('user.')->middleware('role:user')->group(function () {
        Route::get('/dashboard', UserDashboard::class)->name('dashboard');
        Route::get('/pengajuan', Pengajuan::class)->name('pengajuan');
        Route::get('/pengajuan/proses/{id_perusahaan}', ProsesPengajuan::class)->name('pengajuan.proses');
        Route::get('/ajukan-perusahaan-baru', AjukanPerusahaanBaru::class)->name('ajukan-perusahaan-baru');
        Route::get('/magang', ProsesMagang::class)->name('magang');
        Route::get('/riwayat-prakerin', RiwayatPrakerin::class)->name('riwayat-prakerin');
        Route::get('/nilai', NilaiSiswa::class)->name('nilai');
    });

    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
});

// Routes untuk form penilaian (tidak memerlukan auth)
Route::get('/penilaian/form/{token}', [PenilaianController::class, 'showForm'])->name('penilaian.form');
Route::post('/penilaian/submit/{token}', [PenilaianController::class, 'submitPenilaian'])->name('penilaian.submit');
Route::get('/penilaian/success/{token}', [PenilaianController::class, 'showSuccess'])->name('penilaian.success');

Route::get('/pengajuan/approve/{token}', [PengajuanApprovalController::class, 'approve'])->name('pengajuan.approve');
Route::get('/pengajuan/decline/{token}', [PengajuanApprovalController::class, 'decline'])->name('pengajuan.decline');
