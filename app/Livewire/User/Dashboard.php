<?php

namespace App\Livewire\User;

use App\Models\Pengajuan;
use App\Models\Prakerin;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout("components.layouts.layout-user-dashboard")]
#[Title('Dashboard Siswa')]
class Dashboard extends Component
{
    // Properti dari Dashboard asli
    public $user;
    public $siswa;
    public $totalPengajuan = 0;
    public $diterimaCount = 0;
    public $ditolakCount = 0;
    public $pendingCount = 0;
    public $recentPengajuan = [];

    // Properti dari ProsesMagang
    public $prakerinData;
    public $pengajuanDiterimaData;

    /**
     * Lifecycle hook yang berjalan saat komponen pertama kali dimuat.
     * Mengambil dan memproses semua data yang diperlukan untuk dashboard gabungan.
     */
    public function mount()
    {
        // Inisialisasi koleksi untuk data magang
        $this->prakerinData = collect();
        $this->pengajuanDiterimaData = collect();

        // Mengambil data user yang login beserta relasi siswa
        $user = Auth::user()->load(['siswa.kelas', 'siswa.jurusan']);
        $this->user = $user;
        $this->siswa = $user->siswa;

        // Lanjutkan hanya jika data siswa ada
        if ($this->siswa) {
            // 1. Cek dan update prakerin yang sudah lewat waktu
            $this->updateExpiredPrakerin();
            
            // 2. Mengambil semua pengajuan untuk statistik dan aktivitas terbaru
            $semuaPengajuan = $this->siswa->pengajuan()->with('perusahaan')->get();

            // Menghitung statistik pengajuan
            $this->totalPengajuan = $semuaPengajuan->count();
            $this->diterimaCount = $semuaPengajuan->where('status_pengajuan', 'diterima_perusahaan')->count();
            $this->ditolakCount = $semuaPengajuan->whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan'])->count();
            $this->pendingCount = $semuaPengajuan->whereIn('status_pengajuan', ['pending', 'diterima_admin'])->count();
            
            // Mengambil 5 pengajuan terbaru untuk ditampilkan
            $this->recentPengajuan = $semuaPengajuan->sortByDesc('id_pengajuan')->take(5);

            // 3. Logika dari ProsesMagang: Cek status magang aktif
            // Memuat relasi yang dibutuhkan untuk detail magang
            $this->prakerinData = Prakerin::with([
                'perusahaan.pembimbingSekolah',
                'pembimbingPerusahaan'
            ])
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->latest('tanggal_mulai')
            ->get();

            // 4. Jika tidak ada magang aktif, cek pengajuan yang sudah diterima perusahaan
            if ($this->prakerinData->isEmpty()) {
                $this->pengajuanDiterimaData = Pengajuan::with('perusahaan.pembimbingSekolah')
                    ->where('nis_siswa', $this->siswa->nis)
                    ->where('status_pengajuan', 'diterima_perusahaan')
                    ->latest('updated_at')
                    ->get();
            }
        }
    }

    /**
     * Update prakerin yang sudah lewat waktu menjadi selesai
     */
    private function updateExpiredPrakerin()
    {
        $expiredPrakerin = Prakerin::where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->where('tanggal_selesai', '<', now())
            ->get();

        foreach ($expiredPrakerin as $prakerin) {
            $prakerin->update(['status_prakerin' => 'selesai']);
        }
    }

    /**
     * Merender view komponen dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.user.dashboard');
    }
}
