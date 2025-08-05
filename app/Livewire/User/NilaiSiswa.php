<?php

namespace App\Livewire\User;

use App\Models\Penilaian;
use App\Models\Prakerin;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout("components.layouts.layout-user-dashboard")]
#[Title('Nilai PKL')]
class NilaiSiswa extends Component
{
    use WithPagination;

    public $user;
    public $siswa;
    public $search = '';
    public $perPage = 10;
    public $selectedPrakerin = null;
    public $showDetailNilai = false;

    public function mount()
    {
        $this->user = Auth::user();
        $this->siswa = $this->user->siswa;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

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

    public function tutupDetailNilai()
    {
        $this->showDetailNilai = false;
        $this->selectedPrakerin = null;
    }

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

        return view('livewire.user.nilai-siswa', [
            'prakerinSelesai' => $prakerinSelesai
        ]);
    }
} 