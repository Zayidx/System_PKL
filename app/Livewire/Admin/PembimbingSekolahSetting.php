<?php

namespace App\Livewire\Admin;

use App\Models\PembimbingSekolah;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Setting Pembimbing Sekolah")]
class PembimbingSekolahSetting extends Component
{
    public $selectedKelas = '';
    public $selectedJurusan = '';
    public $selectedPembimbing = '';
    public $siswaList;
    public $pembimbingList = [];
    public $kelasList = [];
    public $jurusanList = [];

    public function mount()
    {
        $this->loadData();
        $this->loadSiswaList(); // Tambahkan ini untuk memuat data siswa saat pertama kali
    }

    public function loadData()
    {
        $this->pembimbingList = PembimbingSekolah::orderBy('nama_pembimbing_sekolah')->get();
        $this->kelasList = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        $this->jurusanList = Jurusan::orderBy('nama_jurusan_lengkap')->get();
    }

    public function updatedSelectedKelas()
    {
        $this->loadSiswaList();
    }

    public function updatedSelectedJurusan()
    {
        $this->loadSiswaList();
    }

    public function loadSiswaList()
    {
        $query = Siswa::with(['kelas', 'jurusan', 'pembimbingSekolah']);

        if ($this->selectedKelas) {
            $query->where('id_kelas', $this->selectedKelas);
        }

        if ($this->selectedJurusan) {
            $query->where('id_jurusan', $this->selectedJurusan);
        }

        $this->siswaList = $query->orderBy('nama_siswa')->get();
    }

    public function assignPembimbing($siswaNis, $pembimbingNip)
    {
        try {
            $siswa = Siswa::findOrFail($siswaNis);
            $siswa->update(['nip_pembimbing_sekolah' => $pembimbingNip]);
            
            $this->dispatch('swal:success', ['message' => 'Pembimbing sekolah berhasil ditugaskan.']);
            $this->loadSiswaList();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menugaskan pembimbing sekolah.']);
        }
    }

    public function removePembimbing($siswaNis)
    {
        try {
            $siswa = Siswa::findOrFail($siswaNis);
            $siswa->update(['nip_pembimbing_sekolah' => null]);
            
            $this->dispatch('swal:success', ['message' => 'Pembimbing sekolah berhasil dihapus.']);
            $this->loadSiswaList();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menghapus pembimbing sekolah.']);
        }
    }

    public function bulkAssignPembimbing()
    {
        if (!$this->selectedPembimbing) {
            $this->dispatch('swal:error', ['message' => 'Pilih pembimbing sekolah terlebih dahulu.']);
            return;
        }

        try {
            $siswaIds = $this->siswaList->pluck('nis')->toArray();
            
            DB::transaction(function () use ($siswaIds) {
                Siswa::whereIn('nis', $siswaIds)
                    ->update(['nip_pembimbing_sekolah' => $this->selectedPembimbing]);
            });
            
            $this->dispatch('swal:success', ['message' => 'Pembimbing sekolah berhasil ditugaskan ke semua siswa yang dipilih.']);
            $this->loadSiswaList();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menugaskan pembimbing sekolah secara massal.']);
        }
    }

    public function render()
    {
        return view('livewire.admin.pembimbing-sekolah-setting');
    }
}