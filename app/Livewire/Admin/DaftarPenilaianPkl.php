<?php

namespace App\Livewire\Admin;

use App\Models\Penilaian;
use App\Models\Kompetensi;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

#[Layout("components.layouts.layout-admin-dashboard")]
class DaftarPenilaianPkl extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJurusan = '';
    public $filterStatus = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJurusan()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Penilaian::with([
            'siswa.kelas.jurusan',
            'pembimbingPerusahaan.perusahaan',
            'kompetensi'
        ]);

        // Filter berdasarkan pencarian
        if ($this->search) {
            $query->whereHas('siswa', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            })->orWhereHas('pembimbingPerusahaan', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        // Filter berdasarkan jurusan
        if ($this->filterJurusan) {
            $query->whereHas('siswa.kelas.jurusan', function($q) {
                $q->where('id_jurusan', $this->filterJurusan);
            });
        }

        // Filter berdasarkan status (berdasarkan jumlah kompetensi)
        if ($this->filterStatus === 'selesai') {
            $query->whereHas('kompetensi');
        } elseif ($this->filterStatus === 'belum_selesai') {
            $query->whereDoesntHave('kompetensi');
        }

        $penilaian = $query->orderBy('id_penilaian', 'desc')->paginate(15);

        // Hitung statistik untuk setiap penilaian
        $penilaian->getCollection()->transform(function ($item) {
            $kompetensiWithNilai = $item->kompetensi()->withPivot('nilai')->get();
            $item->nilai_rata_rata = $kompetensiWithNilai->avg('pivot.nilai');
            $item->nilai_tertinggi = $kompetensiWithNilai->max('pivot.nilai');
            $item->nilai_terendah = $kompetensiWithNilai->min('pivot.nilai');
            $item->jumlah_kompetensi = $kompetensiWithNilai->count();
            return $item;
        });

        $jurusan = \App\Models\Jurusan::orderBy('nama_jurusan_lengkap')->get();

        return view('livewire.admin.daftar-penilaian-pkl', [
            'penilaian' => $penilaian,
            'jurusan' => $jurusan
        ]);
    }
} 