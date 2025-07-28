<?php

namespace App\Livewire\StaffHubin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Pengajuan;
use App\Models\Perusahaan;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

#[Layout("components.layouts.layout-staff-hubin-dashboard")]
#[Title('Dashboard Staff Hubin')]
class Dashboard extends Component
{
    public $statPengajuan;
    public $statPending;
    public $statDiterima;
    public $statDitolak;
    public $statPerusahaan;
    public $statSiswa;

    public $todayPengajuan;
    public $siswaTanpaPengajuan;
    public array $chartData = [];

    public $search = '';
    public $filterTanggal = '';
    public $filterStatus = '';

    public function mount()
    {
        $this->calculateStats();

        $this->todayPengajuan = Pengajuan::with(['siswa', 'perusahaan'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('created_at')
            ->get();

        $this->siswaTanpaPengajuan = Siswa::whereDoesntHave('pengajuan')
            ->with('kelas')
            ->orderBy('nama_siswa')
            ->take(10)
            ->get();
    }
    
    private function calculateStats()
    {
        $this->statPengajuan = Pengajuan::count();
        $this->statPending = Pengajuan::where('status_pengajuan', 'pending')->count();
        $this->statDiterima = Pengajuan::where('status_pengajuan', 'diterima_perusahaan')->count();
        $this->statDitolak = Pengajuan::whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan'])->count();
        $this->statPerusahaan = Perusahaan::count();
        $this->statSiswa = Siswa::count();

        $this->chartData = [
            'Pending' => $this->statPending,
            'Diterima' => $this->statDiterima,
            'Ditolak' => $this->statDitolak,
        ];
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->filterTanggal = '';
        $this->search = '';
    }

    public function clearFilter()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
    }

    public function getStatusClass($status)
    {
        return match ($status) {
            'pending' => 'bg-light-warning text-warning',
            'diterima_perusahaan' => 'bg-light-success text-success',
            'ditolak_admin', 'ditolak_perusahaan' => 'bg-light-danger text-danger',
            default => 'bg-light-secondary text-secondary',
        };
    }

    public function render()
    {
        $query = Pengajuan::with(['siswa', 'perusahaan'])
            ->orderByDesc('created_at');

        $query->when($this->search, function ($q) {
            $q->whereHas('siswa', function ($subq) {
                $subq->where('nama_siswa', 'like', '%' . $this->search . '%');
            });
        });

        $query->when($this->filterStatus, function ($q) {
            if ($this->filterStatus === 'ditolak') {
                 $q->whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan']);
            } else {
                 $q->where('status_pengajuan', $this->filterStatus);
            }
        });

       
        $query->when($this->filterTanggal, function ($q) {
            $days = match($this->filterTanggal) {
                '1_hari' => 1,
                '3_hari' => 3,
                '7_hari' => 7,
                '30_hari' => 30,
                default => 0,
            };

            if ($days > 0) {
                $q->where('created_at', '>=', Carbon::now()->subDays($days));
            }
        });

        $historyPengajuan = $query->take(20)->get();

        return view('livewire.staff-hubin.dashboard', [
            'historyPengajuan' => $historyPengajuan
        ]);
    }
}