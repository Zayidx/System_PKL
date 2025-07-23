<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
class PengajuanSiswaDashboard extends Component
{
      protected $paginationTheme = 'bootstrap';
    use WithPagination;

    public $id_kelas;
    public $kelas;
    public $search = '';
    public $perPage = 10;
    public $sortBy = 'nama_siswa';
    public $sortDir = 'asc';

    public function mount($id_kelas)
    {
        $this->id_kelas = $id_kelas;
        $this->kelas = Kelas::findOrFail($id_kelas);
    }
    
    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = ($this->sortDir === 'asc') ? 'desc' : 'asc';
            return;
        }
        $this->sortBy = $column;
        $this->sortDir = 'asc';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $siswaList = Siswa::withCount('pengajuan') // Menambahkan hitungan relasi pengajuan
            ->where('id_kelas', $this->id_kelas)
            ->where(function ($query) {
                $query->where('nama_siswa', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
            
        return view('livewire.admin.pengajuan-siswa-dashboard', [
            'siswaList' => $siswaList,
        ]);
    }
}
