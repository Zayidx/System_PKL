<?php

namespace App\Livewire\StaffHubin;

use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
class PrakerinSiswaDashboard extends Component
{
    use WithPagination;

    public $id_kelas;
    public $kelas;
    public $search = '';
    public $perPage = 10;

    public function mount($id_kelas)
    {
        $this->id_kelas = $id_kelas;
        $this->kelas = Kelas::findOrFail($id_kelas);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $siswaList = Siswa::with(['kelas', 'jurusan'])
            ->where('id_kelas', $this->id_kelas)
            ->whereHas('prakerin')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('prakerin')
            ->orderBy('nama_siswa')
            ->paginate($this->perPage);

        return view('livewire.staff-hubin.prakerin-siswa-dashboard', [
            'siswaList' => $siswaList
        ]);
    }
} 