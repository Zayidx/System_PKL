<?php

namespace App\Livewire\StaffHubin;

use App\Models\Kelas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
class PrakerinDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $kelasList = Kelas::withCount(['siswa as total_siswa', 'siswa as prakerin_count' => function($query) {
                $query->whereHas('prakerin');
            }])
            ->when($this->search, function($query) {
                $query->where('nama_kelas', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama_kelas')
            ->paginate($this->perPage);

        return view('livewire.staff-hubin.prakerin-dashboard', [
            'kelasList' => $kelasList
        ]);
    }
} 