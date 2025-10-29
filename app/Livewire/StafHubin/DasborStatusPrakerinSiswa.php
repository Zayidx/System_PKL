<?php

namespace App\Livewire\StafHubin;

use App\Models\Siswa;
use App\Models\Prakerin;
use App\Models\PembimbingSekolah;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('components.layouts.layout-staf-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
/**
 * Menyajikan status prakerin untuk satu siswa:
 * - Menampilkan daftar prakerin lengkap dengan pembimbing dan perusahaan.
 * - Memungkinkan staf hubin menetapkan pembimbing sekolah.
 * - Mendukung pencarian dan paginasi daftar prakerin.
 */
class DasborStatusPrakerinSiswa extends Component
{
    use WithPagination;

    public $nis;
    public $siswa;
    public $search = '';
    public $perPage = 10;
    public $selectedPrakerinId;
    public $selectedPembimbingSekolahId;

    /**
     * Memuat data siswa yang akan dimonitor berdasarkan NIS.
     */
    public function mount($nis)
    {
        $this->nis = $nis;
        $this->siswa = Siswa::with(['user', 'jurusan', 'kelas'])->where('nis', $nis)->firstOrFail();
    }

    /**
     * Mengembalikan paginasi ke halaman pertama saat pencarian berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Menyimpan prakerin yang akan diberi pembimbing sekolah.
     */
    public function setPembimbingSekolah($prakerinId)
    {
        $this->selectedPrakerinId = $prakerinId;
    }

    /**
     * Memvalidasi dan menetapkan pembimbing sekolah ke prakerin terpilih,
     * kemudian mengirim notifikasi keberhasilan.
     */
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

    /**
     * Mengambil daftar prakerin siswa beserta daftar pembimbing sekolah untuk dropdown.
     */
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

        return view('livewire.staf-hubin.dasbor-status-prakerin-siswa', [
            'prakerinList' => $prakerinList,
            'pembimbingSekolahList' => $pembimbingSekolahList
        ]);
    }
} 
