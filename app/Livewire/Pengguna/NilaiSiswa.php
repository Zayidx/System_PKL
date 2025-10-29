<?php

namespace App\Livewire\Pengguna;

use App\Models\Penilaian;
use App\Models\Prakerin;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout("components.layouts.layout-user-dashboard")]
#[Title('Nilai PKL')]
/**
 * Menampilkan nilai PKL yang telah diterima siswa:
 * - Menyediakan daftar prakerin selesai yang memiliki penilaian.
 * - Mengizinkan siswa membuka detail nilai beserta pembimbing terkait.
 * - Mendukung pencarian berdasarkan nama perusahaan.
 */
class NilaiSiswa extends Component
{
    use WithPagination;

    public $user;
    public $siswa;
    public $search = '';
    public $perPage = 10;
    public $selectedPrakerin = null;
    public $showDetailNilai = false;

    /**
     * Menyimpan referensi user dan relasi siswa saat komponen dimuat.
     */
    public function mount()
    {
        $this->user = Auth::user();
        $this->siswa = $this->user->siswa;
    }

    /**
     * Mengembalikan paginasi ke halaman awal ketika kata kunci berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Memuat detail penilaian untuk prakerin yang dipilih dan menampilkan modal.
     */
    public function lihatDetailNilai($idPrakerin)
    {
        $this->selectedPrakerin = Prakerin::with(['perusahaan', 'pembimbingPerusahaan', 'pembimbingSekolah'])
            ->where('id_prakerin', $idPrakerin)
            ->where('nis_siswa', $this->siswa->nis)
            ->first();

        if ($this->selectedPrakerin) {
            $this->showDetailNilai = true;
        }
    }

    /**
     * Menutup modal detail nilai dan membersihkan state yang dipilih.
     */
    public function tutupDetailNilai()
    {
        $this->showDetailNilai = false;
        $this->selectedPrakerin = null;
    }

    /**
     * Mengambil daftar prakerin selesai yang memiliki penilaian lalu mengirimkannya ke view.
     */
    public function render()
    {
        // Ambil prakerin yang sudah selesai dan memiliki penilaian
        $prakerinSelesai = Prakerin::with(['perusahaan', 'pembimbingPerusahaan', 'pembimbingSekolah'])
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->whereHas('pembimbingPerusahaan.penilaian', function($query) {
                $query->where('nis_siswa', $this->siswa->nis);
            })
            ->when($this->search, function($query) {
                $query->whereHas('perusahaan', function($q) {
                    $q->where('nama_perusahaan', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('tanggal_selesai', 'desc')
            ->paginate($this->perPage);

        return view('livewire.pengguna.nilai-siswa', [
            'prakerinSelesai' => $prakerinSelesai
        ]);
    }
} 
