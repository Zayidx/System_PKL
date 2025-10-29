<?php

use App\Http\Controllers\OtpController;
use App\Livewire\Administrator\DaftarPenilaianPkl;
use App\Livewire\Administrator\DasborGuru;
use App\Livewire\Administrator\DasborJurusan;
use App\Livewire\Administrator\DasborKelas;
use App\Livewire\Administrator\DasborKepalaProgram;
use App\Livewire\Administrator\DasborKepalaSekolah;
use App\Livewire\Administrator\DasborKompetensiNilai;
use App\Livewire\Administrator\DasborPembimbingPerusahaan;
use App\Livewire\Administrator\DasborPembimbingSekolah;
use App\Livewire\Administrator\DasborPengajuan;
use App\Livewire\Administrator\DasborPengajuanSiswa;
use App\Livewire\Administrator\DasborPengelolaanPengguna;
use App\Livewire\Administrator\DasborPerusahaan;
use App\Livewire\Administrator\DasborSiswa;
use App\Livewire\Administrator\DasborStafHubin;
use App\Livewire\Administrator\DasborStatusPengajuanSiswa;
use App\Livewire\Administrator\DasborUtama as DasborAdministratorUtama;
use App\Livewire\Administrator\DasborWaliKelas;
use App\Livewire\Administrator\DetailNilaiPkl;
use App\Livewire\Autentikasi\Daftar;
use App\Livewire\Autentikasi\LupaSandi;
use App\Livewire\Autentikasi\Masuk;
use App\Livewire\BerandaUtama;
use App\Livewire\Pengguna\AjukanPerusahaanBaru;
use App\Livewire\Pengguna\Dasbor as DasborPengguna;
use App\Livewire\Pengguna\NilaiSiswa as NilaiSiswaPengguna;
use App\Livewire\Pengguna\Pengajuan as PengajuanPengguna;
use App\Livewire\Pengguna\ProsesMagang;
use App\Livewire\Pengguna\ProsesPengajuan;
use App\Livewire\Pengguna\RiwayatPrakerin;
use App\Livewire\StafHubin\DasborNilaiSiswa;
use App\Livewire\StafHubin\DasborPrakerin;
use App\Livewire\StafHubin\DasborPrakerinSiswa;
use App\Livewire\StafHubin\DasborStatusPrakerinSiswa;
use App\Livewire\StafHubin\DasborUtama as DasborStafHubinUtama;
use App\Livewire\StafHubin\MitraPerusahaan;
use App\Http\Controllers\PengajuanApprovalController;
use App\Http\Controllers\PenilaianController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', BerandaUtama::class)->name('beranda');

Route::get('/masuk', Masuk::class)->name('masuk');
Route::get('/daftar', Daftar::class)->name('daftar');
Route::get('/lupa-sandi', LupaSandi::class)->name('lupa-sandi');

Route::middleware('auth')->group(function () {
    
    Route::post('/keluar', [Masuk::class, 'logout'])->name('keluar');

    Route::prefix('administrator')->name('administrator.')->middleware('role:superadmin')->group(function () {
        Route::get('/dasbor', DasborAdministratorUtama::class)->name('dasbor');

        Route::prefix('data-induk')->name('data.')->group(function () {
            Route::get('/pengguna', DasborPengelolaanPengguna::class)->name('pengguna');
            Route::get('/perusahaan', DasborPerusahaan::class)->name('perusahaan');
            Route::get('/siswa', DasborSiswa::class)->name('siswa');
            Route::get('/kelas', DasborKelas::class)->name('kelas');
            Route::get('/jurusan', DasborJurusan::class)->name('jurusan');
            Route::get('/guru', DasborGuru::class)->name('guru');
            Route::get('/wali-kelas', DasborWaliKelas::class)->name('wali-kelas');
            Route::get('/pembimbing-perusahaan', DasborPembimbingPerusahaan::class)->name('pembimbing-perusahaan');
            Route::get('/pembimbing-sekolah', DasborPembimbingSekolah::class)->name('pembimbing-sekolah');
            Route::get('/staf-hubin', DasborStafHubin::class)->name('staf-hubin');
            Route::get('/kepala-sekolah', DasborKepalaSekolah::class)->name('kepala-sekolah');
            Route::get('/kepala-program', DasborKepalaProgram::class)->name('kepala-program');
            Route::get('/kompetensi', DasborKompetensiNilai::class)->name('kompetensi');
            Route::get('/penilaian-pkl', DaftarPenilaianPkl::class)->name('penilaian-pkl');
            Route::get('/penilaian-pkl/detail/{id}', DetailNilaiPkl::class)->name('penilaian-pkl.detail');
        });
    });

    Route::prefix('staf-hubin')->name('staf-hubin.')->middleware('role:staffhubin')->group(function () {
        Route::get('/dasbor', DasborStafHubinUtama::class)->name('dasbor');
        Route::prefix('data-induk')->name('data.')->group(function () {
            Route::get('/pengajuan', DasborPengajuan::class)->name('pengajuan');
            Route::get('/pengajuan/kelas/{id_kelas}', DasborPengajuanSiswa::class)->name('pengajuan.kelas');
            Route::get('/pengajuan/siswa/{nis}', DasborStatusPengajuanSiswa::class)->name('pengajuan.siswa');
            Route::get('/mitra-perusahaan', MitraPerusahaan::class)->name('mitra-perusahaan');
            Route::get('/prakerin', DasborPrakerin::class)->name('prakerin');
            Route::get('/prakerin/kelas/{id_kelas}', DasborPrakerinSiswa::class)->name('prakerin.kelas');
            Route::get('/prakerin/status/{nis}', DasborStatusPrakerinSiswa::class)->name('prakerin.status');
            Route::get('/nilai/kelas/{id_kelas}', DasborNilaiSiswa::class)->name('nilai.kelas');
        });
    });

    Route::prefix('pengguna')->name('pengguna.')->middleware('role:user')->group(function () {
        Route::get('/dasbor', DasborPengguna::class)->name('dasbor');
        Route::get('/pengajuan', PengajuanPengguna::class)->name('pengajuan');
        Route::get('/pengajuan/proses/{id_perusahaan}', ProsesPengajuan::class)->name('pengajuan.proses');
        Route::get('/ajukan-perusahaan-baru', AjukanPerusahaanBaru::class)->name('ajukan-perusahaan-baru');
        Route::get('/magang', ProsesMagang::class)->name('magang');
        Route::get('/riwayat-prakerin', RiwayatPrakerin::class)->name('riwayat-prakerin');
        Route::get('/nilai', NilaiSiswaPengguna::class)->name('nilai');
    });

    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
});

// Routes untuk form penilaian (tidak memerlukan auth)
Route::get('/penilaian/form/{token}', [PenilaianController::class, 'showForm'])->name('penilaian.form');
Route::post('/penilaian/submit/{token}', [PenilaianController::class, 'submitPenilaian'])->name('penilaian.submit');
Route::get('/penilaian/success/{token}', [PenilaianController::class, 'showSuccess'])->name('penilaian.success');

Route::get('/pengajuan/approve/{token}', [PengajuanApprovalController::class, 'approve'])->name('pengajuan.approve');
Route::get('/pengajuan/decline/{token}', [PengajuanApprovalController::class, 'decline'])->name('pengajuan.decline');
