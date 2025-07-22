<?php

namespace App\Livewire\Admin;

use App\Models\Guru;
use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Admin Dashboard")]
class Dashboard extends Component
{
    // Properti untuk menampung data statistik
    public $userCount;
    public $siswaCount;
    public $guruCount;
    public $perusahaanCount;
    public $chartData;

    /**
     * Method mount dijalankan sekali saat komponen diinisialisasi.
     * Kita akan mengambil semua data di sini.
     */
    public function mount()
    {
        $this->userCount = User::count();
        $this->siswaCount = Siswa::count();
        $this->guruCount = Guru::count();
        $this->perusahaanCount = Perusahaan::count();

        // Menyiapkan data untuk dikirim ke Chart.js di frontend
        $this->chartData = [
            'labels' => ['Total User', 'Total Siswa', 'Total Guru', 'Total Perusahaan'],
            'data' => [
                $this->userCount,
                $this->siswaCount,
                $this->guruCount,
                $this->perusahaanCount,
            ],
        ];
    }

    /**
     * Merender view dan mengirimkan data ke dalamnya.
     */
    public function render()
    {
        // Mengambil 10 data terbaru untuk ditampilkan dalam daftar
        $latestPerusahaan = Perusahaan::latest('id_perusahaan')->take(10)->get();
        $latestSiswa = Siswa::with('user')->latest('nis')->take(10)->get();
        $latestUser = User::with('role')->latest('id')->take(10)->get();

        return view('livewire.admin.dashboard', [
            'latestPerusahaan' => $latestPerusahaan,
            'latestSiswa' => $latestSiswa,
            'latestUser' => $latestUser,
        ]);
    }
}
