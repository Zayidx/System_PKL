<?php

namespace App\Livewire\Administrator;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Kelas;

#[Layout('components.layouts.layout-staf-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
/**
 * Menyajikan daftar kelas dan jumlah siswa untuk memantau pengajuan prakerin.
 * Difokuskan sebagai titik awal sebelum masuk ke detail siswa tiap kelas.
 */
class DasborPengajuan extends Component
{
    public $search = '';

    /**
     * Mengambil daftar kelas beserta jumlah siswa sesuai kata kunci pencarian.
     */
    public function render()
    {
        // Query untuk mengambil data kelas, dengan filter pencarian
        $kelasList = Kelas::withCount('siswa')
            ->where('nama_kelas', 'like', '%' . $this->search . '%')
            ->orderBy('nama_kelas')
            ->get();
        
        return view('livewire.administrator.dasbor-pengajuan', [
            'kelasList' => $kelasList,
        ]);
    }
}
