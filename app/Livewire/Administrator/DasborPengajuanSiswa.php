<?php

namespace App\Livewire\Administrator;

use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.layout-staf-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
/**
 * Menampilkan daftar siswa dalam satu kelas beserta status pengajuan prakerin:
 * - Mendukung pencarian nama/NIS dan pengurutan dinamis.
 * - Menghitung jumlah pengajuan setiap siswa untuk membantu prioritas tindak lanjut.
 */
class DasborPengajuanSiswa extends Component
{
      protected $paginationTheme = 'bootstrap';
    use WithPagination;

    public $id_kelas;
    public $kelas;
    public $search = '';
    public $perPage = 10;
    public $sortBy = 'nama_siswa';
    public $sortDir = 'asc';

    /**
     * Memastikan kelas yang diminta ada dan menyimpan referensinya untuk penggunaan lain.
     */
    public function mount($id_kelas)
    {
        $this->id_kelas = $id_kelas;
        $this->kelas = Kelas::findOrFail($id_kelas);
    }
    
    /**
     * Mengatur kolom pengurutan; klik berulang akan membalik arah sort.
     */
    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = ($this->sortDir === 'asc') ? 'desc' : 'asc';
            return;
        }
        $this->sortBy = $column;
        $this->sortDir = 'asc';
    }

    /**
     * Reset paginasi ketika kata kunci pencarian berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Menyusun daftar siswa lengkap dengan hitungan pengajuan lalu meneruskan ke view Livewire.
     */
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
            
        return view('livewire.administrator.dasbor-pengajuan-siswa', [
            'siswaList' => $siswaList,
        ]);
    }
}
