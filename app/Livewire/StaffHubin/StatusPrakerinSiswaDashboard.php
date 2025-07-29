<?php

namespace App\Livewire\StaffHubin;

use App\Models\Siswa;
use App\Models\Prakerin;
use App\Models\PembimbingSekolah;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
class StatusPrakerinSiswaDashboard extends Component
{
    use WithPagination;

    public $nis;
    public $siswa;
    public $search = '';
    public $perPage = 10;
    public $selectedPrakerinId;
    public $selectedPembimbingSekolahId;

    public function mount($nis)
    {
        $this->nis = $nis;
        $this->siswa = Siswa::with(['user', 'jurusan', 'kelas'])->where('nis', $nis)->firstOrFail();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setPembimbingSekolah($prakerinId)
    {
        $this->selectedPrakerinId = $prakerinId;
    }

    public function assignPembimbingSekolah()
    {
        $this->validate([
            'selectedPembimbingSekolahId' => 'required|exists:pembimbing_sekolah,nip_pembimbing_sekolah'
        ]);

        $prakerin = Prakerin::findOrFail($this->selectedPrakerinId);
        $prakerin->update([
            'nip_pembimbing_sekolah' => $this->selectedPembimbingSekolahId
        ]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'message' => 'Pembimbing sekolah berhasil ditugaskan.'
        ]);

        $this->selectedPrakerinId = null;
        $this->selectedPembimbingSekolahId = null;
    }

    public function render()
    {
        $prakerinList = Prakerin::with(['perusahaan', 'pembimbingPerusahaan', 'pembimbingSekolah'])
            ->where('nis_siswa', $this->nis)
            ->when($this->search, function($query) {
                $query->whereHas('perusahaan', function($q) {
                    $q->where('nama_perusahaan', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($this->perPage);

        $pembimbingSekolahList = PembimbingSekolah::orderBy('nama_pembimbing_sekolah')->get();

        return view('livewire.staff-hubin.status-prakerin-siswa-dashboard', [
            'prakerinList' => $prakerinList,
            'pembimbingSekolahList' => $pembimbingSekolahList
        ]);
    }
} 