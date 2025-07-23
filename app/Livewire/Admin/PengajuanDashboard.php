<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Kelas;

#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
class PengajuanDashboard extends Component
{
    public $search = '';

    public function render()
    {
        // Query untuk mengambil data kelas, dengan filter pencarian
        $kelasList = Kelas::withCount('siswa')
            ->where('nama_kelas', 'like', '%' . $this->search . '%')
            ->orderBy('nama_kelas')
            ->get();
        
        return view('livewire.admin.pengajuan-dashboard', [
            'kelasList' => $kelasList,
        ]);
    }
}
