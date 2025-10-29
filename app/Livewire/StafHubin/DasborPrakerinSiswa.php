<?php

namespace App\Livewire\StafHubin;

use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('components.layouts.layout-staf-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
/**
 * Menampilkan daftar siswa dalam kelas tertentu yang sudah memiliki prakerin:
 * - Menyediakan pencarian berdasarkan nama/NIS.
 * - Menampilkan jumlah prakerin per siswa untuk memudahkan monitoring.
 */
class DasborPrakerinSiswa extends Component
{
    use WithPagination;

    public $id_kelas;
    public $kelas;
    public $search = '';
    public $perPage = 10;

    /**
     * Memastikan kelas ada dan menyimpan referensinya.
     */
    public function mount($id_kelas)
    {
        $this->id_kelas = $id_kelas;
        $this->kelas = Kelas::findOrFail($id_kelas);
    }

    /**
     * Reset paginasi ketika pencarian berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar siswa beserta jurusan & hitungan prakerin, kemudian meneruskan ke view.
     */
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

        return view('livewire.staf-hubin.dasbor-prakerin-siswa', [
            'siswaList' => $siswaList
        ]);
    }
} 
