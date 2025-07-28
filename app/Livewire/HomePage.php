<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth; // Import Auth facade
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Pengajuan;
use App\Models\Perusahaan;
use App\Models\Siswa;

class HomePage extends Component
{
    #[Layout("components.layouts.layout-homepage")]

    public $user;
    public $statPengajuan;
    public $statPerusahaan;
    public $statSiswa;
    public $statSuccess;
    public $perusahaanLogos;

    /**
     * Lifecycle hook yang berjalan saat komponen pertama kali dimuat.
     * Kita akan mengambil data pengguna yang sedang login di sini.
     */
    public function mount()
    {
        // Mengambil seluruh data pengguna yang terotentikasi
        $this->user = Auth::user();
        $this->statPengajuan = Pengajuan::count();
        $this->statPerusahaan = Perusahaan::count();
        $this->statSiswa = Siswa::count();
        $this->statSuccess = Pengajuan::where('status_pengajuan', 'diterima_perusahaan')->count();
        $this->perusahaanLogos = Perusahaan::orderByDesc('id_perusahaan')->take(6)->get();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('homepage');
    }
    public function render()
    {
        return view('livewire.home-page', [
            'statPengajuan' => $this->statPengajuan,
            'statPerusahaan' => $this->statPerusahaan,
            'statSiswa' => $this->statSiswa,
            'statSuccess' => $this->statSuccess,
            'perusahaanLogos' => $this->perusahaanLogos,
        ]);
    }
}
