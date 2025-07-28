<?php

// 2. KODE UNTUK KOMPONEN LIVEWIRE
// Ganti isi file app/Livewire/Admin/Dashboard.php dengan kode ini

namespace App\Livewire\Admin;

use App\Models\Guru;
use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Pengajuan;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Admin Dashboard")]
class Dashboard extends Component
{
    // Properti untuk menampung data statistik
    public $pengajuanCount;
    public $pengajuanPending;
    public $pengajuanDiterima;
    public $pengajuanDitolak;
    public $pengajuanTren;
    public $pengajuanPie;
    public $latestPengajuan;
    public $topPerusahaan;
    public $latestUser;

    /**
     * Method mount dijalankan sekali saat komponen diinisialisasi.
     * Kita akan mengambil semua data di sini.
     */
    public function mount()
    {
        // Statistik pengajuan
        $this->pengajuanCount = Pengajuan::count();
        $this->pengajuanPending = Pengajuan::where('status_pengajuan', 'pending')->count();
        $this->pengajuanDiterima = Pengajuan::where('status_pengajuan', 'diterima_perusahaan')->count();
        $this->pengajuanDitolak = Pengajuan::whereIn('status_pengajuan', ['ditolak_admin','ditolak_perusahaan'])->count();

        // Pie chart distribusi status pengajuan
        $this->pengajuanPie = [
            'labels' => ['Pending', 'Diterima', 'Ditolak'],
            'data' => [
                $this->pengajuanPending,
                $this->pengajuanDiterima,
                $this->pengajuanDitolak,
            ],
        ];

        // Line chart tren pengajuan per bulan (12 bulan terakhir)
        $bulan = collect(range(0, 11))->map(function($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse()->values();

        $this->pengajuanTren = [
            'labels' => $bulan->map(fn($b) => Carbon::createFromFormat('Y-m', $b)->translatedFormat('M Y')),
            'data' => $bulan->map(fn($b) => Pengajuan::whereYear('created_at', substr($b,0,4))->whereMonth('created_at', substr($b,5,2))->count()),
        ];

        // Data pengajuan terbaru
        $this->latestPengajuan = Pengajuan::with(['siswa','perusahaan'])->latest('created_at')->take(5)->get();

        // Perusahaan dengan pengajuan terbanyak
        $this->topPerusahaan = Perusahaan::withCount('pengajuan')->orderByDesc('pengajuan_count')->take(5)->get();
        
        // User terbaru
        $this->latestUser = User::with('role')->latest('id')->take(5)->get();
    }

    /**
     * Merender view dan mengirimkan data ke dalamnya.
     */
    public function render()
    {
        // Semua data sudah di-load di mount(), jadi kita tinggal passing ke view
        return view('livewire.admin.dashboard', [
            'pengajuanCount' => $this->pengajuanCount,
            'pengajuanPending' => $this->pengajuanPending,
            'pengajuanDiterima' => $this->pengajuanDiterima,
            'pengajuanDitolak' => $this->pengajuanDitolak,
            'pengajuanPie' => $this->pengajuanPie,
            'pengajuanTren' => $this->pengajuanTren,
            'latestPengajuan' => $this->latestPengajuan,
            'topPerusahaan' => $this->topPerusahaan,
            'latestUser' => $this->latestUser,
        ]);
    }
}
