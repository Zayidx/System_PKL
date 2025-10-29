<?php

namespace App\Livewire\StafHubin;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Penilaian;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.layout-staf-hubin-dashboard')]
#[Title('Nilai PKL Siswa')]
/**
 * Menyajikan performa nilai PKL siswa dalam sebuah kelas:
 * - Menampilkan perbandingan prakerin selesai vs dinilai.
 * - Mendukung pencarian, pengurutan, dan detail modal per siswa.
 */
class DasborNilaiSiswa extends Component
{
    use WithPagination;

    public $id_kelas;
    public $kelas;
    public $search = '';
    public $perPage = 10;
    public $sortBy = 'nama_siswa';
    public $sortDir = 'asc';
    public $selectedSiswa = null;
    public $showDetailNilai = false;

    /**
     * Menyimpan referensi kelas yang sedang dipantau.
     */
    public function mount($id_kelas)
    {
        $this->id_kelas = $id_kelas;
        $this->kelas = Kelas::findOrFail($id_kelas);
    }
    
    /**
     * Mengubah kolom pengurutan, membalik arah bila kolom sama diklik.
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
     * Mengembalikan paginasi ke halaman awal saat kata kunci berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Memuat detail prakerin siswa (hanya yang selesai) untuk ditampilkan pada modal.
     */
    public function lihatDetailNilai($nis)
    {
        $this->selectedSiswa = Siswa::with(['prakerin' => function($query) {
                $query->where('status_prakerin', 'selesai')
                      ->with(['perusahaan', 'pembimbingPerusahaan']);
            }])
            ->where('nis', $nis)
            ->first();

        if ($this->selectedSiswa) {
            $this->showDetailNilai = true;
        }
    }

    /**
     * Menutup modal detail nilai dan membersihkan state terpilih.
     */
    public function tutupDetailNilai()
    {
        $this->showDetailNilai = false;
        $this->selectedSiswa = null;
    }

    /**
     * Menyusun daftar siswa beserta statistik prakerin dan mengembalikan view Livewire.
     */
    public function render()
    {
        $siswaList = Siswa::withCount(['prakerin as prakerin_selesai' => function($query) {
                $query->where('status_prakerin', 'selesai');
            }])
            ->withCount(['prakerin as prakerin_dinilai' => function($query) {
                $query->where('status_prakerin', 'selesai')
                      ->whereHas('pembimbingPerusahaan.penilaian', function($q) {
                          $q->whereColumn('penilaian.nis_siswa', 'siswa.nis');
                      });
            }])
            ->where('id_kelas', $this->id_kelas)
            ->where(function ($query) {
                $query->where('nama_siswa', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
            
        return view('livewire.staf-hubin.dasbor-nilai-siswa', [
            'siswaList' => $siswaList,
        ]);
    }
} 
