<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth; // Import Auth facade
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Pengajuan;
use App\Models\Perusahaan;
use App\Models\Siswa;

/**
 * Komponen landing page utama:
 * - Menampilkan statistik global terkait prakerin dan perusahaan.
 * - Menyediakan tombol logout ketika pengguna sudah masuk.
 * - Mengambil daftar logo perusahaan terbaru untuk ditampilkan di hero.
 */
class BerandaUtama extends Component
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

    /**
     * Mengeluarkan pengguna dari sesi saat ini dan kembali ke beranda.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('beranda');
    }
    /**
     * Mengirim data statistik ke view beranda livewire.
     */
    public function render()
    {
        return view('livewire.beranda-utama', [
            'statPengajuan' => $this->statPengajuan,
            'statPerusahaan' => $this->statPerusahaan,
            'statSiswa' => $this->statSiswa,
            'statSuccess' => $this->statSuccess,
            'perusahaanLogos' => $this->perusahaanLogos,
        ]);
    }
}
