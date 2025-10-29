<?php

namespace App\Livewire\StafHubin;

use App\Models\Kelas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.layout-staf-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
/**
 * Menampilkan daftar kelas beserta jumlah siswa dan siswa yang sudah prakerin.
 * Digunakan staf hubin sebagai pintu masuk ke detail prakerin per kelas.
 */
class DasborPrakerin extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    /**
     * Reset paginasi ketika kata kunci pencarian berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar kelas dengan perhitungan jumlah siswa/prakerin,
     * kemudian meneruskannya ke view Livewire.
     */
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

        return view('livewire.staf-hubin.dasbor-prakerin', [
            'kelasList' => $kelasList
        ]);
    }
} 
