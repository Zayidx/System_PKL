<?php

namespace App\Livewire\User;

use App\Models\Prakerin;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Informasi Magang')]
class ProsesMagang extends Component
{
    public $prakerinData = [];
    public $pengajuanData = [];

    /**
     * Method yang dijalankan saat komponen pertama kali di-mount.
     */
    public function mount()
    {
        $this->loadData();
    }

    /**
     * Memuat data magang dan pengajuan untuk siswa yang sedang login.
     */
    public function loadData()
    {
        $user = Auth::user();
        
        // Pastikan user memiliki relasi siswa
        if ($user && $user->siswa) {
            $siswa = $user->siswa;

            // Ambil data prakerin (magang yang sudah dikonfirmasi dan berjalan)
            // Relasi yang diambil (eager loading) untuk optimasi query
            $this->prakerinData = Prakerin::with([
                'perusahaan',
                'pembimbingPerusahaan',
                'pembimbingSekolah'
            ])->where('nis_siswa', $siswa->nis)->get();

            // Ambil data pengajuan yang statusnya sudah diterima oleh perusahaan
            // tetapi belum diproses menjadi data 'prakerin' oleh admin.
            $this->pengajuanData = Pengajuan::with([
                'perusahaan'
            ])->where('nis_siswa', $siswa->nis)
              ->where('status_pengajuan', 'diterima_perusahaan')
              ->get();
        }
    }

    /**
     * Merender view komponen.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.user.proses-magang');
    }
}
