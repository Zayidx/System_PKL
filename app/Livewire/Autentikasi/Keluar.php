<?php

namespace App\Livewire\Autentikasi;

use Detection\MobileDetect;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Komponen tombol keluar yang mendukung tampilan responsif:
 * - Mendeteksi perangkat mobile untuk menyesuaikan UI jika diperlukan.
 * - Menyediakan aksi logout dan mengarahkan kembali ke halaman masuk.
 */
class Keluar extends Component
{
    public $isMobile;

    /**
     * Menginisialisasi informasi apakah pengguna menggunakan perangkat mobile.
     */
    public function mount()
    {
        $mobile = new MobileDetect();
        $this->isMobile = $mobile->isMobile();
    }
    /**
     * Mengeluarkan pengguna dan mengarahkan ke halaman masuk.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('masuk');
    }

    /**
     * Mengembalikan tampilan Livewire yang memuat tombol logout.
     */
    public function render()
    {
        return view('livewire.autentikasi.keluar');
    }
}
