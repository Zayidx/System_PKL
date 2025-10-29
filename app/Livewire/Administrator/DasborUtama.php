<?php

namespace App\Livewire\Administrator;

use App\Models\Pengajuan;
use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\Prakerin;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Dasbor Administrator")]
/**
 * Menyajikan ringkasan kinerja administrator:
 * - Menarik metrik global (total siswa, perusahaan, prakerin aktif, pengajuan pending).
 * - Menampilkan daftar pengajuan terbaru agar admin bisa memantau aktifitas terakhir.
 * - Mengemas refresh data dalam listener Livewire (`refresh-dashboard`) sehingga tombol pada view dapat memicu penyegaran tanpa reload halaman.
 */
class DasborUtama extends Component
{
    // Properti untuk data statistik
    public $totalSiswa = 0;
    public $totalPerusahaan = 0;
    public $siswaAktifPkl = 0;
    public $pengajuanPending = 0;

    // Properti untuk daftar data
    public $latestPengajuan = [];

    /**
     * Mount lifecycle hook.
     * Memuat data statistik saat komponen pertama kali di-render.
     */
    public function mount()
    {
        $this->loadStats();
    }

    /**
     * Listener untuk me-refresh data dashboard.
     * Dipicu oleh klik tombol di view.
     */
    #[On('refresh-dashboard')]
    public function refreshDashboard()
    {
        // Memuat ulang semua data statistik
        $this->loadStats();
    }

    /**
     * Fungsi utama untuk memuat semua data yang dibutuhkan oleh dashboard.
     * Dibungkus dalam try-catch untuk penanganan error yang lebih baik.
     */
    public function loadStats()
    {
        try {
            $this->totalSiswa = Siswa::count();
            $this->totalPerusahaan = Perusahaan::count();
            $this->siswaAktifPkl = Prakerin::where('status_prakerin', 'berlangsung')->count();
            $this->pengajuanPending = Pengajuan::where('status_pengajuan', 'pending')->count();
            
            // Mengambil 5 pengajuan terbaru dengan relasi siswa dan perusahaan
            $this->latestPengajuan = Pengajuan::with(['siswa', 'perusahaan'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Mencatat error jika terjadi kegagalan saat mengambil data
            Log::error('Gagal memuat data dashboard: ' . $e->getMessage());
            // Mengirim notifikasi error ke frontend
            $this->dispatch('swal:error', message: 'Gagal memuat data. Silakan coba lagi.');
        }
    }

    /**
     * Render komponen view.
     */
    public function render()
    {
        return view('livewire.administrator.dasbor-utama');
    }
}
