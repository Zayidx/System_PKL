<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout("components.layouts.layout-user-dashboard")]
#[Title('Dashboard Siswa')]
class Dashboard extends Component
{
    // Properti untuk menyimpan data yang akan ditampilkan
    public $user;
    public $siswa;
    public $totalPengajuan = 0;
    public $diterimaCount = 0;
    public $ditolakCount = 0;
    public $pendingCount = 0;
    public $recentPengajuan = [];

    /**
     * Lifecycle hook yang berjalan saat komponen pertama kali dimuat.
     * Mengambil dan memproses semua data yang diperlukan untuk dashboard.
     */
    public function mount()
    {
        // Mengambil data user yang login beserta relasi yang diperlukan
        $user = Auth::user()->load(['siswa.kelas', 'siswa.jurusan', 'siswa.pengajuan.perusahaan']);
        
        $this->user = $user;
        $this->siswa = $user->siswa;

        if ($this->siswa) {
            $pengajuan = $this->siswa->pengajuan;

            // Menghitung statistik pengajuan
            $this->totalPengajuan = $pengajuan->count();
            $this->diterimaCount = $pengajuan->where('status_pengajuan', 'diterima_perusahaan')->count();
            $this->ditolakCount = $pengajuan->whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan'])->count();
            $this->pendingCount = $pengajuan->whereIn('status_pengajuan', ['pending', 'diterima_admin'])->count();
            
            // Mengambil 5 pengajuan terbaru untuk ditampilkan
            $this->recentPengajuan = $pengajuan->sortByDesc('id_pengajuan')->take(5);
        }
    }

    public function render()
    {
        return view('livewire.user.dashboard');
    }
}
